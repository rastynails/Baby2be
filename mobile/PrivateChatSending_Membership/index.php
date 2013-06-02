<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

	if(isset($_GET["sid"]) && isset($_GET["rid"]) && isset($_GET["msg"]))
	{
		$sid		= 	$_GET["sid"];
		$rid		= 	$_GET["rid"];
		$msg		= 	$_GET["msg"];
			$usr->PrivateChatSending_Membership($sid,$rid,$msg);
	}
	else
	{
		echo '{"Message":"Error"}';
	}	
?>