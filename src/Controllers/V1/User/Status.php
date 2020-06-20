<?php

namespace Controllers\V1\User;

use Core\Controller;
use Core\Request\Request;
use Core\Response\ErrorResponse;
use Core\Response\SuccessResponse;

use Modules\Status\StatusGateway;
use Modules\Status\StatusEntity;
class Status extends Controller
{
	public function listStatus(Request $request, array $get_params)
	{
		$page = $get_params['page'] ?? 1;
		$per_page = $get_params['per_page'] ?? 25;

		$status = new StatusGateway($request->getAuth()->getAccount());
		$list = $status->listStatus((int)$page, (int)$per_page);

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

	public function getStatus(Request $request, $status_id)
	{
		if (!is_numeric($status_id))
		{
			return new ErrorResponse(400, 'Status ID must be an integer.');
		}

		$entity = new StatusEntity();
		$model = $entity->find((int)$status_id);

		if (!$model->isInitialized())
		{
			return new ErrorResponse(404, 'Status does not exist.');
		}

		return new SuccessResponse(200, $model->toPublicArray());
	}

	public function updateStatus(Request $request)
	{

	}

	public function deleteStatus(Request $request, $status_id)
	{
		if (!is_numeric($status_id))
		{
			return new ErrorResponse(400, 'Status ID must be an integer.');
		}

		$status = new StatusGateway($request->getAuth()->getAccount());

		if (!$status->deleteStatus((int)$status_id))
		{
			return new ErrorResponse(404, $status->getErrors());
		}
	}
}