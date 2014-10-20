<?php

namespace view;

require_once("./view/helpers/DiagramMaker.php");

class PollView
{
	private $poll;
	private $owner;
	private $commentHandler;
	private $login;

	public function __construct($poll, $owner, $login, $commentHandler, $reportHandler)
	{
		$this->poll = $poll;
		$this->owner = $owner;
		$this->commentHandler = $commentHandler;
		$this->reportHandler = $reportHandler;
		$this->login = $login;
	}

	public function denyTitle()
	{
		return "Access Denied";
	}

	public function denyPage()
	{
		return 
		'
		<h1>Access Denied</h1>
		<p>Sorry! You\'re not allowed to see this page.</p>
		';
	}

	public function getAnswer()
	{
		return $_GET[helpers\GetHandler::$VOTE];
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
		return isset($_GET[helpers\GetHandler::$VOTE]);
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

	public function userReportedPoll()
	{
		return isset($_POST[helpers\PostHandler::$POLLREPORT_REASON]);
	}

	public function getPollReportReason()
	{
		return $_POST[helpers\PostHandler::$POLLREPORT_REASON];
	}

	public function getTitle()
	{
		return $this->poll->getQuestion();
	}

	public function getResultPage($feedback = null)
	{    
		return 
			$this->getTitleAndCreator().
			$this->getResults().
			$this->makeFeedback($feedback).
			$this->getCommentSection();
	}

	public function getForm($ignoreShare = false)
	{
		//loopar ut alla alternativ som radioknappar
		foreach ($this->poll->getAnswers() as $answer)
		{
			$alternatives .= '<label for="'.$answer->getId().'" >'.$answer->getAnswer().': </label><input type="radio" name="'.helpers\GetHandler::$VOTE.'" id="'.$answer->getId().'" value="'.$answer->getId().'" />';
		}

		$share = "";
		if($ignoreShare == false)	
		{
			$share = $this->getShare();
		}

		//formulär med radioknappar
		return 
			$this->getTitleAndCreator().
			$share.
			'<form id="pollForm" action="'.\Settings::$ROOT.'" method="get">
				'.$alternatives.'
				<input type="hidden" name="'.helpers\GetHandler::$VIEW.'" value="'.helpers\GetHandler::$VIEWPOLL.'" />
				<input type="hidden" name="'.helpers\GetHandler::$ID.'" value="'.$this->poll->getId().'" />
				<input type="hidden" name="'.helpers\GetHandler::$SHOWRESULT.'" />
				<input type="submit" value="Vote" id="postPoll" />
			</form>
			<p><a href="'.\Settings::$ROOT .'?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWPOLL.'&'.helpers\GetHandler::$ID.'='.$this->poll->getId().'&'.helpers\GetHandler::$SHOWRESULT.'">See results</a> without voting.</p>
			';
	}

	private function getShare()
	{
		$shareCode = 
		'<p>Share this poll on your own website:</p>
		<input type="button" value="Share this poll" id="showShareCodeButton" style="display:none">
		<textarea id="shareCodeArea">'.
		htmlspecialchars($this->getForm(true)).
		'</textarea>';

		return $shareCode;
	}

	public function getResults()
	{
		$answers = $this->poll->getAnswers();
		
		//varje svar i procent.
		$percentageArr = $this->convertToPercentage($answers);

		//färgerna som används i diagrammet
		$colors = \view\helpers\DiagramMaker::getDiagramColors();

		//om inga röster har gjorts i undersökningen så ritas diagrammet
		if($percentageArr)
		{
			//rita ett cirkeldiagram som är 200 X 200 px
			$image = \view\helpers\DiagramMaker::drawCircleDiagram($percentageArr, 200, 200);
		}
		else
		{
			$image = "No votes yet.";
		}

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

		return
		'<div class="pollResults">'.
	
		$image.
		$this->getReportPoll().

		'<ul class="resultsList">'.
		$resultList.'	
		</ul>
		</div>';	
	}

	public function getReportPoll()
	{
		$reportForm = "";

		//man ska ha möjlighet att rapportera kränkande polls till admins om man är inloggad.
		if($this->login->getIsLoggedIn())
		{
			$reportForm .= 
			'<input type="button" value="Report this poll" class="showPollReportForm" style="display:none">
			<form method="post" action="'.$_SERVER['REQUEST_URI'].'" class="reportPoll">
				<input type="text" maxlength="200" name="'.helpers\PostHandler::$POLLREPORT_REASON.'" placeholder="(optional) Write a comment. Why did you report this?" />
				<input type="submit" value="Report">
			</form>		
			';		
		}

		return $reportForm;
	}

	public function getTitleAndCreator()
	{

		$pollheader =  
			'<h1>'.$this->poll->getQuestion().'</h1>
			<p>Created by: <a href="'.\Settings::$ROOT.'?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWUSER.
			'&'.helpers\GetHandler::$ID.'='.$this->owner->getId().'">'.$this->owner->getUserName().'</a></p>
			';	


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
			<div id="commentForm">
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<textarea maxlength="1000" cols="100" rows="5" name="'.helpers\PostHandler::$COMMENT.'" id="comment">'.$feedback.'
				</textarea>
				<input type="submit" value="Send Comment">
			</form>	
			</div>
			';
		}
		else
		{
			return 
			'<div id="commentForm">
			<p>Login to make a comment</p>
			</div>';
		}
	}

