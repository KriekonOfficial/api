<?php

namespace Modules\Status;

use Core\Entity\DBEntity;

class StatusCommentEntity extends DBEntity
{
	public function getModelPath() : string
	{
		return '\Modules\Status\Models\StatusCommentModel';
	}

	public function getCollectionTable() : string
	{
		return 'user_status_comments';
	}

	public function getCollectionPrimaryKey() : string
	{
		return 'COMMENTID';
	}
}
