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

	public function __construct()
	{
			$this->htmlView = new \view\HTMLView();
	}

	public function getContent($id)
	{
		$repo = new \model\repository\pollRepo();
		$poll = $repo->getPollById($id);

		$this->pollView = new \view\PollView($poll);

		$title = $this->pollView->getTitle();
		$body = $this->pollView->getBody();

		$this->htmlView->showHTML($title, $body);
	}
}