<?php

namespace Core\Entity;

use \InvalidArgumentException;
use Core\Entity\Exception\EntityException;
use Core\Store\Database\Util\DBWrapper;
use Core\Store\Database\Model\DBResult;
use Core\ErrorBase;
use Core\ClassMetadata;
use Core\Model\Model;

abstract class Entity extends ErrorBase
{
	/**
	* The database in where the db_table is located.
	*/
	protected string $db_name = DEFAULT_DB;

	/**
	* The table that we will be manipulating
	*/
	protected string $db_table;

	/**
	* Primary key for the database table
	*/
	protected string $db_primary_key;

	/**
	* Houses the model object based on the model path
	*/
	protected $model;

	private $metadata;

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

	/**
	* Takes all current values inside the model and inserts them into the db entity
	* @throws EntityException - if the insertion query fails some how.
	* @return an Instance of \Core\Model\Model
	*/
	public function store()
	{
		$model = $this->getModel();

		$last_inserted_id = 0;
		$result = DBWrapper::insert($this->getDBTable(), $model->toArray(), $last_inserted_id, $this->getDBName());
		if ($result === false)
		{
			throw new EntityException('Unable to insert the model ' . get_class($model) . ' with values ' . var_export($model->toArray(), true));
		}

		$model->setPrimaryKey($last_inserted_id);
		$model->setInitializedFlag(true);
		$this->setModel($model);

		return $model;
	}

	/**
	* Update the db entity based on the primary key of the model.
	* @param $params - These are the fields that will be pulled from the model
	* Ex ['registration_time', 'date_of_birth']
	* @throws InvalidArgumentException - If the column does not exist inside the model.
	* @return bool
	*/
	public function update(array $params = []) : bool
	{
		$model = $this->getModel();

		$update_values = $model->toArray();

		$update_params = [];
		foreach ($params as $column)
		{
			if (!isset($update_values[$column]))
			{
				throw new InvalidArgumentException('That column: ' . $column . ' does not exist inside the model ' . get_class($model));
			}
			$update_params[$column] = $update_values[$column];
		}
		return DBWrapper::update($this->getDBTable(), $update_params, [$this->getDBPrimaryKey() => $model->getPrimaryKey()]);
	}

	/**
	* Delete the entity based on the primary key of the model.
	* @throws EntityException if the model object is not initialized
	* @return bool
	*/
	public function delete() : bool
	{
		$model = $this->getModel();

		if (!$model->isInitialized())
		{
			throw new EntityException('Unable to delete the model as it is not initialized. Model: ' . get_class($model));
		}

		$result = DBWrapper::delete($this->getDBTable(), [$this->getDBPrimaryKey() => $model->getPrimaryKey()]);
		if ($result === true)
		{
			$model->setInitializedFlag(false);
			$model->reset();
			$this->setModel($model);
			return true;
		}
		return false;
	}

	/**
	* @param pk_value - Primary key value, mixed types
	* @return A model that extends an instance of Core\Model\Model
	*/
	public function find($pk_value)
	{
		$count = 0;
		$results = DBWrapper::PResult('
			SELECT * FROM ' . $this->getDBTable() . '
			WHERE ' . $this->getDBPrimaryKey() . ' = ?', [$pk_value], $this->getDBName());

		$this->setModelProperties($results);

		return $this->getModel();
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

	protected function setModelProperties(DBResult $result) : void
	{
		$model = $this->getModel();
		$model->reset();

		$reflection = $this->metadata->getReflection();
		foreach ($result->getRecord() as $column => $value)
		{
			$property = $reflection->getProperty($column);
			$property->setAccessible(true);
			$property->setValue($model, $value);
			$property->setAccessible(false);
		}

		if ($result->count() == 1)
		{
			$model->setInitializedFlag(true);
		}
		else if ($result->count() > 1)
		{
			throw new EntityException('There appears to be more than 1 record based off the model: ' . get_class($this));
		}

		$this->setModel($model);
	}
}