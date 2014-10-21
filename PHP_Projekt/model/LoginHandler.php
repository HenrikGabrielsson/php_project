<?php

namespace model;

require_once("./model/helpers/SessionHandler.php");
require_once("./model/repo/UserRepo.php");

class LoginHandler
{

	//Namn på fel. Läggs till i errorList om de har blivit sanna.
	//fel vid login
	const NONAME = "noName";
	const NOPASSWORD = "noPassword";
	const WRONGCREDENTIALS = "wrongCredentials";

	//fel vid registrering
	const SHORTNAME = "shortName";
	const LONGNAME = "longName"; 
	const ILLEGALCHARS = "illegalChars";
	const NAMEALREADYINUSE = "nameAlreadyInUse";
	const SHORTPASSWORD = "shortPassword";
	const NOMATCHPASSWORDS = "noMatchPasswords";
	const EMAILALREADYINUSE = "emailAlreadyInUse";
	const NOVALIDEMAIL = "noValidEmail";

	private $errorList = array();

	private $repo;

	private $ip;
	private $userAgent;
	

	public function __construct()
	{
		session_start();
		$this->repo = new repository\UserRepo();
	}

	//Sätter IP på den inloggade
	public function setCurrentIP($ip)
	{
		$this->ip = $ip;
	}

	//sätter user agent (mjukvara som används) på den inloggade
	public function setCurrentUserAgent($ua)
	{
		$this->userAgent = $ua;
	}

	/**
	* 	funktion som tar emot namn, lösenord och försöker sedan logga in personen
	*	@param string 	namn på den som vill logga in
	*	@param string 	lösenord.
	*/
	public function attemptLogin($username, $password)
	{
		if($username == "")
		{
			$this->errorList[] = self::NONAME;
		}

		if($password == "")
		{
			$this->errorList[] = self::NOPASSWORD;
		}

		//om något fält inte är ifyllt så meddelas användaren om detta och databasen anropas aldrig.
		if(count($this->errorList) > 0)
		{
			return;
		}

		//försöker hämta användaren från databasen.

		$user = $this->repo->getUserByName($username);


		//Om användaren hittades och om lösenordet matchar 
		if($user && $this->checkPassword($password, $user->getPassword(), $user->getSalt()))
		{
			$this->loginUser($user->getUsername(), $user->getId(), $user->getAdmin());
		}
		else
		{
			$this->errorList[] = self::WRONGCREDENTIALS;
			return;
		}
	}

	//hämta namn på den inloggade
	public function getUser()
	{
		return $_SESSION[helpers\SessionHandler::$USERNAME];
	}

	//hämta om användaren är en admin eller inte
	public function getIsAdmin()
	{
		return $_SESSION[helpers\SessionHandler::$ISADMIN];
	}

	//hämta den inloggades userID.
	public function getId()
	{
		return $_SESSION[helpers\SessionHandler::$USERID];
	}

	//hämta om användaren är inloggad eller inte.
	public function getIsLoggedIn()
	{
		if (isset($_SESSION[helpers\SessionHandler::$LOGGEDIN]))
		{
			//jämför det sparade ip-numret/user agent med den nuvarande för att se så det är samma person bakom.
			if($_SESSION[helpers\SessionHandler::$IP] == $this->ip && $_SESSION[helpers\SessionHandler::$USERAGENT] == $this->userAgent)
			{
				return true;
			} 
		}
		return false;
	}

	//skicka lista med fel vid inloggning eller registrering
	public function getErrorList()
	{
		return $this->errorList;
	}

	/**
	*	logga in en användare genom att spara data i session
	* 	@param  string 	namn på användare
	* 	@param 	int 	unikt id på användare
	*	@param 	bool 	admin eller inte.
	*/
	public function loginUser($username, $id, $isAdmin)
	{
		$_SESSION[helpers\SessionHandler::$USERNAME] = $username;
		$_SESSION[helpers\SessionHandler::$ISADMIN] = $isAdmin;
		$_SESSION[helpers\SessionHandler::$USERID] = $id;
		$_SESSION[helpers\SessionHandler::$LOGGEDIN] = true;

		$_SESSION[helpers\SessionHandler::$IP] = $this->ip;
		$_SESSION[helpers\SessionHandler::$USERAGENT]  = $this->userAgent;
	}

	//vid utloggning. Ta bort sessions
	public function logout()
	{
		session_unset(); 
		session_destroy(); 
	}


	//den här funktionen kollar om det angivna lösenordet matchar det hashade/saltade lösenordet från databasen.
	private function checkPassword($givenPassword, $correctPassword, $salt)
	{
		return $correctPassword == sha1($salt.md5($givenPassword));
	}

	/**
	*	Försök registrera ny användare
	* 	@param string 	namn som användaren vill ha
	* 	@param string 	email som användaren vill sparas på
	* 	@param string 	valt lösenord
	* 	@param string 	lösenord igen, för att se så användaren kan skriva det 2 gånger
	*/
	public function attemptRegister($username, $email, $password1, $password2)
	{

		//leta efter fel i namn och lösenord
		$this->validateName($username);
		$this->validatePasswords($password1, $password2);
		$this->validateEmail($email);

		//om några fel hittas så avslutas registreringen.
		if(count($this->errorList) > 0)
		{
			return false;
		}	

		$this->registerUser($username, $email, $password1);
		return true;	
	}

	//denna funktion registrerar en ny medlem genom att lägga till dess uppgifter i databasen.
	private function registerUser($username, $email, $password)
	{
		$salt = mcrypt_create_iv(32,MCRYPT_RAND);
		$password = $this->hashPassword($password, $salt);

		$newUser = new \model\User($username, $email, $password, $salt, date("Y-m-d"));

		$this->repo->add($newUser);

	}

	//kryptera lösenord innan det sparas 
	private function hashPassword($password, $salt)
	{
		return sha1($salt.md5($password));
	}

	//funktion som validerar ett givet namn 
	private function validateName($username)
	{
		//för kort namn eller för långt namn
		if(strlen($username) < 3)
		{
			$this->errorList[] = self::SHORTNAME;
		}
		if(strlen($username) > 25)
		{
			$this->errorList[] = self::LONGNAME;
		}

		//html-taggar i namnet. Olagligt
		if(strlen($username) !== strlen(strip_tags($username))) 
		{
			$this->errorList[] = self::ILLEGALCHARS;
		}

		//om några fel hittas. Detta för att inte öppna databasen i onödan i nästa test.
		if(count($this->errorList) > 0)
		{
			return;
		}		

		//kollar om namnet redan finns
		if($this->repo->getUserByName($username) !== false)
		{
			$this->errorList[] = self::NAMEALREADYINUSE;
		}
	}


	//funktion som validerar ett givet lösenord
	private function validatePasswords($password1, $password2)
	{
		//för kort lösenord
		if(strlen($password1) < 6)
		{
			$this->errorList[] = self::SHORTPASSWORD;
		}

		//lösenorden matchar inte varandra
		if($password1 !== $password2)
		{
			$this->errorList[] = self::NOMATCHPASSWORDS;
		}
	}

	private function validateEmail($email)
	{
		//kollar så emailen är en riktig email-adress.
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
		{
			$this->errorList[] = self::NOVALIDEMAIL;
		}

		//om några fel hittas. Detta för att inte öppna databasen i onödan i nästa test.
		if(count($this->errorList) > 0)
		{
			return;
		}	

		//kollar om emailen redan har ett konto
		if($this->repo->getUserByEmail($email) !== false)
		{
			$this->errorList[] = self::EMAILALREADYINUSE;
		}
	}

}