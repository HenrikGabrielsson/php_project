<?php

namespace model;

class Category
{
	private $repo;

	private $id;			//kategorins id
	private $categoryName;	//Kategorins namn

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