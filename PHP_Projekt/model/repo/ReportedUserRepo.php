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

	//lägg till ny rapport på användare
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

	//ta bort rapport på användare
	public function delete($reportId)
	{
		$sql = "DELETE FROM ".$this->reportedUserTable." WHERE ".$this->reportedUserId." = ?";
		$params = array($reportId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		return $result;				
	}

	/**
	* nominera user för borttagning
	* @param int 	id på UserReport
	* @param int 	id på admin som gör detta val
	*/
	public function nominateForDeletion($reportId, $adminId)
	{
		$sql = "UPDATE ".$this->reportedUserTable." SET ".$this->nominatedForDeletionBy." = ? WHERE ".$this->reportedUserId."=?";
		$params = array($adminId, $reportId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		return $result;			
	}

	/**
	* Hämta alla rapporter på specificerad användare
	* @param 	int 	användarens id
	* @return 	array 	array med UserReport-objekt 	
	*/
	public function getAllReportsOnUser($userId)
	{
		//array som ska returneras
		$retArray = array();

		$sql = "SELECT * FROM ".$this->reportedUserTable ." WHERE ".$this->userId." =?" ;
		$params = array($userId);

		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);	
		
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
		return false;
	}

	//hämta alla UserReports
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
		return false;
	}

}