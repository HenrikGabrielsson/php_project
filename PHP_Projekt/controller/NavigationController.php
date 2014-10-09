<?php

namespace controller;

require_once("model/Login.php");

require_once("controller/LoginController.php");
require_once("controller/HomeController.php");
require_once("controller/PollController.php");
require_once("controller/UserController.php");
require_once("controller/CategoryController.php");
require_once("controller/RegistrationController.php");
require_once("controller/PollCreationController.php");

require_once("./view/helpers/GetHandler.php");
require_once("./view/NavigationView.php");
require_once("./view/SidebarView.php");

class NavigationController
{
    private $navView;
    private $htmlView;
    private $sidebarView;

    private $currentController;     //controller som ska användas.
    
    
    public function __construct($htmlView)
    {
        $this->htmlView = $htmlView;
        $this->navView = new \view\NavigationView();
        $this->sidebarView = new \view\sidebarView();
    }
    
    //kollar url och anropar sedan en lämplig controller 
    public function getPage()
    {
        //börjar med att lägga till innehållet i sidebar.
        $this->htmlView->setSidebarContent($this->sidebarView->getSidebarContent());

        //sedan anropas den konstruktor som lägger till innehåll i title och body.
        switch ($this->navView->getPageController())
        {
            case \view\helpers\GetHandler::getViewPoll():
                $this->currentController = new \controller\PollController($this->htmlView);
                break;
            case \view\helpers\GetHandler::getViewUser():
                $this->currentController = new \controller\UserController($this->htmlView);
                break;
            case \view\helpers\GetHandler::getViewCategory():
                $this->currentController = new \controller\CategoryController($this->htmlView);
                break;
            case \view\helpers\GetHandler::getViewRegister():
                $this->currentController = new \controller\RegistrationController($this->htmlView);
                break;
            case \view\helpers\GetHandler::getViewCreatePoll():
                $this->currentController = new \controller\PollCreationController($this->htmlView);
                break;
            default:
                $this->currentController = new \controller\HomeController($this->htmlView);
        }

        //kör Controller, skickar med id, samt en bool om användaren är inloggad eller inte.
        $this->currentController->getContent($this->navView->getId(), \model\Login::isLoggedIn());
        
    }
}
