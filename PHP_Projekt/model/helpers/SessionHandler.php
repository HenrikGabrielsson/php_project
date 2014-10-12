<?php

namespace model\helpers;

class SessionHandler
{
	public static $LOGGEDIN = "loggedIn";
	public static $USERNAME = "username"; 
	public static $USERID = "id";

	public static function loginUser($username, $id)
	{
		$_SESSION[self::$USERNAME] = $username;
		$_SESSION[self::$USERID] = $id;
		$_SESSION[self::$LOGGEDIN] = true;

	}

	public static function removeSessions()
	{
		session_unset(); 
		session_destroy(); 
	}
}
