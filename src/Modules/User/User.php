<?php

namespace Modules\User;

use Core\Entity\DBEntity;
use Modules\User\Models\UserModel;
use Core\Store\Database\Util\DBWrapper;
use Core\Store\Database\Exception\DatabaseException;

class User extends DBEntity
{
	public function getModelPath() : string
	{
		return '\Modules\User\Models\UserModel';
	}

	public function getCollectionTable() : string
	{
		return 'user';
	}

	public function getCollectionPrimaryKey() : string
	{
		return 'USERID';
	}

	public function findEmail(string $email) : UserModel
	{
		$results = DBWrapper::PResult('
			SELECT * FROM ' . $this->getCollectionTable() . ' WHERE email = ?', [$email], $this->getCollectionName());

		if ($results->count() > 1)
		{
			throw new DatabaseException('Error found more than 1 email address in the database. Email: ' . $email);
		}

		$this->setModelProperties($results);

		return $this->getModel();
	}
}
