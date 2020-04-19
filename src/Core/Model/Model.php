<?php

namespace Core\Model;

use \ReflectionClass;
use Core\Util\JSONWrapper;

abstract class Model
{
	/**
	* Set Defaults at the beginning of the instance of the model.
	*/
	abstract public function reset() : void;

	abstract public function toArray() : array;
	abstract public function toPublicArray() : array;

	public function __construct()
	{
		$this->reset();
	}

	public function toJSON() : string
	{
		return JSONWrapper::json($this->toArray());
	}

	public function toPublicJSON() : string
	{
		return JSONWrapper::json($this->toPublicArray());
	}

	public function createEntity()
	{

	}
}