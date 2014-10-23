<?php

namespace model;

require_once("./model/BasicReport.php");

class UserReport extends BasicReport
{
	private $nominatedForDeletionBy;	//Om användaren har nominerats till bortttagning, id på admin som gjorde nomineringen

	public function __construct($userId, $objectId, $comment, $type, $reportId = null, $nominatedForDeletionBy = null)
	{
		parent::__construct($userId, $objectId, $comment, $type, $reportId);
		$this->nominatedForDeletionBy = $nominatedForDeletionBy;
	}

	public function getNomination()
	{
		return $this->nominatedForDeletionBy;
	}
}