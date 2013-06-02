<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

if(isset($_GET["id"]) && isset($_GET["cid"]))
{	
	$id     = $_GET['id'];
	$cid     = $_GET['cid'];
	$usr->fetchKiss($id,$cid);
}
else
{
    echo '{"Message":"Incorrect ID Supplied"}';
}

?>