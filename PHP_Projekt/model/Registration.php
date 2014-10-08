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

		//om några fel hittas.
		if(count($this->errorList) > 0)
		{
			return false;
		}	

		return true;	
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