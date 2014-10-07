<?php

namespace model;

require_once("./model/repo/UserRepo.php");

class Registration
{
	private $repo;

	public function __construct()
	{
		$this->repo = new \model\repository\UserRepo();
	}

	public function attemptRegister($username, $email, $password1, $password2)
	{
		echo "model works";
	}
}