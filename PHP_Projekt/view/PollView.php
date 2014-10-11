<?php

namespace view;

require_once("./view/helpers/DiagramMaker.php");
require_once("./view/helpers/GetHandler.php");

class PollView
{
	private $poll;
	private $owner;

	public function __construct($poll, $owner)
	{
		$this->poll = $poll;
		$this->owner = $owner;
	}

	public function getAnswer()
	{
		return $_POST[helpers\PostHandler::getVote()];
	}

	public function getClient()
	{
		return $_SERVER["REMOTE_ADDR"];
	}

	public function userWantsResults()
	{
		return isset($_GET[helpers\GetHandler::getShowResult()]);
	}

	public function userVoted()
	{
		return isset($_POST[helpers\PostHandler::getVote()]);
	}


	public function getTitle()
	{
		return $this->poll->getQuestion();
	}

	public function getResult()
	{

		$answers = $this->poll->getAnswers();

		//varje svar i procent.
		$percentageArr = $this->convertToPercentage($answers);

		//färgerna som används i diagrammet
		$colors = \view\helpers\DiagramMaker::getDiagramColors();

		$resultList = "";
		for($i = 0; $i < count($answers); $i++)
		{
			$answer = $answers[$i];
			$percentage = round(100 * ($percentageArr[$i]),1);


			$resultList .= 
			'<li>
				<div class="answerColor" style="background-color:rgb('.$colors[$i][0].','.$colors[$i][1].','.$colors[$i][2].')" ></div>
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
			
			$this->getTitleAndCreator().
			'<div class="pollResults">
			
				<img class="diagramImage" src="data:image/png;base64,' . base64_encode( $raw) .'">

				<ul class="resultsList">
				'.$resultList.'	
				</ul>

			</div>
			';
	}

	public function getForm()
	{

		//loopar ut alla alternativ som radioknappar
		foreach ($this->poll->getAnswers() as $answer)
		{
			$alternatives .= '<label for="'.$answer->getId().'" >'.$answer->getAnswer().': </label><input type="radio" name="'.helpers\PostHandler::getVote().'" id="'.$answer->getId().'" value="'.$answer->getId().'" />';
		}

		//formulär med radioknappar
		return 
			$this->getTitleAndCreator().
			'<form id="pollForm" action="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::getShowResult().'" method="post">
				'.$alternatives.'
				<input type="submit" value="Vote" id="postPoll" />
			</form>
			<p><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::getShowResult().'">See results</a> without voting.</p>
			';
	}

	public function getTitleAndCreator()
	{
		return 
			'<h1>'.$this->poll->getQuestion().'</h1>
			<p>Created by: <a href="?'.helpers\GetHandler::getView().'='.helpers\GetHandler::getViewUser().
			'&'.helpers\GetHandler::getId().'='.$this->owner->getId().'">'.$this->owner->getUserName().'</a> </p>
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