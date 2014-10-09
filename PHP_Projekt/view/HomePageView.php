<?php

namespace view;

require_once("./view/RegistrationView.php");

class HomePageView
{

	private $regView;

	public function __construct()
	{
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
			//visa poll creator
		}

		else 
		{
			$body .= $this->regView->getForm();

		}
			
		return $body;

		
	}
}