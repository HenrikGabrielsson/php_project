<?php

namespace model;

class User
{
	
    private $id;        //unikt id 
    private $userName;      //användarnamn
    private $email;         //email-address
    private $password;      //lösenord(hashat)
    private $salt;          //lösenord-salt
    private $dateAdded;     //datum som kontot skapades
    private $admin;         //false = vanlig användare, true = admin
    
    //User-konstruktor.
    public function __construct($userName, $email, $password, $salt, $dateAdded, $admin = 0, $id = 0)
	{
		$this->id = $id;
		$this->userName = $userName;
		$this->email = $email;
		$this->password = $password;
		$this->salt = $salt;
		$this->dateAdded = $dateAdded;
		$this->admin = $admin;	
	}
	
	//getters för alla fält
	public function getId()
	{
		return $this->id;
	}
	
	public function getUserName()
	{
		return $this->userName;
	}
	
	public function getEmail()
	{
		return $this->email;
	}
	
	public function getPassword()
	{
		return $this->password;
	}
	
	public function getSalt()
	{
		return $this->salt;
	}
	
	public function getDateAdded()
	{
		return $this->dateAdded;
	}
	
	public function getAdmin()
	{
		return $this->admin;
	}
	
}