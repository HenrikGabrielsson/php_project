<?php

namespace model;

require_once("./model/helpers/SessionHandler.php");

class Login
{
	public $noNameError = "noName";
	public $noPasswordError = "noPassword";
	public $wrongCredentialsError = "wrongCredentials";

	private $errorList = array();
	

	public function __construct()
	{
		session_start();
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

		if(count($this->errorList) > 0)
		{
			return;
		} 

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