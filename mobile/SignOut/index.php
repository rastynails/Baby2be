<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/signout.class.php");
require_once('../../internals/config.php');

$signout     =   new signout();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

if(isset($_GET["id"]))
		{
			
			$id = $_GET["id"];
			$signout->CheckSignOut($id);
		}
		else
		{
			echo '{"Message":"Incorrect Id"}';
		}	
		
?>