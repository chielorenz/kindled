<?php

namespace App\Controller;

use App\Service\Pocket;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthController extends Controller
{
	/**
     * @Route("/pocket/authorize", name="auth.pocket.authorize")
     */
    public function authorize(Pocket $pocket)
    {
    	$callback = $this->generateUrl('auth.pocket.authorized', [], UrlGeneratorInterface::ABSOLUTE_URL);
    	$redirect = $pocket->authorize($callback);
    	return $this->redirect($redirect);
    }

    /**
     * @Route("/pocket/authorized", name="auth.pocket.authorized")
     */
    public function authorized(Pocket $pocket)
    {
    	$pocket->authorized();
    	return $this->redirect($this->generateUrl('pocket.list'));
    }

    /**
     * @Route("auth/credentials", methods={"GET"}, name="auth.credentials.create")
     * 
     * @param Request $request
     */
    public function credentialCreate(Request $request) 
    {   
        $redirect = $request->query->get('redirect') ?: $this->generateUrl('home');
        return $this->render('credentials.html.twig', ['redirect' => $redirect]);
    }

    /**
     * @Route("auth/credentials", methods={"POST"}, name="auth.credentials.store")
     */
    public function credentialStore(Request $request) 
    {
        $redirect = $request->query->get('redirect') ?: $this->generateUrl('pocket.list');
        $from = $request->request->get('from');
        $to = $request->request->get('to');

        // todo check emails validity
        if(!$from || !$to) {
            $params = ['error' => 'Invalid adress'];
            if($redirect) $params['redirect'] = $redirect;

            return $this->redirect($this->generateUrl('auth.credentials.create', $params));
        }

        $session = new Session();
        $session->set(DefaultController::FROM, $from);
        $session->set(DefaultController::TO, $to);

        return $this->redirect($redirect);
    }

    /**
     * @Route("auth/logout", name="auth.logout")
     */
    public function logout() 
    {
        session_unset();
        return $this->redirect($this->generateUrl('home'));
    }
}