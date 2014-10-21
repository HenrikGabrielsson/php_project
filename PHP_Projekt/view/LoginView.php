<?php

namespace view;

require_once("./view/helpers/PostHandler.php");

class LoginView
{
	private $login;

	public function __construct($login)
	{
		$this->login = $login;
	} 


    /**
    *   @return     Hämta namn från login-fältet
    */
	public function getUsername()
    {
    	return $_POST[helpers\PostHandler::$LOGINNAME];
    }

    /**
    *   @return     Hämta lösenord från login-fältet
    */
    public function getPassword()
    {
    	return $_POST[helpers\PostHandler::$LOGINPASSWORD];
    }

    /**
    *   @return     Hämta ip från användaren
    */
    public function getIP()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    /**
    *   @return     Hämta info om mjukvaran som klienten använder.
    */
    public function getUserAgent()
    {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    /**
    *   @return     kollar om användaren vill logga in
    */
	public function userWantsToLogin()
	{
		return isset($_GET[helpers\GetHandler::$LOGIN]);
	}

    /**
    *   @return     kollar om användaren vill logga ut.
    */
    public function userWantsToLogout()
    {
        return isset($_GET[helpers\GetHandler::$LOGOUT]);
    }

    /**
    *   Denna funktion skapar innehållet i loginrutan som ska visas på alla sidor.
    *
    *   @return     string  inloggningsrutan.
    */
	public function createLoginBox()
    {

    	$loginDiv = '<div id="loginBox">';

        //Namn på den inloggade och utloggningslänk
        if($this->login->getIsLoggedIn($this->getIP(), $this->getUserAgent()))
        {
        	$loginDiv .= 
            $this->makeFeedBack().
            '<p>You are logged in as <a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWUSER.'&'.helpers\GetHandler::$ID.'='.$this->login->getId().'">'.$this->login->getUser().'</a>.</p>
            <p><a href="?'.helpers\GetHandler::$LOGOUT.'">Log out</a></p>
            ';
        }

        //vid utloggad användare: inloggningsformulär
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



    /**
    *   funktion som skapar eventuell feedback efter att ha frågat modellen.
    *   @return     string      html-lista med feedback.
    */
    private function makeFeedBack()
    {

        $feedback = '<div id="feedback">';

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

    	return $feedback . '</div>';
    }




}