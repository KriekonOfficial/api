<?php

namespace Controllers\Root\Test;

class Auth extends \Core\Controller
{
	public function index()
	{
		echo "Auth Index Test";
	}

	public function login()
	{
		echo "Login Test";
	}

	public function login_params($get_params, \Core\Request\Request $request, $test)
	{
		$response = new \Core\Response\ErrorResponse(405, 'Test');
		return $response;
	}
}