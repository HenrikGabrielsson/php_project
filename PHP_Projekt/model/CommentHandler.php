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
	public $shortComment = "shortComment";
	public $longComment = "longComment";
	public $pollDoesNotExist = "pollDoesNotExist";

	private $errorList = array();

	private $commentRepo;
	private $pollRepo;
	private $userRepo;

	public function __construct()
	{
		$this->commentRepo = new \model\repository\CommentRepo();
		$this->pollRepo = new \model\repository\PollRepo();
		$this->userRepo = new \model\repository\UserRepo();
	}

	public function getErrorList()
	{
		return $this->errorList;
	}

	public function getCommentsInPoll($pollId)
	{
		return $this->commentRepo->getCommentsInPoll($pollId);
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
		if(count($this->errorList) > 0)
		{
			return false;
		}

		//skapar kommentaren och sparar den i databasen
		$comment = new Comment($comment, $pollId, $_SESSION[helpers\SessionHandler::$USERID], date("Y-m-d H:i:s"));
		$this->commentRepo->add($comment);

		return true;
	}

	private function validateComment($comment)
	{
		//html-taggar är ok, men de ska skrivas ut normalt.
		$comment = htmlspecialchars($comment);

		//kommentaren får inte vara tom eller bara innehålla "blanka" tecken.
		if (strlen(trim($comment)) == 0)
		{
			$this->errorList[] = $this->shortComment;
		}

		//kommentaren får inte vara längre än 100 tecken;
		else if(strlen($comment) > 100)
		{
			$this->errorList[] = $this->longComment;
		}

		return $comment;
	}

	private function validatePoll($pollId)
	{		
		if($this->pollRepo->getPollById($pollId) == false)
		{
			$this->errorList[] = $this->pollDoesNotExist;
		}
	}
}