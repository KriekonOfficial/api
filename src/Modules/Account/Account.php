<?php

namespace Modules\Account;

use Core\Entity\Entity;
use Modules\Account\Models\AccountModel;
use Core\Store\Database\Util\DBWrapper;

class Account extends Entity
{
	protected string $model_path = '\Modules\Account\Models\AccountModel';

	protected string $db_table = 'account';
	protected string $db_primary_key = 'ACCTID';

	public function findEmail(string $email) : AccountModel
	{
		$count = 0;
		$results = DBWrapper::PSingle('
			SELECT * FROM ' . $this->getDBTable() . ' WHERE email = ?', [$email], $count);

		$this->setModelProperties($results, $count);

		return $this->getModel();
	}
}