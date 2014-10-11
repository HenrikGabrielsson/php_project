<?php

namespace controller;

require_once("./view/HTMLView.php");
require_once("./view/UserView.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/User.php");

class UserController
{
	private $htmlView;
	private $userView;
	private $userRepo;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->userRepo = new \model\repository\UserRepo();
	}

	public function getContent($id, $loggedIn)
	{
		$user = $this->userRepo->getUserById($id);
		$this->userView = new \view\UserView($user);

		$title = $this->userView->getTitle();
		$body = $this->userView->getBody();
		$this->htmlView->showHTML($title, $body);

	}
}