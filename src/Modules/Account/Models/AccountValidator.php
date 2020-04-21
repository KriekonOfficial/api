<?php

namespace Modules\Account\Models;

use Core\Model\Validator;
use Modules\Account\Account;

class AccountValidator extends Validator
{
	public function validateEmail($email) : bool
	{
		if (!is_string($email))
		{
			$this->addError('The email address must be a string.');
			return false;
		}

		$entity = new Account();
		$model = $entity->findEmail($email);

		if ($model->isInitialized())
		{
			$this->addError('The email address ' . $email . ' already exists.');
			return false;
		}
		return true;
	}
}