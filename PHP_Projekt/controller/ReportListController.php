<?php

namespace controller;

require_once("./view/ReportListView.php");
require_once("./model/repo/ReportedPollRepo.php");
require_once("./model/repo/ReportedCommentRepo.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CommentRepo.php");
require_once("./model/ReportHandler.php");

require_once("./view/helpers/GetHandler.php");

class ReportListController
{
	private $reportListView;
	private $htmlView; 
	private $reportedPollRepo;
	private $reportedCommentRepo;
	private $userRepo;
	private $pollRepo;
	private $commentRepo;
	private $reportHandler;

	public function __construct($htmlView)
	{
		$this->reportListView = new \view\ReportListView();
		$this->htmlView = $htmlView;
		$this->reportedPollRepo = new \model\repository\ReportedPollRepo();
		$this->reportedCommentRepo = new \model\repository\ReportedCommentRepo();
		$this->userRepo = new \model\repository\UserRepo();
		$this->pollRepo = new \model\repository\PollRepo();
		$this->commentRepo = new \model\repository\CommentRepo();
		$this->reportHandler = new \model\ReportHandler();
	}

	public function getContent($login)
	{

		$title = $this->reportListView->getTitle();

		//endast Admins får komma hit.
		if($login->getIsAdmin())
		{
			//ignorera poll report
			if($this->reportListView->getIgnorePollReport())
			{
				$this->reportedPollRepo->delete($this->reportListView->getIgnorePollReport());
			}

			//ignorera comment report
			if($this->reportListView->getIgnoreCommentReport())
			{
				$this->reportedCommentRepo->delete($this->reportListView->getIgnoreCommentReport());
			}

			//delete poll
			if($this->reportListView->getPollToDelete())
			{
				$pollId = $this->reportListView->getPollToDelete();
				$this->reportHandler->pollDeleted($pollId);
			}

			//delete comment
			if($this->reportListView->getCommentToDelete())
			{
				$commentId = $this->reportListView->getCommentToDelete();
				$this->reportHandler->commentDeleted($commentId);
			}

			$pollReports = $this->reportedPollRepo->getAllReports();
			$commentReports = $this->reportedCommentRepo->getAllReports();

			switch($this->reportListView->getListRequest())
			{
				case \view\helpers\GetHandler::$POLLLIST:	
					$polls = $this->getReportedPolls($pollReports);
					$users = $this->getReportedUsers($pollReports);
					$body = $this->reportListView->getPollList($polls, $users, $pollReports);
					break;
				case \view\helpers\GetHandler::$COMMENTLIST:
					$comments = $this->getReportedComments($commentReports);
					$users = $this->getReportedUsers($commentReports);
					$body = $this->reportListView->getCommentList($comments, $users, $commentReports);
					break;
				default:
					$body = $this->reportListView->getUserList($polls, $comments);
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