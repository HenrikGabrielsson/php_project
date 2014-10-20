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

	public function getContent($id, $login)
	{	
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
			$title = $this->pollView->denyTitle();
			$body = $this->pollView->denyPage();
			$this->htmlView->showHTML($title, $body);
			return;
		}

		//om användaren vill se resultat eller frågan där de kan rösta
		if($this->pollView->userWantsResults())
		{
			if($this->pollView->userCommented())
			{
				$comment = $this->pollView->getComment();

				if($this->commentHandler->attemptCreateComment($comment, $id))
				{
					$feedback = "Thank you for your comment.";
				}
				else
				{
					$feedback = $this->commentHandler->getErrorList();
				}
			}

			//om användaren har röstat så skickas detta till modellen
			if($this->pollView->userVoted())
			{
				$ip = $this->pollView->getClient();
				$answer = $this->pollView->getAnswer();

				$this->voter->addNewVote($answer,$ip);

				$feedback = "Thank you for your vote.";

				//uppdaterna med den nya rösten
				$poll = $this->pollRepo->getPollById($id);
				$this->pollView = new \view\PollView($poll, $owner, $login, $this->commentHandler, $this->reportHandler);

			}

			//om användaren har rapporterat en undersökning
			if($this->pollView->userReportedPoll())
			{
				$reportReason = $this->pollView->getPollReportReason();

				if($this->reportHandler->reportPoll($poll, $reportReason))
				{
					$feedback = "Thank you reporting this poll. We will have a look at it.";
				}
				else
				{
					$feedback = $this->reportHandler->getErrorList();
				}
			}

			//om användaren har rapporterat en kommentar
			if($this->pollView->userReportedComment())
			{
				$commentId = $this->pollView->getCommentReportId();
				$reportReason = $this->pollView->getCommentReportReason();

				$comment = $this->commentHandler->getComment($commentId);


				if($this->reportHandler->reportComment($comment, $reportReason))
				{
					$feedback = "Thank you reporting this comment. We will have a look at it.";
				}
				else
				{
					$feedback = $this->reportHandler->getErrorList();
				}
			}

			$body = $this->pollView->getResultPage($feedback);
		}
		else
		{
			$body = $this->pollView->getForm();
		}

		$title = $this->pollView->getTitle();
		$this->htmlView->showHTML($title, $body);
	}
}