<?php

namespace App\EventSubscriber;

use App\Service\Pocket;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
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
     * @var Pocket environment
     */
    private $pocket;

    /**
     * @param UrlGeneratorInterface  $router
     * @param LoggerInterface  $logger
     * @param string  $env
     * @param Pocket $pocket
     */
	public function __construct(UrlGeneratorInterface $router, LoggerInterface $logger, string $env, Pocket $pocket) 
	{
		$this->router = $router;
        $this->logger = $logger;
        $this->env = $env;
        $this->pocket = $pocket;
	}

	/**
	 * @return array
	 */
    public static function getSubscribedEvents() : array
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
            $pocket->logout();
        	$redirectUrl = $this->router->generate('home', ['error' => 'Something went wrong... try again or contat us for help.']);
        	$event->setResponse(new RedirectResponse($redirectUrl));
        }        
    }
}