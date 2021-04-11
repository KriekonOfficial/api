<?php

namespace Core\Model;

use \InvalidArgumentException;
use Core\Entity\EntityInterface;

abstract class Model extends BaseModel
{
	abstract public function reset() : void;
	abstract public function toArray() : array;
	abstract public function toPublicArray() : array;

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
	* The class path to the entity instance from the model.
	* Example \Modules\User\User
	*/
	abstract protected function getEntityPath() : string;

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
