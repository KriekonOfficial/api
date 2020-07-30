<?php

namespace Core\Util;

class KeyGenerator
{
	public static function generateToken(int $length = 12) : string
	{
		return substr(bin2hex(random_bytes($length)), 0, $length);
	}

	public static function generateMD5Token(int $iteration = 45) : string
	{
		return md5(bin2hex(random_bytes($iteration)));
	}
}
