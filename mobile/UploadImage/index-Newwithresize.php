

<?php

/*
PHP Code to resize image
Developer 	:	Jameesh
Date 		:	27-3-2012
input 		:	image name as C:/wamp/www/skadate9/$userfiles/full_size_13_2_50.jpg
*/
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/$config.php');
require_once("../classes/imgresize.class.php");
require_once("../classes/imageTransform.class.php");
$usr		=	new user();
$essence	=	new Essentials();
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
		if ($_POST["img"])
		{
			$imgname = $_POST["img"];
				//echo "Image Name :".$imgname;
				//print_r (getimagesize($imgname));
		list($width,$height)	=	getimagesize($imgname);
		//echo "<br>".$width."<br>";
		//echo $height;
		list($thumb,$prof,$phot,$ind) = explode("_", $imgname);
		$result = array('photoid'=>$phot, 'index'=>$ind, 'profile_id'=>$prof);
		//print_r($result);
			$profile	=	$result['profile_id'];
			$photo		=	$result['photoid'];
			$index		=	$result['index'];
			//echo "<br>".$profile;
			//echo "<br>".$photo;
			//echo "<br>".$index;
			$time		=	time();
			//$temp='../../$userfiles/'.$imgname;
			//echo $temp;
			//$temp='../../$userfiles/full_size_'.$profile.'_'.$photo.'_'.$index;
		$fullsize	=	'../../$userfiles/full_size_'.$profile.'_'.$photo.'_'.$index;	
		$original	=	'../../$userfiles/original_'.$profile.'_'.$photo.'_'.$index;
		$thumbimg	=	'../../$userfiles/thumb_'.$profile.'_'.$photo.'_'.$index;
		$preview	=	'../../$userfiles/preview_'.$profile.'_'.$photo.'_'.$index;
		$view		=	'../../$userfiles/view_'.$profile.'_'.$photo.'_'.$index;
		//echo $_FILES["file"]["tmp_name"];
		//echo $temp;
		//echo $imgname;
		rename($imgname,$fullsize);
			
			$resizeObj = new resize($fullsize);
			$newwidth=200;
			$newheight=($height/$width)*$newwidth;
			//$tmp=imagecreatetruecolor($newwidth,$newheight);
			$resizeObj -> resizeImage($newwidth, $newheight, 0);
			$resizeObj -> saveImage($preview, 100);
			
			$resizeObj = new resize($fullsize);
			$newwidth=530;
			$newheight=($height/$width)*$newwidth;
			//$tmp=imagecreatetruecolor($newwidth,$newheight);
			$resizeObj -> resizeImage($newwidth, $newheight, 0);
			$resizeObj -> saveImage($view, 100);
            
			$resizeObj = new resize($fullsize);
			$newwidth=100;
			$newheight=($height/$width)*$newwidth;
			//$tmp=imagecreatetruecolor($newwidth,$newheight);
			$resizeObj -> resizeImage($newwidth, $newheight, 0);
			$resizeObj -> saveImage($thumbimg, 100);
		}
		else
		{ echo '{"Message":"No images"}';
		exit(0);}
?>