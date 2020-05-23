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
		$verification->setACCTID($this->model->getACCTID());
		$verification->setPrimaryKey(KeyGenerator::generateToken(24));

		$verification_entity = $verification->createEntity();
		$verification->setDateExpire(date(DATEFORMAT_STANDARD, time() + $verification_entity->getEntityCacheTime()));
		$verification_entity = $verification->createEntity();

		$verification_entity->store();

		$mail = new MailWrapper('noreply@kriekon.com', EMAILS['noreply@kriekon.com']);
		$mail->addAddress($this->model->getEmail(), SITE_NAME);

		$body = "Hello and welcome to the Social Network for Gamers!\n";
		$body .= "In order to get your start talking to your fellow gamers, we ask that you verify your email address :P.\n";
		$body .= "Please click on our lovely link to verify your email.\n\n";
		$body .= WWW_URL . '/user/verify/' . $verification->getVerificationCode() . "\n\n";
		$body .= 'Thanks for joining ' . SITE_NAME . ' and we hope we see you more often! :)';
		$mail->send('Welcome to ' . SITE_NAME . '!', $body);

		return true;
	}

	public function login() : bool
	{

	}

	public function verify(string $verification_code) : bool
	{
		$verify = new VerificationModel();
		$entity = $verify->createEntity();

		$verify = $entity->find($verification_code);

		if (!$verify->isInitialized())
		{
			$this->addError('Verification code does not exist, or it has expired. Please request another to use our lovely site :P');
			return false;
		}

		$account = new AccountModel();
		$account_entity = $account->createEntity();

		$account = $account_entity->find($verify->getACCTID());
		$account->setVerified(AccountModel::VERIFIED_ON);

		if (!$account->isInitialized())
		{
			$this->addError('Account no longer exists.');
			return false;
		}

		if (!$account_entity->update(['verified']))
		{
			$this->addError('Unable to verify the account.');
			return false;
		}

		$entity->delete();

		$mail = new MailWrapper('noreply@kriekon.com', EMAILS['noreply@kriekon.com']);
		$mail->addAddress($account->getEmail(), SITE_NAME);

		$body = "Welcome my friend! Your account has now been verified, and you can now start your adventure on " . SITE_NAME . "!\n";
		$body .= "Try to keep the shit posting to a minimum, but hey :P Freedom of Speech.\n\n";
		$body .= "Enjoy!";
		$mail->send('The Real Welcome to ' . SITE_NAME . ' :P', $body);

		return true;
	}
}