<?php 

namespace view;

class NavigationView
{

    public function getPageController()
    {
        return $_GET[helpers\GetHandler::$VIEW];
    }

    public function getId()
    {
        return $_GET[helpers\GetHandler::$ID];
    }
}