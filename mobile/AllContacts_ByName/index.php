<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$secure     =   new secure();
if(isset($_GET["id"]) && isset($_GET["skey"]) && isset($_GET["name"]))
{
	$id=$_GET["id"];
	$skey=$_GET["skey"];
	$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
$name = $_GET["name"];
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
$usr->allContactsByName($name);
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
}
else
{
	echo '{"Message":"incorrect format"}';
}


?>
