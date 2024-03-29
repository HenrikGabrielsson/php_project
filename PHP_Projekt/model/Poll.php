<?php

namespace model;

class Poll
{
	private $id;					//unikt id
	private $question;				//frågan som ställs
	private $creator;				//skaparen av undersökningen
	private $creationDate;			//när undersökningen skapades
	private $public;				//ska den kunna ses av alla på webbplatsen.
	private $category;				//kategori

	private $answers = array();		//en array med de svar som tillhör frågan.
	
	public function __construct($question, $creator, $creationDate, $public, $category, $answers, $id = 0)
	{
		$this->id = $id;
		$this->question = $question;
		$this->creator = $creator;
		$this->creationDate = $creationDate;
		$this->public = $public;
		$this->category = $category;
		
		$this->answers = $answers;
	}
	
	
	public function getId()
	{
		return $this->id;
	}
	public function getQuestion()
	{
		return $this->question;
	}
	public function getCreator()
	{
		return $this->creator;
	}
	public function getCreationDate()
	{
		return $this->creationDate;
	}
	public function getPublic()
	{
		return $this->public;
	}
	public function getCategory()
	{
		return $this->category;
	}
	public function getAnswers()
	{
		return $this->answers;
	}
}
