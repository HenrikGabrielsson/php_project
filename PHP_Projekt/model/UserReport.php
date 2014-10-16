<?php

namespace model;

class UserReport
{
	private $reportedUserId;
	private $userId;
	private $type;
	private $commentFromAdmin;
	private $nominatedForDeletionBy;

	public function __construct($userId, $type, $commentFromAdmin, $nominatedForDeletionBy = null, $reportedUserId = null)
	{
	$this->reportedUserId = $reportedUserId;
	$this->userId = $userId;
	$this->type = $type;
	$this->commentFromAdmin = $commentFromAdmin;
	$this->nominatedForDeletionBy = $nominatedForDeletionBy;
	}

	public function getId()
	{
		return $this->reportedUserId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getCommentFromAdmin()
	{
		return $this->commentFromAdmin;
	}

	public function getNomination()
	{
		return $this->nomination;
	}
}