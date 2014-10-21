<?php 

namespace controller;

require_once("./view/HomePageView.php");
require_once("./model/repo/PollRepo.php");
require_once("./controller/IMainContentController.php");

class HomeController implements IMainContentController
{
	private $homeView;
	private $pollRepo;

	private $login;

	public function __construct(\model\LoginHandler $login)
	{
		$this->login = $login;

		$this->pollRepo = new \model\repository\PollRepo();
		$this->homeView = new \view\HomePageView($this->pollRepo->getLatestPolls(3));
	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	*/
	public function getBody()
	{
		return $this->homeView->getBody($this->login->getIsLoggedIn());
	}

	public function getTitle()
	{
		return $this->homeView->getTitle();
	}
}