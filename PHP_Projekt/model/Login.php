<?php

namespace model;

class Login
{
	public static function isLoggedIn()
	{
		if(isset($_SESSION["loggedIn"]))
		{
			return true;
		}
		return false;
	}
}