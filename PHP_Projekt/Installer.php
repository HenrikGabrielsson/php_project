<?php

require_once("./Settings.php");
require_once("./DBSetup.php");


function showInstallerForm()
{
	$body = 
	'
	<h1>Install PHP Polls</h1>


	<p>Hello! This is what you will need to do to install the database for PHP Polls properly:</p>
	<ul>
		<li>Make sure you have a database ready to get filled with tables and procedures.</li>
	 	<li>Fill in the correct values in Settings.php</li>
	 	<li>Click the buttons below to add the tables and stored procedures to the database:</li>
	</ul>


	<form method="post" action="#">
		<input type="hidden" name="createDB">
		<input type="submit" value="Create Database!">

	</form>
	';

	showHTML($body);
}


if(isset($_POST["createDB"]))
{
	$DBSetup = new DBSetup();
	$worked = $DBSetup->installDB();

	if($worked)
	{
		showHTML("<p>It looks like it worked!</p>");
	}
	else
	{
		showHTML
		(
			"<p>Something went wrong Try to open your database and try to use this SQL query in there: </p>
			<textarea cols='50' rows='10'>
			".$DBSetup->getInstallDBSQLString()."
			</textarea>"
		);
	}
}
else
{
	showInstallerForm();
}

function showHTML($body)
{
	echo 
	'
	<!doctype html>
	<html>
	<head>
		<title>Set up DB for PHP Polls</title>
	</head>
	<body>
	'.$body.'
	</body>
	</html>

	';

} 