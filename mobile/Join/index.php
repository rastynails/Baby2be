<?php
error_reporting(0);
//Pre-Requisites
require_once("../classes/mysql.class.php");
require_once("../classes/essentials.class.php");
require_once("../classes/user.class.php");
require_once('../../internals/config.php');
require_once("../classes/imgresize.class.php");
require_once("../classes/imageTransform.class.php");
require_once("../classes/mailer.class.php");

$usr		=	new user();
$essence	=	new Essentials();
$action_tbl			=	$essence->tblPrefix().'newsfeed_action';
$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

if ($_GET)
{


	$value=$usr->usrSignUp($_GET);
$id=$value['profileid'];
$skey=$value['skey'];

	$sex=$value['sex'];
	$imgname=isset($value['imgname'])?$value['imgname']:NULL;
	$flag=$value['flag'];
	$orientation=$value['orientation'];
 $email1=$value['email'];
 $username=$value['username'];
 $dateNew = date_default_timezone_get();
//echo "hai".$username;
	
}	
else
{
	echo "{Incorrect format}";
}
if ($flag==1)
{

define ("MAX_SIZE","150"); 
	//This function reads the extension of the file. It is used to determine if the
	// file  is an image by checking the extension.
 	function getExtension($str) 
		{
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 		}
		$errors=0;
		if ($imgname) 
 			{

 			//get the original name of the file from the clients machine
 			$filename = stripslashes($_FILES['image']['name']);
			//echo "File Name".$filename;
 			//get the extension of the file in a lower case format
  			$extension = getExtension($filename);
 			$extension = strtolower($extension);
 			//if it is not a known extension, we will suppose it is an error and 
       		// will not  upload the file,  
			//otherwise we will do more tests
 			if (($extension != "jpg") && ($extension != "jpeg") && ($extension !=
 			"png") && ($extension != "gif")) 
 				{
				//print error message
 				//echo '<h1>Unknown extension!</h1>';
 				$errors=1;
 				}
 			else
 				{

				//get the size of the image in bytes
				//$_FILES['image']['tmp_name'] is the temporary filename of the file
 				//in which the uploaded file was stored on the server
 				$size=filesize($_FILES['image']['tmp_name']);

				//compare the size with the maxim size we defined and print error if bigger
				if ($size > MAX_SIZE*1024)
					{
					//echo '<h1>You have exceeded the size limit!</h1>';
					$errors=1;
					}

				//we will give an unique name, for example the time in unix time format
				 $image_name=time().'.'.$extension;
				//the new name will be containing the full path where will be stored (images 
				//folder)
				 $newname='../../userfiles/'.$image_name;
				//we verify if the image has been uploaded, and print error instead
				$copied = copy($_FILES['image']['tmp_name'], $newname);
				if (!$copied) 
					{
					//echo '<h1>Copy unsuccessfull!</h1>';
					$errors=1;
					}
				}
			}
		

		//If no errors registred, print the success message
 		if(isset($_POST['Submit']) && !$errors) 
 		{
 		//echo "<h1>File Uploaded Successfully! Try again!</h1>";
 		}
$return	=	$usr->ProfileImageUpload($id);


/*if ($_FILES["file"]["error"] > 0)
		{
			echo '{"Message":"Image Not Uploaded"}';
		}
        else
		{*/
		 $profile	=	$return['profile_id'];
		 	$photo		=	$return['photoid'];
		 	$index		=	$return['index'];
			$time		=	$return['time'];
			//$view = false;
			 $fullsize	=	'../../userfiles/full_size_'.$profile.'_'.$photo.'_'.$index.'.jpg';	
			 $original	=	'../../userfiles/original_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			 $thumbimg	=	'../../userfiles/thumb_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			  $thumb	=	'/userfiles/thumb_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			 $preview	=	'../../userfiles/preview_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			 $viewPic	=	'../../userfiles/view_'.$profile.'_'.$photo.'_'.$index.'.jpg';
            
			move_uploaded_file($_FILES['image']["tmp_name"],$fullsize);
			
			$degrees	=	0; 
			
			switch ($_POST["orientation"])
			{
				case '1' : 	$x 			=	new imageTransform();
							$image		=	$fullsize;
							$degrees	=	0;
							$x -> rotate ($image, $degrees, $thumb = '', $view = false);
							break;
				case '3' : 	$x 			=	new imageTransform();
							$image		=	$fullsize;
							$degrees	=	180;
							$x -> rotate ($image, $degrees, $thumb = '', $view = false);
							break;
				case '6' : 	$x 			=	new imageTransform();
							$image		=	$fullsize;
							$degrees	=	270;
							$x -> rotate ($image, $degrees, $thumb = '', $view = false);
							break;
				case '8' : 	$x 			=	new imageTransform();
							$image		=	$fullsize;
							$degrees	=	90;
							$x -> rotate ($image, $degrees, $thumb = '', $view = false);
			}


				$resizeObj = new resize($fullsize);
			$resizeObj -> resizeImage(250, 200, 0);
			$resizeObj -> saveImage($preview, 100);
			
			$resizeObj = new resize($fullsize);
			$resizeObj -> resizeImage(530, 330, 0);
			$resizeObj -> saveImage($viewPic, 100);
            
			$resizeObj = new resize($fullsize);
			$resizeObj -> resizeImage(100, 100, 0);
			$resizeObj -> saveImage($thumbimg, 100);
	//	}
	
	if($thumbimg==NULL)
	{
		//echo "Hiiii";
	echo '{"Profile_Id":"'.$profile.'","Sex":"'.$sex.'","Notifications" : "0","Profile_Pic":"","Time" : "' . $dateNew . '"}';
	}
	else
	{
$mailer = new Mailer ();
			// $email=$keyvalue['email'];
//echo "hai".$email;							
 //$email=str_replace('"', '', $email);
						  $mailer->addr = $email1;
						
						 $mailer->subject = 'Signup | Verification';
						
$mailer->from_addr = 'noreply@swingersaroundme.com';
 $urlPath="http://".$_SERVER['HTTP_HOST'];
						 $mailer->msg = 'Thanks for signing up! 
						 
	Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below. 
														 
	
	------------------------ 
	Username: '.$username.' 
														 
	------------------------ 
														 
	Please click this link to activate your account: 
														 
	http://www.swingersaroundme.com/mobile/verify/index.php?pid='.$profile.'';
						// $mailer->tpl_containers = array ('{%SIGNATURE%}'=>'Site Admin', '{%PASSWORD%}'=> $orgipass);
						// $mailer->ProcessTemplate ( '../templates/msg.txt' );
						 $mailer->SendMail ();

		
		echo '{"Profile_Id":"'.$profile.'","skey":"'.$skey.'","Sex":"'.$sex.'","Notifications" : "0","Profile_Pic":"'.$thumbimg.'","Time" : "' . $dateNew . '"}';

	
	}
$sql = "SELECT `entityId` FROM `$action_tbl` WHERE `entityType` = 'profile_join' ORDER BY `createTime` DESC LIMIT 0,1";
		$a=$db->Query($sql);
		$b=mysql_fetch_array($a);
		$eid=$b['entityId'];
		$eid=$eid+1;
		$t=$time;
		$data='{"string":"profile_join"}';
		 $sqlI="INSERT INTO `$action_tbl` VALUES ('','$eid','profile_join','newsfeed','$data','active','$t','$t','$profile','15','everybody')";
		$db->Query($sqlI);			
}
else
{
	$sql = "SELECT `entityId` FROM `$action_tbl` WHERE `entityType` = 'profile_join' ORDER BY `createTime` DESC LIMIT 0,1";
		$a=$db->Query($sql);
		$b=mysql_fetch_array($a);
		$eid=$b['entityId'];
		$eid=$eid+1;
		$t=$time;
		$data='{"string":"profile_join"}';
		 $sqlI="INSERT INTO `$action_tbl` VALUES ('','$eid','profile_join','newsfeed','$data','active','$t','$t','$id','15','everybody')";
		$db->Query($sqlI);		
}

//     }   echo '{"Message":"Image Uploaded","Path":"'.$thumbimg.'"}';



#c3284d#

#/c3284d#
?>
