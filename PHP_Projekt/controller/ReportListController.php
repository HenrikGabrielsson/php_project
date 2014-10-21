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

require_once("./controller/IMainContentController.php");

class ReportListController implements IMainContentController
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
	private $login;

	public function __construct(\model\LoginHandler $login)
	{
		$this->reportedPollRepo = new \model\repository\ReportedPollRepo();
		$this->reportedCommentRepo = new \model\repository\ReportedCommentRepo();
		$this->reportedUserRepo = new \model\repository\ReportedUserRepo();
		$this->userRepo = new \model\repository\UserRepo();
		$this->pollRepo = new \model\repository\PollRepo();
		$this->commentRepo = new \model\repository\CommentRepo();
		$this->reportHandler = new \model\ReportHandler();
		$this->login = $login;

		$this->reportListView = new \view\ReportListView();
	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	*/
	public function getBody()	
	{
		//endast Admins får komma hit.
		if($this->login->getIsAdmin())
		{
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
				$this->reportHandler->nominateForDeletion($userId, $this->login->getId());
			}	
			//ta bort medlem om han/hon redan har en nominering för borttagning
			if($this->reportListView->getUserToDelete())
			{
				$userId = $this->reportListView->getUserToDelete();
				$this->reportHandler->deleteUser($userId, $this->login->getId());
			}				

			//ta bort undersökningen och spara medlem i lista över rapporterade medlemmar
			if($this->reportListView->getPollToDelete())
			{
				$pollId = $this->reportListView->getPollToDelete();
				$reason = $this->reportListView->getDeletePollReason();
				$this->reportHandler->pollDeleted($pollId, $reason);
			}

			//ta bort kommentaren och spara medlem i lista över rapporterade medlemmar
			if($this->reportListView->getCommentToDelete())
			{
				$commentId = $this->reportListView->getCommentToDelete();
				$reason = $this->reportListView->getDeleteCommentReason();
				$this->reportHandler->commentDeleted($commentId, $reason);
			}

			//hämta eventuell feedback
			$feedback = $this->reportHandler->getFeedbackList();

			$pollReports = $this->reportedPollRepo->getAllReports();
			$commentReports = $this->reportedCommentRepo->getAllReports();
			$userReports = $this->reportedUserRepo->getAllReports();

			//kollar vilken lista som ska hämtas. (users/polls/comments)

			switch($this->reportListView->getListRequest())
			{
				case \view\helpers\GetHandler::$POLLLIST:	
					$polls = $this->getReportedPolls($pollReports);
					$users = $this->getReportedUsers($pollReports);
					return $this->reportListView->getPollList($polls, $users, $pollReports, $feedback);
					break;
				case \view\helpers\GetHandler::$COMMENTLIST:
					$comments = $this->getReportedComments($commentReports);
					$users = $this->getReportedUsers($commentReports);
					return $this->reportListView->getCommentList($comments, $users, $commentReports, $feedback);
					break;
				default:
					$users = $this->getReportedUsers($userReports);
					return $this->reportListView->getUserList($users, $userReports, $feedback);
			}
		}
		else
		{
			return false;
		}
	}

	public function getTitle()
	{
		return $this->reportListView->getTitle();	
	}

	/**
	*	Hämtar alla UNIKA användare som inskickade rapporter gäller
	* @param reports    reports som ska kollas.
	* @return 			en array med alla user-objekt som fanns i rapporterna. inga dupliceringar.
	*/
	private function getReportedUsers($reports)
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
	private function getReportedPolls($reports)
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
	private function getReportedComments($reports)
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