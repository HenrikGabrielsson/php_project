<?php

namespace model;

class Voter
{
	private $pollRepo;

	public function __construct($pollRepo)
	{
		$this->pollRepo = $pollRepo;
	}

	public function addNewVote($answer, $ip)
	{
		$vote = $this->pollRepo->alreadyVotedInPoll($answer, $ip);

		//om användaren redan har röstat så uppdateras den förra rösten.
		if($vote)
		{
			$this->pollRepo->updateVote($answer, $vote);
		}
		else
		{
			$this->pollRepo->addVote($answer, $ip);
		}

	}
}