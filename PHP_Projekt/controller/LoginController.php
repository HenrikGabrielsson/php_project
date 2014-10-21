<?php

namespace controller;

require_once("view/LoginView.php");

class LoginController
{
	private $login;
	private $loginView;

	public function __construct($LoginHandler)
	{
		$this->login = $LoginHandler;
		$this->loginView = new \view\LoginView($this->login);
	}

	/**
	*	kollar om användaren är inloggad, vill logga ut/in etc.
	*/
	public function checkForLogin()
	{
		//hämtar lite info om användaren för att kunna skydda användaren mot sessionsstölder
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
		return $this->loginView->createLoginBox();

	}
}