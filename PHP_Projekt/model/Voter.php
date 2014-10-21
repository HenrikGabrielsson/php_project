<?php

namespace model;

class Voter
{
	private $pollRepo;

	public function __construct($pollRepo)
	{
		$this->pollRepo = $pollRepo;
	}

	/**
	* Används när en användare röstar
	* @param int 		id på svaret
	* @param string 	röstarens ip-adress
	*/
	public function addNewVote($answer, $ip)
	{

		//kollar om användaren har röstar redan, genom att leta efter ip-adressen i tidigare röstare i udnersöknignen
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