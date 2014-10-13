<?php

namespace view;

require_once("./view/helpers/DiagramMaker.php");
require_once("./view/helpers/GetHandler.php");

class PollView
{
	private $poll;
	private $owner;
	private $commentHandler;
	private $login;

	public function __construct($poll, $owner, $login, $commentHandler)
	{
		$this->poll = $poll;
		$this->owner = $owner;
		$this->commentHandler = $commentHandler;
		$this->login = $login;
	}

	public function getAnswer()
	{
		return $_POST[helpers\PostHandler::$VOTE];
	}

	public function getComment()
	{
		return $_POST[helpers\PostHandler::$COMMENT];
	}

	public function getClient()
	{
		return $_SERVER["REMOTE_ADDR"];
	}

	public function userWantsResults()
	{
		return isset($_GET[helpers\GetHandler::$SHOWRESULT]);
	}

	public function userVoted()
	{
		return isset($_POST[helpers\PostHandler::$VOTE]);
	}

	public function userCommented()
	{
		return isset($_POST[helpers\PostHandler::$COMMENT]);
	}


	public function getTitle()
	{
		return $this->poll->getQuestion();
	}

	public function getResultPage($feedback = "")
	{    
		return 
			$this->getTitleAndCreator().
			$this->getResults().
			$this->getCommentSection($feedback);
	}

	public function getForm()
	{

		//loopar ut alla alternativ som radioknappar
		foreach ($this->poll->getAnswers() as $answer)
		{
			$alternatives .= '<label for="'.$answer->getId().'" >'.$answer->getAnswer().': </label><input type="radio" name="'.helpers\PostHandler::$VOTE.'" id="'.$answer->getId().'" value="'.$answer->getId().'" />';
		}

		//formulär med radioknappar
		return 
			$this->getTitleAndCreator().
			'<form id="pollForm" action="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$SHOWRESULT.'" method="post">
				'.$alternatives.'
				<input type="submit" value="Vote" id="postPoll" />
			</form>
			<p><a href="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::$SHOWRESULT.'">See results</a> without voting.</p>
			';
	}

	public function getResults()
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

		return
		'<div class="pollResults">
	
		<img class="diagramImage" src="data:image/png;base64,'.$image.'">

		<ul class="resultsList">
		'.$resultList.'	
		</ul>

		</div>';	
	}

	public function getTitleAndCreator()
	{
		return 
			'<h1>'.$this->poll->getQuestion().'</h1>
			<p>Created by: <a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWUSER.
			'&'.helpers\GetHandler::$ID.'='.$this->owner->getId().'">'.$this->owner->getUserName().'</a> </p>
			';	
	}

	public function getCommentSection($feedback)
	{
		if($feedback == null)
		{
			$feedback = "Write a comment. Keep it civil, please.";
		}

		else if(is_array($feedback))
		{
			$feedback = $this->makeFeedback($feedback);
		}

		return 
		'<div id="commentSection" >'.
			$this->getCommentForm().
			$this->getComments().
			
		'</div>
		';
	}

	public function getCommentForm()
	{
		if($this->login->getIsLoggedIn())
		{
			return '
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<textarea maxlength="1000" cols="100" rows="5" name="'.helpers\PostHandler::$COMMENT.'" id="comment">'.$feedback.'
				</textarea>
				<input type="submit" value="Send Comment">
			</form>	
			';
		}
		else
		{
			return 
			'<p>Login to make a comment</p>';
		}
	}

	private function getComments()
	{
		$comments = $this->commentHandler->getCommentsInPoll($this->poll->getId());

		$retHTML = '<div id="comments">';

		foreach ($comments as $comment) 
		{
			$writer = $this->commentHandler->getCommentWriter($comment);

			$retHTML .= 
			'<div class="comment">
				<div class="commentHead">
					<p>'.$comment->getCommentTime().'</p><p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWUSER.
					'&'.helpers\GetHandler::$ID.'='.$comment->getUserId().'">'. $writer->getUserName().'</a>

					<input type="button" value="Report this comment" class="showReportForm" style="display:none">
					<form class="reportForm" method="post" action="'.$_SERVER['REQUEST_URI'].'" id="'.$comment->getId().'" class="reportComment">
						<input type="text" maxlength="200" name="'.helpers\PostHandler::$REPORT.'" placeholder="(optional) Write a comment. Why did you report this?" />
						<input type="submit" value="Report">
					</form>

				</div>
				<div class="commentBody">
					'.$comment->getComment().'
				</div>
			
			</div>
			';
		}

		return $retHTML . '</div>';

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

	public function makeFeedback($feedbackArray)
	{

		$feedback = '';

		if(in_array($this->commentHandler->shortComment, $feedbackArray))
        {
            $feedback .= "You have to write something.\n";
        }	
		if(in_array($this->commentHandler->longComment, $feedbackArray))
        {
            $feedback .= "Your comment was too long. The maximum Length is 1000 characters. \n";
        }      
        if(in_array($this->commentHandler->pollDoesNotExist, $feedbackArray))
        {
            $feedback .= "The poll does not exist.";
        }

        return $feedback;
	}
}

