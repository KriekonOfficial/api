<?php

namespace Core\Entity;

use \InvalidArgumentException;
use Core\ErrorBase;
use Core\ClassMetadata;
use Core\Model\Model;

abstract class Entity extends ErrorBase
{
	/**
	* Houses the model object based on the model path
	*/
	protected $model;

	protected $metadata;

	/**
	* Houses the namespace path to the model for the entity
	*/
	abstract public function getModelPath() : string;

	public function __construct($model = null)
	{
		if ($model === null)
		{
			$path = $this->getModelPath();
			$model = new $path;
		}

		$this->metadata = new ClassMetadata($model);
		$this->metadata->setEntity($this);

		$this->setModel($model);
	}

	public function getModel()
	{
		return $this->model;
	}

	protected function setModel($model) : void
	{
		if (($model instanceof Model) === false)
		{
			throw new InvalidArgumentException('Model Object must extend \Core\Model\Model');
		}
		$this->model = $model;
	}

	protected function resetModel() : void
	{
		$model = $this->getModel();
		$model->setInitializedFlag(false);
		$model->reset();
		$this->setModel($model);
	}

	/**
	* @param $pk_value = mixed|int
	* @return string
	*/
	public function getCacheKey($pk_value = 0, string $pk_name = '') : string
	{
		$model = $this->getModel();

		$key = $this->getCollectionName() . ':';
		$key .= $this->getCollectionTable() . ':';

		$name = $pk_name;
		if (empty($pk_name))
		{
			$name = $this->getCollectionPrimaryKey();
		}
		$key .= $name . ':';

		$pk_key = $pk_value;
		if (empty($pk_key))
		{
			$pk_key = $model->getPrimaryKey();
		}

		$key .= $pk_key;

		return $key;
	}
}