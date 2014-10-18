<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/UserReport.php");

class ReportedUserRepo extends \model\repository\Repository
{
	private $reportedUserTable = "reportedUser";

	//databasens kolumnnamn
	private $reportedUserId = "reportedUserId";
	private $userId = "userId";
	private $type = "type";
	private $nominatedForDeletionBy = "nominatedForDeletionBy";
	private $commentFromAdmin = "commentFromAdmin";

	public function add(\model\UserReport $userReport)
	{
		$sql = "INSERT INTO ".$this->reportedUserTable."(".$this->userId.", ".$this->type.",".$this->commentFromAdmin.")
		VALUES (?,?,?);";
		$params = array($userReport->getUserId(), $userReport->getType(), $userReport->getCommentFromAdmin());
		
		$this->connect();	
		
		$query = $this->dbConnection->prepare($sql);	
		$result = $query->execute($params);
		
		return $result;		
	}

	public function delete($reportId)
	{
		$sql = "DELETE FROM ".$this->reportedUserTable." WHERE ".$this->reportedUserId." = ?";
		$params = array($reportId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		return $result;				
	}


	public function nominateForDeletion($reportId, $adminId)
	{
		$sql = "UPDATE ".$this->reportedUserTable." SET ".$this->nominatedForDeletionBy." = ? WHERE ".$this->reportedUserId."=?";
		$params = array($adminId, $reportId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		return $result;			
	}

	public function getAllReports()
	{
		//array som ska returneras
		$retArray = array();

		$sql = "SELECT * FROM ".$this->reportedUserTable;

		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute();	
		
		//hämta alla rader
		$reports = $query->fetchAll();
		
		//om det kom några polls
		if($reports)
		{
			foreach($reports as $report)
			{
				//skapa alla objekt				
				$retArray[] = new \model\UserReport
				(
					$report[$this->userId],
					$report[$this->type],
					$report[$this->commentFromAdmin],
					$report[$this->nominatedForDeletionBy],
					$report[$this->reportedUserId]					
				);
			}
			
		return $retArray;
		}	
	}

}