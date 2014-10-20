<?php

namespace view;

class SidebarView
{
	private $categories;

	public function __construct($categories)
	{
		$this->categories = $categories;
	}

	public function getSidebarContent($login)
	{
		//om användaren är inloggad så visas en create poll-länk
		$createPoll = "";
		if($login->getIsLoggedIn())
		{
			$createPoll= '<p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWCREATEPOLL.'">Create a poll!</a></p>';
		}

		//om användaren är admin så visas en länk till report-sidorna.
		$reportLists = "";
		if($login->getIsAdmin())
		{
			$reportLists = '<p><a href="?'.helpers\GetHandler::$VIEW.'='.helpers\GetHandler::$VIEWREPORT.'">Check reported items</a></p>';
		}


		$sidebar = 
	
		$createPoll.
		$reportLists.
		'<h2>Categories</h2>
		<ul id="sidebarList">
		';
		foreach($this->categories as $category)
		{
			$link = "?". helpers\GetHandler::$VIEW ."=" .helpers\GetHandler::$VIEWCATEGORY ."&". helpers\GetHandler::$ID ."=". $category->getId();
			$sidebar .=
			'<li><a href="'.$link.'">'.$category->getCategoryName().'</a></li>';
		}

		$sidebar .= "</ul>";
		return $sidebar;
	}
}