<?php 

namespace view;

class NavigationView
{

    public function getPageController()
    {
        if(isset($_GET["view"]))
        {
            return $_GET["view"];
        }
        return null;
    }

    public function getId()
    {
    	if(isset($_GET["id"]))
        {
            return $_GET["id"];
        }
        return null;
    }
}