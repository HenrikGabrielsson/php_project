<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/UserReport.php");
require_once("./model/BasicReport.php");

class ReportRepo extends \model\repository\Repository
{

	//report kan ha tre olika typer
	const USERTYPE = "user";
	const POLLTYPE = "poll";
	const COMMENTTYPE = "comment";


	//databasens kolumnnamn
	private $reportId = "reportId";
	private $userId = "userId";
	private $objectId = "objectId";
	private $comment = "comment";
	private $type = "type";
	private $nominatedBy = "nominatedBy";


	//lägg till ny rapport 
	public function add($report)
	{
		$sql = "INSERT INTO ".$this->reportTable."(".$this->userId.", ".$this->objectId.", ".$this->comment.",".$this->type.")
		VALUES (?,?,?,?);";
		$params = array($report->getUserId(), $report->getObjectId(), $report->getComment(), $report->getType());
		
		$this->connect();	
		
		$query = $this->dbConnection->prepare($sql);	
		$result = $query->execute($params);
		
		return $result;		
	}

	//ta bort rapport på poll
	public function delete($reportId)
	{
		$sql = "DELETE FROM ".$this->reportTable." WHERE ".$this->reportId." = ?";
		$params = array($reportId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		return $result;				
	}

	//uppdatera alla rapporter med userId till den admin som vill ta bort användaren.
	public function nominateForDeletion($userId, $adminId)
	{
		$sql = "UPDATE ".$this->reportTable." SET ".$this->nominatedBy."=? WHERE ".$this->userId."= ? AND ". $this->type." = \"".self::USERTYPE."\"";
		$params = array($adminId, $userId);

		$this->connect();

		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);

		return $result;

		var_dump($result);
	}

	/**
	*	hämta rapport med specificerat id
	*	
	*	@param 	int 							id på den rapport som ska hämtas
	* 	@return BasicReport/UserReport/bool		rapport som hittas, annars false
	*/
	public function getReportById($reportId)
	{
		$sql = "SELECT * FROM ".$this->reportTable." WHERE ".$this->reportId." = ?";
		$params = array($reportId);

		$this->connect();

		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);

		$result = $query->fetch();

		if($result)
		{
			//om det är en rapport för en användare
			if($result[$this->type] == self::USERTYPE)
			{
				$report = new \model\UserReport
				(
					$result[$this->userId],
					$result[$this->objectId],
					$result[$this->comment],
					$result[$this->type],
					$result[$this->reportId],
					$result[$this->nominatedBy]
				);
			}
			else
			{
				$report = new \model\BasicReport
				(
					$result[$this->userId],
					$result[$this->objectId],
					$result[$this->comment],
					$result[$this->type],
					$result[$this->reportId]
				);

			}
			return $report;
		}
		return false;
	}

	/**
	*	Hämta alla rapporter där det rapporterade objektet är av den specificerade typen 
	*	@param 	string 			typen som ska hämtas
	* 	@return array/bool 		array med rapporter som hittades, om inga: false;
	*/
	public function getReportsByType($type)
	{
		$sql = "SELECT * FROM ".$this->reportTable." WHERE ".$this->type." =?";
		$params = array($type);

		$this->connect();

		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);	
		
		//hämta alla rader
		$result = $query->fetchAll();

		if($result)
		{
			$reports = array();

			//om det är rapporter för användare
			if($type == self::USERTYPE)
			{
				foreach($result as $report)
				{
					$report = new \model\UserReport
					(
						$report[$this->userId],
						$report[$this->objectId],
						$report[$this->comment],
						$report[$this->type],
						$report[$this->reportId],
						$report[$this->nominatedBy]
					);		
					$reports[] = $report;							
				}
			}
			else
			{
				foreach($result as $report)
				{
					$report = new \model\BasicReport
					(
						$report[$this->userId],
						$report[$this->objectId],
						$report[$this->comment],
						$report[$this->type],
						$report[$this->reportId]
					);
					$reports[] = $report;
				}

			}
			return $reports;
		}
		return false;
	}
}