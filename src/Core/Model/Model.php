<?php

namespace Core\Model;

use Core\Util\JSONWrapper;
use \InvalidArgumentException;
use Core\Entity\EntityInterface;
use Core\ClassMetadata;

abstract class Model
{
	private bool $initialized_flag = false;

	/**
	* Sets the primary key for the model.
	* This is a unique identifer
	*/
	abstract public function setPrimaryKey($value) : void;

	/**
	* Get the primary key for the model
	*/
	abstract public function getPrimaryKey();

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
	* The class path to the entity instance from the model.
	* Example \Modules\User\User
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
	/**
	* Create a Entity instance with the existing model
	* @return EntityInterface
	*/
	public function createEntity() : EntityInterface
	{
		$entity = $this->getEntityPath();
		if (!class_exists($entity))
		{
			throw new InvalidArgumentException('Unable to create entity as the class does not exist. Class: ' . $entity);
		}
		return new $entity($this);
	}
}
