<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

switch($_GET["param"])
{
	case "login" :
		if(isset($_GET["u"]) && isset($_GET["p"]) && isset($_GET["t"]))
		{
			$username 	= 	$_GET["u"];
			$password 	= 	$_GET["p"];
			$timestamp  = 	$_GET["t"];
			$usr->usrSignIn($username,$password,$timestamp,$essence);
		}
		else
		{
			echo '{"profile_id":"NULL"}';
		}	
		break;
}		
?>