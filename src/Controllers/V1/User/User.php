<?php

namespace Controllers\V1\User;

use Core\Controller;
use Core\Request\Request;
use Core\Response\ErrorResponse;
use Core\Response\SuccessResponse;

use Modules\Password\PasswordModel;
use Modules\User\UserGateway;
use Modules\User\Models\UserModel;

class User extends Controller
{
	public function __construct()
	{
		foreach (['register', 'login', 'verify'] as $method)
		{
			$this->addRequiredAuth($method, false);
		}
	}

	public function info(Request $request)
	{
		$user = $request->getAuth()->getUser();

		return new SuccessResponse(200, $user->toPublicArray(), 'User Information');
	}

	public function register(Request $request)
	{
		$input = $request->getRequestInput();

		$model = new UserModel();
		$model->setEmail(trim($input->get('email') ?? ''));
		$model->setDateOfBirth(trim($input->get('date_of_birth') ?? ''));

		$password = new PasswordModel($input->get('password') ?? '');
		$model->setPasswordHash($password->generatePasswordHash());

		$gateway = new UserGateway($model);
		if (!$gateway->register($password))
		{
			return new ErrorResponse(400, $gateway->getErrors());
		}

		return new SuccessResponse(200, [], 'Welcome to Kriekon, an email verification code has been sent to \'' . $model->getEmail() . '\'.');
	}

	public function login(Request $request)
	{
		$input = $request->getRequestInput();

		$model = new UserModel();
		$model->setEmail(trim($input->get('email') ?? ''));

		$password = new PasswordModel($input->get('password') ?? '');

		$gateway = new UserGateway($model);
		if (!$gateway->login($password, Request::getRequestIP(), $oauth))
		{
			return new ErrorResponse(405, $gateway->getErrors());
		}

		return new SuccessResponse(200, [
			'bearer_token' => $oauth->getBearerToken(),
			'date_expiration' => $oauth->getDateExpiration()
		], 'Login Successful');
	}

	public function verify(Request $request, string $verification_code)
	{
		$model = new UserModel();
		$user = new UserGateway($model);

		if (!$user->verifyEmail($verification_code))
		{
			return new ErrorResponse(404, $user->getErrors());
		}

		return new SuccessResponse(200, [], 'Congratulations your user is now verified! Happy posting :)');
	}
}
