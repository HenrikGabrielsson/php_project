<?php

namespace view;

require_once("./model/repo/CategoryRepo.php");
require_once("./model/Category.php");
require_once("./view/helpers/GetHandler.php");

class SidebarView
{
	private $repo;

	public function __construct()
	{
		$this->repo = new \model\repository\CategoryRepo();
	}

	public function getSidebarContent($login)
	{
		$categories = $this->repo->getAllCategories();

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
		foreach($categories as $category)
		{
			$link = "?". helpers\GetHandler::$VIEW ."=" .helpers\GetHandler::$VIEWCATEGORY ."&". helpers\GetHandler::$ID ."=". $category->getId();
			$sidebar .=
			'<li><a href="'.$link.'">'.$category->getCategoryName().'</a></li>';
		}

		$sidebar .= "</ul>";
		return $sidebar;
	}
}