<?php

namespace view;

class RegistrationView
{
	/**
	*	@return 	bool 	om användaren vill registrera sig.
	*/
	public function userWantsToRegister()
	{
		return isset($_GET[helpers\GetHandler::$REGISTER]);
	}

	/**
	*	@return 	string 	namn som användaren vill registrera sig med
	*/
	public function getUsername()
	{
		return $_POST[helpers\PostHandler::$REGUSERNAME];
	}

	/**
	*	@return 	string 	email som användaren vill registrera sig med
	*/
	public function getEmail()
	{
		return $_POST[helpers\PostHandler::$REGEMAIL];
	}

	/**
	*	@return 	string 	lösenord som användaren vill registrera sig med
	*/
	public function getPassword1()
	{
		return $_POST[helpers\PostHandler::$REGPASSWORD1];
	}

	/**
	*	@return 	string 	lösenord igen som användaren vill registrera sig med
	*/
	public function getPassword2()
	{
		return $_POST[helpers\PostHandler::$REGPASSWORD2];
	}

	/**
	*	@return 	string 	innehåll om det lyckas.
	*/
	public function getSuccessPage()
	{
		$body = 
		'<h1>Registration done</h1>
		<p>Congratulations! You have successfully registered. Login and start creating polls. </p>
		';

		return $body;
	}

	/**
	*	@return 	string 	html. sätter samman feedback och formulär.
	*/
	public function getRegister($feedback)
	{
		$body = 
		'<h1>Registration</h1>

		'.$this->makeFeedback($feedback)
		.$this->getForm();
		
		return $body;
	}


	/**
	*	Hämtar formuläret.
	*	@return string 	formuläret.
	*/
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


	/**
	*	@return string 	sidans title.
	*/
	public function getTitle()
	{
		return "Registration";
	}

	/**
	*	@param 	array 	array med konstanter som berättar vilken typ av feeback användaren bör få.
	*	@return string 	html-lista med feedback.
	*/
	public function makeFeedback($feedbackArray)
	{

		$feedback = 
		'<div id="feedback">
		<ol>';

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

        return $feedback . '</ol></div>';

	}
}