<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/Poll.php");
require_once("./model/Answer.php");

class PollRepo extends \model\repository\Repository
{
	//namn på fält i Poll-tabell
	private $pollID = "pollID";				//unikt id
	private $question = "question";			//frågan som ställs
	private $creator = "creatorID";			//skaparen av undersökningen
	private $creationDate = "creationDate";	//när undersökningen skapades
	private $public = "public";				//ska den kunna ses av alla på webbplatsen.
	private $category = "categoryID";		//kategori
   
   	//namn på fält i Answer-tabell
   	private $answerID = "answerID";
   	private $answer = "answer";
	private $count = "count";
	
	//funktion för att lägga till en undersökning i databasen
	public function add(\model\Poll $poll)
	{
		$sql = "INSERT INTO poll(".$this->creator.", ".$this->question.", ".$this->creationDate.", ".$this->public.", ".$this->category.")
		VALUES (?,?,?,?,?);";
		$params = array($poll->getCreator(), $poll->getQuestion(), date("Y-m-d"), $poll->getPublic(), $poll->getCategory());
		
		$this->connect();	
		
		$query = $this->dbConnection->prepare($sql);	
		$result = $query->execute($params);
		
		//id på den undersökning som lades in senast()
		$lastID = ($this->dbConnection->lastInsertId());

		if($result)
		{
			$this->addAnswers($lastID, $poll->getAnswers());
		}
		
		return $result;
	}
	
	
	public function delete($id)
	{
		$sql = "DELETE FROM poll WHERE ".$this->pollID." = ?";
		$params = array($id);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		//ta även bort alla svarsalternativ som tillhör undersökningen
		if($result)
		{
			$result = $this->deleteAnswers($id);
		}
		
		return $result;
	}
	
	public function getPollById($id)
	{
		$sql = "SELECT * FROM poll WHERE ".$this->pollID."=?";
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
					$result[$this->pollID]
					
				);
				
				return $poll;
			}
		}
	}
	
	public function getAllPollsFromUser($userId, $includePrivate = true)
	{
		//array som ska returneras
		$retPolls = array();
		
		
		$sql = "SELECT * FROM poll WHERE ".$this->creator."=? ";
		$params = array($userId);
		
		//om även privata ska visas
		if($includePrivate === false)
		{
			$sql .= "AND ".$this->public."=1";
		}
		
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
				$answers = $this->getAnswers($poll[$this->pollID]);

				//skapa alla objekt				
				$retPolls[] = new \model\Poll
				(
					$poll[$this->question],
					$poll[$this->creator],
					$poll[$this->creationDate],
					$poll[$this->public],
					$poll[$this->category],
					$answers,
					$poll[$this->pollID]						
				);
				
			}
			
		return $retPolls;
		}
		
	}
	
	public function getAllPollsInCategory($categoryId)
	{
		//array som ska returneras
		$retPolls = array();
		
		//alla "publik" undersökningar i efterfrågad kategori
		$sql = "SELECT * FROM poll WHERE ".$this->category."=? AND ".$this->public."=1";
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
				$answers = $this->getAnswers($poll[$this->pollID]);

				//skapa alla objekt				
				$retPolls[] = new \model\Poll
				(
					$poll[$this->question],
					$poll[$this->creator],
					$poll[$this->creationDate],
					$poll[$this->public],
					$poll[$this->category],
					$answers,
					$poll[$this->pollID]						
				);
				
			}
			
		return $retPolls;
		}
		
	}	
	
	private function deleteAnswers()
	{
		$sql = "DELETE FROM answer WHERE ".$this->pollID." = ?";
		$params = array($id);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);	
		
		return $result;
	}
	
	private function getAnswers($pollId)
	{
		$sql = "SELECT * FROM answer WHERE ".$this->pollID."=?";
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
				$answers[] = (new \model\Answer($row[$this->answer], $row[$this->answerID], $row[$this->pollID], $row[$this->count]));
			}
			
			return $answers;
			
		}
		return false;
		
	}
	
	private function addAnswers($pollID, $answers)
	{
		$params = array();
		$values = array();
	
		//values fylls med placeholders(?) för varje rad som ska in i tabellen
		//params fylls med varannat id(foreign key) och varannat svar.	
		foreach ($answers as $answer) 
		{
			$values[] = "(?,?)";
			
			$params[] = $pollID;
			$params[] = $answer;
		}
		
		$sql = "INSERT INTO answer(".$this->pollID.", ".$this->answer.")
		VALUES".implode(',',$values).";";

		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);	
		$result = $query->execute($params);
		
		return $result;
	}
}



