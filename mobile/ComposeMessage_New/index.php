<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

	if(isset($_GET["sender"]) && isset($_GET["recipient"]) && isset($_GET["msg"]))
	{
		$sender		= 	$_GET["sender"];
		$recipient	= 	$_GET["recipient"];
		$sub		= 	$_GET["sub"];
		$msg		= 	$_GET["msg"];
			$usr->ComposeMessageNew($sender,$recipient,$sub,$msg);
	}
	else
	{
		echo '{"Message":"Error"}';
	}	
?>