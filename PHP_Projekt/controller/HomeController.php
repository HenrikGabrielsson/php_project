<?php 

namespace controller;

require_once("./view/HTMLView.php");

class HomeController
{
	private $htmlView; 

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
	}

	public function getContent()
	{
		$this->htmlView->showHTML($title, $body);
	}
}