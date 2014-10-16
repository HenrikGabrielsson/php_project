<?php

namespace controller;

require_once("model/LoginHandler.php");

require_once("view/LoginView.php");
require_once("view/HTMLView.php");

class LoginController
{
	private $login;

	private $loginView;
	private $htmlView; 

	public function __construct($htmlView, $LoginHandler)
	{
		$this->login = $LoginHandler;
		$this->loginView = new \view\LoginView($this->login);
		$this->htmlView = $htmlView;
	}

	public function checkForLogin()
	{

		$this->login->setCurrentIP($this->loginView->getIP());
		$this->login->setCurrentUserAgent($this->loginView->getUserAgent());

		//kollar om användaren är inloggad
		if($this->login->getIsLoggedIn())
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
		$this->htmlView->setLoginBox($this->loginView->createLoginBox());

	}
}