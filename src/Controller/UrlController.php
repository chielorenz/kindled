<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * This controller manages the convertions made via url
 */
class UrlController extends Controller
{
    /**
     * @Route("/url", methods={"GET"}, name="url.create")
     */
    public function urlCreate()
    {
        return $this->render('url.html.twig');
    }

    /**
     * @Route("/url", methods={"POST"}, name="url.store")
     * 
     * @param Request  $request
     */
    public function urlStore(Request $request)
    {
        $url = $request->request->get('url');

        switch($_POST['type']) {
            case 'download':
                $response = $this->redirect($this->generateUrl('download', ['url' => $url, 'redirect' => 'url.create']));
                break;
            case 'send':
            default:
                $response = $this->redirect($this->generateUrl('send', ['url' => $url, 'redirect' => 'url.create']));
                break;    
        }     

        return $response;
    }
}