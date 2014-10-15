<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/PollReport.php");

class ReportedPollRepo extends \model\repository\Repository
{

	//databasens kolumnnamn
	private $reportedPollId = "reportedPollId";
	private $userId = "userId";
	private $pollId = "pollId";
	private $commentFromReporter = "commentFromReporter";

	public function add(\model\PollReport $pollReport)
	{
		$sql = "INSERT INTO reportedPoll(".$this->userId.", ".$this->pollId.", ".$this->commentFromReporter.")
		VALUES (?,?,?);";
		$params = array($pollReport->getUserId(), $pollReport->getPollId(), $pollReport->getCommentFromReporter());
		
		$this->connect();	
		
		$query = $this->dbConnection->prepare($sql);	
		$result = $query->execute($params);
		
		return $result;		
	}

	public function delete($reportId)
	{
		$sql = "DELETE FROM reportedPoll WHERE ".$this->reportedPollId." = ?";
		$params = array($reportId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		return $result;				
	}

	public function getAllReports()
	{
		//array som ska returneras
		$retArray = array();

		$sql = "SELECT * FROM reportedPoll";

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
				$retArray[] = new \model\PollReport
				(
					$report[$this->userId],
					$report[$this->pollId],
					$report[$this->commentFromReporter],
					$report[$this->reportedPollId]					
				);
			}
			
		return $retArray;
		}	
	}

}