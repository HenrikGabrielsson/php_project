<?php

namespace view;

require_once("./model/repo/CategoryRepo.php");


class PollCreationView
{
	private $catRepo;

	public function __construct()
	{
		$this->catRepo = new \model\repository\CategoryRepo();
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

	public function getCreate($feedback)
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
		'<form id="createPollForm" action="'.$_SERVER['REQUEST_URI'].'&'.helpers\GetHandler::getCreate().'" method="post">

		<label for="createQuestion">Question: </label><input type="text" name="'.helpers\PostHandler::getCreateQuestion().'" id="createQuestion">
	
		<fieldset id="answers_fieldset">
		'.$answerFields.'
		</fieldset>

		<select name="'.helpers\PostHandler::getCreateCategory().'">
			'.$categories.'
		</select>

		<label for="privateRadio">Private: </label><input id="privateRadio" type="radio" name="'.helpers\PostHandler::getCreatePublic().'" value="0">
		<label for="publicRadio">Public: </label><input id="publicRadio" type="radio" name="'.helpers\PostHandler::getCreatePublic().'" value="1">

		<input type="submit" value="Create!">
		</form>
		';

		return $form;
	}

	public function makeFeedback()
	{
		return "i dont do anything yet";
	}

}