<?php

namespace controller;

require_once("./view/ReportListView.php");
require_once("./model/repo/ReportedPollRepo.php");
require_once("./model/repo/ReportedCommentRepo.php");
require_once("./model/repo/ReportedUserRepo.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CommentRepo.php");
require_once("./model/ReportHandler.php");

class ReportListController
{
	private $reportListView;
	private $htmlView; 
	private $reportedPollRepo;
	private $reportedCommentRepo;
	private $reportedUserRepo;
	private $userRepo;
	private $pollRepo;
	private $commentRepo;
	private $reportHandler;

	public function __construct($htmlView)
	{
		$this->reportedPollRepo = new \model\repository\ReportedPollRepo();
		$this->reportedCommentRepo = new \model\repository\ReportedCommentRepo();
		$this->reportedUserRepo = new \model\repository\ReportedUserRepo();

		$this->userRepo = new \model\repository\UserRepo();
		$this->pollRepo = new \model\repository\PollRepo();
		$this->commentRepo = new \model\repository\CommentRepo();
		$this->reportHandler = new \model\ReportHandler();

		$this->reportListView = new \view\ReportListView();
		$this->htmlView = $htmlView;
	}

	public function getContent($login)
	{

		$title = $this->reportListView->getTitle();

		//endast Admins får komma hit.
		if($login->getIsAdmin())
		{
			$feedback;
			//ignorera poll report
			if($this->reportListView->getIgnorePollReport())
			{
				$this->reportedPollRepo->delete($this->reportListView->getIgnorePollReport());
			}

			//ignorera user report
			if($this->reportListView->getIgnoreUserReport())
			{
				$this->reportedUserRepo->delete($this->reportListView->getIgnoreUserReport());
			}

			//ignorera comment report
			if($this->reportListView->getIgnoreCommentReport())
			{
				$this->reportedCommentRepo->delete($this->reportListView->getIgnoreCommentReport());
			}

			//nominera en user för borttagning. (ingen borttagnin sker här.)
			if($this->reportListView->getUserToNominate())
			{
				$userId = $this->reportListView->getUserToNominate();
				if($this->reportHandler->nominateForDeletion($userId, $login->getId()))
				{
					$feedback = "You have successfully nominated this user for deletion. Another admin must confirm before deletion.";
				}
				else 
				{
					$feedback = $this->reportHandler->getErrorList();
				}
			}	
			//ta bort medlem om han/hon redan har en nominering för borttagning
			if($this->reportListView->getUserToDelete())
			{
				$userId = $this->reportListView->getUserToDelete();
				if($this->reportHandler->deleteUser($userId, $login->getId()))
				{
					$feedback = "User deleted";
				}
				else
				{
					$feedback = $this->reportHandler->getErrorList();
				}
			}				

			//ta bort undersökningen och spara medlem i lista över rapporterade medlemmar
			if($this->reportListView->getPollToDelete())
			{
				$pollId = $this->reportListView->getPollToDelete();
				$reason = $this->reportListView->getDeletePollReason();
				if($this->reportHandler->pollDeleted($pollId, $reason))
				{
					$feedback = "Poll has been deleted. User is added to the reported Users list.";
				}
				else
				{
					$feedback = $this->reportHandler->getErrorList();
				}
			}

			//ta bort kommentaren och spara medlem i lista över rapporterade medlemmar
			if($this->reportListView->getCommentToDelete())
			{
				$commentId = $this->reportListView->getCommentToDelete();
				$reason = $this->reportListView->getDeleteCommentReason();
				if($this->reportHandler->commentDeleted($commentId, $reason))
				{
					$feedback = "Poll has been deleted. User is added to the reported Users list.";
				}
				else
				{
					$feedback = $this->reportHandler->getErrorList();
				}
			}

			$pollReports = $this->reportedPollRepo->getAllReports();
			$commentReports = $this->reportedCommentRepo->getAllReports();
			$userReports = $this->reportedUserRepo->getAllReports();

			switch($this->reportListView->getListRequest())
			{
				case \view\helpers\GetHandler::$POLLLIST:	
					$polls = $this->getReportedPolls($pollReports);
					$users = $this->getReportedUsers($pollReports);
					$body = $this->reportListView->getPollList($polls, $users, $pollReports, $feedback);
					break;
				case \view\helpers\GetHandler::$COMMENTLIST:
					$comments = $this->getReportedComments($commentReports);
					$users = $this->getReportedUsers($commentReports);
					$body = $this->reportListView->getCommentList($comments, $users, $commentReports, $feedback);
					break;
				default:
					$users = $this->getReportedUsers($userReports);
					$body = $this->reportListView->getUserList($users, $userReports, $feedback);
			}

		}
		else
		{
			$body = $this->reportListView->denyPage();
		}

		$this->htmlView->showHTML($title, $body);
	}



	private function getReportedUsers($reports)
	{
		$users = array();
		foreach ($reports as $report) 
		{
			//om undersökningen inte redan är tillagd.
			if(array_key_exists($report->getUserId(), $users) == false)
			{
				$users[$report->getUserId()] = $this->userRepo->getUserById($report->getUserId());
			}				
		}
		return array_values($users);
	}



	private function getReportedPolls($reports)
	{
		$polls = array();
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



	private function getReportedComments($reports)
	{
		$comments = array();
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