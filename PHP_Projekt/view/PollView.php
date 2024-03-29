<?php

namespace view;

require_once("./view/helpers/DiagramMaker.php");

class PollView
{
	private $poll;
	private $owner;
	private $commentHandler;
	private $login;

	public function __construct($poll, $login, $owner, $commentHandler, $reportHandler)
	{
		$this->poll = $poll;
		$this->owner = $owner;
		$this->commentHandler = $commentHandler;
		$this->reportHandler = $reportHandler;
		$this->login = $login;
	}

	/**
	*	@return 	int 	id på det som användaren röstade på 
	*/
	public function getAnswer()
	{
		return $_GET[helpers\GetHandler::$VOTE];
	}


	/**
	*	@return 	string 	hämtar kommentar som användaren gjorde
	*/
	public function getComment()
	{
		return $_POST[helpers\PostHandler::$COMMENT];
	}

	/**
	*	@return 	string 	hämtar ip
	*/
	public function getClient()
	{
		return $_SERVER["REMOTE_ADDR"];
	}

	/**
	*	@return 	string 	title på sidan när användaren inte får komma åt innehållet
	*/
	public function userWantsResults()
	{
		return isset($_GET[helpers\GetHandler::$SHOWRESULT]);
	}

	/**
	*	@return 	bool 	om användaren har röstat
	*/
	public function userVoted()
	{
		return isset($_GET[helpers\GetHandler::$VOTE]);
	}


	/**
	*	@return 	bool 	om användaren har kommenterat
	*/
	public function userCommented()
	{
		return isset($_POST[helpers\PostHandler::$COMMENT]);
	}

	/**
	*	@return 	bool 	om användaren har rapporterat kommentar
	*/
	public function userReportedComment()
	{
		return isset($_POST[helpers\PostHandler::$COMMENTREPORT_ID]);
	}

	/**
	*	@return 	int 	hämtar id på rapporterad kommentar
	*/
	public function getCommentReportId()
	{
		return $_POST[helpers\PostHandler::$COMMENTREPORT_ID];
	}


	/**
	*	@return 	string 	om användaren har lämnat en anledning till rapporterad kommentar
	*/
	public function getCommentReportReason()
	{
		return $_POST[helpers\PostHandler::$COMMENTREPORT_REASON];
	}

	/**
	*	@return 	bool 	om användaren har lämnat en anledning till rapporterad poll
	*/
	public function userReportedPoll()
	{
		return isset($_POST[helpers\PostHandler::$POLLREPORT_REASON]);
	}

	/**
	*	@return 	string 	användarens anledning till rapporterad poll
	*/
	public function getPollReportReason()
	{
		return $_POST[helpers\PostHandler::$POLLREPORT_REASON];
	}

	/**
	*	@return 	string 	sidans titel.
	*/	
	public function getTitle()
	{
		return $this->poll->getQuestion();
	}

	/**
	*	denna lilla funktion hämtar in allt som ska ingå i resultatsidan för en undersökning. 
	*	@return 	string 	sidans innehåll sammansatt.
	*/
	public function getResultPage($feedback = null)
	{    
		return 
			$this->getTitleAndCreator().
			$this->getResults().
			$this->makeFeedback($feedback).
			$this->getCommentSection();
	}


