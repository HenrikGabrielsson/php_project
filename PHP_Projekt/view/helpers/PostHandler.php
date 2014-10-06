<?php 

namespace view\helpers;

class PostHandler
{
	private static $loginName = "LoginName";
	private static $loginPassword = "LoginPassword";

	//getters för namnen på alla get-parametrar
	public static function getLoginName()
	{
		return self::$loginName;
	} 

	public static function getLoginPassword()
	{
		return self::$loginPassword;
	} 
}