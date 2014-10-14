<?php

namespace model;

class PollReport
{
	private $reportedPollId;
	private $userId;
	private $pollId;
	private $commentFromReporter;

	public function __construct($userId, $pollId, $commentFromReporter, $reportedPollId = null)
	{
		$this->reportedPollId = $reportedPollId;
		$this->userId = $userId;
		$this->pollId = $pollId;
		$this->commentFromReporter = $commentFromReporter;
	}

	public function getId()
	{
		return $this->reportedPollId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getPollId()
	{
		return $this->pollId;
	}

	public function getCommentFromReporter()
	{
		return $this->commentFromReporter;
	}
}