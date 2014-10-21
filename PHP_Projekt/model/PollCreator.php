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
	const SHORTQUESTION = "shortQuestion"; 
	const LONGQUESTION = "longQuestion";
	const TOOMANYANSWERS = "tooManyAnswers";
	const TOOFEWANSWERS = "tooFewAnswers";
	const LONGANSWER = "longAnswer";
	const NOTPUBLICORPRIVATE = "notPublicOrPrivate";
	const CATEGORYDOESNOTEXIST = "categoryDoesNotExist";

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

	/**
	* Funktion som används när användaren vill skapa en poll
	* @param string 	frågan
	* @param array 		array med svar
	* @param int 		id på den valda kategorin
	* @param bool 		ska det vara en publik/privat poll
	*/
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

		//skapa svarsobjekt att spara tillsammans med ny poll
		$answer_objs = array();
		foreach($answers as $answer)
		{
			$answer_objs[] = new Answer($answer);
		}

		//en poll skapas om valideringen gick bra
		$poll = new \model\Poll($question, $_SESSION[helpers\SessionHandler::$USERID], date("Y-m-d H:i:s"), $public, $category, $answer_objs);
		
		//allt gick bra
		return true;
	}

	//validera en fråga
	private function validateQuestion($question)
	{

		//html-taggar är ok, men de ska skrivas ut normalt.
		$question = htmlspecialchars($question);

		//frågan får inte vara tom eller bara innehålla "blanka" tecken.
		if (strlen(trim($question)) == 0)
		{
			$this->errorList[] = self::SHORTQUESTION;
		}

		//frågan får inte vara längre än 100 tecken;
		else if(strlen($question) > 100)
		{
			$this->errorList[] = self::LONGQUESTION;
		}

		return $question;
	}

	//validera alla svarsalternativ
	private function validateAnswers($answers)
	{

		//plockar bort alla tomma element. fixar till html-taggar
		$tempArray = array();
		foreach ($answers as $ans) 
		{
			//konstig, men tyvärr nödvändig lösning med en temparray för att ta bort tomma element.
			if((strlen(trim($ans)) == 0) == false)
			{
				$ans = htmlspecialchars($ans);
				$tempArray[] = $ans;
			}

			//koll ifall svaret är för långt
			if(strlen($ans) > 100)
			{
				$this->errorList[] = self::LONGANSWER;
			}
		}
		$answers = $tempArray;

		//inga svar
		if(count($answers) < 2)
		{
			$this->errorList[] = self::TOOFEWANSWERS;
		}

		//för många svar
		if(count($answers) > 10)
		{
			$this->errorList[] = self::TOOMANYANSWERS;
		} 

		return $answers;
	}

	//validera kategorivalet
	private function validateCategory($cat)
	{
		if($this->categoryRepo->getCategoryById($cat) == false)
		{
			$this->errorList[] = self::CATEGORYDOESNOTEXIST;
		} 
	}

	//validera public/private
	private function validatePublic($public)
	{
		//kollar så inte värdet är annat än 1 eller 0 eller om den är null
		if(($public != 0 && $public != 1) || is_null($public))
		{
			$this->errorList[] =self::NOTPUBLICORPRIVATE;
		}
	}
}