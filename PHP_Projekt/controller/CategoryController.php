<?php

namespace controller;

require_once("./controller/IMainContentController.php");

require_once("./view/CategoryView.php");

require_once("./model/repo/PollRepo.php");
require_once("./model/repo/CategoryRepo.php");

class CategoryController implements IMainContentController
{
	//views
	private $categoryView; 

	//repos
	private $categoryRepo;
	private $pollRepo;

	public function __construct($id)
	{
		$this->categoryRepo = new \model\repository\CategoryRepo();
		$this->pollRepo = new \model\repository\PollRepo();

		//hämta alla polls i denna kategori.
		$polls = $this->pollRepo->getAllPollsInCategory($id);
		$category = $this->categoryRepo->getCategoryById($id);

		$this->categoryView = new \view\CategoryView($category, $polls);
	}

	//hämta innehåll till sidan
	public function getBody()
	{
		//kategori valdes inte/hittades inte
		if($category === false)
		{
			return false;
		}
		return $this->categoryView->getBody();
	}

	public function getTitle()
	{
		return $this->categoryView->getTitle();
	}
}