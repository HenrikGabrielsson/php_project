<?php

namespace controller;

require_once("model/Login.php");

require_once("view/LoginView.php");
require_once("view/HTMLView.php");

class LoginController
{
	private $login;

	private $loginView;
	private $htmlView; 

	public function __construct($htmlView)
	{
		$this->login = new \model\Login();
		$this->loginView = new \view\LoginView();
		$this->htmlView = $htmlView;
	}

	public function checkForLogin()
	{

		//kollar om användaren är inloggad
		if($this->login->isLoggedIn())
		{
			//om användaren vill logga ut.
			if($this->loginView->userWantsToLogout())
			{
				$this->login->logOut();
			}
		}

		//om användaren inte är inloggad
		else
		{
			//om användaren vill logga in
			if($this->loginView->userWantsToLogin())
			{
				$username = $this->loginView->getUsername();
				$password = $this->loginView->getPassword();

				$this->login->attemptLogin($username, $password);
			}
		}

		//skicka uppgifter till htmlView om feedback och om användaren är inloggad.
		$this->htmlView->createLoginBox($this->login->isLoggedIn(), $this->loginView->getFeedback());
	}
}