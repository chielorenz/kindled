<?php

namespace App\Controller;

use App\Service\Pocket;
use App\Service\Kindled;
use App\Service\Mailer;
use App\Service\Validator;
use App\Service\Credential\Credential;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/url", methods={"GET"}, name="url.create")
     * 
     * @param  Request  $request
     */
    public function urlCreate(Request $request)
    {
        return $this->render('url.html.twig');
    }

    /**
     * @Route("/url", methods={"POST"}, name="url.store")
     * 
     * @param  Request  $request
     */
    public function urlStore(Request $request)
    {
        $url = $request->request->get('url');

        switch($_POST['type']) {
            case 'download':
                $response = $this->redirect($this->generateUrl('download', ['url' => $url]));
                break;
            case 'send':
            default:
                $response = $this->redirect($this->generateUrl('send', ['url' => $url, 'redirect' => 'url.create']));
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
    public function send(Request $request, Kindled $kindled, Mailer $mailer, Credential $credential, Validator $validator)
    {   
        $url = $request->query->get('url');
        $redirect = $request->query->get('redirect') ?: 'home';

        if(!$validator->isValidUrl($url)) {
            return $this->redirect($this->generateUrl($redirect, ['error' => 'Invalid url']));
        }
        
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
    public function download(Request $request, Kindled $kindled, Credential $credential, Validator $validator)
    {
        $url = $request->query->get('url');

        if(!$validator->isValidUrl($url)) {
            return $this->redirect($this->generateUrl($redirect, ['error' => 'Invalid url']));
        }

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
