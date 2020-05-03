<?php

namespace Modules\Account\Models;

use Core\Model\BaseValidator;
use Modules\Account\Account;
use \DateTime;

class AccountValidator extends BaseValidator
{
	public function validateEmail(string $email) : bool
	{
		$email = trim($email);
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
			$this->addError('The email address \'' . $email . '\' already exists.');
			return false;
		}
		return true;
	}

	public function validateDateOfBirth(string $date_of_birth) : bool
	{
		$format = 'Y-m-d';
		$date = DateTime::createFromFormat($format, $date_of_birth);

		if (!($date && ($date->format($format) === $date_of_birth)))
		{
			$this->addError('Date of Birth is in an improper format, must be YYYY-mm-dd');
			return false;
		}
    	return true;
	}

	public function validateAge(string $date_of_birth, int $age) : bool
	{
		if ($this->getModel()->getAge() < $age)
		{
			$this->addError('You must be ' . $age . ' years or older to use our platform. Sorry :(');
			return false;
		}
		return true;
	}
}