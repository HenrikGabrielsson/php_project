<?php

namespace model;

require_once("./model/repo/CategoryRepo.php");

class Category
{
	private $repo;

	private $id;
	private $categoryName;

	public function __construct($id, $name)
	{
		$this->repo = new \model\repository\CategoryRepo();

		$this->id = $id;
		$this->categoryName = $name;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getCategoryName()
	{
		return $this->categoryName;
	}
}	