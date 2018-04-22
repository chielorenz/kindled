<?php

namespace App\Service;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class Validator
{
	/**
	 * @var array
	 */
	private $errors;

	/**
	 * @param  string  $url
	 * @return boolean
	 */
	public function isValidUrl(string $url) : bool
	{
		$validator = Validation::createValidator();
        $this->errors = $validator->validate($url, [new Assert\Url(), new Assert\NotBlank()]);
        return count($this->errors) === 0;
	}

	/**
	 * @param  string  $email
	 * @return boolean
	 */
	public function isValidEmail(string $email) : bool
	{
		$validator = Validation::createValidator();
        $this->errors = $validator->validate($email, [new Assert\Email(), new Assert\NotBlank()]);
        return count($this->errors) === 0;
	}

	/**
	 * @return array
	 */
	public function getErrors() : array
	{
		return $this->errors;
	}
}