<?php

require_once("controller/NavigationController.php");
require_once("view/HTMLView.php");


$htmlView = new \view\HTMLView();


$navController = new \controller\NavigationController($htmlView);
$navController->getPage();