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
            case \view\helpers\GetHandler::$VIEWPOLL:
                $controller = new \controller\PollController($this->htmlView);
                $controller->getContent($this->navView->getId(), \model\Login::isLoggedIn());
                break;
            case \view\helpers\GetHandler::$VIEWUSER:
                $controller = new \controller\UserController($this->htmlView);
                $controller->getContent($this->navView->getId(), \model\Login::isLoggedIn());
                break;
            case \view\helpers\GetHandler::$VIEWCATEGORY:
                $controller = new \controller\CategoryController($this->htmlView);
                $controller->getContent($this->navView->getId(), \model\Login::isLoggedIn());
                break;
            case \view\helpers\GetHandler::$VIEWREGISTER:
                $controller = new \controller\RegistrationController($this->htmlView);
                $controller->getContent($this->navView->getId(), \model\Login::isLoggedIn());
                break;
            case \view\helpers\GetHandler::$VIEWCREATEPOLL:
                $controller = new \controller\PollCreationController($this->htmlView);
                $controller->getContent($this->navView->getId(), \model\Login::isLoggedIn());
                break;
            default:
                $controller = new \controller\HomeController($this->htmlView);
                $controller->getContent($this->navView->getId(), \model\Login::isLoggedIn());
        }

        
        
    }
}
