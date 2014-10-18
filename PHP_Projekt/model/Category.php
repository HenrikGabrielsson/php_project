<?php

namespace model;

class Category
{
	private $repo;

	private $id;
	private $categoryName;

	public function __construct($id, $name)
	{
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