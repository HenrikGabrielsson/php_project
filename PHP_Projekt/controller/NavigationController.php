<?php

namespace controller;

require_once("view/NavigationView.php");

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
