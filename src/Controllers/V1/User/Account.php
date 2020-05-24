<?php

namespace Controllers\V1\User;

use Core\Controller;
use Core\Request\Request;
use Core\Response\ErrorResponse;
use Core\Response\SuccessResponse;

use Modules\Password\PasswordModel;
use Modules\Account\AccountGateway;
use Modules\Account\Models\AccountModel;

class Account extends Controller
{
	public function __construct()
	{
		foreach (['register', 'login', 'verify'] as $method)
		{
			$this->addRequestMetadata($method, false);
		}
	}

	public function register(Request $request)
	{
		$input = $request->getRequestInput();

		$model = new AccountModel();
		$model->setEmail(trim($input->get('email') ?? ''));
		$model->setDateOfBirth(trim($input->get('date_of_birth') ?? ''));

		$password = new PasswordModel($input->get('password') ?? '');
		$model->setPasswordHash($password->generatePasswordHash());

		$gateway = new AccountGateway($model);
		if (!$gateway->register($password))
		{
			return new ErrorResponse(400, $gateway->getErrors());
		}

		return new SuccessResponse(200, [], 'Welcome to Kriekon, an email verification code has been sent to \'' . $model->getEmail() . '\'.');
	}

	public function login(Request $request)
	{
		return new ErrorResponse(403, 'Test1234');
	}

	public function verify(Request $request, string $verification_code)
	{
		$input = $request->getRequestInput();

		$model = new AccountModel();
		$account = new AccountGateway($model);

		if (!$account->verify($verification_code))
		{
			return new ErrorResponse(404, $account->getErrors());
		}

		return new SuccessResponse(200, [], 'Congratulations your account is now verified! Happy posting :)');
	}
}