<?php

namespace view;

require_once("./model/Registration.php");
require_once("./view/helpers/GetHandler.php");

class RegistrationView
{
	private $registration; 

	public function __construct()
	{
		$this->registration = new \model\Registration();
	}

	public function userWantsToRegister()
	{
		return isset($_GET[helpers\GetHandler::getRegister()]);
	}

	public function getForm()
	{
		$body = 
		'<h2>Registration</h2>
		<form id="registrationForm" method="post" action="?'.helpers\GetHandler::getView().'='.helpers\GetHandler::getRegister().'&'.helpers\GetHandler::getRegister().'">

			<label for="regUserName">Username:</label>
				<input type="text" name="regUserName" id="regUserName" placeholder="Username" />

			<label for="regEmail">Email:</label>
				<input type="text" name="regEmail" id="regEmail" placeholder="Email" />

			<label for="regPassword1">Password:</label>
				<input type="password" name="regPassword1" id="regPassword1" placeholder="Password" />

			<label for="regPassword2">Password again:</label>
				<input type="password" name="regPassword2" id="regPassword2" placeholder="Password again" />

			<input type="submit" value="Registrera">
		</form>
		';


		return $body;
	}

	public function getTitle()
	{
		return "Registration";
	}
}