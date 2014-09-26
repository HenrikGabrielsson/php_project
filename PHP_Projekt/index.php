<?php

require_once("controller/NavigationController.php");
require_once("view/HTMLView.php");


$htmlView  = new \view\HtmlView();
$navController = new \controller\NavigationController();

$navController->doControl();
$body = $navController->getBody();
$title $navController->getTitle();

$htmlView->showHTML($title, $body);

