<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/Comment.php");

class CommentRepo extends \model\repository\Repository
{

	private $commentTable = "comment";

	private $deleteCommentProc = "deleteComment";

	//databasens kolumnnamn
	private $commentId = "commentId";
	private $pollId = "pollId";
	private $userId = "userId";
	private $comment = "comment";
	private $commentTime = "commentTime";


	//lägg till en Kommentar
	public function add(\model\Comment $comment)
	{
		$sql = "INSERT INTO ".$this->commentTable."(".$this->pollId.", ".$this->userId.", ".$this->comment.", ".$this->commentTime.")
		VALUES (?,?,?,?);";
		$params = array($comment->getPollId(), $comment->getUserId(), $comment->getComment(), $comment->getCommentTime());
		
		$this->connect();	
		
		$query = $this->dbConnection->prepare($sql);	
		$result = $query->execute($params);
		
		return $result;
	}

	//Ta bort en kommentar (/)by id)
	public function delete($commentId)
	{
		$sql = "CALL ".$this->deleteCommentProc."(?)";
		$params = array($commentId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$result = $query->execute($params);
		
		return $result;		
	}

	/**
	* Hämta kommentarer gjorda av en användare
	* @param int 	id på användaren
	* @param bool 	ska alla hämtas. Annars bara de 10 första
	*/
	public function getCommentsFromUser($userId, $getAll)
	{

		if($getAll)
			$limit = "";
		else 
			$limit = "LIMIT 0,10";

		//array som ska returneras
		$retComments = array();
		
		$sql = "SELECT * FROM ".$this->commentTable." WHERE ".$this->userId."=? " . $limit;
		$params = array($userId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);	
		
		//hämta alla rader
		$comments = $query->fetchAll();
		
		//om det kom några kommentarer
		if($comments)
		{
			foreach($comments as $comment)
			{
				//skapa alla objekt				
				$retComments[] = new \model\Comment
				(
					$comment[$this->comment],
					$comment[$this->pollId],
					$comment[$this->userId],
					$comment[$this->commentTime],
					$comment[$this->commentId]			
				);
				
			}
			
		return $retComments;
		}			
	}

	//hämta alla kommentarer i angiven poll
	public function getCommentsInPoll($pollId)
	{
		//array som ska returneras
		$retComments = array();
		
		$sql = "SELECT * FROM ".$this->commentTable." WHERE ".$this->pollId."=? ORDER BY ".$this->commentTime." DESC";
		$params = array($pollId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);	
		
		//hämta alla rader
		$comments = $query->fetchAll();
		
		//om det kom några kommentarer
		if($comments)
		{
			foreach($comments as $comment)
			{
				//skapa alla objekt				
				$retComments[] = new \model\Comment
				(
					$comment[$this->comment],
					$comment[$this->pollId],
					$comment[$this->userId],
					$comment[$this->commentTime],
					$comment[$this->commentId]			
				);
				
			}
			
		return $retComments;
		}		
	}

	//hämta en specifik kommentar
	public function getCommentById($commentId)
	{
		$sql = "SELECT * FROM ".$this->commentTable." WHERE ".$this->commentId."=?";
		$params = array($commentId);
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);
		
		$result = $query->fetch();

		if($result)
		{			
			$comment = new \model\Comment
			(
				$result[$this->comment],
				$result[$this->pollId],
				$result[$this->userId],
				$result[$this->commentTime],
				$result[$this->commentId]
			);	
			return $comment;

		}
		return null;		
	}

}