<?php 

namespace view;

require_once("./view/helpers/GetHandler.php");

class NavigationView
{

    public function getPageController()
    {
        return $_GET[helpers\GetHandler::getView()];
    }

    public function getId()
    {
        return $_GET[helpers\GetHandler::getId()];
    }
}