<?php

namespace view;

require_once("./model/Category.php");
require_once("./model/Poll.php");
require_once("./view/helpers/GetHandler.php");

class CategoryView
{

	private $category;
	private $polls;

	public function __construct($category, $polls)
	{
		$this->category = $category;
		$this->polls = $polls;
	}

	public function getTitle()
	{
		return $this->category->getCategoryName();
	}

	public function getBody()
	{

		$body = 
		'<h1>'.$this->category->getCategoryName().'</h1>
		<p>There are currently '.count($this->polls).' polls in this category.</p>';;

		
		if(count($this->polls) > 0)
		{
			$pollList = '<ul>';
			foreach($this->polls as $poll)
			{
				$pollList .= 
				'<li><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWPOLL.'&'.helpers\GetHandler::$ID.'='.$poll->getId().'">
				'.$poll->getQuestion().'</a></li>';
			}
			$pollList .= '</ul>';
		}
		

		return $body . $pollList;
	}
}