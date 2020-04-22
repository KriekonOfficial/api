<?php

namespace Modules\Account;

use Core\Entity\Entity;
use Modules\Account\Models\AccountModel;
use Core\Store\Database\Util\DBWrapper;

class Account extends Entity
{
	protected string $db_table = 'account';
	protected string $db_primary_key = 'ACCTID';

	public function getModelPath() : string
	{
		return '\Modules\Account\Models\AccountModel';
	}

	public function findEmail(string $email) : AccountModel
	{
		$count = 0;
		$results = DBWrapper::PResult('
			SELECT * FROM ' . $this->getDBTable() . ' WHERE email = ?', [$email], $this->getDBName());

		$this->setModelProperties($results);

		return $this->getModel();
	}
}