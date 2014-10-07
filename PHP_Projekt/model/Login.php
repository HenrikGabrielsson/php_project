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

		var_dump($user);die();

		helpers\SessionHandler::loginUser($username);

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

	
}