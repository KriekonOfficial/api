<?php

namespace Core\Entity;

interface EntityInterface
{
	/**
	* Takes all current values inside the model and inserts them into the db entity
	* @throws EntityException - if the insertion query fails some how.
	* @return an Instance of \Core\Model\Model
	*/
	public function store();

	/**
	* Update the db entity based on the primary key of the model.
	* @param $params - These are the fields that will be pulled from the model
	* Ex ['registration_time', 'date_of_birth']
	* @throws InvalidArgumentException - If the column does not exist inside the model.
	* @return bool
	*/
	public function update(array $params = []) : bool;

	/**
	* Delete the entity based on the primary key of the model.
	* @throws EntityException if the model object is not initialized
	* @return bool
	*/
	public function delete() : bool;

	/**
	* @param pk_value - Primary key value, mixed types
	* @return A model that extends an instance of Core\Model\Model
	*/
	public function find($pk_value);

	/**
	* The database in where the db_table is located.
	*/
	public function getCollectionName() : string;

	/**
	* The table that we will be manipulating
	*/
	public function getCollectionTable() : string;

	/**
	* Primary key for the database table
	*/
	public function getCollectionPrimaryKey() : string;
}
