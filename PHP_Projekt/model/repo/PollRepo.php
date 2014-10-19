<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/Poll.php");
require_once("./model/Answer.php");

class PollRepo extends \model\repository\Repository
{

	//tabellnamn
	private $pollTable = "poll";
	private $answerTable = "answer";
	private $voteTable = "vote";

	//namn på stored procedures 
	private $deletePollProc = "deletePoll";	//tar bort poll + tillhörande votes,answers, comments, och eventuella reports på alla dessa
	private $didUserVoteProc = "didUserVote";	//kollar om en användare redan röstat i en poll. returrnerar då voteId, annars false;

	//namn på fält i Poll-tabell
	private $pollId = "pollId";				//unikt id
	private $question = "question";			//frågan som ställs
	private $creator = "creatorId";			//skaparen av undersökningen
	private $creationDate = "creationDate";	//när undersökningen skapades
	private $public = "public";				//ska den kunna ses av alla på webbplatsen.
	private $category = "categoryId";		//kategori
   
   	//namn på fält i Answer-tabell
   	private $answerId = "answerId";
   	private $answer = "answer";
	private $count = "count";
	
	//namn på fält i Vote-tabell
	private $voteId = "voteId";				//unikt id på varje röst
	private $ip = "ip";						//ip på den som röstar.
	
	//funktion för att lägga till en undersökning i databasen
	public function add(\model\Poll $poll)
	{

		$sql = "INSERT INTO ".$this->pollTable."(".$this->creator.", ".$this->question.", ".$this->creationDate.", ".$this->public.", ".$this->category.")
		VALUES (?,?,?,?,?);";
		$params = array($poll->getCreator(), $poll->getQuestion(), $poll->getCreationDate(), $poll->getPublic(), $poll->getCategory());
		
		$this->connect();	
		
		$query = $this->dbConnection->prepare($sql);	
		$result = $query->execute($params);
		
		//id på den undersökning som lades in senast()
		$lastId = ($this->dbConnection->lastInsertId());

		if($result)
		{
			$this->addAnswers($lastId, $poll->getAnswers());
		}
		
		return $result;
	}
	
	
	public function delete($id)
	{
		$sql = "CALL ".$this->deletePollProc."(?)";
		$params = array($id);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		return $result;
	}

	public function getAllPublicPolls()
	{
		//array som ska returneras
		$retPolls = array();

		$sql = "SELECT * FROM ".$this->pollTable." WHERE ".$this->public." = 1";

		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute();	
		
		//hämta alla rader
		$polls = $query->fetchAll();
		
		//om det kom några polls
		if($polls)
		{
			foreach($polls as $poll)
			{
				//hämta alla svar som hör till.
				$answers = $this->getAnswers($poll[$this->pollId]);

				//skapa alla objekt				
				$retPolls[] = new \model\Poll
				(
					$poll[$this->question],
					$poll[$this->creator],
					$poll[$this->creationDate],
					$poll[$this->public],
					$poll[$this->category],
					$answers,
					$poll[$this->pollId]						
				);
			}
			
		return $retPolls;
		}	
	}
	
	public function getPollById($id)
	{

		$sql = "SELECT * FROM ".$this->pollTable." WHERE ".$this->pollId."=?";
		$params = array($id);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);
		
		$result = $query->fetch();

