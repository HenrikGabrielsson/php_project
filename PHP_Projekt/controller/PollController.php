<?php 

namespace controller;

require_once("./view/PollView.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/Voter.php");
require_once("./model/CommentHandler.php");
require_once("./model/ReportHandler.php");
require_once("./controller/IMainContentController.php");

class PollController implements IMainContentController
{
	private $poll;
	private $owner;
	private $login;

	private $pollView;
	private $voter;
	private $pollRepo;
	private $userRepo;
	private $commentHandler;
	private $reportHandler;

	public function __construct($id, \model\LoginHandler $login)
	{
		$this->pollRepo = new \model\repository\PollRepo();
		$this->userRepo = new \model\repository\UserRepo();
		
		$this->voter = new \model\Voter($this->pollRepo);
		$this->commentHandler = new \model\CommentHandler();
		$this->reportHandler = new \model\ReportHandler();

		//hämta aktuell poll, ägare och uppgifter om inloggningen
		$this->login = $login;
		$this->poll = $this->pollRepo->getPollById($id);
		$this->owner = $this->userRepo->getUserById($this->poll->getCreator());

		$this->pollView = new \view\PollView($this->poll, $this->login, $this->owner, $this->commentHandler, $this->reportHandler);
	}

	/**
	*	Hämtar innehållet som ska visas och fyller htmlViewn med det.
	*/
	public function getBody()
	{	
		//undersökningen valdes inte/hittades inte
		if($this->poll === false)
		{
			return false;
		}
	
		//om undersökningen är privat så kan bara skaparen se den.
		if($this->poll->getPublic() == false && $this->owner->getId() !== $this->login->getId())
		{
			//om användaren röstade i en privat poll
			if($this->pollView->userVoted())
			{
				return $this->pollView->PrivateVotePage();				
			}

			//övriga får inte komma åt sidan.
			return false;
		}

		//om användaren vill se resultat eller frågan där de kan rösta
		if($this->pollView->userWantsResults())
		{
			if($this->pollView->userCommented())
			{
				$comment = $this->pollView->getComment();
				$this->commentHandler->attemptCreateComment($comment, $this->poll->getId());
				$feedback = $this->commentHandler->getFeedbackList();
			}

			//om användaren har röstat så skickas detta till modellen
			if($this->pollView->userVoted())
			{
				$ip = $this->pollView->getClient();
				$answer = $this->pollView->getAnswer();

				$this->voter->addNewVote($answer,$ip);

				//uppdaterna med den nya rösten
				$this->poll = $this->pollRepo->getPollById($this->poll->getId());

				//uppdatera undersökningsvyn med den nya rösten.
				$this->pollView = new \view\PollView($this->poll, $this->login, $this->owner, $this->commentHandler, $this->reportHandler);
			}

			//om användaren har rapporterat en undersökning
			if($this->pollView->userReportedPoll())
			{
				$reportReason = $this->pollView->getPollReportReason();

				$this->reportHandler->reportPoll($this->poll, $reportReason);

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

		
		return $body;
	}

	public function getTitle()
	{
		return $this->pollView->getTitle();
	}

}