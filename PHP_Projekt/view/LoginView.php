<?php

namespace view;

class LoginView
{

	private $login;

	public function __construct($login)
	{
		$this->login = $login;
	} 

	public function getUsername()
    {
    	return $_POST[helpers\PostHandler::$LOGINNAME];
    }

    public function getPassword()
    {
    	return $_POST[helpers\PostHandler::$LOGINPASSWORD];
    }

    public function getIP()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    public function getUserAgent()
    {
        return $_SERVER["HTTP_USER_AGENT"];
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

        if($this->login->getIsLoggedIn($this->getIP(), $this->getUserAgent()))
        {
        	$loginDiv .= 
            $this->makeFeedBack().
            '<p>You are logged in as '.$this->login->getUser().'.</p>
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
            <input type="submit" value="Log in" />

            </form>
            <p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$REGISTER.'">Register</a></p>';
        }

        return $loginDiv .'</div>';
    }   



    //funktion som skapar eventuell feedback efter att ha frågat modellen.
    private function makeFeedBack()
    {

        $feedback = "";

        if(in_array(\model\LoginHandler::NONAME, $this->login->getErrorList()))
        {
            $feedback .= "<p>Please, fill out your username.</p>";
        }

        if(in_array(\model\LoginHandler::NOPASSWORD, $this->login->getErrorList()))
        {
            $feedback .= "<p>Please, fill out your password.</p>";
        }       

        else if(in_array(\model\LoginHandler::WRONGCREDENTIALS, $this->login->getErrorList()))
        {
            $feedback .= "<p>Your username and/or Password was incorrect. Please, try again.</p>";
        }

    	return $feedback;
    }




}