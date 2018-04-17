<?php

namespace App\EventSubscriber;

use Twig_Environment;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MessageSubscriber implements EventSubscriberInterface
{
	const MESSAGES = [
		'error' => [
            'type' => 'danger',
            'title' => 'Ooops!',
        ],
		'message' => [
            'type' => 'info',
            'title' => 'Kindled!',
        ],
	];

	/** @var Twig_Environment */
	private $twig;

    /**
     * @param Twig_Environment $twig
     */
	public function __construct(Twig_Environment $twig) 
	{
		$this->twig = $twig;
	}

	/**
	 * @return array
	 */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * @param  FilterResponseEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
    	foreach(SELF::MESSAGES as $key => $meta) {
    		if($message = $event->getRequest()->query->get($key)) {
                $this->twig->addGlobal('type', $meta['type']);
                $this->twig->addGlobal('title', $meta['title']);
    			$this->twig->addGlobal('message', $message);
    			return;
    		}	
    	}
    }
}