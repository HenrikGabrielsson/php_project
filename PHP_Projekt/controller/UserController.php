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

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	* @param Login 	En loginhandler som berättar vissa saker om den inloggade användaren.
	* @param id     Id på den aktuella användaren.
	*/
	public function getContent($id, \model\LoginHandler $login)
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
			$ownPolls = $this->pollRepo->getAllPollsFromUser($user->getId(), false, true);
		}
		else
		{
			//bara publika polls
			$ownPolls = $this->pollRepo->getAllPollsFromUser($user->getId(), false);	
		}

		$comments = $this->commentRepo->getCommentsFromUser($user->getId(), false);

		//hämta de polls som användaren har kommenterat i.
		$pollsCommentedIn = array();
		foreach($comments as $comment)
		{
			$pollsCommentedIn[] = $this->pollRepo->getPollById($comment->getPollId());
		}

		$this->userView = new \view\UserView($user, $ownPolls, $pollsCommentedIn, $comments);

		$title = $this->userView->getTitle();
		$body = $this->userView->getBody();
		$this->htmlView->showHTML($title, $body);		
	}
}