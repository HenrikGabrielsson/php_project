<?php

namespace controller;

require_once("./view/PollCreationView.php");
require_once("./model/PollCreator.php");
require_once("./model/repo/CategoryRepo.php");

require_once("./controller/IMainContentController.php");

class PollCreationController implements IMainContentController
{
	private $htmlView;
	private $pollCreationView;
	private $pollCreator;
	private $catRepo;

	private $login;

	public function __construct(\model\LoginHandler $login)
	{
		$this->login = $login;
		
		$this->catRepo = new \model\repository\CategoryRepo();
		$this->pollCreator = new \model\PollCreator();
		$this->pollCreationView = new \view\PollCreationView($this->catRepo->getAllCategories());	
	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	* @param Login 	En loginhandler som berättar vissa saker om den inloggade användaren.
	*/
	public function getBody()
	{
		//om man inte är inloggad så kan man inte skapa en poll
		if($this->login->getIsLoggedIn() === false)
		{
			return  $this->pollCreationView->getNotLoggedIn();
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
				return $this->pollCreationView->getSuccessPage();
			}
			else
			{
				$feedback = $this->pollCreator->getErrorList();
				return $this->pollCreationView->getCreate($feedback);
			}
		}
		else
		{
			return $this->pollCreationView->getCreate($feedback);
		}
	}

	public function getTitle()
	{
		return $this->pollCreationView->getTitle();
	}
}