<?php

namespace view;

require_once("./view/helpers/DiagramMaker.php");

class PollView
{
	private $poll;
	private $scriptPath = "script/pollScript.js";

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

		//om användaren vill se resultatet
		if(isset($_GET["showResult"]))
		{
			$retString .= $this->getResult();
		}

		//annars visas formuläret där man kan svara på frågan som ställs.
		else
		{
			$retString .= $this->getForm();
		}
		return $retString;
	}

	private function getResult()
	{

		$answers = $this->poll->getAnswers();

		//varje svar i procent.
		$percentageArr = $this->convertToPercentage($answers);

		$resultList = "";
		for($i = 0; $i < count($answers); $i++)
		{
			$answer = $answers[$i];
			$percentage = round(100 * ($percentageArr[$i]),1);

			$resultList .= 
			'<li>
				'.$answer->getAnswer().'
				'.$percentage.'  %
			</li>';
		}

		//rita ett cirkeldiagram som är 200 X 200 px
		$image = \view\helpers\DiagramMaker::drawCircleDiagram($percentageArr, 200, 200);
        
        ob_start();
        imagepng($image);
        $raw = ob_get_clean();

		return 
			'
			<div id="pollResults">
			
				<img src="data:image/png;base64,' . base64_encode( $raw) .'">

				<div id="diagramExplanation">
				<ul id="resultsList">
				'.$resultList.'	
				</ul>
				</div>

			</div>
			';
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

	private function convertToPercentage($answers)
	{

		$retArr = array();
		$totalNumVotes = 0;

		//totala summan räknas ut.
		foreach($answers as $answer)
		{
			$totalNumVotes = $totalNumVotes + $answer->getCount(); 
		}

		//om ingen har röstat än så returneras false
		if($totalNumVotes === 0)
		{
			return false;
		}

		//räknar ut hur många procent varje del tar upp av den totala summan. 
		foreach($answers as $answer) 
		{
			$retArr[] = $answer->getCount()/$totalNumVotes;
		}

		return $retArr;

	}


}