<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
if(isset($_GET["pid"]) && isset($_GET["fid"]) && isset($_GET["skey"]))
{
	$pid 	= $_GET["pid"];
	$fid 	= $_GET["fid"];
	$skey	= $_GET["skey"];	
	$usr->sendfriendreq($pid,$fid,$skey);
}
else
{
 echo '{"Message":"Incorrect format"}';
}

?>