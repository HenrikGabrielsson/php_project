<?php

namespace controller;

require_once("./view/PollCreationView.php");
require_once("./model/PollCreator.php");
require_once("./model/repo/CategoryRepo.php");

class PollCreationController
{
	private $htmlView;
	private $pollCreationView;
	private $pollCreator;
	private $catRepo;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		
		$this->catRepo = new \model\repository\CategoryRepo();
		$this->pollCreator = new \model\PollCreator();
		$this->pollCreationView = new \view\PollCreationView($this->catRepo->getAllCategories());	
	}

	public function getContent($login)
	{
		$title = $this->pollCreationView->getTitle();
		$body;

		//om man inte är inloggad så kan man inte skapa en poll
		if($login->getIsLoggedIn() === false)
		{
			$body = $this->pollCreationView->getNotLoggedIn();
		}

		else if($this->pollCreationView->userWantsToCreatePoll())
		{
			$question = $this->pollCreationView->getQuestion();
			$answers = $this->pollCreationView->getAnswers();
			$category = $this->pollCreationView->getCategory();
			$public = $this->pollCreationView->getIsPublic();

			$success = $this->pollCreator->attemptTocreate($question, $answers, $category, $public);

			if($success)
			{
				$body = $this->pollCreationView->getSuccessPage();
			}
			else
			{
				$feedback = $this->pollCreator->getErrorList();
				$body = $this->pollCreationView->getCreate($feedback);
			}
		}
		else
		{
			$body = $this->pollCreationView->getCreate();

		}
		
		$this->htmlView->showHTML($title, $body);

	}
}