<?php

namespace controller;

require_once("./view/HTMLView.php");
require_once("./view/PollCreationView.php");
require_once("./model/PollCreator.php");
require_once("./model/Poll.php");

class PollCreationController
{
	private $htmlView;
	private $pollCreationView;
	private $pollCreator;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->pollCreationView = new \view\PollCreationView();
		$this->pollCreator = new \model\PollCreator();
	}

	public function getContent($id, $loggedIn)
	{
		$title = $this->pollCreationView->getTitle();

		if($this->pollCreationView->userWantsToCreatePoll())
		{
			//send poll to model for validation

			//on success. show poll
		}

		$feedback = $this->pollCreator->getErrorList();
		$body = $this->pollCreationView->getCreate($feedback);

		$this->htmlView->showHTML($title, $body);

	}
}