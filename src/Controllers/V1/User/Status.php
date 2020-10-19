<?php

namespace Controllers\V1\User;

use Core\Controller;
use Core\Request\Request;
use Core\Response\ErrorResponse;
use Core\Response\SuccessResponse;

use Modules\Status\StatusGateway;
use Modules\Status\StatusEntity;
use Modules\User\User;
class Status extends Controller
{
	public function listStatus(Request $request, array $get_params)
	{
		$USERID = $get_params['USERID'] ?? false;
		$page = $get_params['page'] ?? 1;
		$per_page = $get_params['per_page'] ?? 25;

		$user = $request->getAuth()->getUser();
		if ($USERID !== false)
		{
			$user_entity = new User();
			$user = $user_entity->find($USERID);
		}

		$gate = new StatusGateway($user);
		$list = $gate->listStatus((int)$page, (int)$per_page, $total);

		if ($gate->hasError())
		{
			return new ErrorResponse(400, $gate->getErrors());
		}

		$output = [];
		foreach ($list as $status)
		{
			$output[] = $status->toPublicArray();
		}

		return new SuccessResponse(200, [
			'total'   => $total,
			'page'    => $page,
			'threads' => $output
		]);
	}

	public function createStatus(Request $request)
	{
		$input = $request->getRequestInput();

		$status_content = $input->get('status_content');
		if ($status_content === null)
		{
			return new ErrorResponse(400, 'Invalid parameter, missing status_content.');
		}
		$user = $request->getAuth()->getUser();

		$status = new StatusGateway($user);
		if (!$status->createStatus($status_content))
		{
			return new ErrorResponse(400, $status->getErrors());
		}

		return new SuccessResponse(200, [], 'Status Created.');
	}

	public function getStatus(Request $request, int $status_id)
	{
		$entity = new StatusEntity();
		$model = $entity->find((int)$status_id);

		if (!$model->isInitialized())
		{
			return new ErrorResponse(404, 'Status does not exist.');
		}

		return new SuccessResponse(200, $model->toPublicArray());
	}

	public function updateStatus(Request $request, int $status_id)
	{
		$input = $request->getRequestInput();
		$status_content = $input->get('status_content');
		if ($status_content === null)
		{
			return new ErrorResponse(400, 'Invalid parameter, missing status_content.');
		}

		$status = new StatusGateway($request->getAuth()->getUser());

		if (!$status->updateStatus($status_id, $status_content))
		{
			return new ErrorResponse($status->getHttpCode(), $status->getErrors());
		}
		return new SuccessResponse(200, [], 'Status updated.');
	}

	public function deleteStatus(Request $request, int $status_id)
	{
		$status = new StatusGateway($request->getAuth()->getUser());

		if (!$status->deleteStatus((int)$status_id))
		{
			return new ErrorResponse($status->getHttpCode(), $status->getErrors());
		}

		return new SuccessResponse(200, [], 'Status has been deleted');
	}

	public function listComment(Request $request, int $status_id, array $get_params)
	{
		$USERID = $get_params['USERID'] ?? false;
		$page = $get_params['page'] ?? 1;
		$per_page = $get_params['per_page'] ?? 25;

		$user = $request->getAuth()->getUser();
		if ($USERID !== false)
		{
			$user_entity = new User();
			$user = $user_entity->find($USERID);
		}

		$gate = new StatusGateway($user);
	}
}
