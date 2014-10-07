<?php 

namespace controller;

require_once("./view/HTMLView.php");
require_once("./view/RegistrationView.php");
require_once("./view/helpers/GetHandler.php");

class RegistrationController
{
	private $htmlView;
	private $regView;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->regView = new \view\RegistrationView();
	}

	public function getContent($id, $loggedIn)
	{

		$title = $this->regView->getTitle();
		$body;

		if($this->regView->userWantsToRegister())
		{
			echo "pressed";
			/*
			if(it worked)
				give feedback
				login
			else
				give feedback	
			*/		
		}


		else 
		{
			$body = $this->regView->getForm();
		}

		$this->htmlView->showHTML($title, $body);
			
	}
}