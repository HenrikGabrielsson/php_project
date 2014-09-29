<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/User.php");

class UserRepo extends \model\repository\Repository
{
	//fältnamn i databasen
	private $id = "userID";            
    private $userName = "userName";     
    private $email = "email";         
    private $password = "pasword";      
    private $salt = "salt";          
    private $dateAdded = "dateAdded";     
    private $admin = "status";

	//hämta en användare
	public function getUserById($id)
	{		
		$this->connect();
		
		//skapar en sql sats och parametrar som ska skickas med.
		$sql = 'SELECT * FROM user WHERE userID = ? ';
		$params = array($id);
		
		//förbered frågan och kör den sedan (med params)
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);
		
		$user;
		//hämtar användaren (kommer som en array om den hittades, annars FALSE)
		$result = $query->fetch();
		if($result)
		{
			$user = new \model\User
			(
				$result[$this->id],
				$result[$this->userName],
				$result[$this->email],
				$result[$this->password],
				$result[$this->salt],
				$result[$this->dateAdded],
				$result[$this->admin]		
			);
		}
		
		return $user;
		
	}
	
}



