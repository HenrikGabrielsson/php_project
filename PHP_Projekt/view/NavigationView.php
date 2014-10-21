<?php 

namespace view;

class NavigationView
{
	//hämta aktuell vy
    public function getPageController()
    {
        return $_GET[helpers\GetHandler::$VIEW];
    }

    //hämta aktuellt id
    public function getId()
    {
        return $_GET[helpers\GetHandler::$ID];
    }
}