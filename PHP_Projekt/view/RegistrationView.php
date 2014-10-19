<?php

namespace view;

require_once("./view/helpers/GetHandler.php");

class RegistrationView
{
	public function userWantsToRegister()
	{
		return isset($_GET[helpers\GetHandler::$REGISTER]);
	}

	public function getUsername()
	{
		return $_POST[helpers\PostHandler::$REGUSERNAME];
	}

	public function getEmail()
	{
		return $_POST[helpers\PostHandler::$REGEMAIL];
	}

	public function getPassword1()
	{
		return $_POST[helpers\PostHandler::$REGPASSWORD1];
	}

	public function getPassword2()
	{
		return $_POST[helpers\PostHandler::$REGPASSWORD2];
	}

	public function getSuccessPage()
	{
		$body = 
		'<h1>Registration done</h1>
		<p>Congratulations! You have successfully registered. Login and start creating polls. </p>
		';

		return $body;
	}

	public function getRegister($feedback)
	{

		$body = 
		'<h1>Registration</h1>

		'.$this->makeFeedback($feedback)
		.$this->getForm();
		
		return $body;
	}

	public function getForm()
	{
		return 
		'<form id="registrationForm" method="post" action="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$REGISTER.'&'.helpers\GetHandler::$REGISTER.'">

			<label for="regUserName">Username:</label>
				<input type="text" name="'.helpers\PostHandler::$REGUSERNAME.'" id="regUserName"  placeholder="Username" />

			<label for="regEmail">Email:</label>
				<input type="text" name="'.helpers\PostHandler::$REGEMAIL.'" id="regEmail" placeholder="Email" />

			<label for="regPassword1">Password:</label>
				<input type="password" name="'.helpers\PostHandler::$REGPASSWORD1.'" id="regPassword1" placeholder="Password" />

			<label for="regPassword2">Password again:</label>
				<input type="password" name="'.helpers\PostHandler::$REGPASSWORD2.'" id="regPassword2" placeholder="Password again" />

			<input type="submit" value="Register">
		</form>
		';		
	}

	public function getTitle()
	{
		return "Registration";
	}

	public function makeFeedback($feedbackArray)
	{

		$feedback .= '<ol>';

		//namn-feedback
		if(in_array(\model\LoginHandler::SHORTNAME, $feedbackArray))
        {
            $feedback .= "<li>Your username needs to be at least 3 characters long.</li>";
        }	
		if(in_array(\model\LoginHandler::LONGNAME, $feedbackArray))
        {
            $feedback .= "<li>Your username can be at most 25 characters long.</li>";
        }      
        if(in_array(\model\LoginHandler::ILLEGALCHARS, $feedbackArray))
        {
            $feedback .= "<li>Your username contained illegal tags. Don't use html-tags in your name.</li>";
        }
        if(in_array(\model\LoginHandler::NAMEALREADYINUSE, $feedbackArray))
        {
            $feedback .= "<li>Your name is already taken.</li>";
        }

        //lösenordsfeedback
        if(in_array(\model\LoginHandler::SHORTPASSWORD, $feedbackArray))
        {
            $feedback .= "<li>Your password must be at least 6 characters long.</li>";
        }
        if(in_array(\model\LoginHandler::NOMATCHPASSWORDS, $feedbackArray))
        {
            $feedback .= "<li>Your passwords didn't match.</li>";
        }

  		//email-feedback
        if(in_array(\model\LoginHandler::NOVALIDEMAIL, $feedbackArray))
        {
            $feedback .= "<li>You must write a real email address.</li>";
        }
        if(in_array(\model\LoginHandler::EMAILALREADYINUSE, $feedbackArray))
        {
            $feedback .= "<li>A user account is already tied to this email address.</li>";
        }  		

        return $feedback . '</ol>';

	}
}