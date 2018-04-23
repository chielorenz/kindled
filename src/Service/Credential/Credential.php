<?php

namespace App\Service\Credential;

interface Credential
{
	/**
	 * @param string $from
	 */
	public function setFrom(string $from);
	
	/**
	 * @return string
	 */
	public function getFrom() : string;

	/**
	 * @param string $to
	 */
	public function setTo(string $to);

	/**
	 * @return string
	 */
	public function getTo() : string;

	/**
	 * Clear all credential
	 */
	public function clear();
}