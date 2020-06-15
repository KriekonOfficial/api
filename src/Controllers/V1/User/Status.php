<?php

namespace Controllers\V1\User;

use Core\Controller;
use Core\Request\Request;
use Core\Response\ErrorResponse;
use Core\Response\SuccessResponse;

use Modules\Status\StatusGateway;

class Status extends Controller
{
	public function listStatus(Request $request, array $get_params)
	{
		$page = $get_params['page'] ?? 1;
		$per_page = $get_params['per_page'] ?? 25;

		$status = new StatusGateway();
		$list = $status->listStatus($request->getAuth()->getAccount()->getACCTID(), $page, $per_page);

		if ($status->hasError())
		{
			return new ErrorResponse(400, $status->getErrors());
		}

		return new SuccessResponse(200, [
			'page' => $page,
			'threads' => $list
		]);
	}

	public function createStatus(Request $request)
	{
		return new SuccessResponse(200, []);
	}

	public function getStatus(Request $request, int $status_id)
	{
		return new SuccessResponse(200, []);
	}

	public function updateStatus(Request $request)
	{

	}

	public function deleteStatus(Request $request, int $status_id)
	{

	}
}