<?php

namespace Ant\PhotoRestBundle\Entity;

class Error
{
	private $errors;
	
	private $code;
	
	public function getErrors()
	{
		return $this->errors;
	}
	public function setErrors($errors)
	{
		$this->errors = $errors;
	}
	public function setCode($code)
	{
		$this->code = $code;
	}
	public function getCode()
	{
		return $this->code;
	}
}