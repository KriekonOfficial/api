<?php

namespace Core\Model;

trait BaseValidator
{
	/**
	* @return true on length greater than min|false on failure
	*/
	public function minLength($value, $min) : bool
	{
		return strlen($value) > $min;
	}
}