<?php

namespace Modules\Password;

class KeyGenerator
{
	public static function generateToken(int $length = 12) : string
	{
		return md5(bin2hex(random_bytes($length)));
	}
}