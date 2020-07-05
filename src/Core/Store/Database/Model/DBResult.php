<?php

namespace Core\Store\Database\Model;

use \Iterator;
use \Countable;
use Core\Store\Database\Interfaces\DatabaseInterface;

class DBResult implements Iterator, Countable
{
	private $pdo_object;

	private int $pointer = -1;
	private array $record = [];
	private int $total_count = 0;

	public function __construct(DatabaseInterface $pdo_object)
	{
		$this->pdo_object = $pdo_object;
		/**
		* Get the count immidately, before another query is executed.
		*/
		$this->total_count = $pdo_object->rowCount();

		/**
		* Populate the first entry before the loop
		*/
		if ($this->count() >= 1)
		{
			$this->next();
		}
		else if ($this->count() === 0)
		{
			/*
			* No entry don't attempt to start the loop.
			*/
			$this->pointer = 0;
		}
	}

	/**
	* Get the current row on this index/key for the loop
	* @return array
	*/
	public function getRecord() : array
	{
		return $this->record;
	}

	////
	// Iterator Functions
	////

	/**
	* Lets grab the current record.
	* Do not use this function, use getRecord instead.
	* @return array
	*/
	public function current()
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
		//Not needed.
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
		return $this->total_count;
	}
}