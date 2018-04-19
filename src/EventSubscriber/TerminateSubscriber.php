<?php

namespace App\EventSubscriber;

use App\Service\Kindled;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TerminateSubscriber implements EventSubscriberInterface
{
	/**
	 * @param Kindled $kindled
	 */
	public function __construct(Kindled $kindled) 
	{
		$this->kindled = $kindled;
	}

	/**
	 * @return array
	 */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => 'onKernelTerminate'
        ];
    }

    /**
     * @param PostResponseEvent  $event
     */
    public function onKernelTerminate(PostResponseEvent $event) {
    	$this->kindled->clear();
    }
}