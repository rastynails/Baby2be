<?php
/* API to get friend list of logged profile
 * Created by Jameesh on 27th Nov 2012
 * for citymeetme.com
 */

require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

	if(isset($_GET["id"]) && isset($_GET["skey"]) && isset($_GET["start"]) && isset($_GET["limit"]))
	{
		
		$id			= 	$_GET["id"];
		$skey		=	$_GET["skey"];
		$start		=	$_GET["start"];
		$limit		=	$_GET["limit"];
		$usr->getfriendlist($id,$skey,$start,$limit);
	}
	else
	{
		echo '{"Message":"Error"}';
	}	
?>