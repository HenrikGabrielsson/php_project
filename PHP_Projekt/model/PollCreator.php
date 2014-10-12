<?php

namespace model;

require_once("./model/Poll.php");
require_once("./model/Answer.php");
require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CategoryRepo.php");
require_once("./model/helpers/SessionHandler.php");

class PollCreator
{
	private $errorList;

	//errors
	public $shortQuestion = "shortQuestion"; 
	public $longQuestion = "longQuestion";
	public $tooManyAnswers = "tooManyAnswers";
	public $tooFewAnswers = "tooFewAnswers";
	public $notPublicOrPrivate = "notPublicOrPrivate";
	public $categoryDoesNotExist = "categoryDoesNotExist";

	private $categoryRepo;
	private $pollRepo;

	public function __construct()
	{
		$this->categoryRepo = new \model\repository\CategoryRepo();
		$this->pollRepo = new \model\repository\PollRepo();
	}

	public function getErrorList()
	{
		return $this->errorList;
	}

	public function attemptToCreate($question, $answers, $category, $public)
	{
		//validera all input.
		$question = $this->validateQuestion($question);
		$answers = $this->validateAnswers($answers);
		$this->validatePublic($public);
		$this->validateCategory($category);


		//om det finns några fel så stoppar vi här.
		if(count($this->errorList) > 0)
		{
			return false;
		}

		$answer_objs = array();
		foreach($answers as $answer)
		{
			$answer_objs[] = new Answer($answer);
		}

		//en poll skapas om valideringen gick bra
		$poll = new \model\Poll($question, $_SESSION[helpers\SessionHandler::$USERID], date("Y-m-d H:i:s"), $public, $category, $answer_objs);

		$this->pollRepo->add($poll);

		//allt gick bra
		return true;
	}

	private function validateQuestion($question)
	{

		//html-taggar är ok, men de ska skrivas ut normalt.
		$question = htmlspecialchars($question);

		//frågan får inte vara tom eller bara innehålla "blanka" tecken.
		if (strlen(trim($question)) == 0)
		{
			$this->errorList[] = $this->shortQuestion;
		}

		//frågan får inte vara längre än 100 tecken;
		else if(strlen($question) > 100)
		{
			$this->errorList[] = $this->longQuestion;
		}

		return $question;
	}

	private function validateAnswers($answers)
	{

		//plockar bort alla tomma element. fixar till html-taggar
		$tempArray = array();
		foreach ($answers as $ans) 
		{
			if((strlen(trim($ans)) == 0) == false)
			{
				$ans = htmlspecialchars($ans);
				$tempArray[] = $ans;
			}
		}
		$answers = $tempArray;

		//inga svar
		if(is_null($answers))
		{
			$this->errorList[] = $this->tooFewAnswers;
		}

		//för många svar
		if(count($answers) > 10)
		{
			$this->errorList[] = $this->tooManyAnswers;
		} 

		//för få svar
		if(count($answers) < 2)
		{
			$this->errorList[] = $this->tooFewAnswers;
		}

		return $answers;
	}

	private function validateCategory($cat)
	{
		if($this->categoryRepo->getCategoryById($cat) == false)
		{
			$this->errorList[] = $this->categoryDoesNotExist;
		} 
	}

	private function validatePublic($public)
	{
		//kollar så inte värdet är annat än 1 eller 0 eller om den är null
		if(($public != 0 && $public != 1) || is_null($public))
		{
			$this->errorList[] = $this->notPublicOrPrivate;
		}
	}
}