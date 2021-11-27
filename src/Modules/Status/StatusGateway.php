<?php

namespace Modules\Status;

use \Iterator;
use Core\ErrorBase;
use Core\Logger\Logger;
use Core\Logger\Model\LogModel;
use Core\Logger\LogLevel;
use Core\Model\BaseValidator;
use Core\Util\TimeUtils;

use Modules\Status\Models\StatusModel;
use Modules\Status\StatusEntity;
use Modules\Status\Models\StatusList;

use Modules\Status\Models\StatusCommentModel;
use Modules\Status\StatusCommentEntity;
use Modules\Status\Models\StatusCommentList;

use Modules\User\Models\UserModel;

class StatusGateway extends ErrorBase
{
	public function listStatus(UserModel $user, int $page = 1, int $per_page = 25, int &$total = 0) : StatusList
	{
		if ($per_page > 200)
		{
			$this->addError('Max status threads per page is 200.');
			return [];
		}

		$offset = 0;
		if ($page > 1)
		{
			$offset = ($page - 1) * $per_page;
		}

		$list = new StatusList($user->getUSERID(), $offset, $per_page);
		$total = $list->getTotalCount();

		return $list;
	}

	public function createStatus(UserModel $user, string $status_content) : ?StatusModel
	{
		$status = new StatusModel();
		$status->setUSERID($user->getUSERID());
		$status->setStatusDate(date(TimeUtils::DATEFORMAT_STANDARD));
		$status->setStatusContent($status_content);

		if (!$this->validateStatus($status))
		{
			return null;
		}

		$entity = $status->createEntity();
		$status = $entity->store();

		return $status;
	}

	public function getStatus(int $STATUSID, bool $request_cache = false) : StatusModel
	{
		$entity = new StatusEntity();
		$entity->setRequestCache($request_cache);
		return $entity->find((int)$STATUSID);
	}

	public function updateStatus(int $STATUSID, string $status_content) : ?StatusModel
	{
		$status = $this->getStatus($STATUSID, true);
		if (!$status->isInitialized())
		{
			$this->setHttpCode(404);
			$this->addError('Status has gone away.');
			return null;
		}

		$status->setStatusContent($status_content);
		$status->setStatusModifiedDate(date(TimeUtils::DATEFORMAT_STANDARD));

		if (!$this->validateStatus($status))
		{
			return null;
		}

		$entity = $status->createEntity();
		if (!$entity->update(['status_content', 'status_modified_date']))
		{
			$this->setHttpCode(500);
			$this->addError('Unable to update status_content at this time. Please try again later.');
			return null;
		}
		return $status;
	}

	public function deleteStatus(int $STATUSID) : bool
	{
		$model = $this->getStatus($STATUSID, true);
		if (!$model->isInitialized())
		{
			$this->setHttpCode(404);
			$this->addError('Status has gone away.');
			return false;
		}

		$entity = $model->createEntity();
		if (!$entity->delete())
		{
			$this->setHttpCode(500);
			$this->addError('Unable to delete status at this time please try again later.');
			return false;
		}

		$log = new LogModel('Status ID has been deleted: ' . $STATUSID, LogLevel::LOG);
		$log->setAssociation('USERID', $model->getUSERID());
		$log->setLogType('status_delete');
		Logger::log($log);

		return true;
	}

	public function listComment(StatusModel $status, int $page = 1, int $per_page = 25, ?int &$total = 0) : StatusCommentList
	{
		if ($per_page > 200)
		{
			$this->addError('Max comment threads per page is 200.');
			return [];
		}

		$offset = 0;
		if ($page > 1)
		{
			$offset = ($page - 1) * $per_page;
		}

		$list = new StatusCommentList($status, $offset, $per_page);
		$total = $list->getTotalCount();

		return $list;
	}

	public function createComment(UserModel $user, StatusModel $status, string $comment_content) : ?StatusCommentModel
	{
		if (!$status->isInitialized())
		{
			$this->setHttpCode(404);
			$this->addError('Status does not exist.');
			return null;
		}

		$comment = new StatusCommentModel();
		$comment->setStatusID($status->getStatusID());
		$comment->setUserID($user->getUSERID());
		$comment->setCommentContent($comment_content);
		$comment->setCommentDate(date(TimeUtils::DATEFORMAT_STANDARD));

		if (!$this->validateComment($comment))
		{
			return null;
		}

		$entity = $comment->createEntity();
		$comment = $entity->store();

		return $comment;
	}

	public function getComment(int $COMMENTID, bool $request_cache = false) : StatusCommentModel
	{
		$entity = new StatusCommentEntity();
		$entity->useRequestCache($request_cache);
		return $entity->find($COMMENTID);
	}

	public function updateComment(int $COMMENTID, string $comment_content) : ?StatusCommentModel
	{
		$comment = $this->getComment($COMMENTID, true);
		if (!$comment->isInitialized())
		{
			$this->setHttpCode(404);
			$this->addError('Comment has gone away.');
			return null;
		}

		$comment->setCommentContent($comment_content);
		$comment->setCommentModifiedDate(date(TimeUtils::DATEFORMAT_STANDARD));

		if (!$this->validateComment($comment))
		{
			return null;
		}

		$entity = $comment->createEntity();
		if (!$entity->update(['comment_content', 'comment_modified_date']))
		{
			$this->setHttpCode(500);
			$this->addError('Unable to update comment_content at this time. Please try again later.');
			return null;
		}

		return $comment;
	}

	public function deleteComment(int $COMMENTID) : bool
	{
		$model = $this->getComment($COMMENTID, true);

		if (!$model->isInitialized())
		{
			$this->setHttpCode(404);
			$this->addError('Comment has gone away.');
			return false;
		}

		$entity = $model->createEntity();
		if (!$entity->delete())
		{
			$this->setHttpCode(500);
			$this->addError('Unable to delete status at this time please try again later.');
			return false;
		}

		$log = new LogModel('Comment ID has been deleted: ' . $COMMENTID, LogLevel::LOG);
		$log->setAssociation('USERID', $model->getUSERID());
		$log->setLogType('comment_delete');
		Logger::log($log);

		return true;
	}

	private function validateStatus(StatusModel $status) : bool
	{
		$validator = new BaseValidator($status);
		$validator->addRule('maxLength', ['status_content'], ['status_content' => 300]);

		if (!$validator->validate())
		{
			$this->setHttpCode(400);
			$this->addError($validator->getErrors());
			return false;
		}
		return true;
	}

	private function validateComment(StatusCommentModel $comment) : bool
	{
		$validator = new BaseValidator($comment);
		$validator->addRule('maxLength', ['comment_content'], ['comment_content' => 300]);

		if (!$validator->validate())
		{
			$this->setHttpCode(400);
			$this->addError($validator->getErrors());
			return false;
		}
		return true;
	}
}
