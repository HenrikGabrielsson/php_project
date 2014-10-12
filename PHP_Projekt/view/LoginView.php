<?php

namespace view;

require_once("./view/helpers/GetHandler.php");
require_once("./view/helpers/PostHandler.php");
require_once("./model/Login.php");

class LoginView
{

	private $login;

	public function __construct($login)
	{
		$this->login = $login;
	} 

	public function getFeedback()
	{
		return null;
	}

	public function getUsername()
    {
    	return $_POST[helpers\PostHandler::$LOGINNAME];
    }

    public function getPassword()
    {
    	return $_POST[helpers\PostHandler::$LOGINPASSWORD];
    }

	public function userWantsToLogin()
	{
		return isset($_GET[helpers\GetHandler::$LOGIN]);
	}

    public function userWantsToLogout()
    {
        return isset($_GET[helpers\GetHandler::$LOGOUT]);
    }


	public function createLoginBox()
    {

    	$loginDiv = '<div id="loginBox">';

        if(\model\Login::isLoggedIn())
        {
        	$loginDiv .= 
            $this->makeFeedBack().
            '<p>You are logged in as '.$this->login->getLoggedInUser().'.</p>
            <p><a href="?'.helpers\GetHandler::$LOGOUT.'">Log out</a></p>
            ';
        }
        else
        {
        	$loginDiv .= 
            $this->makeFeedBack().
            '<form id="loginForm" method="post" action="?login">
            <label for="loginName">Username:</label><input type="text" name="'.helpers\PostHandler::$LOGINNAME.'" id="loginName" placeholder="Username" />
            <label for="loginPassword">Password:</label><input type="password" name="'.helpers\PostHandler::$LOGINPASSWORD.'" id="loginPassword" placeholder="Password" />
            <input type="submit" value="Logga in" />

            </form>
            <p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$REGISTER.'">Register</a></p>';
        }

        return $loginDiv .'</div>';
    }   



    //funktion som skapar eventuell feedback efter att ha frågat modellen.
    private function makeFeedBack()
    {

        $feedback = "";

        if(in_array($this->login->noNameError, $this->login->getErrorList()))
        {
            $feedback .= "<p>Please, fill out your username.</p>";
        }

        if(in_array($this->login->noPasswordError, $this->login->getErrorList()))
        {
            $feedback .= "<p>Please, fill out your password.</p>";
        }       

        else if(in_array($this->login->wrongCredentialsError, $this->login->getErrorList()))
        {
            $feedback .= "<p>Your username and/or Password was incorrect. Please, try again.</p>";
        }

    	return $feedback;
    }




}