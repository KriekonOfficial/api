<?php

namespace Modules\Status\Models;

use Core\Model\Model;

class StatusModel extends Model
{
	private int $_STATUSID = 0;
	private int $_ACCTID = 0;
	private string $_status_date = DATE_ZERO;
	private string $_status_modified_date = DATE_ZERO;
	private string $_status_content = '';

	protected int $STATUSID;
	protected int $ACCTID;
	protected string $status_date;
	protected string $status_modified_date;
	protected string $status_content;

	public function getStatusID() : int
	{
		return $this->STATUSID;
	}

	public function setStatusID(int $STATUSID) : void
	{
		$this->STATUSID = $STATUSID;
	}

	public function getACCTID() : int
	{
		return $this->ACCTID;
	}

	public function setACCTID(int $ACCTID) : void
	{
		$this->ACCTID = $ACCTID;
	}

	public function getStatusDate() : string
	{
		return $this->status_date;
	}

	public function setStatusDate(string $date_time) : void
	{
		$this->status_date = $date_time;
	}

	public function getStatusModifiedDate() : string
	{
		return $this->status_modified_date;
	}

	public function setStatusModifiedDate(string $modified_date) : void
	{
		$this->status_modified_date = $modified_date;
	}

	public function getStatusContent() : string
	{
		return $this->status_content;
	}

	public function setStatusContent(string $status_content) : void
	{
		$this->status_content = $status_content;
	}


	////
	// Abstract
	///

	public function getPrimaryKey()
	{
		return $this->getStatusID();
	}

	public function setPrimaryKey($primary_key) : void
	{
		$this->setStatusID($primary_key);
	}

	public function reset() : void
	{
		$this->setStatusID($this->_STATUSID);
		$this->setACCTID($this->_ACCTID);
		$this->setStatusDate($this->_status_date);
		$this->setStatusModifiedDate($this->_status_modified_date);
		$this->setStatusContent($this->_status_content);
	}

	public function toArray() : array
	{
		return [
			'STATUSID' => $this->getStatusID(),
			'ACCTID' => $this->getACCTID(),
			'status_date' => $this->getStatusDate(),
			'status_modified_date' => $this->getStatusModifiedDate(),
			'status_content' => $this->getStatusContent()
		];
	}

	public function toPublicArray() : array
	{
		return $this->toArray();
	}

	protected function getEntityPath() : string
	{
		return '\Modules\Status\StatusEntity';
	}
}