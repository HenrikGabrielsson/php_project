<?php 

namespace controller;

require_once("./view/HTMLView.php");
require_once("./view/PollView.php");
require_once("./model/Poll.php");
require_once("./model/repo/PollRepo.php");


class PollController
{
	private $htmlView;
	private $pollView;

	public function __construct($htmlView)
	{
			$this->htmlView = $htmlView;
	}

	public function getContent($id, $loggedIn)
	{
		//skapar ett repositorie-objekt och hämtar aktuell undersökning
		$repo = new \model\repository\pollRepo();
		$poll = $repo->getPollById($id);

		//skapar ett view-objekt för att bestäma vad som ska visas.
		$this->pollView = new \view\PollView($poll);

		//hämtar titel och body som ska visas, och skickar till htmlView som renderar.
		$title = $this->pollView->getTitle();
		$body = $this->pollView->getBody();
		$this->htmlView->showHTML($title, $body);
	}
}