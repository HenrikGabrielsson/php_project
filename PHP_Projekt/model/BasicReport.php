<?php 

namespace model;

class BasicReport
{
	private $reportId;				//unikt id
	private $userId;				//id på användare som rapporten gäller
	private $objectId;				//det rapporterade objektets id
	private $comment;				//anledning angiven av rapportören
	private $type;					//typ av objekt

	public function __construct($userId, $objectId, $comment, $type, $reportId = null)
	{
		$this->reportId = $reportId;
		$this->userId = $userId;
		$this->objectId = $objectId;
		$this->comment = $comment;
		$this->type = $type;
	}	

	public function getId()
	{
		return $this->reportId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getObjectId()
	{
		return $this->objectId;
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function getType()
	{
		return $this->type;
	}
}