<?php 

namespace controller;

require_once("./view/HomePageView.php");
require_once("./view/HTMLView.php");

class HomeController
{
	private $htmlView; 
	private $homeView;

	public function __construct($htmlView)
	{
		$this->htmlView = $htmlView;
		$this->homeView = new \view\HomePageView();
	}

	public function getContent($id, $loggedIn)
	{
		$title = $this->homeView->getTitle();
		$body = $this->homeView->getBody($loggedIn);

		$this->htmlView->showHTML($title, $body);
	}
}