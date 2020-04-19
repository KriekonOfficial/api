<?php

namespace Core\Store\Database\Interfaces;

interface DatabaseInterface
{
	/**
	* @param $dbname - The database schema to use.
	*/
	public function __construct(string $dbname);

	/**
	* Do we have an Object connected?
	* @return bool
	*/
	public function isConnected() : bool;

	/**
	* Terminates the connection, unless the connection is persistant.
	* In our case we do not use persistant connections.
	* @return void
	*/
	public function disconnect() : void;

	/**
	* Executes an SQL prepared statement
	* @param $sql - The SQL Statement to execute
	* @param $values - The values to bind corresponding question marks to.
	* @return bool
	*/
	public function query(string $sql, array $values = []) : bool;

	/**
	* Get the value of whatever is stored inside $results
	* @return mixed Array|DBResult - Iterator/Countable data
	*/
	public function getResults();

	/**
	* Creates an Iterator object and only fetches 1 row at a time.
	* @return DBResult
	*/
	public function getDBResult() : \Core\Store\Database\Model\DBResult;

	/**
	* Returns all the results from the query.
	* @return array
	*/
	public function fetchAllResults() : array;

	/**
	* Returns a single result
	* @return array
	*/
	public function fetchResult() : array;

	/**
	* The Count of the affected rows from the query.
	* @return int
	*/
	public function rowCount() : int;

	/**
	* Returns the ID of the last inserted row, or the last value from a sequence object
	* @return string|int
	*/
	public function getLastInsertID();

	/**
	* Start our transaction
	* @return bool
	*/
	public function beginTransaction() : bool;

	/**
	* Commit all the queries in the transaction and end the transaction
	* @return bool
	*/
	public function commitTransaction() : bool;

	/**
	* Rollback all the queries in the transaction and end the transaction
	* @return bool
	*/
	public function rollbackTransaction() : bool;

	/**
	* Are we in a transaction?
	* @return bool
	*/
	public function inTransaction() : bool;
}
