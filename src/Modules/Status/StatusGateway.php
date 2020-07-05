<?php

namespace Modules\Status;

use Core\ErrorBase;
use Core\Logger\Logger;
use Core\Logger\Model\LogModel;
use COre\Logger\LogLevel;

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

	public function listStatus(int $page = 1, int $per_page = 25) : array
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

		foreach (new StatusList($this->account->getACCTID(), $offset, $per_page) as $key => $model)
		{
			$status[] = $model->toPublicArray();
		}
		return $status;
	}

	public function deleteStatus(int $status_id) : bool
	{
		$entity = new StatusEntity();
		$model = $entity->find($status_id);

		if (!$model->isInitialized())
		{
			$this->addError('Status does not exist.');
			return false;
		}

		if (!$entity->delete())
		{
			$this->addError('Unable to delete status at this time please try again later.');
			return false;
		}

		$log = new LogModel('Status ID has been deleted: ' . $status_id, LogLevel::LOG);
		$log->setAssociation('ACCTID', $model->getACCTID());
		$log->setLogType('status_delete');
		Logger::log($log);

		return true;
	}
}