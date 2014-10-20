<?php

namespace controller;

require_once("./view/CategoryView.php");

require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CategoryRepo.php");

class CategoryController
{

	private $htmlView;
	private $categoryView; 

	private $categoryRepo;
	private $pollRepo;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->categoryRepo = new \model\repository\CategoryRepo();
		$this->pollRepo = new \model\repository\PollRepo();
	}

	public function getContent($id)
	{
		//hämta alla polls i denna kategori.
		$polls = $this->pollRepo->getAllPollsInCategory($id);
		$category = $this->categoryRepo->getCategoryById($id);

		//kategori valdes inte/hittades inte
		if($category === false)
		{
			$this->htmlView->showErrorPage();
			die();
		}
		
		$this->categoryView = new \view\CategoryView($category, $polls);

		//hämta titel och body och visa i htmlView.
		$title = $this->categoryView->getTitle();
		$body = $this->categoryView->getBody();
		$this->htmlView->showHTML($title, $body);
	}
}