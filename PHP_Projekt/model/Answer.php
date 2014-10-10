<?php

namespace model;

class Answer
{
	private $id;		//unikt id
	private $pollId;	//undersökning som svaret hör till
	private $answer;	//svarstext
	private $count;		//antal röster på detta svar.
	
	public function __construct($answer, $pollId = 0, $count = 0, $id = 0)
	{
		$this->id = $id;
		$this->pollId = $pollId;
		$this->answer = $answer;
		$this->count = $count;
	} 
	
	public function getId() 
	{
		return $this->id;
	}
	
	public function getPollId()
	{
		return $this->pollId;
	}
	
	public function getAnswer()
	{
		return $this->answer;
	}
	
	public function getCount()
	{
		return $this->count;
	}	
}