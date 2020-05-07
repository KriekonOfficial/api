<?php

namespace Modules\Account;

use Core\ErrorBase;
use Modules\Account\Models\AccountModel;
use Modules\Account\Models\AccountValidator;
use Modules\Account\Models\VerificationModel;
use Modules\Password\PasswordModel;
use Modules\Password\PasswordValidator;
use Modules\Password\KeyGenerator;

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
		$account_validator->addValidator('validateAge', [16]);

		$account_validator->addRule('validateEmail', ['email']);
		$account_validator->addRule('validateDateOfBirth', ['date_of_birth']);
		$account_validator->addRule('validateAge', ['date_of_birth']);
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

		$entity = $this->model->createEntity();
		$model = $entity->store();

		$verification = new VerificationModel();
		$verification->setPrimaryKey($model->getACCTID());
		$verification->setVerificationCode(KeyGenerator::generateToken(24));

		$verification_entity = $verification->createEntity();
		$verification->setDateExpire(date(DATEFORMAT_STANDARD, time() + $verification_entity->getEntityCacheTime()));
		$verification_entity = $verification->createEntity();

		$verification_entity->store();

		return true;
	}

	public function login() : bool
	{

	}
}