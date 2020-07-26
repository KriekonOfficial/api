<?php

namespace Modules\Status;

use Core\ErrorBase;
use Core\Logger\Logger;
use Core\Logger\Model\LogModel;
use COre\Logger\LogLevel;
use Core\Model\BaseValidator;

use Modules\Status\Models\StatusModel;
use Modules\Status\StatusEntity;
use Modules\Status\Models\StatusList;

use Modules\Account\Models\AccountModel;

class StatusGateway extends ErrorBase
{
	private $account;
	public function __construct(AccountModel $account)
	{
		$this->account = $account;
	}

	public function listStatus(int $page = 1, int $per_page = 25, ?int &$total = 0) : array
	{
		if ($per_page > 200)
		{
			$this->addError('Max status threads per page is 200.');
			return [];
		}

		$status = [];

		$offset = 0;
		if ($page > 1)
		{
			$offset = $page * $per_page;
		}

		$list = new StatusList($this->account->getACCTID(), $offset, $per_page);
		$total = $list->getTotalCount();
		foreach ($list as $key => $model)
		{
			$status[] = $model->toPublicArray();
		}

		return $status;
	}

	public function deleteStatus(int $STATUSID) : bool
	{
		$entity = new StatusEntity();
		$model = $entity->find($STATUSID);

		if (!$model->isInitialized())
		{
			$this->setHttpCode(404);
			$this->addError('Status does not exist.');
			return false;
		}

		if (!$entity->delete())
		{
			$this->setHttpCode(500);
			$this->addError('Unable to delete status at this time please try again later.');
			return false;
		}

		$log = new LogModel('Status ID has been deleted: ' . $STATUSID, LogLevel::LOG);
		$log->setAssociation('ACCTID', $model->getACCTID());
		$log->setLogType('status_delete');
		Logger::log($log);

		return true;
	}

	public function createStatus(string $status_content) : bool
	{
		$status = new StatusModel();
		$status->setACCTID($this->account->getACCTID());
		$status->setStatusDate(date(DATEFORMAT_STANDARD));
		$status->setStatusContent($status_content);

		if (!$this->validateStatus($status))
		{
			return false;
		}

		$entity = $status->createEntity();
		$status = $entity->store();

		return true;
	}

	public function updateStatus(int $STATUSID, string $status_content) : bool
	{
		$entity = new StatusEntity();
		$status = $entity->find($STATUSID);
		if (!$status->isInitialized())
		{
			$this->setHttpCode(404);
			$this->addError('Status does not exist.');
			return false;
		}

		$status->setStatusContent($status_content);
		$status->setStatusModifiedDate(date(DATEFORMAT_STANDARD));

		if (!$this->validateStatus($status))
		{
			return false;
		}

		$entity = $status->createEntity();
		if (!$entity->update(['status_content', 'status_modified_date']))
		{
			$this->setHttpCode(500);
			$this->addError('Unable to update status_content at this time. Please try again later.');
			return false;
		}
		return true;
	}

	private function validateStatus(StatusModel $status) : bool
	{
		$validator = new BaseValidator($status);
		$validator->addValidator('maxLength', [300]);
		$validator->addRule('maxLength', ['status_content']);

		if (!$validator->validate())
		{
			$this->setHttpCode(400);
			$this->addError($validator->getErrors());
			return false;
		}
		return true;
	}
}
