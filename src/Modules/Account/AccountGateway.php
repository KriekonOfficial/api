<?php

namespace Modules\Account;

use Core\ErrorBase;
use Modules\Account\Models\AccountModel;
use Modules\Account\Models\AccountValidator;
use Modules\Password\PasswordModel;
use Modules\Password\PasswordValidator;

class AccountGateway extends ErrorBase
{
	private $model;

	public function __construct(AccountModel $model)
	{
		$this->model = $model;
	}

	public function register(PasswordModel $password) : bool
	{
		$this->model->setRegistrationTime(date(DATEFORMAT_STANDARD));

		$account_validator = new AccountValidator($this->model);
		$account_validator->addRule('validateEmail', ['email']);
		if (!$account_validator->validate())
		{
			$this->addError($account_validator->getErrors());
			return false;
		}

		$password_validator = new PasswordValidator($password);
		$password_validator->addRule('validateStrength', ['password']);
		if (!$password_validator->validate())
		{
			$this->addError($password_validator->getErrors());
			return false;
		}
		return true;
	}

	public function login() : bool
	{

	}
}