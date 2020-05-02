<?php

namespace Modules\Password;

use Core\Model\BaseValidator;

class PasswordValidator extends BaseValidator
{
	public static function verifyPasswordHash(string $password_hash, string $verify_hash) : bool
	{
		return password_verify($password_hash, $verify_hash);
	}
}