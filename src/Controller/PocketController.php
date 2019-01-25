<?php

namespace App\Controller;

use App\Service\Pocket;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * This controller manages Pocket integration
 */
class PocketController extends AbstractController
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
     * @Route("/pocket", name="pocket.list")
     * 
     * @param Pocket  $pocket
     */
    public function articles(Pocket $pocket)
    {
        return $this->render('pockets.html.twig', ['articles' => $pocket->articles()]);
    }
}
