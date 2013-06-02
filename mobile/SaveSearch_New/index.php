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
			$sname		=	$_GET["name"];
			$id			=	$_GET["id"];
			$url_info 	= 	parse_url($_SERVER["QUERY_STRING"]);
			$url_info['path']	=	str_replace("id=$id&","",$url_info['path']);
			$url_info['path']	=	str_replace("name=$sname&","",$url_info['path']);
			parse_str($url_info['path'], $criterion);
			$search->SaveSearch($id,$sname,$criterion);
		}
		else
		{
			echo '{"Message":"Malformatted string"}';
		}	
?>