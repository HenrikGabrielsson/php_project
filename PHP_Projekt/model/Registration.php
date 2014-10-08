<?php

namespace model;

require_once("./model/repo/UserRepo.php");


class Registration
{


	//namn på fel som läggs till i errorList ifall de blir sanna
	public $shortName = "shortName";
	public $longName = "longName"; 
	public $illegalChars = "illegalChars";
	public $nameAlreadyInUse = "nameAlreadyInUse";
	public $shortPassword = "shortPassword";
	public $noMatchPasswords = "noMatchPasswords";
	public $emailAlreadyInUse = "emailAlreadyInUse";
	public $noValidEmail = "noValidEmail";

	private $errorList = array();

	private $repo;

	public function __construct()
	{
		$this->repo = new \model\repository\UserRepo();
	}

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
			$this->errorList[] = $this->shortName;
		}
		if(strlen($username) > 25)
		{
			$this->errorList[] = $this->longName;
		}

		//html-taggar i namnet. Olagligt
		if(strlen($username) !== strlen(strip_tags($username))) 
		{
			$this->errorList[] = $this->illegalChars;
		}

		//om några fel hittas. Detta för att inte öppna databasen i onödan i nästa test.
		if(count($this->errorList) > 0)
		{
			return;
		}		

		//kollar om namnet redan finns
		if($this->repo->getUserByName($username) !== NULL)
		{
			$this->errorList[] = $this->nameAlreadyInUse;
		}
	}


	//funktion som validerar ett givet lösenord
	private function validatePasswords($password1, $password2)
	{
		//för kort lösenord
		if(strlen($password1) < 6)
		{
			$this->errorList[] = $this->shortPassword;
		}

		//lösenorden matchar inte varandra
		if($password1 !== $password2)
		{
			$this->errorList[] = $this->noMatchPasswords;
		}
	}

	private function validateEmail($email)
	{
		//kollar så emailen är en riktig email-adress.
		if(filter_var($email, FILTER_VALIDATE_EMAIL) === false)
		{
			$this->errorList[] = $this->noValidEmail;
		}

		//om några fel hittas. Detta för att inte öppna databasen i onödan i nästa test.
		if(count($this->errorList) > 0)
		{
			return;
		}	

		//kollar om emailen redan har ett konto
		if($this->repo->getUserByEmail($email) !== NULL)
		{
			$this->errorList[] = $this->emailAlreadyInUse;
		}
	}

	public function getErrorList()
	{
		return $this->errorList;
	}
}