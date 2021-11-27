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

		$gate = new StatusGateway();
		$total = 0;
		$list = $gate->listStatus($user, (int)$page, (int)$per_page, $total);

		if ($gate->hasError())
		{
			return new ErrorResponse(400, $gate->getErrors());
		}

		$output = [];
		foreach ($list as $status)
		{
			$output[] = $status->toPublicArray();
		}

		$response = new SuccessResponse(200, [
			'threads' => $output
		]);

		$response->setMeta([
			'total' => $total,
			'page' => $page
		]);
		return $response;
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

		$status = new StatusGateway();
		$model = $status->createStatus($user, $status_content);
		if ($model === null)
		{
			return new ErrorResponse(400, $status->getErrors());
		}

		return new SuccessResponse(200, $model->toPublicArray(), 'Status Created.');
	}

	public function getStatus(Request $request, int $status_id)
	{
		$status = new StatusGateway();
		$model = $status->getStatus($status_id);

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

		$status = new StatusGateway();

		$model = $status->getStatus($status_id, true);
		if (!$model->isInitialized())
		{
			return new ErrorResponse(404, 'Status does not exist.');
		}

		if ($model->getUserID() != $request->getAuth()->getUser()->getUserID())
		{
			return new ErrorResponse(403, 'Invalid access.');
		}

		$model = $status->updateStatus($model->getStatusID(), $status_content);
		if ($model === null)
		{
			return new ErrorResponse($status->getHttpCode(), $status->getErrors());
		}
		return new SuccessResponse(200, $model->toPublicArray(), 'Status updated.');
	}

	public function deleteStatus(Request $request, int $status_id)
	{
		$status = new StatusGateway();

		$model = $status->getStatus($status_id, true);
		if (!$model->isInitialized())
		{
			return new ErrorResponse(404, 'Status does not exist.');
		}

		if ($model->getUserID() != $request->getAuth()->getUser()->getUserID())
		{
			return new ErrorResponse(403, 'Invalid access.');
		}

		if (!$status->deleteStatus($model->getStatusID()))
		{
			return new ErrorResponse($status->getHttpCode(), $status->getErrors());
		}

		return new SuccessResponse(200, [], 'Status deleted');
	}

	public function listComment(Request $request, int $status_id, array $get_params)
	{
		$page = $get_params['page'] ?? 1;
		$per_page = $get_params['per_page'] ?? 25;

		$gate = new StatusGateway();
		$status = $gate->getStatus($status_id);
		if (!$status->isInitialized())
		{
			return new ErrorResponse(400, 'Status thread does not exist.');
		}

		$total = 0;
		$list = $gate->listComment($status, (int)$page, (int)$per_page, $total);

		if ($gate->hasError())
		{
			return new ErrorResponse(400, $gate->getErrors());
		}

		$output = [];
		foreach ($list as $comment)
		{
			$output[] = $comment->toPublicArray();
		}

		$response = new SuccessResponse(200, [
			'threads' => $output
		]);

		$response->setMeta([
			'total' => $total,
			'page' => $page
		]);
		return $response;
	}

	public function getComment(Request $request, int $comment_id)
	{
		$status = new StatusGateway();
		$model = $status->getComment($comment_id);

		if (!$model->isInitialized())
		{
			return new ErrorResponse(404, 'Comment does not exist.');
		}

		return new SuccessResponse(200, $model->toPublicArray());
	}

	public function createComment(Request $request, int $status_id)
	{
		$user = $request->getAuth()->getUser();

		$gate = new StatusGateway();
		$input = $request->getRequestInput();
		$comment_content = $input->get('comment_content');
		if ($comment_content === null)
		{
			return new ErrorResponse(400, 'Invalid parameter, missing comment_content.');
		}

		$status = $gate->getStatus($status_id);

		$comment = $gate->createComment($user, $status, $comment_content);
		if ($comment === null)
		{
			return new ErrorResponse($gate->getHttpCode(), $gate->getErrors());
		}

		return new SuccessResponse(200, $comment->toPublicArray(), 'Comment created');
	}

	public function updateComment(Request $request, int $comment_id)
	{
		$input = $request->getRequestInput();
		$comment_content = $input->get('comment_content');
		if ($comment_content === null)
		{
			return new ErrorResponse(400, 'Invalid parameter, missing comment.');
		}

		$gate = new StatusGateway();

		$model = $gate->getComment($comment_id, true);
		if (!$model->isInitialized())
		{
			return new ErrorResponse(404, 'Comment does not exist.');
		}

		if ($model->getUserID() != $request->getAuth()->getUser()->getUserID())
		{
			return new ErrorResponse(403, 'Invalid access.');
		}

		$model = $gate->updateComment($model->getCommentID(), $comment_content);
		if ($model === null)
		{
			return new ErrorResponse($gate->getHttpCode(), $gate->getErrors());
		}
		return new SuccessResponse(200, $model->toPublicArray(), 'Status updated.');
	}

	public function deleteComment(Request $request, int $comment_id)
	{
		$gate = new StatusGateway();
		$model = $gate->getComment($comment_id, true);
		if (!$model->isInitialized())
		{
			return new ErrorResponse(404, 'Comment no longer exists.');
		}

		if ($model->getUserID() != $request->getAuth()->getUser()->getUserID())
		{
			return new ErrorResponse(403, 'Invalid access.');
		}

		if (!$gate->deleteComment($model->getCommentID()))
		{
			return new ErrorResponse($gate->getHttpCode(), $gate->getErrors());
		}

		return new SuccessResponse(200, [], 'Comment deleted.');
	}
}
