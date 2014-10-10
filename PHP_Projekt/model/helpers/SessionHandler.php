<?php

namespace model\helpers;

class SessionHandler
{
	private static $loggedIn = "loggedIn";
	private static $username = "username"; 
	private static $userId = "id";

	//getters för namnen på session-variablerna
	public static function getLoggedIn()
	{
		return self:: $loggedIn;
	} 

	public static function getUsername()
	{
		return self::$username;
	}

	public function getUserId()
	{
		return self::$userId;
	}

	public static function loginUser($username, $id)
	{
		$_SESSION[self::$username] = $username;
		$_SESSION[self::$userId] = $id;
		$_SESSION[self::$loggedIn] = true;

	}

	public static function removeSessions()
	{
		session_unset(); 
		session_destroy(); 
	}
}
