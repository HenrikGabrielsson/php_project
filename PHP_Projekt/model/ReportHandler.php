<?php

namespace model;

require_once("./model/repo/ReportedCommentRepo.php");
require_once("./model/repo/ReportedPollRepo.php");
require_once("./model/repo/ReportedUserRepo.php");

require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CommentRepo.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/PollReport.php");
require_once("./model/CommentReport.php");

class ReportHandler
{
	private $reportedPollRepo;
	private $reportedCommentRepo;
	private $reportedUserRepo;
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
	const REPORTEDCOMMENT = "reportedComment";
	const USERNOMINATED = "userNominated";
	const USERDELETED = "userDeleted";
	const POLLDELETED = "pollDeleted";
	const COMMENTDELETED = "commentDeleted";

	private $feedbackList = array();

	public function __construct()
	{
		$this->reportedPollRepo = new repository\ReportedPollRepo();
		$this->reportedCommentRepo = new repository\ReportedCommentRepo();
		$this->reportedUserRepo = new repository\ReportedUserRepo();
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
		return $this->reportedUserRepo->getAllReports();
	}

	//hämta alla PollReports
	public function getAllPollReports()
	{
		return $this->reportedPollRepo->getAllReports();
	}

	//hämta alla CommentReports
	public function getAllCommentReports()
	{
		return $this->reportedCommentRepo->getAllReports();
	}

	//ta bort en UserReport
	public function deleteUserReport($id)
	{
		$this->reportedUserRepo->delete($id);
	}

	//ta bort en PollReport
	public function deletePollReport($id)
	{
		$this->reportedPollRepo->delete($id);
	}

	//ta bort en CommentReport
	public function deleteCommentReport($id)
	{
		$this->reportedCommentRepo->delete($id);
	}

	/**
	* rapportera en kommentar
	* @param int 	id på rapporterad kommentar
	* @param int 	anledning till rapportering
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
			$report = new CommentReport($comment->getUserId(), $comment->getId(), $reason);
			$this->reportedCommentRepo->add($report);
			$this->feedbackList[] = self::REPORTEDCOMMENT;
		}
	}

	/**
	* rapportera en poll
	* @param int 	id på rapporterad poll
	* @param int 	anledning till rapportering
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
			$report = new PollReport($poll->getCreator(), $poll->getId(), $reason);
			$this->reportedPollRepo->add($report);
			$this->feedbackList[] = self::REPORTEDPOLL;
		}
	}

	/**
	*	När man vill ta bort en poll
	* 	@param int 		id på poll
	* 	@param string 	anledning
	*/
	public function pollDeleted($pollId, $reason)
	{
		$reports = $this->reportedPollRepo->getAllReports();

		if(!$this->validateReason($reason, true))
		{
			return;
		}

		$poll = $this->pollRepo->getPollById($pollId);

		//lägg till rapport på medlem
		$userReport = new userReport($poll->getCreator(), "poll", $reason);
		
		$this->reportedUserRepo->add($userReport);

		//ta bort poll
		$this->pollRepo->delete($pollId);

		$this->feedbackList[] = self::POLLDELETED;
		
	}

	/**
	*	När man vill ta bort en comment
	* 	@param int 		id på comment
	* 	@param string 	anledning
	*/
	public function commentDeleted($commentId, $reason)
	{
		$reports = $this->reportedCommentRepo->getAllReports();

		if(!$this->validateReason($reason, true))
		{
			return;
		}

		$comment = $this->commentRepo->getCommentById($commentId);

		//lägg till rapport på medlem
		$userReport = new userReport($comment->getUserId(), "comment", $reason);
		$this->reportedUserRepo->add($userReport);		

		//ta bort comment
		$this->commentRepo->delete($commentId);

		$this->feedbackList[] = self::COMMENTDELETED;
	}

	/**
	*	När man vill ta bort en user
	* 	@param int 		id på user
	* 	@param int 		id på admin som gör det 
	*/
	public function deleteUser($userId, $adminId)
	{

		$reports = $this->reportedUserRepo->getAllReportsOnUser($userId);

		foreach($reports as $report)
		{
				//Det får inte vara samma som tar bort användaren som nominerade den för borttagning
				if($report->getNomination() == $adminId)
				{
					$this->feedbackList[] = self::SAMEADMIN;
					return;
				}
		}
		
		//die!!
		$this->userRepo->delete($userId);
		$this->feedbackList[] = self::USERDELETED;
	}

	/**
	*	När man nominera en user till att bli borttagen
	* 	@param int 		id på user
	* 	@param int 		id på admin som gör nominering
	*/
	public function nominateForDeletion($userId, $adminId)
	{
		$reports = $this->reportedUserRepo->getAllReports();

		//uppdatera alla reports
		foreach($reports as $report)
		{
			if($report->getUserId() == $userId)
			{
				$this->reportedUserRepo->nominateForDeletion($report->getId(), $adminId);
			}
		}
		$this->feedbackList[] = self::USERNOMINATED;
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
	* @param reports    reports som ska kollas.
	* @return 			en array med alla user-objekt som fanns i rapporterna. inga dupliceringar.
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
				if(array_key_exists($report->getPollId(), $polls) == false)
				{
					$polls[$report->getPollId()] = $this->pollRepo->getPollById($report->getPollId());
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
				if(array_key_exists($report->getCommentId(), $comments) == false)
				{
					$comments[$report->getCommentId()] = $this->commentRepo->getCommentById($report->getCommentId());
				}	
			}
			return array_values($comments);
		}
	}
}