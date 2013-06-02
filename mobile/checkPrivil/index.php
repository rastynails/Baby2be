<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/config.php');
require_once("../classes/secure.class.php");

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

if(isset($_GET["id"]) AND isset($_GET["service"]))
	{
		$service 	= 	$_GET["service"];
                $pid            =       $_GET["id"];
                
               $usr->checkprivil($service,$pid); 
	
	}
	else
	{
		echo '{"Message":"Syntax Error"}';
	}	
?>