<?php

namespace Modules\Account\Models;

use Core\Model\BaseValidator;
use Modules\Account\Account;

class AccountValidator extends BaseValidator
{
	public function validateEmail(string $email) : bool
	{
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