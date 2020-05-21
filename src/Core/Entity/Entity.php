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
}