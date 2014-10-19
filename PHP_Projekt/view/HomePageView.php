<?php

namespace view;

class HomePageView
{

	private $recentPolls;

	public function __construct($recentPolls)
	{
		$this->recentPolls = $recentPolls;
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
			$body .= 
			'<div id="homeAdvert">
				<p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWCREATEPOLL.'">Click here</a> to create a new poll and get the people\'s opinion on something!</p>
			</div>';
		}

		else 
		{
			$body .= 
			'<div id="homeAdvert">
				<p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWREGISTER.'">Click here</a> to register. When you register you can create polls 
				and let people share their opinion in whatever question you wonder about. You can even share the poll on your own blog or website! Awesome, isn\'t it??</p>
			</div>';

		}

		$body .= 
		'<h2>Recent polls</h2>
		<div id="recentPolls">';
		
		foreach($this->recentPolls as $poll)
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