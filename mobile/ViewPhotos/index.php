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
	if(isset($_GET["id"]) && isset($_GET["skey"]))
		{
			$pid		= 	$_GET["id"];
			$skey   =   $_GET["skey"]; 
			//$vid		=	$_GET["vid"];
			$vid = isset($_GET["vid"])?$_GET["vid"]:NULL;
			//echo "HI".$vid;
			if ($vid==NULL)
			{
				$vid=$pid;
			}
			else
			{
				$vid =$_GET["vid"];
			}
			//echo "hiiii".$vid;
			$usr->ViewPhotos($pid,$skey,$vid);
		}
		else
		{
			echo '{"Message":"Incorrect ID Supplied"}';
		}	
?>