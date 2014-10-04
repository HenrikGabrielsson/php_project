<?php

namespace view;

class PollView
{
	private $poll;

	public function __construct($poll)
	{
		$this->poll = $poll;
	}

	public function getTitle()
	{
		return $this->poll->getQuestion();
	}

	public function getBody()
	{
		$retString = "<h2>".$this->poll->getQuestion()."</h2>";

		if(isset($_GET["showResults"]))
		{
			$retString .= $this->getResult();
		}
		else
		{
			$retString .= $this->getForm();
		}
		return $retString;
	}

	private function getResult()
	{

	}

	private function getForm()
	{

		//loopar ut alla alternativ som radioknappar
		foreach ($this->poll->getAnswers() as $answer)
		{
			$alternatives .= '<label for="'.$answer->getId().'" >'.$answer->getAnswer().': </label><input type="radio" id="'.$answer->getId().'" value="'.$answer->getId().'" />';
		}

		//formulär med radioknappar
		return 
		'
		<form id="pollForm" action="?voted" method="POST">
			'.$alternatives.'
			<input type="submit" value="Rösta" id="postPoll" />
		</form>
		';
	}
}