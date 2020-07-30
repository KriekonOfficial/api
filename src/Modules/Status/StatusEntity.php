<?php

namespace Modules\Status;

use Core\Entity\DBEntity;

class StatusEntity extends DBEntity
{
	public function getModelPath() : string
	{
		return '\Modules\Status\Models\StatusModel';
	}

	public function getCollectionTable() : string
	{
		return 'user_status';
	}

	public function getCollectionPrimaryKey() : string
	{
		return 'STATUSID';
	}
}
