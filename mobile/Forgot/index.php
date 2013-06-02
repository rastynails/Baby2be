<?php
//Pre-Requisites
define('PASSLEN',8);
define('SITEMAIL',"admin@domain.com");

require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/password.class.php");
require_once("../classes/mailer.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

if(isset($_GET["email"]))
	{
		$fnemail		= 	$_GET["email"];
		//$pid	    = 	$_GET["pid"];
		//$skey       =   $_GET["skey"];
		$usr->resetpassword($fnemail);
		
	}
	else
	{
		echo '{"Message":"Error: Requires Valid Email"}';
	}	
?>