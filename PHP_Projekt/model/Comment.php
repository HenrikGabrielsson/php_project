<?php

namespace model;

class Comment
{

	private $id;			//kommentarens id
	private $comment;		//kommentaren i text
	private $pollId;		//undersökning där kommentaren gjordes
	private $userId;		//kommentarskrivarens id
	private $commentTime;	//när kommentaren gjordes

	public function __construct($comment, $pollId, $userId, $commentTime, $id = 0)
	{
		$this->id = $id;
		$this->comment = $comment;
		$this->pollId = $pollId;
		$this->userId = $userId;
		$this->commentTime = $commentTime;
	}

	//getters
	public function getId()
	{
		return $this->id;
	}

	public function getPollId()
	{
		return $this->pollId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function getCommentTime()
	{
		return $this->commentTime;
	}
}