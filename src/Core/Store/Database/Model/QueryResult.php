<?php

namespace Core\Store\Database\Model;

use \PDO;
use \PDOStatement;
use Core\ErrorBase;
use Core\Store\Database\Interfaces\QueryInterface;
use Core\Store\Database\Model\DBResult;

class QueryResult extends ErrorBase implements QueryInterface
{
	private $query;
	private int $count = 0;
	protected $results = [];

	public function __construct(?PDOStatement $query)
	{
		$this->query = $query;
		if ($query !== null)
		{
			$this->count = $query->rowCount();
		}
	}

	public function isInitialized() : bool
	{
		return $this->query !== null;
	}

	/**
	* Get the value of whatever is stored inside $results
	* @return mixed Array|DBResult - Iterator/Countable data
	*/
	public function getResults()
	{
		return $this->results;
	}

	/**
	* Creates an Iterator object and only fetches 1 row at a time.
	* @return DBResult
	*/
	public function getDBResult() : DBResult
	{
		$this->results = new DBResult($this);
		return $this->results;
	}

	/**
	* Returns all the results from the query.
	* @return array
	*/
	public function fetchAllResults() : array
	{
		if (!$this->isInitialized())
		{
			return [];
		}

		$results = $this->query->fetchAll(PDO::FETCH_ASSOC);
		$this->results = is_array($results) === true ? $results : [];
		return $this->results;
	}

	/**
	* Returns a single result
	* @return array
	*/
	public function fetchResult() : array
	{
		if (!$this->isInitialized())
		{
			return [];
		}

		$results = $this->query->fetch(PDO::FETCH_ASSOC);
		$this->results = is_array($results) === true ? $results : [];
		return $this->results;
	}

	/**
	* The Count of the affected rows from the query.
	* @return int
	*/
	public function rowCount() : int
	{
		return $this->count;
	}
}
