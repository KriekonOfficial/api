<?php

namespace Controllers\V1\User;

use Core\Controller;
use Core\Request\Request;
use Core\Response\ErrorResponse;
use Core\Response\SuccessResponse;

class Status extends Controller
{
	public function listStatus(Request $request, array $get_params)
	{
		//get_params will be pagination options.
		return new ErrorResponse(200, 'Test', $get_params);
	}

	public function createStatus(Request $request)
	{
		return new SuccessResponse(200, []);
	}

	public function getStatus(Request $request, int $status_id)
	{
		return new SuccessResponse(200, []);
	}
}