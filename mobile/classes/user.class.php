<?php

error_reporting(0);
class user
{
	 private $username		=	"";
	 private $password		=	"";
	 private $timestamp		=	"";
	 private $email			=	"";
	 private $profile_id	=	"";
	 private $randompass	=	"";
	 
/************************************************************************
	 
  Accepts the username, password and timestamp and return the profile id
	 
*************************************************************************/


public function usrSignUp($kv)
     {

        $essence    =    new Essentials();
		
        $db         =     new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
        $db1        =     new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db2        =     new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db4        =     new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
        $profile_tbl            =    $essence->tblPrefix().'profile';
        $profile_tbl_extended   =    $essence->tblPrefix().'profile_extended';
        $profile_online         =    $essence->tblPrefix().'profile_online';
        $key_tbl                =    $essence->tblPrefix().'lang_key';
        $value_tbl              =    $essence->tblPrefix().'lang_value';
		$action_tbl				=	 $essence->tblPrefix().'newsfeed_action';
		$type_plan_tbl			=	 $essence->tblPrefix().'membership_type_plan';
		$fin_sale_tbl			=	 $essence->tblPrefix().'fin_sale';
		$mem_user_tbl			=	 $essence->tblPrefix().'membership';

        $phoneNumber 		=	isset($kv["phone_number"])?$kv["phone_number"]:NULL;
		



            /*    $uname 				=	str_replace('"','',isset($kv["username"])?$kv["username"]:NULL);
              
		$sex 				=	str_replace('"','',isset($kv["sex"])?$kv["sex"]:"0");
            
		$match_sex 			=	str_replace('"','',isset($kv["match_sex"])?$kv["match_sex"]:"0");
		$email				=	str_replace('"','',isset($kv["email"])?$kv["email"]:NULL);
		$password			=	str_replace('"','',isset($kv["password"])?$kv["password"]:NULL);
		$birthdate			=	str_replace('"','',isset($kv["birthdate"])?$kv["birthdate"]:NULL);
		$match_agerange		        =	str_replace('"','',isset($kv["match_agerange"])?$kv["match_agerange"]:NULL);
		$country			=	str_replace('"','',isset($kv["country_id"])?$kv["country_id"]:NULL);
		$state_id			=	str_replace('"','',isset($kv["state_id"])?$kv["state_id"]:NULL);
		$city_id			=	str_replace('"','',isset($kv["city_id"])?$kv["city_id"]:NULL);
		$zip				=	str_replace('"','',isset($kv["zip"])?$kv["zip"]:NULL);
		$headline			=	str_replace('"','',isset($kv["headline"])?$kv["headline"]:NULL);
		$generalDescription	        =	str_replace('"','',isset($kv["general_description"])?$kv["general_description"]:NULL);
		$join_ip1			=	isset($kv["join_ip"])?$kv["join_ip"]:NULL;
		$join_ip 			= 	sprintf("%u", ip2long(long2ip(ip2long($join_ip1))));
		//$join_ip 			= 	sprintf("%u", ip2long($join_ip1));
		$image				=	$_FILES['image']['name'];*/








                $uname 				=	isset($kv["username"])?$kv["username"]:NULL;trim($uname,'"'); 
                $uname			= stripslashes($uname);
			$uname 		 	= str_replace('"','',$uname); 
              
		$sex 				=	isset($kv["sex"])?$kv["sex"]:"0";trim($sex,'"');
		
                
		$match_sex 			=	isset($kv["match_sex"])?$kv["match_sex"]:"0";trim($match_sex,'"');
		$email				=	isset($kv["email"])?$kv["email"]:NULL;trim($email,'"');
                
		$password			=	isset($kv["password"])?$kv["password"]:NULL;trim($password,'"');
            $password			= stripslashes($password);
			$password 		 	= str_replace('"','',$password);    
		$birthdate			=	isset($kv["birthdate"])?$kv["birthdate"]:NULL;trim($birthdate,'"');
              $birthdate			= stripslashes($birthdate);
			$birthdate 		 	= str_replace('"','',$birthdate);   
		$match_agerange		        =	isset($kv["match_agerange"])?$kv["match_agerange"]:NULL;trim($match_agerange,'"');
               $match_agerange			= stripslashes($match_agerange);
			$match_agerange 		 	= str_replace('"','',$match_agerange);  
		$country			=	isset($kv["country_id"])?$kv["country_id"]:NULL;trim($country,'"');
             $country			= stripslashes($country);
			$country 		 	= str_replace('"','',$country);    
		$state_id			=	isset($kv["state_id"])?$kv["state_id"]:NULL;trim($state_id,'"');
               $state_id			= stripslashes($state_id);
			$state_id 		 	= str_replace('"','',$state_id); 
		$city_id			=	isset($kv["city_id"])?$kv["city_id"]:NULL;trim($city_id,'"');
             $city_id			= stripslashes($city_id);
			$city_id 		 	= str_replace('"','',$city_id);    
		$zip				=	isset($kv["zip"])?$kv["zip"]:NULL;trim($zip,'"');
                
		$headline			=	isset($kv["headline"])?$kv["headline"]:NULL;trim($headline,'"');
               $headline			= stripslashes($headline);
			$headline 		 	= str_replace('"','',$headline);  
		$generalDescription	        =	isset($kv["general_description"])?$kv["general_description"]:NULL;trim($generalDescription,'"');
		$generalDescription			= stripslashes($generalDescription);
			$generalDescription 		 	= str_replace('"','',$generalDescription); 
		$join_ip1			=	isset($kv["join_ip"])?$kv["join_ip"]:NULL;
                
		$join_ip 			= 	sprintf("%u", ip2long(long2ip(ip2long($join_ip1))));
                
		//$join_ip 			= 	sprintf("%u", ip2long($join_ip1));
		$image				=	$_FILES['image']['name'];

/*if ($zip>0)
 {
 $sqlCHK ="SELECT *
FROM `$zip_tbl`
WHERE `zip` =$zip
LIMIT 1";
$sqlChkR=$db->Query($sqlCHK);
$sqlChkExe = mysql_fetch_array($sqlChkR);
$state_id = $sqlChkExe['state_id'];
$city_id = $sqlChkExe['city_id'];
 }*/

$sqlnew = "SELECT `value` FROM `skadate_config` where `name`='default_membership_type_id'";
$sqlcheck = $db->Query($sqlnew);
$sqlcheckE = mysql_fetch_array($sqlcheck);
$value1 = $sqlcheckE['value'];
$value1			= stripslashes($value1);
$value1 		 	= str_replace('"','',$value1); 
 

		// echo "hiii".$image;
			 $orientation=isset($kv['orientation'])?$kv['orientation']:NULL;
        	$keyvalue = array('join_stamp' => time(),'activity_stamp' => time(),'membership_type_id'=>$value1,'language_id'=>1);
			if($phoneNumber!=NULL)
			{
				$kv = array_diff($kv,array($kv['phone_number']));
			}
			else
			{
				$phoneNumber = "NULL";
			}
			foreach ($kv as $key => $value)
			{
				  $keyvalue[$key] = stripcslashes($value);
			}
			$email			= stripslashes($email);
			$email 		 	= str_replace('"','',$email); 
  			$sqlCHK 		= "SELECT * from $profile_tbl where email='$email'";
			$sqlChkR		= $db->Query($sqlCHK);
			$sqlChkExe 		= mysql_fetch_array($sqlChkR);
			
                        $sqlEmailId = $sqlChkExe['email']; 
                        if ($sqlEmailId != NULL) 
                        { 
                        echo '{"Profile_id":"NULL","Message":"Email id exists"}';
			exit();	
                         }
                        


			$sqlChkResult 	= $sqlChkExe['profile_id'];
			//echo $sqlChkResult;
			if($sqlChkResult==NULL)
			{
        	if(count($keyvalue))
        	{
                        
			$t			=	time();
 			$sqlInsert	=	"INSERT INTO $profile_tbl (profile_id,username,email,sex,match_sex,birthdate,country_id,state_id,city_id,zip,password,join_stamp,activity_stamp,membership_type_id,has_photo,language_id,headline,general_description,match_agerange,join_ip) VALUES ('','$uname','$email','$sex','$match_sex','$birthdate','$country','$state_id','$city_id','$zip','$password','$t','$t','$value1','y','1','$headline','$generalDescription','$match_agerange',$join_ip)";
			$db->Query($sqlInsert);
		 	$profileId	=	mysql_insert_id();
			$sqlInsertEx=	"INSERT INTO $profile_tbl_extended (profile_id,i_am_at_least_18_years_old,i_agree_with_tos) VALUES ('$profileId','1','1')";
			$db->Query($sqlInsertEx);

            //$profile_id    =    $db->InsertRow($profile_tbl,$keyvalue);

           // $newkv        =     array('profile_id' => $profileId,'i_am_at_least_18_years_old' =>1,'i_agree_with_tos'=>1);
            //$db->InsertRow($profile_tbl_extended,$newkv);
            //getting hash key
                $time                =    time() + (3 * 24 * 60 * 60);
                $hash                =    md5(rand(0,10000));
                $ip                  =    '"0.0.0.0"';
                $agent               =    '"mobile"';
                $dateNew = date_default_timezone_get(); 
                //getting username and password
 
                $sqlSession 	= "SELECT username ,password FROM $profile_tbl WHERE profile_id='$profileId'";
                $sqlExe 		= $db->Query($sqlSession);
                $sqlExeResult 	= mysql_fetch_array($sqlExe);
                $sqlName 		= $sqlExeResult['username'];
                $sqlPwd 		= $sqlExeResult['password'];
               // echo $profile_id;
                
                $test="SELECT * from $profile_tbl where email='$email' ";
                $row1=$db->Query($test);
		        $row =mysql_fetch_array($row1);
//echo 
                $profileId=$row['profile_id'];
				//$membership_id =$row['membership_type_id'];
				//$profile=$profile_id;

                //insert session key into online table
//echo $profileId;
if ($profileId!='' or  $profileId!=0)
		{

                $query                =    "INSERT INTO `$profile_online` VALUES('$profileId','$hash','$time','0.0.0.0','mobile')";
                $a                    =    $db1->Query($query);
                //print_r($a);
                        
                        $sqlAuth = "SELECT po.hash from `$profile_online` po
                                    LEFT JOIN $profile_tbl t ON (t.profile_id = po.profile_id)
                                    WHERE t.username='$sqlName'";
                        //$sqlResult = $db->Query($sqlAuth);
                        $signResult = @mysql_query($sqlAuth);
                        $sqlKey = mysql_fetch_array($signResult);
                        $sqlSessionKey = $sqlKey['hash'];
						$sex= stripslashes($sex);
						$sex= stripslashes($sex);
     //   echo '{"Profile_Id":"'.$profile_id.'","skey":"'.$sqlSessionKey.'","sex":'.$sex.',"Profile_Pic":"","Notifications" : "0"}';
						 
	if($image=='' or $image==NULL)
	{
//echo '{"Profile_Id":"' . $profile_id . '","skey":"' . $sqlSessionKey . '","sex":"' . $sex . '","Profile_Pic":"","Notifications" : "0","Time" : "' . $dateNew . '"}';
echo '{"Profile_Id":"'.$profileId.'","skey":"' . $sqlSessionKey . '","Notifications" : "0","sex":"'.$sex.'","Time":"'.$dateNew.'","Profile_Pic":""}';
return $value = array('profileid'=>$profileId, 'imgname'=>$image,'orientation'=>$orientation,'sex'=>$sex,'flag'=>0);
	}
	else 
	{
	$db5         	=     new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
  	$sqlTypPlan  	="SELECT period,units,is_recurring,price FROM $type_plan_tbl WHERE membership_type_id=$value1";
	$sqlTPExe 	 	= $db5->Query($sqlTypPlan);
   	$sqlTPRes 	 	= mysql_fetch_array($sqlTPExe);
 	$sqlTPPeriod 	= $sqlTPRes['period'];
 	$sqlTPUnits  	= $sqlTPRes['units'];
 	$sqlTPRecurring = $sqlTPRes['is_recurring'];
 	$sqlTPPrice 	= $sqlTPRes['price'];

  $sqlFinSale = "INSERT INTO $fin_sale_tbl(profile_id,payment_provider_order_number,order_stamp,unit,period,amount,membership_type_id,status,is_recurring) VALUES($profileId,'trial',$t,'$sqlTPUnits','$sqlTPPeriod','$sqlTPPrice','$value1','approval','$sqlTPRecurring')";
 $db5->Query($sqlFinSale);
 $finsale_id=@mysql_insert_id();
 $curentdate = date("Y-m-d H:i",$t);

 $dateExp = strtotime(date("Y-m-d H:i", strtotime($curentdate)) . " +".$sqlTPPeriod." ".$sqlTPUnits);
 $sqlUserMem = "INSERT INTO $mem_user_tbl (fin_sale_id,start_stamp,expiration_stamp,credits,membership_type_id,prev_membership_type_id,notified) VALUES ($finsale_id,$t,$dateExp,$sqlTPPrice,'$value1','18','0')";
$db->Query($sqlUserMem); 

	return $value = array('profileid'=>$profileId, 'imgname'=>$image,'orientation'=>$orientation,'sex'=>$sex,'flag'=>1,'skey'=>$sqlSessionKey,'email'=>$email,'username'=>$uname);
	
	}		 
		$sql = "SELECT `entityId` FROM `$action_tbl` WHERE `entityType` = 'profile_join' ORDER BY `createTime` DESC LIMIT 0,1";
		$a=$db->Query($sql);
		$b=mysql_fetch_array($a);
		$eid=$b['entityId'];
		$eid=$eid+1;
		$t=time();
		 //$profile;
		$data='{"string":"profile_join"}';
		 $sqlI="INSERT INTO `$action_tbl` VALUES ('','$eid','profile_join','newsfeed','$data','active','$t','$t','$profileId','15','everybody')";
		$db2->Query1($sqlI);
}
else
{
echo '{"profile_id":"NULL","Message":"Failed Executing Query, Try again later"}';
}
}
}
else
{
echo '{"profile_id":"NULL","Message":"Email id exists"}';
}

     }
//--------------------------------------------------------------------------------------------
/********************************************************************************************/
public function ProfileImageUpload($id)
	{

		$essence = new Essentials();
		$profile_pic = $essence->tblPrefix().'profile_photo';
		$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
                $sql = "SELECT max(`number`) as number from $profile_pic where `profile_id`=$id";

		if($db->Query($sql))
		{
			$row = $db->Row();
			$index = rand(1,99);
			$number = 0;
			$time = time();
			//echo "<br>".$id;
			//echo "<br>".$index;
			$keyvalue = array('photo_id'=>'NULL', 'profile_id'=>$id, 'index'=>$index, 'status'=>'"approval"', 'number'=>$number, 'description'=>'NULL', 'publishing_status'=>'"public"', 'password'=>'NULL', 'title'=>'NULL', 'added_stamp'=>$time, 'authed'=>0);
           
                        //$sqlins = "INSERT INTO '$profile_pic' values (NULL,$id,$index,'approval',$number,NULL,'public', NULL, NULL, $time, 0)";
			$photo_id	=	$db->InsertRow($profile_pic,$keyvalue);


            //if($db->Query($sqlins))
			//{
               // $row    =   $db->Row();
			   
				$result = array('photoid'=>$photo_id, 'index'=>$index, 'profile_id'=>$id,'time'=>$time);
               
				return $result;
			//}
		}
	}
/********************************************************************************************/
 public function usrSignInVerify($pid)
	  {		
	  		$essence	=	new Essentials();
			$profile_tbl    =	$essence->tblPrefix().'profile';
			$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	  		
			$sql="SELECT email_verified from $profile_tbl where profile_id='$pid' and status='active'";
			$res=$db->Query($sql);
			$ev=mysql_fetch_array($res);
			$evres=$ev['email_verified'];
			if ($evres=="undefined")
			{
			$sqlU="UPDATE `$profile_tbl`  SET `email_verified`='yes' where profile_id='$pid' and status='active'";
			$db->Query($sqlU);	
			//$this->authenticate1($username,$password,$timestamp);
			    header("Location:verify.php");//echo '<div class="statusmsg">Your account has been activated, you can now login</div>';
			}
			
			if ($evres=="yes")
			{
				//$this->authenticate($username,$password,$timestamp);
				header("Location:verify.php");//echo '<div class="statusmsg">Your account has been activated, you can now login</div>';
			}
	  }
 public function usrSignIn($un,$ps,$ts)
	  {
	  		$username	=	$un;
			$password	=	$ps;
			$timestamp	=	time();
			$essence	=	new Essentials();
			$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$profile_tbl=	$essence->tblPrefix().'profile';
			$sql12 = "SELECT profile_id from $profile_tbl where (username='$username' or email='$username') and password='$password'";
			$sql0ExeA =$db->Query($sql12);
			$sqlResA = mysql_fetch_array($sql0ExeA);
			$sqlResultA = $sqlResA['profile_id'];
			if($sqlResultA!=NULL)
			{
			$sql123 = "SELECT profile_id from $profile_tbl where (username='$username' or email='$username') and password='$password' and status='active'";
			$sql0ExeAB =$db->Query($sql123);
			$sqlResAB = mysql_fetch_array($sql0ExeAB);
			$sqlResultAB = $sqlResAB['profile_id'];
			if($sqlResultAB==NULL)
			{
			echo '{"Message":"User suspended"}';
			}
			else
			{
			$sql0 = "SELECT profile_id from $profile_tbl where (username='$username' or email='$username') and password='$password'";
			$sql0Exe =$db->Query($sql0);
			$sqlRes = mysql_fetch_array($sql0Exe);
			$sqlResult = $sqlRes['profile_id'];
			if($sqlResult!='')
			{
                  
                        $this->authenticate($username,$password,$timestamp);
			
			/*$sql="SELECT email_verified from $profile_tbl where (username='$username' or email='$username') and password='$password'";
			$res=$db->Query($sql);
			$ev=mysql_fetch_array($res);
			$evres=$ev['email_verified'];
			
				if ($evres=="yes")
				{
					$this->authenticate($username,$password,$timestamp);
				}
				else
				{
					echo '{"Message":"Follow Verification Link"}';
				}*/
			}
			else
			{
				echo '{"profile_id":"NULL","Message":"No such Username-Password Combination"}';
			}
}
}
			else
			{
				echo '{"profile_id":"NULL","Message":"No such Username-Password Combination"}';
			}
		}
	
	 public function usrSignInold($un,$ps,$ts)
	  {
	  		$username	=	$un;
			$password	=	$ps;
			$timestamp	=	time();
$essence	=	new Essentials();

			$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$profile_tbl=	$essence->tblPrefix().'profile';
			$sql12 = "SELECT profile_id from $profile_tbl where (username='$username' or email='$username') and password='$password' and status='active'";
			$sql0ExeA =$db->Query($sql12);
			$sqlResA = mysql_fetch_array($sql0ExeA);
			$sqlResultA = $sqlResA['profile_id'];
if($sqlResultA==NULL)
{
$sql123 = "SELECT profile_id from $profile_tbl where (username='$username' or email='$username') and password='$password'";


			$sql0ExeAB =$db->Query($sql123);
			$sqlResAB = mysql_fetch_array($sql0ExeAB);
			$sqlResultAB = $sqlResAB['profile_id'];
if($sqlResultAB==NULL)
{
echo '{"Message":"User suspended"}';
}
else
{
echo '{"profile_id":"NULL","Message":"No such Username-Password Combination"}';
}
}
else
{

$sql0 = "SELECT profile_id from $profile_tbl where (username='$username' or email='$username') and password='$password'";


			$sql0Exe =$db->Query($sql0);
			$sqlRes = mysql_fetch_array($sql0Exe);
			$sqlResult = $sqlRes['profile_id'];
			if($sqlResult!='')
			{
			
			$sql="SELECT email_verified from $profile_tbl where (username='$username' or email='$username') and password='$password'";
			$res=$db->Query($sql);
			$ev=mysql_fetch_array($res);
			$evres=$ev['email_verified'];
			
				if ($evres=="yes")
				{
					$this->authenticate($username,$password,$timestamp);
				}
				else
				{
					echo '{"Message":"Follow Verification Link"}';
				}
			}
			else
			{
				echo '{"profile_id":"NULL","Message":"No such Username-Password Combination"}';
			}
}
			
	  }
	   /** ------------------------------------------------------------------ */
	 private function authenticate($username,$password,$timestamp)
	 {
	 		$essence	=	new Essentials();
			$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_online			=	$essence->tblPrefix().'profile_online';
				$timestamp = time();
				if ($db->Query("SELECT * from $profile_tbl where username='$username' and password='$password'"))
				//if ($db->Query("SELECT * from $profile_tbl where (username='$username' or email='$username') and password='$password'"))
				{
					if($db->RowCount())
					{
						$row				=	$db->Row();
						$profile_id			=	$row->profile_id;
						$thumbnail			=	$this->getThumbImage($profile_id);
						$Notifications		=	$this->getNotifications($profile_id);
						$sex				=	$row->sex;
                                                $membership_id		=	$row->membership_type_id;
						$join_stamp 		=	$row->join_stamp;
						$dateJoin		= date("Y-m-d",$join_stamp);
						$currentDate		= date("Y-m-d");
						//$query	=	"INSERT INTO `$profile_online` VALUES('$profile_id','$hash','$time','0.0.0.0','mobile')";
						//$a		=	$db1->Query($query);
						$essence	=	new Essentials();
						$db1		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		//membership checking
		if($membership_id == '3' OR $membership_id == '108')
		{
		$userstatus ='y';
		}
		else if ($membership_id == '107')
		{
		 $dateNew = date("Y-m-d",strtotime(date("Y-m-d", strtotime($dateJoin)) . " +30 days"));
		if($currentDate<=$dateNew)
		{
		$userstatus ='y';
		}
		else
		{
		$userstatus ='n';
		}
		
		}
		else
		{
		$userstatus ='n';
		}
				//$time				=	'"'.time().'"';
				//$hash				=	'"'.md5('anand').'"';
				$time				=	time() + (3 * 24 * 60 * 60);
				$hash				=	md5(rand(0,10000));
				$ip					=	'"0.0.0.0"';
				$agent				=	'"mobile"';
				$dateNew = date_default_timezone_get(); 
				//$r					=	array('profile_id' => $profile_id, 'hash' => $hash,'expiration_time'=>$time,'ip'=>$ip,'agent'=>$agent);		
				//$ins				=	$db1->InsertRow($profile_online,$r);
				
				$test=mysql_query("SELECT * from $profile_tbl where username='$username' and password='$password'");
				$row=mysql_fetch_array($test);
				$profile_id=$row['profile_id'];
				//print_r($a);
				//getting hash key		
	if($profile_id!='' or $profile_id!=0)
	{
	
	  $sqlAuth1 = "SELECT profile_id,agent from `$profile_online` po WHERE profile_id='$profile_id'";
			//$sqlResult = $db->Query($sqlAuth);
			$signResult1 = @mysql_query($sqlAuth1);
			$sqlKey1 = mysql_fetch_array($signResult1);

		 $idSession = $sqlKey1['profile_id'];
		 $idAgent = $sqlKey1['agent'];

				
				if($idSession==NULL)
{
			     $query				=	"INSERT INTO `$profile_online` VALUES('$profile_id','$hash','$time','0.0.0.0','mobile')";
				$a					=	$db1->Query($query);
}
else if($idSession!=NULL)
{
$sqlUP="UPDATE $profile_online  SET `hash`='$hash',agent='mobile' where profile_id=$profile_id";
			$db1->Query($sqlUP);
}

$sqlAuth = "SELECT po.hash from `$profile_online` po
						LEFT JOIN $profile_tbl t ON (t.profile_id = po.profile_id)
						WHERE t.username='$username'";
			//$sqlResult = $db->Query($sqlAuth);
			$signResult = @mysql_query($sqlAuth);
			$sqlKey = mysql_fetch_array($signResult);
			$sqlSessionKey = $sqlKey['hash'];
			
			$sql="SELECT count(*) FROM `$friendlist` WHERE `friend_id`=$pid";
		$exe=$db->Query($sql);
		$val=mysql_fetch_array($exe);
		$frcount=$val['count(*)'];

			 $sqlU="UPDATE $profile_tbl  SET `activity_stamp`='$timestamp' where username='$username'  and password='$password'";
			$db1->Query($sqlU);
			echo '{"profile_id":"'.$profile_id.'","skey":"'.$sqlSessionKey.'","Profile_Pic":"'.$thumbnail.'","Notifications" : "'.$Notifications.'","sex" : "'.$sex.'","user_status":"'.$userstatus.'","Time" : "' . $dateNew . '","FRCount" : "' . $frcount . '"}';
}
else
{
echo '{"profile_id":"NULL","Message":"Failed Executing Query, Try again later"}';
}

					}
					else
					{
						echo '{"profile_id":"NULL","Message":"No such Username-Password Combination"}';

					}
				}
				else
				{
					echo '{"profile_id":"NULL","Message":"Failed Executing Query, Try again later"}';

				}
/*				
//old
$essence	=	new Essentials();
				$db1		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
				//$time				=	'"'.time().'"';
				//$hash				=	'"'.md5('anand').'"';
				$time				=	time() + (3 * 24 * 60 * 60);
				$hash				=	md5(rand(0,10000));
				$ip					=	'"0.0.0.0"';
				$agent				=	'"mobile"';
				//$r					=	array('profile_id' => $profile_id, 'hash' => $hash,'expiration_time'=>$time,'ip'=>$ip,'agent'=>$agent);		
				//$ins				=	$db1->InsertRow($profile_online,$r);
				
				$test=mysql_query("SELECT * from $profile_tbl where username='$username' and password='$password'");
				$row=mysql_fetch_array($test);
				$profile_id=$row['profile_id'];
				
			    $query				=	"INSERT INTO `$profile_online` VALUES('$profile_id','$hash','$time','0.0.0.0','mobile')";
				$a					=	$db1->Query($query);
				//print_r($a);
*/						
			}
			else
			{
				echo '{"profile_id":"NULL","Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	 } 
	 /** ------------------------------------------------------------------ */
	 public function getThumbImage($pid)
	 {
	 	$essence			=	new Essentials();
		$profile_pic_tbl	=	$essence->tblPrefix().'profile_photo';
		$sql				=	"SELECT `photo_id`, `index` from `$profile_pic_tbl` where `profile_id`=$pid and `number`=0";
		$db1 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		if($db1->Query($sql))
		{
			if($db1->RowCount())
			{
				$row1		=	$db1->Row();
				$photo_id	=	$row1->photo_id;
				$index		=	$row1->index;
				return 	'/userfiles'."/thumb_$pid"."_"."$photo_id"."_"."$index".".jpg";
			}
			else
			{
				return NULL;
			}
			$db1->kill();
		}
	 }
	 /** ------------------------------------------------------------------ */
	  public function getNotifications($pid)
	 {
	 	$essence		   =	new Essentials();
		$mailbox_conversation_tbl  =	$essence->tblPrefix().'mailbox_conversation';
		$mailbox_message_tbl	   =	$essence->tblPrefix().'mailbox_message';
		$im_message_tbl            =	$essence->tblPrefix().'im_message';
		$photo			   =	$essence->tblPrefix().'profile_photo';
		$profile_tbl		   =	$essence->tblPrefix().'profile';
		
		$db1 			   = 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db 			   = 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		
		
$sql1="SELECT COUNT(DISTINCT `c`.`conversation_id`) as count1 FROM `$mailbox_conversation_tbl` AS `c` LEFT JOIN `$mailbox_message_tbl` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`) WHERE (`initiator_id`=$pid OR `interlocutor_id`=$pid) AND (`bm_deleted` IN(0,2) AND `initiator_id`=$pid OR `bm_deleted` IN(0,1) AND `interlocutor_id`=$pid) AND (`bm_read` IN(0,2) AND `initiator_id`=$pid OR `bm_read` IN(0,1) AND `interlocutor_id`=$pid) AND `m`.`status`='a' AND `m`.`recipient_id`=$pid";
 

$sql ="SELECT im_message_id,sender_id as im_sender_id,recipient_id as im_recipient_id,text as im_text,Profile_Pic as im_Profile_Pic,username as im_username,FROM_UNIXTIME(timestamp,'%T') as im_time_stamp 
					   FROM (SELECT * FROM $im_message_tbl JOIN $profile_tbl where $im_message_tbl.recipient_id =$pid AND $im_message_tbl.read =0 AND $profile_tbl.profile_id=$im_message_tbl.sender_id)as X 
					   LEFT JOIN
					   (SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $photo where $photo.number=0)as Y 
				ON X.sender_id=Y.profile_id";
		
		$db->Query($sql);
		$row	=	$db->Row();
		$cnt1=$db->RowCount()?$db->RowCount():0;
		
		$db1->Query($sql1);
		$row1	=	$db1->Row();
		$cnt2	=	$row1->count1;
			
		  $cnt=$cnt1+$cnt2;
                 //echo $cnt;
		if($db->Query($sql) || $db1->Query($sql1))
		{
			return $cnt;
		}
		else
		{
				return 0;
		}
		
	 }
	  /** ------------------------------------------------------------------ */
	   /** ---------------------------get notifications count--------------------------------------- */ 
    public function getNotificationCount($pid, $skey) { 
        $essence = new Essentials(); 
        $secure = new secure(); 
        $mailbox_conversation_tbl = $essence->tblPrefix() . 'mailbox_conversation'; 
        $mailbox_message_tbl = $essence->tblPrefix() . 'mailbox_message'; 
        $im_message_tbl = $essence->tblPrefix() . 'im_message'; 
        $photo = $essence->tblPrefix() . 'profile_photo'; 
        $profile_tbl = $essence->tblPrefix() . 'profile'; 
        $online_tbl = $essence->tblPrefix() . 'profile_online'; 
        $key = $essence->tblPrefix() . 'lang_key'; 
        $value = $essence->tblPrefix() . 'lang_value'; 
        $membership_limit = $essence->tblPrefix() . 'link_membership_type_service'; 
        $membership_srv = $essence->tblPrefix() . 'link_membership_service_limit'; 
        $friendlist = $essence->tblPrefix().'profile_friend_list';
        $rslt1 = "SELECT COUNT(conversation_id) AS cnt FROM $mailbox_conversation_tbl where interlocutor_id=$pid AND bm_read=1"; 
        $db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
          $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
           $db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
            $db3 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 

        $res = $secure->CheckSecure($pid, $skey); 
        //$res=1; 
        if ($res == 1) { 
            $db1->Query($rslt1); 
            $row1 = $db1->Row(); 
            $cnt1 = $row1->cnt; 
            $rslt2 = "SELECT COUNT(im_message_id) AS cnt FROM $im_message_tbl  where recipient_id=$pid AND $im_message_tbl.read=0"; 
            $db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 

            $db2->Query($rslt2); 

            $row2 = $db2->Row(); 
            $cnt2 = $row2->cnt; 

           /* $sql1 = "SELECT * FROM (SELECT * FROM (SELECT DISTINCT `c`.`conversation_id` as c_id,`c`.*,`m`.`conversation_id` as conv_id,`m`.`status`,`m`.`sender_id`,`m`.`recipient_id`,`m`.`text`,m.is_readable,FROM_UNIXTIME(`m`.`time_stamp`,'%T') as time_stamp  FROM `$mailbox_conversation_tbl` AS `c` LEFT JOIN `$mailbox_message_tbl` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`) WHERE (`initiator_id`=$pid OR `interlocutor_id`=$pid) AND (`bm_deleted` IN(0,2) AND `initiator_id`=$pid OR `bm_deleted` IN(0,1) AND `interlocutor_id`=$pid) AND (`bm_read` IN(0,2) AND `initiator_id`=$pid OR `bm_read` IN(0,1) AND `interlocutor_id`=$pid) AND `m`.`status`='a' AND `m`.`recipient_id`=$pid) as A 
		LEFT JOIN 
		(SELECT profile_id,username,sex as msg_sex FROM $profile_tbl) as B ON  A.sender_id=B.profile_id) as X 
		 LEFT JOIN 
		(SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
		FROM $photo where $photo.number=0)as Y 
		ON 
		  X.profile_id=Y.profile_id";*/ 
            
            $query = "SELECT COUNT(DISTINCT `c`.`conversation_id`) as cnt 
			FROM `".$mailbox_conversation_tbl."` AS `c` 
			LEFT JOIN `".$mailbox_message_tbl."` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`) 
			WHERE (`initiator_id`=$pid  OR `interlocutor_id`=$pid) 
			AND (`bm_deleted` IN(0,2) AND `initiator_id`=$pid OR `bm_deleted` IN(0,1) AND `interlocutor_id`=$pid) 
 			AND (`bm_read` IN(0,2) AND `initiator_id`=$pid OR `bm_read` IN(0,1) AND `interlocutor_id`=$pid) 
 			 AND `m`.`status`='a' AND `m`.`recipient_id`=$pid 
 		"; 
            
            
            
           // echo $query; 
            //exit; 
            //$db1->Query($query); 
            //$row1 = $db1->Row(); 
            //$res1 = $db1->Query($sql1); 
// $resMail = mysql_fetch_array($es1); 
            $sql = "SELECT im_message_id,sender_id as im_sender_id,recipient_id as im_recipient_id,text as im_text,Profile_Pic as im_Profile_Pic,username as im_username, sex as im_sex, FROM_UNIXTIME(timestamp,'%T') as im_time_stamp 
  FROM (SELECT * FROM $im_message_tbl JOIN $profile_tbl where $im_message_tbl.recipient_id =$pid AND $im_message_tbl.read =0 AND $profile_tbl.profile_id=$im_message_tbl.sender_id)as X 
  LEFT JOIN 
  (SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $photo where $photo.number=0)as Y 
ON X.sender_id=Y.profile_id"; 
//echo $sql; 
            $db->Query($sql); 
            $row = $db->Row(); 
            $res = $db->Query($sql); 
// $resChat =mysql_fetch_array($res); 
//Count for mail and chat 
            $cnt1 = $db->RowCount() ? $db->RowCount() : 0; 
            $cnt2 = $db1->RowCount() ? $db1->RowCount() : 0; 
//$cnt=$cnt1+$cnt2; 
            $i = 0; 
            $j = 0; 

           /* while ($resMail = mysql_fetch_array($res1)) { 
                if ($resMail['is_readable'] == 'yes') { 
                    $i++; 
                } else if ($resMail['is_readable'] == 'no') { 
//echo "hai"; 
                    $text = preg_replace("/<[^>]*>/", "", $resMail['text']); 
                    $text = str_replace(" ", "", $text); 
//echo $text; 

                    if (($text == '[wink]4[/wink]') or ($text == '[smiles]58[/smiles]')) { 
//echo "hai"; 
                        $i = $i + 1; 
                    } 
                } 
            }*/ 
//echo $i; 

            $res1 = $db1->Query($query); 
            $row2 = $db1->Row(); 
            $i = $row2->cnt; 
            
            while ($resChat = mysql_fetch_array($res)) { 

                $j++; 
            } 


//echo "How are you?"; 
            $final1 = array(); 
            $final2 = array(); 
            // Assigning to array Ends here 
            if (is_array($result)) { 
                foreach ($result as $array) { 

                    array_push($final1, $array); 
                } 
            } 
            if (is_array($result1)) { 
                foreach ($result1 as $array) { 

                    array_push($final2, $array); 
                } 
            } 
//echo $i; 
//echo $j; 
            $cnt = $i + $j; 

            $url = $this->getThumbImage($pid); 
                        if ($url == "") 
                            $url = "NULL"; 
                            
                           $sqlr="SELECT username FROM $profile_tbl WHERE profile_id=$pid"; 
                            $exe=$db->Query($sqlr); 
            				$row = mysql_fetch_array($exe); 
            				$username = $row['username']; 
            				//$username; 
            if($username==NULL or $username=='') 
            {$unreg=1;}else{$unreg=0;} 
            $sql="SELECT hash FROM $online_tbl WHERE profile_id = $pid "; 
            $sqlex=$db->Query($sql); 
            $sqlresult=mysql_fetch_array($sqlex); 
            $skey=$sqlresult['hash']; 
            
            
            $sql="SELECT status FROM $profile_tbl WHERE profile_id = $pid "; 
            $sqlex=$db->Query($sql); 
            $sqlresult=mysql_fetch_array($sqlex); 
            $status=$sqlresult['status']; 
            
            $sql="SELECT count(*) FROM `$friendlist` WHERE `friend_id`=$pid";
		$exe=$db->Query($sql);
		$val=mysql_fetch_array($exe);
		$frcount=$val['count(*)'];
            
            echo '{"Status":"Live","count": "' . $cnt . '","chatcount":"'.$j.'","mailcount":"'.$i.'","skey":"'.$skey.'","Profile_Pic":"'.$url.'","Un_Registered":"'.$unreg.'","status":"'.$status.'","FRCount":"'.$frcount.'"}'; 
        } else { 
            echo '{"Message":"Session Expired"}'; 
        } 
    } 

    /** ---------------------------get notifications count--------------------------------------- */ 
 public function getNotificationCountBKPsecondjan($pid,$skey)
	  {
	  	$essence			        =	new Essentials();
		$secure                     =   new secure();
		$mailbox_conversation_tbl	=	$essence->tblPrefix().'mailbox_conversation';
		$mailbox_message_tbl		=	$essence->tblPrefix().'mailbox_message';
		$im_message_tbl             =	$essence->tblPrefix().'im_message';
		$photo						=	$essence->tblPrefix().'profile_photo';
		$profile_tbl				=	$essence->tblPrefix().'profile';
		
		$db1 						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db 						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//check user authentication
		$res = $secure->CheckSecure($pid,$skey);
               // $res = 1;
		if($res==1)
		{

/*$sql1="SELECT COUNT(`c`.`conversation_id`) as count1,m.is_readable,m.text FROM `$mailbox_conversation_tbl` AS `c` LEFT JOIN `$mailbox_message_tbl` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`) WHERE (`initiator_id`=$pid OR `interlocutor_id`=$pid) AND (`bm_deleted` IN(0,2) AND `initiator_id`=$pid OR `bm_deleted` IN(0,1) AND `interlocutor_id`=$pid) AND (`bm_read` IN(0,2) AND `initiator_id`=$pid OR `bm_read` IN(0,1) AND `interlocutor_id`=$pid) AND `m`.`status`='a' AND `m`.`recipient_id`=$pid";
			
			 $sql1old="SELECT * FROM (SELECT * FROM (SELECT DISTINCT `c`.`conversation_id` as c_id,`c`.*,`m`.`conversation_id` as conv_id,`m`.`status`,`m`.`sender_id`,`m`.`recipient_id`,`m`.`text`,m.is_readable,FROM_UNIXTIME(`m`.`time_stamp`,'%T') as time_stamp  FROM `$mailbox_conversation_tbl` AS `c` LEFT JOIN `$mailbox_message_tbl` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`) WHERE (`initiator_id`=$pid OR `interlocutor_id`=$pid) AND (`bm_deleted` IN(0,2) AND `initiator_id`=$pid OR `bm_deleted` IN(0,1) AND `interlocutor_id`=$pid) AND (`bm_read` IN(0,2) AND `initiator_id`=$pid OR `bm_read` IN(0,1) AND `interlocutor_id`=$pid) AND `m`.`status`='a' AND `m`.`recipient_id`=$pid) as A
		LEFT JOIN 
		(SELECT profile_id,username,sex as msg_sex FROM $profile_tbl) as B ON  A.sender_id=B.profile_id) as X
		 LEFT JOIN 
		(SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
		FROM $photo where $photo.number=0)as Y 
		ON 
		  X.profile_id=Y.profile_id";
		//echo $sql1;*/
 		
$sql17="SELECT COUNT(`c`.`conversation_id`) as count1 FROM `$mailbox_conversation_tbl` AS `c` LEFT JOIN `$mailbox_message_tbl` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`) WHERE (`initiator_id`=$pid OR `interlocutor_id`=$pid) AND (`bm_deleted` IN(0,2) AND `initiator_id`=$pid OR `bm_deleted` IN(0,1) AND `interlocutor_id`=$pid) AND (`bm_read` IN(0,2) AND `initiator_id`=$pid OR `bm_read` IN(0,1) AND `interlocutor_id`=$pid) AND `m`.`status`='a' AND `m`.`recipient_id`=$pid";
                        
		$sqlres = $db1->Query($sql17);
		$row1 = mysql_fetch_array($sqlres);
		$cnt1=$row1['count1'];
                //echo $sql1;
// $resMail = mysql_fetch_array($es1);
	$sql ="SELECT im_message_id,sender_id as im_sender_id,recipient_id as im_recipient_id,text as im_text,Profile_Pic as im_Profile_Pic,username as im_username, sex as im_sex, FROM_UNIXTIME(timestamp,'%T') as im_time_stamp 
  FROM (SELECT * FROM $im_message_tbl JOIN $profile_tbl where $im_message_tbl.recipient_id =$pid AND $im_message_tbl.read =0 AND $profile_tbl.profile_id=$im_message_tbl.sender_id)as X 
  LEFT JOIN
  (SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $photo where $photo.number=0)as Y 
ON X.sender_id=Y.profile_id";
                
                $sql17 ="SELECT im_message_id,sender_id as im_sender_id,recipient_id as im_recipient_id,text as im_text,Profile_Pic as im_Profile_Pic,username as im_username,FROM_UNIXTIME(timestamp,'%T') as im_time_stamp 
					   FROM (SELECT * FROM $im_message_tbl JOIN $profile_tbl where $im_message_tbl.recipient_id =$pid AND $im_message_tbl.read =0 AND $profile_tbl.profile_id=$im_message_tbl.sender_id)as X 
					   LEFT JOIN
					   (SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $photo where $photo.number=0)as Y 
				ON X.sender_id=Y.profile_id";
                
//echo $sql;
$db->Query($sql);
$row = $db->Row();
$res=$db->Query($sql);
// $resChat =mysql_fetch_array($res);
 
//Count for mail and chat
/*$cnt1=$db->RowCount()?$db->RowCount():0;
$cnt2=$db1->RowCount()?$db1->RowCount():0;
$cnt=$cnt1+$cnt2;
echo $cnt;*/
$i=0;
$j =0;
		while($resMail = mysql_fetch_array($res1))
		{
			if($resMail['is_readable'] == 'yes')
			{
			$i++;
			}
			else if($resMail['is_readable'] == 'no')
{
//echo "hai";
			$text =	preg_replace("/<[^>]*>/","",$resMail['text']); 
			$text =	str_replace(" ","",$text);
//echo $text;
			
					if(($text =='[wink]4[/wink]') or ($text == '[smiles]58[/smiles]') )
					{
//echo "hai";
$i=$i+1;
					}
}
		}
 
		while($resChat =mysql_fetch_array($res))
		{
		$j++;
		}
		$cnt=$cnt1+$j;
			echo '{"Status":"Live","count": "'.$cnt.'"}';
		}
	else
	{
		echo '{"Message":"Session Expired"}';
	}	
	  }
/*************************/
	   public function getNotificationCountolopo($pid,$skey)
	  {
	  	$essence			        =	new Essentials();
		$secure                     =   new secure();
		$mailbox_conversation_tbl	=	$essence->tblPrefix().'mailbox_conversation';
		$mailbox_message_tbl		=	$essence->tblPrefix().'mailbox_message';
		$im_message_tbl             =	$essence->tblPrefix().'im_message';
		$photo						=	$essence->tblPrefix().'profile_photo';
		$profile_tbl				=	$essence->tblPrefix().'profile';
		$db1 						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db 						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//check user authentication
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
		$sql1="SELECT * FROM (SELECT * FROM (SELECT DISTINCT `c`.`conversation_id` as c_id,`c`.*,`m`.`conversation_id` as conv_id,`m`.`status`,`m`.`sender_id`,`m`.`recipient_id`,`m`.`text`,m.is_readable,FROM_UNIXTIME(`m`.`time_stamp`,'%T') as time_stamp  FROM `$mailbox_conversation_tbl` AS `c` LEFT JOIN `$mailbox_message_tbl` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`) WHERE (`initiator_id`=$pid OR `interlocutor_id`=$pid) AND (`bm_deleted` IN(0,2) AND `initiator_id`=$pid OR `bm_deleted` IN(0,1) AND `interlocutor_id`=$pid) AND (`bm_read` IN(0,2) AND `initiator_id`=$pid OR `bm_read` IN(0,1) AND `interlocutor_id`=$pid) AND `m`.`status`='a' AND `m`.`recipient_id`=$pid) as A
		LEFT JOIN 
		(SELECT profile_id,username,sex as msg_sex FROM $profile_tbl) as B ON  A.sender_id=B.profile_id) as X
		 LEFT JOIN 
		(SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
		FROM $photo where $photo.number=0)as Y 
		ON 
		  X.profile_id=Y.profile_id";
		//echo $sql1;
		 $db1->Query($sql1);
		$row1 = $db1->Row();
		$res1=$db1->Query($sql1);
// $resMail = mysql_fetch_array($es1);
		$sql ="SELECT im_message_id,sender_id as im_sender_id,recipient_id as im_recipient_id,text as im_text,Profile_Pic as im_Profile_Pic,username as im_username, sex as im_sex, FROM_UNIXTIME(timestamp,'%T') as im_time_stamp 
  FROM (SELECT * FROM $im_message_tbl JOIN $profile_tbl where $im_message_tbl.recipient_id =$pid AND $im_message_tbl.read =0 AND $profile_tbl.profile_id=$im_message_tbl.sender_id)as X 
  LEFT JOIN
  (SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $photo where $photo.number=0)as Y 
ON X.sender_id=Y.profile_id";
//echo $sql;
$db->Query($sql);
$row = $db->Row();
$res=$db->Query($sql);
// $resChat =mysql_fetch_array($res);
 
//Count for mail and chat
$cnt1=$db->RowCount()?$db->RowCount():0;
$cnt2=$db1->RowCount()?$db1->RowCount():0;
//$cnt=$cnt1+$cnt2;
$i=0;
$j =0;
		while($resMail = mysql_fetch_array($res1))
		{
			if($resMail['is_readable'] == 'yes')
			{
			$i++;
			}
			else if($resMail['is_readable'] == 'no')
{
//echo "hai";
			$text =	preg_replace("/<[^>]*>/","",$resMail['text']); 
			$text =	str_replace(" ","",$text);
//echo $text;
			
					if(($text =='[wink]4[/wink]') or ($text == '[smiles]58[/smiles]') )
					{
//echo "hai";
$i=$i+1;
					}
}
}
 
		while($resChat =mysql_fetch_array($res))
		{
		$j++;
		}
		$cnt=$i+$j;
			echo '{"Status":"Live","count": "'.$cnt.'"}';
		}
	else
	{
		echo '{"Message":"Session Expired"}';
	}	
	  }
	 /** ------------------------------------------------------------------ */
 public function Notifications($pid,$skey)
	{   
	$essence        			= new Essentials();
	$secure                     = new secure();
	$mailbox_conversation_tbl 	= $essence->tblPrefix().'mailbox_conversation';
	$mailbox_message_tbl 		= $essence->tblPrefix().'mailbox_message';
	$im_message_tbl             = $essence->tblPrefix().'im_message';
	$photo 						= $essence->tblPrefix().'profile_photo';
	$profile_tbl 				= $essence->tblPrefix().'profile';
	$key                    	= $essence->tblPrefix().'lang_key';
	$value                    	= $essence->tblPrefix().'lang_value';
	$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
	$membership_srv		    =	$essence->tblPrefix().'link_membership_service_limit';
	$rslt1 = "SELECT COUNT(conversation_id) AS cnt FROM $mailbox_conversation_tbl where interlocutor_id=$pid AND bm_read=1";
	$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$db3 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
 
	$res = $secure->CheckSecure($pid,$skey);
	if($res==1)
	{
		$db1->Query($rslt1);
		$row1 = $db1->Row();
		$cnt1       =   $row1->cnt;
		$rslt2       = "SELECT COUNT(im_message_id) AS cnt FROM $im_message_tbl  where recipient_id=$pid AND $im_message_tbl.read=0";
		$db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		 
		$db2->Query($rslt2);
		$row2 = $db2->Row();
		$cnt2   = $row2->cnt;
		$sql1old="SELECT COUNT(DISTINCT `c`.`conversation_id`) as count1,m.is_readable,m.text,m.sender_id,m.recipient_id,subject,CONCAT( '/', 'userfiles/thumb_', CAST( ph.profile_id AS CHAR ) , '_', CAST( ph.photo_id AS CHAR ) , '_', CAST( ph.index AS CHAR ) , '.jpg' ) AS Profile_Pic,username,p.profile_id,p.sex as msg_sex,time_stamp FROM `skadate_mailbox_conversation` AS `c` LEFT JOIN `skadate_mailbox_message` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`)  LEFT JOIN skadate_profile as p ON (p.profile_id=m.sender_id) LEFT JOIN skadate_profile_photo as ph ON (ph.profile_id=p.profile_id and ph.number=0) WHERE (`initiator_id`=$pid OR `interlocutor_id`=$pid) AND (`bm_deleted` IN(0,2) AND `initiator_id`=$pid OR `bm_deleted` IN(0,1) AND `interlocutor_id`=$pid) AND (`bm_read` IN(0,2) AND `initiator_id`=$pid OR `bm_read` IN(0,1) AND `interlocutor_id`=$pid) AND `m`.`status`='a' AND `m`.`recipient_id`=$pid";
		$sql1="SELECT * FROM (SELECT * FROM (SELECT DISTINCT `c`.`conversation_id` as c_id,`c`.*,`m`.`conversation_id` as conv_id,`m`.`status`,`m`.`sender_id`,`m`.`recipient_id`,`m`.`text`,m.is_readable,`m`.`time_stamp` as time_stamp  FROM `$mailbox_conversation_tbl` AS `c` LEFT JOIN `$mailbox_message_tbl` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`) WHERE (`initiator_id`=$pid OR `interlocutor_id`=$pid) AND (`bm_deleted` IN(0,2) AND `initiator_id`=$pid OR `bm_deleted` IN(0,1) AND `interlocutor_id`=$pid) AND (`bm_read` IN(0,2) AND `initiator_id`=$pid OR `bm_read` IN(0,1) AND `interlocutor_id`=$pid) AND `m`.`status`='a' AND `m`.`recipient_id`=$pid) as A
						LEFT JOIN 
						(SELECT profile_id,username,sex as msg_sex FROM $profile_tbl) as B ON  A.sender_id=B.profile_id) as X
						LEFT JOIN 
						(SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
						FROM $photo where $photo.number=0)as Y 
						ON 
						X.profile_id=Y.profile_id";
		//echo $sql1;
		 $db1->Query($sql1);
		$row1 = $db1->Row();
		$res1=$db1->Query($sql1);
// $resMail = mysql_fetch_array($es1);
		$sql ="SELECT im_message_id,sender_id as im_sender_id,recipient_id as im_recipient_id,text as im_text,Profile_Pic as im_Profile_Pic,username as im_username, sex as im_sex,timestamp as im_time_stamp 
  FROM (SELECT * FROM $im_message_tbl JOIN $profile_tbl where $im_message_tbl.recipient_id =$pid AND $im_message_tbl.read =0 AND $profile_tbl.profile_id=$im_message_tbl.sender_id)as X 
  LEFT JOIN
  (SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( $photo.profile_id AS CHAR ) , '_', CAST( $photo.photo_id AS CHAR ) , '_', CAST( $photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $photo where $photo.number=0)as Y 
ON X.sender_id=Y.profile_id";
//echo $sql;
$db->Query($sql);
$row = $db->Row();
$res=$db->Query($sql);
// $resChat =mysql_fetch_array($res);
 
//Count for mail and chat
$cnt1=$db->RowCount()?$db->RowCount():0;
$cnt2=$db1->RowCount()?$db1->RowCount():0;
//$cnt=$cnt1+$cnt2;
$i=0;
$j =0;
 
		while($resMail = mysql_fetch_array($res1))
		{
$dateNew = date("m-d-Y H:i:s",$resMail['time_stamp']);
			if($resMail['is_readable'] == 'yes')
			{
			$result[$i] =array('conversation_id'=>$resMail['conversation_id'],'sender_id'=>$resMail['sender_id'],'recipient_id'=>$resMail['recipient_id'],'subject'=>$resMail['subject'], 'text'=>$resMail['text'], 'profile_id'=>$resMail['profile_id'], 'username'=>$resMail['username'],'msg_sex'=>$resMail['msg_sex'], 'Profile_Pic'=>$resMail['Profile_Pic'],'time_stamp'=>$dateNew,'conversation_ts'=>$resMail['conversation_ts']);	
			$i++;
			}
			else if($resMail['is_readable'] == 'no')
{
$text =	preg_replace("/<[^>]*>/","",$resMail['text']); 
			$text =	str_replace(" ","",$text);
//echo $text;
			
					if(($text =='[wink]4[/wink]') or ($text == '[smiles]58[/smiles]') )
					{
$result[$i] =array('conversation_id'=>$resMail['conversation_id'],'sender_id'=>$resMail['sender_id'],'recipient_id'=>$resMail['recipient_id'],'subject'=>$resMail['subject'], 'text'=>$resMail['text'], 'profile_id'=>$resMail['profile_id'], 'username'=>$resMail['username'],'msg_sex'=>$resMail['msg_sex'], 'Profile_Pic'=>$resMail['Profile_Pic'],'time_stamp'=>$dateNew,'conversation_ts'=>$resMail['conversation_ts']);	
$i=$i+1;
	}
}
	}
while($resChat =mysql_fetch_array($res))
		{
		$dateNewChat = date("m-d-Y H:i:s",$resChat['im_time_stamp']);
		/* $sql0 = "SELECT membership_type_id FROM $profile_tbl WHERE profile_id = '$pid'";
		 $memberType1 =  $db->Query1($sql0);
		 $memberTypeId1 = @mysql_fetch_array($memberType1);
		 $membershipTypeId1 = $memberTypeId1['membership_type_id'];
		 //
		  $sql2 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='initiate_im_session' AND membership_type_id='$membershipTypeId1'";
		 $res = $db->Query1($sql2);
		 $resultId = @mysql_fetch_array($res);
		 $resultMemberId = $resultId['membership_type_id'];
	        if(($resultMemberId!=NULL))
		{*/
		$result1[$j] =array('im_message_id'=>$resChat['im_message_id'],'im_sender_id'=>$resChat['im_sender_id'], 'im_recipient_id'=>$resChat['im_recipient_id'],'im_text'=>$resChat['im_text'], 'im_Profile_Pic'=>$resChat['im_Profile_Pic'], 'im_username'=>$resChat['im_username'], 'im_sex'=>$resChat['im_sex'],'im_time_stamp'=>$dateNewChat);
		//print_r($result1);
		$j++;
		}
//echo "How are you?";
	$final1 = array();
	$final2 = array();
	// Assigning to array Ends here
	if (is_array($result))
	{
	foreach($result as $array)
	{
	 
	array_push($final1, $array);
	}
	}
	if (is_array($result1))
	{
	foreach($result1 as $array)
	{
	 
	array_push($final2, $array);
	}
	}
	$cnt=$i+$j;
	if($i>=1 && $j>=1)
{
	
$profile = '{"Status":"Live","Notifications":'.$cnt.',"MailCount":'.$i.',"IMCount":'.$j.',"result":['.json_encode($final2).json_encode($final1).']}';
//$profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
//$profile = preg_replace('\'``\'', ' ', $profile);
$profile = str_replace("},]}", "}]}", $profile);
$profile = str_replace("[]]", "", $profile);
$profile = str_replace("[[{", "[{", $profile);
$profile = str_replace("]]", "]", $profile);
$profile = str_replace("][", ",", $profile);
echo $profile;
//$result="UPDATE `$im_message_tbl`  SET `read`=1 where `recipient_id`=$pid AND `read`=0";
//$db2->Query($result);
}  
else if($i==0 && $j>=1)
{
 //echo "2";
$profile = '{"Status":"Live","Notifications":'.$cnt.',"MailCount":'.$i.',"IMCount":'.$j.',"result":['.json_encode($final2).']}';
// $profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
// $profile = str_replace('\'``\'', ' ', $profile);
//$profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
//$profile = preg_replace('\'``\'', ' ', $profile);
$profile = str_replace("},]}", "}]}", $profile);
$profile = str_replace("[]]", "", $profile);
$profile = str_replace("[[{", "[{", $profile);
$profile = str_replace("]]", "]", $profile);
$profile = str_replace("][", ",", $profile);
echo $profile;
} 
else if($i>=1 && $j==0)
{
 //echo "3";
$profile = '{"Status":"Live","Notifications":'.$cnt.',"MailCount":'.$i.',"IMCount":'.$j.',"result":['.json_encode($final1).']}';
// $profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
// $profile = preg_replace('\'``\'', ' ', $profile);
//$profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
//$profile = preg_replace('\'``\'', ' ', $profile);
$profile = str_replace("},]}", "}]}", $profile);
$profile = str_replace("[]]", "", $profile);
$profile = str_replace("[[{", "[{", $profile);
$profile = str_replace("]]", "]", $profile);
$profile = str_replace("][", ",", $profile);
echo $profile;
}   
else
{
echo '{"Status":"Live","Notifications":"0"}';
}
return $cnt;
}
else
{
echo '{"Message":"Session Expired"}';
}
$db->Close();
$db1->Close();
$db2->Close();
$db3->Close();
 
}
/***************************************/
	 public function NotificationsOldBackUpp($pid,$skey)
	{   
	$essence        			= new Essentials();
	$secure                     = new secure();
	$mailbox_conversation_tbl 	= $essence->tblPrefix().'mailbox_conversation';
	$mailbox_message_tbl 		= $essence->tblPrefix().'mailbox_message';
	$im_message_tbl             = $essence->tblPrefix().'im_message';
	$photo 						= $essence->tblPrefix().'profile_photo';
	$profile_tbl 				= $essence->tblPrefix().'profile';
	$key                    	= $essence->tblPrefix().'lang_key';
	$value                    	= $essence->tblPrefix().'lang_value';

	$rslt1 = "SELECT COUNT(conversation_id) AS cnt FROM $mailbox_conversation_tbl where interlocutor_id=$pid AND bm_read=1";
	$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$db3 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
 
	$res = $secure->CheckSecure($pid,$skey);
	if($res==1)
	{
		$db1->Query($rslt1);
		$row1 = $db1->Row();
		$cnt1       =   $row1->cnt;
		$rslt2       = "SELECT COUNT(im_message_id) AS cnt FROM $im_message_tbl  where recipient_id=$pid AND $im_message_tbl.read=0";
		$db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		 
		$db2->Query($rslt2);
		 
		$row2 = $db2->Row();
		$cnt2   = $row2->cnt;
		
		
$sql1="SELECT *,CONCAT( '/', 'userfiles/thumb_', CAST( ph.profile_id AS CHAR ) , '_', 
		     CAST( ph.photo_id AS CHAR ) , '_', CAST( ph.index AS CHAR ) , '.jpg' ) 
		     AS Profile_Pic
		     FROM 
		(	SELECT DISTINCT c.conversation_id AS c_id,c.*,m.conversation_id AS conv_id,m.status,m.sender_id,m.recipient_id,m.text,
			m.is_readable,FROM_UNIXTIME(m.time_stamp,'%T') AS time_stamp 
			FROM $mailbox_conversation_tbl AS c 
			LEFT JOIN skadate_mailbox_message AS m ON c.conversation_id = m.conversation_id
			WHERE (initiator_id=$pid  OR interlocutor_id=$pid )
			AND 
				(bm_deleted IN(0,2) AND initiator_id=$pid  OR bm_deleted IN(0,1) AND interlocutor_id=$pid ) 
				AND (bm_read IN(0,2) AND initiator_id=$pid  OR bm_read IN(0,1) AND interlocutor_id=$pid ) 
				AND m.status='a' AND m.recipient_id=$pid 
		 ) 
		 AS A 
		 LEFT JOIN 
		  ( 
			SELECT profile_id,username,sex AS msg_sex 
			FROM skadate_profile
		  ) AS B ON A.sender_id=B.profile_id
	      LEFT JOIN 
	     $photo ph ON ph.profile_id=B.profile_id AND ph.number=0";
		//echo $sql1;
		 $db1->Query($sql1);
		$row1 = $db1->Row();
		$res1=$db1->Query($sql1);
// $resMail = mysql_fetch_array($es1);
	
$sql="SELECT im_message_id,sender_id AS im_sender_id,recipient_id AS im_recipient_id,TEXT AS im_text,
CONCAT( '/', 'userfiles/thumb_',  CAST( ph.profile_id AS CHAR ) , '_',  CAST( ph.photo_id AS CHAR ) , '_', CAST( ph.index AS CHAR ) , '.jpg' ) AS im_Profile_Pic,
username AS im_username, sex AS im_sex, FROM_UNIXTIME(TIMESTAMP,'%T') AS im_time_stamp 
FROM 
$im_message_tbl m
JOIN $profile_tbl pf ON pf.profile_id=m.sender_id AND m.recipient_id =$pid AND m.read=0
LEFT JOIN  $photo ph ON m.sender_id=ph.profile_id AND ph.number=0";


//echo $sql;
$db->Query($sql);
$row = $db->Row();
$res=$db->Query($sql);
// $resChat =mysql_fetch_array($res);
 
//Count for mail and chat
$cnt1=$db->RowCount()?$db->RowCount():0;
$cnt2=$db1->RowCount()?$db1->RowCount():0;
//$cnt=$cnt1+$cnt2;
$i=0;
$j =0;
 
		while($resMail = mysql_fetch_array($res1))
		{
			
$dateNew = date("h:i:s",$resMail['time_stamp']);
			if($resMail['is_readable'] == 'yes')
			{
			$result[$i] =array('conversation_id'=>$resMail['conversation_id'],'sender_id'=>$resMail['initiator_id'],'recipient_id'=>$resMail['interlocutor_id'],'subject'=>$resMail['subject'], 'text'=>$resMail['text'], 'profile_id'=>$resMail['profile_id'], 'username'=>$resMail['username'],'msg_sex'=>$resMail['msg_sex'], 'Profile_Pic'=>$resMail['Profile_Pic'],'time_stamp'=>$dateNew,'conversation_ts'=>$resMail['conversation_ts']);	
			$i++;
			}
			else if($resMail['is_readable'] == 'no')
{
$text =	preg_replace("/<[^>]*>/","",$resMail['text']); 
			$text =	str_replace(" ","",$text);
//echo $text;
			
					if(($text =='[wink]4[/wink]') or ($text == '[smiles]58[/smiles]') )
					{
$result[$i] =array('conversation_id'=>$resMail['conversation_id'],'sender_id'=>$resMail['initiator_id'],'recipient_id'=>$resMail['interlocutor_id'],'subject'=>$resMail['subject'], 'text'=>$resMail['text'], 'profile_id'=>$resMail['profile_id'], 'username'=>$resMail['username'],'msg_sex'=>$resMail['msg_sex'], 'Profile_Pic'=>$resMail['Profile_Pic'],'time_stamp'=>$dateNew,'conversation_ts'=>$resMail['conversation_ts']);	
$i=$i+1;
					}
}
	
		}
 
		while($resChat =mysql_fetch_array($res))
		{
		 
		$result1[$j] =array('im_message_id'=>$resChat['im_message_id'],'im_sender_id'=>$resChat['im_sender_id'], 'im_recipient_id'=>$resChat['im_recipient_id'],'im_text'=>$resChat['im_text'], 'im_Profile_Pic'=>$resChat['im_Profile_Pic'], 'im_username'=>$resChat['im_username'], 'im_sex'=>$resChat['im_sex'],'im_time_stamp'=>$resChat['im_time_stamp']);
		//print_r($result1);
		$j++;
		}
//echo "How are you?";
	$final1 = array();
	$final2 = array();
	// Assigning to array Ends here
	if (is_array($result))
	{
	foreach($result as $array)
	{
	 
	array_push($final1, $array);
	}
	}
	if (is_array($result1))
	{
	foreach($result1 as $array)
	{
	 
	array_push($final2, $array);
	}
	}
	$cnt=$i+$j;
	if($i>=1 && $j>=1)
{
	
$profile = '{"Status":"Live","Notifications":'.$cnt.',"MailCount":'.$i.',"IMCount":'.$j.',"result":['.json_encode($final2).json_encode($final1).']}';
//$profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
//$profile = preg_replace('\'``\'', ' ', $profile);
$profile = str_replace("},]}", "}]}", $profile);
$profile = str_replace("[]]", "", $profile);
$profile = str_replace("[[{", "[{", $profile);
$profile = str_replace("]]", "]", $profile);
$profile = str_replace("][", ",", $profile);
echo $profile;
//$result="UPDATE `$im_message_tbl`  SET `read`=1 where `recipient_id`=$pid AND `read`=0";
//$db2->Query($result);
 
}  
else if($i==0 && $j>=1)
{
 //echo "2";
$profile = '{"Status":"Live","Notifications":'.$cnt.',"MailCount":'.$i.',"IMCount":'.$j.',"result":['.json_encode($final2).']}';
// $profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
// $profile = str_replace('\'``\'', ' ', $profile);
//$profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
//$profile = preg_replace('\'``\'', ' ', $profile);
$profile = str_replace("},]}", "}]}", $profile);
$profile = str_replace("[]]", "", $profile);
$profile = str_replace("[[{", "[{", $profile);
$profile = str_replace("]]", "]", $profile);
$profile = str_replace("][", ",", $profile);
echo $profile;
} 
else if($i>=1 && $j==0)
{
 //echo "3";
$profile = '{"Status":"Live","Notifications":'.$cnt.',"MailCount":'.$i.',"IMCount":'.$j.',"result":['.json_encode($final1).']}';
// $profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
// $profile = preg_replace('\'``\'', ' ', $profile);
//$profile=preg_replace( '/([ ]+)/s', '`', $profile, -1, $count );
//$profile = preg_replace('\'``\'', ' ', $profile);
$profile = str_replace("},]}", "}]}", $profile);
$profile = str_replace("[]]", "", $profile);
$profile = str_replace("[[{", "[{", $profile);
$profile = str_replace("]]", "]", $profile);
$profile = str_replace("][", ",", $profile);
echo $profile;
}   
else
{
echo '{"Status":"Live","Notifications":"0"}';
}
return $cnt;
}
else
{
echo '{"Message":"Session Expired"}';
}
 
}

	 /** ------------------------------------------------------------------ */
	 public function resetpassword($fnemail)
	 {
	 		//$profile_id		=	$fnprofile_id;
			$email			=	$fnemail;
			$essence		=	new Essentials();
			$db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			if (!$db->Error())
			{
				$profile_tbl	=	$essence->tblPrefix().'profile';
				if ($db->Query("SELECT * from $profile_tbl where email='$email'"))
				{
					if($db->RowCount())
					{
						 $args 	= 	array(  'length' => $essence->getpasslen(),
											'alpha_upper_include',
											'alpha_lower_include',						
											'number_include',
											'symbol_include');
						 $pwd 	= 	new password_generator( $args );
						 $orgipass	=	$pwd->get_password();
						 $hashpass	=	sha1($essence->getHashSalt() . $orgipass); 
									 
						 $mailer = new Mailer ();
						 $mailer->addr = $email;
 						 $mailer->to = 'John';
						 $mailer->subject = 'Password reset!';
						 $mailer->from_addr = $essence->getSiteEmail();
						 $mailer->tpl_containers = array ('{%SIGNATURE%}'=>'Site Admin', '{%PASSWORD%}'=> $orgipass);
						 $mailer->ProcessTemplate ( '../templates/msg.txt' );
						 $mailer->SendMail ();
					 	
						 $sql	=	"UPDATE `$profile_tbl` SET `password`='".$hashpass."' where `email`='".$email."'";							 
						 $db->Query($sql);
						 echo '{"Message":"Email sent to given email address"}';
						 					 
					}
					else
					{
						echo '{"Token":"NULL","Message":"Email address provided does not match any record"}';
					}
				}
				else
				{
					echo '{"Token":"NULL","Message":"Failed Executing Query, Try again later"}';
					$db->Kill();
				}
			}
			else
			{
				echo '{"Token":"NULL","Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	 }
	 /** ------------------------------------------------------------------ */
	 public function checkAvail($uname)
	 {
	 	$essence	=	new Essentials();
		$salt		=	$essence->getHashSalt();

	
			$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			if (!$db->Error())
			{
				$profile_tbl	=	$essence->tblPrefix().'profile';
				if ($db->Query("SELECT * from $profile_tbl where username='$uname'"))
				{
					if($db->RowCount())
					{
						echo '{"Message":"Username Already Taken",'.'"Salt":"'.$salt.'"}';
				      	
					}
					else
					{
						echo '{"Message":"Username Available",'.'"Salt":"'.$salt.'"}';
						
					}
				}
				else
				{
					echo '{"Message":"Failed Executing Query, Try again later"}';
					$db->Kill();
				}
			}
			else
			{
				echo '{"Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	 }
/** -------------------profile details view with membership starts here----------- */
	  public function getUsrDetail($id,$skey)
	 {mysql_query('SET CHARACTER SET utf8'); 
	 	$essence	=	new Essentials();
		$secure     =   new secure();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		//$db1		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
		if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_tbl_extended	=	$essence->tblPrefix().'profile_extended';
				$profile_view_history	=	$essence->tblPrefix().'profile_view_history';
				$pic_tbl				=	$essence->tblPrefix().'profile_photo';
			    $membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		        $membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
				/*$sql	=	"	SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB  FROM  $profile_tbl ,  $profile_tbl_extended
 								WHERE  $profile_tbl.profile_id =$id 
								AND  $profile_tbl_extended.profile_id =$id";
				*/
				/*$sqlChk = "SELECT membership_type_id FROM $profile_tbl WHERE profile_id='$id'";
				$sqlChk1 = $db->Query($sqlChk);
				$sqlChkId = mysql_fetch_array($sqlChk1);
				$sqlChkMemId = $sqlChkId['membership_type_id'];
				
				$sqlMem = "SELECT membership_type_id FROM $membership_limit WHERE membership_type_id='$sqlChkMemId' AND membership_service_key='view_profiles'";
				$sqlMemId =$db->Query($sqlMem);
				$sqlMemTypeId = mysql_fetch_array($sqlMemId);
				$sqlResult = $sqlMemTypeId['membership_type_id'];
				//echo $sqlResult;
				if($sqlResult !=NULL)
				{*/
				$sql	=	"	SELECT p.profile_id,username,sex,match_sex,match_agerange,headline,has_photo,general_description,birthdate,real_name FROM  $profile_tbl as p
JOIN $profile_tbl_extended ON ($profile_tbl_extended.profile_id=$id)
LEFT JOIN `$pic_tbl` as ptbl ON (p.`profile_id` = `ptbl`.`profile_id` AND `ptbl`.`number`=0)
where p.profile_id =$id"; 
				//$time	=	time();	
					if ($db->Query($sql))
					{
						if($db->RowCount())
						{	//$row=$db->Row();
//print_r($row);
								$profile1	=	 $db->GetJSON();
								$profile1	=	str_replace('{','',$profile1);
								$url		=	$this->getThumbImage($id);
								if($url	==	"")
									$url	=	"NULL";
								$profile1	=	str_replace('}',',"Profile_Image":"'.$url.'"}',$profile1);
								$profile1	= 	str_replace("},", "}", $profile1);
								echo '{"Status":"Live",'.$profile1.'';
					}
					else
					{
						echo '{"Status":"Live","Message":"Incorrect ID"}';
					}	
				
				//}
			}
			else
			{
				echo '{"Status":"Live","Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
		}
		}
		else
		{
		   echo '{"Message":"Session Expired"}';
		}	
	 }
/** -------------------profile details view with membership ends here----- */
	  /** -----------------------profile details view backup starts here-------- */
	  	 public function getUsrDetailBackup($id)
	 {
	 	$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		//$db1		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_tbl_extended	=	$essence->tblPrefix().'profile_extended';
				$profile_view_history	=	$essence->tblPrefix().'$profile_tbl_view_history';
				 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
			
				/*$sql	=	"	SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB  FROM  $profile_tbl ,  $profile_tbl_extended
 								WHERE  $profile_tbl.profile_id =$id 
								AND  $profile_tbl_extended.profile_id =$id";
				*/
				$sql	=	"	SELECT p.profile_id,username,sex,match_sex,match_agerange,headline,has_photo,general_description,real_name,birthdate FROM  $profile_tbl as p
JOIN $profile_tbl_extended ON ($profile_tbl_extended.profile_id=$id)
LEFT JOIN `$pic_tbl` as ptbl ON (p.`profile_id` = `ptbl`.`profile_id` AND `ptbl`.`number`=0)
where p.profile_id =$id"; 
				
			

			//echo $sql;
				//$time	=	time();	
				if ($db->Query($sql))
				{
					if($db->RowCount())
					{	
							//$sql1="INSERT INTO $profile_view_history VALUES('','$id', '$vid', '$time')";
							//$db1->Query($sql1);							
							$profile1	=	 $db->GetJSON();
							
							$url		=	$this->getThumbImage($id);
							if($url	==	"")
								$url	=	"NULL";
							$profile1	=	str_replace('}',',"Profile_Image":"'.$url.'"}',$profile1);
							$profile1	= 	str_replace("},", "}", $profile1);
							echo $profile1;
					}
					else
					{
						echo '{"Message":"Incorrect ID"}';
					}	
				
				}
			}
			else
			{
				echo '{"Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	 }
	  /** ----------------------profile details view backup ends here--------- */
	  /****************profile detail with admin settings starts here*******************/
	  
	  public function getUsrDetailNew($id)
	 {
	 	$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		//$db1		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_tbl_extended	=	$essence->tblPrefix().'profile_extended';
				$profile_view_history	=	$essence->tblPrefix().'$profile_tbl_view_history';
				$pic_tbl				=	$essence->tblPrefix().'profile_photo';
				 $profile_field_tbl		=	$essence->tblPrefix().'profile_field';
				
				/*$sql	=	"	SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB  FROM  $profile_tbl ,  $profile_tbl_extended
 								WHERE  $profile_tbl.profile_id =$id 
								AND  $profile_tbl_extended.profile_id =$id";
				*/
				/*$sql	=	"	SELECT p.profile_id,username,sex,match_sex,match_agerange,headline,has_photo,general_description,real_name,birthdate,CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
CAST( ptbl.photo_id AS CHAR ) , '_',
CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM  $profile_tbl as p
JOIN $profile_tbl_extended ON ($profile_tbl_extended.profile_id=$id)
LEFT JOIN `$pic_tbl` as ptbl ON (p.`profile_id` = `ptbl`.`profile_id` AND `ptbl`.`number`=0)
where p.profile_id =$id"; 
				
				
				*/
				/*$sql	=	"	SELECT case when(select viewable_by_member from $profile_field_tbl as profile_id where name='profile_id') = '0'
								then '' else (select p.profile_id from $profile_tbl as p where p.profile_id =$id)  end as profile_id,
								case when(select viewable_by_member from $profile_field_tbl where name='email') = '0'
				                then '' else email end as email,
								case when (select viewable_by_member from $profile_field_tbl where name='username') = '0'
				                then '' else username end as username,
								case when (select viewable_by_member from $profile_field_tbl where name='sex') = '0'
				                then '' else sex end as sex,
								case when (select viewable_by_member from $profile_field_tbl where name='match_sex') = '0'
				                then '' else match_sex end as match_sex,
								case when (select viewable_by_member from $profile_field_tbl where name='headline') = '0'
				                then '' else headline end as headline,
								case when (select viewable_by_member from $profile_field_tbl where name='has_photo') = '0'
				                then '' else has_photo end as has_photo,
								case when (select viewable_by_member from $profile_field_tbl where name='general_description') = '0'
				                then '' else general_description end as general_description,
								case when (select viewable_by_member from $profile_field_tbl where name='birthdate') = '0'
				                then '' else birthdate end as birthdate,
								case when (select viewable_by_member from $profile_field_tbl where name='match_agerange') = '0'
				                then '' else match_agerange end as match_agerange,
								case when (select viewable_by_member from $profile_field_tbl where name='real_name') = '0'
				                then '' else real_name end as real_name,
								CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
CAST( ptbl.photo_id AS CHAR ) , '_',
CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM  $profile_tbl as p
JOIN $profile_tbl_extended pe ON (pe.profile_id=$id)
LEFT JOIN `$pic_tbl` as ptbl ON (p.`profile_id` = `ptbl`.`profile_id` AND `ptbl`.`number`=0) 
where p.profile_id =$id "; 
*/
$sql	=	"	SELECT p.profile_id,case when(select viewable_by_member from $profile_field_tbl where name='email') = '0'
				                then '' else email end as email,
								case when (select viewable_by_member from $profile_field_tbl where name='username') = '0'
				                then '' else username end as username,
								case when (select viewable_by_member from $profile_field_tbl where name='sex') = '0'
				                then '' else sex end as sex,
								case when (select viewable_by_member from $profile_field_tbl where name='match_sex') = '0'
				                then '' else match_sex end as match_sex,
								case when (select viewable_by_member from $profile_field_tbl where name='headline') = '0'
				                then '' else headline end as headline,
								case when (select viewable_by_member from $profile_field_tbl where name='has_photo') = '0'
				                then '' else has_photo end as has_photo,
								case when (select viewable_by_member from $profile_field_tbl where name='general_description') = '0'
				                then '' else general_description end as general_description,
								case when (select viewable_by_member from $profile_field_tbl where name='birthdate') = '0'
				                then '' else birthdate end as birthdate,
								case when (select viewable_by_member from $profile_field_tbl where name='match_agerange') = '0'
				                then '' else match_agerange end as match_agerange,
								case when (select viewable_by_member from $profile_field_tbl where name='real_name') = '0'
				                then '' else real_name end as real_name,
								CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
CAST( ptbl.photo_id AS CHAR ) , '_',
CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM  $profile_tbl as p
JOIN $profile_tbl_extended pe ON (pe.profile_id=$id)
LEFT JOIN `$pic_tbl` as ptbl ON (p.`profile_id` = `ptbl`.`profile_id` AND `ptbl`.`number`=0) 
where p.profile_id =$id "; 
				
				//echo $sql;


			//echo $sql;
				//$time	=	time();	
				if ($db->Query($sql))
				{
					if($db->RowCount())
					{	
							//$sql1="INSERT INTO $profile_view_history VALUES('','$id', '$vid', '$time')";
							//$db1->Query($sql1);							
							$profile1	=	 $db->GetJSON();
							
							$url		=	$this->getThumbImage($id);
							if($url	==	"")
								$url	=	"NULL";
							$profile1	=	str_replace('}',',"Profile_Image":"'.$url.'"}',$profile1);
							$profile1	= 	str_replace("},", "}", $profile1);
							echo $profile1;
					}
					else
					{
						echo '{"Message":"Incorrect ID"}';
					}	
				
				}
			}
			else
			{
				echo '{"Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	 }
	  /****************profile detail with admin settings ends here*******************/
	  public function getUsrDetails($id,$vid)
	 {
	 	$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_tbl_extended		=	$essence->tblPrefix().'profile_extended';
				$profile_view_history		=	$essence->tblPrefix().'profile_view_history';
				$sql	=	"	SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB  FROM  $profile_tbl ,  $profile_tbl_extended
 								WHERE  $profile_tbl.profile_id =$id 
								AND  $profile_tbl_extended.profile_id =$id";
								
				$time	=	time();				
				if ($db->Query($sql))
				{
					if($db->RowCount())
					{	
							$sql1="INSERT INTO $profile_view_history VALUES('',$vid, $id, $time)";
							$db1->Query($sql1);							
							$profile1	=	 $db->GetJSON();
							
							$url		=	$this->getThumbImage($id);
							if($url	==	"")
								$url	=	"NULL";
							$profile1	=	str_replace('}',',"Profile_Image":"'.$url.'"}',$profile1);
							$profile1	= 	str_replace("},", "}", $profile1);
							echo $profile1;
					}
					else
					{
						echo '{"Message":"Incorrect ID"}';
					}	
				
				}
			}
			else
			{
				echo '{"Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	 }
	  /** ------------------------------------------------------------------ */
	  /** -------------------profile view back up starts here--------- */
	  public function UsrDetailBackup($id,$vid)
	 {
	 	$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_tbl_extended		=	$essence->tblPrefix().'profile_extended';
				$viewtable			=	$essence->tblPrefix().'profile_view_history';
				
				/*
				$sql	=	"SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB  FROM  $profile_tbl ,  $profile_tbl_extended
 								WHERE  $profile_tbl.profile_id =$id 
								AND  $profile_tbl_extended.profile_id =$id";
				*/
				$sql	=	"SELECT $profile_tbl.profile_id,username,sex,match_sex,match_agerange,headline,has_photo,general_description,real_name,birthdate FROM  $profile_tbl ,$profile_tbl_extended WHERE  $profile_tbl.profile_id =$id AND  $profile_tbl_extended.profile_id =$id";
				if ($db->Query($sql))
				{
					if($db->RowCount())
					{	
							if($vid)
							{
								$kv	=	array('profile_id'=>$vid,'viewed_id'=>$id,'time_stamp'=>time());
								//print_r($kv);
								$abc	=	$db1->InsertRow($viewtable,$kv);
							}
							$profile1	=	 $db->GetJSON();
							
							$url		=	$this->getThumbImage($id);
							if($url	==	"")
								$url	=	"NULL";
							$profile1	=	str_replace('}',',"Profile_Image":"'.$url.'"}',$profile1);
							$profile1	= 	str_replace("},", "}", $profile1);
							echo $profile1;
					}
					else
					{
						echo '{"Message":"Incorrect ID"}';
					}	
				
				}
			}
			else
			{
				echo '{"Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	 }
		  /** -------------------profile view backup starts here--------- */
	/***************profile view with membership checking starts here****************/
    public function UsrDetail($id,$skey,$vid)
     {
mysql_query('SET CHARACTER SET utf8');
         $essence    =    new Essentials();
        $secure     =   new secure();
        $db         =     new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
        $db1         =     new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
        $res = $secure->CheckSecure($vid,$skey);
        $res=1;
        if($res==1)
        {
        if (!$db->Error())
            {
                $profile_tbl            =    $essence->tblPrefix().'profile';
                $profile_tbl_extended        =    $essence->tblPrefix().'profile_extended';
                $viewtable            =    $essence->tblPrefix().'profile_view_history';
                $membership_limit        =    $essence->tblPrefix().'link_membership_type_service';
                $membership_srv        =    $essence->tblPrefix().'link_membership_service_limit';
               
                                $sql    =    "SELECT $profile_tbl.profile_id,username,sex,match_sex,match_agerange,headline,has_photo,real_name,
                                        general_description,birthdate FROM  $profile_tbl ,$profile_tbl_extended WHERE  $profile_tbl.profile_id =$id
                                        AND $profile_tbl_extended.profile_id =$id";
$res = $db->Query($sql);
$i=0;
while($row1 = mysql_fetch_array($res))
{
					if($vid)
                                                {
                                                    $kv    =    array('profile_id'=>$vid,'viewed_id'=>$id,'time_stamp'=>time());
                                                  //  print_r($kv);
                                                    $abc    =    $db1->InsertRow($viewtable,$kv);
                                                }
						$url        =    $this->getThumbImage($id);
$url=str_replace('\\/','/',$url);
                                                if($url    ==    "")
                                                    $url    =    "NULL";

$realname=$row1['real_name'];
if($realname=="")
{$realname="";}



$result[$i]= array('profile_id'=>$row1['profile_id'],'username'=>$row1['username'],'sex'=>$row1['sex'],'real_name'=>$realname,'match_sex'=>$row1['match_sex'],'match_agerange'=>$row1['match_agerange'],'headline'=>$row1['headline'],'has_photo'=>$row1['has_photo'],'general_description'=>$row1['general_description'],'birthdate'=>$row1['birthdate'],'Profile_Image'=>$url);
$i++;
}
//print_r($result);
$final = array();
if (is_array($result))
{
	foreach($result as $array)
	{
		
			array_push($final, $array);
	}
}	
			//$i=$i-1;
//print_r($final);
			 $final	=	 '{"Status":"Live", '.json_encode($final).'}';
						$final= str_replace("},]","}",$final);
$final=str_replace("[{","",$final);
echo str_replace("}]}","}",$final);

                           /*    if ($db->Query($sql))
                               {
                                      if($db->RowCount())
                                        { 

if($vid)
                                                {
                                                    $kv    =    array('profile_id'=>$vid,'viewed_id'=>$id,'time_stamp'=>time());
                                                    print_r($kv);
                                                    $abc    =    $db1->InsertRow($viewtable,$kv);
                                                }

                                                $profile1    =     $db->GetJSON();
                                                $profile1    =    str_replace('{','',$profile1);
                                                $url        =    $this->getThumbImage($id);
                                                if($url    ==    "")
                                                    $url    =    "NULL";
                                                $profile1    =    str_replace('}',',"Profile_Image":"'.$url.'"}',$profile1);
                                                $profile1    =     str_replace("},", "}", $profile1);
                                                    echo '{"Status":"Live",'.$profile1;
                                       
 }

                                        else
                                        {
                                            echo '{"Status":"Live","Message":"Incorrect ID"}';
                                        }    
                     
                                  }
*/
                       
        }
            else
            {
                echo '{"Status":"Live","Message":"Error connecting to database. Check configuration"}';
                $db->Kill();
            }
            }
            else
        {
        echo '{"Message":"Session Expired"}';
        }
     }
     /***************profile view with membership checking ends here****************/
	

	 /***************profile view with admin settings starts here****************/
	   public function UsrDetailNew($id,$vid)
	 {
	 	$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_tbl_extended		=	$essence->tblPrefix().'profile_extended';
				$viewtable			=	$essence->tblPrefix().'profile_view_history';
				$profile_field_tbl		=	$essence->tblPrefix().'profile_field';
				$pic_tbl				=	$essence->tblPrefix().'profile_photo';

				/*
				$sql	=	"SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB  FROM  $profile_tbl ,  $profile_tbl_extended
 								WHERE  $profile_tbl.profile_id =$id 
								AND  $profile_tbl_extended.profile_id =$id";
				*/
				$sql1	=	"SELECT $profile_tbl.profile_id,username,sex,match_sex,match_agerange,headline,general_description,real_name,birthdate FROM  $profile_tbl ,$profile_tbl_extended WHERE  $profile_tbl.profile_id =$id AND  $profile_tbl_extended.profile_id =$id";
				
				/*$sql	=	"	SELECT 
								case when(select viewable_by_member from $profile_field_tbl as profile_id where name='profile_id') ='0'
								then '' else (select profile_id from $profile_tbl as pp where pp.profile_id =$id)  end as profile_id,
								case when (select viewable_by_member from $profile_field_tbl where name='email') = '0'
				                then '' else email end as email,
								case when (select viewable_by_member from $profile_field_tbl where name='username') = '0'
				                then '' else username end as username,
								case when (select viewable_by_member from $profile_field_tbl where name='sex') = '0'
				                then '' else sex end as sex,
								case when (select viewable_by_member from $profile_field_tbl where name='match_sex') = '0'
				                then '' else match_sex end as match_sex,
								case when (select viewable_by_member from $profile_field_tbl where name='headline') = '0'
				                then '' else headline end as headline,
								case when (select viewable_by_member from $profile_field_tbl where name='has_photo') = '0'
				                then '' else has_photo end as has_photo,
								case when (select viewable_by_member from $profile_field_tbl where name='general_description') = '0'
				                then '' else general_description end as general_description,
								case when (select viewable_by_member from $profile_field_tbl where name='birthdate') = '0'
				                then '' else birthdate end as birthdate,
								case when (select viewable_by_member from $profile_field_tbl where name='match_agerange') = '0'
				                then '' else match_agerange end as match_agerange,
								case when (select viewable_by_member from $profile_field_tbl where name='real_name') = '0'
				                then '' else real_name end as real_name,
								CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
CAST( ptbl.photo_id AS CHAR ) , '_',
CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM  $profile_tbl as p
JOIN $profile_tbl_extended ON ($profile_tbl_extended.profile_id=$id)
LEFT JOIN `$pic_tbl` as ptbl ON (p.`profile_id` = `ptbl`.`profile_id` AND `ptbl`.`number`=0)
where p.profile_id =$id"; 
*/

$sql	=	"	SELECT p.profile_id,case when (select viewable_by_member from $profile_field_tbl where name='email') = '0'
				                then '' else email end as email,
								case when (select viewable_by_member from $profile_field_tbl where name='username') = '0'
				                then '' else username end as username,
								case when (select viewable_by_member from $profile_field_tbl where name='sex') = '0'
				                then '' else sex end as sex,
								case when (select viewable_by_member from $profile_field_tbl where name='match_sex') = '0'
				                then '' else match_sex end as match_sex,
								case when (select viewable_by_member from $profile_field_tbl where name='headline') = '0'
				                then '' else headline end as headline,
								case when (select viewable_by_member from $profile_field_tbl where name='has_photo') = '0'
				                then '' else has_photo end as has_photo,
								case when (select viewable_by_member from $profile_field_tbl where name='general_description') = '0'
				                then '' else general_description end as general_description,
								case when (select viewable_by_member from $profile_field_tbl where name='birthdate') = '0'
				                then '' else birthdate end as birthdate,
								case when (select viewable_by_member from $profile_field_tbl where name='match_agerange') = '0'
				                then '' else match_agerange end as match_agerange,
								case when (select viewable_by_member from $profile_field_tbl where name='real_name') = '0'
				                then '' else real_name end as real_name,
								CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
CAST( ptbl.photo_id AS CHAR ) , '_',
CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM  $profile_tbl as p
JOIN $profile_tbl_extended ON ($profile_tbl_extended.profile_id=$id)
LEFT JOIN `$pic_tbl` as ptbl ON (p.`profile_id` = `ptbl`.`profile_id` AND `ptbl`.`number`=0)
where p.profile_id =$id"; 
				
				//echo $sql;
				
				if ($db->Query($sql))
				{
					if($db->RowCount())
					{	
							if($vid)
							{
								$kv	=	array('profile_id'=>$vid,'viewed_id'=>$id,'time_stamp'=>time());
								//print_r($kv);
								$abc	=	$db1->InsertRow($viewtable,$kv);
							}
							$profile1	=	 $db->GetJSON();
							
							$url		=	$this->getThumbImage($id);
							if($url	==	"")
								$url	=	"NULL";
							$profile1	=	str_replace('}',',"Profile_Image":"'.$url.'"}',$profile1);
							$profile1	= 	str_replace("},", "}", $profile1);
							echo $profile1;
					}
					else
					{
						echo '{"Message":"Incorrect ID"}';
					}	
				

				}
			}
			else
			{
				echo '{"Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	 }
	 
	 
	 /***************profile view with admin settings ends here****************/
 	 public function getConvMsg($id)
	 {
	 	$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		if (!$db->Error())
			{
				$mail_conv_tbl			=	$essence->tblPrefix().'mailbox_conversation';
				$mail_msg_tbl			=	$essence->tblPrefix().'mailbox_message';
				$sql	=	"
								SELECT * FROM  `$mail_conv_tbl` ,  `$mail_msg_tbl`
 								WHERE  `$mail_conv_tbl`.conversation_id =$id 
								AND  `$mail_msg_tbl`.conversation_id =`$mail_conv_tbl`.conversation_id";
				
			
				if ($db->Query($sql))
				{
					if($db->RowCount())
					{	
							
							$profile	=	 $db->GetJSON();
							
							$url		=	$this->getThumbImage($id);
							if($url	==	"")
								$url	=	"NULL";
							$profile	=	str_replace('}',',"Profile_Image":"'.$url.'"}',$profile);
							echo $profile;
					}
					else
					{
						echo '{"Message":"Incorrect ID"}';
					}	
				
				}
			}
			else
			{
				echo '{"Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	 }
	 /** ------------------------------------------------------------------ */
	 public function userJoin($kv)
	 {
		$essence				=	new Essentials();
		$profile_table			=	$essence->tblPrefix().'profile';	
		$db 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());	
	 	$profile_id				=	$db->InsertRow($profile_table,$kv);
		$newkv					= 	array('profile_id' => $profile_id);
		$profile_table_extend	=	$essence->tblPrefix().'profile_extended';
		$db->InsertRow($profile_table_extend,$newkv);
		echo '{"Profile_Id":"'.$profile_id.'","Profile_Pic":"","Notifications" : "0"}';
	 }
	  
	  /** ------------------------------------------------------------------ */
	 public function BookmarkProfileD($pid,$bid,$skey)
	 {
		$essence		=	new Essentials();
		$secure     =   new secure();
		$bookmark_tbl	=	$essence->tblPrefix().'profile_bookmark_list';
	    $profile_tbl = $essence->tblPrefix().'profile';
		$membership_limit  = $essence->tblPrefix().'link_membership_type_service';
		$membership_srv  = $essence->tblPrefix().'link_membership_service_limit';	
		 $db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		  //check user sign in or not
		 $res = $secure->CheckSecure($pid,$skey);
		 if($res==1)
		 { 
		 if (!$db->Error())
			{
					 //checking membership 
			$sqlChk = "SELECT membership_type_id FROM $profile_tbl WHERE profile_id='$pid'";
			
			$sqlChkId = $db->Query($sqlChk);
			$sqlMemId = mysql_fetch_array($sqlChkId);
			$sqlMemIdType = $sqlMemId['membership_type_id'];
			
			$sqlChkSrv = "SELECT membership_type_id FROM $membership_limit WHERE membership_type_id='$sqlMemIdType' AND membership_service_key='bookmark_members'";
			$sqlChkSrv1 = $db->Query($sqlChkSrv);
			$sqlChkSrv11 = mysql_fetch_array($sqlChkSrv1);
			$sqlChkSrv = $sqlChkSrv11['membership_type_id'];
			if($sqlChkSrv != NULL)
				{
					$newkv	=	array('profile_id' => $pid, 'bookmarked_id' => $bid);		
					$db->InsertRow($bookmark_tbl,$newkv);
					echo '{"Status":"Live","Message":"Bookmarked"}';
				}
				else
				{
				 echo '{"Message":"No permission for bookmark"}';
				}
		}
		}	
		else
		{
			echo '{"Message":"Session Expired"}';
		}
	 }
	 /** ------------------------------------------------------------------ */
	   /** ------------------------------------------------------------------ */
	 public function BookmarkProfile($pid,$bid,$skey)
	 {
		$essence		=	new Essentials();
		$secure     =   new secure();
		$bookmark_tbl	=	$essence->tblPrefix().'profile_bookmark_list';
	    $profile_tbl = $essence->tblPrefix().'profile';
		$membership_limit  = $essence->tblPrefix().'link_membership_type_service';
		$membership_srv  = $essence->tblPrefix().'link_membership_service_limit';	
		$db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		  //check user sign in or not
		 $res = $secure->CheckSecure($pid,$skey);
		 if($res==1)
		 { 
		 if (!$db->Error())
			{
					
					//$newkv	=	array('profile_id' => $pid, 'bookmarked_id' => $bid);		
					//$db->InsertRow($bookmark_tbl,$newkv);
					$sql = "INSERT INTO $bookmark_tbl VALUES('$pid','$bid')";
					if($db->Query1($sql))
					{
					echo '{"Status":"Live","Message":"Bookmarked"}';
					}
					else
					{
					 echo '{"Status":"Live","Message":"Already Bookmarked"}';

					}
			}
		}	
		else
		{
			echo '{"Message":"Session Expired"}';
		}
	 }
	 /** ------------------------------------------------------------------ */
	 public function getNewMembers($id,$skey)
	 {
		 $essence				=	new Essentials();
		 $secure                =   new secure();
		 $profile_table			=	$essence->tblPrefix().'profile';	
		 $profile_tbl_online	=	$essence->tblPrefix().'profile_online';
		 $location_table 		=	$essence->tblPrefix().'location_country';
		 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
		 
		 $db 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//check user sign in or not
		$res = $secure->CheckSecure($id,$skey);
		if($res == 1)
		{
		
		 if (!$db->Error())
			{
				/*$sql	=	"SELECT main.profile_id,username,sex,custom_location,Country_str_name,hash as OnlineStatus, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB, photo_id, ptbl.index as pindex FROM $profile_table AS `main` LEFT JOIN `$profile_table_online` AS `online` USING( `profile_id` ) LEFT JOIN `$country_tbl` AS `ctbl` ON (main.`country_id`= ctbl.`Country_str_code`) LEFT JOIN ``$pic_tbl`` AS `ptbl` ON(  main.`profile_id` = ptbl.`profile_id` and ptbl.number=0 ) ORDER BY `join_stamp` DESC LIMIT 0 , 10";*/
				$sql	=	"SELECT main.profile_id,username,sex,custom_location,Country_str_name,hash as OnlineStatus, 
year(CURRENT_TIMESTAMP)-year(birthdate) as DOB, photo_id,
CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
CAST( ptbl.photo_id AS CHAR ) , '_',
CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic,

 ptbl.index as pindex FROM $profile_table AS `main`
 LEFT JOIN `$profile_tbl_online` AS `online` USING( `profile_id` ) 
LEFT JOIN `$location_table` AS `ctbl` ON (main.`country_id`= `ctbl`.`Country_str_code`) 
LEFT JOIN `$pic_tbl` AS `ptbl` ON( main.`profile_id` = ptbl.`profile_id` and ptbl.number=0 ) 
where main.status='active' and  main.profile_id!='$id' and main.email_verified='yes'
ORDER BY `join_stamp`
 DESC LIMIT 0 , 10";
				
				
				if($db->Query($sql))
				{
					if($db->RowCount())
					{
						$profile = '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo $profile = str_replace("},]", "}]", $profile);
					}
				}
				
			}
		else
			{
			echo '{"Status":"Live","count":"0","Message":"No New Members"}';
			}
		}
		else
		{
				echo '{"Message":"Session Expired"}';
		}		
	 }
	 /** ------------------------------------------------------------------ */
	
 /***************New members with limit start here******************/
	 public function getNewMembersByLimit($id,$skey,$start,$limit)
	 {
		 $essence				=	new Essentials();
		 $secure                =   new secure();
		 $profile_table			=	$essence->tblPrefix().'profile';	
		 $profile_tbl_online	=	$essence->tblPrefix().'profile_online';
		 $location_table 		=	$essence->tblPrefix().'location_country';
		 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
		 $db 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		 
		 //check user sign in
		 $res = $secure->CheckSecure($id,$skey);
                 //$res=1;
		 if($res==1)
        {
		 
		 if (!$db->Error())
			{
				/*$sql	=	"SELECT main.profile_id,username,sex,custom_location,Country_str_name,hash as OnlineStatus, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB, photo_id, ptbl.index as pindex FROM $profile_table AS `main` LEFT JOIN `$profile_table_online` AS `online` USING( `profile_id` ) LEFT JOIN `$country_tbl` AS `ctbl` ON (main.`country_id`= ctbl.`Country_str_code`) LEFT JOIN ``$pic_tbl`` AS `ptbl` ON(  main.`profile_id` = ptbl.`profile_id` and ptbl.number=0 ) ORDER BY `join_stamp` DESC LIMIT 0 , 10";*/
			



	$sql	=	"SELECT main.profile_id,username,sex,custom_location,Country_str_name,hash as OnlineStatus, 
year(CURRENT_TIMESTAMP)-year(birthdate) as DOB, photo_id,
CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
CAST( ptbl.photo_id AS CHAR ) , '_',
CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic,

 ptbl.index as pindex FROM $profile_table AS `main`
 LEFT JOIN `$profile_tbl_online` AS `online` USING( `profile_id` ) 
LEFT JOIN `$location_table` AS `ctbl` ON (main.`country_id`= `ctbl`.`Country_str_code`) 
LEFT JOIN `$pic_tbl` AS `ptbl` ON( main.`profile_id` = ptbl.`profile_id` and ptbl.number=0 ) 
where main.status='active' and main.profile_id!='$id' 
ORDER BY `join_stamp`
 DESC LIMIT  $start, $limit";

//for counting total rows
		 $sql1 ="SELECT main.profile_id,username,sex,custom_location,Country_str_name,hash as OnlineStatus, 
		year(CURRENT_TIMESTAMP)-year(birthdate) as DOB, photo_id,
		CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
		CAST( ptbl.photo_id AS CHAR ) , '_',
		CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic,
		
		 ptbl.index as pindex FROM $profile_table AS `main`
		 LEFT JOIN `$profile_tbl_online` AS `online` USING( `profile_id` ) 
		LEFT JOIN `$location_table` AS `ctbl` ON (main.`country_id`= `ctbl`.`Country_str_code`) 
		LEFT JOIN `$pic_tbl` AS `ptbl` ON( main.`profile_id` = ptbl.`profile_id` and ptbl.number=0 )
		where main.status='active' and main.profile_id!='$id'  
		ORDER BY `join_stamp`";
		$db->Query($sql1);
		$totalCount=$db->RowCount();
				if($db->Query($sql))
				{
					if($db->RowCount())
					{
						$profile = '{"Status":"Live","Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo $profile = str_replace("},]", "}]", $profile);
					}
				}
				
			}
		else
			{
			echo '{"Status":"Live","count":"0","Message":"No New Members"}';
			}	
		}
		else
		{
      		echo '{"Message":"Session Expired"}';
        }	
			
	 }
	  public function getOnlineMembers($id,$skey)
	 	{
		 $essence				=	new Essentials();
		 $secure     =   new secure();
		 $online_table 			=	$essence->tblPrefix().'profile_online';	
		 $profile_table			=	$essence->tblPrefix().'profile';
		 $profile_table_extend	=	$essence->tblPrefix().'location_country';
		 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
		 $db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		 $res = $secure->CheckSecure($id,$skey);
			 if($res==1)
				{
						if (!$db->Error())
					{
						 $sql	=	"SELECT `$profile_table`.profile_id, `$profile_table`.username, `$profile_table`.sex, $profile_table.birthdate, $profile_table.custom_location, 
		$profile_table.country_id, $profile_table.state_id, $profile_table.city_id, $profile_table_extend.Country_str_name,$profile_table.has_photo, year(
		CURRENT_TIMESTAMP ) - year( birthdate ) AS DOB, CONCAT( '/', 'userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) , '_', CAST( 
		`$pic_tbl`.photo_id AS CHAR ) , '_', CAST( `$pic_tbl`.index AS CHAR ) , '.jpg' ) AS Profile_Pic
		FROM $profile_table JOIN $online_table ON $online_table.profile_id= $profile_table.profile_id join $profile_table_extend ON $profile_table_extend.Country_str_code = $profile_table.country_id 
		LEFT JOIN `$pic_tbl` ON $profile_table.profile_id=`$pic_tbl`.profile_id AND `$pic_tbl`.`number` = 0 
		where $profile_table.status='active' and $profile_table.profile_id!='$id' and $profile_table.email_verified='yes'
		ORDER BY expiration_time DESC";
		
					if($db->Query($sql))
						{ 
							if($db->RowCount())
							{
								$profile = '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
								echo $profile = str_replace("},]", "}]", $profile);
							}
						}
					}
					else
						{
						echo '{"Status":"Live","count":"0","Message":"No Online Members"}';
						}	
					}
					else
					{
						echo '{"Message":"Session Expired"}';
					}
		}	 
	 /*************Online members with limit starts here***********/
	 	  /*************Online members with limit starts here***********/
	 	 

 public function getOnlineMembersByLimit($id, $skey, $start, $limit) { 
        $essence = new Essentials(); 
        $secure = new secure(); 
        $online_table = $essence->tblPrefix() . 'profile_online'; 
        $profile_table = $essence->tblPrefix() . 'profile'; 
        $country_table = $essence->tblPrefix() . 'location_country'; 
        $pic_tbl = $essence->tblPrefix() . 'profile_photo'; 
        $state_table = $essence->tblPrefix() .'location_state'; 
        $city_table = $essence->tblPrefix() .'location_city'; 
        $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        $res = $secure->CheckSecure($id, $skey); 
        if ($res == 1) { 
            if (!$db->Error()) { 
           $sql	=	"SELECT * FROM (SELECT DISTINCT P.`profile_id`, username , sex , year(CURRENT_TIMESTAMP ) - year( birthdate ) AS DOB, 
                C.Country_str_name,CONCAT(S.Admin1_str_name,',',Ci.Feature_str_name) as custom_location, 
                CONCAT( '/$', 'userfiles/thumb_', CAST( `ptbl`.profile_id AS CHAR ) , '_', CAST(`ptbl`.photo_id AS CHAR ) , '_', CAST( `ptbl`.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
                FROM `$online_table` O 
                LEFT JOIN $profile_table P USING (profile_id) 
                LEFT JOIN $country_table C ON (C.Country_str_code=P.country_id) 
                LEFT JOIN $state_table S ON (S.Admin1_str_code=P.state_id) 
                LEFT JOIN $city_table Ci ON (Ci.Feature_int_id=P.city_id) 
                LEFT JOIN `$pic_tbl` AS `ptbl` ON( P.`profile_id` = ptbl.`profile_id` and ptbl.number=0 and ptbl.status='active' ) 
                WHERE (P.profile_id!=$id) ORDER BY `expiration_time` DESC) as Main LIMIT $start,$limit"; 
			 
			 
			//for counting total rows 
			$sqlT	=	"SELECT DISTINCT `profile_id`, username , sex , year(CURRENT_TIMESTAMP ) - year( birthdate ) AS DOB, 
                C.Country_str_name,CONCAT(S.Admin1_str_name,',',Ci.Feature_str_name) as custom_location 
                FROM `$online_table` O 
                LEFT JOIN $profile_table P USING (profile_id) 
                LEFT JOIN $country_table C ON (C.Country_str_code=P.country_id) 
                LEFT JOIN $state_table S ON (S.Admin1_str_code=P.state_id) 
                LEFT JOIN $city_table Ci ON (Ci.Feature_int_id=P.city_id) 
                WHERE (profile_id!=$id) ORDER BY `expiration_time` DESC"; 
			$db->Query($sqlT); 
                
                
                $db->Query($sqlT); 
                $totalCount = $db->RowCount(); 


                if ($db->Query($sql)) { 
                    if ($db->RowCount()) { 
                        $profile = '{"Status":"Live","Total rows":' . $totalCount . ',"count": ' . $db->RowCount() . ',"result": [' . $db->GetJSON() . ']}'; 
                        echo $profile = str_replace("},]", "}]", $profile); 
                    } 
                } 
            } else { 
                echo '{"Status":"Live","Total rows":"0","count":"0","Message":"No Online Members"}'; 
            } 
        } else { 
            echo '{"Message":"Session Expired"}'; 
        } 
        $db->Close(); 
    } 







 public function getOnlineMembersByLimit1($id,$skey,$start,$limit)
	 	{
		 $essence				=	new Essentials();
		 $secure     =   new secure();
		 $online_table 			=	$essence->tblPrefix().'profile_online';	
		 $profile_table			=	$essence->tblPrefix().'profile';
		 $profile_table_extend	=	$essence->tblPrefix().'location_country';
		 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
		 $db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		 $res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
			if (!$db->Error())
			{
			$sql	=	"SELECT `$profile_table`.profile_id, `$profile_table`.username, `$profile_table`.sex, $profile_table.birthdate, $profile_table.custom_location, 
			$profile_table.country_id, $profile_table.state_id, $profile_table.city_id, $profile_table_extend.Country_str_name,$profile_table.has_photo, year(
			CURRENT_TIMESTAMP ) - year( birthdate ) AS DOB, CONCAT( '/', 'userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) , '_', CAST( 
			`$pic_tbl`.photo_id AS CHAR ) , '_', CAST( `$pic_tbl`.index AS CHAR ) , '.jpg' ) AS Profile_Pic
			FROM $profile_table JOIN $online_table ON $online_table.profile_id= $profile_table.profile_id join $profile_table_extend ON $profile_table_extend.Country_str_code = $profile_table.country_id 
			LEFT JOIN `$pic_tbl` ON $profile_table.profile_id=`$pic_tbl`.profile_id AND `$pic_tbl`.`number` = 0 
			where $profile_table.status='active' and $profile_table.profile_id!='$id' and $profile_table.email_verified='yes' ORDER BY expiration_time DESC LIMIT $start,$limit";
			
			
			//for counting total rows
			$sqlT	=	"SELECT `$profile_table`.profile_id, `$profile_table`.username, `$profile_table`.sex, $profile_table.birthdate, $profile_table.custom_location, 
			$profile_table.country_id, $profile_table.state_id, $profile_table.city_id, $profile_table_extend.Country_str_name,$profile_table.has_photo, year(
			CURRENT_TIMESTAMP ) - year( birthdate ) AS DOB, CONCAT( '/', 'userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) , '_', CAST( 
			`$pic_tbl`.photo_id AS CHAR ) , '_', CAST( `$pic_tbl`.index AS CHAR ) , '.jpg' ) AS Profile_Pic
			FROM $profile_table JOIN $online_table ON $online_table.profile_id= $profile_table.profile_id join $profile_table_extend ON $profile_table_extend.Country_str_code = $profile_table.country_id 
			LEFT JOIN `$pic_tbl` ON $profile_table.profile_id=`$pic_tbl`.profile_id AND `$pic_tbl`.`number` = 0 
			where $profile_table.status='active' and $profile_table.profile_id!='$id' and $profile_table.email_verified='yes' ORDER BY expiration_time";
			$db->Query($sqlT);
			$totalCount = $db->RowCount();
			
			
				if($db->Query($sql))
				{ 
					if($db->RowCount())
					{
					$profile = '{"Status":"Live","Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo $profile = str_replace("},]", "}]", $profile);
					}
				}
			}
			else
			{
			echo '{"Status":"Live","Total rows":"0","count":"0","Message":"No Online Members"}';
			}	
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
		}
	 
	  /*************Online members with limit ends here***********/
	  
	/**************bookmark members starts here****************/
	 public function BookmarkedMembersBackup($pid)
	 {
		 $essence              	=	new Essentials();
		 $db 		            = 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		 $profile_tbl			=	$essence->tblPrefix().'profile';
		 $profile_tbl_extended	=	$essence->tblPrefix().'profile_bookmark_list';
		 $location_table  		=	$essence->tblPrefix().'location_country';
	 	 $profile_state			=	$essence->tblPrefix().'location_state';
		 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
		 $membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		 $membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
		 $bookmark_list		    =	$essence->tblPrefix().'profile_bookmark_list';
		 
		 if (!$db->Error())
			{
				$sqlMem = "SELECT membership_type_id FROM $profile_tbl WHERE profile_id='$pid'";   //get membership id of a user
				$sqlResult = $db->Query($sqlMem);
				$sqlMemId = mysql_fetch_array($sqlResult);
				$sqlResultId = $sqlMemId['membership_type_id'];

				//check the membership of particular profile id
				$sql1 = "SELECT membership_type_id FROM  $membership_limit WHERE membership_type_id = '$sqlResultId' AND membership_service_key = 'bookmark_members'";	
				$sqlResult1 = $db->Query($sql1);
				$sqlMemId1 = mysql_fetch_array($sqlResult1);
				$sqlResultId1 = $sqlMemId1['membership_type_id'];
				
				if($sqlResultId1 !=NULL)   //check membership availbility of of user
				{
					//check the membership limit 
					$sql2 = "SELECT limit FROM $membership_srv WHERE membership_type_id = '$sqlResultId' AND membership_service_key ='bookmark_members'";
					$sqlResult2 = $db->Query($sql2);
					$sqlMemId2 = mysql_fetch_array($sqlResult1);
					$sqlResultId2 = $sqlMemId2['limit'];
					//check the limit of bookmarking members
					$sql3 = "SELECT COUNT(*) FROM $bookmark_list WHERE profile_id = '$pid'";
					$sqlBookMark = $db->Query($sql3);
					$sqlBookMarkResult = mysql_fetch_array($sqlBookMark);
					$sqlBookMarkLimit = $sqlBookMarkResult['COUNT(*)'];
					
					
					//check limit available in membership
					if($sqlResultId2 !=NULL)    //check limit available
					{
						 if($sqlResultId2 <= $sqlBookMarkLimit)   //check limit count exceed
						 {
					
						  $sql					=	"SELECT p.profile_id,username,sex,custom_location,year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,
													 Country_str_name as Country_str_name, CONCAT( '/','userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) ,
													 '_', CAST( `$pic_tbl`.photo_id AS CHAR ) ,  '_', CAST( `$pic_tbl`.index AS CHAR ) ,  '.jpg' ) 
													 as Profile_Pic FROM $profile_tbl as p
													 JOIN $profile_tbl_extended ON (p.profile_id =`$profile_tbl_extended`.bookmarked_id)
													 JOIN $location_table ON (`$location_table`.Country_str_code=p.country_id )
													 LEFT JOIN $pic_tbl ON (`$pic_tbl`.profile_id = p.profile_id  AND `$pic_tbl`.number = 0)
													 where `$profile_tbl_extended`.profile_id =$pid";
				
										if ($db->Query($sql))
										{
											if($db->RowCount())
											{
												$profile = '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
												echo $profile = str_replace("},]", "}]", $profile);
											}
											else
											{
												echo '{"count":"0","Message":"Incorrect ID"}';
											}	
										}
							}
							else
							{
									echo '{"Message":"Limit Exceed"}';
							}
						}
						else
						{
							$sql					=	"SELECT p.profile_id,username,sex,custom_location,year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,
													 Country_str_name as Country_str_name, CONCAT( '/','userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) ,
													 '_', CAST( `$pic_tbl`.photo_id AS CHAR ) ,  '_', CAST( `$pic_tbl`.index AS CHAR ) ,  '.jpg' ) 
													 as Profile_Pic FROM $profile_tbl as p
													 JOIN $profile_tbl_extended ON (p.profile_id =`$profile_tbl_extended`.bookmarked_id)
													 JOIN $location_table ON (`$location_table`.Country_str_code=p.country_id )
													 LEFT JOIN $pic_tbl ON (`$pic_tbl`.profile_id = p.profile_id  AND `$pic_tbl`.number = 0)
													 where `$profile_tbl_extended`.profile_id =$pid";
				
										if ($db->Query($sql))
										{
											if($db->RowCount())
											{
												$profile = '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
												echo $profile = str_replace("},]", "}]", $profile);
											}
											else
											{
												echo '{"count":"0","Message":"Incorrect ID"}';
											}	
										}
						}
				}
				else
				{
						echo '{"Message":"Membership Denied"}';
				}		
	 		}
	 }


	/**************bookmark members ends here****************/
	/**************bookmark members backup  starts here****************/
		 public function BookmarkedMembers($pid,$skey)
	 {
		 $essence	=	new Essentials();
		 $secure     =   new secure();
		 $db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		 $profile_tbl			=	$essence->tblPrefix().'profile';
		 $profile_tbl_extended	=	$essence->tblPrefix().'profile_bookmark_list';
		 $location_table  		=	$essence->tblPrefix().'location_country';
	 	 $profile_state			=	$essence->tblPrefix().'location_state';
		 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
		  $res = $secure->CheckSecure($pid,$skey);
		 if($res==1)
		{
		 if (!$db->Error())
			{
		
				/*$sql					=	"
				SELECT * ,year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name as Country_str_name, CONCAT( '/','userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) ,  '_', 
				CAST( `$pic_tbl`.photo_id AS CHAR ) ,  '_', CAST( `$pic_tbl`.index AS CHAR ) ,  '.jpg' ) 
				as Profile_Pic
				FROM `$profile_tbl` ,`$profile_tbl_extended`,`$location_table`,`$pic_tbl`
				WHERE `$profile_tbl`.profile_id =`$profile_tbl_extended`.bookmarked_id AND
				`$profile_tbl_extended`.profile_id =$pid AND
				`$location_table`.Country_str_code=`$profile_tbl`.country_id AND
				`$pic_tbl`.profile_id = `$profile_tbl`.profile_id AND
				`$pic_tbl`.number = 0";
				
				*/
								
					$sql					=	"
				SELECT p.profile_id,username,sex,custom_location,year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name as Country_str_name, CONCAT( '/','userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) ,  '_', 
				CAST( `$pic_tbl`.photo_id AS CHAR ) ,  '_', CAST( `$pic_tbl`.index AS CHAR ) ,  '.jpg' ) 
				as Profile_Pic
				FROM $profile_tbl as p
				JOIN $profile_tbl_extended ON (p.profile_id =`$profile_tbl_extended`.bookmarked_id)
				JOIN $location_table ON (`$location_table`.Country_str_code=p.country_id )
				LEFT JOIN $pic_tbl ON (`$pic_tbl`.profile_id = p.profile_id  AND `$pic_tbl`.number = 0)
				where p.status='active' AND `$profile_tbl_extended`.profile_id =$pid";
/*				 $sql					=	"
				SELECT *
					FROM (
					
					SELECT $profile_tbl.profile_id, $profile_tbl.username, $profile_tbl.birthdate, year(
					CURRENT_TIMESTAMP ) - year( $profile_tbl.birthdate ) AS DOB, $profile_tbl.sex, Country_str_name AS Country_str_name, $profile_tbl.custom_location
					FROM `$profile_tbl` , `$profile_tbl_extended` , `$location_table`
					WHERE `$profile_tbl`.profile_id = `$profile_tbl_extended`.bookmarked_id
					AND `$profile_tbl_extended`.profile_id =$pid
					AND `$location_table`.Country_str_code = `$profile_tbl`.country_id
					) AS X
					LEFT JOIN (
					
					SELECT  $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id,$pic_tbl.index,$pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', CAST( $pic_tbl.photo_id AS CHAR ) , '_', CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic
					FROM $pic_tbl
					WHERE $pic_tbl.number =0
					) AS Y ON X.profile_id = Y.prof_id
					";
*/

				if ($db->Query1($sql))
				{
					if($db->RowCount())
					{
						$profile = '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo $profile = str_replace("},]", "}]", $profile);
					}
					else
					{
						echo '{"count":"0","Message":"Incorrect ID"}';
					}	
				}	
	 		}
			}
			else
		{
			echo '{"Message":"Session Expired"}';
		}
	 }

	/**************bookmark members backup ends  here****************/
	  
	/************Bookmarked members with limit starts here*******************/
	    
	 public function BookmarkedMembersByLimit($pid,$start,$limit,$skey)
	 {
		 $essence	=	new Essentials();
		  $secure     =   new secure();
		 $db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		 $profile_tbl			=	$essence->tblPrefix().'profile';
		 $profile_tbl_extended	=	$essence->tblPrefix().'profile_bookmark_list';
		 $location_table  		=	$essence->tblPrefix().'location_country';
	 	 $profile_state			=	$essence->tblPrefix().'location_state';
		 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
		  //check user authetication
		 $res = $secure->CheckSecure($pid,$skey);
		 if($res==1)
		{
		 if (!$db->Error())
			{
				/*$sql					=	"
				SELECT * ,year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name as Country_str_name, CONCAT( '/','userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) ,  '_', 
				CAST( `$pic_tbl`.photo_id AS CHAR ) ,  '_', CAST( `$pic_tbl`.index AS CHAR ) ,  '.jpg' ) 
				as Profile_Pic
				FROM `$profile_tbl` ,`$profile_tbl_extended`,`$location_table`,`$pic_tbl`
				WHERE `$profile_tbl`.profile_id =`$profile_tbl_extended`.bookmarked_id AND
				`$profile_tbl_extended`.profile_id =$pid AND
				`$location_table`.Country_str_code=`$profile_tbl`.country_id AND
				`$pic_tbl`.profile_id = `$profile_tbl`.profile_id AND
				`$pic_tbl`.number = 0";
				
				*/
					$sql					=	"
				SELECT p.profile_id,username,sex,custom_location,year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name as Country_str_name, CONCAT( '/','userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) ,  '_', 
				CAST( `$pic_tbl`.photo_id AS CHAR ) ,  '_', CAST( `$pic_tbl`.index AS CHAR ) ,  '.jpg' ) 
				as Profile_Pic
				FROM $profile_tbl as p
				JOIN $profile_tbl_extended ON (p.profile_id =`$profile_tbl_extended`.bookmarked_id)
				JOIN $location_table ON (`$location_table`.Country_str_code=p.country_id )
				LEFT JOIN $pic_tbl ON (`$pic_tbl`.profile_id = p.profile_id  AND `$pic_tbl`.number = 0)
				where p.status='active' AND `$profile_tbl_extended`.profile_id =$pid ORDER BY  p.profile_id DESC LIMIT $start,$limit";


//for counting total rows
				$sqlT=	"
				SELECT p.profile_id,username,sex,custom_location,year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name as Country_str_name, CONCAT( '/','userfiles/thumb_', CAST( `$pic_tbl`.profile_id AS CHAR ) ,  '_', 
				CAST( `$pic_tbl`.photo_id AS CHAR ) ,  '_', CAST( `$pic_tbl`.index AS CHAR ) ,  '.jpg' ) 
				as Profile_Pic
				FROM $profile_tbl as p
				JOIN $profile_tbl_extended ON (p.profile_id =`$profile_tbl_extended`.bookmarked_id)
				JOIN $location_table ON (`$location_table`.Country_str_code=p.country_id )
				LEFT JOIN $pic_tbl ON (`$pic_tbl`.profile_id = p.profile_id  AND `$pic_tbl`.number = 0)
				where p.status='active' AND `$profile_tbl_extended`.profile_id =$pid ORDER BY  p.profile_id ";
				$db->Query($sqlT);
				$totalCount = $db->RowCount();





/*				 $sql					=	"
				SELECT *
					FROM (
					
					SELECT $profile_tbl.profile_id, $profile_tbl.username, $profile_tbl.birthdate, year(
					CURRENT_TIMESTAMP ) - year( $profile_tbl.birthdate ) AS DOB, $profile_tbl.sex, Country_str_name AS Country_str_name, $profile_tbl.custom_location
					FROM `$profile_tbl` , `$profile_tbl_extended` , `$location_table`
					WHERE `$profile_tbl`.profile_id = `$profile_tbl_extended`.bookmarked_id
					AND `$profile_tbl_extended`.profile_id =$pid
					AND `$location_table`.Country_str_code = `$profile_tbl`.country_id
					) AS X
					LEFT JOIN (
					
					SELECT  $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id,$pic_tbl.index,$pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', CAST( $pic_tbl.photo_id AS CHAR ) , '_', CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic
					FROM $pic_tbl
					WHERE $pic_tbl.number =0
					) AS Y ON X.profile_id = Y.prof_id
					";
*/

				if ($db->Query($sql))
				{
					if($db->RowCount())
					{
						$profile = '{"Status":"Live","Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo $profile = str_replace("},]", "}]", $profile);
					}
					else
					{
						echo '{"Status":"Live","Total rows":"0","count":"0","Message":"Incorrect ID"}';
					}	
				}	
	 		}
			}
		else
		{
			echo '{"Message":"Session Expired"}';
		}
	 }
	  
	  
	  /************Bookmarked members with limit ends here*******************/
	 	  
	public function getFeaturedMembers($id,$skey)
	{
	 $essence				=	new Essentials();
	 $secure     =   new secure();
	 $profile_table			=	$essence->tblPrefix().'profile';
	 $location_table 		=	$essence->tblPrefix().'location_country';
	 $profile_state			=	$essence->tblPrefix().'location_state';
	 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
	 $db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	 $res = $secure->CheckSecure($id,$skey);
	 if($res==1)
	 {
	  if (!$db->Error())
		{
			/*$sql	=	"
			SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name as Country_str_name, 
Admin1_str_code as State,CONCAT( '/','userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) ,  '_', 
CAST( $pic_tbl.photo_id AS CHAR ) ,  '_', CAST( $pic_tbl.index AS CHAR ) ,  '.jpg' ) 
as Profile_Pic  FROM `$profile_table` join (`$location_table`) 
ON (`$profile_table_extend`.Country_str_code=`$profile_table`.country_id AND `$profile_table`.featured= 'y') 
left join (`$profile_state`) ON (`$profile_state`.Admin1_str_code = `$profile_table`.state_id)
left join (`$pic_tbl`) ON (`$profile_table`.profile_id = `$pic_tbl`.profile_id
AND $pic_tbl.number =0)";

*/
	$sql	=	"
			SELECT pr.profile_id,username,sex,custom_location,YEAR(CURRENT_TIMESTAMP)-YEAR(birthdate) AS DOB,Country_str_name AS Country_str_name, 
Admin1_str_code AS State,CONCAT( '/','userfiles/thumb_', CAST( pp.profile_id AS CHAR ) , '_', 
CAST( pp.photo_id AS CHAR ) , '_', CAST( pp.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
FROM $profile_table  pr
JOIN $location_table  lc ON lc.Country_str_code=pr.country_id AND pr.featured= 'y' 
LEFT JOIN $profile_state ls ON ls.Admin1_str_code = pr.state_id
LEFT JOIN $pic_tbl pp ON pr.profile_id = pp.profile_id AND pp.number =0
WHERE pr.status='active' AND pr.profile_id!='$id' ORDER BY pr.profile_id DESC";

/*
	SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name as Country_str_name, Admin1_str_code as State FROM `$profile_table` join (`$profile_table_extend`) ON (`skadate_location_country`.Country_str_code=`skadate_profile`.country_id AND `skadate_profile`.featured= 'y') left join (`$profile_state`) ON (`profile_state`.Admin1_str_code = `skadate_profile`.state_id)
*/
			if($db->Query($sql))
			{ 
				if($db->RowCount())
				{
					$profile = '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo $profile = str_replace("},]", "}]", $profile);
				}
				else
				{
					echo '{"Status":"Live","count":"0","Message":"No Members Found"}';
				}	
			}
		}
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
	}
	/** ------------------------------------------------------------------ */
	
	/*************featured members with limit starts here*************/
	public function getFeaturedMembersByLimit($id,$skey,$start,$limit)
	{
	 $essence				=	new Essentials();
	 $secure     =   new secure();
	 $profile_table			=	$essence->tblPrefix().'profile';
	 $location_table 		=	$essence->tblPrefix().'location_country';
	 $profile_state			=	$essence->tblPrefix().'location_state';
	 $pic_tbl				=	$essence->tblPrefix().'profile_photo';
	 
	 $db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	 $res = $secure->CheckSecure($id,$skey);
	if($res==1)
	{
	  if (!$db->Error())
		{
			/*$sql	=	"
			SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name as Country_str_name, 
Admin1_str_code as State,CONCAT( '/','userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) ,  '_', 
CAST( $pic_tbl.photo_id AS CHAR ) ,  '_', CAST( $pic_tbl.index AS CHAR ) ,  '.jpg' ) 
as Profile_Pic  FROM `$profile_table` join (`$location_table`) 
ON (`$profile_table_extend`.Country_str_code=`$profile_table`.country_id AND `$profile_table`.featured= 'y') 
left join (`$profile_state`) ON (`$profile_state`.Admin1_str_code = `$profile_table`.state_id)
left join (`$pic_tbl`) ON (`$profile_table`.profile_id = `$pic_tbl`.profile_id
AND $pic_tbl.number =0)";

*/
	

$sql="SELECT pr.profile_id,username,sex,custom_location,YEAR(CURRENT_TIMESTAMP)-YEAR(birthdate) AS DOB,Country_str_name AS Country_str_name, 
Admin1_str_code AS State,CONCAT( '/','userfiles/thumb_', CAST( pp.profile_id AS CHAR ) , '_', 
CAST( pp.photo_id AS CHAR ) , '_', CAST( pp.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
FROM $profile_table  pr
JOIN $location_table  lc ON lc.Country_str_code=pr.country_id AND pr.featured= 'y' 
LEFT JOIN $profile_state ls ON ls.Admin1_str_code = pr.state_id
LEFT JOIN $pic_tbl pp ON pr.profile_id = pp.profile_id AND pp.number =0
WHERE pr.status='active' AND pr.profile_id!='$id' ORDER BY pr.profile_id DESC LIMIT $start,$limit";

//for counting total rows
	$sqlT	= "SELECT pr.profile_id,username,sex,custom_location,YEAR(CURRENT_TIMESTAMP)-YEAR(birthdate) AS DOB,Country_str_name AS Country_str_name, 
Admin1_str_code AS State,CONCAT( '/','userfiles/thumb_', CAST( pp.profile_id AS CHAR ) , '_', 
CAST( pp.photo_id AS CHAR ) , '_', CAST( pp.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
FROM $profile_table  pr
JOIN $location_table  lc ON lc.Country_str_code=pr.country_id AND pr.featured= 'y' 
LEFT JOIN $profile_state ls ON ls.Admin1_str_code = pr.state_id
LEFT JOIN $pic_tbl pp ON pr.profile_id = pp.profile_iࡤ AND pp.number =0
WHERE pr.status='active' AND pr.profile_id!='$id' ORDER BY pr.profile_id DESC";
	$db->Query($sqlT);
	$totalCount = $db->RowCount();
	/*
	SELECT *, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name as Country_str_name, Admin1_str_code as State FROM `$profile_table` join (`$profile_table_extend`) ON (`skadate_location_country`.Country_str_code=`skadate_profile`.country_id AND `skadate_profile`.featured= 'y') left join (`$profile_state`) ON (`profile_state`.Admin1_str_code = `skadate_profile`.state_id)
*/
			if($db->Query($sql))
			{ 
				if($db->RowCount())
				{
					$profile = '{"Status":"Live","Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo $profile = str_replace("},]", "}]", $profile);
				}
				else
				{
					echo '{"Status":"Live","Total rows":"0","count":"0","Message":"No Members Found"}';
				}	
			}
		}
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
	}
	
	/*************featured members with limit ends here*************/
	    /**********view my photos of a a user with membership checking starts here***************/
	 public function ViewPhotosold($pid,$skey,$vid)
	 {	
	  	$essence			=	new Essentials();
		$secure     =   new secure();
		$pic_tbl			=	$essence->tblPrefix().'profile_photo';
		$profile_tbl			=	$essence->tblPrefix().'profile';
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
		$friend_list		 =	$essence->tblPrefix().'profile_friend_list';
		
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db2 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//check user authentication
		
		$res = $secure->CheckSecure($vid,$skey);
		if($res==1)
		{
		
			//find the membership id
			/*$sqlChk = "SELECT membership_type_id FROM $profile_tbl WHERE profile_id='$vid'";
			$sqlChkId = $db->Query($sqlChk);
			$sqlChkTypeId = mysql_fetch_array($sqlChkId);
			$sqlChkResultId = $sqlChkTypeId['membership_type_id'];
		
		   //check the membership of particular profile id
			$sql1 = "SELECT membership_type_id FROM  $membership_limit WHERE membership_type_id = '$sqlChkResultId' AND membership_service_key = 'view_photo'";	
			$sqlResult1 = $db->Query($sql1);
			$sqlMemId1 = mysql_fetch_array($sqlResult1);
			$sqlResultId1 = $sqlMemId1['membership_type_id'];
			
			if($sqlResultId1!=NULL)
			{*/
				$sql				=	"SELECT `photo_id`, `index` , publishing_status from `$pic_tbl` where `profile_id`=$pid  AND `number` > 0 order by added_stamp DESC";
					//echo $sql;
					$db1 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
					$str				= 	"";
					if($db1->Query($sql))
					{
						if($db1->RowCount())
						{
							for($i=0;$i<$db1->RowCount();$i++)
							{ 
								$j==0;
								$row1		=	$db1->Row();
								$photo_id	=	$row1->photo_id;
								$ps		=	$row1->publishing_status;
								$index			=	$row1->index;
								if ($pid==$vid)
								{
									$j=$j+1;
										$str	   .=  '[{';
										$str	   .=  '"Photo"'.':'.'"/userfiles/'."thumb_$pid"."_"."$photo_id"."_"."$index".".jpg".'"';
										$str	   .=  '}]';
								}
								else if($ps == 'friends_only')
								{
									$sqlF ="SELECT id FROM $friend_list WHERE profile_id='$vid' and friend_id='$pid'";
									//echo $sqlF;
									$sqlExe = $db2->Query1($sqlF);
									$sqlFriend = mysql_fetch_array($sqlExe);
									$sqlFResult = $sqlFriend['id'];
										if($sqlFResult != NULL)
										{
											$j=$j+1;
											$str	   .=  '[{';
											$str	   .=  '"Photo"'.':'.'"/$userfiles/'."thumb_$pid"."_"."$photo_id"."_"."$index".".jpg".'"';
											$str	   .=  '}]';
										}
								}
								else if($ps == 'public')
								{
									$j=$j+1;
										$str	   .=  '[{';
										$str	   .=  '"Photo"'.':'.'"/userfiles/'."thumb_$pid"."_"."$photo_id"."_"."$index".".jpg".'"';
										$str	   .=  '}]';
								}
								
							}
							$str=str_replace("}][{", "},{", $str);
							$profile = ',"count": '.$db1->RowCount().',"photocount":'.$j.','.'"result": '.$str.'';
							echo '{"Status":"Live"'.$profile.'}';
							$db1->kill();
						}
						else
						{
							echo '{"Status":"Live","count":"0"}';
						}
					 }
			 /*}
			 else
			 {
				echo '{"Message":"Membership Denied"}';
			 }*/
		}	
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
		
	 }



////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


public function ViewPhotos($pid,$skey,$vid)
	 {	
	  	$essence			=	new Essentials();
		$secure     		=   new secure();
		$pic_tbl			=	$essence->tblPrefix().'profile_photo';
		$profile_tbl		=	$essence->tblPrefix().'profile';
		$membership_limit	=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
		$friend_list		=	$essence->tblPrefix().'profile_friend_list';
		
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db2 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//check user authentication
		
		$res = $secure->CheckSecure($vid,$skey);
		if($res==1)
		{
		
			//find the membership id
			/*$sqlChk = "SELECT membership_type_id FROM $profile_tbl WHERE profile_id='$vid'";
			$sqlChkId = $db->Query($sqlChk);
			$sqlChkTypeId = mysql_fetch_array($sqlChkId);
			$sqlChkResultId = $sqlChkTypeId['membership_type_id'];
		
		   //check the membership of particular profile id
			$sql1 = "SELECT membership_type_id FROM  $membership_limit WHERE membership_type_id = '$sqlChkResultId' AND membership_service_key = 'view_photo'";	
			$sqlResult1 = $db->Query($sql1);
			$sqlMemId1 = mysql_fetch_array($sqlResult1);
			$sqlResultId1 = $sqlMemId1['membership_type_id'];
			
			if($sqlResultId1!=NULL)
			{*/
				 $sql				=	"SELECT `photo_id`, `index` , publishing_status,status from `$pic_tbl` where `profile_id`=$pid AND `number`>0 order by added_stamp DESC";
					//echo $sql;
					$db1 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
					$str				= 	"";
					if($db1->Query($sql))
					{
						if($db1->RowCount())
						{
							for($i=0;$i<$db1->RowCount();$i++)
							{ 
								$j==0;
								$row1		=	$db1->Row();
								 $photo_id	=	$row1->photo_id;
								 $ps		=	$row1->publishing_status;
								$index			=	$row1->index;
                                                               $status = $row1->status;                                        
                                                                   $toohot=$row1->toohot;
if ($status!='suspended'){
								if ($pid==$vid)
								{
									$j=$j+1;
										$str	   .=  '[{';
										$str	   .=  '"Photo"'.':'.'"/userfiles/'."thumb_$pid"."_"."$photo_id"."_"."$index".".jpg".'"';
										$str	   .=  '}]';
								}
								else if($ps == 'friends_only' and $row1->status=='active')
								{
									$sqlF ="SELECT id FROM $friend_list WHERE profile_id='$vid' and friend_id='$pid'";
									//echo $sqlF;
									$sqlExe = $db2->Query1($sqlF);
									$sqlFriend = mysql_fetch_array($sqlExe);
									$sqlFResult = $sqlFriend['id'];
										if($sqlFResult != NULL)
										{
											$j=$j+1;
											$str	   .=  '[{';
											$str	   .=  '"Photo"'.':'.'"/userfiles/'."thumb_$pid"."_"."$photo_id"."_"."$index".".jpg".'"';
											$str	   .=  '}]';
										}
                                                                                else
                                                                                {
                                                                                        $j=$j+1;
											$str	   .=  '[{';
											$str	   .=  '"Photo"'.':'.'"/userfiles/'."friends_only_photo.gif".'"';
											$str	   .=  '}]'; 
                                                                                }
								}
                                                                else if($ps == 'password_protected' and $row1->status=='active')
								{
									$j=$j+1;
											$str	   .=  '[{';
											$str	   .=  '"Photo"'.':'.'"/userfiles/'."password_protected_photo.gif".'"';
											$str	   .=  '}]'; 
								}
								else if($ps == 'public' and $row1->status=='active')
								{
									$j=$j+1;
										$str	   .=  '[{';
										$str	   .=  '"Photo"'.':'.'"/userfiles/'."thumb_$pid"."_"."$photo_id"."_"."$index".".jpg".'"';
										$str	   .=  '}]';
								}
                                                                else if($ps == 'public' and $row1->status=='approval')
								{
									$j=$j;/*$j=$j+1;
										$str	   .=  '[{';
										$str	   .=  '"Photo"'.':'.'"Waiting for admin aproval"';
										$str	   .=  '}]';*/
								}}else
                                                                {
                                                                    $j=$j;
										
                                                                }
								
							}
if($j==0){echo '{"Status":"Live","count":"0"}';}else{
							$str=str_replace("}][{", "},{", $str);
							$profile = ',"count": '.$j.',"photocount":'.$j.','.'"result": '.$str.'';
							echo '{"Status":"Live"'.$profile.'}';
							$db1->kill();}
						}
						else
						{
							echo '{"Status":"Live","count":"0"}';
						}
					 }
			 /*}
			 else
			 {
				echo '{"Message":"Membership Denied"}';
			 }*/
		}	
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
		
	 }



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	 /**********view my photos of a a user with membership checking ends here***************/
	/**********view my photos of a a user starts here***************/
	 public function ViewPhotosBackup($pid)
	 {	
	  	$essence			=	new Essentials();
		$pic_tbl			=	$essence->tblPrefix().'profile_photo';
		$sql				=	"SELECT `photo_id`, `index` from `$pic_tbl` where `profile_id`=$pid AND `number` > 0";
		$db1 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$str				= 	"";
		
		
		
		
		if($db1->Query($sql))
		{
			if($db1->RowCount())
			{
				for($i=0;$i<$db1->RowCount();$i++)
				{ 
					$row1		=	$db1->Row();
					$photo_id	=	$row1->photo_id;
					$index		=	$row1->index;
					$str	   .=  '[{';
					$str	   .=  '"Photo"'.':'.'"/userfiles/'."thumb_$pid"."_"."$photo_id"."_"."$index".".jpg".'"';
					$str	   .=  '}]';
				}
			$str=str_replace("}][{", "},{", $str);
			$profile = '{"count": '.$db1->RowCount().','.'"result": '.$str.'}';
			echo $profile;
			$db1->kill();
			}
			else
			{
				echo '{"count":"0"}';
			}
		 }
		
	 }
	 	/**********view my photos of a a user ends here***************/

	   /** ------------------------------------------------------------------ */
	 public function allContacts()
	 {
		 $essence		=	new Essentials();
		 $profile_table	=	$essence->tblPrefix().'profile';
		 $pic_tbl		=	$essence->tblPrefix().'profile_photo';
		 $db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		  if (!$db->Error())
			{
				 $sql	=	"SELECT p.profile_id,username,sex,CONCAT( '/','userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_',
CAST( $pic_tbl.photo_id AS CHAR ) , '_',CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) as Profile_Pic
FROM `$profile_table` p
LEFT JOIN `$pic_tbl` ON p.profile_id=`$pic_tbl`.profile_id AND `$pic_tbl`.number=0 where p.status='active'";
			//echo $sql;
			if($db->Query($sql))
				{ 
					if($db->RowCount())
					{
						/*$profile	=	 '['.$db->GetJSON().']';
						echo $profile	= 	str_replace("},]", "}]", $profile);*/
						
						$profile	=	 '['.$db->GetJSON().']';
						$profile	= 	str_replace("},]", "}]", $profile);
						$profile = '{"Status":"Live","count": '.$db->RowCount().','.'"result": '.$profile.'}';
						echo $profile;
					}
					else
					{
						echo '{"Status":"Live","Message":"No Members Found"}';
					}	
				}
			}
	 }
	   /** ----------------------all contacts with authentication---------------------------------- */
	 public function ChatMsgSending($id,$msg)
	 {	
	  	$essence				=	new Essentials();
		$profile_pic_tbl		=	$essence->tblPrefix().'chat_message';
		$db						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$time					=	time();
		$sql					=	"INSERT into `$profile_pic_tbl` values('','1','$id', '$msg', '$time', '000000')";
		if($db->Query($sql))
			 {
				echo '{"Message":"Success"}';
			 }
			 else
			 {
				echo '{"Message":"Error"}';
			 }
	  }
	/** ------------------------------------------------------------------ */	   
	public function ChatMsgRetrieving($id)
	{	
		$essence				=	new Essentials();
		$profile_pic_tbl		=	$essence->tblPrefix().'chat_message';
		$db						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$time					=	time();
		$sql					=	"SELECT * from `$profile_pic_tbl` where `profile_id`=$id";
		if($db->Query($sql))
		{
			if($db->RowCount())
			{	
				$profile	=	 '['.$db->GetJSON().']';
				$profile	= 	str_replace("},]", "}]", $profile);
				$profile = '{"count": '.$db->RowCount().','.'"result": '.$profile.'}';
				echo $profile;
			}
			else
			{
				echo '{"count":"0"}';
			}
		}
	}
	
		   /** --------------------Private chat with membership starts here---------- */
 public function PrivateChatMsgSending($sid,$rid,$msg,$skey)
	   {	
	  	$essence				=	new Essentials();
		$secure                 =   new secure();

		$im_message_tbl			=	$essence->tblPrefix().'im_message';
		$profile_table			=	$essence->tblPrefix().'profile';
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv                 =	$essence->tblPrefix().'link_membership_service_limit';
		$session                        =	$essence->tblPrefix().'im_session';
		$db				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$time				=	time();
		$msg 				= 	mysql_real_escape_string($msg);
		
		$db1				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//checking session
		$sqlS = "SELECT im_session_id FROM $session WHERE (opener_id ='$sid' or  opener_id='$rid') and (opponent_id ='$sid' or opponent_id ='$rid')";
		$sqlExe =$db1->Query1($sqlS);
		$sqlSession = @mysql_fetch_array($sqlExe);
		$sqlres = $sqlSession['im_session_id'];
//$sqlSenderOp = $sqlSession['opener_id'];
//$sqlRecipientOp = $sqlSession['opponent_id'];
if($sqlres!=NULL)
		{
$openerTime = time();
		$im_session_id			=	$sqlres;

$sqlOpener = "SELECT opener_id,opponent_id FROM $session WHERE im_session_id='$im_session_id'";
$sqlOpenerExe = $db->Query($sqlOpener);
$sqlOpenerRes = mysql_fetch_array($sqlOpenerExe);
$sqlOpenerID = $sqlOpenerRes['opener_id'];
$sqlOponentID = $sqlOpenerRes['opponent_id'];

//if(($sid==$sqlOpenerID) and ($rid==$sqlOponentID))
//{
$sqlUpdate = "UPDATE $session SET opener_activity = $openerTime WHERe im_session_id=$im_session_id";
$db->Query($sqlUpdate);
//}
//elseif(($rid==$sqlOpenerID) and ($sid==$sqlOponentID))
//{
//$sqlUpdate = "UPDATE $session SET opponent_activity = $openerTime WHERE (opener_id='$sid' or opener_id='$rid') and im_session_id=$im_session_id";
//$db->Query($sqlUpdate);
//}
	}
		else
		{
		$sqlI = "INSERT INTO $session VALUES('','$sid','$rid','$time','$time','NULL')";
		$sqlExe = $db1->Query1($sqlI);
		$sqlresult = @mysql_insert_id();
		$im_session_id = $sqlresult;
		}
		
		/*$im_session_id			=	"";*/
		//checking user sign in or not
			$res = $secure->CheckSecure($sid,$skey);

			if($res == 1)
			{
 //checking limit.................
                            
/*$sql="select membership_type_id from skadate_profile where profile_id=$sid";
$sqlres=$db1->Query($sql);
$sqlr=mysql_fetch_array($sqlres);
$memid=$sqlr['membership_type_id'];

$time1=time();
$timeStamp1= date("Y-m-d",$time1);			
$sqlChk1 = "SELECT COUNT(sender_id) FROM $im_message_tbl WHERE sender_id = '$sid' and FROM_UNIXTIME(timestamp,'%Y-%m-%d')='$timeStamp1'";
$sqlCount = $db->Query1($sqlChk1);
$sqlCount1 = mysql_fetch_array($sqlCount);
$sqlCount2 = $sqlCount1['COUNT(sender_id)'];
			
$sqlChk11 = "SELECT `limit` FROM `$membership_srv` WHERE `membership_type_id`='$memid' AND `membership_service_key`='send_message'";

$sqlCount01 = $db->Query1($sqlChk11);
$sqlCount11 = mysql_fetch_array($sqlCount01);
$sqlCount22 = $sqlCount11['limit'];


//      checking limit..................
                            
                            
		
		//membership checking of recipient 
		$sqlRec = "SELECT membership_type_id FROM $profile_table WHERE profile_id = '$rid'";
		$memberRec = $db->Query1($sqlRec);
		$memberRecId1 = mysql_fetch_array($memberRec);
		$memberRecipientId = $memberRecId1['membership_type_id'];
		
		$sqlRecChk = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='initiate_im_session' AND membership_type_id='$memberRecipientId'";
		$sqlRecChk1 = $db->Query1($sqlRecChk);
		$sqlRecFetch = mysql_fetch_array($sqlRecChk1);
		$sqlRecChkResult = $sqlRecFetch['membership_type_id'];
		
		//membership checking
		 $sql0 = "SELECT membership_type_id FROM $profile_table WHERE profile_id ='$sid'";
		 $memberType1 =  $db->Query1($sql0);
		 $memberTypeId1 = mysql_fetch_array($memberType1);
		 $membershipTypeId1 = $memberTypeId1['membership_type_id'];
		 //
		 $sql2 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='initiate_im_session' AND membership_type_id='$membershipTypeId1'";
		 $res = $db->Query1($sql2);
		 $resultId = mysql_fetch_array($res);
		 $resultMemberId = $resultId['membership_type_id'];
		// echo $resultMemberId;
		// echo $sqlRecChkResult;
		if(($resultMemberId!=NULL))
		{
                    
              //if for count
                  if($sqlCount2 < $sqlCount22 ){*/
                    
                    
$read = '0';
			$sql					=	"INSERT into `$im_message_tbl` values('','$im_session_id','$sid', '$rid', '$msg', '$time','$read','000000')";
					//$sqlR					=	"Update $im_message_tbl SET $im_message_tbl.read=1 where sender_id=$sid AND recipient_id=$rid";
				  // $db1->Query1($sqlR);
					if($db->Query1($sql))
				{
					
					if($db->RowCount())
					{	
						$profile	=	 '['.$db->GetJSON().']';
						$profile	= 	str_replace("},]", "}]", $profile);
						$t=date("m-d-Y H:i:s");
						$profile = '{"Status":"Live","Message":"Success","Time":"'.date("m-d-Y H:i:s").'"}';
						echo $profile;
					}
					else
					{
						echo '{"Status":"Live","Message":"Success","Time":"'.date("m-d-Y H:i:s").'"}';
					}
				}
				else
				{
					echo '{"Message":"Error"}';
				}
	  
                                
                                
                /*  }
		   else
		   {
				echo '{"Message":"Message Exceed"}';
		   }
	   
                }
                 else
		   {
				echo '{"Message":"Message Denied"}';
		   }
                */
           }
		else
			{
				echo '{"Message":"Session Expired"}';
			}
	  } 
/*********************************************************/
	      public function PrivateChatMsgSendingPblmTime($sid,$rid,$msg,$skey)
	   {	
	  	$essence				=	new Essentials();
		$secure                 =   new secure();
		$im_message_tbl			=	$essence->tblPrefix().'im_message';
		$profile_table			=	$essence->tblPrefix().'profile';
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv                 =	$essence->tblPrefix().'link_membership_service_limit';
		$session 			=	$essence->tblPrefix().'im_session';
		$db						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$time					=	time();
		$msg 					= 	mysql_real_escape_string($msg);
		
		$db1						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//checking session
		$sqlS = "SELECT im_session_id FROM $session WHERE (opener_id	='$sid' or  opener_id='$rid') and (opponent_id ='$sid' or opponent_id ='$rid')";
		$sqlExe =$db1->Query1($sqlS);
		$sqlSession = @mysql_fetch_array($sqlExe);
		$sqlres = $sqlSession['im_session_id'];
		if($sqlres!=NULL)
		{
		$im_session_id			=	$sqlres;
		}
		else
		{
		$sqlI = "INSERT INTO $session VALUES('','$sid','$rid','$time','NULL','NULL')";
		$sqlExe = $db1->Query1($sqlI);
		$sqlresult = @mysql_insert_id();
		$im_session_id = $sqlresult;
		}
		
		
		
		
		/*$im_session_id			=	"";*/
		//checking user sign in or not
			$res = $secure->CheckSecure($sid,$skey);

			if($res == 1)
			{
		
		/*//membership checking of recipient 
		$sqlRec = "SELECT membership_type_id FROM $profile_table WHERE profile_id = '$rid'";
		$memberRec = $db->Query1($sqlRec);
		$memberRecId1 = mysql_fetch_array($memberRec);
		$memberRecipientId = $memberRecId1['membership_type_id'];
		
		$sqlRecChk = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='initiate_im_session' AND membership_type_id='$memberRecipientId'";
		$sqlRecChk1 = $db->Query1($sqlRecChk);
		$sqlRecFetch = mysql_fetch_array($sqlRecChk1);
		$sqlRecChkResult = $sqlRecFetch['membership_type_id'];
		
		//membership checking
		 $sql0 = "SELECT membership_type_id FROM $profile_table WHERE profile_id ='$sid'";
		 $memberType1 =  $db->Query1($sql0);
		 $memberTypeId1 = mysql_fetch_array($memberType1);
		 $membershipTypeId1 = $memberTypeId1['membership_type_id'];
		 //
		 $sql2 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='initiate_im_session' AND membership_type_id='$membershipTypeId1'";
		 $res = $db->Query1($sql2);
		 $resultId = mysql_fetch_array($res);
		 $resultMemberId = $resultId['membership_type_id'];
		// echo $resultMemberId;
		// echo $sqlRecChkResult;
		if(($resultMemberId!=NULL))
		{*/
	$sql	=	"INSERT into `$im_message_tbl` values('','$im_session_id','$sid', '$rid', '$msg', '$time','0','000000')";
					//$sqlR					=	"Update $im_message_tbl SET $im_message_tbl.read=1 where sender_id=$sid AND recipient_id=$rid";
				  // $db1->Query1($sqlR);
		if($db->Query1($sql))
		{
			
		if($db->RowCount())
		{	
						$profile	=	 '['.$db->GetJSON().']';
						$profile	= 	str_replace("},]", "}]", $profile);
						$t=date("Y-m-d H:i:s");
						$profile = '{"Status":"Live","Message":"Success","Time":"'.date("Y-m-d H:i:s").'"}';
						echo $profile;
					}
					else
					{
						echo '{"Status":"Live","Message":"Success","Time":"'.date("Y-m-d H:i:s").'"}';
					}
				}
				else
				{
					echo '{"Message":"Error"}';
				}
	 /*  }
		   else
		   {
				echo '{"Message":"Membership Denied"}';
		   }*/
	   }
		else
			{
				echo '{"Message":"Session Expired"}';
			}
	    
	 } 
	   /** -------------------Private chat with membership ends here-------------- */

	
	 /** --------------------Private chat backup starts here--------------------- */
	   public function PrivateChatMsgSendingBackup($sid,$rid,$msg)
	   {	
	  	$essence				=	new Essentials();
		$im_message_tbl			=	$essence->tblPrefix().'im_message';
		$db						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$time					=	time();
		$msg 					= 	mysql_real_escape_string($msg);
		$im_session_id			=	"";
		$sql					=	"INSERT into `$im_message_tbl` values('','$im_session_id','$sid', '$rid', '$msg', '$time','0','000000')";
		if($db->Query($sql))
			 {
				date_default_timezone_set('Asia/Kolkata');
				echo '{"Message":"Success", "Time": "'.date("H:i:s").'"}';
			 }
			 else
			 {
				echo '{"Message":"Error"}';
			 }
	   }
	      
		  
		  //private chat sending
		  public function PrivateChatMsgSendingold($sid,$rid,$msg,$skey)
	   {	
	  	$essence				=	new Essentials();
		$secure             =   new secure();

		$im_message_tbl			=	$essence->tblPrefix().'im_message';
		$profile_table			=	$essence->tblPrefix().'profile';
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		    =	$essence->tblPrefix().'link_membership_service_limit';
		$db						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$time					=	time();
		$msg 					= 	mysql_real_escape_string($msg);
		$im_session_id			=	"";
		
		
		//check user sign in or not
		$res = $secure->CheckSecure($sid,$skey);

		if($res == 1)
		{
				   $sql					=	"INSERT into `$im_message_tbl` values('','$im_session_id','$sid', '$rid', '$msg', '$time','0','000000')";
					if($db->Query($sql))
						 {
							date_default_timezone_set('Asia/Kolkata');
							echo '{"Status":"Live","Message":"Success", "Time": "'.date("H:i:s").'"}';
						 }
						 else
						 {
							echo '{"Status":"Live","Message":"Error"}';
						 }
	 } 
	 else
	 {
	      echo '{"Message":"Session Expired"}';
	 }
} 
	   /** -----------------Private chat backup ends here----------------------- */
 public function PrivateChatMsgReceiving($sid,$rid,$skey)
	    {	
			
			$essence				=	new Essentials();
				//$secure                 =   new secure();
				$secure                 =   new secure();
					$profile_table			=	$essence->tblPrefix().'profile';
			$im_message_tbl			=	$essence->tblPrefix().'im_message';
$im_session_tbl			=	$essence->tblPrefix().'im_session';
			$pic_tbl				=	$essence->tblPrefix().'profile_photo';
			$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		    =	$essence->tblPrefix().'link_membership_service_limit';
			$db						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$time					=	time();
			$today = strtotime("$hour:00:00");
			$yesterday = strtotime('-1 day', $today);

			$im_session_id			=	"";
			$db1						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$res = $secure->CheckSecure($rid,$skey);

			if($res == 1)
			{
			
			//membership checking
		  $sql0 = "SELECT membership_type_id FROM $profile_table WHERE profile_id = '$rid'";
		 $memberType1 =  $db->Query1($sql0);
		 $memberTypeId1 = @mysql_fetch_array($memberType1);
		 $membershipTypeId1 = $memberTypeId1['membership_type_id'];
		 //
		  $sql2 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='initiate_im_session' AND membership_type_id='$membershipTypeId1'";
		 $res = $db->Query1($sql2);
		 $resultId = @mysql_fetch_array($res);
		 $resultMemberId = $resultId['membership_type_id'];
	
			if(($resultMemberId!=NULL))
		{
			
			
	
			
			$sql					=	"SELECT *,if((sender_id=$sid AND recipient_id=$rid),'receiving','sending') AS chat,timestamp AS Time from `$im_message_tbl` where (sender_id=$sid AND recipient_id=$rid AND timestamp>=$yesterday) OR (sender_id=$rid AND recipient_id=$sid AND timestamp>=$yesterday) ORDER BY timestamp ASC";


$res=$db->Query($sql);
$resArray = mysql_fetch_array($res);
$resSender= $resArray['sender_id'];
$resRecipient= $resArray['recipient_id'];

			$sqlR					=	"Update $im_message_tbl SET $im_message_tbl.read=1 where sender_id=$sid AND recipient_id=$rid";
$db1->Query($sqlR);
$dateUpdate = time();

if($rid == $resRecipient and $sid ==$resSender)
{
$sqlUpdate = "UPDATE $im_session_tbl SET opponent_activity=$dateUpdate WHERE opener_id=$sid and opponent_id=$rid";
$db1->Query($sqlUpdate);
}
else
{
$sqlUpdate = "UPDATE $im_session_tbl SET opener_id=$dateUpdate WHERE opener_id=$sid and opponent_id=$rid";
$db1->Query($sqlUpdate);
}
			





$i=0;
                        while($row1 = mysql_fetch_array($res))
                        {
                        $dateNew =date("m-d-Y H:i:s",$row1['Time']);
                            $result[$i] =array('im_message_id'=>$row1['im_message_id'],'im_session_id'=>$row1['im_session_id'], 'sender_id'=>$row1['sender_id'],'recipient_id'=>$row1['recipient_id'], 'text'=>$row1['text'], 'Time'=>$dateNew, 'read'=>$row1['read'],'color'=>$row1['color'], 'chat'=>$row1['chat']);
					$i++;
                        }
                        $final = array();
		// Assigning to array Ends here
if (is_array($result))
{
	foreach($result as $array)
	{
		
			array_push($final, $array);
	}
}	
			//$i=$i-1;
			$final	=	 '{"count": '.$i.',"result": '.json_encode($final).'}';
						echo str_replace("},]","}]",$final);
				
/*if($db->Query($sql))
				{
					
					if($db->RowCount())
					{	
						$profile	=	 '['.$db->GetJSON().']';
						$profile	= 	str_replace("},]", "}]", $profile);
						$profile = '{"count": '.$db->RowCount().','.'"result": '.$profile.'}';
						echo $profile;
					}
					else
					{
						echo '{"count":"0"}';
					}
			     
				 }
				 else
				 {
				 echo '{"count":"0"}';
				 }
*/
			}
			else
			{
				echo '{"Message":"Membership Denied"}';
			}
			}
			else
			{
				echo '{"Message":"Session Expired"}';
			}
	    }
/***********************************************/
	  public function PrivateChatMsgReceivingPblemTime($sid,$rid,$skey)
	    {	
			
			$essence				=	new Essentials();
				//$secure                 =   new secure();
				$secure                 =   new secure();
					$profile_table			=	$essence->tblPrefix().'profile';
			$im_message_tbl			=	$essence->tblPrefix().'im_message';
			$pic_tbl				=	$essence->tblPrefix().'profile_photo';
			$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		    =	$essence->tblPrefix().'link_membership_service_limit';
			$db						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$time					=	time();
			$today = strtotime("$hour:00:00");
			$yesterday = strtotime('-1 day', $today);
			$im_session_id			=	"";
			$db1						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$res = $secure->CheckSecure($rid,$skey);

			if($res == 1)
			{
			
			//membership checking
		 /* $sql0 = "SELECT membership_type_id FROM $profile_table WHERE profile_id = '$rid'";
		 $memberType1 =  $db->Query1($sql0);
		 $memberTypeId1 = @mysql_fetch_array($memberType1);
		 $membershipTypeId1 = $memberTypeId1['membership_type_id'];
		 //
		  $sql2 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='initiate_im_session' AND membership_type_id='$membershipTypeId1'";
		 $res = $db->Query1($sql2);
		 $resultId = @mysql_fetch_array($res);
		 $resultMemberId = $resultId['membership_type_id'];
	
			if(($resultMemberId!=NULL))
		{*/
			
			
			/*$sql					=	"SELECT *,if((sender_id=$sid AND recipient_id=$rid),'sending','receiving') AS chat,FROM_UNIXTIME(timestamp,'%Y %D %M %h:%i:%s') AS Time from `$im_message_tbl` where (sender_id=$sid AND recipient_id=$rid AND timestamp>=$yesterday) OR (sender_id=$rid AND recipient_id=$sid AND timestamp>=$yesterday) ORDER BY timestamp ASC";*/
			
			$sql					=	"SELECT *,if((sender_id=$sid AND recipient_id=$rid),'receiving','sending') AS chat,FROM_UNIXTIME(timestamp,'%Y %D %M %h:%i:%s') AS Time from `$im_message_tbl` where (sender_id=$sid AND recipient_id=$rid AND timestamp>=$yesterday) OR (sender_id=$rid AND recipient_id=$sid AND timestamp>=$yesterday) ORDER BY timestamp ASC";
			$sqlR					=	"Update $im_message_tbl SET $im_message_tbl.read=1 where sender_id=$sid AND recipient_id=$rid";
			$db1->Query($sqlR);
				if($db->Query($sql))
				{
					
					if($db->RowCount())
					{	
						$profile	=	 '['.$db->GetJSON().']';
						$profile	= 	str_replace("},]", "}]", $profile);
						$profile = '{"count": '.$db->RowCount().','.'"result": '.$profile.'}';
						echo $profile;
					}
					else
					{
						echo '{"count":"0"}';
					}
			     
				 }
				 else
				 {
				 echo '{"count":"0"}';
				 }
			/*}
			else
			{
				echo '{"Message":"Membership Denied"}';
			}*/
			}
			else
			{
				echo '{"Message":"Session Expired"}';
			}
	    }

	/** ------------------------------------------------------------------ */
	static function generateMessageHash( $sender, $subject, $message )
	{
		if (!strlen($subject)) // means it is not the first message
		{
			$subject = md5(time());
			$charsonly = preg_replace('~\s+~', '', "$sender-".strtolower(trim($subject).trim($message)));
			$hash = md5($charsonly);
			return $hash;
		}
	}
	 /** ------------------------compose message with membership checking starts here------- */
public function ComposeMessage($sender,$recipient,$sub,$msg,$skey)
	{	
		
                $essence			=	new Essentials();
		$secure     			=       new secure();
		$profile_pic_tbl		=	$essence->tblPrefix().'mailbox_message';
		$profile_table_extend           =	$essence->tblPrefix().'mailbox_conversation';
		$profile_table			=	$essence->tblPrefix().'profile';
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv                 =	$essence->tblPrefix().'link_membership_service_limit';
		$key					=	$essence->tblPrefix().'lang_key';
		$value					=	$essence->tblPrefix().'lang_value';

		$time					=	time();
		$subject				=	$sub;
		$text 					= 	$msg;
	        $msg 					= 	mysql_real_escape_string($msg);
		$sub 					= 	mysql_real_escape_string($sub);
		$text 					= 	$msg;
		$hash 					= 	self::generateMessageHash( $sender, $subject, $text );
		$db 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//check user sign in or not
		if($sender == "0")
		{
		$sql1		=	$db->Query1("INSERT into `$profile_table_extend` values('', '$sender','$recipient', '$sub','1', '','1', '$time','no')");
		$cid		=	@mysql_insert_id();
		$sql		=	"INSERT into `$profile_pic_tbl` values('', '$cid','$time', '$sender', '$recipient', '$msg', 'no', 'no', 'a', '$hash')";
		$db1->Query1($sql);
		}
		else
		{
		$res = $secure->CheckSecure($sender,$skey);
                
		if($res==1)
		{

			
						
$sql="select membership_type_id from skadate_profile where profile_id=$sender";
$sqlres=$db1->Query($sql);
$sqlr=mysql_fetch_array($sqlres);
$memid=$sqlr['membership_type_id'];

$time1=time();
$timeStamp1= date("Y-m-d",$time1);			
$sqlChk1 = "SELECT COUNT(sender_id) FROM $profile_pic_tbl WHERE sender_id = '$sender' and FROM_UNIXTIME(time_stamp,'%Y-%m-%d')='$timeStamp1'";
$sqlCount = $db->Query1($sqlChk1);
$sqlCount1 = mysql_fetch_array($sqlCount);
$sqlCount2 = $sqlCount1['COUNT(sender_id)'];
			
$sqlChk11 = "SELECT `limit` FROM `$membership_srv` WHERE `membership_type_id`='$memid' AND `membership_service_key`='send_message'";

$sqlCount01 = $db->Query1($sqlChk11);
$sqlCount11 = mysql_fetch_array($sqlCount01);
$sqlCount22 = $sqlCount11['limit'];



			if($sqlCount2 < $sqlCount22 )
				{

		$db2 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$sql1		=	$db->Query1("INSERT into `$profile_table_extend` values('', '$sender','$recipient', '$sub','1', '','1', '$time','no')");
		$cid		=	@mysql_insert_id();
		$sql		=	"INSERT into `$profile_pic_tbl` values('', '$cid','$time', '$sender', '$recipient', '$msg', 'no', 'yes', 'a', '$hash')";
		if($db1->Query1($sql))
			{
				echo '{"Status":"Live","Message":"Success"}';
			}
			else
			{
				echo '{"Status":"Live","Message":"Error"}';
			}
}
else
{
echo '{"Message":"Message Exceed"}';

}
		}
	else //session expired
	{
		echo '{"Message":"Session Expired"}';
	}
	}			
	}



	public function ComposeMessagedec15($sender,$recipient,$sub,$msg,$skey)
	{	
		$essence				=	new Essentials();
		$secure     			=   new secure();
		$profile_pic_tbl		=	$essence->tblPrefix().'mailbox_message';
		$profile_table_extend	=	$essence->tblPrefix().'mailbox_conversation';
		$profile_table			=	$essence->tblPrefix().'profile';
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
		$key					=	$essence->tblPrefix().'lang_key';
		$value					=	$essence->tblPrefix().'lang_value';

		$time					=	time();
		$subject				=	$sub;
		$text 					= 	$msg;
	    $msg 					= 	mysql_real_escape_string($msg);
		$sub 					= 	mysql_real_escape_string($sub);
		$text 					= 	$msg;
		$hash 					= 	self::generateMessageHash( $sender, $subject, $text );
		$db 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//check user sign in or not
		if($sender == "0")
		{
		$sql1		=	$db->Query1("INSERT into `$profile_table_extend` values('', '$sender','$recipient', '$sub','1', '','1', '$time','no')");
		$cid		=	@mysql_insert_id();
		$sql		=	"INSERT into `$profile_pic_tbl` values('', '$cid','$time', '$sender', '$recipient', '$msg', 'no', 'no', 'a', '$hash')";
		$db1->Query1($sql);
		}
		else
		{
		$res = $secure->CheckSecure($sender,$skey);
		if($res==1)
		{
		$db2 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$sql1		=	$db->Query1("INSERT into `$profile_table_extend` values('', '$sender','$recipient', '$sub','1', '','1', '$time','no')");
		$cid		=	@mysql_insert_id();
		$sql		=	"INSERT into `$profile_pic_tbl` values('', '$cid','$time', '$sender', '$recipient', '$msg', 'no', 'yes', 'a', '$hash')";
		if($db1->Query1($sql))
			{
				echo '{"Status":"Live","Message":"Success"}';
			}
			else
			{
				echo '{"Status":"Live","Message":"Error"}';
			}
		}
	else //session expired
	{
		echo '{"Message":"Session Expired"}';
	}
	}			
	}
	/** ----------------------compose message with membership checking ends here---------------- */
	 /** ------------------compose message backup starts here-------------- */
	public function ComposeMessageBackup($sender,$recipient,$sub,$msg)
	{	
		$essence				=	new Essentials();
		$profile_pic_tbl		=	$essence->tblPrefix().'mailbox_message';
		$profile_table_extend	=	$essence->tblPrefix().'mailbox_conversation';
		$time					=	time();
		$subject				=	$sub;
		$text 					= 	$msg;
	    $msg 					= 	mysql_real_escape_string($msg);
		$sub 					= 	mysql_real_escape_string($sub);
		$text 					= 	$msg;
		
		$hash 					= 	self::generateMessageHash( $sender, $subject, $text );
		$db 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
					$sql1					=	$db->Query("INSERT into `$profile_table_extend` values('', '$sender','$recipient', '$sub','1', '','1', '$time','no')");
					$cid					=	mysql_insert_id();
					$sql					=	"INSERT into `$profile_pic_tbl` values('', '$cid','$time', '$sender', '$recipient', '$msg', 'no', 'yes', 'a', '$hash')";
						if($db->Query($sql))
						{
							echo '{"Message":"Success"}';
							
						}
						else
						{
							echo '{"Message":"Error"}';
						}
	}
	/** -------------------Compose message with membership checking ends here------------------------ */
	
	
public function DeleteConversationNew($pid,$cid,$skey)
	{	
		$essence			=	new Essentials();
		$secure             =   new secure();
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db11 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db2 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db3 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db4 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db5 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db6 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db7 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db8 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
		
		$mailbox_message	=	$essence->tblPrefix().'mailbox_message';
		$mailbox_conv		=	$essence->tblPrefix().'mailbox_conversation';
		
	    $sql				=  	"SELECT * FROM `$mailbox_conv` WHERE conversation_id=$cid";
		$r					=	$db->Query($sql);
		$row				=	$db->Row();
		$initiator_id		=	$row->initiator_id;
		//echo "sender_id=".$initiator_id;
		$sqlq				=	"SELECT `bm_deleted` FROM `$mailbox_conv` WHERE conversation_id=$cid";
		$r1					=	$db11->Query($sqlq);
		$row1				=	$db11->Row();
		$bm					=	$row1->bm_deleted;
		//echo "bm_read=".$bm;
		switch($bm)
		{
			case 0:
					if($pid!=$initiator_id)
					{
						$sql1 =	"UPDATE `$mailbox_conv` SET `bm_deleted`=2 WHERE `conversation_id`=$cid";
						$db1->Query($sql1);
						
					}
					else
					{
						
						$sql2 =	"UPDATE `$mailbox_conv` SET `bm_deleted`=1 WHERE `conversation_id`=$cid";
						$db2->Query($sql2);
						
					}
					break;
			case 1: 
					if($pid!=$initiator_id)
					{
						$sql3 =	"DELETE FROM `$mailbox_conv` where `conversation_id`=$cid";
						$db3->Query($sql3);
						$sql4 =	"DELETE FROM `$mailbox_message` where `conversation_id`=$cid";
						$db4->Query($sql4);
						
					}
					break;
			case 2: 
					if($pid=$initiator_id)
					{
						$sql5 =	"DELETE FROM `$mailbox_conv` where `conversation_id`=$cid";
						$db5->Query($sql5);
					    $sql6 =	"DELETE FROM `$mailbox_message` where `conversation_id`=$cid";
						$db6->Query($sql6);
						
					}
					break;
			case 3: 
					$sql7	=	"DELETE FROM `$mailbox_conv` where `conversation_id`=$cid";
					$db7->Query($sql7);
					$sql8 	=	"DELETE FROM `$mailbox_message` where `conversation_id`=$cid";
					$db8->Query($sql8);
		}
				
		if($db->Query($sql))
		{
			echo '{"Status":"Live","Message":"Success"}';
		}
		else
		{
			echo '{"Status":"Live","Message":"Error"}';
		}
	}
	else
	{
		echo '{"Message":"Session Expired"}';
	}
		
}
	
	/** ------------------------------------------------------------------ */
	public function DeleteConversation($id)
	{	
		$essence			=	new Essentials();
		$mailboxconv		=	$essence->tblPrefix().'mailbox_conversation';
		$sql				=	"DELETE FROM `$mailboxconv` WHERE conversation_id=$id";
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		if($db->Query($sql))
		{
			echo '{"Message":"Success"}';
		}
		else
		{
			echo '{"Message":"Error"}';
		}
	}
	/** ------------------------------------------------------------------ */
	public function DeleteMessage($id)
	{	
		$essence			=	new Essentials();
		$profile_pic_tbl	=	$essence->tblPrefix().'mailbox_message';
		$sql				=	"DELETE FROM `$profile_pic_tbl` WHERE message_id=$id";
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		if($db->Query($sql))
		{
			echo '{"Message":"Success"}';
		}
		else
		{
			echo '{"Message":"Error"}';
		}
	}
	/** ------------------------------------------------------------------ */
	public function DeleteMessageBySender($sid,$rid,$cid,$skey)
	{	
		$essence				=	new Essentials();
		$secure     =   new secure();

		$mailbox_message		=	$essence->tblPrefix().'mailbox_message';
		$mailbox_message_tbl	=	$essence->tblPrefix().'mailbox_conversation';
		$db 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db2					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		  //Checking user Authentication
		$res = $secure->CheckSecure($sid,$skey);
		if($res==1)
		{
	    $query = "SELECT `bm_deleted` FROM `$mailbox_message_tbl` WHERE `interlocutor_id`=$rid AND `initiator_id`=$sid AND `conversation_id`=$cid";
		$bm_deleted=$db->Query($query);
		$row=mysql_fetch_array($bm_deleted);
		$bm_del=$row['bm_deleted'];
		if($bm_del>0)
		{
		  // $sql =	"UPDATE `$mailbox_message_tbl` SET `bm_deleted`=2 WHERE `interlocutor_id`=$rid AND `initiator_id`=$sid AND `conversation_id`=$cid";
			 $sql 	=	"DELETE FROM `$mailbox_message_tbl` WHERE `interlocutor_id`=$sid AND `initiator_id`=$rid AND `conversation_id`=$cid";
			 $del  =	"DELETE FROM `$mailbox_message` WHERE `conversation_id`=$cid";
			
			 $db2->Query($del);
		   if($db->Query($sql))
			{
				echo '{"Message":"Success"}';
			}
			else
			{
			echo '{"Message":"Error"}';
			}
		}
		else
		{			
			$sql1 =	"UPDATE `$mailbox_message_tbl` SET bm_deleted=1 WHERE `interlocutor_id`=$rid AND `initiator_id`=$sid AND `conversation_id`=$cid";
			if($db1->Query($sql1))
			{
				echo '{"Message":"Success"}';
			}
			else
			{
				echo '{"Message":"Error"}';
			}
		}
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
	}
	/** ------------------------------------------------------------------ */
	public function DeleteMessageByRecipient($sid,$rid,$cid,$skey)
	{	
		$essence				=	new Essentials();
		$secure     =   new secure();
		$mailbox_message		=	$essence->tblPrefix().'mailbox_message';
		$mailbox_message_tbl	=	$essence->tblPrefix().'mailbox_conversation';
		$db 					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db2					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//Checking user Authentication
		$res = $secure->CheckSecure($rid,$skey);
		if($res==1)
		{	
		$query = "SELECT `bm_deleted` FROM `$mailbox_message_tbl` WHERE `interlocutor_id`=$sid AND `initiator_id`=$rid AND `conversation_id`=$cid";
		$bm_deleted=$db->Query($query);
		$row=mysql_fetch_array($bm_deleted);
		$bm_del=$row['bm_deleted'];
		//echo $bm_del;
		if($bm_del>0)
		{
		  // $sql =	"UPDATE `$mailbox_message_tbl` SET `bm_deleted`=2 WHERE `interlocutor_id`=$rid AND `initiator_id`=$sid AND `conversation_id`=$cid";
		     $sql  ="DELETE FROM `$mailbox_message_tbl` WHERE `interlocutor_id`=$rid AND `initiator_id`=$sid AND `conversation_id`=$cid";
			 $del  ="DELETE FROM `$mailbox_message` WHERE `conversation_id`=$cid";
			
			 $db2->Query($del);
		   if($db->Query($sql))
			{
				echo '{"Status":"Live","Message":"Success"}';
			}
			else
			{
				echo '{"Status":"Live","Message":"Error"}';
			}
		}
		else
		{			
			$sql1 =	"UPDATE `$mailbox_message_tbl` SET `bm_deleted`=2 WHERE `interlocutor_id`=$rid AND `initiator_id`=$sid AND `conversation_id`=$cid";
			if($db1->Query($sql1))
			{
				echo '{"Status":"Live","Message":"Success"}';
			}
			else
			{
				echo '{"Status":"Live","Message":"Error"}';
			}
		}
		
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
		
	}
	/** ------------------------------------------------------------------ */
	public function ReplyMessage($id,$skey,$mid,$msg)
	{	
		$essence				=	new Essentials();
		$secure     =   new secure();
		$mailbox_message_tbl	=	$essence->tblPrefix().'mailbox_message';
		$profile_table_extend	=	$essence->tblPrefix().'mailbox_conversation';
		$profile_tbl			= 	$essence->tblPrefix().'profile';  
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
                
		$db						= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db2					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db3					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db4					= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
	       $res = $secure->CheckSecure($id,$skey);
              
		if($res==1)
		{
                    
		
		$sql1					=   "SELECT * from `$mailbox_message_tbl` where `message_id`= $mid";
		
                if($db4->Query1($sql1))
		{
			//$row4		=	$db4->Row();
			//$cid		=	$row4->conversation_id;
			$row1		=	mysql_fetch_array($db4->Query1($sql1));
			$cid            =	$row1['conversation_id'];
			$time		=	time();
			$sender		=	$row1['sender_id'];
			$recipient	=	$row1['recipient_id'];
                       
			//echo $cid;
			//$sender		=	$row4->sender_id;
			//$recipient	=	$row4->recipient_id;
			//echo $sender;
			//here check membership type id of a recipient
			/*$sqlRec0 = "SELECT membership_type_id FROM $profile_tbl WHERE profile_id='$recipient'";
			$sqlMemId = $db->Query1($sqlRec0);
			$sqlMemTypeId = mysql_fetch_array($sqlMemId);
			$sqlMemResultId = $sqlMemTypeId['membership_type_id'];
			 //here checks whether the recipient has the permission to send mail 
			 $sqlRecipient = "SELECT membership_type_id FROM $membership_limit WHERE membership_type_id = '$sqlMemResultId' AND membership_service_key='send_message'";
			 $sqlRecipientId = $db4->Query1($sqlRecipient);
			 $sqlRecipientTypeId = mysql_fetch_array($sqlRecipientId);
			 $sqlRecipientResult = $sqlRecipientTypeId['membership_type_id'];
			 if($sqlRecipientResult !=NULL)
			 {*/
					$sql2		=   "SELECT * from `$profile_table_extend` where `conversation_id`= $cid";
					if($db2->Query1($sql2))
					{
						$row2			=	$db2->Row();
						$initiator		=	$row2->initiator_id;
						$bm_deleted		=	$row2->bm_deleted;
						//$interlocutor	=	$row2->interlocutor_id;
						//echo $initiator;
$sql="select membership_type_id from skadate_profile where profile_id=$recipient";
$sqlres=$db1->Query($sql);
$sqlr=mysql_fetch_array($sqlres);
$memid=$sqlr['membership_type_id'];

$time1=time();
$timeStamp1= date("Y-m-d",$time1);			
$sqlChk1 = "SELECT COUNT(sender_id) FROM $mailbox_message_tbl WHERE sender_id = '$recipient' and FROM_UNIXTIME(time_stamp,'%Y-%m-%d')='$timeStamp1'";

$sqlCount = $db->Query1($sqlChk1);
$sqlCount1 = mysql_fetch_array($sqlCount);
$sqlCount2 = $sqlCount1['COUNT(sender_id)'];
			
$sqlChk11 = "SELECT `limit` FROM `$membership_srv` WHERE `membership_type_id`='$memid' AND `membership_service_key`='send_message'";

$sqlCount01 = $db->Query1($sqlChk11);
$sqlCount11 = mysql_fetch_array($sqlCount01);
$sqlCount22 = $sqlCount11['limit'];



			if($sqlCount2 < $sqlCount22 )
				{						
						
							$sql		=	"INSERT into `$mailbox_message_tbl` values('', '$cid', '$time', '$recipient', '$sender', '$msg', 'no', 'yes', 'a', '')";
					
							if($db->Query1($sql))
							{
								if($initiator != $sender)
								{
									$sql1 =	"UPDATE `$profile_table_extend` SET `bm_read`=1,`bm_deleted`=0 WHERE  `initiator_id`=$recipient AND `interlocutor_id`=$sender AND `conversation_id`=$cid";
									$db1->Query1($sql1);
								}
								else
								{
									$sql3 =	"UPDATE `$profile_table_extend` SET `bm_read`=2,`bm_deleted`=0 WHERE `initiator_id`=$sender AND `interlocutor_id`=$recipient AND `conversation_id`=$cid";
									$db3->Query1($sql3);
								}
							echo '{"Status":"Live","Message":"Success"}';
							}
							else
							{
								echo '{"Status":"Live","Message":"Error"}';
							}
					
                                                        
                                                         }
                                                         else
                                                            {
                                                            echo '{"Message":"Message Exceed"}';
                                                            }
                                        }
				/*}
				else
				{
						echo '{"Message":"Membership denied"}';
				}*/
		}
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
	}
	/** -----------------------view inbox with membership starts here------------ */
	public function ViewInbox($id,$skey)
	{
	mysql_query('SET CHARACTER SET utf8'); 
	
	$essence = new Essentials();
	$secure     =   new secure();
	$profile_table = $essence->tblPrefix().'profile';
	$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$mail_conv_tbl = $essence->tblPrefix().'mailbox_conversation';
	$mail_msg_tbl = $essence->tblPrefix().'mailbox_message';
	$pic_tbl = $essence->tblPrefix().'profile_photo';
	$membership_limit = $essence->tblPrefix().'link_membership_type_service';
	$membership_srv = $essence->tblPrefix().'link_membership_service_limit';
	$key                    =    $essence->tblPrefix().'lang_key';
	$value                    =    $essence->tblPrefix().'lang_value';
	//checking the user sign in or not
	$i=0;
	$res = $secure->CheckSecure($id,$skey);
	if($res==1)
		{
		 
	$sql="SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number,text,is_readable,bm_read,Time,sex,username,subject,
CONCAT( '/', 'userfiles/thumb_', CAST( L.profile_id AS CHAR ), '_', CAST( L.photo_id AS CHAR ) , '_', CAST( L.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
 FROM

	(
	
	SELECT m.message_id, COUNT(m.message_id) AS MailCount, COUNT(m.conversation_id) AS conversation_number, 
	m.sender_id, m.recipient_id, m.is_kiss, m.text, m.is_readable, 
	m.time_stamp AS last_message_ts, FROM_UNIXTIME(m.time_stamp, '%T' ) AS TIME, c.conversation_id AS conv_id, c.*,ms.is_replied  
	 FROM $mail_conv_tbl AS c 
	INNER JOIN 
	( SELECT * FROM $mail_msg_tbl
	  WHERE recipient_id=$id AND IF (sender_id!=$id, STATUS='a', 1) ORDER BY time_stamp DESC ) AS m 
	  ON(m.conversation_id=c.conversation_id) 
	  
	  INNER JOIN 
	  
		( SELECT conversation_id, IF(sender_id=$id,'yes','no') AS is_replied
		FROM $mail_msg_tbl 
		WHERE (recipient_id=$id OR sender_id=$id) ORDER BY time_stamp DESC 
		) AS ms ON(ms.conversation_id=c.conversation_id) 
	  WHERE (c.initiator_id=$id OR interlocutor_id=$id) AND c.bm_deleted 
	  NOT IN (IF (c.initiator_id=$id, '1, 3','2, 3')) AND IF (sender_id!=$id, STATUS='a', 1) GROUP BY c.conversation_id 
	 ORDER BY MAX( m.time_stamp ) DESC 
	) AS M
	LEFT JOIN $profile_table N ON M.sender_id=N.profile_id 
        LEFT JOIN $pic_tbl L ON N.profile_id=L.profile_id AND L.number =0 
	LEFT JOIN 
 	(SELECT COUNT( conversation_id ) AS conversation_number, conversation_id FROM $mail_msg_tbl 
	WHERE recipient_id = $id OR sender_id =$id GROUP BY conversation_id )AS TAB2 ON M.conversation_id=TAB2.conversation_id";
		 
		$res =  $db->Query($sql);
	 while($row1 = mysql_fetch_array($res))
	{
		if($row1['is_readable'] == 'yes')
		{
		$result[$i] =array('sender_id'=>$row1['sender_id'],'message_id'=>$row1['message_id'], 'recipient_id'=>$row1['recipient_id'],'conversation_id'=>$row1['conversation_id'], 'conversation_number'=>$row1['conversation_number'], 'text'=>$row1['text'], 'Time'=>$row1['Time'],'sex'=>$row1['sex'], 'username'=>$row1['username'], 'is_readable'=>$row1['is_readable'], 'bm_read'=>$row1['bm_read'], 'subject'=>$row1['subject'], 'Profile_Pic'=>$row1['Profile_Pic']);
		$i=$i+1; 
		}
		}
		$final = array();
	// Assigning to array Ends here
		if (is_array($result))
		{
		foreach($result as $array)
		{
		 
		array_push($final, $array);
		}
		}
	$final = '{"Status":"Live","count": '.$i.',"result": '.json_encode($final).'}';
	echo str_replace("},]","}]",$final);
	}
 
	else
	{
	echo '{"Message":"Session Expired"}';
	}
	}

		
	/** ---------------------------view inbox with membeship ends here------------ */
function isnotempty ($var) { 
return (strlen (trim ($var)) > 0); 
} 
	/** -------------------------view inbox backup starts here------------ */
	public function ViewInboxbackup($id)
	{	
		mysql_query('SET CHARACTER SET utf8'); 

		$essence			=	new Essentials();
		$profile_table		=	$essence->tblPrefix().'profile';
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$mail_conv_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$mail_msg_tbl		=	$essence->tblPrefix().'mailbox_message';
		$pic_tbl			=	$essence->tblPrefix().'profile_photo';
		
		/*echo   $sql="SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `last_message_ts`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS Time, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `skadate_mailbox_conversation` AS `c` INNER JOIN ( SELECT * FROM `skadate_mailbox_message` WHERE `recipient_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `skadate_mailbox_message` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT 0,15)as M

LEFT JOIN 

(SELECT skadate_profile.profile_id, skadate_profile.username, skadate_profile.birthdate, year( CURRENT_TIMESTAMP ) - year( skadate_profile.birthdate ) AS DOB, skadate_profile.sex FROM  skadate_profile) as N ON M.`sender_id`=N.profile_id ) AS K 

LEFT JOIN 

(SELECT skadate_profile_photo.profile_id as prof_id,skadate_profile_photo.photo_id,skadate_profile_photo.index,skadate_profile_photo.number, CONCAT( '/', 'userfiles/thumb_', CAST( skadate_profile_photo.profile_id AS CHAR ) , '_', CAST( skadate_profile_photo.photo_id AS CHAR ) , '_', CAST( skadate_profile_photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM skadate_profile_photo WHERE skadate_profile_photo.number =0 )AS L

ON K.profile_id=L.prof_id";
*/

$sql="SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number,text,Time,sex,username,subject,Profile_Pic FROM (SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `last_message_ts`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS Time, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `recipient_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT 0,15)as M LEFT JOIN (SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year( CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex FROM $profile_table) as N ON M.`sender_id`=N.profile_id ) AS K LEFT JOIN (SELECT $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id,$pic_tbl.index,$pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', CAST( $pic_tbl.photo_id AS CHAR ) , '_', CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $pic_tbl WHERE $pic_tbl.number =0 )AS L ON K.profile_id=L.prof_id) as TAB1

LEFT JOIN

(select count( conversation_id ) as conversation_number, conversation_id from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2
ON
		TAB1.conversation_id=TAB2.conversation_id";


	/*$sql="SELECT sender_id,message_id,TAB1.conversation_id,TAB1.conversation_number,text,Time,sex,username,subject,Profile_Pic FROM (SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `last_message_ts`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS Time, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `recipient_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT 0,15)as M LEFT JOIN (SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year( CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex FROM $profile_table) as N ON M.`sender_id`=N.profile_id ) AS K LEFT JOIN (SELECT $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id,$pic_tbl.index,$pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', CAST( $pic_tbl.photo_id AS CHAR ) , '_', CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $pic_tbl WHERE $pic_tbl.number =0 )AS L ON K.profile_id=L.prof_id) as TAB1

LEFT JOIN

(select count( conversation_id ) as conversation_number, conversation_id from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2
ON
		TAB1.conversation_id=TAB2.conversation_id";

	*/	
	/*$db1->Query($sql1);
	$row		 =	$db1->Row();
     $conv_count =	$row->conv_count;*/
	//echo $sql;
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					//$url		=	$this->getThumbImage($id);
					//if($url	==	"")
						//$url	=	"NULL";
					$profile = '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					$profile	=	str_replace('},]}','}]}',$profile);
					echo $profile;
				}
				else
				{
					echo '{"count":"0","Message":"Incorrect ID"}';
				}	
			}
	}
	/** -----------view inbox backup ends here----------- */
	/*************view inbox with limit starts here********************/
	  /*     * ***********view inbox with limit starts here******************* */ 
    public function ViewInboxByLimit($id, $skey, $start, $limit) { 
        mysql_query('SET CHARACTER SET utf8'); 

        $essence = new Essentials(); 
        $secure = new secure(); 

        $profile_table = $essence->tblPrefix() . 'profile'; 
        $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        $db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        $db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        $mail_conv_tbl = $essence->tblPrefix() . 'mailbox_conversation'; 
        $mail_msg_tbl = $essence->tblPrefix() . 'mailbox_message'; 
        $pic_tbl = $essence->tblPrefix() . 'profile_photo'; 
        $membership_limit = $essence->tblPrefix() . 'link_membership_type_service'; 
        $membership_srv = $essence->tblPrefix() . 'link_membership_service_limit'; 
        $key = $essence->tblPrefix() . 'lang_key'; 
        $value = $essence->tblPrefix() . 'lang_value'; 
//checking user sign in or not 
        $res = $secure->CheckSecure($id, $skey); 
        //$res=1; 
        if ($res == 1) { 

        $sql = "SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number, 
            text,is_readable,bm_read,Time,sex,username,subject,Profile_Pic FROM 
            (SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) 
            AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, 
            `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` 
            AS `last_message_ts`,`m`.`time_stamp` AS Time, `c`.`conversation_id` as conv_id, 
            `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` 
            WHERE `recipient_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) 
            AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN 
            ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` 
            FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` ASC ) 
            AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE 
            (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN 
            (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) 
            GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT $start,$limit)as 
            M LEFT JOIN (SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, 
            year( CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex 
            FROM $profile_table) as N ON M.`sender_id`=N.profile_id ) AS K LEFT JOIN 
            (SELECT $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id,$pic_tbl.index,$pic_tbl.number, 
            CONCAT( '/', 'userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', 
            CAST( $pic_tbl.photo_id AS CHAR ) , '_', CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) 
            AS Profile_Pic FROM $pic_tbl WHERE $pic_tbl.number =0 )AS L ON K.profile_id=L.prof_id) as TAB1 
            LEFT JOIN (select count( conversation_id ) as conversation_number, conversation_id 
            from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2 
            ON TAB1.conversation_id=TAB2.conversation_id"; 

$sqlT = "SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number, 
            text,is_readable,bm_read,Time,sex,username,subject,Profile_Pic FROM 
            (SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) 
            AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, 
            `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` 
            AS `last_message_ts`,`m`.`time_stamp` AS Time, `c`.`conversation_id` as conv_id, 
            `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` 
            WHERE `recipient_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) 
            AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN 
            ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` 
            FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` ASC ) 
            AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE 
            (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN 
            (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) 
            GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC )as 
            M LEFT JOIN (SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, 
            year( CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex 
            FROM $profile_table) as N ON M.`sender_id`=N.profile_id ) AS K LEFT JOIN 
            (SELECT $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id,$pic_tbl.index,$pic_tbl.number, 
            CONCAT( '/', 'userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', 
            CAST( $pic_tbl.photo_id AS CHAR ) , '_', CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) 
            AS Profile_Pic FROM $pic_tbl WHERE $pic_tbl.number =0 )AS L ON K.profile_id=L.prof_id) as TAB1 
            LEFT JOIN (select count( conversation_id ) as conversation_number, conversation_id 
            from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2 
            ON TAB1.conversation_id=TAB2.conversation_id"; 

           /*$sqlTold = "SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number, 
                    text,is_readable,bm_read,Time,sex,username,subject,Profile_Pic FROM 
                    (SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) 
                    AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, 
                    `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` 
                    AS `last_message_ts`, `m`.`time_stamp` AS Time, `c`.`conversation_id` as conv_id, 
                    `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN 
                    ( SELECT * FROM `$mail_msg_tbl` WHERE `recipient_id`=$id AND IF 
                    (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` 
                    ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, 
                    IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` 
                    WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) 
                    AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) 
                    WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` 
                    NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF 
                    (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX 
                    ( `m`.`time_stamp` ))as M LEFT JOIN (SELECT $profile_table.profile_id, 
                    $profile_table.username, $profile_table.birthdate, year( CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, 
                    $profile_table.sex FROM $profile_table) as N ON M.`sender_id`=N.profile_id ) 
                    AS K LEFT JOIN (SELECT $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id, 
                    $pic_tbl.index,$pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', 
                    CAST( $pic_tbl.profile_id AS CHAR ) , '_', CAST( $pic_tbl.photo_id AS CHAR ) , '_', 
                    CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $pic_tbl 
                    WHERE $pic_tbl.number =0 )AS L ON K.profile_id=L.prof_id) as TAB1 LEFT JOIN 
                    (select count( conversation_id ) as conversation_number, conversation_id 
                    from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id ) 
                    as TAB2 ON TAB1.conversation_id=TAB2.conversation_id";*/ 

            $a = $db2->Query1($sqlT); 
            $resRows = mysql_fetch_array($a); 
            $totalCount = mysql_num_rows($a); 
//echo "toalCount".$totalCount;	 
            $res = $db->Query($sql); 
            $i = 0; 

            while ($row1 = mysql_fetch_array($res)) { 
                $dateNew = date("m-d-Y H:i:s", $row1['Time']); 
                $Count = mysql_num_rows($row1); 
                $text = $row1['text']; 

$conid=$row1['conversation_id'];
           $sqln="SELECT `sender_id`,`recipient_id` FROM `$mail_msg_tbl` WHERE `conversation_id`='$conid' ORDER BY `time_stamp` DESC Limit 1";
            $sqlexe=$db1->query($sqln);
            $sqlfet=mysql_fetch_array($sqlexe);
            $sendervalue=$sqlfet['sender_id'];
            $recipientvalue=$sqlfet['recipient_id'];




                if ($row1['is_readable'] == 'no') { 

                    $text = preg_replace("/<[^>]*>/", "", $text); 
                    $text = str_replace(" ", "", $text); 

	                        //echo $text."</br>"; 
                    if (($text == "[wink]4[/wink]") or ($text == "[smiles]58[/smiles]")) { 
                        //echo $text."</br>"; 
                        // echo "is_readable".$text; 
                        $result[$i] = array('sender_id' => $row1['sender_id'], 'message_id' => $row1['message_id'], 'recipient_id' => $row1['recipient_id'], 'conversation_id' => $row1['conversation_id'], 'conversation_number' => $row1['conversation_number'], 'text' => $text, 'Time' => $dateNew, 'sex' => $row1['sex'], 'username' => $row1['username'], 'is_readable' => $row1['is_readable'], 'bm_read' => $row1['bm_read'], 'subject' => $row1['subject'], 'Profile_Pic' => $row1['Profile_Pic'],'LS' =>$sendervalue,'LR' =>$recipientvalue); 
                        $i = $i + 1; 
                    } 
                } else { 
                    //$text = $row1['text']; 
                    //echo $text; 
                    $result[$i] = array('sender_id' => $row1['sender_id'], 'message_id' => $row1['message_id'], 'recipient_id' => $row1['recipient_id'], 'conversation_id' => $row1['conversation_id'], 'conversation_number' => $row1['conversation_number'], 'text' => $text, 'Time' => $dateNew, 'sex' => $row1['sex'], 'username' => $row1['username'], 'is_readable' => $row1['is_readable'], 'bm_read' => $row1['bm_read'], 'subject' => $row1['subject'], 'Profile_Pic' => $row1['Profile_Pic'],'LS' =>$sendervalue,'LR' =>$recipientvalue); 
                    $i = $i + 1; 
                } 
            } 
//echo $i; 
            $final = array(); 
// Assigning to array Ends here 
            if (is_array($result)) { 
                foreach ($result as $array) { 

                    array_push($final, $array); 
                } 
            } 
            $final = '{"Status":"Live","Total rows":' . $totalCount . ',"count": ' . $i . ',"result": ' . json_encode($final) . '}'; 

            $profile = str_replace('},]}', '}]}', $final); 
            echo $profile; 
        } else { 
            echo '{"Message":"Session Expired"}'; 
        } 
    } 

    /*     * ***********view inbox with limit & backup starts here******************* */ 
/*************view inbox with limit starts here********************/
public function ViewInboxByLimitoldbackup($id,$skey,$start,$limit)
{
mysql_query('SET CHARACTER SET utf8'); 

$essence = new Essentials();
$secure             =   new secure();

$profile_table = $essence->tblPrefix().'profile';
$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
$db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
$mail_conv_tbl = $essence->tblPrefix().'mailbox_conversation';
$mail_msg_tbl = $essence->tblPrefix().'mailbox_message';
$pic_tbl = $essence->tblPrefix().'profile_photo';
$membership_limit = $essence->tblPrefix().'link_membership_type_service';
$membership_srv = $essence->tblPrefix().'link_membership_service_limit';
  $key                    =    $essence->tblPrefix().'lang_key';
        $value                    =    $essence->tblPrefix().'lang_value';
//checking user sign in or not
$res = $secure->CheckSecure($id,$skey);
if($res==1)
{


$sql="SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number,text,is_readable,bm_read,Time,sex,username,subject,
CONCAT( '/', 'userfiles/thumb_', CAST( L.profile_id AS CHAR ), '_', CAST( L.photo_id AS CHAR ) , '_', CAST( L.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
 FROM

	(
	
	SELECT m.message_id, COUNT(m.message_id) AS MailCount, COUNT(m.conversation_id) AS conversation_number, 
	m.sender_id, m.recipient_id, m.is_kiss, m.text, m.is_readable, 
	m.time_stamp AS last_message_ts, FROM_UNIXTIME(m.time_stamp, '%T' ) AS TIME, c.conversation_id AS conv_id, c.*,ms.is_replied  
	 FROM $mail_conv_tbl AS c 
	INNER JOIN 
	( SELECT * FROM $mail_msg_tbl
	  WHERE recipient_id=$id AND IF (sender_id!=$id, STATUS='a', 1) ORDER BY time_stamp DESC ) AS m 
	  ON(m.conversation_id=c.conversation_id) 
	  
	  INNER JOIN 
	  
		( SELECT conversation_id, IF(sender_id=$id,'yes','no') AS is_replied
		FROM $mail_msg_tbl 
		WHERE (recipient_id=$id OR sender_id=$id) ORDER BY time_stamp DESC  
		) AS ms ON(ms.conversation_id=c.conversation_id) 
	  WHERE (c.initiator_id=$id OR interlocutor_id=$id) AND c.bm_deleted 
	  NOT IN (IF (c.initiator_id=$id, '1, 3','2, 3')) AND IF (sender_id!=$id, STATUS='a', 1) GROUP BY c.conversation_id 
	 ORDER BY MAX( m.time_stamp ) DESC LIMIT $start,$limit
	) AS M
	LEFT JOIN $profile_table N ON M.sender_id=N.profile_id 
        LEFT JOIN $pic_tbl L ON N.profile_id=L.profile_id AND L.number =0 
	LEFT JOIN 
 	(SELECT COUNT( conversation_id ) AS conversation_number, conversation_id FROM $mail_msg_tbl 
	WHERE recipient_id = $id OR sender_id =$id GROUP BY conversation_id )AS TAB2 ON M.conversation_id=TAB2.conversation_id";
$sqlT="SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number,text,is_readable,bm_read,Time,sex,username,subject,
CONCAT( '/', 'userfiles/thumb_', CAST( L.profile_id AS CHAR ), '_', CAST( L.photo_id AS CHAR ) , '_', CAST( L.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
 FROM

	(
	
	SELECT m.message_id, COUNT(m.message_id) AS MailCount, COUNT(m.conversation_id) AS conversation_number, 
	m.sender_id, m.recipient_id, m.is_kiss, m.text, m.is_readable, 
	m.time_stamp AS last_message_ts, FROM_UNIXTIME(m.time_stamp, '%T' ) AS TIME, c.conversation_id AS conv_id, c.*,ms.is_replied  
	 FROM $mail_conv_tbl AS c 
	INNER JOIN 
	( SELECT * FROM $mail_msg_tbl
	  WHERE recipient_id=$id AND IF (sender_id!=$id, STATUS='a', 1) ORDER BY time_stamp DESC ) AS m 
	  ON(m.conversation_id=c.conversation_id) 
	  
	  INNER JOIN 
	  
		( SELECT conversation_id, IF(sender_id=$id,'yes','no') AS is_replied
		FROM $mail_msg_tbl 
		WHERE (recipient_id=$id OR sender_id=$id) ORDER BY time_stamp DESC 
		) AS ms ON(ms.conversation_id=c.conversation_id) 
	  WHERE (c.initiator_id=$id OR interlocutor_id=$id) AND c.bm_deleted 
	  NOT IN (IF (c.initiator_id=$id, '1, 3','2, 3')) AND IF (sender_id!=$id, STATUS='a', 1) GROUP BY c.conversation_id 
	 ORDER BY MAX( m.time_stamp ) DESC 
	) AS M
	LEFT JOIN $profile_table N ON M.sender_id=N.profile_id 
        LEFT JOIN $pic_tbl L ON N.profile_id=L.profile_id AND L.number =0 
	LEFT JOIN 
 	(SELECT COUNT( conversation_id ) AS conversation_number, conversation_id FROM $mail_msg_tbl 
	WHERE recipient_id = $id OR sender_id =$id GROUP BY conversation_id )AS TAB2 ON M.conversation_id=TAB2.conversation_id";
 
$a=$db2->Query1($sqlT);
//$resRows=mysql_fetch_array($a);


$j=0;
 
 while($row2 = mysql_fetch_array($a))
{
//$Count=mysql_num_rows($row1);
$text = $row2['text'];
if($row2['is_readable'] == 'no')
{
$j=$j+1;
}
else
{
$j=$j+1;
}
 
}



$totalCount=$j;
//echo "toalCount".$totalCount;
$res =  $db->Query($sql);
$i=0;
 
 while($row1 = mysql_fetch_array($res))
{
$Count=mysql_num_rows($row1);
$text = $row1['text'];
if($row1['is_readable'] == 'no')
				{
							
							$text =	preg_replace("/<[^>]*>/","",$text);
							$text =	str_replace(" ","",$text);
							
							
							//echo $text."</br>";
							if(($text =="[wink]4[/wink]")or($text =="[smiles]58[/smiles]"))
							{
								//echo $text."</br>";
					  			$result[$i] =array('sender_id'=>$row1['sender_id'],'message_id'=>$row1['message_id'], 'recipient_id'=>$row1['recipient_id'],'conversation_id'=>$row1['conversation_id'], 'conversation_number'=>$row1['conversation_number'], 'text'=>$text, 'Time'=>$row1['Time'],'sex'=>$row1['sex'], 'username'=>$row1['username'], 'is_readable'=>$row1['is_readable'],'bm_read'=>$row1['bm_read'],'subject'=>$row1['subject'], 'Profile_Pic'=>$row1['Profile_Pic']);
								$i=$i+1;
							}
}

						
 else
{
 
$result[$i] =array('sender_id'=>$row1['sender_id'],'message_id'=>$row1['message_id'], 'recipient_id'=>$row1['recipient_id'],'conversation_id'=>$row1['conversation_id'], 'conversation_number'=>$row1['conversation_number'], 'text'=>$text, 'Time'=>$row1['Time'],'sex'=>$row1['sex'], 'username'=>$row1['username'], 'is_readable'=>$row1['is_readable'],'bm_read'=>$row1['bm_read'],'subject'=>$row1['subject'], 'Profile_Pic'=>$row1['Profile_Pic']);
$i=$i+1;
}
 
}
//echo $i;
$final = array();
// Assigning to array Ends here
if (is_array($result))
{
foreach($result as $array)
{
 
array_push($final, $array);
}
}
$final = '{"Status":"Live","Total rows":'.$totalCount.',"count": '.$i.',"result": '.json_encode($final).'}'; 
 
$profile = str_replace('},]}','}]}',$final);
echo $profile;
}
 
 
else
{
echo '{"Message":"Session Expired"}';
}
}

	/*************view inbox with limit & backup starts here********************/
	public function ViewInboxByLimitBackup($id,$start,$limit)
	{	
		mysql_query('SET CHARACTER SET utf8'); 

		$essence			=	new Essentials();
		$profile_table		=	$essence->tblPrefix().'profile';
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$mail_conv_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$mail_msg_tbl		=	$essence->tblPrefix().'mailbox_message';
		$pic_tbl			=	$essence->tblPrefix().'profile_photo';
		
		/*echo   $sql="SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `last_message_ts`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS Time, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `skadate_mailbox_conversation` AS `c` INNER JOIN ( SELECT * FROM `skadate_mailbox_message` WHERE `recipient_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `skadate_mailbox_message` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT 0,15)as M

LEFT JOIN 

(SELECT skadate_profile.profile_id, skadate_profile.username, skadate_profile.birthdate, year( CURRENT_TIMESTAMP ) - year( skadate_profile.birthdate ) AS DOB, skadate_profile.sex FROM  skadate_profile) as N ON M.`sender_id`=N.profile_id ) AS K 

LEFT JOIN 

(SELECT skadate_profile_photo.profile_id as prof_id,skadate_profile_photo.photo_id,skadate_profile_photo.index,skadate_profile_photo.number, CONCAT( '/', 'userfiles/thumb_', CAST( skadate_profile_photo.profile_id AS CHAR ) , '_', CAST( skadate_profile_photo.photo_id AS CHAR ) , '_', CAST( skadate_profile_photo.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM skadate_profile_photo WHERE skadate_profile_photo.number =0 )AS L

ON K.profile_id=L.prof_id";
*/

$sql="SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number,text,Time,sex,username,subject,Profile_Pic FROM (SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `last_message_ts`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS Time, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `recipient_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT $start,$limit)as M LEFT JOIN (SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year( CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex FROM $profile_table) as N ON M.`sender_id`=N.profile_id ) AS K LEFT JOIN (SELECT $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id,$pic_tbl.index,$pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', CAST( $pic_tbl.photo_id AS CHAR ) , '_', CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $pic_tbl WHERE $pic_tbl.number =0 )AS L ON K.profile_id=L.prof_id) as TAB1

LEFT JOIN

(select count( conversation_id ) as conversation_number, conversation_id from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2
ON
		TAB1.conversation_id=TAB2.conversation_id";


	/*$sql="SELECT sender_id,message_id,TAB1.conversation_id,TAB1.conversation_number,text,Time,sex,username,subject,Profile_Pic FROM (SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `last_message_ts`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS Time, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `recipient_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT 0,15)as M LEFT JOIN (SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year( CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex FROM $profile_table) as N ON M.`sender_id`=N.profile_id ) AS K LEFT JOIN (SELECT $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id,$pic_tbl.index,$pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', CAST( $pic_tbl.photo_id AS CHAR ) , '_', CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $pic_tbl WHERE $pic_tbl.number =0 )AS L ON K.profile_id=L.prof_id) as TAB1

LEFT JOIN

(select count( conversation_id ) as conversation_number, conversation_id from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2
ON
		TAB1.conversation_id=TAB2.conversation_id ";

	*/	
	/*$db1->Query($sql1);
	$row		 =	$db1->Row();
     $conv_count =	$row->conv_count;*/
	//echo $sql;
//for counting total rows

	$sqlT="SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number,text,Time,sex,username,subject,Profile_Pic FROM (SELECT * FROM (SELECT * FROM (SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `last_message_ts`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS Time, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `recipient_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT 0,15)as M LEFT JOIN (SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year( CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex FROM $profile_table) as N ON M.`sender_id`=N.profile_id ) AS K LEFT JOIN (SELECT $pic_tbl.profile_id as prof_id,$pic_tbl.photo_id,$pic_tbl.index,$pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', CAST( $pic_tbl.photo_id AS CHAR ) , '_', CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $pic_tbl WHERE $pic_tbl.number =0 )AS L ON K.profile_id=L.prof_id) as TAB1

LEFT JOIN

(select count( conversation_id ) as conversation_number, conversation_id from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2
ON
		TAB1.conversation_id=TAB2.conversation_id";	
	
$db->Query($sqlT);
$totalCount=$db->RowCount();	
	
	
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					//$url		=	$this->getThumbImage($id);
					//if($url	==	"")
						//$url	=	"NULL";
					$profile = '{"Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					$profile	=	str_replace('},]}','}]}',$profile);
					echo $profile;
				}
				else
				{
					echo '{"count":"0","Message":"Incorrect ID"}';
				}	
			}
	}
	
	
	
	
	/*************view inbox with limit & backup ends here********************/
	/** -----------------Send mail with membership starts here----------------- */
	public function SendMail($id,$skey)
	{	
		mysql_query('SET CHARACTER SET utf8'); 

		$essence			=	new Essentials();
		$secure             =   new secure();
		$profile_table		=	$essence->tblPrefix().'profile';
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$mail_conv_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$mail_msg_tbl		=	$essence->tblPrefix().'mailbox_message';
		$profile_pic_tbl	=	$essence->tblPrefix().'profile_photo';
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
		//checking user sign in or not
		$res = $secure->CheckSecure($id,$skey);
		if($res == 1)
		{
		$i=0;
	
								  $sql="SELECT sender_id,message_id,recipient_id,conversation_id,conversation_number,text,is_readable,Time,sex,username,subject,
CONCAT( '/', 'userfiles/thumb_', CAST( B.profile_id AS CHAR ) , '_', CAST( B.photo_id AS CHAR ) , '_', CAST( B.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
 FROM 
  
     (SELECT m.message_id, COUNT(m.message_id) AS MailCount,COUNT(m.conversation_id) AS conversation_number, m.sender_id, 
    m.recipient_id, m.is_kiss, m.text, m.is_readable, FROM_UNIXTIME(m.time_stamp, '%T' ) AS TIME,m.time_stamp AS TT, 
    c.conversation_id AS conv_id, c.*, ms.is_replied 
     FROM $mail_conv_tbl AS c 
     INNER JOIN 

	(SELECT * FROM $mail_msg_tbl 
	 WHERE sender_id=$id AND IF (sender_id!=$id, STATUS='a', 1) ORDER BY time_stamp DESC 
	 ) AS m 
	  ON(m.conversation_id=c.conversation_id) 
	  
	  INNER JOIN 

	     ( 
	      SELECT recipient_id,conversation_id, IF(sender_id=$id,'yes','no') AS is_replied 
	      FROM $mail_msg_tbl 
	      WHERE (recipient_id=$id OR sender_id=$id) ORDER BY time_stamp DESC
	      ) AS 
	      ms ON ms.conversation_id=c.conversation_id
	      WHERE 
	      (c.initiator_id=$id OR interlocutor_id=$id) AND
	      c.bm_deleted NOT IN (IF (c.initiator_id=$id, '1, 3','2, 3')) AND IF (sender_id!=$id,STATUS='a', 1) 
	      GROUP BY c.conversation_id ORDER BY MAX( m.time_stamp ) DESC) AS P 
	      
	      INNER JOIN $profile_table A ON P.recipient_id=A.profile_id
	      
	      LEFT JOIN $profile_pic_tbl B ON A.profile_id=B.profile_id AND B.number =0 
	      ORDER BY TT DESC";
 
 $res =  $db->Query($sql);
 while($row1 = mysql_fetch_array($res))
			{
			$text = $row1['text'];
			//echo $text1;
			//$text = trim($text1);
			if($row1['is_readable'] == 'no')
			{
			$text =	preg_replace("/<[^>]*>/","",$text); 
			$text =	str_replace(" ","",$text);
			
					if(($text =='[wink]4[/wink]') or ($text == '[smiles]58[/smiles]') )
					{
				  // echo "is_readable".$text;
					 $result[$i] =array('sender_id'=>$row1['sender_id'],'message_id'=>$row1['message_id'], 'recipient_id'=>$row1['recipient_id'],'conversation_id'=>$row1['conversation_id'], 'conversation_number'=>$row1['conversation_number'], 'text'=>$text, 'Time'=>$row1['Time'],'sex'=>$row1['sex'], 'username'=>$row1['username'], 'is_readable'=>$row1['is_readable'], 'subject'=>$row1['subject'], 'Profile_Pic'=>$row1['Profile_Pic']);
							$i=$i+1;
					}
			}
			else
			{
				
				$text = $row1['text'];
				//echo $text;
				$result[$i] =array('sender_id'=>$row1['sender_id'],'message_id'=>$row1['message_id'], 'recipient_id'=>$row1['recipient_id'],'conversation_id'=>$row1['conversation_id'], 'conversation_number'=>$row1['conversation_number'], 'text'=>$text, 'Time'=>$row1['Time'],'sex'=>$row1['sex'], 'username'=>$row1['username'], 'is_readable'=>$row1['is_readable'], 'subject'=>$row1['subject'], 'Profile_Pic'=>$row1['Profile_Pic']);
					$i=$i+1;
			}
		}
				$final = array();
		// Assigning to array Ends here
if (is_array($result))
{
	foreach($result as $array)
	{
		
			array_push($final, $array);
	}
}			
			$final	=	 '{"Status":"Live","count": '.$i.',"result": '.json_encode($final).'}';
		//$final	=	 '{"Status":"Live","count": '.$i.',"result": '.json_encode($final).'}';
			echo str_replace("},]","}]",$final);
	}
	else
	{
		echo '{"Message":"Session Expired"}';
	}
}
				
				
				
				
				
				
 
				/*  if($db->Query($sql))
						{
							if($db->RowCount())
							{	
								$profile = '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
							    //$profile = str_replace(" ", "", $profile);
							    echo $profile = str_replace("},]", "}]", $profile);
							}
							else
							{
								echo '{"Status":"Live","count":"0","Message":"Incorrect ID"}';
							}	
						}
				
				
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}	*/
						
	
	 /** ----------------Send mail with membership ends here-------------- */
	 /** ----------------Send mail backup starts here----------------------- */
	 
	 	public function SendMailBackup($id)
	{	
		mysql_query('SET CHARACTER SET utf8'); 

		$essence			=	new Essentials();
		$profile_table		=	$essence->tblPrefix().'profile';
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$mail_conv_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$mail_msg_tbl		=	$essence->tblPrefix().'mailbox_message';
		$profile_pic_tbl	=	$essence->tblPrefix().'profile_photo';
				
		
      /* $sql="SELECT * FROM(select message_id,conversation_id,time_stamp,sender_id,recipient_id,text,subject,profile_id,username,sex,Time,conversation_number from 
(SELECT skadate_mailbox_message.message_id,skadate_mailbox_message.conversation_id,skadate_mailbox_message.time_stamp,skadate_mailbox_message.sender_id, skadate_mailbox_message.recipient_id,skadate_mailbox_message.text,skadate_mailbox_message.is_kiss,skadate_mailbox_message.is_readable,skadate_mailbox_message.status, skadate_mailbox_message.hash,skadate_mailbox_conversation.initiator_id,skadate_mailbox_conversation.interlocutor_id,skadate_mailbox_conversation.subject, skadate_mailbox_conversation.bm_read,skadate_mailbox_conversation.bm_deleted,skadate_mailbox_conversation.bm_read_special,skadate_mailbox_conversation.conversation_ts, skadate_mailbox_conversation.is_system, skadate_profile.profile_id,skadate_profile.email, skadate_profile.username, skadate_profile.password, skadate_profile.sex, skadate_profile.match_sex, skadate_profile.birthdate, skadate_profile.headline, skadate_profile.general_description, skadate_profile.match_agerange, skadate_profile.custom_location, skadate_profile.country_id, skadate_profile.zip, skadate_profile.state_id, skadate_profile.city_id, skadate_profile.join_stamp, skadate_profile.activity_stamp, skadate_profile.membership_type_id, skadate_profile.affiliate_id, skadate_profile.email_verified,skadate_profile.reviewed, skadate_profile.has_photo, skadate_profile.has_media, skadate_profile.featured, skadate_profile.register_invite_score,skadate_profile.rate_score,skadate_profile.rates,skadate_profile.language_id,skadate_profile.join_ip, skadate_profile.neigh_location, skadate_profile.neigh_location_distance, skadate_profile.bg_color, skadate_profile.bg_image, skadate_profile.bg_image_url, skadate_profile.bg_image_mode, skadate_profile.bg_image_status, skadate_profile.has_music, skadate_profile.is_private,
 FROM_UNIXTIME(time_stamp, '%T' ) AS Time 
FROM `skadate_mailbox_conversation` , `skadate_mailbox_message` , `skadate_profile`

 WHERE
 `skadate_mailbox_message`.sender_id =$id AND `skadate_mailbox_conversation`.bm_deleted !=1 AND `skadate_mailbox_message`.conversation_id = `skadate_mailbox_conversation`.conversation_id AND `skadate_profile`.profile_id =`skadate_mailbox_message`.recipient_id)X, 

 (select count( conversation_id ) as conversation_numberr,'0' as conversation_number,conversation_id as conversation_i from skadate_mailbox_message where recipient_id = $id or sender_id = $id group by conversation_i )Y 

where X.conversation_id=Y. conversation_i ) as A 

LEFT JOIN

(select *,CONCAT( '/','userfiles/thumb_', CAST( pictable .profile_id AS CHAR ) , '_',CAST( pictable .photo_id AS CHAR ) , '_',CAST( pictable .index AS CHAR ) , '.jpg' ) as Profile_Pic 
FROM `skadate_profile_photo` as pictable where `number` = 0) as  B

ON

A.profile_id = B.profile_id  ORDER BY time_stamp DESC";*/


/*$sql="SELECT * FROM(SELECT * FROM(SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS `Time`, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `sender_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT 0,15) as P

JOIN

(SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year(
CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex
FROM $profile_table) AS Q ON P.recipient_id=Q.profile_id) AS A

LEFT JOIN

(SELECT $profile_pic_tbl.profile_id as prof_id,$profile_pic_tbl.photo_id,$profile_pic_tbl.index,$profile_pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_', CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $profile_pic_tbl  WHERE $profile_pic_tbl.number =0 )AS B

 ON A.profile_id=B.prof_id";
 
*/
	
	
		$sql="SELECT sender_id,message_id,recipient_id,conversation_id,conversation_number,text,Time,sex,username,is_readable,subject,Profile_Pic FROM(SELECT * FROM(SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS `Time`, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `sender_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE 
(`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND  `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id,`status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT 0,15) as P
JOIN
(SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year(
CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex
FROM $profile_table) AS Q ON P.recipient_id=Q.profile_id) AS A
LEFT JOIN
(SELECT $profile_pic_tbl.profile_id as prof_id,$profile_pic_tbl.photo_id,$profile_pic_tbl.index,$profile_pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_', CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $profile_pic_tbl  WHERE $profile_pic_tbl.number =0 )AS B
 ON A.profile_id=B.prof_id";
	if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$profile = '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo $profile = str_replace("},]", "}]", $profile);
				}
				else
				{
					echo '{"count":"0","Message":"Incorrect ID"}';
				}	
			}
	}

	 
	 /** -----------------Send mail backup ends here------------------------ */
	 public function SendMailByLimit($id, $skey, $start, $limit) { 
        date_default_timezone_get(); 
        mysql_query('SET CHARACTER SET utf8'); 

        $essence = new Essentials(); 
        $secure = new secure(); 
        $profile_table = $essence->tblPrefix() . 'profile'; 
        $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        $db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 

        $mail_conv_tbl = $essence->tblPrefix() . 'mailbox_conversation'; 
        $mail_msg_tbl = $essence->tblPrefix() . 'mailbox_message'; 
        $profile_pic_tbl = $essence->tblPrefix() . 'profile_photo'; 
        $membership_limit = $essence->tblPrefix() . 'link_membership_type_service'; 
        $membership_srv = $essence->tblPrefix() . 'link_membership_service_limit'; 
        //checking user sign in or not 
        $res = $secure->CheckSecure($id, $skey); 
        $res=1; 
        if ($res == 1) { 
            $i = 0; 

 $sql = "SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number,text,Time,sex,username,is_readable,subject,Profile_Pic FROM(SELECT * FROM(SELECT * FROM(SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `Time`, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `sender_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE 
(`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND  `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id,`status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT $start,$limit) as P 
JOIN 
(SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year( 
CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex 
FROM $profile_table) AS Q ON P.recipient_id=Q.profile_id) AS A 
LEFT JOIN 
(SELECT $profile_pic_tbl.profile_id as prof_id,$profile_pic_tbl.photo_id,$profile_pic_tbl.index,$profile_pic_tbl.number, CONCAT( '/$', 'userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_', CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $profile_pic_tbl  WHERE $profile_pic_tbl.number =0 )AS B 
 ON A.profile_id=B.prof_id) as TAB1 
		 
		LEFT JOIN 
		 
		(select count( conversation_id ) as conversation_number, conversation_id from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2 
		ON 
		TAB1.conversation_id=TAB2.conversation_id ORDER BY TIME DESC"; 


      /*   $sql = "SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number,text,Time,sex,username,is_readable,subject,Profile_Pic FROM(SELECT * FROM(SELECT * FROM(SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `Time`, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `sender_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE 
(`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND  `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id,`status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT $start,$limit) as P 
JOIN 
(SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year( 
CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex 
FROM $profile_table) AS Q ON P.recipient_id=Q.profile_id) AS A 
LEFT JOIN 
(SELECT $profile_pic_tbl.profile_id as prof_id,$profile_pic_tbl.photo_id,$profile_pic_tbl.index,$profile_pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_', CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $profile_pic_tbl  WHERE $profile_pic_tbl.number =0 )AS B 
 ON A.profile_id=B.prof_id) as TAB1 
		 
		LEFT JOIN 
		 
		(select count( conversation_id ) as conversation_number, conversation_id from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2 
		ON 
		TAB1.conversation_id=TAB2.conversation_id ORDER BY TIME DESC"; */

//echo $sql; 
            //for counting total rows 
            $sqlT = "SELECT sender_id,message_id,recipient_id,TAB2.conversation_id,TAB2.conversation_number,text,Time,sex,username,is_readable,subject,Profile_Pic FROM(SELECT * FROM(SELECT * FROM(SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `Time`, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `sender_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE 
(`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND  `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id,`status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC) as P 
JOIN 
(SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year( 
CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex 
FROM $profile_table) AS Q ON P.recipient_id=Q.profile_id) AS A 
LEFT JOIN 
(SELECT $profile_pic_tbl.profile_id as prof_id,$profile_pic_tbl.photo_id,$profile_pic_tbl.index,$profile_pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_', CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $profile_pic_tbl  WHERE $profile_pic_tbl.number =0 )AS B 
 ON A.profile_id=B.prof_id) as TAB1 
		 
		LEFT JOIN 
		 
		(select count( conversation_id ) as conversation_number, conversation_id from `$mail_msg_tbl` where recipient_id = $id or sender_id =$id group by conversation_id )as TAB2 
		ON 
		TAB1.conversation_id=TAB2.conversation_id ORDER BY TIME DESC"; 

// echo $sql; 
            $db1->Query1($sqlT); 
            $totalCount = $db1->RowCount(); 
            /* $result1=@mysql_query($sqlT); 
              echo "Total".$totalCount=@mysql_num_rows($result1); 
             */ $res = $db->Query($sql); 
            while ($row1 = mysql_fetch_array($res)) { 
                $dateNew = date("m-d-Y H:i:s", $row1['Time']); 
                $text = $row1['text']; 
                //echo $text1; 
                //$text = trim($text1); 
                if ($row1['is_readable'] == 'no') { 
                    $text = preg_replace("/<[^>]*>/", "", $text); 
                    $text = str_replace(" ", "", $text); 

                    if (($text == '[wink]4[/wink]') or ($text == '[smiles]58[/smiles]')) { 
                        // echo "is_readable".$text; 
                        $result[$i] = array('sender_id' => $row1['sender_id'], 'message_id' => $row1['message_id'], 'recipient_id' => $row1['recipient_id'], 'conversation_id' => $row1['conversation_id'], 'conversation_number' => $row1['conversation_number'], 'text' => $text, 'Time' => $dateNew, 'sex' => $row1['sex'], 'username' => $row1['username'], 'is_readable' => $row1['is_readable'], 'subject' => $row1['subject'], 'Profile_Pic' => $row1['Profile_Pic'],'LS'=>$row1['sender_id'],'LR'=>$row1['recipient_id']); 
                        $i = $i + 1; 
                    } 
                } else { 

                    $text = $row1['text']; 
                    //echo $text; 
                    $result[$i] = array('sender_id' => $row1['sender_id'], 'message_id' => $row1['message_id'], 'recipient_id' => $row1['recipient_id'], 'conversation_id' => $row1['conversation_id'], 'conversation_number' => $row1['conversation_number'], 'text' => $text, 'Time' => $dateNew, 'sex' => $row1['sex'], 'username' => $row1['username'], 'is_readable' => $row1['is_readable'], 'subject' => $row1['subject'], 'Profile_Pic' => $row1['Profile_Pic'],'LS'=>$row1['sender_id'],'LR'=>$row1['recipient_id']); 
                    $i = $i + 1; 
                } 
            } 
            $final = array(); 
            // Assigning to array Ends here 
            if (is_array($result)) { 
                foreach ($result as $array) { 

                    array_push($final, $array); 
                } 
            } if ($i == 0) { 
                $totalCount = 0; 
            } 
            //$final	=	'{"Status":"Live","Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON($final).']}'; 
            $final = '{"Status":"Live","Total rows":' . $totalCount . ',"count": ' . $i . ',"result": ' . json_encode($final) . '}'; 
            $profile = str_replace('},]}', '}]}', $final); 
            echo $profile; 
        } else { 
            echo '{"Message":"Session Expired"}'; 
        } 
    } 
	  public function SendMailByLimitOLD($id,$skey,$start,$limit)
	{	
		mysql_query('SET CHARACTER SET utf8'); 

		$essence			=	new Essentials();
		$secure     =   new secure();
		$profile_table		=	$essence->tblPrefix().'profile';
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

		$mail_conv_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$mail_msg_tbl		=	$essence->tblPrefix().'mailbox_message';
		$profile_pic_tbl	=	$essence->tblPrefix().'profile_photo';
		$membership_limit	=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
	//checking user sign in or not
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
	$i=0;
		

$sql="SELECT sender_id,message_id,recipient_id,conversation_id,conversation_number,text,is_readable,Time,sex,username,subject,
CONCAT( '/', 'userfiles/thumb_', CAST( B.profile_id AS CHAR ) , '_', CAST( B.photo_id AS CHAR ) , '_', CAST( B.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
 FROM 
  
     (SELECT m.message_id, COUNT(m.message_id) AS MailCount,COUNT(m.conversation_id) AS conversation_number, m.sender_id, 
    m.recipient_id, m.is_kiss, m.text, m.is_readable, FROM_UNIXTIME(m.time_stamp, '%T' ) AS TIME,m.time_stamp AS TT, 
    c.conversation_id AS conv_id, c.*, ms.is_replied 
     FROM $mail_conv_tbl AS c 
     INNER JOIN 

	(SELECT * FROM $mail_msg_tbl 
	 WHERE sender_id=$id AND IF (sender_id!=$id, STATUS='a', 1) ORDER BY time_stamp DESC 
	 ) AS m 
	  ON(m.conversation_id=c.conversation_id) 
	  
	  INNER JOIN 

	     ( 
	      SELECT recipient_id,conversation_id, IF(sender_id=$id,'yes','no') AS is_replied 
	      FROM $mail_msg_tbl 
	      WHERE (recipient_id=$id OR sender_id=$id) ORDER BY time_stamp DESC
	      ) AS 
	      ms ON ms.conversation_id=c.conversation_id
	      WHERE 
	      (c.initiator_id=$id OR interlocutor_id=$id) AND
	      c.bm_deleted NOT IN (IF (c.initiator_id=$id, '1, 3','2, 3')) AND IF (sender_id!=$id,STATUS='a', 1) 
	      GROUP BY c.conversation_id ORDER BY MAX( m.time_stamp ) DESC LIMIT $start,$limit) AS P 
	      
	      INNER JOIN $profile_table A ON P.recipient_id=A.profile_id
	      
	      LEFT JOIN $profile_pic_tbl B ON A.profile_id=B.profile_id AND B.number =0 
	      ORDER BY TT DESC";
$sqlT="SELECT sender_id,message_id,recipient_id,conversation_id,conversation_number,text,is_readable,Time,sex,username,subject,
CONCAT( '/', 'userfiles/thumb_', CAST( B.profile_id AS CHAR ) , '_', CAST( B.photo_id AS CHAR ) , '_', CAST( B.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
 FROM 
  
     (SELECT m.message_id, COUNT(m.message_id) AS MailCount,COUNT(m.conversation_id) AS conversation_number, m.sender_id, 
    m.recipient_id, m.is_kiss, m.text, m.is_readable, FROM_UNIXTIME(m.time_stamp, '%T' ) AS TIME,m.time_stamp AS TT, 
    c.conversation_id AS conv_id, c.*, ms.is_replied 
     FROM $mail_conv_tbl AS c 
     INNER JOIN 

	(SELECT * FROM $mail_msg_tbl 
	 WHERE sender_id=$id AND IF (sender_id!=$id, STATUS='a', 1) ORDER BY time_stamp DESC 
	 ) AS m 
	  ON(m.conversation_id=c.conversation_id) 
	  
	  INNER JOIN 

	     ( 
	      SELECT recipient_id,conversation_id, IF(sender_id=$id,'yes','no') AS is_replied 
	      FROM $mail_msg_tbl 
	      WHERE (recipient_id=$id OR sender_id=$id) ORDER BY time_stamp DESC
	      ) AS 
	      ms ON ms.conversation_id=c.conversation_id
	      WHERE 
	      (c.initiator_id=$id OR interlocutor_id=$id) AND
	      c.bm_deleted NOT IN (IF (c.initiator_id=$id, '1, 3','2, 3')) AND IF (sender_id!=$id,STATUS='a', 1) 
	      GROUP BY c.conversation_id ORDER BY MAX( m.time_stamp ) DESC) AS P 
	      
	      INNER JOIN $profile_table A ON P.recipient_id=A.profile_id
	      
	      LEFT JOIN $profile_pic_tbl B ON A.profile_id=B.profile_id AND B.number =0 
	      ORDER BY TT DESC";

 
 $db1->Query1($sqlT);
 $totalCount=$db1->RowCount();
/* $result1=@mysql_query($sqlT);
 echo "Total".$totalCount=@mysql_num_rows($result1);
*/ $res =  $db->Query($sql);
 while($row1 = mysql_fetch_array($res))
			{
			$text = $row1['text'];
			//echo $text1;
			//$text = trim($text1);
			if($row1['is_readable'] == 'no')
			{
			$text =	preg_replace("/<[^>]*>/","",$text); 
			$text =	str_replace(" ","",$text);
			
					if(($text =='[wink]4[/wink]') or ($text == '[smiles]58[/smiles]') )
					{
				  // echo "is_readable".$text;
					 $result[$i] =array('sender_id'=>$row1['sender_id'],'message_id'=>$row1['message_id'], 'recipient_id'=>$row1['recipient_id'],'conversation_id'=>$row1['conversation_id'], 'conversation_number'=>$row1['conversation_number'], 'text'=>$text, 'Time'=>$row1['Time'],'sex'=>$row1['sex'], 'username'=>$row1['username'], 'is_readable'=>$row1['is_readable'], 'subject'=>$row1['subject'], 'Profile_Pic'=>$row1['Profile_Pic']);
							$i=$i+1;
					}
			}
			else
			{
				
				$text = $row1['text'];
				//echo $text;
				$result[$i] =array('sender_id'=>$row1['sender_id'],'message_id'=>$row1['message_id'], 'recipient_id'=>$row1['recipient_id'],'conversation_id'=>$row1['conversation_id'], 'conversation_number'=>$row1['conversation_number'], 'text'=>$text, 'Time'=>$row1['Time'],'sex'=>$row1['sex'], 'username'=>$row1['username'], 'is_readable'=>$row1['is_readable'], 'subject'=>$row1['subject'], 'Profile_Pic'=>$row1['Profile_Pic']);
					$i=$i+1;
			}
		}
				$final = array();
		// Assigning to array Ends here
if (is_array($result))
{
	foreach($result as $array)
	{
		
			array_push($final, $array);
	}
}	if($i==0)
{
	$totalCount=0;
}
			//$final	=	'{"Status":"Live","Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON($final).']}'; 
			$final	=	'{"Status":"Live","Total rows":'.$totalCount.',"count": '.$i.',"result": '.json_encode($final).'}';
						$profile	=	str_replace('},]}','}]}',$final);
						echo $profile;
	}
			
	else
	{
		echo '{"Message":"Session Expired"}';
	}
}
				
				
	
	 /***************send mail with limit & membership starts here**********************/
	 /***************send mail with limit & membership starts here**********************/
	
	 /***************send mail with limit & membership starts here**********************/
	 /******************send mail with limit backup starts here****************/
	 public function SendMailByLimitBackup($id,$start,$limit)
	{	
		mysql_query('SET CHARACTER SET utf8'); 

		$essence			=	new Essentials();
		$profile_table		=	$essence->tblPrefix().'profile';
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$mail_conv_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$mail_msg_tbl		=	$essence->tblPrefix().'mailbox_message';
		$profile_pic_tbl	=	$essence->tblPrefix().'profile_photo';
				
		
      /* $sql="SELECT * FROM(select message_id,conversation_id,time_stamp,sender_id,recipient_id,text,subject,profile_id,username,sex,Time,conversation_number from 
(SELECT skadate_mailbox_message.message_id,skadate_mailbox_message.conversation_id,skadate_mailbox_message.time_stamp,skadate_mailbox_message.sender_id, skadate_mailbox_message.recipient_id,skadate_mailbox_message.text,skadate_mailbox_message.is_kiss,skadate_mailbox_message.is_readable,skadate_mailbox_message.status, skadate_mailbox_message.hash,skadate_mailbox_conversation.initiator_id,skadate_mailbox_conversation.interlocutor_id,skadate_mailbox_conversation.subject, skadate_mailbox_conversation.bm_read,skadate_mailbox_conversation.bm_deleted,skadate_mailbox_conversation.bm_read_special,skadate_mailbox_conversation.conversation_ts, skadate_mailbox_conversation.is_system, skadate_profile.profile_id,skadate_profile.email, skadate_profile.username, skadate_profile.password, skadate_profile.sex, skadate_profile.match_sex, skadate_profile.birthdate, skadate_profile.headline, skadate_profile.general_description, skadate_profile.match_agerange, skadate_profile.custom_location, skadate_profile.country_id, skadate_profile.zip, skadate_profile.state_id, skadate_profile.city_id, skadate_profile.join_stamp, skadate_profile.activity_stamp, skadate_profile.membership_type_id, skadate_profile.affiliate_id, skadate_profile.email_verified,skadate_profile.reviewed, skadate_profile.has_photo, skadate_profile.has_media, skadate_profile.featured, skadate_profile.register_invite_score,skadate_profile.rate_score,skadate_profile.rates,skadate_profile.language_id,skadate_profile.join_ip, skadate_profile.neigh_location, skadate_profile.neigh_location_distance, skadate_profile.bg_color, skadate_profile.bg_image, skadate_profile.bg_image_url, skadate_profile.bg_image_mode, skadate_profile.bg_image_status, skadate_profile.has_music, skadate_profile.is_private,
 FROM_UNIXTIME(time_stamp, '%T' ) AS Time 
FROM `skadate_mailbox_conversation` , `skadate_mailbox_message` , `skadate_profile`

 WHERE
 `skadate_mailbox_message`.sender_id =$id AND `skadate_mailbox_conversation`.bm_deleted !=1 AND `skadate_mailbox_message`.conversation_id = `skadate_mailbox_conversation`.conversation_id AND `skadate_profile`.profile_id =`skadate_mailbox_message`.recipient_id)X, 

 (select count( conversation_id ) as conversation_numberr,'0' as conversation_number,conversation_id as conversation_i from skadate_mailbox_message where recipient_id = $id or sender_id = $id group by conversation_i )Y 

where X.conversation_id=Y. conversation_i ) as A 

LEFT JOIN

(select *,CONCAT( '/','userfiles/thumb_', CAST( pictable .profile_id AS CHAR ) , '_',CAST( pictable .photo_id AS CHAR ) , '_',CAST( pictable .index AS CHAR ) , '.jpg' ) as Profile_Pic 
FROM `skadate_profile_photo` as pictable where `number` = 0) as  B

ON

A.profile_id = B.profile_id  ORDER BY time_stamp DESC";*/


/*$sql="SELECT * FROM(SELECT * FROM(SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS `Time`, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `sender_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE (`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id, `status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT 0,15) as P

JOIN

(SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year(
CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex
FROM $profile_table) AS Q ON P.recipient_id=Q.profile_id) AS A

LEFT JOIN

(SELECT $profile_pic_tbl.profile_id as prof_id,$profile_pic_tbl.photo_id,$profile_pic_tbl.index,$profile_pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_', CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $profile_pic_tbl  WHERE $profile_pic_tbl.number =0 )AS B

 ON A.profile_id=B.prof_id";
 
*/
	
	
		$sql="SELECT sender_id,message_id,recipient_id,conversation_id,conversation_number,text,Time,sex,username,subject,Profile_Pic FROM(SELECT * FROM(SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS `Time`, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `sender_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE 
(`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND  `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id,`status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC LIMIT $start,$limit) as P
JOIN
(SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year(
CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex
FROM $profile_table) AS Q ON P.recipient_id=Q.profile_id) AS A
LEFT JOIN
(SELECT $profile_pic_tbl.profile_id as prof_id,$profile_pic_tbl.photo_id,$profile_pic_tbl.index,$profile_pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_', CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $profile_pic_tbl  WHERE $profile_pic_tbl.number =0 )AS B
 ON A.profile_id=B.prof_id";
 
	//for counting total rows
		$sqlT="SELECT sender_id,message_id,recipient_id,conversation_id,conversation_number,text,Time,sex,username,subject,Profile_Pic FROM(SELECT * FROM(SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `MailCount`,COUNT(`m`.`conversation_id`) AS `conversation_number`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`, `m`.`text`, `m`.`is_readable`, FROM_UNIXTIME(`m`.`time_stamp`, '%T' ) AS `Time`, `c`.`conversation_id` as conv_id, `c`.*, `ms`.`is_replied` FROM `$mail_conv_tbl` AS `c` INNER JOIN ( SELECT * FROM `$mail_msg_tbl` WHERE `sender_id`=$id AND IF (`sender_id`!=$id, `status`='a', 1) ORDER BY `time_stamp` DESC ) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) INNER JOIN ( SELECT `conversation_id`, IF(`sender_id`=$id,'yes','no') AS `is_replied` FROM `$mail_msg_tbl` WHERE (`recipient_id`=$id OR `sender_id`=$id) ORDER BY `time_stamp` DESC ) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`) WHERE 
(`c`.`initiator_id`=$id OR `interlocutor_id`=$id) AND  `c`.`bm_deleted` NOT IN (IF (`c`.`initiator_id`=$id, '1, 3','2, 3')) AND IF (`sender_id`!=$id,`status`='a', 1) GROUP BY `c`.`conversation_id` ORDER BY MAX( `m`.`time_stamp` ) DESC) as P
JOIN
(SELECT $profile_table.profile_id, $profile_table.username, $profile_table.birthdate, year(
CURRENT_TIMESTAMP ) - year( $profile_table.birthdate ) AS DOB, $profile_table.sex
FROM $profile_table) AS Q ON P.recipient_id=Q.profile_id) AS A
LEFT JOIN
(SELECT $profile_pic_tbl.profile_id as prof_id,$profile_pic_tbl.photo_id,$profile_pic_tbl.index,$profile_pic_tbl.number, CONCAT( '/', 'userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_', CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic FROM $profile_pic_tbl  WHERE $profile_pic_tbl.number =0 )AS B
 ON A.profile_id=B.prof_id";
 
 $db->Query($sqlT);
 $totalCount=$db->RowCount();
	if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$profile = '{"Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo $profile = str_replace("},]", "}]", $profile);
				}
				else
				{
					echo '{"count":"0","Message":"Incorrect ID"}';
				}	
			}
	}
	 	 /***************send mail with limit back up starts here**********************/
public function ConversationDetails($id,$pid,$skey)
		{
		//mysql_query('SET CHARACTER SET utf8'); 

			
		$essence			=	new Essentials();
		$secure             =   new secure();
		$profile_table		=	$essence->tblPrefix().'profile';
		$pic_tbl			=	$essence->tblPrefix().'profile_photo';
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$mail_conv_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$mail_msg_tbl		=	$essence->tblPrefix().'mailbox_message';
		$membership_limit	=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
		
		//check user authentication
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
									
									$sql				=		"select $mail_msg_tbl.conversation_id,sender_id,message_id,recipient_id,text,subject,username,sex,
									CONCAT( '/','userfiles/thumb_', CAST( pictable .profile_id AS CHAR ) , '_',
									CAST( pictable .photo_id AS CHAR ) , '_', CAST( pictable .index AS CHAR ) , '.jpg' ) as Profile_Pic,
									time_stamp as Time from `$mail_msg_tbl`
									JOIN `$mail_conv_tbl` ON (`$mail_msg_tbl`.conversation_id =$id AND `$mail_conv_tbl`.conversation_id=$id)
									LEFT JOIN `$pic_tbl` as pictable ON (`$mail_msg_tbl`.`sender_id` = `pictable`.`profile_id` AND `pictable`.`number`=0) 
									LEFT JOIN `$profile_table` ON ($profile_table.profile_id=$mail_msg_tbl.sender_id)";
									//echo $sql;
                                                                        $res = $db->Query($sql);
 $i=0;
 while($row1=mysql_fetch_array( $res))
 {
     $dateNew=date("Y-m-d  H:i:s",$row1['Time']);
  $result[$i] =array('conversation_id'=>$row1['conversation_id'],'sender_id'=>$row1['sender_id'],'message_id'=>$row1['message_id'], 'recipient_id'=>$row1['recipient_id'],'text'=>$row1['text'],'subject'=>$row1['subject'],'username'=>$row1['username'],'sex'=>$row1['sex'],'Profile_Pic'=>$row1['Profile_Pic'],'Time'=>$dateNew);
$i++;							
}
 $final = array();
	// Assigning to array Ends here
		if (is_array($result))
		{
		foreach($result as $array)
		{
		 
		array_push($final, $array);
		}
		}
	$final = '{"Status":"Live","count": '.$i.',"result": '.json_encode($final).'}';
	echo str_replace("},]","}]",$final);
$sql1 =	"UPDATE `$mail_conv_tbl` SET `bm_read`=3 ,`bm_read_special`=3 WHERE `conversation_id`=$id";
$db1->Query($sql1);
                
                
		
                                                                        
                        
              }
		else
		{
			echo '{"Message":"Session Expired"}';
		}
$db->Close();
	}
/**************************/
		public function ConversationDetailsOldf($id,$pid,$skey)
		{
		mysql_query('SET CHARACTER SET utf8'); 

			
		$essence			=	new Essentials();
		$secure             =   new secure();
		$profile_table		=	$essence->tblPrefix().'profile';
		$pic_tbl			=	$essence->tblPrefix().'profile_photo';
		$db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$mail_conv_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$mail_msg_tbl		=	$essence->tblPrefix().'mailbox_message';
		$membership_limit	=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
		
		//check user authentication
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
									
									

$sql="SELECT msg.conversation_id,sender_id,message_id,recipient_id,text,subject,username,sex, 
CONCAT( '/','userfiles/thumb_', CAST( pictable .profile_id AS CHAR ) , '_', CAST( pictable .photo_id AS CHAR ) , '_', CAST( pictable .INDEX AS CHAR )
 , '.jpg' ) AS Profile_Pic, FROM_UNIXTIME(time_stamp,'%d %b %Y %T:%f') AS Time 
 FROM $mail_msg_tbl msg
 JOIN $mail_conv_tbl con ON msg.conversation_id=con.conversation_id AND msg.conversation_id=$id
 LEFT JOIN $pic_tbl AS pictable ON msg.sender_id = pictable.profile_id AND pictable.number=0
 LEFT JOIN $profile_table sp ON msg.sender_id=sp.profile_id";
									//echo $sql;
		if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					
					$profile = '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					$profile	=	str_replace('},]}','}]}',$profile);
					echo $profile;
					$sql1 =	"UPDATE `$mail_conv_tbl` SET `bm_read`=3 ,`bm_read_special`=3 WHERE `conversation_id`=$id";
				    $db1->Query($sql1);
				}
				else
				{
					echo '{"Status":"Live","count":"0","Message":"Incorrect ID"}';
				}	
			}
		
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}
	}
		/*````````````````````````````````````````````````````````````````````````````````````````````````````````*/
		public function ConversationDetailsTemp($id)
		{	
		mysql_query('SET CHARACTER SET utf8'); 
		$essence			=	new Essentials();
		$profile_table		=	$essence->tblPrefix().'profile';
		$pic_tbl			=	$essence->tblPrefix().'profile_photo';
		$db 				= 	new MySQL(true, $esࡳence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$mail_conv_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$mail_msg_tbl		=	$essence->tblPrefix().'mailbox_message';
				
		 $sql				=		"select *,CONCAT( '/','userfiles/thumb_', CAST( pictable .profile_id AS CHAR ) , '_',
CAST( pictable .photo_id AS CHAR ) , '_',
CAST( pictable .index AS CHAR ) , '.jpg' ) as Profile_Pic,FROM_UNIXTIME(time_stamp,'%T') as Time from `$mail_msg_tbl`
 JOIN `$mail_conv_tbl` ON (`$mail_msg_tbl`.conversation_id =$id AND `$mail_conv_tbl`.conversation_id=$id)
 LEFT JOIN `$pic_tbl` as pictable ON (`$mail_msg_tbl`.`recipient_id` = `pictable`.`profile_id` AND `pictable`.`number`=0) 
LEFT JOIN `$profile_table` ON (`$mail_msg_tbl`.recipient_id=`$profile_table`.profile_id)";
			
		if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$url		=	$this->getThumbImage($id);
					if($url	==	"")
						$url	=	"NULL";
					$profile = '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					$profile	=	str_replace('},]}','}]}',$profile);
					echo $profile;
				}
				else
				{
					echo '{"count":"0","Message":"Incorrect ID"}';
				}	
			}
		}
	
	/** ------------------------------------------------------------------ */
	 	public function getMessageDetails($id,$pid,$skey)
		{
			mysql_query('SET CHARACTER SET utf8'); 

			$essence = new Essentials();
			$secure     =   new secure();
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			$profile_table = $essence->tblPrefix().'profile';
			$mail_conv_tbl = $essence->tblPrefix().'mailbox_conversation';
			$mail_msg_tbl = $essence->tblPrefix().'mailbox_message';
			$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
			$membership_srv			=	$essence->tblPrefix().'link_membership_service_limit';
			
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			//$res=1;
			if($res==1)
			{	
				
			if (!$db->Error())
			{
				//$mail_conv_tbl = $essence->tblPrefix().'mailbox_conversation';
				//$mail_msg_tbl = $essence->tblPrefix().'mailbox_message';
				/*echo $sql1 = " SELECT *,FROM_UNIXTIME(time_stamp,'%d %b %Y %T:%f') as DateTime FROM `$mail_msg_tbl`,`$mail_conv_tbl`
				WHERE `$mail_msg_tbl`.message_id =$id AND `$mail_msg_tbl`.sender_id =`$mail_conv_tbl`.initiator_id AND `$mail_msg_tbl`.recipient_id=`$mail_conv_tbl`.interlocutor_id AND `$mail_msg_tbl`.conversation_id =`$mail_conv_tbl`.conversation_id";*/
				
				 $sql = "SELECT *,FROM_UNIXTIME(time_stamp,'%c-%d-%Y %H:%i:%s') as DateTime FROM `$mail_msg_tbl`,`$mail_conv_tbl` 
                 WHERE `$mail_msg_tbl`.message_id =$id AND  `$mail_msg_tbl`.conversation_id =`$mail_conv_tbl`.conversation_id";
                 $result=$db1->Query($sql);
				 $rslt=@mysql_fetch_array($result);
				  $mid=$rslt['message_id'];
				  $cid=$rslt['conversation_id'];
				  $sid=$rslt['sender_id'];
				  $rid=$rslt['recipient_id'];
				
				$qry="select recipient_id from `$mail_msg_tbl` where `$mail_msg_tbl`.message_id=$id";
				$pi=@mysql_query($qry);
				
				$row=@mysql_fetch_array($pi);
				$pid=$row['recipient_id'];
				//echo "pid=".$pid;
				$pid=$rid;
				$sqll="select username, sex from `$profile_table` where profile_id='$pid'";
				$rst=$db1->Query($sqll);
				$row1=@mysql_fetch_array($rst);
				$uname=$row1['username'];
				$sex=$row1['sex'];
				
	
				if ($db->Query1($sql))
				{
					if($db->RowCount())
					{
						$url = $this->getThumbImage($pid);
						if($url == "")
						$url = "NULL";
						$profile = '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						$profile = str_replace('},]}',',"username":"'.$uname.'","sex":"'.$sex.'","Profile_Image":"'.$url.'"}',$profile);
						$profile = str_replace("}", "}]}", $profile);
						echo $profile;
						
						/////////////
						//"SELECT COUNT(conversation_id) AS cnt FROM $mailbox_conversation_tbl where interlocutor_id=$pid AND bm_read=1";
						 $sql1 =	"UPDATE `$mail_conv_tbl` SET `bm_read`=1 ,`bm_read_special`=1 WHERE `interlocutor_id`=$rid AND `initiator_id`=$sid AND `conversation_id`=$cid";
					     $db1->Query1($sql1);
						/////////////	
					}
					else
					{
					echo '{"Status":"Live","Message":"Incorrect ID"}';
					}
				
				}
			}
			else
			{
				echo '{"Status":"Live","Message":"Error connecting to database. Check configuration"}';
				$db->Kill();
			}
	}
		else //session expired
			{
				echo '{"Message":"Session Expired"}';
	        }	
		}
		/** ------------------------------------------------------------------ */
public function getMessageDetailsInverse($id, $pid, $skey) { 
        //mysql_query('SET CHARACTER SET utf8'); 

        $essence = new Essentials(); 
        $secure = new secure(); 
        $htmlString = new htmltostring(); 
        $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        $db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        $profile_table = $essence->tblPrefix() . 'profile'; 
        $pic_tbl = $essence->tblPrefix() . 'profile_photo'; 
        $membership_limit = $essence->tblPrefix() . 'link_membership_type_service'; 
        $membership_srv = $essence->tblPrefix() . 'link_membership_service_limit'; 
        $key = $essence->tblPrefix() . 'lang_key'; 
        $value = $essence->tblPrefix() . 'lang_value'; 
        $newid = $pid; 
        //check user sign in or not 
        $res = $secure->CheckSecure($pid, $skey); 
        //$res=1; 
        if ($res == 1) { 
            $i = 1; 
            if (!$db->Error()) { 
                $mail_conv_tbl = $essence->tblPrefix() . 'mailbox_conversation'; 
                $mail_msg_tbl = $essence->tblPrefix() . 'mailbox_message'; 
                $sql = "SELECT p.username,c.subject,m.text,m.message_id as message_id,p.sex,m.sender_id,m.recipient_id,m.conversation_id,is_readable,time_stamp as DateTime1,CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
			CAST( ptbl.photo_id AS CHAR ) , '_', 
			CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Image FROM $profile_table p 
			JOIN $mail_msg_tbl as m ON (m.message_id =" . $id . " AND m.recipient_id=p.profile_id) 
			JOIN $mail_conv_tbl as c ON (m.conversation_id =c.conversation_id) 
			LEFT JOIN `$pic_tbl` as ptbl ON (m.`recipient_id` = `ptbl`.`profile_id` AND `ptbl`.`number`=0 and `ptbl`.status='active')"; 

                $result = $db->Query1($sql); 
                $rslt = mysql_fetch_array($result); 
                $mid = $rslt['message_id']; 
                $cid = $rslt['conversation_id']; 
                $sid = $rslt['sender_id']; 
                $rid = $rslt['recipient_id']; 
                $dateNew = date('m-d-Y H:i:s', $rslt['DateTime1']); 
                // $text =$rslt['text']; 
                $pid = $db->Query1("select sender_id from $mail_msg_tbl where $mail_msg_tbl.message_id=" . $id); 
                if (mysql_num_rows($pid) != 0) { 
                    //echo "<br/>I am inside pid"; 
                    $row = mysql_fetch_array($pid); 
                    //echo "<br/>row=".$row; 
                    $pid = $row['sender_id']; 
                    $rst = $db->Query1("select username, sex from $profile_table where $profile_table.profile_id=" . $sid); 
                    //echo "<br/>rst=".$rst; 
                    $row1 = mysql_fetch_array($rst); 
                    //echo "<br/>row1".$row1; 
                    $uname = $row1['username']; 
                    $sex = $row1['sex']; 
                    if ($sid == 0) { 
                        $uname = "System Message"; 
                    } 
                } else { 
                    echo '{"Status":"Live","Message":"Incorrect ID"}'; 
                    exit(0); 
                } 
                //echo "Brrrrr".$sql; 
                $res = $db->Query($sql); 
                while ($row12 = mysql_fetch_array($res)) { 
                    $url = $this->getThumbImage($pid); 
                    $url = str_replace('\\/', '/', $url); 
                    if ($url == "") 
                        $url = "NULL"; 
                    //$profile = '{"count": '.$db->RowCount().',"result": '.$db->GetJSON().'}'; 
                    $result = array('username' => $uname, 'subject' => $row12['subject'], 'text' => $row12['text'], 'message_id' => $mid, 'sex' => $row12['sex'], 'sender_id' => $sid, 'recipient_id' => $rid, 'conversation_id' => $cid, 'is_readable' => $row12['is_readable'], 'DateTime' => $dateNew, 'Profile_Image' => $url); 
                    //$i=$i+1; 
                } 
                $final = $result; 
                $final = '{"Status":"Live","count": ' . $i . ',"result": [' . json_encode($final) . ']}'; 
                echo str_replace("},]", "}]", $final); 
                $sql1 = "UPDATE `$mail_conv_tbl` SET `bm_read`=3 ,`bm_read_special`=3 WHERE interlocutor_id=" . $rid . " AND initiator_id=" . $sid . " AND conversation_id=" . $cid; 
                $db1->Query1($sql1); 
            } 
            else { 
                echo '{"Status":"Live","Message":"Error connecting to database. Check configuration"}'; 
                $db->Kill(); 
            } 
        } else { //session expired 
            echo '{"Message":"Session Expired"}'; 
        } 
    } 



		public function getMessageDetailsInverseOLDBAK($id,$pid,$skey)
		{
		//mysql_query('SET CHARACTER SET utf8'); 
		
		$essence 		= new Essentials();
		$secure     	=   new secure();
		$htmlString 	= new htmltostring();
		$db 			= new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db1 			= new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$profile_table 	= $essence->tblPrefix().'profile';
		$pic_tbl 		= $essence->tblPrefix().'profile_photo';
		$membership_limit = $essence->tblPrefix().'link_membership_type_service';
		$membership_srv = $essence->tblPrefix().'link_membership_service_limit';
		$key            =    $essence->tblPrefix().'lang_key';
		$value  		=    $essence->tblPrefix().'lang_value';
		$newid			=$pid;
		//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		  	{
			$i=1;
			if (!$db->Error())
			{
			$mail_conv_tbl = $essence->tblPrefix().'mailbox_conversation';
			$mail_msg_tbl = $essence->tblPrefix().'mailbox_message';
			
$sql="SELECT p.username,c.subject,m.text,m.message_id AS message_id,p.sex,m.sender_id,m.recipient_id,m.conversation_id,is_readable,
FROM_UNIXTIME(time_stamp,'%m-%d-%Y %T:%f') AS DateTime,CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
CAST( ptbl.photo_id AS CHAR ) , '_', CAST( ptbl.index AS CHAR ) , '.jpg' ) AS Profile_Image
 FROM $profile_table p 
 JOIN $mail_msg_tbl m ON  m.recipient_id=p.profile_id AND m.message_id =$id
 JOIN $mail_conv_tbl c ON m.conversation_id =c.conversation_id
 LEFT JOIN $pic_tbl ptbl ON m.recipient_id = ptbl.profile_id AND ptbl.number=0";

			$result=$db->Query1($sql);
			$rslt=mysql_fetch_array($result);
			$mid=$rslt['message_id'];
			$cid=$rslt['conversation_id'];
			$sid=$rslt['sender_id'];
			$rid=$rslt['recipient_id'];
			// $text =$rslt['text'];
			$pid=$db->Query1("select sender_id from $mail_msg_tbl where $mail_msg_tbl.message_id=".$id);
			if(mysql_num_rows($pid)!=0)
			{
			//echo "<br/>I am inside pid";
			$row=mysql_fetch_array($pid);
			//echo "<br/>row=".$row;
			$pid=$row['sender_id'];
			$rst=$db->Query1("select username, sex from $profile_table where $profile_table.profile_id=".$sid);
			//echo "<br/>rst=".$rst;
			$row1=mysql_fetch_array($rst);
			//echo "<br/>row1".$row1;
			$uname=$row1['username'];
			$sex=$row1['sex'];
			if($sid == 0)
			{
			$uname ="System Message";
			}
			}
			else
			{
			echo '{"Status":"Live","Message":"Incorrect ID"}';
			exit(0);
			}
			//echo "Brrrrr".$sql;
			$res= $db->Query($sql);
			while($row1 = mysql_fetch_array($res))
			{
			$url = $this->getThumbImage($pid);
			$url=str_replace('\\/','/',$url);
			if($url == "")
			$url = "NULL";
			//$profile = '{"count": '.$db->RowCount().',"result": '.$db->GetJSON().'}';
			$result =array('username'=>$uname,'subject'=>$row1['subject'],'text'=>$row1['text'],'message_id'=>$mid,'sex'=>$row1['sex'],'sender_id'=> $sid,'recipient_id'=>$rid,'conversation_id'=>$cid,'is_readable'=>$row1['is_readable'], 'DateTime'=>$row1['DateTime'], 'Profile_Image'=>$url);
			//$i=$i+1;
			}
			$final = $result;
			$final = '{"Status":"Live","count": '.$i.',"result": ['.json_encode($final).']}';
			echo str_replace("},]","}]",$final);
			$sql1 = "UPDATE `$mail_conv_tbl` SET `bm_read`=3 ,`bm_read_special`=3 WHERE interlocutor_id=".$rid." AND initiator_id=".$sid." AND conversation_id=".$cid;
			$db1->Query1($sql1);
			}
			else
			{
			echo '{"Status":"Live","Message":"Error connecting to database. Check configuration"}';
			$db->Kill();
			}
			}
			else //session expired
			{
			echo '{"Message":"Session Expired"}';
		}
	}


 /** ------------------------------------------------------------------ */
	public function getMyWatches($id,$skey)
	{	
		mysql_query('SET CHARACTER SET utf8'); 
		$essence			 =  new Essentials();
		$secure     =   new secure();
		$profile			 =  $essence->tblPrefix().'profile';
		$profile_view		 =	$essence->tblPrefix().'profile_view_history';
		$profile_pic_tbl	 =	$essence->tblPrefix().'profile_photo';
	    $location_table 	 =	$essence->tblPrefix().'location_country';
		$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
		
      /*$sql1 = "SELECT *, year(CURRENT_TIMESTAMP ) - year( birthdate ) AS DOB,CONCAT( '/','userfiles/thumb_', CAST( skadate_profile_photo.profile_id AS CHAR ) , '_',
CAST( skadate_profile_photo.photo_id AS CHAR ) , '_',
CAST( skadate_profile_photo.index AS CHAR ) , '.jpg' ) as Profile_Pic
						FROM skadate_profile
						JOIN skadate_profile_view_history ON ( skadate_profile.`profile_id` = skadate_profile_view_history.`profile_id` 
						AND skadate_profile_view_history.`profile_id` =$id ) 
						JOIN skadate_profile_photo ON ( skadate_profile.`profile_id` = skadate_profile_photo.`profile_id` 
						AND skadate_profile_photo.number =0)";*/
						
/* $sql1 ="SELECT * FROM (SELECT * FROM (SELECT `p`.*,year(CURRENT_TIMESTAMP ) - year(`p`.`birthdate`) AS DOB, `pvh`.`view_count`, `pvh`.`view_time`, `pvh`.`time_stamp` FROM `$profile` AS `p` 

RIGHT JOIN (SELECT `profile_id`, COUNT(`profile_id`) AS `view_count`,`time_stamp`, MAX(time_stamp) view_time FROM `$profile_view` WHERE `viewed_id`=$id GROUP BY `profile_id`) AS `pvh` USING(`profile_id`) 

ORDER BY `pvh`.`time_stamp` DESC) as A LEFT JOIN (SELECT profile_id as prof_id,CONCAT( '/','userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_',CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $profile_pic_tbl where $profile_pic_tbl.number =0) as B ON A.profile_id=B.prof_id) AS TAB1

LEFT JOIN

(SELECT * FROM `$location_table`) AS TAB2 ON TAB1.country_id=TAB2.Country_str_code";
			

		*/ $sql1 ="SELECT profile_id,username,sex,DOB,custom_location,country_str_name,Profile_Pic FROM (SELECT * FROM (SELECT `p`.*,year(CURRENT_TIMESTAMP ) - year(`p`.`birthdate`) AS DOB, `pvh`.`view_count`, `pvh`.`view_time`, `pvh`.`time_stamp` FROM `$profile` AS `p` 

RIGHT JOIN (SELECT `profile_id`, COUNT(`profile_id`) AS `view_count`,`time_stamp`, MAX(time_stamp) view_time FROM `$profile_view` WHERE `viewed_id`=$id and viewed_id!=profile_id GROUP BY `profile_id`) AS `pvh` USING(`profile_id`) 

ORDER BY `pvh`.`time_stamp` DESC) as A LEFT JOIN (SELECT profile_id as prof_id,CONCAT( '/','userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_',CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $profile_pic_tbl where $profile_pic_tbl.number =0) as B ON A.profile_id=B.prof_id) AS TAB1

LEFT JOIN

(SELECT * FROM `$location_table`) AS TAB2 ON TAB1.country_id=TAB2.Country_str_code where profile_id!=0 or  profile_id!=NULL and profile_id!='$id'";
			
						
		if($db->Query($sql1))
		{	
			if($db->RowCount())
			{	
				$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
				echo str_replace("},]","}]",$stri);
			}
			else
			{
				echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
			
			}
		}
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
		
	}
 /** -----------------------new API for my watches by limit starts here-------------- */
	 /** -----------------------new API for my watches by limit starts here-------------- */ 
    public function getMyWatchesByLimit($id, $skey, $start, $limit) { 
        mysql_query('SET CHARACTER SET utf8'); 
        $essence = new Essentials(); 
        $secure = new secure(); 
        $profile = $essence->tblPrefix() . 'profile'; 
        $profile_view = $essence->tblPrefix() . 'profile_view_history'; 
        $profile_pic_tbl = $essence->tblPrefix() . 'profile_photo'; 
        $location_table = $essence->tblPrefix() . 'location_country'; 
        $location_state = $essence->tblPrefix() . 'location_state'; 
        $location_city = $essence->tblPrefix() . 'location_city'; 

        $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 

        $res = $secure->CheckSecure($id, $skey); 
        //$res=1; 
        if ($res == 1) { 
            /* $sql1 = "SELECT *, year(CURRENT_TIMESTAMP ) - year( birthdate ) AS DOB,CONCAT( '/','userfiles/thumb_', CAST( skadate_profile_photo.profile_id AS CHAR ) , '_', 
              CAST( skadate_profile_photo.photo_id AS CHAR ) , '_', 
              CAST( skadate_profile_photo.index AS CHAR ) , '.jpg' ) as Profile_Pic 
              FROM skadate_profile 
              JOIN skadate_profile_view_history ON ( skadate_profile.`profile_id` = skadate_profile_view_history.`profile_id` 
              AND skadate_profile_view_history.`profile_id` =$id ) 
              JOIN skadate_profile_photo ON ( skadate_profile.`profile_id` = skadate_profile_photo.`profile_id` 
              AND skadate_profile_photo.number =0)"; */ 

            /* $sql1 ="SELECT * FROM (SELECT * FROM (SELECT `p`.*,year(CURRENT_TIMESTAMP ) - year(`p`.`birthdate`) AS DOB, `pvh`.`view_count`, `pvh`.`view_time`, `pvh`.`time_stamp` FROM `$profile` AS `p` 

              RIGHT JOIN (SELECT `profile_id`, COUNT(`profile_id`) AS `view_count`,`time_stamp`, MAX(time_stamp) view_time FROM `$profile_view` WHERE `viewed_id`=$id GROUP BY `profile_id`) AS `pvh` USING(`profile_id`) 

              ORDER BY `pvh`.`time_stamp` DESC) as A LEFT JOIN (SELECT profile_id as prof_id,CONCAT( '/','userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_',CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $profile_pic_tbl where $profile_pic_tbl.number =0) as B ON A.profile_id=B.prof_id) AS TAB1 

              LEFT JOIN 

              (SELECT * FROM `$location_table`) AS TAB2 ON TAB1.country_id=TAB2.Country_str_code"; 


             */ $sql1 = "SELECT DISTINCT profile_id,username,sex,DOB,CONCAT(Admin1_str_name,',',Feature_str_name) AS custom_location,country_str_name,Profile_Pic FROM (SELECT * FROM (SELECT `p`.*,year(CURRENT_TIMESTAMP ) - year(`p`.`birthdate`) AS DOB, `pvh`.`view_count`, `pvh`.`view_time`, `pvh`.`time_stamp` FROM `$profile` AS `p` 

RIGHT JOIN (SELECT `profile_id`, COUNT(`profile_id`) AS `view_count`,`time_stamp`, MAX(time_stamp) view_time FROM `$profile_view` WHERE `viewed_id`=$id and viewed_id!=profile_id GROUP BY `profile_id`) AS `pvh` USING(`profile_id`) 

ORDER BY `pvh`.`time_stamp` DESC LIMIT $start,$limit) as A LEFT JOIN (SELECT profile_id as prof_id,CONCAT( '/','userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_',CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $profile_pic_tbl where $profile_pic_tbl.number =0 and $profile_pic_tbl.status='active') as B ON A.profile_id=B.prof_id) AS TAB1 

LEFT JOIN `$location_state` AS `state` ON (`state_id`= `state`.`Admin1_str_code`) 
					LEFT JOIN `$location_city` AS `city` ON (`city_id`= `city`.`Feature_int_id`) 

LEFT JOIN 

(SELECT * FROM `$location_table`) AS TAB2 ON TAB1.country_id=TAB2.Country_str_code where profile_id!=0 or  profile_id!=NULL and profile_id!='$id'"; 


            $sqlT = "SELECT DISTINCT profile_id,username,sex,DOB,custom_location,country_str_name,Profile_Pic FROM (SELECT * FROM (SELECT `p`.*,year(CURRENT_TIMESTAMP ) - year(`p`.`birthdate`) AS DOB, `pvh`.`view_count`, `pvh`.`view_time`, `pvh`.`time_stamp` FROM `$profile` AS `p` 
 
RIGHT JOIN (SELECT `profile_id`, COUNT(`profile_id`) AS `view_count`,`time_stamp`, MAX(time_stamp) view_time FROM `$profile_view` WHERE `viewed_id`=$id and viewed_id!=profile_id GROUP BY `profile_id`) AS `pvh` USING(`profile_id`) 

ORDER BY `pvh`.`time_stamp`) as A LEFT JOIN (SELECT profile_id as prof_id,CONCAT( '/','userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_',CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $profile_pic_tbl where $profile_pic_tbl.number =0 and $profile_pic_tbl.status='active') as B ON A.profile_id=B.prof_id) AS TAB1 


LEFT JOIN 

(SELECT * FROM `$location_table`) AS TAB2 ON TAB1.country_id=TAB2.Country_str_code where profile_id!=0 or  profile_id!=NULL and profile_id!='$id'"; 

            $db->Query($sqlT); 
            $totalCount = $db->RowCount(); 
            if ($db->Query($sql1)) { 
                if ($db->RowCount()) { 
                    $profile = '{"Status":"Live","Total rows":' . $totalCount . ',"count": ' . $db->RowCount() . ',"result": [' . $db->GetJSON() . ']}'; 
                    echo $profile = str_replace("},]", "}]", $profile); 
                } else { 
                    echo '{"Status":"Live","Total rows":"0","count":"0","Message":"Incorrect ID"}'; 
                } 
            } 
        } else { 
            echo '{"Message":"Session Expired"}'; 
        } 
    } 
	 
	public function getMyWatchesByLimitOLD($id,$skey,$start,$limit)
	{	
		mysql_query('SET CHARACTER SET utf8'); 
		$essence			 =  new Essentials();
		$secure     =   new secure();
		$profile			 =  $essence->tblPrefix().'profile';
		$profile_view		 =	$essence->tblPrefix().'profile_view_history';
		$profile_pic_tbl	 =	$essence->tblPrefix().'profile_photo';
	    $location_table 	 =	$essence->tblPrefix().'location_country';
		
		$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
      /*$sql1 = "SELECT *, year(CURRENT_TIMESTAMP ) - year( birthdate ) AS DOB,CONCAT( '/','userfiles/thumb_', CAST( skadate_profile_photo.profile_id AS CHAR ) , '_',
CAST( skadate_profile_photo.photo_id AS CHAR ) , '_',
CAST( skadate_profile_photo.index AS CHAR ) , '.jpg' ) as Profile_Pic
						FROM skadate_profile
						JOIN skadate_profile_view_history ON ( skadate_profile.`profile_id` = skadate_profile_view_history.`profile_id` 
						AND skadate_profile_view_history.`profile_id` =$id ) 
						JOIN skadate_profile_photo ON ( skadate_profile.`profile_id` = skadate_profile_photo.`profile_id` 
						AND skadate_profile_photo.number =0)";*/
						
/* $sql1 ="SELECT * FROM (SELECT * FROM (SELECT `p`.*,year(CURRENT_TIMESTAMP ) - year(`p`.`birthdate`) AS DOB, `pvh`.`view_count`, `pvh`.`view_time`, `pvh`.`time_stamp` FROM `$profile` AS `p` 

RIGHT JOIN (SELECT `profile_id`, COUNT(`profile_id`) AS `view_count`,`time_stamp`, MAX(time_stamp) view_time FROM `$profile_view` WHERE `viewed_id`=$id GROUP BY `profile_id`) AS `pvh` USING(`profile_id`) 

ORDER BY `pvh`.`time_stamp` DESC) as A LEFT JOIN (SELECT profile_id as prof_id,CONCAT( '/','userfiles/thumb_', CAST( $profile_pic_tbl.profile_id AS CHAR ) , '_', CAST( $profile_pic_tbl.photo_id AS CHAR ) , '_',CAST( $profile_pic_tbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $profile_pic_tbl where $profile_pic_tbl.number =0) as B ON A.profile_id=B.prof_id) AS TAB1

LEFT JOIN

(SELECT * FROM `$location_table`) AS TAB2 ON TAB1.country_id=TAB2.Country_str_code";
			

		*/ 

$sql1="SELECT A.profile_id,username,sex,DOB,custom_location,country_str_name
,CONCAT( '/','userfiles/thumb_', CAST( pp.profile_id AS CHAR ) ,'_',CAST( pp.photo_id AS CHAR ) , '_',CAST( pp.index AS CHAR ) , '.jpg') AS Profile_Pic

FROM 
(
	SELECT p.*,YEAR(CURRENT_TIMESTAMP ) - YEAR(p.birthdate) AS DOB, pvh.view_count, pvh.view_time, pvh.time_stamp 
	FROM $profile AS p
	RIGHT JOIN 
	(
	SELECT profile_id, COUNT(profile_id) AS view_count,time_stamp, MAX(time_stamp) view_time 
	FROM $profile_view
	WHERE viewed_id=$id AND viewed_id!=profile_id 
	GROUP BY profile_id
	) AS pvh ON p.profile_id = pvh.profile_id
	ORDER BY pvh.time_stamp DESC LIMIT $start,$limit
) A
LEFT JOIN $profile_pic_tbl pp ON A.profile_id=pp.profile_id AND pp.number =0
LEFT JOIN $location_table lc ON A.country_id=lc.Country_str_code
WHERE A.profile_id!=0 OR A.profile_id!=NULL AND A.profile_id!='$id'";


$sqlT = "SELECT A.profile_id,username,sex,DOB,custom_location,country_str_name
,CONCAT( '/','userfiles/thumb_', CAST( pp.profile_id AS CHAR ) ,'_',CAST( pp.photo_id AS CHAR ) , '_',CAST( pp.index AS CHAR ) , '.jpg') AS Profile_Pic

FROM 
(
	SELECT p.*,YEAR(CURRENT_TIMESTAMP ) - YEAR(p.birthdate) AS DOB, pvh.view_count, pvh.view_time, pvh.time_stamp 
	FROM $profile AS p
	RIGHT JOIN 
	(
	SELECT profile_id, COUNT(profile_id) AS view_count,time_stamp, MAX(time_stamp) view_time 
	FROM $profile_view
	WHERE viewed_id=$id AND viewed_id!=profile_id 
	GROUP BY profile_id
	) AS pvh ON p.profile_id = pvh.profile_id
	ORDER BY pvh.time_stamp DESC
) A
LEFT JOIN $profile_pic_tbl pp ON A.profile_id=pp.profile_id AND pp.number =0
LEFT JOIN $location_table lc ON A.country_id=lc.Country_str_code
WHERE A.profile_id!=0 OR A.profile_id!=NULL AND A.profile_id!='$id'";

 $db->Query($sqlT);
 $totalCount=$db->RowCount();
	if($db->Query($sql1))
			{
				if($db->RowCount())
				{	
					$profile = '{"Status":"Live","Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo $profile = str_replace("},]", "}]", $profile);
				}
				else
				{
					echo '{"Status":"Live","Total rows":"0","count":"0","Message":"Incorrect ID"}';
				}	
			}
			}
			else
		{
		echo '{"Message":"Session Expired"}';
		}
	}				
	 
	 
	 
/** ------------------------new API for my watches by limit ends here----------------- */
	 /** ------------------------------------------------------------------ */
	public function ImageUpload($id,$ut)
	{
		$essence = new Essentials();
		$profile_pic = $essence->tblPrefix().'profile_photo';
		$profile_tbl = $essence->tblPrefix().'profile';
		$membership_limit  = $essence->tblPrefix().'link_membership_type_service';
		$membership_srv  = $essence->tblPrefix().'link_membership_service_limit';

		$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
        $sql = "SELECT max(`number`) as number from $profile_pic where `profile_id`=$id";
$sqlD = "SELECT username from $profile_tbl where `profile_id`=$id";
$res =$db->Query($sqlD);
$rw = mysql_fetch_array($res);
$username = $rw['username'];
		
		//checking membership for uploading photos
		/*$sqlChk = "SELECT membership_type_id,username FROM $profile_tbl WHERE profile_id='$id'";
		$sqlChkId = $db->Query($sqlChk);
		$sqlMemId = mysql_fetch_array($sqlChkId);
		$sqlMemIdType = $sqlMemId['membership_type_id'];
		$Username = $sqlMemId['username'];
		
		$sqlChkSrv = "SELECT membership_type_id FROM $membership_limit WHERE membership_type_id='$sqlMemIdType' AND membership_service_key='upload_photo'";
		$sqlChkSrv1 = $db->Query($sqlChkSrv);
		$sqlChkSrv11 = mysql_fetch_array($sqlChkSrv1);
		$sqlChkSrv = $sqlChkSrv11['membership_type_id'];
		if($sqlChkSrv != NULL)
		{*/
				if($db->Query($sql))
				{
					$row = $db->Row();
					$index = rand(1,99);
					$number = $row->number?$row->number+1:1;
					$time = time();
if($ut=='public' OR $ut=="public")
					{$keyvalue = array('photo_id'=>'NULL', 'profile_id'=>$id, 'index'=>$index, 'status'=>'"approval"', 'number'=>$number, 'description'=>'NULL', 'publishing_status'=>'"public"', 'password'=>'NULL', 'title'=>'NULL', 'added_stamp'=>$time, 'authed'=>0);}
else{
$keyvalue = array('photo_id'=>'NULL', 'profile_id'=>$id, 'index'=>$index, 'status'=>'"approval"', 'number'=>$number, 'description'=>'NULL', 'publishing_status'=>'"friends_only"', 'password'=>'NULL', 'title'=>'NULL', 'added_stamp'=>$time, 'authed'=>0);}
				   
					//$sqlins = "INSERT INTO '$profile_pic' values (NULL,$id,$index,'approval',$number,NULL,'public', NULL, NULL, $time, 0)";
					$photo_id	=	$db->InsertRow($profile_pic,$keyvalue);
					//if($db->Query($sqlins))
					//{
					   // $row    =   $db->Row();
						$result = array('photoid'=>$photo_id, 'index'=>$index, 'profile_id'=>$id,'username'=>$username,'time'=>$time);
						///print_r($result);
						return $result;
					//}
				}
			/*}
			else
			{
				echo '{"Message":"Membership Denied"}';
			}	*/
	}

/****************New API for getting State here*************/
	public function getState($id)
	{
mysql_query('SET CHARACTER SET utf8');
			$essence = new Essentials();
			//$secure     =   new secure();
			$location_table 	 =	$essence->tblPrefix().'location_country';
			$profile			 =  $essence->tblPrefix().'profile';
			$location_tbl		 =	$essence->tblPrefix().'location_state';


			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
				//check user sign in or not
			//$res = $secure->CheckSecure($pid,$skey);
			//if($res==1)
			//{
			$sql	="Select s.`Admin1_str_name` as Name,s.Admin1_str_code as Code from $location_tbl s LEFT JOIN $location_table c ON(c.Country_str_code =s.Country_str_code) where c.Country_str_code = '$id'";
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
		/*}
		else
		{
			echo '{"Message":"Session Expired"}';
		}
*/	
	}

/****************New API for getting State ends here*************/


/****************New API for getting City starts here*************/
public function getCity($id)
	{
mysql_query('SET CHARACTER SET utf8');
			$essence = new Essentials();
			//$secure     =   new secure();
			$location_table 	 =	$essence->tblPrefix().'location_country';
			$profile			 =  $essence->tblPrefix().'profile';
			$location_tbl		 =	$essence->tblPrefix().'location_state';
			$location_tbl_city		 =	$essence->tblPrefix().'location_city';

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
				//check user sign in or not
			//$res = $secure->CheckSecure($pid,$skey);
			//if($res==1)
			//{
			$sql	="Select c.`Feature_str_name` as Name,c.Feature_int_id as Code from $location_tbl_city c 
					  JOIN $location_tbl s ON(c.Admin1_str_code =s.Admin1_str_code) where c.Admin1_str_code = '$id'";
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
		/*}
		else
		{
			echo '{"Message":"Session Expired"}';
		}
*/	
	}
	
/****************New API for getting City ends here*****************/
public function getZip($id)
	{

			mysql_query('SET CHARACTER SET utf8');
			$essence = new Essentials();
			$location_table 	 =	$essence->tblPrefix().'location_country';
			$location_zip_tbl	 =  $essence->tblPrefix().'location_zip';
			$location_tbl		 =	$essence->tblPrefix().'location_state';
			$location_tbl_city	 =	$essence->tblPrefix().'location_city';

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			  $sql	="Select c.`Feature_str_name` as Name,c.Feature_int_id as Code,st.Admin1_str_code as StateCode,st.Admin1_str_name as StateName from $location_tbl_city c
JOIN $location_zip_tbl zp ON(zp.city_id=c.Feature_int_id)
JOIN $location_tbl st ON (st.Admin1_str_code=zp.state_id) WHERE zip = '$id'";
//echo $sql;
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"count": 0,"result": [{"Name":"NULL","Code":"NULL"}]}';
				
				}
			}	
	}
/****************New API for viewing Main forum starts here*****/
public function addNewForumMain($id,$skey)
	{

			$essence = new Essentials();
			$secure     =   new secure();

			$forum_tbl		 =	$essence->tblPrefix().'forum_topic';
			$forum_tbl_post	 =	$essence->tblPrefix().'forum_post';
			$forum_table 	 =	$essence->tblPrefix().'forum_group';
			$forum			 =  $essence->tblPrefix().'forum';

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			//check user sign in or not
			$res = $secure->CheckSecure($id,$skey);
			if($res==1)
			{
			$sql					=	"SELECT name,forum_group_id as group_id FROM $forum_table";
			
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}		
	}
/****************New API for viewing Main forum ends here*****/
/****************New API for viewing Sub forum starts here*****/
public function addNewForumSub($fid,$id,$skey)
	{

			$essence = new Essentials();
			$secure     =   new secure();
			$forum_tbl		 =	$essence->tblPrefix().'forum_topic';
			$forum_tbl_post	 =	$essence->tblPrefix().'forum_post';
			$forum_table 	 =	$essence->tblPrefix().'forum_group';
			$forum			 =  $essence->tblPrefix().'forum';
			$profile_tbl	 =	$essence->tblPrefix().'profile';


			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			/*$sql = "SELECT username,f.name,f.forum_id,t.title,p.text,FROM_UNIXTIME(p.create_stamp,'%Y %d %b at %h:%i %p') as createtime,
				   (SELECT COUNT(*) FROM $forum_tbl t LEFT JOIN $forum f ON t.forum_id = f.forum_id WHERE f.forum_group_id = '$fid') as count_topic ,
				   (SELECT COUNT(*) FROM skadate_forum_post p 
				   LEFT JOIN $forum_tbl t ON p.forum_topic_id = t.forum_topic_id
				   LEFT JOIN $forum g ON g.forum_id = t.forum_id 
				   WHERE g.forum_group_id = '$fid' ) as count_post,
				   (SELECT p.title FROM skadate_forum_topic p
				   LEFT JOIN skadate_forum_post t ON t.forum_topic_id = p.forum_topic_id 
				   ORDER BY p.create_stamp DESC LIMIT 0,1 ) as latest_topic  
				   FROM $forum_table g 
				   LEFT JOIN $forum f ON (f.forum_group_id = g.forum_group_id) 
				   LEFT JOIN $forum_tbl t ON (t.forum_id = f.forum_id) 
				   LEFT JOIN $forum_tbl_post p ON (p.forum_topic_id = t.forum_topic_id) 
				   LEFT JOIN $profile_tbl pf ON (pf.profile_id = t.profile_id)
				   WHERE f.forum_group_id = '$fid'";
				   /*SELECT `forum_id`,COUNT(`forum_topic_id`) AS `count_topic` FROM `skadate_forum_topic` 
GROUP BY `forum_id` 
				   
			
			
			/*$sql					=	"SELECT f.name,f.forum_id,t.title,p.text,p.count_post,t.count_topic FROM $forum_table g
										 LEFT JOIN $forum f ON (f.forum_group_id = g.forum_group_id)
										 LEFT JOIN $forum_tbl t  ON (t.forum_id = f.forum_id)
										 LEFT JOIN $forum_tbl_post p ON (p.forum_topic_id = t.forum_topic_id)
										 WHERE f.forum_group_id = '$fid'";
										 echo $sql;*/
										 
			//check user sign in or not
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
								 
										 
			$sql = "SELECT t.name FROM $forum t
					LEFT JOIN $forum_table  f ON f.forum_group_id = t.forum_group_id
					WHERE f.forum_group_id = '$fid'";							 
										 
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}		
	}
/****************New API for viewing Sub forum ends here*****/
/****************New API for viewing Topic forum starts here*****/
public function addNewForumTopic($forumid,$id,$skey)
	{

			$essence = new Essentials();
			$secure     =   new secure();
			$forum_tbl		 =	$essence->tblPrefix().'forum_topic';
			$forum_tbl_post	 =	$essence->tblPrefix().'forum_post';
			$forum_table 	 =	$essence->tblPrefix().'forum_group';
			$forum			 =  $essence->tblPrefix().'forum';

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
				//check user sign in or not
			$res = $secure->CheckSecure($id,$skey);
			if($res==1)
			{

			$fid = $forumid ;
			
			$sql					=	"SELECT title,FROM_UNIXTIME(create_stamp,'%Y %b %d') as create_time  FROM $forum_tbl t
										 LEFT JOIN $forum f ON f.forum_id = t.forum_id
										 WHERE f.forum_id = '$fid'";
			
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}		
	}
/****************New API for viewing Topic forum ends here*****/
/*************New API for viewing Topic forum starts here*****/
public function addNewForumPost($tid,$id,$skey)
	{

			$essence = new Essentials();
			$secure     =   new secure();
			$forum_tbl		 =	$essence->tblPrefix().'forum_topic';
			$forum_tbl_post	 =	$essence->tblPrefix().'forum_post';
			$forum_table 	 =	$essence->tblPrefix().'forum_group';
			$forum			 =  $essence->tblPrefix().'forum';
			$profile_tbl	 =	$essence->tblPrefix().'profile';
			$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
			$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
		   $res = $secure->CheckSecure($id,$skey);
		   if($res==1)
		   {
			$sql2="SELECT profile_id FROM $forum_tbl_post WHERE forum_topic_id='$tid'";
			$res2=$db2->Query($sql2);
			$resPid=mysql_fetch_array($res2);
			$pid=$resPid['profile_id'];
			//membership checking starts here
		 $sql0 = "SELECT membership_type_id FROM $profile_tbl WHERE profile_id ='$id'";
		 $memberType1 =  $db->Query($sql0);
		 $memberTypeId1 = mysql_fetch_array($memberType1);
		 $membershipTypeId1 = $memberTypeId1['membership_type_id'];
		 //echo "Mem Type ID".$membershipTypeId1;
		 $sql2 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='forum_read' AND membership_type_id='$membershipTypeId1'";
		 //echo $sql2;
		 $res = $db->Query($sql2);
		 $resultId = mysql_fetch_array($res);
		 $resultMemberId = $resultId['membership_type_id'];
		
		/* if($resultMemberId != NULL)
        { */
			
			$sql					=	"SELECT username,text,FROM_UNIXTIME(p.create_stamp,'%Y %b %d') as create_time 
										 FROM $forum_tbl_post p
										 LEFT JOIN $forum_tbl t ON t.forum_topic_id = p.forum_topic_id
										 LEFT JOIN $profile_tbl pt ON pt.profile_id = p.profile_id
										 WHERE t.forum_topic_id='$tid'";
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
			/*}
			else
	{
		echo '{"Message":"Membership Denied"}';
	}*/
	}
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
	}
/****************New API for viewing Topic forum ends here*****/

/****************New API for adding topic in forum starts here*****/
public function addTopicForum($pid,$fid,$title,$text,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$forum_tbl		 =	$essence->tblPrefix().'forum_topic';
			$forum_tbl_post	 =	$essence->tblPrefix().'forum_post';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
			$time = time();
			$profile_id = $pid;
			$forum_id = $fid ;
			$title = $title;
			$text = $text;
			$sql1					=	"INSERT into `$forum_tbl` values('','$forum_id','$profile_id','$title','$time','n','n','0','0','0','0')";
			$db->Query($sql1);
			$tid					=	mysql_insert_id();
			$sql					=	"INSERT into `$forum_tbl_post` values('', '$tid','$profile_id','$time', '$text', '0', '0')";
			if($db->Query($sql))
			{
					echo '{"Status":"Live","Message":"Success"}';
			}
			else
			{
				echo '{"Status":"Live","Message": "Failure"}';
			
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
	}

/****************New API for adding topic in forum ends here*****/
/****************New API for adding posts in forum starts here*****/


public function replyTopicForum($pid,$tid,$text,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$forum_table 	 =	$essence->tblPrefix().'forum_group';
			$forum			 =  $essence->tblPrefix().'forum';
			$forum_tbl		 =	$essence->tblPrefix().'forum_topic';
			$forum_tbl_post	 =	$essence->tblPrefix().'forum_post';
		
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
			$time = time();
			$profile_id = $pid;
			$topic_id = $tid;
			$text	= $text;
			$sql 	="INSERT into `$forum_tbl_post` values('', '$topic_id','$profile_id','$time', '$text', '0', '0')";
			if($db->Query($sql))
				{	
					echo '{"Status":"Live","Message": "Success"}';
				}
				else
				{
					echo '{"Status":"Live","Message":"Failure"}';
				
				}
			}
			else
			{
				echo '{"Message":"Session Expired"}';
			}	
				
	}
		
/****************New API for adding posts in forum ends here*****/
		
/****************New API for view topics in forum starts here*****/
public function retreiveTopicForum($pid,$tid,$skey)
	{

			$essence = new Essentials();
			$secure     =   new secure();

			$forum_table 	 =	$essence->tblPrefix().'forum_group';
			$forum			 =  $essence->tblPrefix().'forum';
			$forum_tbl		 =	$essence->tblPrefix().'forum_topic';
			$forum_tbl_post	 =	$essence->tblPrefix().'forum_post';
			$profile_tbl	 =	$essence->tblPrefix().'profile';

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
			$time = time();
			$profile_id = $pid;
			$topic_id = $tid;
			$sql 	="SELECT pt.username, title, TEXT,FROM_UNIXTIME(p.create_stamp,'%Y %d %b at %h:%i %p') as create_stamp FROM $forum_tbl_post p
					  JOIN $forum_tbl f ON ( f.forum_topic_id = p.forum_topic_id ) 
					  LEFT JOIN $profile_tbl pt ON (pt.profile_id = p.profile_id)
					  WHERE p.profile_id = '$pid' AND p.forum_topic_id ='$tid'";
			if($db->Query($sql))
			{
				if($db->RowCount())
					{	
						$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace("},]","}]",$stri);
					}
					else
					{
						echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
					
					}
			}
		}
		else
		{	
			echo '{"Message":"Session Expired"}';
		}		
	}



/****************New API for view topics in forum ends here*****/

/****************New API for view events  starts here*****/
public function viewEvents($id,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();

			$event_table 	 =	$essence->tblPrefix().'event';
			$event_profile_tbl 	 =	$essence->tblPrefix().'event_profile';
			$location_table 	 =	$essence->tblPrefix().'location_country';
			$profile_tbl			=	$essence->tblPrefix().'profile';
			$time = time();

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
		//check user sign in or not
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{


			//$sql1	= "SELECT title,description,profile_id,start_date,end_date,create_date,image FROM `$event_table`";
			$sql	= "SELECT e.id as event_id,`title`,username,FROM_UNIXTIME(start_date,'%M %d, %Y') as startdate, `image` ,Country_str_name as location ,(
					   SELECT COUNT( p.event_id ) 
					   FROM skadate_event_profile p
                       WHERE STATUS =  '1'
                       AND p.event_id = e.id
                       ) AS Attendees
                       FROM skadate_event e 
					   JOIN $location_table l ON (l.Country_str_code = e.country_id)
					   LEFT JOIN $profile_tbl p ON (e.profile_id = p.profile_id)
					   WHERE start_date>='$time '";
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
	}

/****************New API for view events  ends here*****/
/******new API for viewing events separately starts here**************/

public function FetchEventsOld($id)
	{

			$essence = new Essentials();
			$event_table 	 =	$essence->tblPrefix().'event';
			$location_table 	 =	$essence->tblPrefix().'location_country';
			$profile_tbl			=	$essence->tblPrefix().'profile';

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			/*$sql1 = "SELECT i_am_attand FROM $event_table WHERE id ='$id'";
			$attand = $db->Query($sql1);
			echo $attand;
			if($attand ==1)
			{
			$attandResult = 'joined';
			}
			else
			{
			$attandResult = '';
			}*/
			$sql	="SELECT e.id,username,title,description,FROM_UNIXTIME(start_date,'%Y %d %b at %h:%i %p') as startdate,
					  FROM_UNIXTIME(end_date,'%Y %d %b at %h:%i %p') as enddate,Country_str_name as location,address,e.custom_location,image FROM `skadate_event` e 
					  JOIN $location_table l ON (l.Country_str_code = e.country_id)
					  LEFT JOIN $profile_tbl p ON (e.profile_id = p.profile_id) 
					  where id='$id'";
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
	}
/******new API for viewing events separately ends here**************/
/**************new API for fetching  events starts here***************/
public function FetchEvents($id,$pid,$skey)
	{

			$essence = new Essentials();
			$secure     =   new secure();
			$event_table 	 =	$essence->tblPrefix().'event';
			$location_table 	 =	$essence->tblPrefix().'location_country';
			$profile_tbl			=	$essence->tblPrefix().'profile';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			
			//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
		
			$sql	="SELECT e.id,username,title,description,FROM_UNIXTIME(start_date,'%Y %d %b at %h:%i %p') as startdate,
					  FROM_UNIXTIME(end_date,'%Y %d %b at %h:%i %p') as enddate,Country_str_name as location,address,e.custom_location,image,i_am_attand FROM `skadate_event` e 
					  JOIN $location_table l ON (l.Country_str_code = e.country_id)
					  LEFT JOIN $profile_tbl p ON (e.profile_id = p.profile_id) 
					  where id='$id'";
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"Status":"Live","Result":'.$db->GetJSON().'';
					echo str_replace("},","}}",$stri);
				}
				else
				{
					echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
	}
/**************new API for fetching  events ends here
/******new API for join events starts here**************/

public function joinEvents($id,$eid,$flag)
	{
			$essence = new Essentials();
			$event_table 	 =	$essence->tblPrefix().'event';
			$event_profile_tbl 	 =	$essence->tblPrefix().'event_profile';
			$profile_tbl			=	$essence->tblPrefix().'profile';

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$sqlP = "SELECT p.profile_id FROM $profile_tbl p
			         WHERE p.profile_id = '$id'";
					// echo $sqlP;
			$profileExist = $db->Query($sqlP); 
			If($profileExist !=NULL)
			{
			
			$sql	="SELECT * FROM `$event_profile_tbl` where profile_id=$id";

			if($db->Query($sql))
			{
				$row = $db->Row();
				$time = time();
				$profile_id = $id;
				$event_id = $eid;
				
				$keyvalue =array('event_id'=>$event_id,'profile_id'=>$profile_id, 'status'=>'1', 'join_date'=>$time);
				
			
				foreach ($keyvalue as $key=> $value )
				{
				
					if($flag == 'A')
					{
						$sqlC="SELECT * FROM $event_profile_tbl WHERE profile_id= '$id' AND event_id = '$eid'";

						$db->Query($sqlC);
						$countSql=$db->RowCount();
						if($countSql==NULL)
						{
							$result =	$db->InsertRow($event_profile_tbl,$keyvalue);
						}
						else
						{
							 $result1 = "UPDATE $event_profile_tbl SET status ='1' WHERE profile_id= '$id' AND event_id = '$eid'";
							 $result = $db->Query( $result1);
						}
					}
					else if($flag == 'NA')	
					{
						$sqlC="SELECT * FROM $event_profile_tbl WHERE profile_id= '$id' AND event_id = '$eid'";
						$db->Query($sqlC);
						$countSql=$db->RowCount();
						if($countSql==NULL)
						{
						
							$result2 =	"INSERT INTO $event_profile_tbl VALUES('','$event_id','$profile_id','0','$time')";
							$result=$db->Query($result2);
						}
						else
						{
							 $result2 = "UPDATE $event_profile_tbl SET status ='0' where profile_id= '$id' AND event_id = '$eid' AND status='1'";
							 $result = $db->Query($result2);
						 }
					}
					else if($flag == 'NS')
					{
						$result3 = "DELETE FROM $event_profile_tbl WHERE  profile_id='$id' AND event_id='$event_id'";
						$result = $db->Query($result3);
						
					}
					else
					{
					echo '{"Result": "Failure"}';
					break;
					}
					 $sqlR	= "SELECT * FROM `$event_profile_tbl` where event_id='$eid' AND status = '1' ";
					$db->Query($sqlR);
					$totalCount=$db->RowCount();
					if($totalCount==NULL)
					$totalCount=0;
				    $sqlA	= "SELECT id,event_id,profile_id,status,FROM_UNIXTIME(join_date,'%Y-%M-%d') as joindate FROM `$event_profile_tbl` where  event_id='$eid' AND status = '0'";
					$db->Query($sqlA);
					$NotAttend=$db->RowCount();
					if($NotAttend==NULL)
					$NotAttend=0;
					
					echo '{"Status" : "Success","Profile_Id":"'.$profile_id.'","Event_Id":"'.$event_id.'","Attendees" : "'.$totalCount.'","People who will not attend" :"'.$NotAttend.'"}';
					
					
						return $result;
					
				}
				//$sqlins = "INSERT INTO '$profile_pic' values (NULL,$id,$index,'approval',$number,NULL,'public', NULL, NULL, $time, 0)";
				//if($db->Query($sqlins))
				//{
				   // $row    =   $db->Row();
					//$result = array('id'=>$id, 'index'=>$index, 'profile_id'=>$pid);
					///print_r($result);

			}
				else
					{
						$stri = '{"Status": "Failure","result": ['.$db->GetJSON().']}';
						echo str_replace("},]","}]",$stri);
	
					}
				}
			else
				{
					echo '{"Incorrect ID"}';

				}	
			
	}
/******new API for join events ends here**************/
/******new API for my events starts here**************/
public function myEvents($id,$pid,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$event_table 	 =	$essence->tblPrefix().'event_profile';
			$event_tbl	 =	$essence->tblPrefix().'event';
			$location_table 	 =	$essence->tblPrefix().'location_country';
			$profile_table			=	$essence->tblPrefix().'profile';	
			$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
			$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';


			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
			
			
			//membership checking starts here
		 $sql0 = "SELECT membership_type_id FROM $profile_table WHERE profile_id ='$id'";
		 $memberType1 =  $db->Query($sql0);
		 $memberTypeId1 = mysql_fetch_array($memberType1);
		 $membershipTypeId1 = $memberTypeId1['membership_type_id'];
		 
		 $sql2 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='event_view' AND membership_type_id='$membershipTypeId1'";
		 $res = $db->Query($sql2);
		 $resultId = mysql_fetch_array($res);
		 $resultMemberId = $resultId['membership_type_id'];
		
		// if($resultMemberId != NULL)
       // { 
			
			$sql	="SELECT title,description,profile_id,start_date,end_date,create_date,image,Country_str_name as location ,(
					   SELECT COUNT( p.event_id ) 
					   FROM skadate_event_profile p
                       WHERE STATUS =  '1'
                       AND p.event_id = e.id
                       ) AS Attendees FROM `$event_tbl` e
					 JOIN skadate_location_country l ON (l.Country_str_code = e.country_id) WHERE profile_id=$id";
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
					echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
				
				}
			}
			/*}
			else
	{
		echo '{"Message":"Membership Denied"}';
	}*/
	}
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
	
	}
/******new API for my events ends here**************/
	 /*     * **********new API for news feed view latest starts************ */ 

    public function newsFeedView($pid, $skey) { 
        mysql_query('SET CHARACTER SET utf8'); 
        //$script_tz = date_default_timezone_get(); 
        //date_default_timezone_set('Asia/Calcutta'); 
        //echo $script_tz; 
        $essence            = new Essentials(); 
        $secure             = new secure(); 
        $news_tb_action     = $essence->tblPrefix() . 'newsfeed_action'; 
        $news_tb_feed       = $essence->tblPrefix() . 'newsfeed_action_feed'; 
        $news_tb_like       = $essence->tblPrefix() . 'newsfeed_like'; 
        $pic_tbl            = $essence->tblPrefix() . 'profile_photo'; 
        $profile_tbl        = $essence->tblPrefix() . 'profile'; 
        $news_tb_like       = $essence->tblPrefix() . 'newsfeed_like'; 
        $news_tb_comment    = $essence->tblPrefix() . 'newsfeed_comment'; 
        $forum_topic        = $essence->tblPrefix() . 'forum_topic'; 
        $forum_post         = $essence->tblPrefix() . 'forum_post'; 
        $friend_list        = $essence->tblPrefix() . 'profile_friend_list'; 
        $profile_music      = $essence->tblPrefix() . 'profile_music'; 
        $photo_upload       = $essence->tblPrefix() . 'profile_photo'; 
        $event_add          = $essence->tblPrefix() . 'event'; 
        $like_status        = $essence->tblPrefix() . 'newsfeed_like'; 
        $blog_tbl           = $essence->tblprefix() . 'blog_post'; 
        $music_upload       = $essence->tblPrefix() . 'profile_music'; 
        $group_tbl          = $essence->tblprefix() . 'group'; 
        $profile_cmnt_tbl   = $essence->tblprefix() . 'profile_comment'; 
        $event_tb_comment   = $essence->tblPrefix() . 'event_comment'; 
        $blog_tb_comment    = $essence->tblPrefix() . 'blog_post_comment'; 
        $music_tb_comment   = $essence->tblPrefix() . 'music_comment'; 
        $group_tb_comment   = $essence->tblPrefix() . 'group_comment'; 
        $photo_tb_comment   = $essence->tblPrefix() . 'photo_comment'; 
        $classified_tb_comment = $essence->tblPrefix() . 'classified_comment'; 
        $video_tb_comment   = $essence->tblPrefix() . 'video_comment'; 
        $group_membe        = $essence->tblPrefix() . 'group_member'; 
        $video_tbl          = $essence->tblPrefix() . 'profile_video'; 
        $config_tbl         = $essence->tblPrefix() . 'config'; 
        $config_section_tbl = $essence->tblPrefix() . 'config_section'; 
        $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        $db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
         $db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
          $db3 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
           $db4 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
          $db5 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
         $db6 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        $db7 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
         $db8 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
          $db9 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        //check user sign in or not 
        $res = $secure->CheckSecure($pid, $skey); 
        $res=1; 
        if ($res == 1) 
            { 
            $sqlConfig = "SELECT value FROM $config_tbl WHERE config_section_id =(SELECT config_section_id FROM $config_section_tbl  WHERE label='Newsfeed Settings' and section='newsfeed') and name='display_count'"; 
            $sqlConfigRes = $db->Query1($sqlConfig); 
            $sqlConfigExe = mysql_fetch_array($sqlConfigRes); 
            $sqlNewsCount = $sqlConfigExe['value']; 

            $count1 = $sqlNewsCount; 
            //$count1=50; 
            //Query strats here  
          $sql = "SELECT a.id,a.entityId,a.entityType,data,a.userId,p.username,p.sex,a.createTime as Time,CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
                    CAST( ptbl.photo_id AS CHAR ) , '_', CAST( ptbl.index AS CHAR ) , '.jpg' ) 
                    as Profile_Pic,f.friend_id as FriendID,(SELECT p.username FROM $profile_tbl p WHERE 
                    p.profile_id=f.friend_id)as FriendName,(SELECT p.sex FROM $profile_tbl p WHERE 
                    p.profile_id=f.friend_id)as FriendSex,CONCAT( '/','userfiles/thumb_', 
                    CAST( ptl.profile_id AS CHAR ) , '_',CAST( ptl.photo_id AS CHAR ) , '_', 
                    CAST( ptl.index AS CHAR ) , '.jpg' ) as Friend_Pic,CONCAT( '/','userfiles/thumb_', 
                    CAST( photo_upload.profile_id AS CHAR ) , '_',CAST( ptbl.photo_id AS CHAR ) , '_', 
                    CAST( ptbl.index AS CHAR ) , '.jpg' ) as ActionImage,(SELECT COUNT(*) FROM 
                    $news_tb_comment c WHERE c.entity_id= a.entityId) as Comment,(SELECT COUNT(*) FROM 
                    $news_tb_like l WHERE l.entityId = a.entityId and l.entityType=a.entityType) 
                    as likecount,pp.title as Title,t.text as Description,cmnt.id as Comment_Id,ls.id 
                    as LikeStatusID,(SELECT COUNT(*) FROM $video_tb_comment c WHERE 
                    c.entity_id= a.entityId) as VComment,v.title as vtitle,v.privacy_status,v.description 
                    as vdescription,sus.status as statTxt FROM $news_tb_action a 
                    JOIN $profile_tbl p ON (p.profile_id=a.userId) 
                    LEFT JOIN $pic_tbl ptbl ON (ptbl.profile_id = a.userId and ptbl.number=0 ) 
                    LEFT JOIN $friend_list f ON (f.id=a.entityId and f.profile_id=a.userId and a.entityType='friend_add') 
                    LEFT JOIN $pic_tbl photo_upload ON (photo_upload.profile_id = a.userId and photo_upload.photo_id=a.entityId) 
                    LEFT JOIN $forum_topic  pp ON (pp.forum_topic_id=a.entityId and pp.profile_id=a.userId and a.entityType='forum_add_topic') 
                    LEFT JOIN $forum_post t ON (t.forum_topic_id=pp.forum_topic_id AND t.profile_id=a.userId and a.entityType='forum_add_topic') 
		LEFT JOIN $news_tb_comment cmnt ON (cmnt.entity_id = a.entityId and cmnt.create_time_stamp=a.createTime and a.entityType='profile_comment' and a.entityType!='user_comment') 
		LEFT JOIN $pic_tbl ptl ON (ptl.profile_id = f.friend_id and ptl.number=0 and ptl.status='active') 
		LEFT JOIN $news_tb_like ls ON (ls.entityId=a.entityId and ls.userId=$pid and ls.entityType=a.entityType) 
                LEFT JOIN $video_tbl v ON (v.upload_stamp=a.createTime and v.profile_id=a.userId) 
                LEFT JOIN skadate_user_status sus ON (sus.profile_id = a.userId) 
		WHERE a.status='active' ORDER BY a.updateTime DESC LIMIT 0,$count1"; 
            //echo $sql; 
            //(SELECT COUNT(*) FROM $video_tb_comment c WHERE c.entity_id= a.id) as Comment,//JOIN $video_tb_comment p ON (p.entity_id=a.entityId) 
            $res1 = $db->Query($sql); 

// $sample_status; 
            //event add 
            $sql2 = "SELECT a.id,a.entityId,a.entityType,event_add.image as ActionImage,event_add.title as Title,event_add.description as Description,(SELECT COUNT(*) FROM $event_tb_comment c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a 
					  JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
					  LEFT JOIN $event_add  event_add ON (event_add.profile_id = a.userId and event_add.create_date=a.createTime and a.entityType='event_add')					  
					  WHERE a.status='active' ORDER BY a.updateTime DESC LIMIT 0,$count1 
					  "; 
            $res2 = $db2->Query($sql2); 

            //bloga post 
            $sql3 = "SELECT a.id,a.entityId,a.entityType,blog_tbl.title as Title,blog_tbl.preview_text as Description,(SELECT COUNT(*) FROM 	$blog_tb_comment c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a 
					  JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
					  LEFT JOIN $blog_tbl  blog_tbl ON (blog_tbl.profile_id = a.userId and blog_tbl.create_time_stamp=a.createTime and a.entityType='blog_post_add')					  
					  WHERE a.status='active' ORDER BY a.updateTime DESC LIMIT 0,$count1 
					  "; 
            $res3 = $db3->Query($sql3); 

            //music upload 
            $sql4 = "SELECT a.id,a.entityId,a.entityType,music_upload.title as Title,music_upload.description as Description,music_upload.privacy_status,(SELECT COUNT(*) FROM $music_tb_comment c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a 
					  JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
					  LEFT JOIN $music_upload  music_upload ON (music_upload.profile_id = a.userId and music_upload.upload_stamp=a.createTime and a.entityType='music_upload')					  
					  WHERE a.status='active' ORDER BY a.updateTime DESC LIMIT 0,$count1 
					  "; 
            $res4 = $db4->Query($sql4); 

            //gropu add 
            $sql5 = "SELECT a.id,a.entityId,a.entityType,group_tbl.title as Title,CONCAT( '/','userfiles/thumb_group_', CAST( group_tbl.group_id AS CHAR ), '_',CAST( group_tbl.photo AS CHAR ) , '.jpg' ) as ActionImage,group_tbl.description as Description,(SELECT COUNT(*) FROM $group_tb_comment c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a 
					  JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
					  LEFT JOIN $group_tbl  group_tbl ON (group_tbl.owner_id = a.userId and group_tbl.creation_stamp=a.createTime and (a.entityType='group_add' or a.entityType='group_join'))					  
					  WHERE a.status='active' ORDER BY a.updateTime DESC LIMIT 0,$count1 
					  "; 

            $res5 = $db5->Query($sql5); 
            //profile comment table 

            $sql6 = "SELECT a.id,a.entityId,a.entityType,profile_cmnt_tbl.text as Title,(SELECT COUNT(*) FROM $profile_cmnt_tbl c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a 
		   JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
		   LEFT JOIN $profile_cmnt_tbl  profile_cmnt_tbl ON (profile_cmnt_tbl.entity_id = a.entityId and profile_cmnt_tbl.author_id=a.userId and  profile_cmnt_tbl.create_time_stamp=a.createTime)					  
WHERE a.status='active' ORDER BY a.updateTime DESC LIMIT 0,$count1"; 
//echo $sql6;				  
            $res6 = $db6->Query($sql6); 

//photo upload 
            $sql7 = "SELECT DISTINCT a.id,a.entityId,a.entityType,photo_upload.photo_id,CONCAT( '/','userfiles/thumb_', CAST( photo_upload.profile_id AS CHAR ) , '_',CAST( photo_upload.photo_id AS CHAR ) , '_', CAST( photo_upload.index AS CHAR ) , '.jpg' ) as ActionImage,(SELECT COUNT(*) FROM $photo_tb_comment c WHERE c.entity_id= a.entityId) as Comment,photo_upload.publishing_status,a.userId FROM $news_tb_action a 
		   JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
		   LEFT JOIN $photo_upload  photo_upload ON (photo_upload.profile_id=a.userId and photo_upload.added_stamp=a.createTime and photo_upload.number>0)					  
WHERE a.status='active' ORDER BY a.updateTime DESC LIMIT 0,$count1"; 
//echo $sql7; 
            $res7 = $db7->Query($sql7); 

//classified 
            $sql8 = "SELECT (SELECT COUNT(*) FROM $classified_tb_comment c WHERE c.entity_id= a.id) as Comment FROM $news_tb_action a 
		   JOIN $classified_tb_comment p ON (p.entity_id=a.entityId)					  
WHERE a.status='active' ORDER BY a.updateTime DESC LIMIT 0,$count1"; 

//echo $sql7; 
            $res8 = $db8->Query($sql8); 
//media upload 


            $count = $db2->RowCount(); 
            $i = 1; 
            // Query Ends here		 
            // Assigning to array Starts here  
            while ($row1 = @mysql_fetch_array($res1) and $row2 = @mysql_fetch_array($res2) and $row3 = @mysql_fetch_array($res3) and $row4 = @mysql_fetch_array($res4) and $row5 = @mysql_fetch_array($res5) and $row6 = @mysql_fetch_array($res6) and $row7 = @mysql_fetch_array($res7) or $row8 = @mysql_fetch_array($res8)) { 

                $dateNew = date("m-d-Y H:i:s", $row1['Time']); 
                if ($row2['entityType'] == 'event_add') { 
                    $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row2['ActionImage'], 'Comment' => $row2['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row2['Title'], 'Description' => $row2['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                    $i++; 
                }else if ($row1['entityType'] == 'friend_add'){
                	$returnValue = json_decode($row1['data']);
                	$fid=$returnValue->content->friend_id;
                	$sql="SELECT username, sex FROM $profile_tbl WHERE profile_id=$fid";
                	$exe=$db8->query($sql);
                	$res=mysql_fetch_array($exe);
                	$fusername=$res['username'];
                	$fsex=$res['sex'];
                	$fthumb=$this->getThumbImage($fid);
                	
                	 $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $fid, 'FriendName' => $fusername, 'FriendSex' => $fsex, 'Friend_Pic' => $fthumb, 'ActionImage' => $row1['ActionImage'], 'Comment' => $row1['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row1['Title'], 'Description' => $row1['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $sqlFstatus); 
                    $i++; 
                	
                } 
                else if ($row3['entityType'] == 'blog_post_add') { 
                    $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => 'null', 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row1['ActionImage'], 'Comment' => $row3['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row3['Title'], 'Description' => $row3['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                    $i++; 
                } else if ($row4['entityType'] == 'music_upload') { 

///////////////////////////////

if ($row4['privacy_status'] == 'public') {
                        $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row2['ActionImage'], 'Comment' => $row4['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row4['Title'], 'Description' => $row4['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']);
                    $i++;
                    } 
                    else if ($row4['privacy_status'] == 'password_protected') {
                        $i;
                    } else if ($row4['privacy_status'] == 'friends_only') {
                        $userid = $row1['userId'];
                        $profileId = $pid;
                        $sqlF = "SELECT id FROM $friend_list WHERE profile_id='$profileId' and friend_id='$userid'";
                        $sqlExe = $db->Query($sqlF);
                        $sqlFriend = mysql_fetch_array($sqlExe);
                        $sqlFResult = $sqlFriend['id'];
                        if ($sqlFResult != NULL) {
                            $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row2['ActionImage'], 'Comment' => $row4['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row4['Title'], 'Description' => $row4['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']);
                    $i++;
                        } else {
                            //$result[$i] = array('id' => NULL, 'entityId' => NULL, 'entityType' => NULL, 'data' => NULL, 'userId' => NULL, 'username' => NULL, 'sex' => NULL, 'Time' => NULL, 'Profile_Pic' => NULL, 'FriendID' => NULL, 'FriendName' => NULL, 'FriendSex' => NULL, 'Friend_Pic' => NULL, 'ActionImage' => NULL, 'Comment' => NULL, 'likecount' => NULL, 'Title' => NULL, 'Description' => NULL, 'Comment_Id' => NULL, 'LikeStatusID' => NULL);
                            $i;
                        }
                    } else {
							$result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row2['ActionImage'], 'Comment' => $row4['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row4['Title'], 'Description' => $row4['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']);
                    $i++;
                    }






//////////////////////////////////////
                   /* $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row2['ActionImage'], 'Comment' => $row4['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row4['Title'], 'Description' => $row4['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                    $i++; */
                } else if ($row5['entityType'] == 'group_add' or $row5['entityType'] == 'group_join') { 
                    if ($row5['entityType'] == 'group_join') { 
                        $sqlgj = "SELECT group_id FROM $group_membe WHERE member_id=$row1[userId] AND group_id=$row1[entityId]"; 
                        $commentr = $db8->Query($sqlgj); 
                        $commentr_res = mysql_fetch_array($commentr); 
                        $user_name = $commentr_res['group_id']; 
                        $sqlgjj = "SELECT title,description FROM $group_tbl WHERE group_id=$user_name"; 
                        $commentr1 = $db8->Query($sqlgjj); 
                        $commentr_res1 = mysql_fetch_array($commentr1); 
                        $title = $commentr_res1['title']; 
                        $description = $commentr_res1['description']; 
                        $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row5['ActionImage'], 'Comment' => $row5['Comment'], 'likecount' => $row1['likecount'], 'Title' => $title, 'Description' => $description, 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                        $i++; 
                    } else if ($row5['entityType'] == 'group_add') { 
                        $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row5['ActionImage'], 'Comment' => $row5['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row5['Title'], 'Description' => $row5['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                        $i++; 
                    } 
                }else if ($row1['entityType'] == 'profile_join'){ 
                	$iid=$row1['entityId']; 
                	$ttype=$row1['entityType']; 
                	$querylike="SELECT id FROM $news_tb_like WHERE (entityId='$iid' and userId=$pid and entityType='$ttype')"; 
                	$commentr = $db->Query($querylike); 
                    $commentr_res = mysql_fetch_array($commentr); 
                    $iidL = $commentr_res['id']; 
                    $row1['LikeStatusID']=$iidL; 
                    $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row1['ActionImage'], 'Comment' => $row1['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row1['Title'], 'Description' => $row1['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                    $i++; 
                } 
                else if ($row6['entityType'] == 'profile_comment') { 
                    $sql8 = "SELECT username FROM $profile_tbl WHERE profile_id=$row1[entityId]"; 
                    $commentr = $db->Query($sql8); 
                    $commentr_res = mysql_fetch_array($commentr); 
                    $user_name = $commentr_res['username']; 
                    $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['entityId'], 'FriendName' => $user_name, 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row1['ActionImage'], 'Comment' => $row6['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row6['Title'], 'Description' => $row1['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                    $i++; 
                } else if ($row7['entityType'] == 'photo_upload' or $row7['entityType'] == 'profile_avatar_change') { 
					$photoid=$row7['photo_id']; 
                	$sqlPS="SELECT privacy 
FROM `skadate_photo_albums` a 
LEFT JOIN `skadate_photo_album_items` ai ON ai.`album_id` = a.id 
WHERE `photo_id` =$photoid 
LIMIT 0 , 30"; 
                	$commentr = $db->Query($sqlPS); 
                    $commentr_res = mysql_fetch_array($commentr); 
                    $PSS = $commentr_res['privacy']; 
                    if($row7['entityType'] == 'profile_avatar_change') 
                    { 
                    	$enid=$row7['entityId']; 
                    $sqlneww="SELECT count(*) FROM `skadate_newsfeed_comment` WHERE `entity_id` =$enid"; 
                    $commentr = $db->Query($sqlneww); 
                    $commentr_res = mysql_fetch_array($commentr); 
                    $row7['Comment'] = $commentr_res['count(*)']; 
                    } 
                    
                	/*if(($PSS!=NULL OR $PSS!='') AND  $PSS!='public') 
                	{$row7['publishing_status']=$PSS;}*/ 
                	 
                     if($PSS!=NULL OR $PSS!='') 
                	{ 
	                	if ($PSS=='public') 
	                	{ 
	                		$row7['publishing_status']=$row7['publishing_status']; 
	                	} 
	                	else if($PSS=='friends_only' AND $row7['publishing_status']!='password_protected') 
	                	{ 
	                		$row7['publishing_status']=$PSS; 
	                	} 
	                	else if($PSS=='friends_only' AND $row7['publishing_status']=='password_protected') 
	                	{ 
	                		$row7['publishing_status']='password_protected'; 
	                	} 
	                	else if($PSS=='password_protected') 
	                	{ 
	                		$row7['publishing_status']='password_protected'; 
	                	} 
                	} 
                    
                    
                    
                    
                    
						if($row7['ActionImage']==NULL OR $row7['ActionImage']=='') 
							{ 
								$row7['ActionImage']='Deleted Photo'; 
							} 
                	 
                	 
                    if ($row7['publishing_status'] == 'public') { 
                        $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row7['ActionImage'], 'Comment' => $row7['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row1['Title'], 'Description' => $row1['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                        $i++; 
                    } 
                    else if ($row7['publishing_status'] == 'password_protected') { 
                        $i; 
                    } else if ($row7['publishing_status'] == 'friends_only') { 
                        $userid = $row7['userId']; 
                        $profileId = $pid; 
                        $sqlF = "SELECT id FROM $friend_list WHERE profile_id='$profileId' and friend_id='$userid'"; 
                        $sqlExe = $db->Query($sqlF); 
                        $sqlFriend = mysql_fetch_array($sqlExe); 
                        $sqlFResult = $sqlFriend['id']; 
                        if ($sqlFResult != NULL) { 
                            $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row7['ActionImage'], 'Comment' => $row7['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row1['Title'], 'Description' => $row1['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                            $i++; 
                        } else { 
                            //$result[$i] = array('id' => NULL, 'entityId' => NULL, 'entityType' => NULL, 'data' => NULL, 'userId' => NULL, 'username' => NULL, 'sex' => NULL, 'Time' => NULL, 'Profile_Pic' => NULL, 'FriendID' => NULL, 'FriendName' => NULL, 'FriendSex' => NULL, 'Friend_Pic' => NULL, 'ActionImage' => NULL, 'Comment' => NULL, 'likecount' => NULL, 'Title' => NULL, 'Description' => NULL, 'Comment_Id' => NULL, 'LikeStatusID' => NULL); 
                            $i; 
                        } 
                    } else { 
							$result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row7['ActionImage'], 'Comment' => $row7['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row1['Title'], 'Description' => $row1['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                        $i++; 
                    } 
                } else if ($row7['entityType'] == 'post_classified_item') { 

                    $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row1['ActionImage'], 'Comment' => $row1['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row1['Title'], 'Description' => $row1['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                    $i++; 
                } else if ($row7['entityType'] == 'media_upload') { 
                    if ($row1['privacy_status'] == 'public') { 
                        //$db10 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 

                        $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row1['ActionImage'], 'Comment' => $row1['VComment'], 'likecount' => $row1['likecount'], 'Title' => $row1['vtitle'], 'Description' => $row1['vdescription'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                        //print_r($result); 
                        $i++; 
                    } else if ($row1['privacy_status'] == 'friends_only') { 
                        $userid = $row7['userId']; 
                        $profileId = $pid; 
                        $sqlF = "SELECT id FROM $friend_list WHERE profile_id='$profileId' and friend_id='$userid'"; 
                        $sqlExe = $db->Query($sqlF); 
                        $sqlFriend = mysql_fetch_array($sqlExe); 
                        $sqlFResult = $sqlFriend['id']; 
                        if ($sqlFResult != NULL) { 
                            $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row1['ActionImage'], 'Comment' => $row1['VComment'], 'likecount' => $row1['likecount'], 'Title' => $row1['vtitle'], 'Description' => $row1['vdescription'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $row1['LikeStatusID']); 
                            $i++; 
                        } 
                    } else { 
                        $i; 
                    } 
                } else if ($row7['entityType'] == 'status_update') { 
                    $id = $row1['id']; 
                    $type = $row1['entityType']; 

                    $sqlF = "SELECT id,(SELECT COUNT(*) FROM $news_tb_like ll WHERE ll.entityId ='$id' and ll.entityType='$type') AS count FROM $news_tb_like l WHERE l.entityId ='$id' and l.entityType='$type' and l.userId='$pid'"; 
                    
                    /*(select count(*) FROM $news_tb_comment entity_id=$id) as Comment,*/ 
                    $sqlExe = $db->Query($sqlF); 
                    $sqlFriend = @mysql_fetch_array($sqlExe); 
                    $sqlFResult = $sqlFriend['count']; 
                    $sqlFstatus = $sqlFriend['id']; 
                    $sqlF="select count(*) FROM $news_tb_comment where entity_id=$id"; 
                    $sqlExe = $db->Query($sqlF); 
                    $sqlFriend = @mysql_fetch_array($sqlExe); 
                    $sqlFResult1 = $sqlFriend['count(*)']; 
                    
                    $comment=$sqlFResult1;if($comment>=1){$row1['Comment']=$comment;}else{$row1['Comment']="0";} 
                    $row1['LikeStatusID'] = $sqlFriend['id']; 
                    //if($sqlFResult>0){ 
                    //echo $row1['LikeStatusID']; 
                    if ($sqlFResult >= 1) { 
                        $row1['likecount'] = $sqlFResult; 
                    } else { 
                        $row1['likecount'] = "0"; 
                    } 
                    //$row1['likecount']=$sqlFResult; 
                    //} 
                    $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row1['ActionImage'], 'Comment' => $row1['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row1['statTxt'], 'Description' => $row1['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $sqlFstatus); 
                    $i++; 
                } else { 

                    $result[$i] = array('id' => $row1['id'], 'entityId' => $row1['entityId'], 'entityType' => $row1['entityType'], 'data' => $row1['data'], 'userId' => $row1['userId'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Time' => $dateNew, 'Profile_Pic' => $row1['Profile_Pic'], 'FriendID' => $row1['FriendID'], 'FriendName' => $row1['FriendName'], 'FriendSex' => $row1['FriendSex'], 'Friend_Pic' => $row1['Friend_Pic'], 'ActionImage' => $row1['ActionImage'], 'Comment' => $row1['Comment'], 'likecount' => $row1['likecount'], 'Title' => $row1['Title'], 'Description' => $row1['Description'], 'Comment_Id' => $row1['Comment_Id'], 'LikeStatusID' => $sqlFstatus); 
                    $i++; 
                } 
            } 
//print_r($result); 
            $final = array(); 
            // Assigning to array Ends here 
            if (is_array($result)) { 
                foreach ($result as $array) { 

                    array_push($final, $array); 
                } 
            } 
            $i = $i - 1; 
            $final = '{"Status":"Live","count": ' . $i . ',"result": ' . json_encode($final) . '}'; 
            echo str_replace("},]", "}]", $final); 
        } else { 
            echo '{"Message":"Session Expired"}'; 
        } 
    } 

    /*     * **********new API for news feed view latest ends******** 
/************new API for news feed view latest starts*************/
public function newsFeedViewold($pid,$skey)
	{
			mysql_query('SET CHARACTER SET utf8');
			$essence = new Essentials();
			$secure     =   new secure();
			$news_tb_action 	 =	$essence->tblPrefix().'newsfeed_action';
			$news_tb_feed 	     =	$essence->tblPrefix().'newsfeed_action_feed';
			$news_tb_like	     =	$essence->tblPrefix().'newsfeed_like';
			$pic_tbl			 =	$essence->tblPrefix().'profile_photo';
			$profile_tbl		 =	$essence->tblPrefix().'profile';
			$news_tb_like	     =	$essence->tblPrefix().'newsfeed_like';
			$news_tb_comment	 =	$essence->tblPrefix().'newsfeed_comment';
			$forum_topic         =  $essence->tblPrefix().'forum_topic';
			$forum_post          =  $essence->tblPrefix().'forum_post';
			$friend_list		 =	$essence->tblPrefix().'profile_friend_list';
            $profile_music		 =	$essence->tblPrefix().'profile_music';
            $photo_upload        =	$essence->tblPrefix().'profile_photo';
        	$event_add           =	$essence->tblPrefix().'event';
			$like_status         =	$essence->tblPrefix().'newsfeed_like';
			$blog_tbl			 =	$essence->tblprefix().'blog_post';
			$music_upload        =	$essence->tblPrefix().'profile_music';
			$group_tbl			 =	$essence->tblprefix().'group';
			$profile_cmnt_tbl	 =  $essence->tblprefix().'profile_comment';
			$event_tb_comment	 =	$essence->tblPrefix().'event_comment';
			$blog_tb_comment	 =	$essence->tblPrefix().'blog_post_comment';
			$music_tb_comment	 =	$essence->tblPrefix().'music_comment';
			$group_tb_comment	 =	$essence->tblPrefix().'group_comment';
			$photo_tb_comment	 =	$essence->tblPrefix().'photo_comment';
			$classified_tb_comment	 =	$essence->tblPrefix().'classified_comment';
			$video_tb_comment	 =	$essence->tblPrefix().'video_comment';
			$group_membe	 =	$essence->tblPrefix().'group_member';
			$video_tbl	      =	$essence->tblPrefix().'profile_video';
			 	
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db3 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db4 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db5 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db6 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db7 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db8 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db9 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
			$count1=15;
	 //Query strats here  
$sql   = "SELECT a.id,a.entityId,a.entityType,data,a.userId,p.username,p.sex,FROM_UNIXTIME(a.createTime,'%m-%d-%Y %h:%i:%s ') as Time,CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
         CAST( ptbl.photo_id AS CHAR ) , '_', CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic,f.friend_id as FriendID,(SELECT p.username FROM $profile_tbl p WHERE p.profile_id=f.friend_id)as FriendName,(SELECT p.sex FROM $profile_tbl p WHERE p.profile_id=f.friend_id)as FriendSex,CONCAT( '/','userfiles/thumb_', CAST( ptl.profile_id AS CHAR ) , '_',CAST( ptl.photo_id AS CHAR ) , '_', CAST( ptl.index AS CHAR ) , '.jpg' ) as Friend_Pic,CONCAT( '/','userfiles/thumb_', CAST( photo_upload.profile_id AS CHAR ) , '_',CAST( ptbl.photo_id AS CHAR ) , '_', CAST( ptbl.index AS CHAR ) , '.jpg' ) as ActionImage,(SELECT COUNT(*) FROM $news_tb_comment c WHERE c.entity_id= a.id) as Comment,(SELECT COUNT(*) FROM $news_tb_like l WHERE l.entityId = a.entityId and l.entityType=a.entityType) as likecount,pp.title as Title,t.text as Description,cmnt.id as Comment_Id,ls.id as LikeStatusID,(SELECT COUNT(*) FROM $video_tb_comment c WHERE c.entity_id= a.entityId) as VComment,v.title as vtitle,v.privacy_status,v.description as vdescription FROM $news_tb_action a
					  JOIN $profile_tbl p ON (p.profile_id=a.userId)
					  LEFT JOIN $pic_tbl ptbl ON (ptbl.profile_id = a.userId and ptbl.number=0)
					  LEFT JOIN $friend_list f ON (f.friendship_id=a.entityId and f.profile_id=a.userId and a.entityType='friend_add')
					  LEFT JOIN $pic_tbl photo_upload ON (photo_upload.profile_id = a.userId and photo_upload.added_stamp=a.createTime and a.entityType='photo_upload')
					 LEFT JOIN $forum_topic  pp ON (pp.forum_topic_id=a.entityId and pp.profile_id=a.userId and a.entityType='forum_add_topic')
         			  LEFT JOIN $forum_post t ON (t.forum_topic_id=pp.forum_topic_id AND t.profile_id=a.userId and a.entityType='forum_add_topic')
					  LEFT JOIN $news_tb_comment cmnt ON (cmnt.entity_id = a.entityId and cmnt.create_time_stamp=a.createTime)
					  LEFT JOIN $pic_tbl ptl ON (ptl.profile_id = f.friend_id and ptl.number=0)
					  LEFT JOIN $news_tb_like ls ON (ls.entityId=a.entityId and ls.userId=$pid and ls.entityType=a.entityType)
					  LEFT JOIN $video_tbl v ON (v.upload_stamp=a.createTime and v.profile_id=a.userId)
					  WHERE a.status='active' ORDER BY a.id DESC LIMIT 0,$count1
					  ";
					//echo $sql;
					 //(SELECT COUNT(*) FROM $video_tb_comment c WHERE c.entity_id= a.id) as Comment,//JOIN $video_tb_comment p ON (p.entity_id=a.entityId)
 $res1=$db->Query($sql);

// $sample_status;
 //event add
 $sql2   = "SELECT a.id,a.entityId,a.entityType,event_add.image as ActionImage,event_add.title as Title,event_add.description as Description,(SELECT COUNT(*) FROM $event_tb_comment c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a
					  JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
					  LEFT JOIN $event_add  event_add ON (event_add.profile_id = a.userId and event_add.create_date=a.createTime and a.entityType='event_add')					  
					  WHERE a.status='active' ORDER BY createTime DESC LIMIT 0,$count1
					  ";
 $res2=$db2->Query($sql2);
 
 //bloga post
 $sql3   = "SELECT a.id,a.entityId,a.entityType,blog_tbl.title as Title,blog_tbl.preview_text as Description,(SELECT COUNT(*) FROM 	$blog_tb_comment c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a
					  JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
					  LEFT JOIN $blog_tbl  blog_tbl ON (blog_tbl.profile_id = a.userId and blog_tbl.create_time_stamp=a.createTime and a.entityType='blog_post_add')					  
					  WHERE a.status='active' ORDER BY createTime DESC LIMIT 0,$count1
					  ";
 $res3=$db3->Query($sql3);
 
 //music upload
 $sql4   = "SELECT a.id,a.entityId,a.entityType,music_upload.title as Title,music_upload.description as Description,(SELECT COUNT(*) FROM $music_tb_comment c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a
					  JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
					  LEFT JOIN $music_upload  music_upload ON (music_upload.profile_id = a.userId and music_upload.upload_stamp=a.createTime and a.entityType='music_upload')					  
					  WHERE a.status='active' ORDER BY createTime DESC LIMIT 0,$count1
					  ";
 $res4=$db4->Query($sql4);
 
 //gropu add
 $sql5   = "SELECT a.id,a.entityId,a.entityType,group_tbl.title as Title,CONCAT( '/','userfiles/thumb_group_', CAST( group_tbl.group_id AS CHAR ), '_',CAST( group_tbl.photo AS CHAR ) , '.jpg' ) as ActionImage,group_tbl.description as Description,(SELECT COUNT(*) FROM $group_tb_comment c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a
					  JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
					  LEFT JOIN $group_tbl  group_tbl ON (group_tbl.owner_id = a.userId and group_tbl.creation_stamp=a.createTime and (a.entityType='group_add' or a.entityType='group_join'))					  
					  WHERE a.status='active' ORDER BY createTime DESC LIMIT 0,$count1
					  ";
					  
 $res5=$db5->Query($sql5);
 //profile comment table
 $sql6	= "SELECT a.id,a.entityId,a.entityType,profile_cmnt_tbl.text as Title,(SELECT COUNT(*) FROM $profile_cmnt_tbl c WHERE c.entity_id= a.entityId) as Comment FROM $news_tb_action a
		   JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
		   LEFT JOIN $profile_cmnt_tbl  profile_cmnt_tbl ON (profile_cmnt_tbl.entity_id = a.entityId and profile_cmnt_tbl.author_id=a.userId and  profile_cmnt_tbl.create_time_stamp=a.createTime)					  
WHERE a.status='active' ORDER BY createTime DESC LIMIT 0,$count1";				  
$res6 = $db6->Query($sql6);

//photo upload
$sql7	= "SELECT a.id,a.entityId,a.entityType,CONCAT( '/','userfiles/thumb_', CAST( photo_upload.profile_id AS CHAR ) , '_',CAST( photo_upload.photo_id AS CHAR ) , '_', CAST( photo_upload.index AS CHAR ) , '.jpg' ) as ActionImage,(SELECT COUNT(*) FROM $photo_tb_comment c WHERE c.entity_id= a.entityId) as Comment,photo_upload.publishing_status,a.userId FROM $news_tb_action a
		   JOIN $profile_tbl p ON (p.profile_id=a.userId)					  
		   LEFT JOIN $photo_upload  photo_upload ON (photo_upload.profile_id=a.userId and  photo_upload.added_stamp=a.createTime )					  
WHERE a.status='active' ORDER BY a.id DESC LIMIT 0,$count1";
//echo $sql7;
$res7 = $db7->Query($sql7);

//classified
$sql8	= "SELECT (SELECT COUNT(*) FROM $classified_tb_comment c WHERE c.entity_id= a.id) as Comment FROM $news_tb_action a
		   JOIN $classified_tb_comment p ON (p.entity_id=a.entityId)					  
WHERE a.status='active' ORDER BY createTime DESC LIMIT 0,$count1";

//echo $sql7;
$res8 = $db8->Query($sql8);
//media upload


 $count=$db2->RowCount();
 $i=1;
		// Query Ends here		
		// Assigning to array Starts here  
			while($row1 = @mysql_fetch_array($res1) and $row2 = @mysql_fetch_array($res2) and $row3 = @mysql_fetch_array($res3)and $row4 = @mysql_fetch_array($res4) and $row5 = @mysql_fetch_array($res5) and $row6 = @mysql_fetch_array($res6) and $row7 = @mysql_fetch_array($res7) or $row8 = @mysql_fetch_array($res8) )
			{
				if ($row2['entityType']=='event_add' )
				{
					$result[$i] =array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'], 'FriendSex'=>$row1['FriendSex'],'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row2['ActionImage'], 'Comment'=>$row2['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row2['Title'], 'Description'=>$row2['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
					$i++;
				}
				else if($row3['entityType']=='blog_post_add' )
				{
					$result[$i] =array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>'null', 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'], 'FriendSex'=>$row1['FriendSex'],'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row1['ActionImage'], 'Comment'=>$row3['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row3['Title'], 'Description'=>$row3['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
					$i++;
				}
				else if ($row4['entityType']=='music_upload' )
				{
					$result[$i] =array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'], 'FriendSex'=>$row1['FriendSex'],'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row2['ActionImage'], 'Comment'=>$row4['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row4['Title'], 'Description'=>$row4['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
					$i++;
				}
				else if($row5['entityType']=='group_add' or $row5['entityType']=='group_join')
				{
					if($row5['entityType']=='group_join')
					{
						$sqlgj ="SELECT group_id FROM $group_membe WHERE member_id=$row1[userId] AND group_id=$row1[entityId]";
				$commentr = $db8->Query($sqlgj);
				$commentr_res =mysql_fetch_array($commentr);
				$user_name = $commentr_res['group_id'];
				$sqlgjj ="SELECT title,description FROM $group_tbl WHERE group_id=$user_name";
				$commentr1 = $db8->Query($sqlgjj);
				$commentr_res1 =mysql_fetch_array($commentr1);
				$title = $commentr_res1['title'];
				$description=$commentr_res1['description'];
				$result[$i] =array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'], 'FriendSex'=>$row1['FriendSex'],'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row5['ActionImage'], 'Comment'=>$row5['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$title, 'Description'=>$description,  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
						$i++;
					}
					else if($row5['entityType']=='group_add')
					{
						$result[$i] =array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'], 'FriendSex'=>$row1['FriendSex'],'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row5['ActionImage'], 'Comment'=>$row5['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row5['Title'], 'Description'=>$row5['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
						$i++;
					}
				}
				else if($row6['entityType']=='profile_comment')
				{
				$sql8 ="SELECT username FROM $profile_tbl WHERE profile_id=$row1[entityId]";
				$commentr = $db->Query($sql8);
				$commentr_res =mysql_fetch_array($commentr);
				$user_name = $commentr_res['username'];
						$result[$i] =array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['entityId'], 'FriendName'=>$user_name, 'FriendSex'=>$row1['FriendSex'],'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row1['ActionImage'], 'Comment'=>$row6['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row6['Title'], 'Description'=>$row1['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
						$i++;
				}
				else if($row7['entityType']=='photo_upload' or $row7['entityType']=='profile_avatar_change')
				{
						if($row7['publishing_status'] == 'public')
						{
						$result[$i] =array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'], 'FriendSex'=>$row1['FriendSex'],'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row7['ActionImage'], 'Comment'=>$row7['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row1['Title'], 'Description'=>$row1['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
						$i++;
						}
						else if($row7['publishing_status'] == 'friends_only')
						{
						$userid = $row7['userId'];
						$profileId = $pid;
						$sqlF ="SELECT id FROM $friend_list WHERE profile_id='$profileId' and friend_id='$userid'";
						$sqlExe = $db->Query($sqlF);
						$sqlFriend = mysql_fetch_array($sqlExe);
						$sqlFResult = $sqlFriend['id'];
							if($sqlFResult != NULL)
							{
								$result[$i] =array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'], 'FriendSex'=>$row1['FriendSex'],'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row7['ActionImage'], 'Comment'=>$row7['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row1['Title'], 'Description'=>$row1['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
						$i++;
							}
							else
							{
							$result[$i] =array('id'=>NULL,'entityId'=>NULL, 'entityType'=>NULL,'data'=>NULL, 'userId'=>NULL, 'username'=>NULL, 'sex'=>NULL,'Time'=>NULL, 'Profile_Pic'=>NULL, 'FriendID'=>NULL, 'FriendName'=>NULL, 'FriendSex'=>NULL,'Friend_Pic'=>NULL, 'ActionImage'=>NULL, 'Comment'=>NULL, 'likecount'=>NULL, 'Title'=>NULL, 'Description'=>NULL,  'Comment_Id'=>NULL,'LikeStatusID'=>NULL);
						$i;
		
							}
						
						}
                                               else
                                                {
                                               $result[$i] =array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'], 'FriendSex'=>$row1['FriendSex'],'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row7['ActionImage'], 'Comment'=>$row7['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row1['Title'], 'Description'=>$row1['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
						$i++;
                                               }
						
				}
				else if($row7['entityType']=='post_classified_item')
				{
				
						$result[$i] = array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'],'FriendSex'=>$row1['FriendSex'], 'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row1['ActionImage'], 'Comment'=>$row8['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row1['Title'], 'Description'=>$row1['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
				 		$i++;
				}
				else if($row7['entityType']=='media_upload')
				{
					if ($row1['privacy_status']=='public'){
					//$db10 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

						$result[$i] = array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'],'FriendSex'=>$row1['FriendSex'], 'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row1['ActionImage'], 'Comment'=>$row1['VComment'], 'likecount'=>$row1['likecount'], 'Title'=>$row1['vtitle'], 'Description'=>$row1['vdescription'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
				 	//print_r($result);
						$i++;
					}
					else if($row1['privacy_status'] == 'friends_only')
						{
						$userid = $row7['userId'];
						$profileId = $pid;
						$sqlF ="SELECT id FROM $friend_list WHERE profile_id='$profileId' and friend_id='$userid'";
						$sqlExe = $db->Query($sqlF);
						$sqlFriend = mysql_fetch_array($sqlExe);
						$sqlFResult = $sqlFriend['id'];
							if($sqlFResult != NULL)
							{
								$result[$i] = array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'],'FriendSex'=>$row1['FriendSex'], 'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row1['ActionImage'], 'Comment'=>$row1['VComment'], 'likecount'=>$row1['likecount'], 'Title'=>$row1['vtitle'], 'Description'=>$row1['vdescription'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
						$i++;
							}
						}
					else
					{
						$i;
					}
				}
				else if($row7['entityType']=='status_update')
				{
					$id=$row1['id'];
					$type=$row1['entityType'];
					
				$sqlF ="SELECT id,(SELECT COUNT(*) FROM $news_tb_like ll WHERE ll.entityId ='$id' and ll.entityType='$type') AS count FROM $news_tb_like l WHERE l.entityId ='$id' and l.entityType='$type' and l.userId='$pid'";
						$sqlExe = $db->Query($sqlF);
						$sqlFriend = @mysql_fetch_array($sqlExe);
						$sqlFResult = $sqlFriend['count'];
						$row1['LikeStatusID']=$sqlFriend['id'];
						//if($sqlFResult>0){
						//echo $row1['LikeStatusID'];
						if($sqlFResult>=1){$row1['likecount']=$sqlFResult;}else {$row1['likecount']="0";}
						//$row1['likecount']=$sqlFResult;
						//}
						$result[$i] = array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'],'FriendSex'=>$row1['FriendSex'], 'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row1['ActionImage'], 'Comment'=>$row1['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row1['Title'], 'Description'=>$row1['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
				 		$i++;
				}
				else 
				{
				
						$result[$i] = array('id'=>$row1['id'],'entityId'=>$row1['entityId'], 'entityType'=>$row1['entityType'],'data'=>$row1['data'], 'userId'=>$row1['userId'], 'username'=>$row1['username'], 'sex'=>$row1['sex'],'Time'=>$row1['Time'], 'Profile_Pic'=>$row1['Profile_Pic'], 'FriendID'=>$row1['FriendID'], 'FriendName'=>$row1['FriendName'],'FriendSex'=>$row1['FriendSex'], 'Friend_Pic'=>$row1['Friend_Pic'], 'ActionImage'=>$row1['ActionImage'], 'Comment'=>$row1['Comment'], 'likecount'=>$row1['likecount'], 'Title'=>$row1['Title'], 'Description'=>$row1['Description'],  'Comment_Id'=>$row1['Comment_Id'],'LikeStatusID'=>$row1['LikeStatusID']);
				 		$i++;
				}
			}

//print_r($result);
		$final = array();
		// Assigning to array Ends here
if (is_array($result))
{
	foreach($result as $array)
	{
		
			array_push($final, $array);
	}
}	
			$i=$i-1;
			$final	=	 '{"Status":"Live","count": '.$i.',"result": '.json_encode($final).'}';
						echo str_replace("},]","}]",$final);
	}
	else
	{
		echo '{"Message":"Session Expired"}';
	}
}

/************new API for news feed view latest ends********
/***********new API for news feed fetch option starts here**************/
public function fetchNewsFeed($fid,$pid,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$news_tb_action 	 =	$essence->tblPrefix().'newsfeed_action';
			$news_tb_feed 	 =	$essence->tblPrefix().'newsfeed_action_feed';
			$news_tb_like	 =	$essence->tblPrefix().'newsfeed_like';
			$pic_tbl			=	$essence->tblPrefix().'profile_photo';
			$profile_tbl			=	$essence->tblPrefix().'profile';
			$news_tb_like	 =	$essence->tblPrefix().'newsfeed_like';
			$news_tb_comment	 =	$essence->tblPrefix().'newsfeed_comment';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
			
		/*	$sql1	= "SELECT * FROM $news_tb_action a
					   INNER JOIN $news_tb_feed f ON a.id = f.actionId 
					   WHERE status='active' AND userId='$id'";
				*/
				   
				   
		   	$sql	= "SELECT entityType,username,FROM_UNIXTIME(createTime,'%Y-%m-%d %h:%i ') as createTime,CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
					   CAST( ptbl.photo_id AS CHAR ) , '_',CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic 
					   FROM $news_tb_action a
					   INNER JOIN $news_tb_feed f ON (a.id = f.actionId) 
					   LEFT JOIN $profile_tbl p ON (a.userId = p.profile_id)
					   LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0)
					   WHERE a.status='active' AND a.id='$fid'";
					   
		    $db->Query($sql);
			if($db->RowCount())
					{	
						$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace("},]","}]",$stri);
					}
					else
					{
						echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
					
					}
			}
			else
			{
				echo '{"Message":"Session Expired"}';
			}		
	}

/***********new API for news feed fetch option ends here**************/

/*******new API for news feed like option starts here**********/
public function newsLike($eid,$pid,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$news_tb_like	 =	$essence->tblPrefix().'newsfeed_like';
			$news_tb_action 	 =	$essence->tblPrefix().'newsfeed_action';

			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
			$time = time();
			$entityId = $eid;
			$pid   = $pid;
			$sqlEnt = "SELECT entityId, entityType FROM $news_tb_action WHERE id='$eid'";
			$sqlEntity = $db->Query($sqlEnt);
			$entityType = mysql_fetch_array($sqlEntity);
			$entityTypeResult = $entityType['entityType'];
			$entityIdResult=$entityType['entityId'];
			//echo $sqlEnt;
			//$entityIdResult = $entityType['entityId'];
			if ($entityTypeResult=='status_update')
			{
			$sqlL = "SELECT * FROM $news_tb_like WHERE entityId = '$eid' AND entityType= '$entityTypeResult' AND userId ='$pid'";
		    $db->Query1($sqlL);
			$likeCount = $db->RowCount();
			if($likeCount == NULL)
				{
				$flag = 'N';
				$sql    = "INSERT INTO $news_tb_like VALUES ('','$entityTypeResult','$eid','$pid','$time')";
					if($db->Query1($sql))
						{
								echo '{"Status":"Live","Message": "Success"}';
						}
						else
						{
								echo '{"Status":"Live","Message": "Failure"}';
							
						}
					 
			 	}	
			 	else
			 	{
			 	 echo  '{"Status":"Live","count": 0,"result":"Already liked","flag":"S"}';
			 	} 
			}
			else
			{
			$sqlL = "SELECT * FROM $news_tb_like WHERE entityId = '$entityIdResult' AND entityType= '$entityTypeResult' AND userId ='$pid'";
		    $db->Query1($sqlL);
			$likeCount = $db->RowCount();
			if($likeCount == NULL)
				{
				$flag = 'N';
				$sql    = "INSERT INTO $news_tb_like VALUES ('','$entityTypeResult','$entityIdResult','$pid','$time')";
					if($db->Query1($sql))
						{
								echo '{"Status":"Live","Message": "Success"}';
						}
						else
						{
								echo '{"Status":"Live","Message": "Failure"}';
							
						}
					 
			 	}	
			 	else
			 	{
			 	 echo  '{"Status":"Live","count": 0,"result":"Already liked","flag":"S"}';
			 	} 	
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
	}

/*******new API for news feed like opion ends here**************/
/*******new API for news feed unlike opion starts here**************/

public function newsUnLike($eid,$pid,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$news_tb_like	 =	$essence->tblPrefix().'newsfeed_like';
			$event_tbl	 =	$essence->tblPrefix().'event';
			$news_tb_action 	 =	$essence->tblPrefix().'newsfeed_action';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
			$time = time();
			$pid 	= $pid;
			$eid	= $eid;
			$sqlEnt = "SELECT entityId, entityType FROM $news_tb_action WHERE id='$eid'";
			$sqlEntity = $db->Query1($sqlEnt);
			$entityType = mysql_fetch_array($sqlEntity);
			$entityTypeResult = $entityType['entityType'];
			$entityIdResult=$entityType['entityId'];
			if ($entityTypeResult=='status_update')
			{
			$sqlU = "SELECT id FROM $news_tb_like WHERE entityId='$eid' AND entityType='$entityTypeResult' AND userId = '$pid'";
			$resExe =$db->Query($sqlU);
			$resRes = mysql_fetch_array($resExe);
			$res = $resRes['id'];
			//$res = $db->RowCount();
			//$res = mysql_num_rows($resExe);
			if($res != NULL)
			{
				$flag = 'N';
				$sql    = "DELETE FROM $news_tb_like WHERE entityId='$eid' AND entityType='$entityTypeResult' AND userId = '$pid'";
				if($db->Query($sql))
				{
					echo '{"Status":"Live","Message": "Success"}';
				}
			    else
				{
					echo '{"Status":"Live","Message": "Failure"}';
				
				}
				
			}
			else
			{
				$flag = 'S';
				echo '{"Status":"Live","Message": "Already UnLiked","Flag":"S"}';

			}
		}
		else
		{
			$sqlU = "SELECT id FROM $news_tb_like WHERE entityId='$entityIdResult' AND entityType='$entityTypeResult' AND userId = '$pid'";
			$resExe =$db->Query($sqlU);
			$resRes = mysql_fetch_array($resExe);
			$res = $resRes['id'];
			if($res != NULL)
			{
				$flag = 'N';
				$sql    = "DELETE FROM $news_tb_like WHERE entityId='$entityIdResult' AND entityType='$entityTypeResult' AND userId = '$pid'";
				if($db->Query($sql))
				{
					echo '{"Status":"Live","Message": "Success"}';
				}
			    else
				{
					echo '{"Status":"Live","Message": "Failure"}';
				
				}
				
			}
			else
			{
				$flag = 'S';
				echo '{"Status":"Live","Message": "Already UnLiked","Flag":"S"}';

			}
		}
			}
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
			
	}

/***********new API for news feed unlike option ends here**************/
/***********new API for news feed comment option starts here**************/


public function addNewsComment($pid,$eid,$text,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$news_tb_action 	 =	$essence->tblPrefix().'newsfeed_action';
			$news_tb_like	     =	$essence->tblPrefix().'newsfeed_like';
			$news_tb_comment	 =	$essence->tblPrefix().'newsfeed_comment';
			$blog_tb_comment	 =	$essence->tblPrefix().'blog_post_comment';
			$photo_tb_comment	 =	$essence->tblPrefix().'photo_comment';
			$profile_tb_comment	 =	$essence->tblPrefix().'profile_comment';
			$event_tb_comment	 =	$essence->tblPrefix().'event_comment';
			$group_tb_comment	 =	$essence->tblPrefix().'group_comment';
			$music_tb_comment	 =	$essence->tblPrefix().'music_comment';
			$video_tb_comment	 =	$essence->tblPrefix().'video_comment';
			$classified_tb_comment	 =	$essence->tblPrefix().'classified_comment';
				
			$author_id = $pid;
			$text = $text;
			$time = time();
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			
			$sql0 = "SELECT entityId,entityType FROM $news_tb_action WHERE id=$eid";
			
			$sql0Res = $db->Query1($sql0);
			$sql0Exe = @mysql_fetch_array($sql0Res);
			 $entity_id = $sql0Exe['entityId'];
			 $entityType = $sql0Exe['entityType'];
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
 $sqlChk = "SELECT 'entityType' $news_tb_comment limit 1";
				$sqlChkRes = $db->Query($sqlChk);
$dd=$db->RowCount();
//echo $dd;
				//$sqlChkColumn = mysql_fetch_array($sqlChkRes);
				// $sqlChkColRes = $sqlChkColumn['entityType'];
//echo $sqlChkColRes;
		if($dd==0)	
{			

if($entityType == 'photo_upload')
				{

			 	$sql = "INSERT INTO $photo_tb_comment VALUES ('','$author_id','$text','$entity_id','$time','','')";
				}
				else if($entityType == 'profile_avatar_change')
				{
					$sql = "INSERT INTO $news_tb_comment VALUES ('','$author_id','$text','$eid','$time','','')";
				}
				else if($entityType == 'blog_post_add')
				{
				$sql = "INSERT INTO $blog_tb_comment VALUES ('','$author_id','$text','$entity_id','$time','','')";
				}
				else if($entityType == 'profile_comment')
				{
				$sql = "INSERT INTO $profile_tb_comment VALUES ('','$author_id','$text','$entity_id','$time','','')";
				}	
				else if($entityType == 'event_add' or $entityType == 'event_attend')
				{
				$sql = "INSERT INTO $profile_tb_comment VALUES ('','$author_id','$text','$entity_id','$time','','')";
				}
				else if($entityType == 'group_add' or $entityType == 'group_join')
				{
				$sql = "INSERT INTO $group_tb_comment VALUES ('','$author_id','$text','$entity_id','$time','','')";
				}
				else if($entityType == 'music_upload')
				{
				$sql = "INSERT INTO $music_tb_comment VALUES ('','$author_id','$text','$entity_id','$time','','')";
				}
				else if($entityType == 'media_upload')
				{
				$sql = "INSERT INTO $video_tb_comment VALUES ('','$author_id','$text','$entity_id','$time','','')";
				}
				else if($entityType == 'post_classifieds_item')
				{
				$sql = "INSERT INTO $classified_tb_comment VALUES ('','$author_id','$text','$entity_id','$time','','')";
				}
				else
				{
				$sql = "INSERT INTO $news_tb_comment VALUES ('','$author_id','$text','$eid','$time','','')";
				}
}
else
{
if($entityType == 'photo_upload' or $entityType == 'profile_avatar_change')
				{

			 	$sql = "INSERT INTO $photo_tb_comment (id,author_id,text,entity_id,create_time_stamp,update_time_stamp,update_author_id,entityType) VALUES ('','$author_id','$text','$entity_id','$time','','','$entityType')";
				}
				else if($entityType == 'blog_post_add')
				{
				$sql = "INSERT INTO $blog_tb_comment (id,author_id,text,entity_id,create_time_stamp,update_time_stamp,update_author_id,entityType) VALUES ('','$author_id','$text','$entity_id','$time','','','$entityType')";
				}
				else if($entityType == 'profile_comment')
				{
				$sql = "INSERT INTO $profile_tb_comment (id,author_id,text,entity_id,create_time_stamp,update_time_stamp,update_author_id,entityType) VALUES ('','$author_id','$text','$entity_id','$time','','','$entityType')";
				}	
				else if($entityType == 'event_add' or $entityType == 'event_attend')
				{
				$sql = "INSERT INTO $profile_tb_comment (id,author_id,text,entity_id,create_time_stamp,update_time_stamp,update_author_id,entityType) VALUES ('','$author_id','$text','$entity_id','$time','','','$entityType')";
				}
				else if($entityType == 'group_add' or $entityType == 'group_join')
				{
				$sql = "INSERT INTO $group_tb_comment (id,author_id,text,entity_id,create_time_stamp,update_time_stamp,update_author_id,entityType) VALUES ('','$author_id','$text','$entity_id','$time','','','$entityType')";
				}
				else if($entityType == 'music_upload')
				{
				$sql = "INSERT INTO $music_tb_comment (id,author_id,text,entity_id,create_time_stamp,update_time_stamp,update_author_id,entityType) VALUES ('','$author_id','$text','$entity_id','$time','','','$entityType')";
				}
				else if($entityType == 'media_upload')
				{
				$sql = "INSERT INTO $video_tb_comment (id,author_id,text,entity_id,create_time_stamp,update_time_stamp,update_author_id,entityType) VALUES ('','$author_id','$text','$entity_id','$time','','','$entityType')";
				}
				else if($entityType == 'post_classifieds_item')
				{
				$sql = "INSERT INTO $classified_tb_comment (id,author_id,text,entity_id,create_time_stamp,update_time_stamp,update_author_id,entityType) VALUES ('','$author_id','$text','$entity_id','$time','','','$entityType')";
				}
				else
				{
				$sql = "INSERT INTO $news_tb_comment (id,author_id,text,entity_id,create_time_stamp,update_time_stamp,update_author_id,entityType) VALUES ('','$author_id','$text','$eid','$time','','','$entityType')";
				}
}
$time = time();
   $dateNew = date("m-d-Y H:i:s", $time); 
//echo $sql; 
            if ($db->Query1($sql)) { 
            	$sql="UPDATE $news_tb_action SET updateTime=$time where id='$eid'"; 
            	$db->Query($sql); 
                echo '{"Status":"Live","Message": "Success","Time":"'.$dateNew.'"}'; 
            } else { 
                echo '{"Status":"Live","Message": "Failure","Time":"'.$dateNew.'"}'; 
            } 
        } else { 
            echo '{"Message":"Session Expired"}'; 
        } 
    } 




/***********new API for news feed comment option ends here**************/
/***********new API for news feed comment view option starts here**************/
 public function viewNewsComment($eid, $pid, $skey) { 
    	mysql_query('SET CHARACTER SET utf8'); 
        $essence = new Essentials(); 
        $secure = new secure(); 
        $news_tb_action = $essence->tblPrefix() . 'newsfeed_action'; 
        $news_tb_like = $essence->tblPrefix() . 'newsfeed_like'; 
        $news_tb_comment = $essence->tblPrefix() . 'newsfeed_comment'; 
        $profile_tbl = $essence->tblPrefix() . 'profile'; 
        $pic_tbl = $essence->tblPrefix() . 'profile_photo'; 
        $blog_tb_comment = $essence->tblPrefix() . 'blog_post_comment'; 
        $photo_tb_comment = $essence->tblPrefix() . 'photo_comment'; 
        $profile_tb_comment = $essence->tblPrefix() . 'profile_comment'; 
        $event_tb_comment = $essence->tblPrefix() . 'event_comment'; 
        $group_tb_comment = $essence->tblPrefix() . 'group_comment'; 
        $music_tb_comment = $essence->tblPrefix() . 'music_comment'; 
        $video_tb_comment = $essence->tblPrefix() . 'video_comment'; 
        $classified_tb_comment = $essence->tblPrefix() . 'classified_comment'; 

        $time = time(); 
        $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
        //check user sign in or not 
        $res = $secure->CheckSecure($pid, $skey); 
        //$res=1; 
        if ($res == 1) { 
            $sql0 = "SELECT entityId,entityType FROM $news_tb_action WHERE id=$eid"; 
            $res = $db->Query1($sql0); 
            $exe = @mysql_fetch_array($res); 
            $entityID = $exe['entityId']; 
            $entityType = $exe['entityType']; 
            if ($entityType == 'photo_upload') { 
                $sql = "SELECT p.profile_id,username,sex,text,create_time_stamp,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $photo_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$entityID' ORDER BY create_time_stamp DESC"; 
            } else if ($entityType == 'blog_post_add') { 
                $sql = "SELECT p.profile_id,username,sex,text,create_time_stamp,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $blog_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$entityID' ORDER BY create_time_stamp DESC"; 
            } else if ($entityType == 'profile_comment') { 
                $sql = "SELECT p.profile_id,username,sex,text,create_time_stamp,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $profile_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$entityID' ORDER BY create_time_stamp DESC"; 
            } else if ($entityType == 'event_add' or $entityType == 'event_attend') { 
                $sql = "SELECT p.profile_id,username,sex,text,create_time_stamp,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $profile_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$entityID' ORDER BY create_time_stamp DESC"; 
            } else if ($entityType == 'group_add' or $entityType == 'group_join') { 
                $sql = "SELECT p.profile_id,username,sex,text,create_time_stamp,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $group_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$entityID' ORDER BY create_time_stamp DESC"; 
            } else if ($entityType == 'music_upload') { 
                $sql = "SELECT p.profile_id,username,sex,text,create_time_stamp,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $music_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$entityID' ORDER BY create_time_stamp DESC"; 
            } else if ($entityType == 'media_upload') { 
                $sql = "SELECT p.profile_id,username,sex,text,create_time_stamp,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $video_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$entityID' ORDER BY create_time_stamp DESC"; 
            } else if ($entityType == 'post_classifieds_item') { 
                $sql = "SELECT p.profile_id,username,sex,text,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $classified_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$entityID' ORDER BY create_time_stamp DESC"; 
            }else if ($entityType == 'status_update') { 
                $sql = "SELECT p.profile_id,username,sex,text,create_time_stamp,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $news_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$eid' ORDER BY create_time_stamp DESC"; 
            } else { 
                $sql = "SELECT p.profile_id,username,sex,text,create_time_stamp,FROM_UNIXTIME(create_time_stamp,'%c-%d-%Y %h:%i:%s') as create_time, 
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_', 
					CAST( ptbl.photo_id AS CHAR ) , '_', 
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $news_tb_comment c 
			        LEFT JOIN $profile_tbl p ON (c.author_id = p.profile_id) 
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0) 
					WHERE entity_id ='$entityID' ORDER BY create_time_stamp DESC"; 
            } 


            $i=0; 
            $res = $db->Query($sql); 
            while ($row = mysql_fetch_array($res)) { 
            	$dateNew = date("m-d-Y H:i:s", $row['create_time_stamp']); 
            	if($row['profile_id']=="null" OR $row['profile_id']=='' OR $row['profile_id']==NULL) 
            	{$row['profile_id']=0;$row['username']='username';$row['sex']=0;} 
                $result[$i] = array('profile_id' => $row['profile_id'], 'username' => $row['username'], 'sex' => $row['sex'], 'text' => $row['text'], 'create_time' => $dateNew, 'Profile_Pic' => $row['Profile_Pic']); 
            $i++; 
            
            } 
            $final = array(); 
            foreach ($result as $array) { 

                array_push($final, $array); 
            } 
            $final = '{"Status":"Live","count": ' . $i . ',"result": ' . json_encode($final) . '}'; 
            echo str_replace("},]", "}]", $final); 
            
            
            
            
         /*   if ($db->Query($sql)) { 
                if ($db->RowCount()) { 
                    $stri = '{"Status":"Live","count": ' . $db->RowCount() . ',"result": [' . $db->GetJSON() . ']}'; 
                    echo str_replace("},]", "}]", $stri); 
                } else { 
                    echo '{"Status":"Live","count": 0,"result": [' . $db->GetJSON() . ']}'; 
                } 
            }*/ 
        } else { 
            echo '{"Message":"Session Expired"}'; 
        } 
    } 

/***********new API for news feed comment view option ends here**************/
/***********new API for news feed comment delete option starts here**************/
public function deleteNewsComment($id,$pid,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$news_tb_action 	 =	$essence->tblPrefix().'newsfeed_action';
			$news_tb_like	 =	$essence->tblPrefix().'newsfeed_like';
			$news_tb_comment	 =	$essence->tblPrefix().'newsfeed_comment';
			
			
			$time = time();
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
			$sql0 = "SELECT * FROM $news_tb_comment WHERE id = '$id'";
			$result = $db->Query($sql0); 
			$resultSql0 = mysql_fetch_array($result);
			if($resultSql0!=NULL)
			{
			$sql = "DELETE FROM $news_tb_comment WHERE id = '$id'";
			$resQuery = $db->Query($sql);
				if($resQuery)
				{	
					echo '{"Status":"Live","Message":"Success"}';
				}
				else
				{
					echo '{"Status":"Live","count":"Failure"}';
					
				}	
			}
			else
			{
					echo '{"Status":"Live","count":"Failure"}';

			}	
		}
		else //session expired
			{
				echo '{"Message":"Session Expired"}';
	         }	
	
	}


/***********new API for news feed comment delete option ends here**************/
/***********new API for classifieds  view  starts here**************/
public function viewClassifieds($id)
{
		$essence = new Essentials();
		$classified_item_tbl 	 =	$essence->tblPrefix().'classifieds_item';
		$classified_group_tbl 	 =	$essence->tblPrefix().'classifieds_group';
		$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			$sql	= "SELECT f.profile_id,f.title,f.description,f.create_stamp,f.start_stamp,f.end_stamp FROM $classified_item_tbl f
					   JOIN $classified_group_tbl g ON (g.id = f.group_id)
					   WHERE f.id='$id'";
		    $db->Query($sql);
			if($db->RowCount())
					{	
						$stri	=	 '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace("},]","}]",$stri);
					}
					else
					{
						echo '{"count": 0,"result": ['.$db->GetJSON().']}';
					
					}
			
}

/***********new API for classifieds  ends here**************/
/***********new API for classified comment add starts here**************/
public function addClassifiedComment($pid,$eid,$text)
	{
			$essence = new Essentials();
			$classified_comment_tbl 	 =	$essence->tblPrefix().'classifieds_comment';
			$author_id = $pid;
			$text = $text;
			$entity_id = $eid;
			$time = time();
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$sql = "INSERT INTO $classified_comment_tbl VALUES ('','$author_id','$text','$entity_id','$time','','')";
			if($db->Query($sql))
			{
				if($db->RowCount())
					{	
						$stri	=	 '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace("},]","}]",$stri);
					}
					else
					{
						echo '{"count": 0,"result": ['.$db->GetJSON().']}';
					
					}
			}
	
	}

/***********new API for classified comment add  ends here**************/
/***********new API for classified comment view  starts here**************/
public function viewClassifiedComment($pid)
	{
			$essence = new Essentials();
			$classified_comment_tbl 	 =	$essence->tblPrefix().'classifieds_comment';
			$author_id = $pid;
			$time = time();
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$sql = "SELECT author_id,text,entity_id,create_time_stamp FROM $classified_comment_tbl WHERE author_id='$pid'";
			$db->Query($sql);
				if($db->RowCount())
					{	
						$stri	=	 '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace("},]","}]",$stri);
					}
					else
					{
						echo '{"count": 0,"result": ['.$db->GetJSON().']}';
					
					}
			
	
	}

/***********new API for classified comment view  ends here**************/

/***********new API for classified comment delete starts here**************/
public function deleteClassifiedComment($id)
	{
			
			$essence = new Essentials();
			$classified_comment_tbl 	 =	$essence->tblPrefix().'classifieds_comment';
			$time = time();
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$sql = "DELETE FROM $classified_comment_tbl WHERE id='$id'";
			if($db->Query($sql))
			{
				if($db->RowCount())
					{	
						$stri	=	 '{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace("},]","}]",$stri);
					}
					else
					{
						echo '{"count": 0,"result": ['.$db->GetJSON().']}';
					
					}
			}
     }

/***********new API for trevels plan comment delete ends here**************/
/*************new API for uploading photos in sign up starts here**************/
	public function ImageUploadSignUp()
	{
		$essence = new Essentials();
		$profile_pic = $essence->tblPrefix().'profile_photo';
		$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
       // $sql = "SELECT max(`number`) as number from $profile_pic";
		
		//if($db->Query($sql))
		//{
			//$row = $db->Row();
			
			$index = rand(1,99);
			$number = $row->number?$row->number+1:1;
			//echo $index;
			//echo $number;
			$time = time();
			 $keyvalue = array('photo_id'=>'NULL', 'profile_id'=>'', 'index'=>$index, 'status'=>'"active"', 'number'=>$number, 'description'=>'NULL', 'publishing_status'=>'"public"', 'password'=>'NULL', 'title'=>'NULL', 'added_stamp'=>$time, 'authed'=>0);
           
		    $sqlins = "INSERT INTO '$profile_pic' values (NULL,'',$index,'approval',$number,NULL,'public', NULL, NULL, $time, 0)";
			$photo_id	=	$db->InsertRow($profile_pic,$keyvalue);
			//echo "hai".$photo_id;
			
            //if($db->Query($sqlins))
			//{
               // $row    =   $db->Row();
				//$result = array('photoid'=>$photo_id, 'index'=>$index, 'profile_id'=>$id);
                ///print_r($result);
				///return $result;
			//}
		//}
	}

/*************new API for uploading photos in sign up ends here****************/
/*************new API for add wink for clique starts here****************/
public function addWink($sid,$rid,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$wink_checker 	 		=	$essence->tblPrefix().'wink_checker';
			$conversation_tbl 	    =	$essence->tblPrefix().'mailbox_conversation';
			$mailbox_message_tbl    =	$essence->tblPrefix().'mailbox_message';
			$profile_table			=	$essence->tblPrefix().'profile';	
			$key					=	$essence->tblPrefix().'lang_key';
			$value					=	$essence->tblPrefix().'lang_value';
			$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
			$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';

			$sid = $sid;
			$rid = $rid;
			$time = time();
			$wink ="[wink]4[/wink]"; 
			$wink = trim($wink);
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			//check user sign in or not
		$res = $secure->CheckSecure($sid,$skey);
		if($res==1)
		{
		/* $sql0 = "SELECT membership_type_id FROM $profile_table WHERE profile_id ='$sid'";
		 $memberType1 =  $db->Query1($sql0);
		 $memberTypeId1 = mysql_fetch_array($memberType1);
		 $membershipTypeId1 = $memberTypeId1['membership_type_id'];
		 
		 $sql2 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='send_readable_message' AND membership_type_id='$membershipTypeId1'";
		 $res = $db->Query1($sql2);
		 $resultId = mysql_fetch_array($res);
		 $resultMemberId = $resultId['membership_type_id'];
		 if(($resultMemberId != NULL))
		 {
	 	 $sql3 = "SELECT membership_type_id FROM $profile_table WHERE profile_id ='$rid'";
		 $memberType2 =  $db1->Query1($sql3);
		 $memberTypeId2 = mysql_fetch_array($memberType2);
		 $membershipTypeId2 = $memberTypeId2['membership_type_id'];
		 
		 $sql4 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='send_readable_message' AND membership_type_id='$membershipTypeId2'";
		 $res1 = $db1->Query1($sql4);
		 $resultId1 = mysql_fetch_array($res1);
		 $resultMemberId1 = $resultId1['membership_type_id'];
		 if(($resultMemberId1 != NULL))
		 {*/
			$sqlN ="SELECT username FROM $profile_table	WHERE profile_id='$sid'";
			$name = $db->Query($sqlN);
			$resultName = mysql_fetch_array($name);
			$senderName = $resultName['username'];
			$subject = $sender_name= $senderName;
			$sqlQ ="SELECT username,email FROM $profile_table	WHERE profile_id='$rid'";
			$name = $db->Query1($sqlQ);
			$resultName = mysql_fetch_array($name);
			$recipient_name = $resultName['username'];
			$rname=$recipient_name;
			$Umail = $resultName['email'];
			
			
			$hash 		= 	self::generateMessageHash( $sid, $subject, $wink );
			$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

			$sql0 = "INSERT INTO $conversation_tbl VALUES ('',$sid,$rid,'$subject',0,0,0,$time,'yes')";
			$db->Query($sql0);
			
			$conId = mysql_insert_id();
			
			$sql1 = "INSERT INTO $mailbox_message_tbl VALUES ('',$conId,$time,$sid,$rid,'$wink','no','no','a','$hash')";
			$db->Query($sql1);
			
			$sql2 = "INSERT INTO $wink_checker VALUES ('',$sid,$rid,$time,'Y',$conId)";
			if($db2->Query($sql2))
			{
				if(mysql_insert_id())
					{	
						echo $stri	=	 '{"Status":"Live","Message": "Success"}';
						$mailer = new Mailer ();
			$sqlK="SELECT v.value FROM $value v JOIN $key k ON (k.lang_key_id=v.lang_key_id) WHERE (k.key='notify_about_new_wink_body_txt' and k.lang_section_id='77')";
			$name = $db1->Query1($sqlK);
			$resultName = mysql_fetch_array($name);
			$body = $resultName['value'];
			$site_name="4Clique";
			 $recipient_name = $resultName['username'];
			$sender_name=$senderName;;
						 $mailer->addr = $Umail;
						 $mailer->subject = 'Wink Notification.';
						 $body=str_replace ('{$recipient_name}', $rname, $body);
						 $body=str_replace ('{$sender_name}', $sender_name, $body);
						 $body=str_replace ('{$site_name}', "4Clique", $body);
						 
						 $mailer->msg = $body;
						 $mailer->SendMail ();
					}
					else
					{
						echo '{"Status":"Live","Message": "Failure"}';
					
					}
			}
		/* }
			else
			{
				echo '{"Message":"Membership Denied","Description":"Recipient"}';
			}*/
			
		 /*}
			else
	{
		echo '{"Message":"Membership Denied","Description":"Sender"}';
	}	*/
}
else //session expired
	{
		echo '{"Message":"Session Expired"}';
	}	
	}


/*************new API for add wink for clique ends here****************/
/*************new API for view wink for clique starts here****************/
public function viewWink($id,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$wink_checker 	 		=	$essence->tblPrefix().'wink_checker';
			$conversation_tbl 	    =	$essence->tblPrefix().'mailbox_conversation';
			$mailbox_message_tbl    =	$essence->tblPrefix().'mailbox_message';
			$profile_table			=	$essence->tblPrefix().'profile';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
		//check user sign in or not
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
			
			$sql ="SELECT c.message_id,w.conversation_id,w.sender_id,w.recipient_id,c.text FROM $wink_checker w
			       LEFT JOIN $mailbox_message_tbl c ON (c.conversation_id = w.conversation_id)
				   LEFT JOIN $conversation_tbl m ON (m.conversation_id = c.conversation_id)
				   WHERE w.recipient_id = '$id'";
		  // $sqlR = "Update $conversation_tbl SET $conversation_tbl.bm_read = 2 where conversation_id ='$cid'";	
		  // $db->Query($sqlR);
		   if($db->Query($sql))
		   {
		   if($db->RowCount())
				{	
					$profile	=	 '['.$db->GetJSON().']';
					$profile	= 	str_replace("},]", "}]", $profile);
					$profile = '{"Status":"Live","count": '.$db->RowCount().','.'"result": '.$profile.'}';
					echo $profile;
				}
				else
				{
					echo '{"Status":"Live","count":"0"}';
				   
				}
			  }
			}
			else
			{
				echo '{"Message":"Session Expired"}';
			}
	    }
/*************new API for view wink for clique ends here****************/
/*************new API for fetch wink for clique starts here****************/
public function fetchWink($id,$cid,$pid,$skey)
{
 			$essence = new Essentials();
			$secure     =   new secure();
			$wink_checker 	 		=	$essence->tblPrefix().'wink_checker';
			$conversation_tbl 	    =	$essence->tblPrefix().'mailbox_conversation';
			$mailbox_message_tbl    =	$essence->tblPrefix().'mailbox_message';
			$profile_table			=	$essence->tblPrefix().'profile';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{

			$sql = "SELECT c.message_id,w.conversation_id,w.sender_id,w.recipient_id,c.text,FROM_UNIXTIME(w.time_stamp,'%b %d %Y %h:%i') as time FROM $wink_checker w
				    LEFT JOIN $mailbox_message_tbl c ON (c.conversation_id = w.conversation_id)
				    LEFT JOIN $conversation_tbl m ON (m.conversation_id = c.conversation_id)
				    WHERE w.recipient_id = '$id' AND w.conversation_id = '$cid'";
		
			$sqlR 	=	"Update $conversation_tbl SET $conversation_tbl.bm_read = 2,SET $conversation_tbl.bm_read_special=2 where recipient_id = '$id'";
			$db1->Query($sqlR);
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$profile	=	 '['.$db->GetJSON().']';
					$profile	= 	str_replace("},]", "}]", $profile);
					$profile = '{"Status":"Live","count": '.$db->RowCount().','.'"result": '.$profile.'}';
					echo $profile;
				}
				else
				{
					echo '{"Status":"Live","count":"0"}';
				}
			}
		  }
	else //session expired
		{
			echo '{"Message":"Session Expired"}';
        }	
}
/*************new API for fetch wink for clique ends here****************/
/*************new API for delete wink for clique starts here****************/
public function deleteWink($id,$pid,$skey)
{
			$essence = new Essentials();
			$secure     =   new secure();
			$wink_checker 	 		=	$essence->tblPrefix().'wink_checker';
			$conversation_tbl 	    =	$essence->tblPrefix().'mailbox_conversation';
			$mailbox_message_tbl    =	$essence->tblPrefix().'mailbox_message';
			$profile_table			=	$essence->tblPrefix().'profile';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
			$sql0 = "SELECT * FROM $mailbox_message_tbl WHERE conversation_id = '$id'";
			$result = $db->Query($sql0);
			$resultSql0 = mysql_fetch_array($result);
			if($resultSql0!=NULL)
			{
			
			$sql = "DELETE FROM $conversation_tbl WHERE conversation_id='$id'";
			$db->Query($sql);
		    $sql1 = "DELETE FROM $mailbox_message_tbl WHERE conversation_id = '$id'";
				if($db->Query($sql1))
				{
					echo '{"Status":"Live","Message":"Success"}';	
				}
				else
				{
						  echo '{"Status":"Live","Message":"Incorrect ID"}';
				}
			}
			else
			{
				echo '{"Status":"Live","Message":"Cannot Delete"}';
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}'; 
		}		
			
}
/*************new API for delete wink for clique ends here****************/
/*************new API for add kiss for clique starts here****************/
public function addKiss($sid,$rid,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();

			$conversation_tbl 	    =	$essence->tblPrefix().'mailbox_conversation';
			$mailbox_message_tbl    =	$essence->tblPrefix().'mailbox_message';
			$profile_table			=	$essence->tblPrefix().'profile';	
			$key					=	$essence->tblPrefix().'lang_key';
			$value					=	$essence->tblPrefix().'lang_value';
			$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
			$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';

			$sid = $sid;
			$rid = $rid;
			$time = time();
			$kiss ="[smiles]58[/smiles]"; 
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			//check user sign in or not
			$res = $secure->CheckSecure($sid,$skey);
			if($res==1)
			{
			//membership checking starts here
		/* $sql0 = "SELECT membership_type_id FROM $profile_table WHERE profile_id ='$sid'";
		 $memberType1 =  $db->Query1($sql0);
		 $memberTypeId1 = @mysql_fetch_array($memberType1);
		 $membershipTypeId1 = $memberTypeId1['membership_type_id'];
		 
		 $sql2 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='send_readable_message' AND membership_type_id='$membershipTypeId1'";
		 $res = $db->Query1($sql2);
		 $resultId = @mysql_fetch_array($res);
		 $resultMemberId = $resultId['membership_type_id'];
		 if(($resultMemberId != NULL))
		 {
		 
	 $sql3 = "SELECT membership_type_id FROM $profile_table WHERE profile_id ='$rid'";
		 $memberType2 =  $db1->Query1($sql3);
		 $memberTypeId2 = @mysql_fetch_array($memberType2);
		 $membershipTypeId2 = $memberTypeId2['membership_type_id'];
		 //echo "rid".$membershipTypeId2;
		 
		 $sql4 = "SELECT membership_type_id FROM $membership_limit WHERE membership_service_key ='send_readable_message' AND membership_type_id='$membershipTypeId2'";
		 $res1 = $db1->Query1($sql4);
		 $resultId1 = @mysql_fetch_array($res1);
		 $resultMemberId1 = $resultId1['membership_type_id'];
		 //echo "rid".$resultMemberId1;
		 //echo "sid".$resultMemberId;
		 
	 if(($resultMemberId1 != NULL) )
        { */
			
			$sqlN ="SELECT username FROM $profile_table	WHERE profile_id='$sid'";
			$name = $db->Query1($sqlN);
			$resultName = mysql_fetch_array($name);
			$senderName = $resultName['username'];
			$subject =$sender_name= $senderName;
			$sqlQ ="SELECT username,email FROM $profile_table	WHERE profile_id='$rid'";
			$name = $db->Query1($sqlQ);
			$resultName = mysql_fetch_array($name);
			$recipient_name = $resultName['username'];
			$rname=$recipient_name;
			$Umail = $resultName['email'];
			$hash 		= 	self::generateMessageHash( $sid, $subject, $kiss );
			$sql0 = "INSERT INTO $conversation_tbl VALUES ('',$sid,$rid,'$subject',0,0,0,$time,'yes')";
			//echo $sql0;
			$db->Query1($sql0);
			
			$conId = $db->GetLastInsertID();
			//echo $conId;
			
			$sql1 = "INSERT INTO $mailbox_message_tbl VALUES ('',$conId,$time,$sid,$rid,'$kiss','no','no','a','$hash')";
			if($db1->Query1($sql1))
			{
				echo '{"Status":"Live","Message":"Success"}';
			$mailer = new Mailer ();
			$sqlK="SELECT v.value FROM $value v JOIN $key k ON (k.lang_key_id=v.lang_key_id) WHERE (k.key='notify_about_new_kiss_body_txt' and k.lang_section_id='77')";
			$name = $db->Query1($sqlK);
			$resultName = mysql_fetch_array($name);
			$body = $resultName['value'];
			$site_name="4Clique";
			 $recipient_name = $resultName['username'];
			$sender_name=$senderName;;
						 $mailer->addr = $Umail;
						 $mailer->subject = 'Kiss Notification.';
						 $body=str_replace ('{$recipient_name}', $rname, $body);
						 $body=str_replace ('{$sender_name}', $sender_name, $body);
						 $body=str_replace ('{$site_name}', "4Clique", $body);
						  $body=str_replace ('{$site_url}', "http://beta.sodtechnologies.com/4clique/", $body);

						 $mailer->msg = $body;
						 $mailer->SendMail ();
			}
			else
			{
				echo '{"Status":"Live","Message":"Failure"}';
			
			}
			/*}//recipient membership
			else
			{
				echo '{"Message":"Membership Denied","Description":"Recipient"}';
			}	
			}//sender membership
			else
			{
				echo '{"Message":"Membership Denied","Description":"Sender"}';
			}	*/
		}//secure
		
		else
		{
			echo '{"Message":"Session Expired"}';
		}		
	}

/*************new API for add kiss for clique ends here****************/
/*************new API for view kiss for clique starts here****************/
public function viewKiss($id,$skey)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$conversation_tbl 	    =	$essence->tblPrefix().'mailbox_conversation';
			$mailbox_message_tbl    =	$essence->tblPrefix().'mailbox_message';
			$profile_table			=	$essence->tblPrefix().'profile';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

		//check user sign in or not
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{

			$sql ="SELECT c.message_id,c.conversation_id,c.sender_id,c.recipient_id,c.text FROM $mailbox_message_tbl c 
				   LEFT JOIN $conversation_tbl m ON (m.conversation_id = c.conversation_id)
				   WHERE c.recipient_id = '$id'";
		  // $sqlR = "Update $conversation_tbl SET $conversation_tbl.bm_read = 2 where conversation_id ='%࠲4cid'";	
		  // $db->Query($sqlR);
		   if($db->Query($sql))
		   {
		   if($db->RowCount())
				{	
					$profile	=	 '['.$db->GetJSON().']';
					$profile	= 	str_replace("},]", "}]", $profile);
					$profile = '{"Status":"Live","count": '.$db->RowCount().','.'"result": '.$profile.'}';
					echo $profile;
				}
				else
				{
					echo '{"Status":"Live","count":"0"}';
				   
				}
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}
			
	 }

/*************new API for view kiss for clique ends here****************/
/*************new API for fetch kiss for clique starts here****************/
public function fetchKiss($id,$cid,$pid,$skey)
{
 			$essence = new Essentials();
			$secure     =   new secure();
			$conversation_tbl 	    =	$essence->tblPrefix().'mailbox_conversation';
			$mailbox_message_tbl    =	$essence->tblPrefix().'mailbox_message';
			$profile_table			=	$essence->tblPrefix().'profile';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
			
			$sql = "SELECT c.message_id,c.conversation_id,c.sender_id,c.recipient_id,c.text,FROM_UNIXTIME(c.time_stamp,'%b %d %Y %h:%i') as time FROM $mailbox_message_tbl c 
				    LEFT JOIN $conversation_tbl m ON (m.conversation_id = c.conversation_id)
				    WHERE c.recipient_id = '$id' AND c.conversation_id = '$cid'";
		
			$sqlR 	=	"Update $conversation_tbl SET $conversation_tbl.bm_read = 2,SET $conversation_tbl.bm_read_special= 2 where recipient_id = '$id'";
			$db1->Query($sqlR);
			if($db->Query($sql))
			{
				if($db->RowCount())
				{	
					$profile	=	 '['.$db->GetJSON().']';
					$profile	= 	str_replace("},]", "}]", $profile);
					$profile = '{"Status":"Live","count": '.$db->RowCount().','.'"result": '.$profile.'}';
					echo $profile;
				}
				else
				{
					echo '{"Status":"Live","count":"0"}';
				}
			}
		}
		else //session expired
		{
			echo '{"Message":"Session Expired"}';
		}	
}

/*************new API for fetch kiss for clique ends here****************/
/*************new API for delete kiss for clique ends here****************/
public function deleteKiss($id,$pid,$skey)
{
			$essence = new Essentials();
			$secure     =   new secure();
			$conversation_tbl 	    =	$essence->tblPrefix().'mailbox_conversation';
			$mailbox_message_tbl    =	$essence->tblPrefix().'mailbox_message';
			$profile_table			=	$essence->tblPrefix().'profile';
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
			$sql0 = "SELECT * FROM $mailbox_message_tbl WHERE conversation_id = '$id'";
			$result = $db->Query($sql0);
			$resultSql0 = mysql_fetch_array($result);
			if($resultSql0!=NULL)
			{
			$sql = "DELETE FROM $conversation_tbl WHERE conversation_id='$id'";
			$db->Query($sql);
		    $sql1 = "DELETE FROM $mailbox_message_tbl WHERE conversation_id = '$id'";
				if($db->Query($sql1))
				{
					echo '{"Status":"Live","Message":"Success"}';	
				}
				else
				{
					echo '{"Status":"Live","Message":"Incorrect Id"}';
				}
			}
		
		else
		{
			echo '{"Status":"Live","Message":"Cannot Delete"}';
		}	
		}
		else   //session expired
		{
			echo '{"Message":"Session Expired"}';
		}		
			
}

/*************new API for delete kiss for clique ends here****************/
/***********new API for news feed comment view option starts here**************/
public function viewNewsLike($eid,$pid,$skey)
	{
			$essence 			= new Essentials();
			$secure     		=   new secure();
			$news_tb_action 	=	$essence->tblPrefix().'newsfeed_action';
			$news_tb_like	 	=	$essence->tblPrefix().'newsfeed_like';
			$profile_tbl		=	$essence->tblPrefix().'profile';
			$pic_tbl			=	$essence->tblPrefix().'profile_photo';
					$time 		= time();
					$db 		= new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			
			//check user sign in or not
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
				$sqlEnt = "SELECT entityId, entityType FROM $news_tb_action WHERE id='$eid'";
			$sqlEntity = $db->Query($sqlEnt);
			$entityType = mysql_fetch_array($sqlEntity);
			$entityTypeResult = $entityType['entityType'];
			$entityIdResult=$entityType['entityId'];
			if ($entityTypeResult=='status_update')
			{
			$sql = "SELECT p.profile_id,username,sex,FROM_UNIXTIME(timeStamp,'%d %b %Y,%h:%i %p') as create_time,
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
					CAST( ptbl.photo_id AS CHAR ) , '_',
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $news_tb_like c
			        JOIN $profile_tbl p ON (c.userId  = p.profile_id)
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0)
					WHERE entityId ='$eid' and entityType ='$entityTypeResult' ORDER BY c.timeStamp DESC";
		  //echo $sql;
			
			if($db->Query($sql))
			{
				if($db->RowCount())
					{	
						$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace("},]","}]",$stri);
					}
					else
					{
						echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
					
					}
			}
		}
		else
		{
			{
			$sql = "SELECT p.profile_id,username,sex,FROM_UNIXTIME(timeStamp,'%d %b %Y,%h:%i %p') as create_time,
					CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
					CAST( ptbl.photo_id AS CHAR ) , '_',
					CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic FROM $news_tb_like c
			        JOIN $profile_tbl p ON (c.userId  = p.profile_id)
					LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0)
					WHERE entityId ='$entityIdResult' and entityType ='$entityTypeResult' ORDER BY c.timeStamp DESC";
		  //echo $sql;
			
			if($db->Query($sql))
			{
				if($db->RowCount())
					{	
						$stri	=	 '{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace("},]","}]",$stri);
					}
					else
					{
						echo '{"Status":"Live","count": 0,"result": ['.$db->GetJSON().']}';
					
					}
			}
		}
		}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
	
	}

/***********new API for news feed comment view option ends here**************/
/***********new API for add report in photo option ends here**************/
public function addReport($eid,$pid,$skey,$text)
	{
			$essence = new Essentials();
			$secure     =   new secure();
			$report_tbl	=	$essence->tblPrefix().'report';
			$time 		= time();
			$text       = mysql_real_escape_string($text);
			$status ="active";
			$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
			$res = $secure->CheckSecure($pid,$skey);
			if($res==1)
			{
			$sql = "SELECT report_id FROM $report_tbl WHERE reporter_id='$pid' and entity_id='$eid'";
			
			$sqlExe = $db->Query1($sql);
			$sqlRes = @mysql_fetch_array($sqlExe);
			$sqlResult = $sqlRes['report_id'];
			
				if($sqlResult == NULL)
				{
				 $sqlRe = "INSERT INTO $report_tbl VALUES ('','$pid','$eid','photo','$text','$time','$status')";
					 if($db->Query1($sqlRe))
					 {
						echo '{"Message":"Success"}';
					 }
					 else
					 {
						echo '{"Message":"Failure"}';
					 }
				}
				else
				{
				echo '{"Message":"You have already reported this content"}';
				}
			}
			else
			{
				echo '{"Message":"Session Expired"}';
			}
			
}
/***********new API for add report in photo  option ends here**************/




/*
				Insert Longitude and Latitude of a User
				Developed by Jameesh
				On 28-03-2012
				For Golf with Member
				Table Associated skadate_location_map
				Starts here
				*/
				
				public function addlocation($profileid,$long,$lat,$skey)
				{

					$essence 	= new Essentials();
$secure     =   new secure();
					$location 	= $essence->tblPrefix().'location_map';
$profile_tbl = $essence->tblPrefix().'profile';
					$db 		= new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

   $currentDate = date("Y-m-d",time());
 $sqlChk = "SELECT FROM_UNIXTIME(join_stamp,'%Y-%m-%d') as join_stamp,membership_type_id FROM $profile_tbl WHERE profile_id='$profileid'";
$sqlChkExe = $db->Query($sqlChk);
$sqlChkRes = mysql_fetch_array($sqlChkExe);
  $dateJoin = $sqlChkRes['join_stamp'];
$memberShip =  $sqlChkRes['membership_type_id'];
  $dateNew = date("Y-m-d",strtotime(date("Y-m-d", strtotime($dateJoin)) . " +30 days"));






$res = $secure->CheckSecure($profileid,$skey);
			if($res==1)
			{
					$a			="SELECT map_id FROM $location WHERE profile_id=$profileid";
					$r			=$db->Query($a);
					$ab			=mysql_fetch_array($r);
					if($ab==NULL)
					{
$db45 		= new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

							$sql45	=	"INSERT INTO $location (map_id,profile_id,longitude,latitude) VALUES ('','$profileid','$long','$lat')";	
							if($db45->Query($sql45))
							{
								echo '{"Message":"Success"}';
							}
							else
							{
							echo '{"Message":"Failure"}';
						
							}
						
					}
					else
					{
if($memberShip == '3' OR $memberShip =='108')
{
							$sql1	="UPDATE $location SET longitude=$long,latitude=$lat WHERE profile_id=$profileid";
							if($db->Query($sql1))
							{
								echo '{"Message":"Success"}';
							}
							else
							{
							echo '{"Message":"Failure"}';
						
							}
}
else
{

	if($currentDate<=$dateNew)
	{

	$sql1	="UPDATE $location SET longitude=$long,latitude=$lat WHERE profile_id=$profileid";
								if($db->Query($sql1))
								{
									echo '{"Message":"Success"}';
								}
								else
								{
								echo '{"Message":"Failure"}';
						
								}
	}
	else
	{
		echo '{"Message":"Expired"}';
	}
}
					}
}
			else
			{
				echo '{"Message":"Session Expired"}';
			}
$db->Close();
$db45->Close();

				}
				
/*-------------Insert Longitude and Latitude of a User Ends here----------------------------*/

				/*
				Maping Nearest Longitude and Latitude of a User with in a specific radius
				Developed by Jameesh 
				On 28-03-2012
				For Golf with Member
				Table Associated skadate_location_map
				Starts here
				*/
				

public function addlocationmap($profileid,$long,$lat,$skey)
				{

					$essence 	= 	new Essentials();
					$secure     =   new secure();
					$location 	= 	$essence->tblPrefix().'location_map';
					$pic_tbl	=	$essence->tblPrefix().'profile_photo';
					$profile_tbl	=	$essence->tblPrefix().'profile';
					$profile_online_tbl	=	$essence->tblPrefix().'profile_online';
					$db 		= new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
 $currentDate = date("Y-m-d",time());
 $sqlChk = "SELECT FROM_UNIXTIME(join_stamp,'%Y-%m-%d') as join_stamp,membership_type_id FROM $profile_tbl WHERE profile_id='$profileid'";
$sqlChkExe = $db->Query($sqlChk);
$sqlChkRes = mysql_fetch_array($sqlChkExe);
  $dateJoin = $sqlChkRes['join_stamp'];
$memberShip =  $sqlChkRes['membership_type_id'];
 $dateNew = date("Y-m-d",strtotime(date("Y-m-d", strtotime($dateJoin)) . " +30 days"));
//echo $dateNew;
$res = $secure->CheckSecure($profileid,$skey);
$res=1;
			if($res==1)
			{
			
					 $a			="SELECT map_id FROM $location WHERE profile_id=$profileid";
					$r			=$db->Query($a);
					$ab			=mysql_fetch_array($r);
					if($ab==NULL)
					{
					
	 $sql	=	"INSERT INTO $location (map_id,profile_id,longitude,latitude) VALUES ('','$profileid','$long','$lat')";	
$db->Query($sql);
$flag2=1;
							/*if($db->Query($sql))
							{
								$flag=1;
							}
							else
							{
							$flag=0;
						
							}
*/

//return $flag2;
//echo "flag".$flag2;

						
					}
					else
					{
	
if($memberShip =='3' OR $memberShip =='108')
{
							$sql1	="UPDATE $location SET longitude=$long,latitude=$lat WHERE profile_id=$profileid";
							if($db->Query($sql1))
							{
								$flag=1;
							}
							else
							{
							$flag=0;
						
							}
return $flag;
}
else
{

if($currentDate<=$dateNew)
{

$sql1	="UPDATE $location SET longitude=$long,latitude=$lat WHERE profile_id=$profileid";
							$db->Query($sql1);
							$flag =1;

}
else
{
$flag=0;

}
//return $flag;
}

}				
			
	//echo "flag".$flag2;		
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
  					/*  Selection of points within specified radius of given lat/lon (c) Chris Veness 2008-2012       */
  					/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
  
					$lat = $_GET["lat"];  // latitude of centre of bounding circle in degrees
					$lon = $_GET["lon"];  // longitude of centre of bounding circle in degrees
					$rad = 200;  // radius of bounding circle in kilometers
					$R = 6371;  // earth's radius, km
  
					// first-cut bounding box (in degrees)
					$maxLat = $lat + rad2deg($rad/$R);
					$minLat = $lat - rad2deg($rad/$R);
					// compensate for degrees longitude getting smaller with increasing latitude
					$maxLon = $lon + rad2deg($rad/$R/cos(deg2rad($lat)));
					$minLon = $lon - rad2deg($rad/$R/cos(deg2rad($lat)));
		  
					// convert origin of filter circle to radians
					$lat = deg2rad($lat);
					$lon = deg2rad($lon);
					
				if($flag==1 or $flag2==1) 
{	
$db3 		= new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());				 
 $sqlres    =  "Select DISTINCT l.profile_id,username,sex,year(CURRENT_TIMESTAMP ) - year(birthdate) AS DOB,CONCAT( '/','userfiles/thumb_', CAST( ptbl.profile_id AS CHAR ) , '_',
         CAST( ptbl.photo_id AS CHAR ) , '_',
         CAST( ptbl.index AS CHAR ) , '.jpg' ) as Profile_Pic,latitude,longitude, 
       				     acos(sin($lat)*sin(radians(latitude)) + cos($lat)
						 *cos(radians(latitude))*cos(radians(longitude)-$lon))*$R As D
			              From $profile_tbl p
						  LEFT JOIN $location l ON (p.profile_id=l.profile_id)
                                                  LEFT JOIN $pic_tbl ptbl ON (p.profile_id = ptbl.profile_id and ptbl.number=0)
						   Where acos(sin($lat)*sin(radians(latitude)) + 
						  cos($lat)*cos(radians(latitude))*cos(radians(longitude)-$lon))*$R < $rad";

//year(CURRENT_TIMESTAMP ) - year(`ptbl`.`birthdate`) AS DOB, 
					 	  
				//echo $sql;
			if($db3->Query($sqlres)) 
			{

				if($db3->RowCount())     
				{
					$stri	=	 '{"Total rows": '.$db3->RowCount().',"count": '.$db3->RowCount().',"result": ['.$db3->GetJSON().']}';
							echo str_replace("},]","}]",$stri);
				}
				else
				{
					echo '{"Total rows": 0,"count": 0,"result": ['.$db3->GetJSON().']}';
				}
			}
			else
			{
				echo '{"Total rows": 0,"count": 0,"result": ['.$db3->GetJSON().']}';
			}

}
else  
{
echo '{"Message":"Expired"}';
}

}
			else
			{
				echo '{"Message":"Session Expired"}';
			}
$db->Close();
$db3->Close();
		
		}


/******************************************************************/
/*******************All contacts new API starts here***************************/
 public function allContactsByName($name)
	 {
		 $essence		=	new Essentials();
		 $profile_table		=	$essence->tblPrefix().'profile';
		 $pic_tbl		=	$essence->tblPrefix().'profile_photo';
		 $db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		  if (!$db->Error())
			{
$sql	="SELECT p.profile_id,username,sex,CONCAT( '/','userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , '_', 
CAST( $pic_tbl.photo_id AS CHAR ) , '_',CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
FROM `$profile_table` p 
LEFT JOIN `$pic_tbl` ON p.profile_id=`$pic_tbl`.profile_id AND `$pic_tbl`.number=0 
WHERE p.status='active' AND username LIKE '$name%'";




			//echo $sql;
			if($db->Query($sql))
				{ 
					if($db->RowCount())
					{
						/*$profile	=	 '['.$db->GetJSON().']';
						echo $profile	= 	str_replace("},]", "}]", $profile);*/
						
						$profile	=	 '['.$db->GetJSON().']';
						$profile	= 	str_replace("},]", "}]", $profile);
						$profile = '{"Status":"Live","count": '.$db->RowCount().','.'"result": '.$profile.'}';
						echo $profile;
					}
					else
					{
						echo '{"Status":"Live","Message":"No Members Found"}';
					}	
				}
			}
	 }
/******************All contacts new API ends here******************************/
/******************user status starts here******************************/
 public function userStatus($id,$skey)
	 {
		 $essence		=	new Essentials();
$secure     =   new secure();
		 $profile_table		=	$essence->tblPrefix().'profile';
		
		 $db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		  
	$res = $secure->CheckSecure($id,$skey);
			if($res==1)
			{			 

$sql	="SELECT membership_type_id,join_stamp
FROM  $profile_table WHERE profile_id=$id";
$res=$db->Query($sql);
$row = mysql_fetch_array($res);
$membership_id = $row['membership_type_id'];
$join_stamp = $row['join_stamp'];

$dateJoin		= date("Y-m-d",$join_stamp);
$currentDate		= date("Y-m-d");
if($membership_id == '3' OR $membership_id == '108')
{
$userstatus ='y';
}
else if ($membership_id == '107')
{
 $dateNew = date("Y-m-d",strtotime(date("Y-m-d", strtotime($dateJoin)) . " +30 days"));
if($currentDate <= $dateNew)
{

$userstatus ='y';
}
else
{
$userstatus ='n';
}

}
else
{
$userstatus ='n';
}

echo '{"UserStatus":"'.$userstatus.'"}';
			
}
			else
			{
				echo '{"Message":"Session Expired"}';
			}
			
	 }
/******************user status ends here******************************/
//---------------------------->check privil start here<-----------------------                
public function checkprivil($service,$pid)
    {
    $essence		=	new Essentials();
    $secure     =   new secure();
    $profile_table		=	$essence->tblPrefix().'profile';
    $service_table		=	$essence->tblPrefix().'link_membership_type_service';
    $limit_table		=	$essence->tblPrefix().'link_membership_service_limit';
    $session                    =       $essence->tblPrefix().'im_session';
    $db 			= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

    //$res = $secure->CheckSecure($id,$skey);
$res=1;
        if($res==1)
        {			 

        $sql	="SELECT membership_type_id FROM  $profile_table WHERE profile_id=$pid";
        $res=$db->Query($sql);
        $row = mysql_fetch_array($res);
        $membership_id = $row['membership_type_id'];
$sql="select * from $service_table where membership_type_id=$membership_id and membership_service_key='$service'";
if($db->Query($sql))
        { 
                if($db->RowCount())
                {
                    if($service=='initiate_im_session')
                    {
                       $sqllimit="select `limit` from $limit_table where membership_service_key='initiate_im_session' AND membership_type_id=$membership_id";
                        $a=$db->Query($sqllimit);
                        $b=  mysql_fetch_array($a);
                        $limit=$b['limit'];
                        
                        $time1=time();
                        $timeStamp1= date("Y-m-d",$time1);			
                       $sqlChk1 = "SELECT COUNT(opener_id) FROM $session WHERE opener_id = '$pid' and FROM_UNIXTIME(start_stamp,'%Y-%m-%d')='$timeStamp1'";
                        $sqlCount = $db->Query1($sqlChk1);
                        $sqlCount1 = mysql_fetch_array($sqlCount);
                       $sqlCount2 = $sqlCount1['COUNT(opener_id)'];
                        
                        if($sqlCount2<$limit)
                        {
                            echo '{"Service":"'.$service.'","Privilage":"True"}';
                        }
                        else
                        {
                            echo '{"Service":"'.$service.'","Privilage":"false"}';
                        }
                    }
                    else
                    {
                        echo '{"Service":"'.$service.'","Privilage":"True"}';
                    }
                }
                else
                {
                        echo '{"Service":"'.$service.'","Privilage":"false"}';
                }	
        }
        else
        {
        echo '{"Service":"'.$service.'","Privilage":"false"}';
        }	

        }
        else
        {
        echo '{"Message":"Session Expired"}';
        }

    }    
/****************************************************************************/
public function acceptfriendreq($pid,$skey,$id)
{
	$essence = 	new Essentials();
	$secure  =  new secure();
	
	$friendlist = $essence->tblPrefix().'profile_friend_list';
	$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	
	$res=$secure->CheckSecure($pid, $skey);
	$res=1;
	if($res==1)
	{
		$sql="SELECT * FROM `$friendlist` WHERE `id`=$id";
                $exe=$db->Query($sql);
                $res=mysql_fetch_array($exe);
                $p=$res['profile_id'];
                $f=$res['friend_id'];
                $fi=$res['friendship_id'];
          if ($p=='' OR $p==NULL)
          {
          	echo '{"Message":"Friend request cancelled by sender"}';
          	exit;
          }      
		$sql="UPDATE $friendlist SET `status`='active' WHERE `id`=$id";
		
		if($db->Query($sql))
            { 
                echo '{"Message":"Success"}';
                
                $sql="SELECT id FROM `$friendlist` WHERE `profile_id`=$f AND `friend_id`=$p";
                $exe=$db->Query($sql);
                $res=mysql_fetch_array($exe);
                $frid=$res['id'];
                if($frid>0)
                {
                	$sql="UPDATE $friendlist SET `status`='active' WHERE `id`=$frid";
                	$db->Query($sql);
                }
                else 
                {
                	$sql="INSERT INTO $friendlist VALUES ('',$f,$p,'active',$fi)";
                	$db->Query($sql);
                }
                $data='{"line":"friend_add","content":{"friend_id":'."$f".',"profile_id":'."$p".'}}';
                /*$sqln="SELECT `entityId` FROM `skadate_newsfeed_action` WHERE `entityType`='friend_add'   ORDER BY `id` DESC LIMIT 1";
                $exe=$db->Query($sqln);
                $res=mysql_fetch_array($exe);
                $entity_id=$res['entityId'];
                $entity_id=$entity_id+1;*/
                $t=time();
                $sql="INSERT INTO `skadate_newsfeed_action` VALUES ('',$fi,'friend_add','newsfeed','$data','active',$t,$t,$p,'15','everybody')";
                $db->Query($sql);
            }
            else 
            {
            	echo '{"Message":"Failure"}';
            }
            
	}
    else 
    {
    	echo '{"Message":"Session Expired"}';
    }
} 
/****************************************************************************/
/****************************************************************************/
public function friendreqcount($pid,$skey)
{
	$essence = 	new Essentials();
	$secure  =  new secure();
	
	$friendlist = $essence->tblPrefix().'profile_friend_list';
	$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	
	$res=$secure->CheckSecure($pid, $skey);
	if($res==1)
	{
		$sql="SELECT count(*) FROM `$friendlist` WHERE `friend_id`=$pid";
		$exe=$db->Query($sql);
		$val=mysql_fetch_array($exe);
		$count=$val['count(*)'];
		if ($count>0)
		{echo '{"Status":"Live","count":'.$count.'}';}
		else 
		{echo '{"Status":"Live","count":'.$count.'}';}
	}
    else 
    {
    	echo '{"Message":"Session Expired"}';
    }
}
/****************************************************************************/	
/****************************************************************************/   
public function recievedfriendreq($pid,$skey)
{
	$essence = 	new Essentials();
	$secure  =  new secure();
	mysql_query('SET CHARACTER SET utf8'); 
	$friendlist = $essence->tblPrefix().'profile_friend_list';
	$profile_table		=	$essence->tblPrefix().'profile';
	$location_table 	=	$essence->tblPrefix().'location_country';
    $location_state     =   $essence->tblPrefix().'location_state';
    $location_city      =   $essence->tblPrefix().'location_city';
    $pic_tbl			=	$essence->tblPrefix().'profile_photo';
	$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	
	$res=$secure->CheckSecure($pid, $skey);
	$res=1;
	if($res==1)
	{
		$sql="SELECT `f`.*,`p`.username,`p`.sex,CONCAT( '/','userfiles/thumb_', CAST( pic_tbl.profile_id AS CHAR ) , '_', 
			CAST( pic_tbl.photo_id AS CHAR ) , '_',CAST( pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic,year(CURRENT_TIMESTAMP ) - year(`p`.`birthdate`) AS DOB,Country_str_name,
			DATE( FROM_UNIXTIME(`join_stamp`) ) AS JoinDate,CONCAT(Admin1_str_name,',',Feature_str_name) AS custom_location
		 	FROM `$friendlist` `f` 
			LEFT JOIN `$profile_table` `p` ON (`p`.`profile_id`=`f`.`profile_id`)
			LEFT JOIN `$location_table` AS `ctbl` ON (`p`.`country_id`= `ctbl`.`Country_str_code`)
            LEFT JOIN `$location_state` AS `st` ON (`p`.`state_id`= `st`.`Admin1_str_code`)
			LEFT JOIN `$location_city` AS `cty` ON (`p`.`city_id`= `cty`.`Feature_int_id`)	
			LEFT JOIN `$pic_tbl` AS `pic_tbl` ON( `f`.`profile_id`=`pic_tbl`.`profile_id` AND `pic_tbl`.`number`=0)
			WHERE `friend_id`=$pid AND `f`.`status`='pending' ORDER BY `p`.username ASC";
		if($db->Query($sql))
            { 
                    if($db->RowCount())
                    {
                            
                            $profile	=	'['.$db->GetJSON().']';
                            $profile	= 	str_replace("},]", "}]", $profile);
                            $profile    =       '{"Status":"Live","Totalrows":'.$db->RowCount().',"count": '.$db->RowCount().','.'"result": '.$profile.'}';
                            echo $profile;
                    }
                    else
                    {
                            echo '{"Status":"Live","Totalrows":"0","Message":"No Friend Request"}';
                    }	
            }
	}
    else 
    {
    	echo '{"Message":"Session Expired"}';
    }
} 

/****************************************************************************/	
                   /*Send Friend Request*/                        
/****************************************************************************/ 
public function sendfriendreq($pid,$fid,$skey)
{
	$essence = 	new Essentials();
	$secure  =  new secure();
	$friendlist = $essence->tblPrefix().'profile_friend_list';
	$blocklist = $essence->tblPrefix().'profile_block_list';
	$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$db2 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	$res=$secure->CheckSecure($pid, $skey);
	$res=1;
	if($res==1)
	{
		$flag=$this->checkprivilage('creat_friends_network', $pid);
		if($flag==0)
		{
			echo '{"Message":"Membership Denied","Description":"Sender"}';
		}
		else 
		{
			$flag1=$this->checkprivilage('creat_friends_network', $fid);
			if($flag1==0)
			{
				echo '{"Message":"Membership Denied","Description":"Recipient"}';
			}
			else 
			{	
				
				$sqlb="SELECT profile_id FROM `$blocklist` WHERE blocked_id=$pid AND profile_id=$fid"; 
                        $sqlr=$db2->Query($sqlb); 
						$sqlresult=mysql_fetch_array($sqlr); 
						$bid=$sqlresult['profile_id'];                        
                        if ($bid>0) 
                        {echo '{"Message":"Your profile has been blocked"}';exit;} 
				$sqlb="SELECT profile_id FROM `$blocklist` WHERE blocked_id=$fid AND profile_id=$pid"; 
                        $sqlr=$db2->Query($sqlb); 
						$sqlresult=mysql_fetch_array($sqlr); 
						$bid=$sqlresult['profile_id'];                        
                        if ($bid>0) 
                        {echo '{"Message":"You already blocked this user"}';exit;}
				mysql_connect($essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
				mysql_select_db($essence->getDbName());
				$sql="SELECT DISTINCT `friendship_id`,`status` FROM `$friendlist` WHERE (`profile_id`=$pid AND `friend_id`=$fid) OR (`profile_id`=$fid AND `friend_id`=$pid)";
				$sqlexe=mysql_query($sql);
				$sqlres=mysql_fetch_array($sqlexe);
				$friendshipid=$sqlres['friendship_id'];
				$friendshipstatus=$sqlres['status'];
				if($friendshipid>0)
				{
					//echo "haiiiiiiiiiiii";
					$sql="INSERT INTO `$friendlist`(`id`, `profile_id`, `friend_id`, `status`, `friendship_id`) VALUES ('','$pid','$fid','pending','$friendshipid')";
					if(mysql_query($sql))
					{
						echo '{"Message":"Success"}';
					}
					else 
					{
						if($friendshipstatus=='active')
						{
							echo '{"Message":"You are friend with this user"}';
						}
						else 
						{
						echo '{"Message":"You already send a request to this user"}';
						}
					}
				}
				else 
				{
					mysql_connect($essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
					mysql_select_db($essence->getDbName());
					$sqlq	="SELECT `id` FROM `$friendlist`  ORDER BY `id` DESC LIMIT 1";
					$result	=mysql_query($sqlq);
					$a		=mysql_fetch_array($result);
					$friendshipid=$a['id'];
					$sql	="INSERT INTO `$friendlist`(`id`, `profile_id`, `friend_id`, `status`, `friendship_id`) VALUES ('','$pid','$fid','pending','2')";
					if(mysql_query($sql))
					{
						echo '{"Message":"Success"}';
						$sqlq	="SELECT `id` FROM `$friendlist`  ORDER BY `id` DESC LIMIT 1";
					$result	=mysql_query($sqlq);
					$a		=mysql_fetch_array($result);
					$friendshipid=$a['id'];
					$sql="Update `$friendlist` SET `friendship_id`=$friendshipid WHERE `id`=$friendshipid";
					mysql_query($sql);
					}
					else 
					{
						echo '{"Message":"You already send a request to this user"}';
					}
				}
				
			}
		}
	}
	else
	{
		echo '{"Message":"Session Expired"}';
	}
}
/****************************************************************************/	
/****************************************************************************/     
public function sentfriendreq($pid,$skey)
{
	$essence = 	new Essentials();
	$secure  =  new secure();
	
	$friendlist = $essence->tblPrefix().'profile_friend_list';
	$profile_table		=	$essence->tblPrefix().'profile';
	$location_table 		=	$essence->tblPrefix().'location_country';
    $location_state                =       $essence->tblPrefix().'location_state';
    $location_city                 =       $essence->tblPrefix().'location_city';
    $pic_tbl			=	$essence->tblPrefix().'profile_photo';
	$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	
	$res=$secure->CheckSecure($pid, $skey);
	//$res=1;
	if($res==1)
	{
		$sql="SELECT `f`.*,`p`.username,`p`.sex,CONCAT( '/','userfiles/thumb_', CAST( pic_tbl.profile_id AS CHAR ) , '_', 
CAST( pic_tbl.photo_id AS CHAR ) , '_',CAST( pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic,year(CURRENT_TIMESTAMP ) - year(`p`.`birthdate`) AS DOB,Country_str_name,
		 DATE( FROM_UNIXTIME(`join_stamp`) ) AS JoinDate,CONCAT(Admin1_str_name,',',Feature_str_name) AS custom_location
		 	FROM `$friendlist` `f` 
			LEFT JOIN `$profile_table` `p` ON (`p`.`profile_id`=`f`.`friend_id`)
			LEFT JOIN `$location_table` AS `ctbl` ON (`p`.`country_id`= `ctbl`.`Country_str_code`)
            LEFT JOIN `$location_state` AS `st` ON (`p`.`state_id`= `st`.`Admin1_str_code`)
			LEFT JOIN `$location_city` AS `cty` ON (`p`.`city_id`= `cty`.`Feature_int_id`)	
			LEFT JOIN `$pic_tbl` AS `pic_tbl` ON( `f`.`profile_id`=`pic_tbl`.`profile_id` AND `pic_tbl`.`number`=0)
		WHERE `f`.`profile_id`=$pid AND `f`.`status`='pending'";
		
		if($db->Query($sql))
            { 
                    if($db->RowCount())
                    {
                            
                            $profile	=	'['.$db->GetJSON().']';
                            $profile	= 	str_replace("},]", "}]", $profile);
                            $profile    =       '{"Status":"Live","count": '.$db->RowCount().','.'"result": '.$profile.'}';
                            echo $profile;
                    }
                    else
                    {
                            echo '{"Status":"Live","Message":"No Friend Request"}';
                    }	
            }
	}
    else 
    {
    	echo '{"Message":"Session Expired"}';
    }
} 
/****************************************************************************/	
                  /*Decline Friend Request*/
/****************************************************************************/
public function declinefriendreq($pid,$skey,$id)
{
	$essence = 	new Essentials();
	$secure  =  new secure();
	
	$friendlist = $essence->tblPrefix().'profile_friend_list';
	$db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	
	$res=$secure->CheckSecure($pid, $skey);
	//$res=1;
	if($res==1)
	{
		$sql="DELETE FROM $friendlist WHERE `id`=$id";
		
		if($db->Query($sql))
            { 
                echo '{"Message":"Success"}';
            }
            else 
            {
            	echo '{"Message":"Failure"}';
            }
            
	}
    else 
    {
    	echo '{"Message":"Session Expired"}';
    }
} 
/****************************************************************************/	
                  /*Get Friend List*/
/****************************************************************************/ 
public function getfriendlist($id,$skey,$start,$limit)
	 	{
	 		 mysql_query('SET CHARACTER SET utf8'); 
		 $essence			=	new Essentials();
		 $secure     			=       new secure();
		 $online_table 			=	$essence->tblPrefix().'profile_online';	
		 $profile_table			=	$essence->tblPrefix().'profile';
		 $profile_table_extend          =	$essence->tblPrefix().'profile_extended';
		 $pic_tbl			=	$essence->tblPrefix().'profile_photo';
		 $friend_tbl			=	$essence->tblPrefix().'profile_friend_list';
		 $location_state                =       $essence->tblPrefix().'location_state';
                 $location_city                 =       $essence->tblPrefix().'location_city';
                 $location_table 		=	$essence->tblPrefix().'location_country';
		 $db                            = 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		 $res = $secure->CheckSecure($id,$skey);
		 $i=0;
		 $res=1;
			 if($res==1)
				{
						if (!$db->Error())
					{
						$sqlC	=	"SELECT `f`.*,`p`.*,`pe`.*,
						CONCAT( '/','userfiles/thumb_', CAST( pic_tbl.profile_id AS CHAR ) , '_', 
CAST( pic_tbl.photo_id AS CHAR ) , '_',CAST( pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
,Country_str_name,`online`.`hash` AS `online` FROM $friend_tbl  AS `f`
						 INNER JOIN `" . $profile_table . "` AS `p` ON( `f`.`friend_id`=`p`.`profile_id` )
				INNER JOIN `" . $profile_table_extend . "` AS `pe` ON( `f`.`profile_id`=`pe`.`profile_id` )
				LEFT JOIN `" . $online_table . "` AS `online` ON( `f`.`friend_id`=`online`.`profile_id` )
				LEFT JOIN `$location_table` AS `ctbl` ON (`p`.`country_id`= `ctbl`.`Country_str_code`)
				LEFT JOIN `" . $pic_tbl . "` AS `pic_tbl` ON( `f`.`friend_id`=`pic_tbl`.`profile_id` AND `pic_tbl`.`number`=0)
				WHERE (`f`.`profile_id`=$id) AND (`p`.`status`='active') AND (`f`.`status`='active')  
				ORDER BY `p`.`has_photo` DESC";
					$db->Query($sqlC);
			$totalCount = $db->RowCount();		
						
						
						$sql	=	"SELECT `f`.*,`p`.*,`pe`.*,Country_str_name,CONCAT(Admin1_str_name,',',Feature_str_name) AS custom_location,year(CURRENT_TIMESTAMP ) - year(`p`.`birthdate`) AS DOB,
						CONCAT( '/','userfiles/thumb_', CAST( pic_tbl.profile_id AS CHAR ) , '_', 
CAST( pic_tbl.photo_id AS CHAR ) , '_',CAST( pic_tbl.index AS CHAR ) , '.jpg' ) AS Profile_Pic 
,`online`.`hash` AS `online` FROM $friend_tbl  AS `f`
						 INNER JOIN `" . $profile_table . "` AS `p` ON( `f`.`friend_id`=`p`.`profile_id` )
				INNER JOIN `" . $profile_table_extend . "` AS `pe` ON( `f`.`profile_id`=`pe`.`profile_id` )
				LEFT JOIN `" . $online_table . "` AS `online` ON( `f`.`friend_id`=`online`.`profile_id` )
				LEFT JOIN `$location_state` AS `st` ON (p.`state_id`= `st`.`Admin1_str_code`)
				LEFT JOIN `$location_city` AS `cty` ON (p.`city_id`= `cty`.`Feature_int_id`)
				LEFT JOIN `$location_table` AS `ctbl` ON (`p`.`country_id`= `ctbl`.`Country_str_code`)
				LEFT JOIN `" . $pic_tbl . "` AS `pic_tbl` ON( `f`.`friend_id`=`pic_tbl`.`profile_id` AND `pic_tbl`.`number`=0)
				WHERE (`f`.`profile_id`=$id) AND (`p`.`status`='active') AND (`f`.`status`='active')  
				ORDER BY `p`.`username` ASC LIMIT $start, $limit";
		
					$res=$db->Query($sql);
					while($row1 = @mysql_fetch_array($res))
						{
						$result[$i] =array('profile_id'=>$row1['friend_id'],'username'=>$row1['username'], 'sex'=>$row1['sex'],'birthdate'=>$row1['birthdate'],'DOB'=>$row1['DOB'], 'Country_str_name'=>$row1['Country_str_name'],'custom_location'=>$row1['custom_location'], 'online'=>$row1['online'],'profile_pic'=>$row1['Profile_Pic']);
					$i++;
						}
						
						$final = array();
						if (is_array($result))
						{
							foreach($result as $array)
							{
								array_push($final, $array);
							}
						}	
						//$i=$i-1;
						if($i==0){echo '{"Status":"Live","Totalrows":"0","count":"0","Message":"No Friends found"}';}
						else{
						$final	=	 '{"Status":"Live","Totalrows":'.$totalCount.',"count": '.$i.',"result": '.json_encode($final).'}';
						echo str_replace("},]","}]",$final);}
						
						
					}
					else
					{
					echo '{"Status":"Live","count":"0","Message":"No Friends found"}';
					}	
				}
				else
				{
					echo '{"Message":"Session Expired"}';
				}
$db->Close();
		}	
/****************************************************************************/	
  /****************************************************************************/ 
public function checkprivilage($service,$pid)
    {
    $essence			=	new Essentials();
    $secure     		=   new secure();
    $profile_table		=	$essence->tblPrefix().'profile';
    $service_table		=	$essence->tblPrefix().'link_membership_type_service';
    $limit_table		=	$essence->tblPrefix().'link_membership_service_limit';
    $session            =   $essence->tblPrefix().'im_session';
    $db 				= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

  
        $sql			=	"SELECT membership_type_id FROM  $profile_table WHERE profile_id=$pid";
        $res			=	$db->Query($sql);
        $row 			= 	mysql_fetch_array($res);
        $membership_id 	= 	$row['membership_type_id'];
        $flag=0;
		$sql	=	"select * from $service_table where membership_type_id=$membership_id and membership_service_key='$service'";
		if($db->Query($sql))
        { 
                if($db->RowCount())
                {
                        $flag=1;
                }
                else
                {
                        $flag=0;
                }	
        }
        else
        {
        $flag=0;
        }	
//Temporary
$flag=1;
        return $flag;

    }    
/****************************************************************************/       
         
  public function delete_Photos($photoid,$pid,$skey,$Count) { 
        $essence = new Essentials(); 
        $secure = new secure(); 
       
        $profile_photo  = $essence->tblPrefix() . 'profile_photo '; 

        $res = $secure->CheckSecure($pid, $skey); 
        //$res=1;
        if ($res == 1) { 
        $time = time(); $f=0;
        $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass()); 
	for($i=0;$i<$Count;$i++)
	{$id= $photoid[$i];
		$sql1="select `profile_id`,`index` from $profile_photo where photo_id='$id'";
				$sqlexe=$db->query($sql1);
				$sqlfet=mysql_fetch_array($sqlexe);
				$profile_id=$sqlfet['profile_id'];
				$index=$sqlfet['index'];
				$file='thumb_'.$profile_id.'_'.$id.'_'.$index.'.jpg';
		@unlink('../../userfiles/'.$file);		
        
                $sql = "DELETE FROM $profile_photo WHERE photo_id = '$id'"; 
                $resQuery = $db->Query($sql); 
                if ($resQuery) { 
                   $f=$f; 
                } else { 
                    $f=1;
                } 
	}

	if($f==0){echo '{"Status":"Live","Message":"Success"}'; }else{echo '{"Status":"Live","count":"Failure"}';}
        } 
			else { //session expired 
            echo '{"Message":"Session Expired"}'; 
        } 
            
        
    }        
 //---------------------------->check privil end here<----------------------- 


}
?>
