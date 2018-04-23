<?php

namespace App\Service\Credential;

interface Credential
{
	/**
	 * @param string $from
	 */
	public function setFrom(string $from);
	
	/**
	 * @return string|null
	 */
	public function getFrom();

	/**
	 * @param string $to
	 */
	public function setTo(string $to);

	/**
	 * @return string|null
	 */
	public function getTo();

	/**
	 * Clear all credential
	 */
	public function clear();
}