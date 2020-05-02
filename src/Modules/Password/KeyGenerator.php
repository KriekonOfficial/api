<?php

namespace Modules\Password;

class KeyGenerator
{
	public static function generateToken(int $length = 12) : string
	{
		return substr(md5(bin2hex(random_bytes($length))), $length);
	}
}