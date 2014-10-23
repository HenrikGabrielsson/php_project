<?php

namespace model;

require_once("./model/repo/ReportRepo.php");

require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CommentRepo.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/BasicReport.php");
require_once("./model/UserReport.php");

class ReportHandler
{
	private $reportRepo;
	private $pollRepo;
	private $commentRepo;
	private $userRepo;

	//errors
	const LONGREASON = "longReason";
	const NOREASON = "noReason";
	const NOCOMMENT = "noComment";
	const NOPOLL = "noPoll";
	const SAMEADMIN = "sameAdmin";

	//success
	const REPORTEDPOLL = "reportedPoll";
	const REPORTEDCOMMENT = "reportComment";
	const USERNOMINATED = "userNominated";
	const DELETED = "deleted";

	private $feedbackList = array();

	public function __construct()
	{
		$this->reportRepo = new repository\ReportRepo();
		$this->pollRepo = new repository\PollRepo();
		$this->commentRepo = new repository\CommentRepo();
		$this->userRepo = new repository\UserRepo();
	}

	//hämta liste med feedback
	public function getFeedbackList()
	{
		return $this->feedbackList;
	}	


	//hämta alla UserReports
	public function getAllUserReports()
	{
		return $this->reportRepo->getReportsByType(repository\ReportRepo::USERTYPE);
	}

	//hämta alla PollReports
	public function getAllPollReports()
	{
		return $this->reportRepo->getReportsByType(repository\ReportRepo::POLLTYPE);
	}

	//hämta alla CommentReports
	public function getAllCommentReports()
	{
		return $this->reportRepo->getReportsByType(repository\ReportRepo::COMMENTTYPE);
	}

	//ta bort en Report
	public function deleteReport($id)
	{
		$this->reportRepo->delete($id);
	}

	/**
	* rapportera en kommentar
	* @param Comment	id på rapporterad kommentar
	* @param string		anledning till rapportering
	*/
	public function reportComment($comment, $reason)
	{
		//validera allt
		$reason = htmlspecialchars($reason);
		$validReason = $this->validateReason($reason, false);
		$validComment = $this->validateComment($comment);

		//spara rapport om validering gick igenom
		if($validComment && $validReason)
		{
			$this->report($comment->getUserId(), $comment->getId(), $reason, repository\ReportRepo::COMMENTTYPE);
			$this->feedbackList[] = self::REPORTEDCOMMENT;
		}
	}

	/**
	* rapportera en poll
	* @param Poll		id på rapporterad poll
	* @param string		anledning till rapportering
	*/	
	public function reportPoll($poll, $reason)
	{
		//validera allt
		$reason = htmlspecialchars($reason);
		$validReason = $this->validateReason($reason, false);
		$validPoll = $this->validatePoll($poll);

		//spara rapport om validering gick igenom
		if($validPoll && $validReason)
		{
			$this->report($poll->getCreator(), $poll->getId(), $reason, repository\ReportRepo::POLLTYPE);
			$this->feedbackList[] = self::REPORTEDPOLL;
		}
	}


	//skapa en ny BasicReport
	private function report($userId, $objectId, $reason, $type)
	{
		$report = new BasicReport($userId, $objectId, $reason, $type);
		$this->reportRepo->add($report);
	}


	/**
	*	När man vill ta bort ett objekt
	* 	@param int			id till report som ska bort.
	* 	@param string 		anledning
	*/
	public function deleteObject($reportId, $reason)
	{
		//om anledningen inte var valid
		if(!$this->validateReason($reason, true))
		{
			return;
		}

		//hämta rapporten
		$report = $this->reportRepo->getReportById($reportId);

		//ta bort poll och dess rapporter (tas bort med stored procedure i db)
		if($report->getType() === repository\ReportRepo::POLLTYPE)
		{	
			$this->pollRepo->delete($report->getObjectId());
		}

		//ta bort comment och dess rapporter (tas bort med stored procedure i db)
		else if($report->getType() === repository\ReportRepo::COMMENTTYPE)
		{
			$this->commentRepo->delete($report->getObjectId());
		}

		//skapa ny userReport	
		$userReport = new userReport($report->getUserId(), $report->getObjectId(), $reason, repository\ReportRepo::USERTYPE);		
		$this->reportRepo->add($userReport);

		$this->feedbackList[] = self::DELETED;		
	}


