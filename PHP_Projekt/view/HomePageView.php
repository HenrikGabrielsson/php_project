<?php

namespace view;

class HomePageView
{

	private $recentPolls;

	public function __construct($recentPolls)
	{
		$this->recentPolls = $recentPolls;
	}

	/**
	*	Hämta sidans title
	* 	
	* 	@return   String 	det som ska stå i title
	*/
	public function getTitle()
	{
		return "Create polls, vote and share your opinion";
	}

	/**
	*	Hämta sidans innehåll
	* 	
	* 	@return   String 	det som ska finnas på sidans main_content
	*/
	public function getBody($loggedIn)
	{
		$body = 
		'<h1>Welcome to PHP Polls</h1>
		<div id="homeAdvert">';

		//länk till "Create poll" om man är inloggad
		if($loggedIn)
		{
			$body .= 
			'<p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWCREATEPOLL.'">Click here</a> to create a new poll and get the people\'s opinion on something!</p>';
		}

		//länk till "Register" om man inte är inloggad.
		else 
		{
			$body .= 
			'<p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWREGISTER.'">
			Click here</a> to register. When you register you can create polls and let people share their opinion in whatever question you wonder about. 
			You can even share the poll on your own blog or website! Awesome, isn\'t it??</p>';
		}

		//avslutar div ovan och skriver ut en lista på de senaste undersökningarna som gjordes.
		$body .= 
		'</div>

		<h2>Recent polls</h2>
		<div id="recentPolls">';
		
		if($this->recentPolls)
		{
			foreach($this->recentPolls as $poll)
			{
				$body .= 
				'<li>
					<a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWPOLL.'&'.helpers\GetHandler::$ID.'='.$poll->getId().'">
					'.$poll->getQuestion().'</a>
				</li>';			
			}
			$body .= "</div>";
		}
					
		return $body;

		
	}
}

