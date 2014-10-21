<?php

namespace view;

class CategoryView
{

	private $category;
	private $polls;

	public function __construct($category, $polls)
	{
		$this->category = $category;

		//om det finns kategorier...annars blir polls null
		if($polls)
		{
			$this->polls = $polls;
		}
		else
		{
			$this->polls = null;
		}
	}

	/**
	*	Hämta titel
	*
	* @return 	String 	det som ska stå i titeln
	*/
	public function getTitle()
	{
		return $this->category->getCategoryName();
	}
	
	/**
	*	Hämta sidans innehåll
	* 	
	* 	@return   String 	det som ska finnas på sidans main_content
	*/
	public function getBody()
	{
		$body = 
		'<h1>'.$this->category->getCategoryName().'</h1>
		<p>There are currently '.count($this->polls).' polls in this category.</p>';;

		// om det finns några polls i denna kategori så skrivs de ut
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