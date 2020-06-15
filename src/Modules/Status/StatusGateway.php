<?php

namespace Modules\Status;

use Core\ErrorBase;

use Modules\Status\Models\StatusModel;
use Modules\Status\StatusEntity;
use Modules\Status\Models\StatusList;

class StatusGateway extends ErrorBase
{
	public function listStatus(int $ACCTID, int $page = 1, int $per_page = 25) : array
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

		foreach (new StatusList($ACCTID, $offset, $per_page) as $key => $model)
		{
			$status[] = $model->toPublicArray();
		}
		return $status;
	}
}