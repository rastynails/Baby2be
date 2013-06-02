<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');
require_once("../classes/htmltostring.class.php");

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	if(isset($_GET["id"]) && isset($_GET["pid"]) && isset($_GET["skey"]))
		{
			$id		= 	$_GET["id"];
			$pid	= 	$_GET["pid"];
			$skey   =   $_GET["skey"];
			$usr->getMessageDetailsInverse($id,$pid,$skey);
		}
		else
		{
			echo '{"Message":"Incorrect ID Supplied"}';
		}	
?>