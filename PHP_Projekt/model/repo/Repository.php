<?php

namespace model\repository;

abstract class Repository 
{
    private $userName = "henrikgabrielss";
    private $password = "tCiSetWtDead";
    private $connectionString = "mysql:host=henrikgabrielsson.se.mysql;dbname=henrikgabrielss";
    private $dbConnection;
    

    public function connect()
    {
        //kollar ifall det inte redan finns en anslutning till databasen så skapas den här
        if($this->dbConnection === NULL)
        {
            try
            {
                $this->dbConnection($this->connectionString, $this->userName, $this->password);
            }
            catch(Exception $e)
            {
                throw new Exception("Problems with the database connection.");
            }
        }
    }
    
    
}


