<?php

namespace Core\Model;

use Core\ClassMetadata;
use Core\Util\JSONWrapper;

abstract class BaseModel
{
	private bool $initialized_flag = false;

	/**
	* Set Defaults at the beginning of the instance of the model.
	*/
	abstract public function reset() : void;

	/**
	* An array of all protected properties
	*/
	abstract public function toArray() : array;

	/**
	* An array of protected properties that the user is allowed to see.
	*/
	abstract public function toPublicArray() : array;

	public function __construct()
	{
		$this->reset();
	}

	final public function toJSON() : string
	{
		return JSONWrapper::json($this->toArray());
	}

	final public function toPublicJSON() : string
	{
		return JSONWrapper::json($this->toPublicArray());
	}

	final public function isInitialized() : bool
	{
		return $this->initialized_flag;
	}

	final public function setInitializedFlag(bool $flag) : void
	{
		$this->initialized_flag = $flag;
	}

	/**
	* Set properties for the object based on the record given.
	*/
	final public function setModelProperties(array $record) : void
	{
		$metadata = new ClassMetadata($this);

		$reflection = $metadata->getReflection();
		foreach ($record as $column => $value)
		{
			$property = $reflection->getProperty($column);
			$property->setAccessible(true);
			$property->setValue($this, $value);
			$property->setAccessible(false);
		}
	}
}
