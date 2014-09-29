<?php

class User
{
	
    private $id;            //unikt id
    private $userName;      //användarnamn
    private $email;         //email-address
    private $password;      //lösenord(hashat)
    private $salt;          //lösenord-salt
    private $dateAdded;     //datum som kontot skapades
    private $admin = false;         //false = vanlig användare, true = admin
    
    public function __construct($id, $userName, $email, $password, $salt, $dateAdded, $admin)
	{
		$this->id = $id;
		$this->userName = $userName;
		$this->email = $email;
		$this->password = $password;
		$this->salt = $salt;
		$this->dateAdded = $dateAdded;
		$this->admin = $admin;
		
	}
    
}