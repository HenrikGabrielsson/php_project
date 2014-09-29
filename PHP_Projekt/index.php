<?php

require_once("controller/NavigationController.php");
require_once("view/HTMLView.php");
require_once("model/repo/UserRepo.php");

$htmlView  = new \view\HtmlView();
$navController = new \controller\NavigationController();

$navController->doControl();
$body = $navController->getBody();
$title = $navController->getTitle();

$test = new \model\repository\UserRepo();
$test->getUserById(1);

$htmlView->showHTML($title, $body);

