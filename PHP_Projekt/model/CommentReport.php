<?php

namespace model;

class CommentReport
{
	private $reportedCommentId;
	private $userId;
	private $commentId;
	private $commentFromReporter;

	public function __construct($userId, $commentId, $commentFromReporter, $reportedCommentId = null)
	{
		$this->reportedCommentId = $reportedCommentId;
		$this->userId = $userId;
		$this->commentId = $commentId;
		$this->commentFromReporter = $commentFromReporter;
	}

	public function getId()
	{
		return $this->reportedCommentId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getCommentId()
	{
		return $this->commentId;
	}

	public function getCommentFromReporter()
	{
		return $this->commentFromReporter;
	}
}