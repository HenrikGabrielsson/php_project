<?php

namespace model\helpers;

class SessionHandler
{
	private static $loggedIn = "loggedIn";
	private static $username = "username"; 

	//getters för namnen på session-variablerna
	public static function getLoggedIn()
	{
		return self:: $loggedIn;
	} 

	public static function getUsername()
	{
		return self::$username;
	}

	public static function loginUser($username)
	{
		$_SESSION[self::$username] = $username;
		$_SESSION[self::$loggedIn] = true;
	}

	public static function removeSessions()
	{
		session_unset(); 
		session_destroy(); 
	}
}
