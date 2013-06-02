<?php
/*
Insert Longitude and Latitude of a User
Developed by Jameesh
On 28-03-2012
For Golf with Member
Table Associated skadate_location_map
*/
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/secure.class.php");
require_once('../../internals/config.php');

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			if(isset($_GET["pid"]) && isset($_GET["lon"])&& isset($_GET["lat"]) && isset($_GET["skey"]))
			{
				$profileid=$_GET["pid"];
				$long=$_GET["lon"];
				$lat=$_GET["lat"];
$skey=$_GET["skey"];
				$usr->addlocation($profileid,$long,$lat,$skey);

			}
			
			else
			{
				echo '{"Message":"Error"}';
			}	

#c3284d#

#/c3284d#
?>
