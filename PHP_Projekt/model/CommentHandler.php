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

	//success-feedback
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

	//hämta listan med eventuell feedback
	public function getFeedbackList()
	{
		return $this->feedbackList;
	}

	/**
	*	@param 	int 	id till en poll
	* 	@return array 	array med kommentarer som hör till en poll
	*/
	public function getCommentsInPoll($pollId)
	{
		return $this->commentRepo->getCommentsInPoll($pollId);
	}

	/**
	*	@param 	int 	id på den comment som ska hämtas
	*	@return Comment den kommentar som hittades
	*/
	public function getComment($id)
	{
		return $this->commentRepo->getCommentById($id);
	}

	/**
	*	@param 	int 	id på den comment där skrivaren ska hämtas
	*	@return User 	den användare som hittades
	*/	
	public function getCommentWriter($comment)
	{
		return $this->userRepo->getUserById($comment->getUserId());
	}

	/**
	* Här valideras en kommentar och sparas sedan om den är ok
	* @param 	string 	kommentaren
	* @param 	int 	id på den poll där kommentaren skrevs.
	*/
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

	/*
	*	Validerar en kommentar
	* 	@param 	string 	kommentar som ska valideras
	* 	@return string 	validerar och eventuellt lite modifierad kommentar (escape på html)
	*/
	private function validateComment($comment)
	{
		//html-taggar är ok, men de ska skrivas ut normalt.
		$comment = trim(htmlspecialchars($comment));

		//kommentaren får inte vara tom eller bara innehålla "blanka" tecken.
		if (strlen($comment) == 0)
		{
			$this->feedbackList[] = self::SHORTCOMMENT;
		}

		//kommentaren får inte vara längre än 1000 tecken;
		else if(strlen($comment) > 1000)
		{
			$this->feedbackList[] = self::LONGCOMMENT;
		}

		return $comment;
	}

	/**
	*	kollar så en poll finns.
	* 	@param int 	id på poll som ska finnas.
	*/
	private function validatePoll($pollId)
	{		
		if($this->pollRepo->getPollById($pollId) == false)
		{
			$this->feedbackList[] = self::POLLDOESNOTEXIST;
		}
	}
}