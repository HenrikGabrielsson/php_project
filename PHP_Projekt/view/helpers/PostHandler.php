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

	private static $createQuestion = "createQuestion";
	private static $createAnswer = "createAnswer";
	private static $createCategory = "createCategory";
	private static $createPublic = "createPublic";

	private static $vote = "vote";
	private static $comment = "comment";


	//getters för namnen på alla post-parametrar
	public static function getLoginName()
	{
		return self::$loginName;
	} 

	public static function getLoginPassword()
	{
		return self::$loginPassword;
	} 

	//getters för registrering
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

	//getters för att skapa undersökning
	public function getCreateQuestion()
	{
		return self::$createQuestion;
	}

	public function getCreateAnswer()
	{
		return self::$createAnswer;
	}

	public function getCreateCategory()
	{
		return self::$createCategory;
	}

	public function getCreatePublic()
	{
		return self::$createPublic;
	}

	public function getVote()
	{
		return self::$vote;
	}

	public function getComment()
	{
		return self::$comment;
	}
}