<?php

namespace controller;

require_once("./model/LoginHandler.php");
require_once("./model/repo/CategoryRepo.php");

require_once("controller/LoginController.php");
require_once("controller/HomeController.php");
require_once("controller/PollController.php");
require_once("controller/UserController.php");
require_once("controller/CategoryController.php");
require_once("controller/RegistrationController.php");
require_once("controller/PollCreationController.php");
require_once("controller/SearchController.php");
require_once("controller/ReportListController.php");

require_once("./view/NavigationView.php");
require_once("./view/SidebarView.php");


class NavigationController
{
    private $loginHandler;
    private $navView;
    private $htmlView;
    private $sidebarView;
    private $catRepo;
    
    public function __construct($htmlView)
    {
        $this->catRepo = new \model\repository\CategoryRepo();
        $this->loginHandler = new \model\LoginHandler();
        $this->htmlView = $htmlView;
        $this->navView = new \view\NavigationView();
        $this->sidebarView = new \view\sidebarView($this->catRepo->getAllCategories());
    }
    
    //kollar url och anropar sedan en lämplig controller 
    public function getPage()
    {
        //börjar med att kolla om användaren är inloggad
        $loginController = new \controller\LoginController($this->htmlView, $this->loginHandler);
        $loginController->checkForLogin();

        //sen lägger vi till innehållet i sidebar.
        $this->htmlView->setSidebarContent($this->sidebarView->getSidebarContent($this->loginHandler));

        //sedan anropas den konstruktor som lägger till innehåll i title och body.
        switch ($this->navView->getPageController())
        {
            case \view\helpers\GetHandler::$VIEWPOLL:
                $controller = new PollController($this->htmlView);
                $controller->getContent($this->navView->getId(), $this->loginHandler);
                break;
            case \view\helpers\GetHandler::$VIEWUSER:
                $controller = new UserController($this->htmlView);
                $controller->getContent($this->navView->getId(), $this->loginHandler);
                break;
            case \view\helpers\GetHandler::$VIEWCATEGORY:
                $controller = new CategoryController($this->htmlView);
                $controller->getContent($this->navView->getId());
                break;
            case \view\helpers\GetHandler::$VIEWREGISTER:
                $controller = new RegistrationController($this->htmlView);
                $controller->getContent($this->loginHandler);
                break;
            case \view\helpers\GetHandler::$VIEWCREATEPOLL:
                $controller = new PollCreationController($this->htmlView);
                $controller->getContent($this->loginHandler);
                break;
            case \view\helpers\GetHandler::$VIEWSEARCH:
                $controller = new SearchController($this->htmlView);
                $controller->getContent();
                break;
            case \view\helpers\GetHandler::$VIEWREPORT:
                $controller = new ReportListController($this->htmlView);
                $controller->getContent($this->loginHandler);
                break;
            default:
                $controller = new HomeController($this->htmlView);
                $controller->getContent($this->loginHandler);
        }
 
    }
}