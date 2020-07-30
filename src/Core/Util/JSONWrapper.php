<?php

namespace Core\Util;

class JSONWrapper
{
	public static function json(array $array, $constant = null) : string
	{
		if ($constant !== null)
		{
			return json_encode($array, $constant);
		}
		return json_encode($array);
	}

	public static function decode(string $json) : ?array
	{
		return json_decode($json, true);
	}
}
