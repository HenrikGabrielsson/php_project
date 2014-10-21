<?php 

namespace controller;

require_once("./view/PollView.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/Voter.php");
require_once("./model/CommentHandler.php");
require_once("./model/ReportHandler.php");

class PollController
{
	private $htmlView;
	private $pollView;
	private $voter;
	private $pollRepo;
	private $userRepo;
	private $commentHandler;
	private $reportHandler;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;

		$this->pollRepo = new \model\repository\PollRepo();
		$this->userRepo = new \model\repository\UserRepo();
		
		$this->voter = new \model\Voter($this->pollRepo);
		$this->commentHandler = new \model\CommentHandler();
		$this->reportHandler = new \model\ReportHandler();

	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	* @param login 	En loginhandler som berättar vissa saker om den inloggade användaren.
	* @param id   	id på den in poll som ska visas upp.
	*/
	public function getContent($id, \model\LoginHandler $login)
	{	

		//hämta aktuell poll för sidan
		$poll = $this->pollRepo->getPollById($id);

		//undersökningen valdes inte/hittades inte
		if($poll === false)
		{
			$this->htmlView->showErrorPage();
			die();
		}

		$owner = $this->userRepo->getUserById($poll->getCreator());

		$this->pollView = new \view\PollView($poll, $owner, $login, $this->commentHandler, $this->reportHandler);

		//om undersökningen är privat så kan bara skaparen se den.
		if($poll->getPublic() == false && $poll->getCreator() !== $login->getId())
		{
			//om användaren röstade i en privat poll
			if($poll->getPublic() == false && $this->pollView->userVoted())
			{
				$title = $this->pollView->privateVoteTitle();
				$body = $this->pollView->PrivateVotePage();
				$this->htmlView->showHTML($title, $body);
				die();
			}
			
			//Access Denied för övriga
			$this->htmlView->showDenyPage();
			return;
		}

		//om användaren röstade i en privat poll (shared)
		if($poll->getPublic() == false && $this->pollView->userVoted())
		{
			$title = $this->pollView->privateVoteTitle();
			$body = $this->pollView->PrivateVotePage();
			$this->htmlView->showHTML($title, $body);
			die();
		}

		//om användaren vill se resultat eller frågan där de kan rösta
		if($this->pollView->userWantsResults())
		{
			if($this->pollView->userCommented())
			{
				$comment = $this->pollView->getComment();

				$this->commentHandler->attemptCreateComment($comment, $id);

				$feedback = $this->commentHandler->getFeedbackList();
			}

			//om användaren har röstat så skickas detta till modellen
			if($this->pollView->userVoted())
			{
				$ip = $this->pollView->getClient();
				$answer = $this->pollView->getAnswer();

				$this->voter->addNewVote($answer,$ip);

				//uppdaterna med den nya rösten
				$poll = $this->pollRepo->getPollById($id);

				//uppdatera undersökningsvyn med den nya rösten.
				$this->pollView = new \view\PollView($poll, $owner, $login, $this->commentHandler, $this->reportHandler);
			}

			//om användaren har rapporterat en undersökning
			if($this->pollView->userReportedPoll())
			{
				$reportReason = $this->pollView->getPollReportReason();

				$this->reportHandler->reportPoll($poll, $reportReason);

				$feedback = $this->reportHandler->getFeedbackList();
			}

			//om användaren har rapporterat en kommentar
			if($this->pollView->userReportedComment())
			{
				$commentId = $this->pollView->getCommentReportId();
				$reportReason = $this->pollView->getCommentReportReason();

				$comment = $this->commentHandler->getComment($commentId);

				$this->reportHandler->reportComment($comment, $reportReason);

				$feedback = $this->reportHandler->getFeedbackList();
			}

			$body = $this->pollView->getResultPage($feedback);
		}

		//sidan där man kan rösta visas annars
		else
		{
			$body = $this->pollView->getForm();
		}

		$title = $this->pollView->getTitle();
		$this->htmlView->showHTML($title, $body);
	}
}