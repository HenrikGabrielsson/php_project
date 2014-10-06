<?php

namespace model;

require_once("./model/helpers/SessionHandler.php");

class Login
{
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
		helpers\SessionHandler::loginUser();
	}

	public function logout()
	{
		helpers\SessionHandler::removeSessions();
	}

	public function getLoggedInUser()
	{
		return "TESTTESTTESTTEST";
	}
	
}