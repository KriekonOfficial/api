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
	public function register(Request $request)
	{
		$input = $request->getRequestInput();

		$model = new AccountModel();
		$model->setEmail($input['email'] ?? '');
		$model->setDateOfBirth($input['date_of_birth'] ?? '');

		$password = new PasswordModel($input['password'] ?? '');
		$model->setPasswordHash($password->generatePasswordHash());

		$gateway = new AccountGateway($model);
		if (!$gateway->register($password))
		{
			return new ErrorResponse(400, $gateway->getErrors());
		}

		return new SuccessResponse(200, 'Welcome to Kriekon, an email verification code has been sent to ' . $model->getEmail() . '.');
	}

	public function login(Request $request)
	{
		return new ErrorResponse(403, 'Test1234');
	}
}