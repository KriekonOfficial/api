<?php

namespace Controllers\V1\User;

use Core\Controller;
use Core\Request\Request;
use Core\Response\ErrorResponse;
use Core\Response\SuccessResponse;

use Modules\Status\StatusGateway;
use Modules\Status\StatusEntity;
use Modules\Account\Account;
class Status extends Controller
{
	public function listStatus(Request $request, array $get_params)
	{
		$ACCTID = $get_params['ACCTID'] ?? false;
		$page = $get_params['page'] ?? 1;
		$per_page = $get_params['per_page'] ?? 25;

		$account = $request->getAuth()->getAccount();
		if ($ACCTID !== false)
		{
			$account_entity = new Account();
			$account = $account_entity->find($ACCTID);
		}

		$status = new StatusGateway($account);
		$list = $status->listStatus((int)$page, (int)$per_page, $total);

		if ($status->hasError())
		{
			return new ErrorResponse(400, $status->getErrors());
		}

		return new SuccessResponse(200, [
			'total' => $total,
			'page' => $page,
			'threads' => $list
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
		$account = $request->getAuth()->getAccount();

		$status = new StatusGateway($account);
		if (!$status->createStatus($status_content))
		{
			return new ErrorResponse(400, $status->getErrors());
		}

		return new SuccessResponse(200, [], 'Status Created.');
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

	public function updateStatus(Request $request, $status_id)
	{
		if (!is_numeric($status_id))
		{
			return new ErrorResponse(400, 'Status ID must be an integer.');
		}

		$input = $request->getRequestInput();
		$status_content = $input->get('status_content');
		if ($status_content === null)
		{
			return new ErrorResponse(400, 'Invalid parameter, missing status_content.');
		}

		$status = new StatusGateway($request->getAuth()->getAccount());

		if (!$status->updateStatus($status_id, $status_content))
		{
			return new ErrorResponse($status->getHttpCode(), $status->getErrors());
		}
		return new SuccessResponse(200, [], 'Status updated.');
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
			return new ErrorResponse($status->getHttpCode(), $status->getErrors());
		}

		return new SuccessResponse(200, [], 'Status has been deleted');
	}
}
