<?php

namespace view;

class PollCreationView
{
	private $categories;

	public function __construct($categories)
	{
		$this->categories = $categories;
	}

	/**
	*	@return 	bool	om användaren vill skapa en poll
	*/
	public function userWantsToCreatePoll()
	{
		return isset($_GET[helpers\GetHandler::$CREATE]);	
	}

	/**
	*	@return 	string 		sidans title
	*/
	public function getTitle()
	{
		return "Create a new poll";
	}

	/**
	*	@return 	string	frågan som användaren skrivit
	*/
	public function getQuestion()
	{
		return $_POST[helpers\PostHandler::$CREATEQUESTION];
	}


	/**
	*	@return 	array 	en array med svaren som användaren skrev in
	*/
	public function getAnswers()
	{
		return $_POST[helpers\PostHandler::$CREATEANSWER];
	}


	/**
	*	@return 	int 	id på vald kategori
	*/
	public function getCategory()
	{
		return $_POST[helpers\PostHandler::$CREATECATEGORY];
	}

	/**
	*	@return 	int 	om det är en publik eller privat poll.
	*/
	public function getIsPublic()
	{
		return $_POST[helpers\PostHandler::$CREATEPUBLIC];
	}

	/**
	*	@return 	string 	innehåll om man inte är inloggad
	*/
	public function getNotLoggedIn()
	{
		return 
		'<h1>Create Poll</h1>
		<p>You must log in before you can create a poll.</p>';
	}

	/**
	*	@return 	string 	innehåll om man har lyckats skapa en undersökning
	*/
	public function getSuccessPage()
	{
		$body = 
		'<h1>Poll Created</h1>
		<p>Congratulations! Your poll has successfully been created.</p>';
		return $body;
	}

	/**
	*	Denna funktion kallar på rätt metoder för att ett formulär och eventuell feedback	
	*
	*	@param 		array 	lista med den typ av feedback som ska skrivas ut.
	*	@return 	string 	formulär för att skapa en poll
	*/
	public function getCreate($feedback = null)
	{
		$body = 
		'<h1>Create Poll</h1>'.
		$this->getForm().
		$this->makeFeedback($feedback);
		
		return $body;
	}

	/**
	*	@return string 	ett htmlformulär med allt som behövs för att skapa en poll.
	*/
	public function getForm()
	{
		$categories ="";

		//skapar dropdown med kategorierna
		foreach($this->categories as $category)
		{
			$categories .= '<option value="'.$category->getId().'">'.$category->getCategoryName().'</option>';
		}

		//skapar alla svarsfält.
		$answerFields = '';
		for($i = 1; $i <= 10; $i++)
		{
			$answerFields .= 
			'<label class="createAnswerLabels" for="createAnswer'.$i.'">Answer '.$i.': </label>
			<input type="text" id="createAnswer'.$i.'" maxlength="100" class="createAnswer" name="'.helpers\PostHandler::$CREATEANSWER.'[]">';
		}

		//resten av formuläret.
		$form = 
		'<form id="createPollForm" action="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWCREATEPOLL.'&'.helpers\GetHandler::$CREATE.'" method="post">

		<label for="createQuestion">Question: </label><input maxlength="100" type="text" name="'.helpers\PostHandler::$CREATEQUESTION.'" id="createQuestion">
	
		<fieldset id="answers_fieldset">
		'.$answerFields.'
		</fieldset>

		<select name="'.helpers\PostHandler::$CREATECATEGORY.'">
			'.$categories.'
		</select>

		<label for="privateRadio">Private: </label><input id="privateRadio" type="radio" name="'.helpers\PostHandler::$CREATEPUBLIC.'" value="0">
		<label for="publicRadio">Public: </label><input id="publicRadio" type="radio" name="'.helpers\PostHandler::$CREATEPUBLIC.'" value="1" checked>

		<input type="submit" value="Create!">
		</form>
		';

		return $form;
	}

	/**
	*	Denna funktion tar emot en array med konstanter som berättar vilken typ av feedback användaren bör få.
	* 	@param 	array 	konstanter som berättar vilken feedback som ska ges.
	*	@param 	string 	lista med feedback
	*/
	public function makeFeedback($feedbackArray)
	{

		//avsluta om den är tom.
		if(is_null($feedbackArray))
		{
			return;
		}

		//fråga vilka konstanter som finns i listan och skriv ut feedback i en lista.
		$feedback .= '<div id="feedback"><ol>';

		if(in_array(\model\Pollcreator::SHORTQUESTION, $feedbackArray))
        {
            $feedback .= "<li>You must write a quesion.</li>";
        }	
		if(in_array(\model\Pollcreator::LONGQUESTION, $feedbackArray))
        {
            $feedback .= "<li>Your question can't be longer than 100 characters.</li>";
        }      
        if(in_array(\model\Pollcreator::TOOMANYANSWERS, $feedbackArray))
        {
            $feedback .= "<li>You can have a maximum of 10 answers for one question.</li>";
        }
        if(in_array(\model\Pollcreator::TOOFEWANSWERS, $feedbackArray))
        {
            $feedback .= "<li>A question must have at least 2 answers.</li>";
        }   
        if(in_array(\model\Pollcreator::LONGANSWER, $feedbackArray))
        {
            $feedback .= "<li>An answer can't be longer than 100 characters.</li>";
        }   
        if(in_array(\model\Pollcreator::NOTPUBLICORPRIVATE, $feedbackArray))
        {
            $feedback .= "<li>Decide if you want your poll to be public or private.</li>";
        }
        if(in_array(\model\Pollcreator::CATEGORYDOESNOTEXIST, $feedbackArray))
        {
            $feedback .= "<li>Pick a category.</li>";
        }

        return $feedback . '</ol></div>';
	}

}