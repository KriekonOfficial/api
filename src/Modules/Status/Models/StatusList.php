<?php

namespace Modules\Status\Models;

use Core\Store\Database\Util\DBWrapper;
use Core\Store\Database\Model\DBResult;
use Core\APIError;

class StatusList extends DBResult
{
	public const SUPPORTED_ORDER_FILTER = ['status_date'];
	public const SUPPORTED_ORDER = ['desc', 'asc'];

	public function __construct(
		int $ACCTID,
		int $offset = 0,
		int $limit = 100,
		string $order_by = 'status_date',
		string $order = 'desc'
	)
	{
		if (!in_array($order_by, self::SUPPORTED_ORDER_FILTER))
		{
			throw new APIError('Invalid order filter, supported order filters. ' . implode(', ', self::SUPPORTED_ORDER_FILTER), 400);
		}

		if (!in_array($order, self::SUPPORTED_ORDER))
		{
			throw new APIError('Invalid order, supported orders.' . implode(', ', self::SUPPORTED_ORDER), 400);
		}

		$factory = DBWrapper::factory('
			SELECT * FROM account_status
			WHERE ACCTID = ?
			ORDER BY ' . $order_by . ' ' . $order . '
			LIMIT ?, ?', [$ACCTID, $offset, $limit]);

		parent::__construct($factory);
	}

	public function current() : StatusModel
	{
		$status = new StatusModel();
		$status->setModelProperties($this->getRecord());
		$status->setInitializedFlag(true);
		return $status;
	}
}