<?php

namespace model;

class PollReport
{
	private $reportedPollId;		//unikt id
	private $userId;				//undersökningens ägares unika id
	private $pollId;				//undersökningens unika id
	private $commentFromReporter;	//frivillig kommentar från rapportören

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