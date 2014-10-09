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

		$form = 
		'<form id="createPollForm" action="?'.helpers\GetHandler::getCreate().'" method="post">

		<label for="createQuestion">Question: </label><input type="text" name="'.helpers\PostHandler::getCreateQuestion().'" id="createQuestion">
		<label for="createAnswer">Answer: </label><input type="text" id="createAnswer" name="'.helpers\PostHandler::getCreateAnswer().'">

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