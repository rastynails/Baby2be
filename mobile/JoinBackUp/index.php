<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/config.php');
require_once("../classes/mailer.class.php");
require_once("../classes/secure.class.php");

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

if ($_GET)
{
	$usr->usrSignUp($_GET);
}
else
{
	echo "{Incorrect format}";
}
?>