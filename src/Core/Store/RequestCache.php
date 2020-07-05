<?php

namespace Core\Store;

/**
* Stores an item in memory for a single request nothing more.
*/
class RequestCache
{
	private static array $cache = [];

	/**
	* @param $key - the key that is stored inside static prop $cache
	* @param $value - mixed, this can be anything you want.
	* @return void
	*/
	public static function setCacheItem(string $key, $value) : void
	{
		self::$cache[$key] = $value;
	}

	/**
	* Will not add item if something already exists there.
	* @see addCacheItem
	*/
	public static function addCacheItemIfNotSet(string $key, $value) : void
	{
		if (!isset(self::$cache[$key]))
		{
			self::$cache[$key] = $value;
		}
	}

	/**
	* @return mixed|null on failure
	*/
	public static function getCacheItem(string $key)
	{
		return self::$cache[$key] ?? null;
	}

	public static function hasCacheItem(string $key) : bool
	{
		return isset(self::$cache[$key]);
	}

	/**
	* Removes cache item from static prop
	*/
	public static function deleteCacheItem(string $key) : void
	{
		unset(self::$cache[$key]);
	}

	/**
	* Clears entire cache.
	*/
	public static function resetCache() : void
	{
		self::$cache = [];
	}
}