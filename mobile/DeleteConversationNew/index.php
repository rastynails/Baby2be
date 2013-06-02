<?php
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

	if(isset($_GET["pid"]) && isset($_GET["cid"]) && isset($_GET["skey"]))
	{
		$pid	= 	$_GET["pid"];
		$cid	= 	$_GET["cid"];
		$skey   =   $_GET["skey"];
		$usr->DeleteConversationNew($pid,$cid,$skey);
	}
	else
	{
		echo '{"Message":"Error"}';
	}	
?>