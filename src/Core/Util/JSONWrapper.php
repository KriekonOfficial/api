<?php

namespace Core\Util;

class JSONWrapper
{
	public static function json(array $array) : string
	{
		return json_encode($array, JSON_FORCE_OBJECT);
	}

	public static function decode(string $json) : ?array
	{
		return json_decode($json, true);
	}
}