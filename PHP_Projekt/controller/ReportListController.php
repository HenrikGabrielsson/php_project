<?php

namespace controller;

require_once("./view/ReportListView.php");
require_once("./model/repo/ReportedPollRepo.php");
require_once("./model/repo/ReportedCommentRepo.php");

class ReportListController
{
	private $reportListView;
	private $htmlView; 
	private $reportedPollRepo;
	private $reportedCommentRepo;

	public function __construct($htmlView)
	{
		$this->reportListView = new \view\ReportListView();
		$this->htmlView = $htmlView;
		$this->reportedPollRepo = new \model\repository\ReportedPollRepo();
		$this->reportedCommentRepo = new \model\repository\ReportedCommentRepo();
		
	}

	public function getContent($login)
	{

		$title = "Reported things";

		if($login->getIsAdmin())
		{
			$body = "welcome mr Admin, we have a table right here for you";
		}
		else
		{
			$body = $this->reportListView->denyPage();
		}

		$this->htmlView->showHTML($title, $body);
	}
	
}