	/**
	*	hämtar in formulär där anädnaren kan rösta i en poll
	* 	@param 		bool 	om delaknappen inte ska visas. default= false (knappen visas)
	*	@return 	string 	html-form
	*/
	public function getForm($ignoreShare = false)
	{
		//loopar ut alla alternativ som radioknappar
		foreach ($this->poll->getAnswers() as $answer)
		{
			$alternatives .= '<label for="'.$answer->getId().'" >'.$answer->getAnswer().': </label><input type="radio" name="'.helpers\GetHandler::$VOTE.'" id="'.$answer->getId().'" value="'.$answer->getId().'" />';
		}

		//hämta delen där användaren kan kopiera kod för att visa undersökningen på sin egen sida.
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

	/**
	*	@return 	string 	returnerar ett textfält och en knapp för om användaren vill dela undersökningen.
	*/
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

	/**
	*	Här skapas resultatet för rösterna i undersökningen.
	*	@return 	string 	html för den del som ska visa resultatet.
	*/
	public function getResults()
	{
		$answers = $this->poll->getAnswers();
		
		//varje svar i procent.
		$percentageArr = $this->convertToPercentage($answers);

		//färgerna som används i diagrammet
		$colors = \view\helpers\DiagramMaker::getDiagramColors();

		//om röster har gjorts i undersökningen så ritas diagrammet
		if($percentageArr)
		{
			//rita ett cirkeldiagram som är 200 X 200 px
			$image = \view\helpers\DiagramMaker::drawCircleDiagram($percentageArr, 200, 200);
		}
		else
		{
			$image = "No votes yet.";
		}

		//skriver ut förklaringen till diagrammet, med färg, procent, och svar.
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

	/**
	*	Formulär där man kan rapportera poll.
	* 	@return string 	html-form för att rapportera poll.
	*/
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

	/**
	*	@return 	string 	titel på undersökning och länk till skaparens användarsida
	*/
	public function getTitleAndCreator()
	{

		$pollheader =  
			'<h1>'.$this->poll->getQuestion().'</h1>
			<p>Created by: <a href="'.\Settings::$ROOT.'?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWUSER.
			'&'.helpers\GetHandler::$ID.'='.$this->owner->getId().'">'.$this->owner->getUserName().'</a></p>
			';	

		return $pollheader;
	}

	/**
	*	@return 	string 	hämtar delarna där man kan kommentera och läsa kommentarer från andra.
	*/
	public function getCommentSection()
	{
		return 
		'<div id="commentSection" >'.
			$this->getCommentForm().
			$this->getComments().
			
		'</div>
		';
	}

	/**
	*	@return 	string 	om man är inloggad: ett formulär för att lämna kommentarer på undersöknignens resultat.
	*/
	public function getCommentForm()
	{
		if($this->login->getIsLoggedIn())
		{
			return '
			<div id="commentForm">
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<textarea maxlength="1000" cols="100" rows="5" name="'.helpers\PostHandler::$COMMENT.'" id="comment"></textarea>
				<input type="submit" value="Send Comment">
			</form>	
			</div>
			';
		}
		//Vid utloggad visas inget formulär.
		else
		{
			return 
			'<div id="commentForm">
			<p>Login to make a comment</p>
			</div>';
		}
	}

	/**
	*	Denna funktion hämtar in alla kommentarer som gjort s på undersöknigen och visar dem på sidan.
	*	@return 	string 	html för delen med kommentarerna.
	*/
	private function getComments()
	{
		//hämtar kommentarer för denna undersökning.
		$comments = $this->commentHandler->getCommentsInPoll($this->poll->getId());

		$retHTML = '<div id="comments">';

		// om det finns kommentarer
		if($comments)
		{
			foreach ($comments as $comment) 
			{
				//hämta kommentarens författare
				$writer = $this->commentHandler->getCommentWriter($comment);

				$retHTML .= 
				'<div class="comment">
					<div class="commentHead">
						<p>'.$comment->getCommentTime().'</p><p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWUSER.
						'&'.helpers\GetHandler::$ID.'='.$comment->getUserId().'">'. $writer->getUserName().'</a>
					</div>

					<div class="commentBody"><p>'.$comment->getComment().'</p></div>';
				//rapporteringsfunktion för inloggade.
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
				$retHTML .= '</div>';
			}
		}

		return $retHTML . '</div>';
	}

	/**
	*	@return 	string 	sidans titel om man har röstat i privat poll.
	*/
	public function privateVoteTitle()
	{
		return "Thanks for the vote";
	}

	/**
	*	@return 	string 	sidans innehåll om man röstat i privat poll.
	*/
	public function privateVotePage()
	{
		return 
		'<h1>Thanks for the vote!</h1>
		<p>This poll is private so we can not show you the result. But thanks for the vote. 
			Maybe if you ask the owner nicely, he will show you the results. </p>
		';
	}

	/**
	*	@param 		array 	alla svar som gjort i undersökning
	*	@return 	array 	alla i svar i procent-format.
	*/
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

	/**
	*	Funktion som skapar feedback efter att ha fått en lista med konstanter.
	*	@param 	string/array 	feedback
	*	@return string 			html-list med feedback.
	*/
	public function makeFeedback($feedback)
	{
		$retString = '<div id="feedback"><ul>';  

		if(is_array($feedback))
		{
			//felmeddelanden
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
   
	        //övrig feedback
	        if(in_array(\model\ReportHandler::REPORTEDPOLL, $feedback) || in_array(\model\ReportHandler::REPORTEDCOMMENT, $feedback))
	        {
	            $retString .= "<li>Thanks for reporting this. We will take a look at it.</li>";
	        }
	    }

        if($this->userVoted())
        {
        	$retString .= "<li>Thanks for the vote!</li>";
        }     


        return $retString . '</ul></div>';
	}
}

