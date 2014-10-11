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

	public function getSidebarContent()
	{
		$categories = $this->repo->getAllCategories();


		$sidebar = 
		'
		<p><a href="?'.helpers\GetHandler::getView().'='.helpers\GetHandler::getViewCreatePoll().'">Create a poll!</a></p>
		<h2>Categories</h2>
		<ul id="sidebarList">
		';
		foreach($categories as $category)
		{
			$link = "?". helpers\GetHandler::getView() ."=" .helpers\GetHandler::getViewCategory() ."&". helpers\GetHandler::getId() ."=". $category->getId();
			$sidebar .=
			'<li><a href="'.$link.'">'.$category->getCategoryName().'</a></li>';
		}

		$sidebar .= "</ul>";
		return $sidebar;
	}
}