<?php

namespace model\helpers;

class SessionHandler
{
	//variabler som ska användas vid $_SESSION
	public static $LOGGEDIN = "loggedIn";
	public static $USERNAME = "username"; 
	public static $USERID = "id";
	public static $ISADMIN = "isAdmin";
	public static $IP = "ip";
	public static $USERAGENT = "userAgent";
}
