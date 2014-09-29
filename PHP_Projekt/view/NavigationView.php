<?php 

namespace view;

class NavigationView
{

    public function getPageController()
    {
        if(isset($_GET["wantedView"]))
        {
            return $_GET["wantedView"];
        }
    }
}