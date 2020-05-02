<?php

namespace Modules\Account\Models;

use Core\Model\BaseValidator;
use Modules\Account\Account;

class AccountValidator extends BaseValidator
{
	public function validateEmail(string $email) : bool
	{
		if (empty($email))
		{
			$this->addError('Email must be specified.');
			return false;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$this->addError('Not a valid email address');
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