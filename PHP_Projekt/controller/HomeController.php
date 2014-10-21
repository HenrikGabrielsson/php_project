<?php 

namespace controller;

require_once("./view/HomePageView.php");
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
		$this->homeView = new \view\HomePageView($this->pollRepo->getLatestPolls(3));
		
	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	* @param Login 	En loginhandler som berättar vissa saker om den inloggade användaren.
	*/
	public function getContent(\model\LoginHandler $login)
	{
		$title = $this->homeView->getTitle();

		$body = $this->homeView->getBody($login->getIsLoggedIn());
		$this->htmlView->showHTML($title, $body);
	}
}