<?php 

namespace controller;

require_once("./view/HTMLView.php");
require_once("./view/PollView.php");
require_once("./model/Poll.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/Voter.php");

class PollController
{
	private $htmlView;
	private $pollView;
	private $voter;
	private $repo;

	public function __construct($htmlView)
	{
		$this->repo = new \model\repository\pollRepo();
		$this->htmlView = $htmlView;
		$this->voter = new \model\Voter($this->repo);
	}

	public function getContent($id, $loggedIn)
	{
		//skapar ett repositorie-objekt och hämtar aktuell undersökning
		
		$poll = $this->repo->getPollById($id);

		$this->pollView = new \view\PollView($poll);

		//om användaren har röstat så skickas detta till modellen
		if($this->pollView->userVoted())
		{
			$ip = $this->pollView->getClient();
			$answer = $this->pollView->getAnswer();

			$this->voter->addNewVote($answer,$ip);
			//skicka svaret till model.
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