<?php

namespace view;

require_once("./view/helpers/GetHandler.php");

class LoginView
{
	public function getFeedback()
	{
		return null;
	}

	public function userWantsToLogin()
	{
		return helpers\GetHandler::isParameterSet(helpers\GetHandler::getId());
	}
}