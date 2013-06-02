<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	
	if(isset($_GET["pid"]) && isset($_GET["eid"]) && isset($_GET["text"]))
	{
		$pid	=	$_GET["pid"];
		$eid	=	$_GET["eid"];
		$text	=	$_GET["text"];
		$usr->addClassifiedComment($pid,$eid,$text);
	}
	else
	{
		echo '{"Message":"Incorrect Format"}';
	}	
?>