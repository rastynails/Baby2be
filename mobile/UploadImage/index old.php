<?php
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/$config.php');
require_once("../classes/imgresize.class.php");
require_once("../classes/imgresize.class.php");

$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

if($_POST)
{
	$return	=	$usr->ImageUpload($_POST["id"]);
	print_r($return);
	if ($_FILES["file"]["error"] > 0)
		{
			echo '{"Message":"Image Not Uploaded"}';
		}
	  else
		{
			$profile	=	$return['profile_id'];
			$photo		=	$return['photoid'];
			$index		=	$return['index'];
			$time		=	time();
			
			$fullsize	=	'../../$userfiles/full_size_'.$profile.'_'.$photo.'_'.$index.'.jpg';	
			$original	=	'../../$userfiles/original_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			$thumb		=	'../../$userfiles/thumb_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			$preview	=	'../../$userfiles/preview_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			$view		=	'../../$userfiles/view_'.$profile.'_'.$photo.'_'.$index.'.jpg';
		
			move_uploaded_file($_FILES["file"]["tmp_name"],$fullsize);
			
			$resizeObj = new resize($fullsize);
			$resizeObj -> resizeImage(250, 200, 0);
			$resizeObj -> saveImage($preview, 100);
			
			$resizeObj = new resize($fullsize);
			$resizeObj -> resizeImage(530, 330, 0);
			$resizeObj -> saveImage($view, 100);
	
			$resizeObj = new resize($fullsize);
			$resizeObj -> resizeImage(100, 100, 0);
			$resizeObj -> saveImage($thumb, 100);
		}
			echo '{"Message":"Image Uploaded","Path":"'.$thumb.'"}';
}
?>
