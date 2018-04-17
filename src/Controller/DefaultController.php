<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Service\Pocket;
use App\Service\Kindled;
use App\Service\Mailer;

class DefaultController extends Controller
{
    const FROM = 'from';
    const TO = 'to';

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
     * @Route("/send", name="send")
     *
     * @param Request $request
     * @return Response
     */
    public function send(Request $request, Kindled $kindled, Mailer $mailer)
    {   
        $url = $request->query->get('url');
        
        $session = new Session();
        $from = $session->get(self::FROM);
        $to = $session->get(self::TO);
        
        $mobi = $kindled->convert($url);
        $mailer->send($mobi, $from, $to);
        $kindled->clear();

        return $this->redirect($this->generateUrl('pocket.list', ['message' => 'Article sent!']));
    }

    /**
     * @Route("/info", name="info")
     *
     * @return Response
     */
    public function info() {
        return $this->render('info.html.twig');
    }
}
