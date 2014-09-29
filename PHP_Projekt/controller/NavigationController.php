<?php

namespace controller;

require_once("view/NavigationView.php");
require_once("controller/HomeController.php");

class NavigationController
{
    
    private $navView;
    private $currentController;     //controller som ska användas.
    
    
    public function __construct()
    {
        $this->navView = new \view\NavigationView();
    }
    
    //kollar url och anropar sedan en lämplig 
    public function doControl()
    {
        switch ($this->navView->getPageController())
        {
            case "poll":
                $this->currentController = new \controller\PollController();
                break;
            case "user":
                $this->currentController = new \controller\UserController();
                break;
            case "category":
                $this->currentController = new \controller\CategoryController();
                break;
            default:
                $this->currentController = new \controller\HomeController();
        }
        
        
    }
    
    public function getBody()
    {
        return "test";
    }
    
    public function getTitle()
    {
        return "test";
    }
    
    
}
