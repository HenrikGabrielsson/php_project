<?php 

namespace controller;

require_once("./view/HTMLView.php");
require_once("./view/PollView.php");
require_once("./model/Poll.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/Voter.php");

class PollController
{
	private $htmlView;
	private $pollView;
	private $voter;
	private $pollRepo;
	private $userRepo;

	public function __construct($htmlView)
	{
		$this->pollRepo = new \model\repository\PollRepo();
		$this->userRepo = new \model\repository\UserRepo();
		$this->htmlView = $htmlView;
		$this->voter = new \model\Voter($this->pollRepo);
	}

	public function getContent($id, $loggedIn)
	{
		//skapar ett repositorie-objekt och hämtar aktuell undersökning
		
		$poll = $this->pollRepo->getPollById($id);
		$owner = $this->userRepo->getUserById($poll->getCreator());

		$this->pollView = new \view\PollView($poll, $owner);

		//om användaren har röstat så skickas detta till modellen
		if($this->pollView->userVoted())
		{
			$ip = $this->pollView->getClient();
			$answer = $this->pollView->getAnswer();

			$this->voter->addNewVote($answer,$ip);
		}

		//om användaren vill se resultat eller frågan där de kan rösta
		if($this->pollView->userWantsResults())
		{
			$body = $this->pollView->getResult();
		}
		else
		{
			$body = $this->pollView->getForm();
		}

		$title = $this->pollView->getTitle();
		$this->htmlView->showHTML($title, $body);
	}
}