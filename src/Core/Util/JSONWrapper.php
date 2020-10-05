<?php

namespace Core\Util;

class JSONWrapper
{
	private static array $errors = [];

	public static function json(array $array, $constant = 0) : string
	{
		self::$errors = [];

		$json = json_encode($array, $constant);

		if ($json === false)
		{
			self::$errors[] = json_last_error_msg();
			return '';
		}
		return $json;
	}

	public static function decode(string $json) : ?array
	{
		self::$errors = [];

		$decode = json_decode($json, true);
		if (json_last_error() !== JSON_ERROR_NONE)
		{
			self::$errors[] = json_last_error_msg();
			return null;
		}
		return $decode;
	}

	public static function getErrors() : array
	{
		return self::$errors;
	}

	public static function getLastError() : string
	{
		$error = end(self::$errors);
		return $error !== false ? $error : '';
	}
}
