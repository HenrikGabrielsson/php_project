<?php

namespace model;

require_once("./model/helpers/SessionHandler.php");
require_once("./model/repo/UserRepo.php");

class Login
{
	public $noNameError = "noName";
	public $noPasswordError = "noPassword";
	public $wrongCredentialsError = "wrongCredentials";

	private $errorList = array();

	private $repo;
	

	public function __construct()
	{
		session_start();
		$this->repo = new repository\UserRepo();
	}

	public static function isLoggedIn()
	{

		if($_SESSION[helpers\SessionHandler::getLoggedIn()])
		{
			return true;
		}
		return false;
	}

	public function attemptLogin($username, $password)
	{
		if($username == "")
		{
			$this->errorList[] = $this->noNameError;
		}

		if($password == "")
		{
			$this->errorList[] = $this->noPasswordError;
		}

		//om något fält inte är ifyllt så meddelas användaren om detta och databasen anropas aldrig.
		if(count($this->errorList) > 0)
		{
			return;
		}

		//försöker hämta användaren från databasen.

		$user = $this->repo->getUserByName($username);

		//Om användaren inte hittades eller om lösenordet inte matchar 
		if(isset($user) && $this->checkPassword($password, $user->getPassword(), $user->getSalt()))
		{
			helpers\SessionHandler::loginUser($username);
		}
		else
		{
			$this->errorList[] = $this->wrongCredentialsError;
			return;
		}
	}

	public function logout()
	{
		helpers\SessionHandler::removeSessions();
	}

	public function getLoggedInUser()
	{
		return $_SESSION[helpers\SessionHandler::getUsername()];
	}

	public function getErrorList()
	{
		return $this->errorList;
	}

	//den här funktionen kollar om det angivna lösenordet matchar det hashade/saltade lösenordet från databasen.
	private function checkPassword($givenPassword, $correctPassword, $salt)
	{
		return $correctPassword == sha1($salt.md5($givenPassword));
	}

	
}