<?php

namespace Core\Model;

use Core\Util\JSONWrapper;
use \InvalidArgumentException;

abstract class Model
{
	private bool $initialized_flag = false;

	/**
	* Sets the primary key for the model.
	* This is a unique identifer
	*/
	abstract public function setPrimaryKey(int $value) : void;

	/**
	* Get the primary key for the model
	*/
	abstract public function getPrimaryKey() : int;

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

	/**
	* The class path to the db entity from the model.
	* Example \Modules\Account\Account
	*/
	abstract protected function getEntityPath() : string;

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
	* Create a DB Entity with the existing model
	* @return An Child object that extends Entity
	*/
	public function createEntity()
	{
		$entity = $this->getEntityPath();
		if (!class_exists($entity))
		{
			throw new InvalidArgumentException('Unable to create entity as the class does not exist. Class: ' . $entity);
		}
		return new $entity($this);
	}
}