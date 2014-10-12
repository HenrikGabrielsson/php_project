<?php

namespace view;

require_once("./view/RegistrationView.php");
require_once("./view/PollCreationView.php");
require_once("./model/PollCreator.php");

class HomePageView
{

	private $regView;
	private $createView;
	private $pollCreator;
	private $pollRepo;

	public function __construct($pollRepo)
	{
		$this->pollCreator = new \model\PollCreator();
		$this->pollRepo = $pollRepo;

		$this->createView = new PollCreationView($this->pollCreator);
		$this->regView = new RegistrationView();
	}

	public function getTitle()
	{
		return "Create polls, vote and share your opinion";
	}

	public function getBody($loggedIn)
	{
		$body = '<h1>Welcome to PHP Polls</h1>';

		if($loggedIn)
		{
			$body .= $this->createView->getForm($this->pollCreator);
		}

		else 
		{
			$body .= $this->regView->getForm();

		}

		$body .= 
		'<h2>Recent polls</h2>
		<div id="recentPolls">';
		$recentPolls = $this->pollRepo->getLatestPolls(3);
		
		foreach($recentPolls as $poll)
		{
			$body .= 
			'<li>
				<a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWPOLL.'&'.helpers\GetHandler::$ID.'='.$poll->getId().'">
				'.$poll->getQuestion().'</a>
			</li>';			
		}
		$body .= "</div>";
					
		return $body;

		
	}
}