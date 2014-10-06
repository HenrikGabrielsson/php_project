<?php

namespace controller;

require_once("model/Login.php");

require_once("controller/LoginController.php");
require_once("controller/HomeController.php");
require_once("controller/PollController.php");
require_once("controller/UserController.php");
require_once("controller/CategoryController.php");

require_once("view/NavigationView.php");
require_once("view/LoginView.php");

class NavigationController
{

    private $loginRequest = "login";
    private $logoutRequest = "logout";
    private $pollRequest = "poll";
    private $userRequest = "user";
    private $categoryRequest = "category";

    private $navView;
    private $htmlView;

    private $currentController;     //controller som ska användas.
    
    
    public function __construct($htmlView)
    {
        $this->htmlView = $htmlView;
        $this->navView = new \view\NavigationView();
    }
    
    //kollar url och anropar sedan en lämplig controller 
    public function getPage()
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
        
        //kör Controller, skickar med id, samt en bool om användaren är inloggad eller inte.
        $this->currentController->getContent($this->navView->getId(), \model\Login::isLoggedIn());
        
    }
}
