<?php

namespace Core\Store\Database\Util;

use Core\Store\Database\Exception;
use Core\Store\Database\DBPool;
use Core\Store\Database\Model\DBResult;
use Core\Store\Database\Exception\DatabaseException;
use Core\Store\Database\Interfaces\DatabaseInterface;

class DBWrapper
{
	public static function factory(string $sql, array $params = [], string $database = DEFAULT_DB) : DatabaseInterface
	{
		$pool = self::getDBPool($database);

		$pool->query($sql, $params);

		return $pool;
	}

	/**
	* Executes a SQL query prepared then it throws it into an Object that is implemented by Iterator and Countable
	* This allows you to loop through a large query with out having to allocate additional memory to store the results.
	* It only grabs the row/result for a single row on each loop.
	* @param $sql - The sql you run
	* @param $params - Values that you wanna insert into the prepared statement
	* @param $database - The database you wanna execute this function on
	* @return DBResult
	*/
	public static function PResult(string $sql, array $params = [], string $database = DEFAULT_DB) : DBResult
	{
		$pool = self::getDBPool($database);

		$results = new DBResult($pool);
		if ($pool->query($sql, $params))
		{
			$results = $pool->getDBResult();
		}
		return $results;
	}

	/**
	* Returns all results in the query
	* @param $sql - The sql you run
	* @param $params - Values that you wanna insert into the prepared statement
	* @param $database - The database you wanna execute this function on
	* @return array
	*/
	public static function PExecute(string $sql, array $params = [], ?int &$out_count = 0, string $database = DEFAULT_DB) : array
	{
		$out_count = 0;

		$pool = self::getDBPool($database);

		$results = [];
		if ($pool->query($sql, $params))
		{
			$results = $pool->fetchAllResults();
			$out_count = $pool->rowCount();
		}
		return $results;
	}

	/**
	* Only returns a single result
	* @param $sql - The sql you run
	* @param $params - Values that you wanna insert into the prepared statement
	* @param $database - The database you wanna execute this function on
	* @return array
	*/
	public static function PSingle(string $sql, array $params = [], ?int &$out_count = 0, string $database = DEFAULT_DB) : array
	{
		$out_count = 0;

		$pool = self::getDBPool($database);

		$results = [];
		if ($pool->query($sql, $params))
		{
			$results = $pool->fetchResult();
			$out_count = $pool->rowCount();
		}
		return $results;
	}

	/**
	* Executing Raw Sql
	* @param $sql - The sql you run
	* @param $database - The database you wanna execute this function on
	* @return array
	*/
	public static function execute(string $sql, ?int &$out_count = 0, string $database = DEFAULT_DB) : array
	{
		$out_count = 0;

		$pool = self::getDBPool($database);

		$results = [];
		if ($pool->query($sql))
		{
			$results = $pool->fetchAllResults();
			$out_count = $pool->rowCount();
		}
		return $results;
	}

	public static function insert(string $table, array $params, &$last_insert_id = 0, string $database = DEFAULT_DB) : bool
	{
		$pool = self::getDBPool($database);

		if (!$pool->query(DatabaseLib::generateInsertSQL($table, $params), $params))
		{
			return false;
		}
		$last_insert_id = $pool->getLastInsertID();
		return true;
	}

	public static function update(string $table, array $set_params, array $where_params, string $database = DEFAULT_DB) : bool
	{
		$pool = self::getDBPool($database);

		$prepared = [];
		$sql = DatabaseLib::generateUpdateSQL($table, $set_params, $where_params, $prepared);

		if (!$pool->query($sql, $prepared))
		{
			return false;
		}
		return true;
	}

	public static function delete(string $table, array $where_params, string $database = DEFAULT_DB) : bool
	{
		$pool = self::getDBPool($database);

		$prepared = [];
		$sql = DatabaseLib::generateDeleteSQL($table, $where_params, $prepared);

		if (!$pool->query($sql, $prepared))
		{
			return false;
		}
		return true;
	}

	/**
	* Start a transaction to wrap all queries till the transaction ends.
	* @param $database - The database you wanna execute this function on
	* @return bool
	*/
	public static function startTransaction(string $database = DEFAULT_DB) : bool
	{
		$pool = self::getDBPool($database);

		return $pool->beginTransaction();
	}

	/**
	* Commits all queries in the transaction and ends the transaction.
	* @param $database - The database you wanna execute this function on
	* @return bool
	*/
	public static function commitTransaction(string $database = DEFAULT_DB) : bool
	{
		$pool = self::getDBPool($database);

		return $pool->commitTransaction();
	}

	/**
	* Rollback all queries in the transaction and ends the transaction.
	* @param $database - The database you wanna execute this function on
	* @return bool
	*/
	public static function rollbackTransaction(string $database = DEFAULT_DB) : bool
	{
		$pool = self::getDBPool($database);

		return $pool->rollbackTransaction();
	}

	public static function quote(string $value) : ?string
	{
		$pool = self::getDBPool(DEFAULT_DB);

		return $pool->quote($value);
	}

	private static function getDBPool(string $database) : DatabaseInterface
	{
		$pool = DBPool::getDBI($database);
		if ($pool === null)
		{
			throw new DatabaseException(implode(', ', DBPool::getPoolErrors($database)));
		}
		return $pool;
	}
}