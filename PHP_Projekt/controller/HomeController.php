<?php 

namespace controller;

require_once("./view/HomePageView.php");
require_once("./view/HTMLView.php");
require_once("./model/repo/PollRepo.php");

class HomeController
{
	private $htmlView; 
	private $homeView;
	private $pollRepo;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->pollRepo = new \model\repository\PollRepo();
		$this->homeView = new \view\HomePageView($this->pollRepo);
		
	}

	public function getContent($id, $loggedIn)
	{
		$title = $this->homeView->getTitle();
		$body = $this->homeView->getBody($loggedIn);

		$this->htmlView->showHTML($title, $body);
	}
}