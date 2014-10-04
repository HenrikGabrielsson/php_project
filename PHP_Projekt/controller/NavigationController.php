<?php

namespace controller;

require_once("view/NavigationView.php");
require_once("controller/HomeController.php");
require_once("controller/PollController.php");
require_once("controller/UserController.php");
require_once("controller/CategoryController.php");

require_once("view/HTMLView.php");

class NavigationController
{
    private $pollRequest = "poll";
    private $userRequest = "user";
    private $categoryRequest = "category";

    private $navView;
    private $htmlView;

    private $currentController;     //controller som ska användas.
    
    
    public function __construct()
    {
        $this->htmlView = new \view\HTMLView();
        $this->navView = new \view\NavigationView();
    }
    
    //kollar url och anropar sedan en lämplig controller
    public function doControl()
    {
        switch ($this->navView->getPageController())
        {
            case $this->pollRequest:
                $this->currentController = new \controller\PollController($this->htmlView);
                break;
            case $this->userRequest:
                $this->currentController = new \controller\UserController($this->htmlView);
                break;
            case $this->categoryRequest:
                $this->currentController = new \controller\CategoryController($this->htmlView);
                break;
            default:
                $this->currentController = new \controller\HomeController($this->htmlView);
        }
        
        //kör Controller 
        $this->currentController->getContent($this->navView->getId());
        
    }
}
