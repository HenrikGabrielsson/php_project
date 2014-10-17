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
	public $longReason = "longReason";
	public $noReason = "noReason";
	public $noComment = "noComment";
	public $noPoll = "noPoll";
	public $sameAdmin = "sameAdmin";

	private $errorList = array();

	public function __construct()
	{
		$this->reportedPollRepo = new repository\ReportedPollRepo();
		$this->reportedCommentRepo = new repository\ReportedCommentRepo();
		$this->reportedUserRepo = new repository\ReportedUserRepo();
		$this->pollRepo = new repository\PollRepo();
		$this->commentRepo = new repository\CommentRepo();
		$this->userRepo = new repository\UserRepo();
	}

	public function getErrorList()
	{
		return $this->errorList;
	}	

	public function reportComment($comment, $reason)
	{
		$reason = htmlspecialchars($reason);
		$validReason = $this->validateReason($reason, false);
		$validComment = $this->validateComment($comment);

		if($validComment && $validReason)
		{
			$report = new CommentReport($comment->getUserId(), $comment->getId(), $reason);
			$this->reportedCommentRepo->add($report);
			return true;
		}
		return false;
		

	}

	public function reportPoll($poll, $reason)
	{

		$reason = htmlspecialchars($reason);
		$validReason = $this->validateReason($reason, false);
		$validPoll = $this->validatePoll($poll);

		if($validPoll && $validReason)
		{
			$report = new PollReport($poll->getCreator(), $poll->getId(), $reason);
			$this->reportedPollRepo->add($report);
			return true;
		}
		return false;
	}

	public function pollDeleted($pollId, $reason)
	{
		$reports = $this->reportedPollRepo->getAllReports();

		if(!$this->validateReason($reason, true))
		{
			return false;
		}

		//ta bort alla som har rapporterat denna poll
		foreach($reports as $report)
		{
			if($report->getPollId() == $pollId)
			{
				$this->reportedPollRepo->delete($report->getId());
			}
		}

		$poll = $this->pollRepo->getPollById($pollId);

		//lägg till rapport på medlem
		$userReport = new userReport($poll->getCreator(), "poll", $reason);
		$this->reportedUserRepo->add($userReport);

		//ta bort poll
		$this->pollRepo->delete($pollId);

		return true;
	}

	public function commentDeleted($commentId, $reason)
	{
		$reports = $this->reportedCommentRepo->getAllReports();

		if(!$this->validateReason($reason, true))
		{
			return false;
		}

		//ta bort alla som har rapporterat denna comment
		foreach($reports as $report)
		{
			if($report->getCommentId() == $commentId)
			{
				$this->reportedCommentRepo->delete($report->getId());
			}
		}

		$comment = $this->commentRepo->getCommentById($commentId);

		//lägg till rapport på medlem
		$userReport = new userReport($comment->getUserId(), "comment", $reason);
		$this->reportedUserRepo->add($userReport);		

		//ta bort comment
		$this->commentRepo->delete($commentId);

		return true;
	}

	//ta bort en användare
	public function deleteUser($userId, $adminId)
	{

		$reports = $this->reportedUserRepo->getAllReports();

		//ta bort alla som har rapporterat denna användare
		foreach($reports as $report)
		{
			if($report->getUserId() == $userId)
			{

				//Det får inte vara samma som tar bort användaren som nominerade den för borttagning
				if($report->getNomination() == $adminId)
				{
					$this->errorList[] = $this->sameAdmin;
					return false;
				}
				//annars tas rapporterna bort en efter en.
				else
				{
					$this->reportedUserRepo->delete($report->getId());
				}
			}
		}
		//die!!
		$this->userRepo->delete($userId);
		return true;
	}


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
		return true;
	}



	private function validateReason($reason, $mandatory)
	{
		$reason = htmlspecialchars($reason);

		//om reason inte är valfritt och den är tom:
		if($mandatory && strlen(trim($reason)) == 0)
		{
			$this->errorList[] = $this->noReason;
			return false;
		}

		if(strlen($reason) > 200)
		{
			$this->errorList[] = $this->longReason;
			return false;
		}
		return true;
	}

	private function validatePoll($poll)
	{		
		if(is_null($poll))
		{
			$this->errorList[] = $this->noPoll;
			return false;
		}	
		return true;
		
	}

	private function validateComment($comment)
	{
		if(is_null($comment))
		{
			$this->errorList[] = $this->noComment;
			return false;
		}	
		return true;
	}
}