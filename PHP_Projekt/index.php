<?php

require_once("controller/LoginController.php");
require_once("controller/NavigationController.php");

require_once("view/HTMLView.php");


$htmlView = new \view\HTMLView();


$loginController = new \controller\LoginController($htmlView);
$loginController->checkForLogin();

$navController = new \controller\NavigationController($htmlView);
$navController->getPage();