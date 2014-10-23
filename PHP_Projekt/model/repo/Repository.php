<?php

namespace model\repository;

require_once("Settings.php");

abstract class Repository 
{
    protected $dbConnection; 

    //tabellnamn
    protected $pollTable = "poll";
    protected $answerTable = "answer";
    protected $voteTable = "vote";
    protected $categoryTable = "category";
    protected $commentTable = "comment";    
    protected $reportTable = "report";
    protected $userTable = "user";
        
    public function connect()
    {
        //kollar ifall det inte redan finns en anslutning till databasen så skapas den här
        if($this->dbConnection === NULL)
        {
            try
            {
                $this->dbConnection  = new \PDO(\Settings::$connectionString, \Settings::$dbUserName,\Settings::$dbPassword);
            }
            catch(Exception $e)
            {
                throw new Exception("Problems with the database connection.");
            }
        }
    }
}


