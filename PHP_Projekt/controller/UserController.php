<?php

namespace controller;

require_once("./view/HTMLView.php");
require_once("./view/UserView.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CommentRepo.php");
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
		$this->pollRepo = new \model\repository\PollRepo();
		$this->commentRepo = new \model\repository\CommentRepo();
	}

	public function getContent($id, $login)
	{
		$user = $this->userRepo->getUserById($id);
		$polls = $this->pollRepo->getAllPollsFromUser($user->getId(), false, true);
		$comments = $this->commentRepo->getCommentsFromUser($user->getId(), false);
		$this->userView = new \view\UserView($user, $polls, $comments);

		$title = $this->userView->getTitle();
		$body = $this->userView->getBody();
		$this->htmlView->showHTML($title, $body);

	}
}