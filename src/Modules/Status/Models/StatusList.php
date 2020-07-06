<?php

namespace Modules\Status\Models;

use Core\Store\Cache;
use Core\Store\Database\Util\DBWrapper;
use Core\Store\Database\Model\DBResult;
use Core\APIError;
use Modules\Status\StatusEntity;

class StatusList extends DBResult
{
	public const SUPPORTED_ORDER_FILTER = ['status_date'];
	public const SUPPORTED_ORDER = ['desc', 'asc'];

	private $entity;
	private int $ACCTID;
	private string $order;
	private string $order_by;

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

		$this->entity = new StatusEntity();
		$this->ACCTID = $ACCTID;
		$this->order = $order;
		$this->order_by = $order_by;

		$factory = DBWrapper::factory('
			SELECT * FROM ' . $this->entity->getCollectionTable() . '
			WHERE ACCTID = ?
			ORDER BY ' . $order_by . ' ' . $order . '
			LIMIT ?, ?', [$ACCTID, $offset, $limit], $this->entity->getCollectionName());

		parent::__construct($factory);
	}

	public function current() : StatusModel
	{
		$status = new StatusModel();
		$status->setModelProperties($this->getRecord());
		$status->setInitializedFlag(true);
		return $status;
	}

	public function getTotalCount() : int
	{
		$key = $this->entity->getCacheKey($this->ACCTID, 'ACCTID') . ':total_status';
		$cache_total = Cache::get($key);
		if ($cache_total !== null)
		{
			return $cache_total;
		}

		$results = DBWrapper::PSingle('
			SELECT COUNT(STATUSID) AS total FROM ' . $this->entity->getCollectionTable() . '
			WHERE ACCTID = ?
			ORDER BY ' . $this->order_by . ' ' . $this->order, [$this->ACCTID], $out_count, $this->entity->getCollectionName());

		$total = $results['total'] ?? 0;

		Cache::set($key, $total, 30);

		return $total;
	}
}