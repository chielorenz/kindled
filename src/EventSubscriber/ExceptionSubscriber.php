<?php

namespace App\EventSubscriber;

use App\Service\Pocket;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
	private $router;

    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /** 
     * @var string environment
     */
    private $env;

    /**
     * @param UrlGeneratorInterface  $router
     * @param LoggerInterface  $logger
     * @param string  $env
     */
	public function __construct(UrlGeneratorInterface $router, LoggerInterface $logger, string $env) 
	{
		$this->router = $router;
        $this->logger = $logger;
        $this->env = $env;
	}

	/**
	 * @return array
	 */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    /**
     * @param  GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->logger->error($exception->getMessage());

        if($this->env == 'prod') {
            // TODO use pocket
            $session = new Session();
            $session->set(Pocket::REQUEST_TOKEN, null);
            $session->set(Pocket::ACCESS_TOKEN, null);
        
        	$redirectUrl = $this->router->generate('home', ['error' => 'Something went wrong... try again or contat us for help.']);
        	$event->setResponse(new RedirectResponse($redirectUrl));
        }        
    }
}