	private function getComments()
	{
		$comments = $this->commentHandler->getCommentsInPoll($this->poll->getId());

		$retHTML = '<div id="comments">';

		if($comments)
		{
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
		}

		return $retHTML . '</div>';
	}

	public function privateVoteTitle()
	{
		return "Thanks for the vote";
	}

	public function privateVotePage()
	{
		return 
		'<h1>Thanks for the vote!</h1>
		<p>This poll is private so we can not show you the result. But thanks for the vote. 
			Maybe if you ask the owner nicely, he will show you the results. </p>
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

		if($totalNumVotes == 0)
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
			if(in_array(\model\ReportHandler::LONGREASON, $feedback))
	        {
	            $retString .= "<li>The reason you wrote was too long. Maximum number of characters is 200.</li>";
	        }
	     	if(in_array(\model\ReportHandler::NOCOMMENT, $feedback))
	        {
	            $retString .= "<li>This comment doesn't exist.</li>";
	        }
	       	if(in_array(\model\ReportHandler::NOPOLL, $feedback))
	        {
	            $retString .= "<li>This poll doesn't exist.</li>";
	        }
			if(in_array(\model\CommentHandler::SHORTCOMMENT, $feedback))
	        {
	            $retString .= "<li>You have to write something.</li>";
	        }	
			if(in_array(\model\CommentHandler::LONGCOMMENT, $feedback))
	        {
	            $retString .= "<li>Your comment was too long. The maximum Length is 1000 characters. </li>";
	        }      
	        if(in_array(\model\CommentHandler::POLLDOESNOTEXIST, $feedback))
	        {
	            $retString .= "<li>The poll does not exist.</li>";
	        }
	        if(in_array(\model\CommentHandler::COMMENTSAVED, $feedback))
	        {
	            $retString .= "<li>Thanks for the comment.</li>";
	        }	        
   
	        //rättmeddelanden
	        if(in_array(\model\ReportHandler::REPORTEDPOLL, $feedback) || in_array(\model\ReportHandler::REPORTEDCOMMENT, $feedback))
	        {
	            $retString .= "<li>Thanks for reporting this. We will take a look at it.</li>";
	        }	     

	        $retString .="</ul>";
	    }

	    else
	    {
	    	$retString .= '<p>'.$feedback.'</p>';

	    	//om användaren har röstat
	    	if($this->userVoted())
	        {
	        	$retString .= "<p>Thanks for the vote.</p>";
	        }
	    }

        return $retString . '</div>';
	}
}

