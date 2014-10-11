<?php

namespace view;

class UserView
{
	private $user;

	public function __construct($user)
	{
		$this->user = $user;
	}

	public function getTitle()
	{
		return $this->user->getUserName();
	}

	public function getBody()
	{
		return "just testing";
	}
}