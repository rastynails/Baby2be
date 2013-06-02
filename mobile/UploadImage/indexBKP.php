<?php

//ini_set("memory_limit","10M");    //Pre-Requisites
    require_once("../classes/mysql.class.php");
    require_once("../classes/essentials.class.php");
    require_once("../classes/user.class.php");
    require_once('../../internals/$config.php');
    require_once("../classes/imgresize.class.php");
	require_once("../classes/secure.class.php");
    require_once("../classes/imageTransform.class.php");
    
    $usr		=	new user();
    $essence	=	new Essentials();
	$secure     =   new secure();
	$id =$_POST["id"];
	$skey=$_POST["skey"];
	$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
    $db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	//define a maxim size for the uploaded images in Kb
 	
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

	//This variable is used as a flag. The value is initialized with 0 (meaning no 
	// error  found)  
	//and it will be changed to 1 if an errro occures.  
	//If the error occures the file will not be uploaded.
 	$errors=0;
	//checks if the form has been submitted
 	if(isset($_POST['Submit'])) 
 		{
 		//reads the name of the file the user submitted for uploading
 		$image=$_FILES['image']['name'];
 		//if it is not empty
 		if ($image) 
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
				$newname='../../$userfiles/'.$image_name;
				//we verify if the image has been uploaded, and print error instead
				$copied = copy($_FILES['image']['tmp_name'], $newname);
				if (!$copied) 
					{
					//echo '<h1>Copy unsuccessfull!</h1>';
					$errors=1;
					}
				}
			}
		}

		//If no errors registred, print the success message
 		if(isset($_POST['Submit']) && !$errors) 
 		{
 		//echo "<h1>File Uploaded Successfully! Try again!</h1>";
 		}

if($_POST["id"])
{
          $return	=	$usr->ImageUpload($_POST["id"]);
        
        
        	$profile	=	$return['profile_id'];
			$photo		=	$return['photoid'];
			$index		=	$return['index'];
			$username	=	$return['username'];
			$time		=	time();
			//$view = false;
			$fullsize	=	'../../$userfiles/full_size_'.$profile.'_'.$photo.'_'.$index.'.jpg';	
			$original	=	'../../$userfiles/original_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			$thumbimg	=	'../../$userfiles/thumb_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			$preview	=	'../../$userfiles/preview_'.$profile.'_'.$photo.'_'.$index.'.jpg';
			$viewPic	=	'../../$userfiles/view_'.$profile.'_'.$photo.'_'.$index.'.jpg';
            
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
			$optionArray=$resizeObj -> resizeImage1(250, 200, 0);
			$optimalWidth  = $optionArray['ow'];
				//echo "<br>Optimal Width".$optimalWidth;
				$optimalHeight = $optionArray['oh'];
				//echo "<br>Optimal Height".$optimalHeight;	
				$optionArray=$resizeObj -> resizeImage($optimalWidth, $optimalHeight, 0);
			$resizeObj -> saveImage($preview, 100);
			
			$resizeObj = new resize($fullsize);
			$optionArray=$resizeObj -> resizeImage1(530, 330, 0);
			$optimalWidth  = $optionArray['ow'];
				//echo "<br>Optimal Width".$optimalWidth;
				$optimalHeight = $optionArray['oh'];
				//echo "<br>Optimal Height".$optimalHeight;		
				$optionArray=$resizeObj -> resizeImage($optimalWidth, $optimalHeight, 0);
			$resizeObj -> saveImage($viewPic, 100);
            
			$resizeObj = new resize($fullsize);
			$optionArray=$resizeObj -> resizeImage1(100, 100, 0);
			$optimalWidth  = $optionArray['ow'];
				//echo "<br>Optimal Width".$optimalWidth;
				$optimalHeight = $optionArray['oh'];
				//echo "<br>Optimal Height".$optimalHeight;	
				$optionArray=$resizeObj -> resizeImage($optimalWidth, $optimalHeight, 0);
			$resizeObj -> saveImage($thumbimg, 100);
	}
       // unlink($newname);
		echo '{"Status":"Live","Message":"Image Uploaded","Path":"'.$thumbimg.'"}';
		$sql = "SELECT `entityId` FROM `$action_tbl` WHERE `entityType` = 'photo_upload' ORDER BY `createTime` DESC LIMIT 0,1";
		$a=$db->Query($sql);
		$b=mysql_fetch_array($a);
		$eid=$b['entityId'];
		$eid=$eid+1;
		$t=time();
		$data='{"string":"photo_upload","content":{"photo_url":"http:\/\/beta.sodtechnologies.com\/4clique\/member\/photos\/'.$username.'\/'.$eid.'","photo_src":"http:\/\/beta.sodtechnologies.com\/4clique\/$userfiles\/'.$thumbimg.'"}}';
		$sqlI="INSERT INTO `$action_tbl` VALUES ('','$eid','photo_upload','photo','$data','active','$t','$t','$profile','15','everybody')";
		$db->Query($sqlI);
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
    
?>