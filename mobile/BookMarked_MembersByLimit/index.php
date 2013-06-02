<?php
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

	if(isset($_GET["pid"]) && isset($_GET["start"]) && isset($_GET["limit"]) && isset($_GET["skey"]))
	{
		$profileid		= 	$_GET["pid"];
		$start		= 	$_GET["start"];
		$limit		= 	$_GET["limit"];
		$skey		=   $_GET["skey"]; 
		$usr->BookmarkedMembersByLimit($profileid,$start,$limit,$skey);
		
	}
	else
	{
		echo '{"Message":"Error"}';
	}	
?>