	/**
	*	När man nominera en user till att bli borttagen
	* 	@param int 		id på user
	* 	@param int 		id på admin som gör nominering
	*/
	public function nominateForDeletion($userId, $adminId)
	{
		//uppdatera alla rapporter med detta användarid av typen User. Spara adminId.
		$this->reportRepo->nominateForDeletion($userId, $adminId);
		$this->feedbackList[] = self::USERNOMINATED;
	}


	/**
	*	När man vill ta bort en user
	* 	@param int 		id på user
	* 	@param int 		id på admin som vill ta bort användaren
	*/
	public function deleteUser($userId, $adminId)
	{
		$reports = $this->getAllUserReports();

		foreach($reports as $report)
		{
				//Det får inte vara samma som tar bort användaren som nominerade den för borttagning
				if($report->getUserId() == $userId && $report->getNomination() == $adminId)
				{
					$this->feedbackList[] = self::SAMEADMIN;
					return;
				}
		}
		
		//die!!
		$this->userRepo->delete($userId);
		$this->feedbackList[] = self::DELETED;
	}



	/**
	*validera anledningen
	* @param string 	anledning till borttagning
	* @param bool 		är anledning obligatorisk
	*/
	private function validateReason($reason, $mandatory)
	{
		$reason = htmlspecialchars($reason);

		//om reason inte är valfritt och den är tom:
		if($mandatory && strlen(trim($reason)) == 0)
		{
			$this->feedbackList[] = self::NOREASON;
			return false;
		}

		if(strlen($reason) > 200)
		{
			$this->feedbackList[] = self::LONGREASON;
			return false;
		}
		return true;
	}

	//validera poll
	private function validatePoll($poll)
	{		
		if(is_null($poll))
		{
			$this->feedbackList[] = self::NOPOLL;
			return false;
		}	
		return true;
		
	}

	//validera comment
	private function validateComment($comment)
	{
		if(is_null($comment))
		{
			$this->feedbackList[] = self::NOCOMMENT;
			return false;
		}	
		return true;
	}


	/**
	*	Hämtar alla UNIKA användare som inskickade rapporter gäller
	* @param 	reports    reports som ska kollas.
	* @return 			   en array med alla user-objekt som fanns i rapporterna. inga dupliceringar.
	*/
	public function getReportedUsers($reports)
	{
		$users = array();
		if($reports)
		{
			foreach ($reports as $report) 
			{
				//om användaren inte redan är tillagd.
				if(array_key_exists($report->getUserId(), $users) == false)
				{
					$users[$report->getUserId()] = $this->userRepo->getUserById($report->getUserId());
				}				
			}
			return array_values($users);
		}
	}


	/**
	*	Hämtar alla UNIKA undersökningar som inskickade rapporter gäller
	* @param reports    reports som ska kollas.
	* @return 			en array med alla poll-objekt som fanns i rapporterna. inga dupliceringar.
	*/
	public function getReportedPolls($reports)
	{
		$polls = array();

		if($reports)
		{
			foreach($reports as $report)
			{
				//om undersökningen inte redan är tillagd.
				if(array_key_exists($report->getObjectId(), $polls) == false)
				{
					$polls[$report->getObjectId()] = $this->pollRepo->getPollById($report->getObjectId());
				}	
			}
			return array_values($polls);
		}
	}

	/**
	*	Hämtar alla UNIKA kommentarer som inskickade rapporter gäller
	* @param reports    reports som ska kollas.
	* @return 			en array med alla comment-objekt som fanns i rapporterna. inga dupliceringar.
	*/
	public function getReportedComments($reports)
	{
		$comments = array();
		if($reports)
		{
			foreach($reports as $report)
			{
				//om kommentaren inte redan är tillagd.
				if(array_key_exists($report->getObjectId(), $comments) == false)
				{
					$comments[$report->getObjectId()] = $this->commentRepo->getCommentById($report->getObjectId());
				}	
			}
			return array_values($comments);
		}
	}
}