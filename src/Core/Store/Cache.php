<?php

namespace Core\Store;

use \Redis;
use Core\Util\JSONWrapper;

class Cache
{
	private static $memory = null;

	private function __construct()
	{
		self::$memory = new Redis();
		self::$memory->connect('127.0.0.1', 6379);
	}

	private static function initialize()
	{
		if (self::$memory === null)
		{
			return new Cache();
		}
		return true;
	}

	/**
	* @param $key - Redis key to set the value to.
	* @param $value - a string that gets paired with the redis key.
	* @param $time - Optional - 0 infinite, how long should this key live in seconds.
	* @return bool
	*/
	public static function set(string $key, string $value, int $time = 0) : bool
	{
		self::initialize();

		if ($time == 0)
		{
			if (!self::$memory->set($key, $value))
			{
				return false;
			}
		}
		else
		{
			if (!self::$memory->set($key, $value, $time))
			{
				return false;
			}
		}
		return true;
	}

	/**
	* @param $key - Redis key to set the value to.
	* @param $value - an array that will be encoded into a json
	* @param $time - Optional - 0 infinite, how long should this key live in seconds.
	* @return bool
	*/
	public static function setArray(string $key, array $value, int $time = 0) : bool
	{
		$value = JSONWrapper::json($value);

		if (JSONWrapper::hasError())
		{
			return false;
		}

		return self::set($key, $value, $time);
	}

	/**
	* @param $key - Redis key to get the value of.
	* @return mixed|null on failure
	*/
	public static function get(string $key)
	{
		self::initialize();

		if (!self::$memory->exists($key))
		{
			return null;
		}
		return self::$memory->get($key);
	}

	/**
	* @param $key - Redis key to get the value of.
	* @return array|null on failure
	*/
	public static function getArray(string $key) : ?array
	{
		$json = self::get($key);
		if ($json === null)
		{
			return null;
		}

		return JSONWrapper::decode($json);
	}

	/**
	* @param $key - Redis key to check to see if it exists.
	* @return bool
	*/
	public static function exists(string $key) : bool
	{
		self::initialize();

		return self::$memory->exists($key);
	}

	/**
	* @param $key - Redis key to delete from redis.
	* @return bool
	*/
	public static function delete(string $key) : bool
	{
		self::initialize();

		if (!self::$memory->del($key))
		{
			return false;
		}
		return true;
	}
}
