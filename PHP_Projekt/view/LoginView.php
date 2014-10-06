<?php

namespace view;

require_once("./view/helpers/GetHandler.php");
require_once("./view/helpers/PostHandler.php");
require_once("./model/Login.php");

class LoginView
{

	private $login;

	public function __construct()
	{
		$this->login = new \model\Login();
	} 

	public function getFeedback()
	{
		return null;
	}

	public function getUsername()
    {
    	return $_POST[helpers\PostHandler::getLoginName()];
    }

    public function getPassword()
    {
    	return $_POST[helpers\PostHandler::getLoginPassword()];
    }

	public function userWantsToLogin()
	{
		return isset($_GET[helpers\GetHandler::getLogin()]);
	}

    public function userWantsToLogout()
    {
        return isset($_GET[helpers\GetHandler::getLogout()]);
    }


	public function createLoginBox()
    {
    	$loginDiv = '<div id="loginBox">';

        if(\model\Login::isLoggedIn())
        {
        	$loginDiv .= 
            '<p>You are logged in as '.$this->login->getLoggedInUser().'.</p>
            <p><a href="?'.helpers\GetHandler::getLogout().'">Log out</a></p>
            ';
        }
        else
        {
        	$loginDiv .= 
            '<form id="loginForm" method="post" action="?login">
            <label for="loginName"><input type="text" name="'.helpers\PostHandler::getLoginName().'" id="loginName" placeholder="Username" />
            <label for="loginPassword"><input type="password" name="'.helpers\PostHandler::getLoginPassword().'" id="loginPassword" placeholder="Password" />
            <input type="submit" value="Logga in" />

            </form>';
        }

        return $loginDiv .'</div>';
    }   



    //funktion som skapar eventuell feedback efter att ha frågat modellen.
    private function makeFeedBack()
    {
    	return null;
    }




}