<?php 

namespace view\helpers;

class PostHandler
{
	private static $loginName = "LoginName";
	private static $loginPassword = "LoginPassword";

	private static $regUsername = "regUsername";
	private static $regEmail = "regEmail";
	private static $regPassword1 = "regPassword1";
	private static $regPassword2 = "regPassword2";


	//getters för namnen på alla post-parametrar
	public static function getLoginName()
	{
		return self::$loginName;
	} 

	public static function getLoginPassword()
	{
		return self::$loginPassword;
	} 

	public function getRegUsername()
	{
		return self::$regUsername;
	}

	public function getRegEmail()
	{
		return self::$regEmail;
	}

		public function getRegPassword1()
	{
		return self::$regPassword1;
	}

		public function getRegPassword2()
	{
		return self::$regPassword2;
	}

}