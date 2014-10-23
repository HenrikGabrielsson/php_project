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

	private $category;

	public function __construct($id)
	{
		$this->categoryRepo = new \model\repository\CategoryRepo();
		$this->pollRepo = new \model\repository\PollRepo();

		//hämta kategorin och alla polls i denna kategori.
		$this->category = $this->categoryRepo->getCategoryById($id);
		$polls = $this->pollRepo->getAllPollsInCategory($id);

		$this->categoryView = new \view\CategoryView($this->category, $polls);
	}

	//hämta innehåll till sidan
	public function getBody()
	{
		if($this->category == false)
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