<?php

namespace App\Controller;

use App\Service\Pocket;
use App\Service\Validator;
use App\Service\Credential\Credential;
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
     *
     * @param Pocket  $pocket
     */
    public function authorize(Pocket $pocket)
    {
    	$callback = $this->generateUrl('auth.pocket.authorized', [], UrlGeneratorInterface::ABSOLUTE_URL);
    	$redirect = $pocket->authorize($callback);
    	return $this->redirect($redirect);
    }

    /**
     * @Route("/pocket/authorized", name="auth.pocket.authorized")
     *
     * @param Pocket $pocket
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
     * 
     * @param Request  $request
     * @param Credential $ credential,
     * @param Validator  $validator
     */
    public function credentialStore(Request $request, Credential $credential, Validator $validator) 
    {
        $redirect = $request->query->get('redirect') ?: $this->generateUrl('home');
        $from = $request->request->get('from');
        $to = $request->request->get('to');

        if(!$validator->isValidEmail($from) || !$validator->isValidEmail($to)) {
            $params = ['error' => 'Invalid address', 'redirect' => $redirect];
            return $this->redirect($this->generateUrl('auth.credentials.create', $params));
        }

        $credential->setFrom($from);
        $credential->setTo($to);

        return $this->redirect($redirect);
    }

    /**
     * @Route("auth/logout", name="auth.logout")
     *
     * @param Credential  $credential
     * @param Pocket  $pocket
     */
    public function logout(Credential $credential, Pocket $pocket) 
    {
        $credential->clear();
        $pocket->logout();
        return $this->redirect($this->generateUrl('home'));
    }
}