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

	/**
	 * @param string $from
	 */
	public function setFrom(string $from) 
	{
		$this->session->set(self::FROM_EMAIL, $from);
	}

	/**
	 * @return string
	 */
	public function getFrom() : string
	{
		return $this->session->get(self::FROM_EMAIL);
	}

	/**
	 * @param string $to
	 */
	public function setTo(string $to) 
	{
		$this->session->set(self::TO_EMAIL, $to);
	}

	/**
	 * @return string
	 */
	public function getTo() : string
	{
		return $this->session->get(self::TO_EMAIL);
	}

	public function clear()
	{
		$this->session->remove(self::FROM_EMAIL);
		$this->session->remove(self::TO_EMAIL);
	}
}