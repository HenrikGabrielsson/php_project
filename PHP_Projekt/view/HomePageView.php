<?php

namespace view;

require_once("./view/RegistrationView.php");
require_once("./view/PollCreationView.php");
require_once("./model/PollCreator.php");

class HomePageView
{

	private $regView;
	private $createView;
	private $pollCreator;

	public function __construct()
	{
		$this->pollCreator = new \model\PollCreator();
		$this->createView = new PollCreationView();

		$this->regView = new RegistrationView();
	}

	public function getTitle()
	{
		return "Create polls, vote and share your opinion";
	}

	public function getBody($loggedIn)
	{
		$body = '<h1>Welcome to Polls n\' Shit</h1>';

		if($loggedIn)
		{
			$body .= $this->createView->getForm($this->pollCreator);
		}

		else 
		{
			$body .= $this->regView->getForm();

		}
			
		return $body;

		
	}
}