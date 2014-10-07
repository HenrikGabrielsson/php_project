<?php

namespace view\helpers;

class GetHandler
{
	private static $login = "login";
	private static $logout = "logout";
	private static $view  = "view";
	private static $id = "id";
	private static $showResult = "showResult";
	private static $register = "register";

	//getters för namnen på alla get-parametrar
	public static function getLogin()
	{
		return self::$login;
	} 

	public static function getLogout()
	{
		return self::$logout;
	} 

	public static function getView()
	{
		return self::$view;
	} 

	public static function getId()
	{
		return self::$id;
	} 

	public static function getShowResult()
	{
		return self::$showResult;
	} 

	public static function getRegister()
	{
		return self::$register;
	} 

}