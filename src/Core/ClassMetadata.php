<?php

namespace Core;

use Core\Entity\Entity;
use \ReflectionClass;
use \DomainException;
use \InvalidArgumentException;

class ClassMetadata
{
	private $reflection;

	private $entity = null;

	private array $protected_properties = [];
	private array $public_properties = [];
	private array $private_properties = [];

	private array $protected_fields = [];
	private array $public_fields = [];
	private array $private_fields = [];

	public function __construct($class)
	{
		$this->reflection = new ReflectionClass($class);
		$this->protected_properties = $this->reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
		$this->public_properties = $this->reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
		$this->private_properties = $this->reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
	}

	public function getEntity()
	{
		if ($this->entity === null)
		{
			throw new DomainException('Entity has not been set or does not exist');
		}
		return $this->entity;
	}

	public function setEntity($entity)
	{
		if (($entity instanceof Entity) === false)
		{
			throw new InvalidArgumentException('Failed to set entity as it is not an instance of a valid DB Entity');
		}
		$this->entity = $entity;
	}

	public function getReflection() : ReflectionClass
	{
		return $this->reflection;
	}

	public function getProtectedProperties() : array
	{
		return $this->protected_properties;
	}

	public function getPublicProperties() : array
	{
		return $this->public_properties;
	}

	public function getPrivateProperties() : array
	{
		return $this->private_properties;
	}

	public function getProtectedFields() : array
	{
		if (empty($this->protected_fields))
		{
			foreach ($this->getProtectedProperties() as $property)
			{
				$this->protected_fields[] = $property->getName();
			}
		}

		return $this->protected_fields;
	}

	public function getPublicFields() : array
	{
		if (empty($this->public_fields))
		{
			foreach ($this->getPublicProperties() as $property)
			{
				$this->public_fields[] = $property->getName();
			}
		}

		return $this->public_fields;
	}

	public function getPrivateFields() : array
	{
		if (empty($this->private_fields))
		{
			foreach ($this->getPrivateProperties() as $property)
			{
				$this->private_fieldsp[] = $property->getName();
			}
		}

		return $this->private_fields;
	}
}