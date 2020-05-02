<?php

namespace Modules\Password;

use Core\Model\BaseValidator;

class PasswordValidator extends BaseValidator
{
	public function validateStrength(string $value) : bool
	{
		if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value))
		{
			$this->addError('Password must be a minimum of eight characters, at least one uppercase letter, one lowercase letter, one number, and one special character');
			return false;
		}
		return true;
	}
}