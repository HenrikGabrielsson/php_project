<?php


/*
 * I denna fil finns vissa inställningar som kan ändras beroende på vart appen ska köras.
 * 
 */
class Settings
{
	//The root directory you will be using 
	public static $ROOT = "http://www.henrikgabrielsson.se/project";
	
	//name of the database
	public static $dbUserName = "henrikgabrielss";

	//the database password
	public static $dbPassword = "";

	public static $dbName = "henrikgabrielss";
	
	//finish the conntection String
	public static $connectionString = "mysql:host=henrikgabrielsson.se.mysql;dbname=henrikgabrielss";
}
