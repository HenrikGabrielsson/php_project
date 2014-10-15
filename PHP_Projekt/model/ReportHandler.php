<?php

namespace model;

require_once("./model/repo/ReportedCommentRepo.php");
require_once("./model/repo/ReportedPollRepo.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CommentRepo.php");
require_once("./model/PollReport.php");
require_once("./model/CommentReport.php");

class ReportHandler
{
	private $reportedPollRepo;
	private $reportedCommentRepo;
	private $pollRepo;
	private $commentRepo;

	//errors
	public $longReason = "longReason";
	public $noComment = "noComment";
	public $noPoll = "noPoll";

	private $errorList = array();

	public function __construct()
	{
		$this->reportedPollRepo = new repository\ReportedPollRepo();
		$this->reportedCommentRepo = new repository\ReportedCommentRepo();
		$this->pollRepo = new repository\PollRepo();
		$this->commentRepo = new repository\CommentRepo();
	}

	public function getErrorList()
	{
		return $this->errorList;
	}	

	public function reportComment($comment, $reason)
	{
		$reason = htmlspecialchars($reason);
		$validReason = $this->validateReason($reason);
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
		$validReason = $this->validateReason($reason);
		$validPoll = $this->validatePoll($poll);

		if($validPoll && $validReason)
		{
			$report = new PollReport($poll->getCreator(), $poll->getId(), $reason);
			$this->reportedPollRepo->add($report);
			return true;
		}
		return false;
	}

	public function pollDeleted($id)
	{
		$reports = $this->reportedPollRepo->getAllReports();

		//ta bort alla som har rapporterat denna poll
		foreach($reports as $report)
		{
			if($report->getPollId() == $id)
			{
				$this->reportedPollRepo->delete($report->getId());
			}
		}

		//ta bort poll
		$this->pollRepo->delete($id);
	}

	public function commentDeleted($id)
	{
		$reports = $this->reportedCommentRepo->getAllReports();

		//ta bort alla som har rapporterat denna comment
		foreach($reports as $report)
		{
			if($report->getCommentId() == $id)
			{
				$this->reportedCommentRepo->delete($report->getId());
			}
		}

		//ta bort poll
		$this->commentRepo->delete($id);
	}

	private function validateReason($reason)
	{
		$reason = htmlspecialchars($reason);

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