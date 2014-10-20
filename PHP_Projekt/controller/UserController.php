<?php

namespace controller;

require_once("./view/UserView.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CommentRepo.php");

class UserController
{
	private $htmlView;
	private $userView;
	
	private $userRepo;
	private $pollRepo;
	private $commentRepo;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->userRepo = new \model\repository\UserRepo();
		$this->pollRepo = new \model\repository\PollRepo();
		$this->commentRepo = new \model\repository\CommentRepo();
	}

	public function getContent($id, $login)
	{
		$user = $this->userRepo->getUserById($id);

		//om användaren inte hittas/ej anges
		if($user === false)
		{
			$this->htmlView->showErrorPage();
			die();
		}

		//om den inloggade användaren kollar på sin egen sida så ser den lite annorlunda ut
		if($id == $login->getId())
		{
			//även privata polls visas
			$polls = $this->pollRepo->getAllPollsFromUser($user->getId(), false, true);
			$comments = $this->commentRepo->getCommentsFromUser($user->getId(), false);
 
		}
		else
		{
			$polls = $this->pollRepo->getAllPollsFromUser($user->getId(), false);
			$comments = $this->commentRepo->getCommentsFromUser($user->getId(), false);	
		}

		$this->userView = new \view\UserView($user, $polls, $comments);

		$title = $this->userView->getTitle();
		$body = $this->userView->getBody();
		$this->htmlView->showHTML($title, $body);		
	}
}