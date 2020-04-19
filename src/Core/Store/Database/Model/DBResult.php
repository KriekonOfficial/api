<?php

namespace Core\Store\Database\Model;

use Core\Store\Database\Interfaces\DatabaseInterface;

class DBResult implements \Iterator, \Countable
{
	private $pdo_object;

	private int $pointer = -1;
	private array $record = [];

	public function __construct(DatabaseInterface $pdo_object)
	{
		$this->pdo_object = $pdo_object;
	}

	/**
	* Get the current row on this index/key for the loop
	* @return array
	*/
	public function getRecord() : array
	{
		return $this->current();
	}

	////
	// Iterator Functions
	////

	/**
	* Lets grab the current record.
	* Do not use this function, use getRecord instead.
	* @return array
	*/
	public function current() : array
	{
		return $this->record;
	}

	/**
	* Current index/key for the loop
	* @return int
	*/
	public function key() : int
	{
		return $this->pointer;
	}

	/**
	* Have we reached the end of our iterator?
	* @return bool
	*/
	public function valid() : bool
	{
		return ($this->pointer < $this->count());
	}

	/**
	* Moves the pointer to the next result
	* @return void
	*/
	public function next() : void
	{
		$this->record = $this->pdo_object->fetchResult();
		$this->pointer++;
	}

	/**
	* Executed at the beginning of the loop
	* @return void
	*/
	public function rewind() : void
	{
		$this->next();
	}

	////
	// Countable Functions
	////

	/**
	* Gets the total amount of rows that will be returned from the query.
	* @return int
	*/
	public function count() : int
	{
		return $this->pdo_object->rowCount();
	}
}