<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/User.php");

class UserRepo extends \model\repository\Repository
{

    
    //konstruktorn
    public function __construct()
    {
        $this->connect();
    }
	
	//hämta en användare
	public function getUserById($id)
	{		
		$this->connect();
		
	}
	
}



