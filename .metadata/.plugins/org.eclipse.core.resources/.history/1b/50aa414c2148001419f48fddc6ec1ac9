<?php

require_once("controller/NavigationController.php");
require_once("view/HTMLView.php");

require_once("model/User.php");
require_once("model/repo/UserRepo.php");

$htmlView  = new \view\HtmlView();
$navController = new \controller\NavigationController();

$navController->doControl();
$body = $navController->getBody();
$title = $navController->getTitle();

$htmlView->showHTML($title, $body);

$repo = new \model\repository\UserRepo();
$user = new \model\User("something", "aaa@oooo.com", "pass", "salt", "2010-01-01", 1, 5);

$repo->update($user);
