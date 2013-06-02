<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');
//require_once("../classes/mailer.class.php");

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
if(isset($_GET["eid"]) && isset($_GET["pid"]) && isset($_GET["skey"]) && isset($_GET["text"]))
{
	$eid 	= $_GET["eid"];
	$pid 	= $_GET["pid"];
	$skey       =   $_GET["skey"];
	$text       =   $_GET["text"];
	$usr->addReport($eid,$pid,$skey,$text);
}
else
{
 echo "Incorrect format";
}

?>