		if($result)
		{
			//svaren till undersökningen
			$answers = $this->getAnswers($id);
			
			//om det kom några svar tillbaka, skapa nytt poll-objekt
			if($answers)
			{
				$poll = new \model\Poll
				(
					$result[$this->question],
					$result[$this->creator],
					$result[$this->creationDate],
					$result[$this->public],
					$result[$this->category],
					$answers,
					$result[$this->pollId]
					
				);
				
				return $poll;
			}
		}
		return null;
	}
	
	public function getAllPollsFromUser($userId, $getAll, $includePrivate = false)
	{
		//array som ska returneras
		$retPolls = array();
		
		if($getAll)
			$limit = "";
		else 
			$limit = "LIMIT 0,10";

		if($includePrivate)
			$privates = "";
		else
			$privates  = "AND ".$this->public."=1 ";

		$sql = "SELECT * FROM ".$this->pollTable." WHERE ".$this->creator."= ? ".$privates." ORDER BY ". $this->creationDate ." DESC ".$limit;
		$params = array( $userId);

		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);	
		
		//hämta alla rader
		$polls = $query->fetchAll();
		
		//om det kom några polls
		if($polls)
		{
			foreach($polls as $poll)
			{
				//hämta alla svar som hör till.
				$answers = $this->getAnswers($poll[$this->pollId]);

				//skapa alla objekt				
				$retPolls[] = new \model\Poll
				(
					$poll[$this->question],
					$poll[$this->creator],
					$poll[$this->creationDate],
					$poll[$this->public],
					$poll[$this->category],
					$answers,
					$poll[$this->pollId]						
				);
				
			}
			
		return $retPolls;
		}
		
	}

	public function getLatestPolls($numberOfPolls)
	{
		if(!is_int($numberOfPolls))
		{
			throw new \Exception("Parameter has too be a number.");
		}

		$retPolls = array();

		//alla "publik" undersökningar i efterfrågad kategori
		$sql = "SELECT * FROM ".$this->pollTable." WHERE ".$this->public."=1 ORDER BY ". $this->creationDate ." DESC LIMIT 0,".$numberOfPolls;
		$params = array();
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);

		//hämta alla rader
		$polls = $query->fetchAll();
		
		//om det kom några polls
		if($polls)
		{
			foreach($polls as $poll)
			{
				//hämta alla svar som hör till.
				$answers = $this->getAnswers($poll[$this->pollId]);

				//skapa alla objekt				
				$retPolls[] = new \model\Poll
				(
					$poll[$this->question],
					$poll[$this->creator],
					$poll[$this->creationDate],
					$poll[$this->public],
					$poll[$this->category],
					$answers,
					$poll[$this->pollId]						
				);
				
			}
		}
			
		return $retPolls;

	}
	
	public function getAllPollsInCategory($categoryId)
	{
		//array som ska returneras
		$retPolls = array();
		
		//alla "publik" undersökningar i efterfrågad kategori
		$sql = "SELECT * FROM ".$this->pollTable." WHERE ".$this->category."=? AND ".$this->public."=1";
		$params = array($categoryId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);	
		
		//hämta alla rader
		$polls = $query->fetchAll();

		//om det kom några polls
		if($polls)
		{
			foreach($polls as $poll)
			{
				//hämta alla svar som hör till.
				$answers = $this->getAnswers($poll[$this->pollId]);

				//skapa alla objekt				
				$retPolls[] = new \model\Poll
				(
					$poll[$this->question],
					$poll[$this->creator],
					$poll[$this->creationDate],
					$poll[$this->public],
					$poll[$this->category],
					$answers,
					$poll[$this->pollId]						
				);		
			}		
		return $retPolls;
		}
	}
	
	public function addVote($answerId, $ip)
	{

		$sql = "INSERT INTO ".$this->voteTable."(".$this->ip.",".$this->answerId.")
		VALUES(?,?)";
		$params = array($ip, $answerId);
		
		$this->connect();	

		$query = $this->dbConnection->prepare($sql);
		return $query->execute($params);
	}

	public function updateVote($answerId, $voteId)
	{
		$sql = "UPDATE ".$this->voteTable." SET ".$this->answerId."=? WHERE ".$this->voteId."=?";
		$params = array($answerId, $voteId);

		$this->connect();

		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);		
	}
	
	private function getAnswers($pollId)
	{
		$sql = "SELECT * FROM ".$this->answerTable." WHERE ".$this->pollId."=?";
		$params = array($pollId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);

		if($result)
		{
			$rows = $query->fetchAll();
			
			$answers = array();
			
			foreach($rows as $row)
			{
				$answers[] = (new \model\Answer($row[$this->answer],  $row[$this->pollId], $this->getVotes($row[$this->answerId]), $row[$this->answerId]));
			}	
			return $answers;
			
		}
		return false;
		
	}
	
	//skickar tillbaka false om personen inte redan röstat. Annars skickas voteId på den förra rösten tillbaka.
	public function alreadyVotedInPoll($answerId, $ip)
	{		
		
		//här så undersöker vi om personen med denna ip redan har röstat i undersökningen
		$sql = "CALL ".$this->didUserVoteProc."(?,?)";
		$params = array($answerId, $ip);

		$this->connect();

		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);

		$result = $query->fetch();

		return $result[$this->voteId];
		
	}
	
	private function getVotes($answerId)
	{
		$sql = "SELECT COUNT(*) FROM ".$this->voteTable." WHERE ".$this->answerId."=?";
		$params = array($answerId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);
		
		$result = $query->fetch();
		
		return $result[0];
	}
	
	private function addAnswers($pollId, $answers)
	{
		$params = array();
		$values = array();
	
		//values fylls med placeholders(?) för varje rad som ska in i tabellen
		//params fylls med varannat id(foreign key) och varannat svar.	
		foreach ($answers as $answer) 
		{
			$values[] = "(?,?)";

			$params[] = $pollId;
			$params[] = $answer->getAnswer();
		}
		
		$sql = "INSERT INTO ".$this->answerTable."(".$this->pollId.", ".$this->answer.")
		VALUES".implode(',',$values).";";

		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);	
		$result = $query->execute($params);
		
		return $result;
	}
}