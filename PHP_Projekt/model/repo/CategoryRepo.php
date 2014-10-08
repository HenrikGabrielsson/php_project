<?php

namespace model\repository;

require_once("./model/repo/Repository.php");
require_once("./model/Category.php");

class CategoryRepo extends \model\repository\Repository
{


	private $categoryId = "categoryId";
	private $categoryName = "categoryName";

	public function __construct()
	{

	}

	public function getAllCategories()
	{
		//array som ska returneras
		$catArray = array();
		
		$sql = "SELECT * FROM category";
		$params = array();
		
		$this->connect();
		
		$query = $this->dbConnection->prepare($sql);
		$query->execute($params);	
		
		//hämta alla rader
		$categories = $query->fetchAll();

		//om det kom några categorier
		if($categories)
		{
			foreach($categories as $category)
			{
				//skapa alla objekt				
				$catArray[] = new \model\Category($category[$this->categoryId], $category[$this->categoryName]);
			}
			
		return $catArray;
		}
	}
}