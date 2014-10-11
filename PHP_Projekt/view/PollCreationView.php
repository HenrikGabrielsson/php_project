<?php

namespace view;

require_once("./model/repo/CategoryRepo.php");


class PollCreationView
{
	private $catRepo;
	private $pollCreator;

	public function __construct($pollCreator)
	{
		$this->catRepo = new \model\repository\CategoryRepo();
		$this->pollCreator = $pollCreator;
	}

	public function userWantsToCreatePoll()
	{
		return isset($_GET[helpers\GetHandler::getCreate()]);	
	}

	public function getTitle()
	{
		return "Create a new poll";
	}

	public function getQuestion()
	{
		return $_POST[helpers\PostHandler::getCreateQuestion()];
	}

	public function getAnswers()
	{
		return $_POST[helpers\PostHandler::getCreateAnswer()];
	}

	public function getCategory()
	{
		return $_POST[helpers\PostHandler::getCreateCategory()];
	}

	public function getIsPublic()
	{
		return $_POST[helpers\PostHandler::getCreatePublic()];
	}

	public function getNotLoggedIn()
	{
		return 
		'<h1>Create Poll</h1>
		<p>You must log in before you can create a poll.</p>';
	}

	public function getSuccessPage()
	{
		$body = 
		'<h1>Poll Created</h1>
		<p>Congratulations! Your poll has successfully been created.</p>';
		return $body;
	}

	public function getCreate($feedback = null)
	{
		$body = 
		'<h1>Create Poll</h1>

		'.$this->makeFeedback($feedback)
		.$this->getForm();
		
		return $body;
	}

	public function getForm()
	{

		$categories ="";

		foreach($this->catRepo->getAllCategories() as $category)
		{
			$categories .= '<option value="'.$category->getId().'">'.$category->getCategoryName().'</option>';
		}

		$answerFields = '';
		for($i = 1; $i <= 10; $i++)
		{
			$answerFields .= 
			'<label class="createAnswerLabels" for="createAnswer'.$i.'">Answer '.$i.': </label>
			<input type="text" id="createAnswer'.$i.'" class="createAnswer" name="'.helpers\PostHandler::getCreateAnswer().'[]">';
		}

		$form = 
		'<form id="createPollForm" action="?'.helpers\GetHandler::getView().'='.helpers\GetHandler::getViewCreatePoll().'&'.helpers\GetHandler::getCreate().'" method="post">

		<label for="createQuestion">Question: </label><input type="text" name="'.helpers\PostHandler::getCreateQuestion().'" id="createQuestion">
	
		<fieldset id="answers_fieldset">
		'.$answerFields.'
		</fieldset>

		<select name="'.helpers\PostHandler::getCreateCategory().'">
			'.$categories.'
		</select>

		<label for="privateRadio">Private: </label><input id="privateRadio" type="radio" name="'.helpers\PostHandler::getCreatePublic().'" value="0">
		<label for="publicRadio">Public: </label><input id="publicRadio" type="radio" name="'.helpers\PostHandler::getCreatePublic().'" value="1" checked>

		<input type="submit" value="Create!">
		</form>
		';

		return $form;
	}

	public function makeFeedback($feedbackArray)
	{
		if(is_null($feedbackArray))
		{
			return;
		}

		$feedback .= '<ol>';

		if(in_array($this->pollCreator->shortQuestion, $feedbackArray))
        {
            $feedback .= "<li>You must write a quesion.</li>";
        }	
		if(in_array($this->pollCreator->longQuestion, $feedbackArray))
        {
            $feedback .= "<li>Your question can't be longer than 100 characters.</li>";
        }      
        if(in_array($this->pollCreator->tooManyAnswers, $feedbackArray))
        {
            $feedback .= "<li>You can have a maximum of 10 answers for one question.</li>";
        }
        if(in_array($this->pollCreator->tooFewAnswers, $feedbackArray))
        {
            $feedback .= "<li>A question must have at least 2 answers.</li>";
        }    
        if(in_array($this->pollCreator->notPublicOrPrivate, $feedbackArray))
        {
            $feedback .= "<li>Decide if you want your poll to be public or private.</li>";
        }
        if(in_array($this->pollCreator->categoryDoesNotExist, $feedbackArray))
        {
            $feedback .= "<li>Pick a category.</li>";
        }

        return $feedback . '</ol>';

	}

}