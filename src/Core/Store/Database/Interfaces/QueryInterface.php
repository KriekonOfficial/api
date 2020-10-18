<?php

namespace Core\Store\Database\Interfaces;

interface QueryInterface
{
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
}
