<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	if(isset($_GET["id"]))
		{
			$id		= 	$_GET["id"];
			$vid		= 	$_GET["vid"];
			$usr->UsrDetailNew($id,$vid);
		}
		else
		{
			echo '{"Message":"Incorrect ID Supplied"}';
		}	
?>