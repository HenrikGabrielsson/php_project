<?php

require_once("controller/navigationController.php");
require_once("view/htmlView.php");

$navController = new \controller\NavigationController();
$htmlView = new \view\htmlView.php();

$htmlContent = $navController->doGetRightPage();

$htmlView->showPage($htmlContent);

