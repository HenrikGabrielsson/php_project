<?php

namespace model;

require_once("./model/repo/CommentRepo.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/UserRepo.php");
require_once("./model/Comment.php");
require_once("./model/helpers/SessionHandler.php");

class CommentHandler
{


	//errors
	const SHORTCOMMENT = "shortComment";
	const LONGCOMMENT = "longComment";
	const POLLDOESNOTEXIST = "pollDoesNotExist";

	const COMMENTSAVED = "commentSaved";

	private $feedbackList = array();

	private $commentRepo;
	private $pollRepo;
	private $userRepo;

	public function __construct()
	{
		$this->commentRepo = new \model\repository\CommentRepo();
		$this->pollRepo = new \model\repository\PollRepo();
		$this->userRepo = new \model\repository\UserRepo();
	}

	public function getFeedbackList()
	{
		return $this->feedbackList;
	}

	public function getCommentsInPoll($pollId)
	{
		return $this->commentRepo->getCommentsInPoll($pollId);
	}

	public function getComment($id)
	{
		return $this->commentRepo->getCommentById($id);
	}

	public function getCommentWriter($comment)
	{
		return $this->userRepo->getUserById($comment->getUserId());
	}

	public function attemptCreateComment($comment, $pollId)
	{
		//validering
		$comment = $this->validateComment($comment);
		$this->validatePoll($pollId);

		//om det finns fel.
		if(count($this->feedbackList) > 0)
		{
			return;
		}

		//skapar kommentaren och sparar den i databasen
		$comment = new Comment($comment, $pollId, $_SESSION[helpers\SessionHandler::$USERID], date("Y-m-d H:i:s"));
		$this->commentRepo->add($comment);

		$this->feedbackList[] = self::COMMENTSAVED;	
	}

	private function validateComment($comment)
	{
		//html-taggar är ok, men de ska skrivas ut normalt.
		$comment = htmlspecialchars($comment);

		//kommentaren får inte vara tom eller bara innehålla "blanka" tecken.
		if (strlen(trim($comment)) == 0)
		{
			$this->feedbackList[] = self::SHORTCOMMENT;
		}

		//kommentaren får inte vara längre än 100 tecken;
		else if(strlen($comment) > 100)
		{
			$this->feedbackList[] = self::LONGCOMMENT;
		}

		return $comment;
	}

	private function validatePoll($pollId)
	{		
		if($this->pollRepo->getPollById($pollId) == false)
		{
			$this->feedbackList[] = self::POLLDOESNOTEXIST;
		}
	}
}