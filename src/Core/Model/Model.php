<?php

namespace Core\Model;

use \ReflectionClass;
use Core\Util\JSONWrapper;

abstract class Model
{
	private bool $initialized_flag = false;

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

	public function isInitialized() : bool
	{
		return $this->initialized_flag;
	}

	public function setInitializedFlag(bool $flag) : void
	{
		$this->initialized_flag = $flag;
	}

	public function createEntity()
	{

	}
}