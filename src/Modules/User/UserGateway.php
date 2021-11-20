<?php

namespace Modules\User;

use Core\ErrorBase;
use Core\APIError;
use Modules\User\Models\UserModel;
use Modules\User\Models\UserValidator;
use Modules\User\Models\VerificationModel;
use Modules\Password\PasswordModel;
use Modules\Password\PasswordValidator;
use Core\Util\KeyGenerator;
use Core\Util\TimeUtils;
use Classes\MailWrapper;
use Modules\Auth\Models\OAuthBearerModel;

class UserGateway extends ErrorBase
{
	private $model;

	public function __construct(UserModel $model)
	{
		$this->model = $model;
	}

	public function register(PasswordModel $password) : bool
	{
		$this->model->setRegistrationTime(date(TimeUtils::DATEFORMAT_STANDARD));

		$user_validator = new UserValidator($this->model);
		$user_validator->addRule('validateEmail', ['email']);
		$user_validator->addRule('validateDateOfBirth', ['date_of_birth']);
		$user_validator->addRule('validateAge', ['date_of_birth'], ['date_of_birth' => 16]);
		if (!$user_validator->validate())
		{
			$this->addError($user_validator->getErrors());
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
		$verification->setUSERID($this->model->getUSERID());
		$verification->setPrimaryKey(KeyGenerator::generateToken(24));

		$verification_entity = $verification->createEntity();
		$verification->setDateExpire(date(TimeUtils::DATEFORMAT_STANDARD, time() + $verification_entity->getEntityCacheTime()));
		$verification_entity = $verification->createEntity();

		$verification_entity->store();

		try
		{
			$mail = new MailWrapper(MailWrapper::getDefaultEmail(), MailWrapper::getDefaultPassword());
			$mail->addAddress($this->model->getEmail(), SITE_NAME);

			$body = "Hello and welcome to the Social Network for Gamers!\n";
			$body .= "In order to get your start talking to your fellow gamers, we ask that you verify your email address :P.\n";
			$body .= "Please click on our lovely link to verify your email.\n\n";
			$body .= WWW_URL . '/user/verify/' . $verification->getVerificationCode() . "\n\n";
			$body .= 'Thanks for joining ' . SITE_NAME . ' and we hope we see you more often! :)';
			$mail->send('Welcome to ' . SITE_NAME . '!', $body);
		}
		catch (APIError $e)
		{
			// Continue
			$this->addError('Failed to send welcome email');
			$this->addErrorInternal($e->getMessage());
			return false;
		}

		return true;
	}

	public function login(PasswordModel $password, string $ip_address, ?OAuthBearerModel &$bearer = null) : bool
	{
		$entity = $this->model->createEntity();
		$this->model = $entity->findEmail($this->model->getEmail());

		if (!$this->model->isInitialized())
		{
			$this->addError('Email does not exist.');
			return false;
		}

		if (!$password->verifyPasswordHash($this->model->getPasswordHash()))
		{
			$this->addError('Invalid Password, please try again.');
			return false;
		}

		$bearer = new OAuthBearerModel();
		$bearer->setAccessToken(KeyGenerator::generateToken(24));
		$bearer->setUSERID($this->model->getUSERID());
		$bearer->setAuthorizedIP($ip_address);
		$bearer->setDateExpiration(date(TimeUtils::DATEFORMAT_STANDARD, strtotime('+1 hour')));
		$bearer->generateBearerToken();

		$bearer_entity = $bearer->createEntity();
		$bearer = $bearer_entity->store();
		if (!$bearer->isInitialized())
		{
			$this->addError('Failed to login, please try again later.');
			return false;
		}
		return true;
	}

	public function verifyEmail(string $verification_code) : bool
	{
		$verify = new VerificationModel();
		$entity = $verify->createEntity();

		$verify = $entity->find($verification_code);

		if (!$verify->isInitialized())
		{
			$this->addError('Verification code does not exist, or it has expired. Please request another to use our lovely site :P');
			return false;
		}

		$user = new UserModel();
		$user_entity = $user->createEntity();

		$user = $user_entity->find($verify->getUSERID());
		$user->setVerified(UserModel::VERIFIED_ON);

		if (!$user->isInitialized())
		{
			$this->addError('User no longer exists.');
			return false;
		}

		if (!$user_entity->update(['verified']))
		{
			$this->addError('Unable to verify the user.');
			return false;
		}

		$entity->delete();

		$mail = new MailWrapper(MailWrapper::getDefaultEmail(), MailWrapper::getDefaultPassword());
		$mail->addAddress($user->getEmail(), SITE_NAME);

		$body = "Welcome my friend! Your user has now been verified, and you can now start your adventure on " . SITE_NAME . "!\n";
		$body .= "Try to keep the shit posting to a minimum, but hey :P Freedom of Speech.\n\n";
		$body .= "Enjoy!";
		$mail->send('The Real Welcome to ' . SITE_NAME . ' :P', $body);

		return true;
	}
}
