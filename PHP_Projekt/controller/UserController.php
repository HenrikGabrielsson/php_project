<?php

namespace controller;

require_once("./view/UserView.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CommentRepo.php");

require_once("./controller/IMainContentController.php");

class UserController implements IMainContentController
{
	private $userView;

	private $user;
	private $login;
	
	private $userRepo;
	private $pollRepo;
	private $commentRepo;

	/**
	* @param Login 	En loginhandler som berättar vissa saker om den inloggade användaren.
	* @param id     Id på den aktuella användaren.
	*/
	public function __construct($id, \model\LoginHandler $login)
	{
		$this->userRepo = new \model\repository\UserRepo();
		$this->pollRepo = new \model\repository\PollRepo();
		$this->commentRepo = new \model\repository\CommentRepo();

		$this->user = $this->userRepo->getUserById($id);
		$this->login = $login;

		$this->userView = new \view\UserView($this->user);
	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	*/
	public function getBody()
	{
		//om användaren inte hittas/ej anges
		if($this->user === false)
		{
			return false;
		}

		//om den inloggade användaren kollar på sin egen sida så ser den lite annorlunda ut
		if($this->user->getId() == $this->login->getId())
		{
			//även privata polls visas
			$ownPolls = $this->pollRepo->getAllPollsFromUser($this->user->getId(), false, true);
		}
		else
		{
			//bara publika polls
			$ownPolls = $this->pollRepo->getAllPollsFromUser($this->user->getId(), false);	
		}

		$comments = $this->commentRepo->getCommentsFromUser($this->user->getId(), false);

		//hämta de polls som användaren har kommenterat i.
		$pollsCommentedIn = array();
		foreach($comments as $comment)
		{
			$pollsCommentedIn[] = $this->pollRepo->getPollById($comment->getPollId());
		}

		return $this->userView->getBody($ownPolls, $pollsCommentedIn, $comments);	
	}

	public function getTitle()
	{
		return $this->userView->getTitle();
	}
}