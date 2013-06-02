<?php
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');


$usr		=	new user();
$secure     =   new secure();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

	if(isset($_GET["sid"])&& isset($_GET["rid"]) && isset($_GET["cid"])&& isset($_GET["skey"]) )
	{
		//$id			= 	$_GET["id"];
		$sid		= 	$_GET["sid"];
		$rid		= 	$_GET["rid"];
		$cid		= 	$_GET["cid"];
		$skey		=	$_GET["skey"];

		$usr->DeleteMessageBySender($sid,$rid,$cid,$skey);
	}
	else
	{
		echo '{"Message":"Error"}';
	}	
?>