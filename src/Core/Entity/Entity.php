<?php

namespace Core\Entity;

use \ReflectionClass;
use Core\Entity\Exception\EntityAbstractException;
use Core\Store\Database\Util\DBWrapper;
use Core\ErrorBase;
use Core\ClassMetadata;

abstract class Entity extends ErrorBase
{
	protected string $db_name = 'kriekon';

	/**
	*
	*/
	protected string $db_table;

	/**
	* Primary key for the database table
	*/
	protected string $db_primary_key;

	protected string $model_path;
	protected $model;

	private $metadata;

	abstract public function store() : bool;
	abstract public function update(array $params = []) : bool;
	abstract public function delete() : bool;

	public function __construct($model = null)
	{
		$path = $this->getModelPath();
		if ($model === null)
		{
			$model = new $path;
		}

		$this->metadata = new ClassMetadata($model);
		$this->metadata->setEntity($this);

		$this->setModel($model);
	}

	protected function storeCommon() : bool
	{
		return true;
	}

	protected function updateCommon(array $params = []) : bool
	{
		return true;
	}

	protected function deleteCommon() : bool
	{
		return true;
	}

	public function find($primary_key)
	{
		$count = 0;
		$results = DBWrapper::PSingle('
			SELECT * FROM ' . $this->getDBTable() . '
			WHERE ' . $this->getDBPrimaryKey() . ' = ?', [$primary_key], $count, $this->getDBName());

		$model = $this->getModel();
		$model->reset();

		$reflection = $this->metadata->getReflection();
		foreach ($results as $column => $value)
		{
			$property = $reflection->getProperty($column);
			$property->setAccessible(true);
			$property->setValue($model, $value);
			$property->setAccessible(false);
		}

		if ($count == 1)
		{
			$model->setInitializedFlag(true);
		}

		$this->setModel($model);

		return $model;
	}

	public function getDBName() : string
	{
		return $this->db_name;
	}

	public function getDBTable() : string
	{
		return $this->db_table;
	}

	public function getDBPrimaryKey() : string
	{
		return $this->db_primary_key;
	}

	public function getDBFields() : array
	{
		return $this->db_fields;
	}

	public function getModel()
	{
		return $this->model;
	}

	public function getModelPath() : string
	{
		return $this->model_path;
	}

	protected function setModel($model) : void
	{
		$this->model = $model;
	}

	protected function setModelPath(string $model_path) : void
	{
		$this->model_path = $model_path;
	}
}