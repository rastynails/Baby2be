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
if (isset($_GET["pid"]) && isset($_GET["tid"]) && isset($_GET["text"]) && isset($_GET["skey"]))
{
	$pid	= $_GET["pid"];
	$tid	= $_GET["tid"];
	$text	= $_GET["text"];
	$skey	= $_GET["skey"];
	$usr->replyTopicForum($pid,$tid,$text,$skey);
}
else
{
	echo "{Incorrect format}";
}

?>