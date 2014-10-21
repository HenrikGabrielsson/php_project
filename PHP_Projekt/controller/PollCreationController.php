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

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	* @param Login 	En loginhandler som berättar vissa saker om den inloggade användaren.
	*/
	public function getContent(\model\LoginHandler $login)
	{
		$title = $this->pollCreationView->getTitle();
		$body;

		//om man inte är inloggad så kan man inte skapa en poll
		if($login->getIsLoggedIn() === false)
		{
			$body = $this->pollCreationView->getNotLoggedIn();
		}

		//användaren vill försöka skapa en undersökning
		else if($this->pollCreationView->userWantsToCreatePoll())
		{
			//hämta formulärdata
			$question = $this->pollCreationView->getQuestion();
			$answers = $this->pollCreationView->getAnswers();
			$category = $this->pollCreationView->getCategory();
			$public = $this->pollCreationView->getIsPublic();

			//försöker skapa den och visar sedan feedback
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
			$body = $this->pollCreationView->getCreate($feedback);
		}
		
		$this->htmlView->showHTML($title, $body);

	}
}