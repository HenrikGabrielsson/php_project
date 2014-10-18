<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/User.php");

class UserRepo extends \model\repository\Repository
{

	private $userTable = "user";

	//fältnamn i databasen
	private $userId = "userId";            
    private $userName = "userName";     
    private $email = "email";         
    private $password = "password";      
    private $salt = "salt";          
    private $dateAdded = "dateAdded";     
    private $admin = "status";

	//hämta en användare
	public function getUserById($id)
	{		
		
		//skapar en sql sats och parametrar som ska skickas med.
		$sql = 'SELECT * FROM '.$this->userTable.' WHERE '.$this->userId.' = ? ';
		$params = array($id);
		
		$this->connect();
		
		//förbered frågan och kör den sedan (med params)
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);
		
		
		//hämtar användaren (kommer som en array om den hittades, annars FALSE)
		$result = $query->fetch();
		
		//lägger in datan i ett User-objekt och returnerar
		$user;
		if($result)
		{
			$user = new \model\User
			(
				$result[$this->userName],
				$result[$this->email],
				$result[$this->password],
				$result[$this->salt],
				$result[$this->dateAdded],
				$result[$this->admin],
				$result[$this->userId]
			);
		}
		
		return $user;
		
	}

	//hämtar en användare baserat på ett givet namn. 
	public function getUserByName($username)
	{
		$sql = 'SELECT * FROM '.$this->userTable.' WHERE '.$this->userName.' = BINARY ?';
		$params = array($username);

		$this->connect();

		//förbered frågan och kör den sedan (med params)
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);

		$result = $query->fetch();

		//lägger in datan i ett User-objekt och returnerar (om det finns).
		$user;
		if($result)
		{
			$user = new \model\User
			(	
				$result[$this->userName],
				$result[$this->email],
				$result[$this->password],
				$result[$this->salt],
				$result[$this->dateAdded],
				$result[$this->admin],
				$result[$this->userId]
			);
		}

		return $user;
	}


	//hämtar en användare baserat på ett givet email.
	public function getUserByEmail($email)
	{
		$sql = 'SELECT * FROM '.$this->userTable.' WHERE '.$this->email.' = ?';
		$params = array($email);

		$this->connect();

		//förbered frågan och kör den sedan (med params)
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);

		$result = $query->fetch();

		//lägger in datan i ett User-objekt och returnerar (om det finns).
		$user;
		if($result)
		{
			$user = new \model\User
			(	
				$result[$this->userName],
				$result[$this->email],
				$result[$this->password],
				$result[$this->salt],
				$result[$this->dateAdded],
				$result[$this->admin],
				$result[$this->userId]
			);
		}

		return $user;
	}	


	public function add(\model\User $user)
	{
		
		$sql = "INSERT INTO ".$this->userTable."(".$this->userName.",".$this->email.",".$this->dateAdded.",".$this->admin.",".$this->password.",".$this->salt.") VALUES(?,?,?,?,?,?)";
		$params = array($user->getUserName(), $user->getEmail(), $user->getDateAdded(), $user->getAdmin(), $user->getPassword(), $user->getSalt());

		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
				
		return $result;		
	}
	
	public function delete($id)
	{
		$sql = "DELETE FROM ".$this->userTable." WHERE ".$this->userId." = ?";
		$params = array($id);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		return $result;
		
	}
	
}



