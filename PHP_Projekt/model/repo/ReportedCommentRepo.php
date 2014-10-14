<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/CommentReport.php");

class ReportedCommentRepo extends \model\repository\Repository
{

	//databasens kolumnnamn
	private $reportedCommentId = "reportedCommentId";
	private $userId = "userId";
	private $commentId = "commentId";
	private $commentFromReporter = "commentFromReporter";

	public function add(\model\CommentReport $commentReport)
	{
		$sql = "INSERT INTO reportedComment(".$this->userId.", ".$this->commentId.", ".$this->commentFromReporter.")
		VALUES (?,?,?);";
		$params = array($commentReport->getUserId(), $commentReport->getCommentId(), $commentReport->getCommentFromReporter());
		
		$this->connect();	
		
		$query = $this->dbConnection->prepare($sql);	
		$result = $query->execute($params);

		return $result;		
	}

	public function delete($reportId)
	{
		$sql = "DELETE FROM reportedComment WHERE ".$this->reportedCommentId." = ?";
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

		$sql = "SELECT * FROM reportedComment";

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
				$retArray[] = new \model\CommentReport
				(
					$report[$this->userId],
					$report[$this->commentId],
					$report[$this->commentFromReporter],
					$report[$this->reportedCommentId]				
				);
			}
			
		return $retArray;
		}	
	}

}