<?php

namespace model\helpers;

class SessionHandler
{
	private static $LoggedIn = "loggedIn";

	public static function getLoggedIn()
	{
		return self:: $LoggedIn;
	} 

	public static function loginUser()
	{
		$_SESSION["loggedIn"] = true;
	}

	public static function removeSessions()
	{
		session_unset(); 
		session_destroy(); 
	}
}
