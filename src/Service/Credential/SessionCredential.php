<?php

namespace App\Service\Credential;

use Symfony\Component\HttpFoundation\Session\Session;

class SessionCredential implements Credential
{
    const FROM_EMAIL = 'from.email';
    const TO_EMAIL = 'to.email';

    /**
     * @var Session
     */
	private $session;

	public function __construct()
	{
		$this->session = new Session();
	}

	public function setFrom(string $from) 
	{
		$this->session->set(self::FROM_EMAIL, $from);
	}

	public function getFrom() 
	{
		return $this->session->get(self::FROM_EMAIL);
	}

	public function setTo(string $to) 
	{
		$this->session->set(self::TO_EMAIL, $to);
	}

	public function getTo() 
	{
		return $this->session->get(self::TO_EMAIL);
	}

	public function clear()
	{
		$this->session->remove(self::FROM_EMAIL);
		$this->session->remove(self::TO_EMAIL);
	}
}