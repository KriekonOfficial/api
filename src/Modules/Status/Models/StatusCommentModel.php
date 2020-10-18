<?php

namespace Modules\Status\Models;

use Core\Model\Model;
use Core\Util\TimeUtils;

class StatusCommentModel extends Model
{
	private int $_COMMENTID = 0;
	private int $_STATUSID = 0;
	private int $_PARENTID = 0;
	private int $_USERID = 0;
	private string $_comment_date = TimeUtils::DATE_ZERO;
	private string $_comment_modified_date = TimeUtils::DATE_ZERO;
	private string $_comment_content = '';

	protected int $COMMENTID;
	protected int $STATUSID;
	protected int $PARENTID;
	protected int $USERID;
	protected string $comment_date;
	protected string $comment_modified_date;
	protected string $comment_content;

	public function getCommentID() : int
	{
		return $this->COMMENTID;
	}

	public function setCommentID(int $COMMENTID) : void
	{
		$this->COMMENTID = $COMMENTID;
	}

	public function getStatusID() : int
	{
		return $this->STATUSID;
	}

	public function setStatusID(int $STATUSID) : void
	{
		$this->STATUSID = $STATUSID;
	}

	public function getParentID() : int
	{
		return $this->PARENTID;
	}

	public function setParentID(int $PARENTID) : void
	{
		$this->PARENTID = $PARENTID;
	}

	public function getUserID() : int
	{
		return $this->USERID;
	}

	public function setUserID(int $USERID) : void
	{
		$this->USERID = $USERID;
	}

	public function getCommentDate() : string
	{
		return $this->comment_date;
	}

	public function setCommentDate(string $comment_date) : void
	{
		$this->comment_date = $comment_date;
	}

	public function getCommentModifiedDate() : string
	{
		return $this->comment_modified_date;
	}

	public function setCommentModifiedDate(string $comment_modified_date) : void
	{
		$this->comment_modified_date = $comment_modified_date;
	}

	public function getCommentContent() : string
	{
		return $this->comment_content;
	}

	public function setCommentContent(string $comment_content) : string
	{
		$this->comment_content = $comment_content;
	}

	public function setPrimaryKey($value) : void
	{
		$this->setCommentID($value);
	}

	public function getPrimaryKey()
	{
		return $this->getCommentID();
	}

	public function reset() : void
	{
		$this->setCommentID($this->_COMMENTID);
		$this->setStatusID($this->_STATUSID);
		$this->setParentID($this->_PARENTID);
		$this->setUserID($this->_USERID);
		$this->setCommentDate($this->_comment_date);
		$this->setCommentModifiedDate($this->_comment_modified_date);
		$this->setCommentContent($this->_comment_content);
	}

	public function toArray() : array
	{
		return [
			'COMMENTID'             => $this->getCommentID(),
			'STATUSID'              => $this->getStatusID(),
			'PARENTID'              => $this->getParentID(),
			'USERID'                => $this->getUserID(),
			'comment_date'          => $this->getCommentDate(),
			'comment_modified_date' => $this->getCommentModifiedDate(),
			'comment_content'       => $this->getCommentContent()
		];
	}

	public function toPublicArray() : array
	{
		return $this->toArray();
	}
}
