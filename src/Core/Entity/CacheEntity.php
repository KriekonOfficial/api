<?php

namespace Core\Entity;

use \InvalidArgumentException;
use Core\Entity\Exception\EntityException;
use Core\Store\Cache;
use Core\Util\JSONWrapper;

abstract class CacheEntity extends Entity implements EntityInterface
{
	/**
	* The amount of time in seconds the Entity is going to live in the cache.
	*/
	abstract public function getEntityCacheTime() : int;

	/**
	* Houses the namespace path to the model for the entity
	*/
	abstract public function getModelPath() : string;

	/**
	* The collection that we will be manipulating
	*/
	abstract public function getCollectionTable() : string;

	/**
	* Primary key for the collection table
	*/
	abstract public function getCollectionPrimaryKey() : string;

	/**
	* The cache collection
	*/
	public function getCollectionName() : string
	{
		return DEFAULT_DB;
	}

	/**
	* Takes all current values inside the model and inserts them into the db entity
	* @throws EntityException - if the insertion query fails some how.
	* @return an Instance of \Core\Model\Model
	*/
	public function store()
	{
		$model = $this->getModel();

		$cache = Cache::setArray($this->getCacheKey(), $model->toArray(), $this->getEntityCacheTime());
		if ($cache === false)
		{
			if (JSONWrapper::hasError())
			{
				throw new EntityException(JSONWrapper::getLastError());
			}
			return $model;
		}

		$model->setInitializedFlag(true);
		$this->setModel($model);

		if ($this->useRequestCache())
		{
			RequestCache::setCacheItem($this->getCacheKey(), $model);
		}

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
			throw new EntityException('Unable to update model ' . get_class($model) . ' as it is not initialized');
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

		$array = Cache::getArray($this->getCacheKey());

		if ($array === null)
		{
			throw new EntityException('Cache entity no longer exists: Entity: ' . get_class($this) . ' Model: ' . get_class($model));
		}

		foreach ($update_params as $param => $value)
		{
			$array[$param] = $value;
		}

		$cache = Cache::setArray($this->getCacheKey(), $array, $this->getEntityCacheTime());
		if ($cache === false && JSONWrapper::hasError())
		{
			throw new EntityException(JSONWrapper::getLastError());
		}

		if ($this->useRequestCache())
		{
			RequestCache::setCacheItem($this->getCacheKey(), $model);
		}

		return $cache;
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
			throw new EntityException('Unable to delete model ' . get_class($model) . ' as it is not initialized');
		}

		if (!Cache::delete($this->getCacheKey()))
		{
			return false;
		}

		$this->resetModel();

		if ($this->useRequestCache())
		{
			RequestCache::deleteCacheItem($this->getCacheKey());
		}

		return true;
	}

	/**
	* @param pk_value - Primary key value, mixed types
	* @return A model that extends an instance of Core\Model\Model
	*/
	public function find($pk_value)
	{
		if ($this->useRequestCache())
		{
			$cache_item = RequestCache::getCacheItem($this->getCacheKey($pk_value));
			if ($cache_item instanceof Model)
			{
				return $cache_item;
			}
		}

		$record = Cache::getArray($this->getCacheKey($pk_value));

		$this->resetModel();

		if ($record === null)
		{
			return $this->getModel();
		}

		$this->setModelProperties($record);

		$model = $this->getModel();
		if ($this->useRequestCache())
		{
			RequestCache::setCacheItem($this->getCacheKey(), $model);
		}

		return $model;
	}

	protected function setModelProperties(array $record) : void
	{
		$model = $this->getModel();

		$reflection = $this->metadata->getReflection();
		$model->setModelProperties($record);

		if (count($record) === count($this->metadata->getProtectedFields()))
		{
			$model->setInitializedFlag(true);
		}

		$this->setModel($model);
	}
}
