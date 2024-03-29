<?php
/*
Search Members MS2
Input given
Gender, LookingFor, AgeRangeFrom, AgeRangeTo, MilesFrom, ZIP, OnlineOnly, WithPhotoOnly
Output Desired
List of profiles each with DisplayName, Gender, Age, Place, OnlineStatus
*/
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once("../classes/search.class.php");
require_once('../../internals/config.php');


$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
$search		=	new search();
	if(isset($_GET))
		{
			
			$search->sSearchNew($_GET);
		}
		else
		{
			echo '{"Message":"Invalid Search Parameters"}';
		}	
?>