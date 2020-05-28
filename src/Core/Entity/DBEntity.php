<?php

namespace Core\Entity;

use \InvalidArgumentException;
use Core\Entity\Exception\EntityException;
use Core\Store\Database\Util\DBWrapper;
use Core\Store\Database\Model\DBResult;

abstract class DBEntity extends Entity implements EntityInterface
{
	/**
	* Houses the namespace path to the model for the entity
	*/
	abstract public function getModelPath() : string;

	/**
	* The table that we will be manipulating
	*/
	abstract public function getCollectionTable() : string;

	/**
	* Primary key for the database table
	*/
	abstract public function getCollectionPrimaryKey() : string;

	/**
	* The database in where the db_table is located.
	*/
	public function getCollectionName() : string
	{
		return DEFAULT_DB;
	}

	public function getEntityCacheTime() : int
	{
		return 0;
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
		$result = DBWrapper::insert($this->getCollectionTable(), $model->toArray(), $last_inserted_id, $this->getCollectionName());
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

		if (!$model->isInitialized())
		{
			throw new EntityException('Unable to delete the model as it is not initialized. Model: ' . get_class($model));
		}

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
		return DBWrapper::update($this->getCollectionTable(), $update_params, [$this->getCollectionPrimaryKey() => $model->getPrimaryKey()], $this->getCollectionName());
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

		$result = DBWrapper::delete($this->getCollectionTable(), [$this->getCollectionPrimaryKey() => $model->getPrimaryKey()], $this->getCollectionName());
		if ($result === true)
		{
			$this->resetModel();
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
		$results = DBWrapper::PResult('
			SELECT * FROM ' . $this->getCollectionTable() . '
			WHERE ' . $this->getCollectionPrimaryKey() . ' = ?', [$pk_value], $this->getCollectionName());

		$this->setModelProperties($results);

		return $this->getModel();
	}

	protected function setModelProperties(DBResult $result) : void
	{
		$this->resetModel();
		$model = $this->getModel();

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