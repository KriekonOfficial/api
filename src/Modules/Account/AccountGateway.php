<?php

namespace Modules\Account;

use Core\ErrorBase;
use Modules\Account\Models\AccountModel;
use Modules\Account\Models\AccountValidator;
use Modules\Account\Models\VerificationModel;
use Modules\Password\PasswordModel;
use Modules\Password\PasswordValidator;
use Modules\Password\KeyGenerator;
use Core\Util\MailWrapper;

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
		$this->model = $entity->store();

		$verification = new VerificationModel();
		$verification->setPrimaryKey($this->model->getACCTID());
		$verification->setVerificationCode(KeyGenerator::generateToken(24));

		$verification_entity = $verification->createEntity();
		$verification->setDateExpire(date(DATEFORMAT_STANDARD, time() + $verification_entity->getEntityCacheTime()));
		$verification_entity = $verification->createEntity();

		$verification_entity->store();

		$mail = new MailWrapper('noreply@kriekon.com', EMAILS['noreply@kriekon.com']);
		$mail->addAddress($this->model->getEmail(), SITE_NAME);

		$body = "Hello and welcome to the Social Network for Gamers!\n";
		$body .= "In order to get your start talking to your fellow gamers we ask that you verify your email address :P.\n";
		$body .= "Please click on our lovely link to verify your email.\n";
		$body .= WWW_URL . '/user/verify/' . $verification->getVerificationCode();
		$mail->send('Welcome to Kriekon!', $body);

		return true;
	}

	public function login() : bool
	{

	}

	public function verify(string $verification_code)
	{

	}
}