<?php

namespace App\Controller;

use App\Service\Pocket;
use App\Service\Kindled;
use App\Service\Mailer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Credential\Credential;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function home(Request $request, Pocket $pocket)
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/list", name="pocket.list")
     * 
     * @param Request $request
     * @return Response
     */
    public function articles(Request $request, Pocket $pocket)
    {
        $articles = $pocket->articles();
        return $this->render('articles.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/uri", methods={"GET"}, name="uri.create")
     * 
     * @param  Request  $request
     */
    public function uriCreate(Request $request)
    {
        return $this->render('uri.html.twig');
    }

    /**
     * @Route("/uri", methods={"POST"}, name="uri.store")
     * 
     * @param  Request  $request
     */
    public function uriStore(Request $request)
    {
        $uri = $request->request->get('uri');

        switch($_POST['type']) {
            case 'download':
                $response = $this->redirect($this->generateUrl('download', ['url' => $uri]));
                break;
            case 'send':
            default:
                $response = $this->redirect($this->generateUrl('send', ['url' => $uri, 'redirect' => 'uri.create']));
                break;    
        }     

        return $response;
    }

    /**
     * @Route("/send", name="send")
     *
     * @param Request $request
     * @param Kindled $kindled
     * @param Credential $credential
     * @return Response
     */
    public function send(Request $request, Kindled $kindled, Mailer $mailer, Credential $credential)
    {   
        $url = $request->query->get('url');
        $redirect = $request->query->get('redirect') ?: 'home';

        // validate url
        
        $from = $credential->getFrom();
        $to = $credential->getTo();

        $mobi = $kindled->convert($url);
        $mailer->send($mobi, $from, $to);

        return $this->redirect($this->generateUrl($redirect, ['message' => 'Article sent!']));
    }

    /**
     * @Route("/download", name="download")
     *
     * @param Request $request
     * @param Kindled $kindled
     * @param Credential $credential
     * @return Response
     */
    public function download(Request $request, Kindled $kindled, Credential $credential)
    {
        $url = $request->query->get('url');

        // validate url

        $name = parse_url($url, PHP_URL_HOST);
        $name = preg_split('/(?=\.[^.]+$)/', $name);
        $name = reset($name);

        $from = $credential->getFrom();
        $to = $credential->getTo();
        
        $mobi = $kindled->convert($url);

        $response = new BinaryFileResponse($mobi);
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name . '_' . date('m_d') . '.mobi');
        $response->headers->set('Content-Disposition', $disposition);
    
        return $response;
    }

    /**
     * @Route("/info", name="info")
     *
     * @return Response
     */
    public function info()
    {
        return $this->render('info.html.twig');
    }
}
