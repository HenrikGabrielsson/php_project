<?php

namespace model\repository;

require_once("./model/repo/Repository.php");

class UserRepo extends \model\repository\Repository
{
    private $id;            //unikt id
    private $userName;      //användarnamn
    private $email;         //email-address
    private $password;      //lösenord(hashat)
    private $salt;          //lösenord-salt
    private $dateAdded;     //datum som kontot skapades
    private $admin;         //false = vanlig användare, true = admin
    
    public function __construct()
    {
        
    }
}



