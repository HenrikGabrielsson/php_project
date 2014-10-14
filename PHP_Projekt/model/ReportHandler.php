<?php

namespace model;

require_once("./model/repo/ReportedCommentRepo.php");
require_once("./model/repo/ReportedPollRepo.php");
require_once("./model/PollReport.php");
require_once("./model/CommentReport.php");

class ReportHandler
{
	private $reportedPollRepo;
	private $reportedCommentRepo;

	//errors
	public $longReason = "longReason";
	public $noComment = "noComment";
	public $noPoll = "noPoll";

	private $errorList = array();

	public function __construct()
	{
		$this->reportedPollRepo = new repository\ReportedPollRepo();
		$this->reportedCommentRepo = new repository\ReportedCommentRepo();
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