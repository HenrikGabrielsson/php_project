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

	public function userReportedComment()
	{
		return isset($_POST[helpers\PostHandler::$COMMENTREPORT_ID]);
	}

	public function getCommentReportId()
	{
		return $_POST[helpers\PostHandler::$COMMENTREPORT_ID];
	}

	public function getCommentReportReason()
	{
		return $_POST[helpers\PostHandler::$COMMENTREPORT_REASON];
	}

	public function getPollReportId()
	{
		return $_POST[helpers\PostHandler::$POLLREPORT_ID];
	}

	public function getPollReportReason()
	{
		return $_POST[helpers\PostHandler::$POLLREPORT_REASON];
	}

	public function userReportedPoll()
	{
		return isset($_POST[helpers\PostHandler::$POLLREPORT_ID]);
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
			$this->makeFeedback($feedback).
			$this->getCommentSection();
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

		$pollheader =  
			'<h1>'.$this->poll->getQuestion().'</h1>
			<p>Created by: <a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWUSER.
			'&'.helpers\GetHandler::$ID.'='.$this->owner->getId().'">'.$this->owner->getUserName().'</a></p>
			';	

			//man ska ha möjlighet att rapportera kränkande polls till admins om man är inloggad.
			if($this->login->getIsLoggedIn())
			{
				$pollheader .= 
				'<input type="button" value="Report this poll" class="showPollReportForm" style="display:none">
				<form method="post" action="'.$_SERVER['REQUEST_URI'].'" class="reportPoll">
					<input type="hidden" name="'.helpers\PostHandler::$POLLREPORT_ID.'" value="'.$this->poll->getId().'">
					<input type="text" maxlength="200" name="'.helpers\PostHandler::$POLLREPORT_REASON.'" placeholder="(optional) Write a comment. Why did you report this?" />
					<input type="submit" value="Report">
				</form>		
				';		
			}

			return $pollheader;

	}

	public function getCommentSection()
	{
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
					'&'.helpers\GetHandler::$ID.'='.$comment->getUserId().'">'. $writer->getUserName().'</a>';


			if($this->login->getIsLoggedIn())
			{
				$retHTML .= 
				'
					<input type="button" value="Report this comment" class="showCommentReportForm" style="display:none">
					<form method="post" action="'.$_SERVER['REQUEST_URI'].'" class="reportComment">
						<input type="hidden" name="'.helpers\PostHandler::$COMMENTREPORT_ID.'" value="'.$comment->getId().'">
						<input type="text" maxlength="200" name="'.helpers\PostHandler::$COMMENTREPORT_REASON.'" placeholder="(optional) Write a comment. Why did you report this?" />
						<input type="submit" value="Report">
					</form>
				';
			}

			$retHTML .=
			'</div>
				<div class="commentBody">
					'.$comment->getComment().'
				</div>
			</div>';
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

	public function makeFeedback($feedback)
	{

		$retString = '<div id="feedback">';  

		if(is_array($feedback))
		{
			
			$retString = "<ul>";
			if(in_array($this->commentHandler->shortComment, $feedback))
	        {
	            $retString .= "<li>You have to write something.</li>";
	        }	
			if(in_array($this->commentHandler->longComment, $feedback))
	        {
	            $retString .= "<li>Your comment was too long. The maximum Length is 1000 characters. </li>";
	        }      
	        if(in_array($this->commentHandler->pollDoesNotExist, $feedback))
	        {
	            $retString .= "<li>The poll does not exist.</li>";
	        }
	        $retString .="</ul>";
	    }

	    else
	    {
	    	$retString = '<p>'.$feedback.'</p>';
	    }

        return $retString . "</div>";
	}
}

