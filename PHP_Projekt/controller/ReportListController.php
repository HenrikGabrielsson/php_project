<?php

namespace controller;

require_once("./view/ReportListView.php");
require_once("./model/ReportHandler.php");

require_once("./controller/IMainContentController.php");

class ReportListController implements IMainContentController
{
	private $reportListView;
	private $reportHandler;
	private $login;

	public function __construct(\model\LoginHandler $login)
	{
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
			//ignorera en report
			if($this->reportListView->getIgnoreReport())
			{
				$this->reportHandler->deleteReport($this->reportListView->getIgnoreReport());
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

			//ta bort ett objekt och spara medlem i lista över rapporterade medlemmar
			if($this->reportListView->getObjectToDelete())
			{
				$reportId = $this->reportListView->getObjectToDelete();
				$reason = $this->reportListView->getDeleteReason();
				$this->reportHandler->deleteObject($reportId, $reason);
			}

			//hämta eventuell feedback
			$feedback = $this->reportHandler->getFeedbackList();

			$pollReports = $this->reportHandler->getAllPollReports();
			$commentReports = $this->reportHandler->getAllCommentReports();
			$userReports = $this->reportHandler->getAllUserReports();

			//kollar vilken lista som ska hämtas. (users/polls/comments)
			switch($this->reportListView->getListRequest())
			{
				case \view\helpers\GetHandler::$POLLLIST:	
					$polls = $this->reportHandler->getReportedPolls($pollReports);
					$users = $this->reportHandler->getReportedUsers($pollReports);
					return $this->reportListView->getPollList($polls, $users, $pollReports, $feedback);
					break;
				case \view\helpers\GetHandler::$COMMENTLIST:
					$comments = $this->reportHandler->getReportedComments($commentReports);
					$users = $this->reportHandler->getReportedUsers($commentReports);
					return $this->reportListView->getCommentList($comments, $users, $commentReports, $feedback);
					break;
				default:
					$users = $this->reportHandler->getReportedUsers($userReports);
					return $this->reportListView->getUserList($users, $userReports, $feedback);
			}
		}
		else
		{
			return false;
		}
	}

	//returnera titel
	public function getTitle()
	{
		return $this->reportListView->getTitle();	
	}

}