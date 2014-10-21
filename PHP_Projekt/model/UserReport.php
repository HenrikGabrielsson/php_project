<?php

namespace model;

class UserReport
{
	private $reportedUserId;			//unikt id
	private $userId;					//användares unika id
	private $type;						//typ av rapportering som ledde till att en rapportering gjordes på användare
	private $commentFromAdmin;			//kommentar från admin gjorde rapporteringen
	private $nominatedForDeletionBy;	//Om användaren har nominerats till bortttagning, id på admin som gjorde nomineringen

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
		return $this->nominatedForDeletionBy;
	}
}