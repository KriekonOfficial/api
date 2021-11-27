<?php

namespace Modules\Status\Models;

use Core\Store\Cache;
use Core\Store\Database\Util\DBWrapper;
use Core\Store\Database\Model\DBResult;
use Core\APIError;
use Modules\Status\StatusCommentEntity;
use Modules\Status\Models\StatusCommentModel;
use Modules\Status\Models\StatusModel;

class StatusCommentList extends DBResult
{
	public const SUPPORTED_ORDER_FILTER = ['comment_date'];
	public const SUPPORTED_ORDER = ['DESC', 'ASC'];

	private StatusCommentEntity $entity;
	private StatusModel $status;
	private string $order;
	private string $order_by;

	public function __construct(
		StatusModel $status,
		int $offset = 0,
		int $limit = 100,
		string $order_by = 'comment_date',
		string $order = 'DESC'
	)
	{
		if (!in_array($order_by, self::SUPPORTED_ORDER_FILTER))
		{
			throw new APIError('Invalid order filter, supported order filters. ' . implode(', ', self::SUPPORTED_ORDER_FILTER), 400);
		}

		$order = strtoupper($order);
		if (!in_array($order, self::SUPPORTED_ORDER))
		{
			throw new APIError('Invalid order, supported orders.' . implode(', ', self::SUPPORTED_ORDER), 400);
		}

		$this->entity = new StatusCommentEntity();
		$this->status = $status;
		$this->order = $order;
		$this->order_by = $order_by;

		$factory = DBWrapper::factory('
			SELECT * FROM ' . $this->entity->getCollectionTable() . '
			WHERE STATUSID = ?
			ORDER BY '.$order_by.' '.$order.'
			LIMIT ?, ?', [$this->status->getStatusID(), $offset, $limit], $this->entity->getCollectionName());

		parent::__construct($factory);
	}

	public function current() : StatusCommentModel
	{
		$comment = new StatusCommentModel();
		$comment->setModelProperties($this->getRecord());
		$comment->setInitializedFlag(true);
		return $comment;
	}

	public function getTotalCount() : int
	{
		$key = $this->entity->getCacheKey($this->status->getStatusID(), 'STATUSID') . ':total_comment';
		$cache_total = Cache::get($key);
		if ($cache_total !== null)
		{
			return $cache_total;
		}

		$results = DBWrapper::PSingle('
			SELECT COUNT(COMMENTID) AS total FROM ' . $this->entity->getCollectionTable() . '
			WHERE STATUSID = ?
			ORDER BY ' . $this->order_by . ' ' . $this->order, [$this->status->getStatusID()], $out_count, $this->entity->getCollectionName());

		$total = $results['total'] ?? 0;

		Cache::set($key, $total, 30);

		return $total;
	}
}
