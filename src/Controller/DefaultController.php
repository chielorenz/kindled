<?php

namespace App\Controller;

use App\Service\Pocket;
use App\Service\Kindled;
use App\Service\Mailer;
use App\Service\Validator;
use App\Service\Credential\Credential;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * This controller holds the main business logic
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/send", name="send")
     *
     * @param Request  $request
     * @param Kindled  $kindled
     * @param Mailer  $mailer
     * @param Credential  $credential
     * @param Validator  $validator
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
     * @param Request  $request
     * @param Kindled  $kindled
     * @param Credential  $credential
     * @param Validator  $validator
     */
    public function download(Request $request, Kindled $kindled, Credential $credential, Validator $validator)
    {
        $url = $request->query->get('url');
        $redirect = $request->query->get('redirect') ?: 'home';

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
     */
    public function info()
    {
        return $this->render('info.html.twig');
    }
}
