<?php
class search
{
	
	/******search backup starts here**********/
	public function sSearchBachup($kv)
	{
	    /*	
		 	Search Members MS2
			Input given
			Gender, LookingFor, AgeRangeFrom, AgeRangeTo, MilesFrom, ZIP, OnlineOnly, WithPhotoOnly
			Output Desired
			List of profiles each with DisplayName, Gender, Age, Place, OnlineStatus
		*/
		//print_r($kv);
		$this->actualSearchBackup($kv);
	}
	private function actualSearchBackup($kv)
	{
	//	mysql_query('SET CHARACTER SET utf8'); 

		$profileId		=	isset($kv["id"])?$kv["id"]:NULL;
		$sex			=	isset($kv["sex"])?$kv["sex"]:NULL;
		$matchSex		=	isset($kv["matchSex"])?$kv["matchSex"]:NULL;
		$ageRangeFrom	=	isset($kv["ageRangeFrom"])?$kv["ageRangeFrom"]:NULL;
		$ageRandeTo		=	isset($kv["ageRangeTo"])?$kv["ageRangeTo"]:NULL;
		$milesFrom		=	isset($kv["milesFrom"])?$kv["milesFrom"]:NULL;
		$zip			=	isset($kv["zip"])?$kv["zip"]:NULL;
		$onlineOnly		=	isset($kv["onlineOnly"])?$kv["onlineOnly"]:'n';
		$withPhotoOnly	=	isset($kv["withPhotoOnly"])?$kv["withPhotoOnly"]:'n';
		
		$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_tbl_extended	=	$essence->tblPrefix().'profile_extended';
				$pic_tbl				=	$essence->tblPrefix().'profile_photo';
				$profile_tbl_online		=	$essence->tblPrefix().'profile_online';
				$location_tbl			=	$essence->tblPrefix().'location_zip';
				$country_tbl			=	$essence->tblPrefix().'location_country';
				
				if($zip)
				{
					$latlongsql		=	"SELECT `latitude` AS `lat`, `longitude` AS `lon` FROM `".$location_tbl."` WHERE `zip`='$zip'";
					
					if ($db->Query($latlongsql))
					{
						if($db->RowCount())
						{	
							$row			=	$db->Row();
							//print_r($row);
							$lat			=	$row->lat;
							$long			=	$row->lon;
						}
					}
				}
				/*
				$searchquery	=	"SELECT * FROM	(SELECT *,hash as OnlineStatus, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB FROM `$profile_tbl` AS `main` 
										LEFT JOIN `$profile_tbl_extended` AS `extend` USING( `profile_id` ) 
										LEFT JOIN `$profile_tbl_online` AS `online` USING( `profile_id` )
										LEFT JOIN `$country_tbl` AS `ctbl` ON (main.`country_id`= ctbl.`Country_str_code`)
										";
				*/
				$searchquery	=	"SELECT profile_id,username,sex,Profile_Pic,DOB,custom_location,Country_str_name  FROM	(SELECT `profile_id`, `email`,`username`, `password`, `sex`, `match_sex`, `birthdate`, `headline`, `general_description`, `match_agerange`, `custom_location`, `country_id`, `zip`, `state_id`, `city_id`, `join_stamp`, `activity_stamp`, `membership_type_id`, `affiliate_id`, `email_verified`, `reviewed`, `has_photo`, `has_media`, `status`, `featured`, `register_invite_score`, `rate_score`, main.`rates`, `language_id`, `join_ip`, `neigh_location`, `neigh_location_distance`, `bg_color`, `bg_image`, `bg_image_url`, `bg_image_mode`, `bg_image_status`, `has_music`, `is_private`,hash as OnlineStatus, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB,Country_str_name FROM `$profile_tbl` AS `main` 
										LEFT JOIN `$profile_tbl_extended` AS `extend` USING( `profile_id` ) 
										LEFT JOIN `$profile_tbl_online` AS `online` USING( `profile_id` )
										LEFT JOIN `$country_tbl` AS `ctbl` ON (main.`country_id`= ctbl.`Country_str_code`)
										";
						
							if($zip and $milesFrom)
							{
								 $searchquery	.= " INNER JOIN 
										( SELECT `zip` FROM `$location_tbl` 
										WHERE 
										 ( 3963.0*acos
										 ( sin( ".($lat/57.29577951).") * sin(`latitude`/57.29577951) 
										 + cos(".($lat/57.29577951).") * cos(`latitude`/57.29577951)
										 * cos(`longitude`/57.29577951 --".($long/57.29577951).") ) )
										 <= '$milesFrom' )
										 AS `zip_third` ON ( `zip_third`.`zip` = `main`.`zip` )";
							} 
							if($withPhotoOnly == 'y')
							{
									 $searchquery	.=		 	" WHERE ( `has_photo`='$withPhotoOnly' )";
							}
							else if($withPhotoOnly == 'n')
							{
									 $searchquery	.=		 	" WHERE ( `has_photo`='y'  OR `has_photo`='n')";
							}
							if($onlineOnly == 'y')
							{
									$searchquery	.=			 " AND ( `online`.`hash` IS NOT NULL )";
							}
							if($matchSex)
							{
									$searchquery	.=			 " AND $matchSex&`main`.`sex`";
							}
							if($ageRandeTo and $ageRangeFrom)
							{
									$searchquery	.=		 " AND YEAR(NOW())-YEAR(`main`.`birthdate`)- IF( DAYOFYEAR(`main`.`birthdate`) > DAYOFYEAR(NOW()),0,0) >= $ageRangeFrom 
										 AND YEAR(NOW())-YEAR(`main`.`birthdate`)-IF( DAYOFYEAR(`main`.`birthdate`) > DAYOFYEAR(NOW()),0,0) <=$ageRandeTo";
							}
									$searchquery	.=		 " AND ( `main`.`status`='active' ) AND `main`.`profile_id`!=$profileId ORDER BY `main`.`has_photo` 
										 DESC, IF( `online`.`profile_id` <> NULL, 0 , 1 ), `activity_stamp` DESC LIMIT 500)X
										 
										 LEFT JOIN 
( select photo_id,`$pic_tbl`.`profile_id` as profid, `index`, status, `number`, `description`,CONCAT( '/','userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , 
'_',CAST( $pic_tbl.photo_id AS CHAR ) , '_',
CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) as Profile_Pic from
`$pic_tbl`)Y 
ON X.`profile_id` = Y.`profid` AND Y.`number` = 0";
										 
				//echo $searchquery;						 
				
				if ($db->Query($searchquery))
				{
					if($db->RowCount())
					{	
						$profile	=	'{"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo $profile		= str_replace("},]", "}]", $profile);
					}
					else
					{
						echo $profile	=	'{"count": 0, "result": [0]}';
					}
				}
			}
	}
	
/******************search backup ends here*************************/

	
public function SaveSearch($id,$sname,$criterion,$skey)
	{
		$essence	=	new Essentials();
		$secure     =   new secure();
		$online_tbl		=	$essence->tblPrefix().'profile_online';
		$criterion_tbl		=	$essence->tblPrefix().'search_criterion';
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
	  
	  //$sql0 = "SELECT hash FROM $online_tbl WHERE profile_id = '$id'";
	  //$sqlExe = $db->Query($sql0);
	  //$sqlRes = mysql_fetch_array($sqlExe);
	  //$skey = $sqlRes['hash']; 
	  //check user sign in or not
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
		if (!$db->Error())
			{
				$db2 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
				//$criterion_tbl		=	$essence->tblPrefix().'search_criterion';
				$criterion_values	=	"'".json_encode($criterion)."'";
				$newkv				=	array('profile_id' => $id, 'criterion_name' => "'".$sname."'", 'criterion' => $criterion_values);
				//print_r($newkv);
				$criterion_id		=	$db2->InsertRow($criterion_tbl,$newkv);
				echo '{"Criterion":"'.$criterion_id.'"}';
			}
	}
	else
	{
		echo '{"Message":"Session Expired"}';
	}

}
	public function FetchallSearch($id,$skey)
	{
		$essence	=	new Essentials();
		$secure     =   new secure();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$res = $secure->CheckSecure($id,$skey);
		if($res==1)
		{
		if (!$db->Error())
			{
				$criterion_tbl		=	$essence->tblPrefix().'search_criterion';
				$sql	=	"SELECT criterion_id,criterion_name from $criterion_tbl	where profile_id=$id";
				if ($db->Query($sql))
				{
					if($db->RowCount())
					{	
						$profile	=	'{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace(",]}","]}",$profile);
					}
					else
					{
						echo $profile	=	'{"Status":"Live","count": 0, "result": [0]}';
					}
				}
			}
			}
			else
		{
		echo '{"Message":"Session Expired"}';
		}
	}
	public function FetchSearch($id,$pid,$skey)
	{
		$essence	=	new Essentials();
		$secure     =   new secure();
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
		$profile_tbl			=	$essence->tblPrefix().'profile';
		$profile_tbl_extended		=	$essence->tblPrefix().'profile_extended';
		$criterion_tbl		=	$essence->tblPrefix().'search_criterion';
				
				
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db3		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
		
		$sql3="SELECT profile_id from $criterion_tbl where criterion_id=$id";
		//echo $sql3;
		$res3=$db3->Query($sql3);
		$resultId3=mysql_fetch_array($res3);
		$resultPid=$resultId3['profile_id'];
		
		if (!$db->Error())
			{
				
				$sql	=	"SELECT profile_id,criterion from $criterion_tbl where criterion_id=$id";
				if ($db->Query($sql))
				{
					$row		=	$db->Row();
					$res		=	json_decode($row->criterion);
					$kv["id"]	=	$row->profile_id;
					//if (is_array($res))
					//{
						foreach ($res as $key => $value)
						{
							$kv[$key] = $value;
							
						}
					//}	
					$this->actualSearch($kv);
				}
			}
			
	}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
	}
/********fetch search by limit starts here**********/
		public function FetchSearchByLimit($id,$start,$limit,$pid,$skey)
	{
		$essence	=	new Essentials();
		$secure     =   new secure();
		
		$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		$membership_srv		=	$essence->tblPrefix().'link_membership_service_limit';
		$profile_tbl			=	$essence->tblPrefix().'profile';
		$profile_tbl_extended		=	$essence->tblPrefix().'profile_extended';
		$criterion_tbl		=	$essence->tblPrefix().'search_criterion';
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$db3		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
		
		$sql3="SELECT profile_id from $criterion_tbl where criterion_id=$id";
		//echo $sql3;
		$res3=$db3->Query($sql3);
		$resultId3=mysql_fetch_array($res3);
		$resultPid=$resultId3['profile_id'];
		
		if (!$db->Error())
			{
				
				$sql	=	"SELECT profile_id,criterion from $criterion_tbl where criterion_id=$id";
				if ($db->Query($sql))
				{
					$row		=	$db->Row();
					$res		=	json_decode($row->criterion);
					$kv["id"]	=	$row->profile_id;
					$kv["start"]=$start;
					$kv["limit"]=$limit;
					//if (is_array($res))
					//{
						foreach ($res as $key => $value)
						{
							$kv[$key] = $value;
							
						}
					//}	
					$this->actualSearchByLimit($kv);
				}
			}
			
	}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
	}
/********fetch search by limit ends here**********/

/********search by limit starts here**********/

    public function sSearchByLimit($kv) {
        $secure = new secure();

        $profileId = isset($kv["id"]) ? $kv["id"] : NULL;
        $skey = isset($kv["skey"]) ? $kv["skey"] : NULL;
        $res = $secure->CheckSecure($profileId, $skey);
        //$res = 1;
        if ($res == 1) {
            /*
              Search Members MS2
              Input given
              Gender, LookingFor, AgeRangeFrom, AgeRangeTo, MilesFrom, ZIP, OnlineOnly, WithPhotoOnly
              Output Desired
              List of profiles each with DisplayName, Gender, Age, Place, OnlineStatus
             */
            //print_r($kv);
            $this->actualSearchByLimit($kv);
        } else {
            echo '{"Message":"Session Expired"}';
        }
    }

    private function actualSearchByLimit($kv) {

        @mysql_query('SET CHARACTER SET utf8');
        $essence = new Essentials();
        $db1 = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
         
        if (isset($kv["search_type"]) OR isset($kv["new_search"]))
        {
        	$profileId= isset($kv["id"]) ? $kv["id"] : NULL;
        	$sex = isset($kv["sex"]) ? $kv["sex"] : NULL;
        	$matchSex = isset($kv["match_sex"]) ? $kv["match_sex"] : NULL;
        	$matchSex = $matchSex[0];
        	$birtdate=isset($kv["birthdate"]) ? $kv["birthdate"] : NULL;
        	if ($birtdate!=NULL){$ageRangeFrom=$birtdate[0];$ageRandeTo=$birtdate[1];}
        	
        	$ethnicity=isset($kv["ethnicity"]) ? $kv["ethnicity"]:NULL;
        	$relationship=isset($kv["relationship"]) ? $kv["relationship"]:NULL;
        	$height=isset($kv["height"]) ? $kv["height"]:NULL;
        	$religion=isset($kv["religion"]) ? $kv["religion"]:NULL;
        	$body_type=isset($kv["body_type"]) ? $kv["body_type"]:NULL;
        	$hair_color=isset($kv["hair_color"]) ? $kv["hair_color"]:NULL;
        	$have_children=isset($kv["have_children"]) ? $kv["have_children"]:NULL;
        	$language=isset($kv["language"]) ? $kv["language"]:NULL;
        	$education=isset($kv["education"]) ? $kv["education"]:NULL;
        	$income=isset($kv["income"]) ? $kv["income"]:NULL;
        	$smoke=isset($kv["smoke"]) ? $kv["smoke"]:NULL;
        	$drink=isset($kv["drink"]) ? $kv["drink"]:NULL;
        	$interests=isset($kv["interests"]) ? $kv["interests"]:NULL;
        	
        	
        	
        	$milesFrom = isset($kv["milesFrom"]) ? $kv["milesFrom"] : NULL;
        	$zip = isset($kv["zip"]) ? $kv["zip"] : NULL;
        	$online=isset($kv["search_online_only"]) ? $kv["search_online_only"] : NULL;
        	if($online==1){$onlineOnly='y';}else{$onlineOnly='n';}
        	$photo=isset($kv["search_with_photo_only"]) ? $kv["search_with_photo_only"] : NULL;
        	if($photo==1){$withPhotoOnly='y';}else{$withPhotoOnly='n';}
        	$location=isset($kv["location"]) ? $kv["location"] : NULL;
        	if($location!=NULL){
        	$country1 = $location['country_id'];
	        $country1 = ltrim(rtrim($country1," ")," ");
	        $state = $location['state_id'];
	        $state = ltrim(rtrim($state," ")," ");
	        $cityid = $location['city_id'];
	        $cityid = ltrim(rtrim($city," ")," ");}
	        
	         $sqlr="SELECT `Feature_int_id` FROM skadate_location_city WHERE `Feature_str_name`='$city'";
            	$sqlExe=$db1->Query($sqlr);
            	$result=mysql_fetch_array($sqlExe);
            	$city=$result['Feature_str_name'];
	        
	        
	        
	        /*$start=0;
	        $limit=50;*/
	        $start = isset($kv["start"]) ? $kv["start"] : '0';
	        $limit = isset($kv["limit"]) ? $kv["limit"] : '';
        }
        
        else 
        {
        
	        $profileId = isset($kv["id"]) ? $kv["id"] : NULL;
	        $sex = isset($kv["sex"]) ? $kv["sex"] : NULL;
	        $matchSex = isset($kv["matchSex"]) ? $kv["matchSex"] : NULL;
	        $ageRangeFrom = isset($kv["ageRangeFrom"]) ? $kv["ageRangeFrom"] : NULL;
	        $ageRandeTo = isset($kv["ageRangeTo"]) ? $kv["ageRangeTo"] : NULL;
	        $milesFrom = isset($kv["milesFrom"]) ? $kv["milesFrom"] : NULL;
	        $zip = isset($kv["zip"]) ? $kv["zip"] : NULL;
	        $onlineOnly = isset($kv["onlineOnly"]) ? $kv["onlineOnly"] : 'n';
	        $withPhotoOnly = isset($kv["withPhotoOnly"]) ? $kv["withPhotoOnly"] : 'n';
	        $start = isset($kv["start"]) ? $kv["start"] : '0';
	        $limit = isset($kv["limit"]) ? $kv["limit"] : '';
	        $country1 = isset($kv["country_id"]) ? $kv["country_id"] : NULL;
	        $country1 = ltrim(rtrim($country1," ")," ");
	        $state = isset($kv["state_id"]) ? $kv["state_id"] : NULL;
	        $state = ltrim(rtrim($state," ")," ");
	        $city = isset($kv["city_id"]) ? $kv["city_id"] : NULL;
	        $city = ltrim(rtrim($city," ")," ");

        }
        
        if ($milesFrom == 0) {
            $milesFrom = 0.01;
        }


        $essence = new Essentials();
        $db = new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
        
        if (!$db->Error()) {
            $profile_tbl = $essence->tblPrefix() . 'profile';
            $profile_tbl_extended = $essence->tblPrefix() . 'profile_extended';
            $pic_tbl = $essence->tblPrefix() . 'profile_photo';
            $profile_tbl_online = $essence->tblPrefix() . 'profile_online';
            $location_tbl = $essence->tblPrefix() . 'location_zip';
            $country_tbl = $essence->tblPrefix() . 'location_country';
            $state_table = $essence->tblPrefix() .'location_state';
        $city_table = $essence->tblPrefix() .'location_city';
            $membership_limit = $essence->tblPrefix() . 'link_membership_type_service';
            $membership_srv = $essence->tblPrefix() . 'link_membership_service_limit';

            if ($zip) {
                $zip1 = explode(" ", $zip);


                $latlongsql = "SELECT `latitude` AS `lat`, `longitude` AS `lon` FROM `" . $location_tbl . "` WHERE `zip`='$zip1[0]'";

                if ($db->Query($latlongsql)) {
                    if ($db->RowCount()) {
                        $row = $db->Row();
                        //print_r($row);
                        $lat = $row->lat;
                        $long = $row->lon;
                    }
                }
            }



/*
            $searchqueryT = "SELECT DISTINCT  main.profile_id,username,sex,
CONCAT( '/$','userfiles/thumb_', CAST(ph.profile_id AS CHAR ) , '_',CAST(photo_id AS CHAR ) , '_',
 CAST(ph.index AS CHAR ) , '.jpg' ) AS Profile_Pic,
FLOOR((TO_DAYS(NOW())- TO_DAYS(main.birthdate)) / 365.25) AS DOB,CONCAT (Admin1_str_name ,',', Feature_str_name) as custom_location,Country_str_name
 
FROM

skadate_profile AS main
LEFT JOIN  skadate_profile_extended ex ON ex.profile_id=main.profile_id
LEFT JOIN skadate_profile_online online ON  ex.profile_id=online.profile_id
LEFT JOIN skadate_location_country ctbl ON main.country_id= ctbl.Country_str_code
LEFT JOIN `$state_table` AS `stbl` ON (main.`state_id`= `stbl`.`Admin1_str_code`)
LEFT JOIN `$city_table` AS `cybl` ON (main.`city_id`= `cybl`.`Feature_int_id`)
LEFT JOIN skadate_profile_photo ph ON ph.profile_id=main.profile_id AND ph.number= 0 and ph.status='active'";
            if ($zip and $milesFrom) {
                $searchqueryT .= " INNER JOIN 
										( SELECT `zip` FROM `$location_tbl` 
										WHERE 
										 ( 3963.0*acos
										 ( sin( " . ($lat / 57.29577951) . ") * sin(`latitude`/57.29577951) 
										 + cos(" . ($lat / 57.29577951) . ") * cos(`latitude`/57.29577951)
										 * cos(`longitude`/57.29577951 --" . ($long / 57.29577951) . ") ) )
										 <= '$milesFrom' )
										 AS `zip_third` ON ( `zip_third`.`zip` = `main`.`zip` )";
            }

            if ($withPhotoOnly == 'y') {
                $searchqueryT .= "WHERE ( `has_photo`='$withPhotoOnly' )";
            } else if ($withPhotoOnly == 'n') {
                $searchqueryT .= " WHERE ( `has_photo`='y'  OR `has_photo`='n')";
            }
            if ($onlineOnly == 'y') {
                $searchqueryT .= " AND ( `online`.`hash` IS NOT NULL )";
            }
            
         if ($ethnicity != NULL){
            	$searchqueryT .= " AND (`ex`.`ethnicity`&$ethnicity[0] )";
            }
        	if ($relationship != NULL){
            	$searchqueryT .= " AND (`ex`.`relationship`&$relationship[0] )";
            }
        	if ($height != NULL){
            	$searchqueryT .= " AND (`ex`.`height`&$height[0] )";
            }
        	if ($religion != NULL){
            	$searchqueryT .= " AND (`ex`.`religion`&$religion[0] )";
            }
        	if ($body_type != NULL){
            	$searchqueryT .= " AND (`ex`.`body_type`&$body_type[0] )";
            }
        	if ($hair_color != NULL){
            	$searchqueryT .= " AND (`ex`.`hair_color`&$hair_color[0] )";
            }
        	if ($have_children != NULL){
            	$searchqueryT .= " AND (`ex`.`have_children`&$have_children[0] )";
            }
        	if ($language != NULL){
            	$searchqueryT .= " AND (`ex`.`language`&$language[0] )";
            }
        	if ($education != NULL){
            	$searchqueryT .= " AND (`ex`.`education`&$education[0] )";
            }
        	if ($income != NULL){
            	$searchqueryT .= " AND (`ex`.`income`&$income[0] )";
            }
        	if ($smoke != NULL){
            	$searchqueryT .= " AND (`ex`.`smoke`&$smoke[0] )";
            }
       	 	if ($drink != NULL){
            	$searchqueryT .= " AND (`ex`.`drink`&$drink[0] )";
            }
        	if ($interests != NULL){
            	$searchqueryT .= " AND (`ex`.`interests`&$interests[0] )";
            }
            
            
            if ($matchSex and $sex) {
                $searchqueryT .= " AND ($matchSex&main.sex)  AND (main.match_sex&$sex)";
            }
           
            
            if (isset($kv["country_id"]) && $kv["country_id"] != NULL) {
                if (isset($kv["state_id"]) && $kv["state_id"] != NULL) {
                    if (isset($kv["city_id"]) && $kv["city_id"] != NULL) {
                        $searchqueryT .= " AND main.country_id='$country1' AND main.state_id='$state' AND main.city_id='$city' ";
                    } else {
                        $searchqueryT .= " AND main.country_id='$country1' AND main.state_id='$state' ";
                    }
                } else {
                    $searchqueryT .= " AND main.country_id='$country1' ";
                }
            }
            if ($ageRandeTo and $ageRangeFrom) {
                $searchqueryT .= " AND YEAR(NOW())-YEAR(birthdate)- IF( DAYOFYEAR(birthdate) > DAYOFYEAR(NOW()),1,0) >= $ageRangeFrom AND YEAR(NOW())-YEAR(birthdate)-IF( DAYOFYEAR(birthdate) > DAYOFYEAR(NOW()),1,0) <=$ageRandeTo";
            }
            $searchqueryT .=" AND (main.status='active' ) AND main.profile_id!=$profileId 
ORDER BY has_photo DESC";
            $db->Query($searchqueryT);
            $totalCount = $db->RowCount();
*/
//SET CONCAT_NULL_YIELDS_NULL OFF;

            


            $searchquery = "SELECT DISTINCT (main.profile_id),username,sex,
CONCAT( '/$','userfiles/thumb_', CAST(ph.profile_id AS CHAR ) , '_',CAST(photo_id AS CHAR ) , '_',
 CAST(ph.index AS CHAR ) , '.jpg' ) AS Profile_Pic,
FLOOR((TO_DAYS(NOW())- TO_DAYS(main.birthdate)) / 365.25) AS DOB,custom_location,Admin1_str_name,Feature_str_name,Country_str_name
 
FROM

skadate_profile AS main
LEFT JOIN  skadate_profile_extended ex ON ex.profile_id=main.profile_id
LEFT JOIN skadate_profile_online online ON  ex.profile_id=online.profile_id
LEFT JOIN skadate_location_country ctbl ON main.country_id= ctbl.Country_str_code
LEFT JOIN `$state_table` AS `stbl` ON (main.`state_id`= `stbl`.`Admin1_str_code`)
LEFT JOIN `$city_table` AS `cybl` ON (main.`city_id`= `cybl`.`Feature_int_id`)
LEFT JOIN skadate_profile_photo ph ON ph.profile_id=main.profile_id AND ph.number= 0";

            if ($zip and $milesFrom) {
                $searchquery .= " INNER JOIN 
										( SELECT `zip` FROM `$location_tbl` 
										WHERE 
										 ( 3963.0*acos
										 ( sin( " . ($lat / 57.29577951) . ") * sin(`latitude`/57.29577951) 
										 + cos(" . ($lat / 57.29577951) . ") * cos(`latitude`/57.29577951)
										 * cos(`longitude`/57.29577951 --" . ($long / 57.29577951) . ") ) )
										 <= '$milesFrom' )
										 AS `zip_third` ON ( `zip_third`.`zip` = `main`.`zip` )";
            }

            if ($withPhotoOnly == 'y') {
                $searchquery .= " WHERE ( `has_photo`='$withPhotoOnly' )";
            } else if ($withPhotoOnly == 'n') {
                $searchquery .= " WHERE ( `has_photo`='y'  OR `has_photo`='n')";
            }
            if ($onlineOnly == 'y') {
                $searchquery .= " AND ( `online`.`hash` IS NOT NULL )";
            }
            if ($ethnicity != NULL){
            	$searchquery .= " AND (`ex`.`ethnicity`&$ethnicity[0] )";
            }
        	if ($relationship != NULL){
            	$searchquery .= " AND (`ex`.`relationship`&$relationship[0] )";
            }
        	if ($height != NULL){
            	$searchquery .= " AND (`ex`.`height`&$height[0] )";
            }
        	if ($religion != NULL){
            	$searchquery .= " AND (`ex`.`religion`&$religion[0] )";
            }
        	if ($body_type != NULL){
            	$searchquery .= " AND (`ex`.`body_type`&$body_type[0] )";
            }
        	if ($hair_color != NULL){
            	$searchquery .= " AND (`ex`.`hair_color`&$hair_color[0] )";
            }
        	if ($have_children != NULL){
            	$searchquery .= " AND (`ex`.`have_children`&$have_children[0] )";
            }
        	if ($language != NULL){
            	$searchquery .= " AND (`ex`.`language`&$language[0] )";
            }
        	if ($education != NULL){
            	$searchquery .= " AND (`ex`.`education`&$education[0] )";
            }
        	if ($income != NULL){
            	$searchquery .= " AND (`ex`.`income`&$income[0] )";
            }
        	if ($smoke != NULL){
            	$searchquery .= " AND (`ex`.`smoke`&$smoke[0] )";
            }
       	 	if ($drink != NULL){
            	$searchquery .= " AND (`ex`.`drink`&$drink[0] )";
            }
        	if ($interests != NULL){
            	$searchquery .= " AND (`ex`.`interests`&$interests[0] )";
            }
            if ($matchSex and $sex) {
                $searchquery .= " AND ($matchSex&main.sex)  AND (main.match_sex&$sex)";
            }
            
            if (isset($kv["country_id"]) && $kv["country_id"] != '' || $country1 != '') {
                if (isset($kv["state_id"]) && $kv["state_id"] != '' || $state != '') {
                    if (isset($kv["city_id"]) && $kv["city_id"] != '' || $city != '') {
                        $searchquery .= " AND main.country_id='$country1' AND main.state_id='$state' AND main.city_id='$city' ";
                    } else {
                        $searchquery .= " AND main.country_id='$country1' AND main.state_id='$state' ";
                    }
                } else {
                    $searchquery .= " AND main.country_id='$country1' ";
                }
            }
            if ($ageRandeTo and $ageRangeFrom) {
                $searchquery .= "AND YEAR(NOW())-YEAR(birthdate)- IF( DAYOFYEAR(birthdate) > DAYOFYEAR(NOW()),1,0) >= $ageRangeFrom AND 
 YEAR(NOW())-YEAR(birthdate)-IF( DAYOFYEAR(birthdate) > DAYOFYEAR(NOW()),1,0) <=$ageRandeTo";
            }
            $searchquery .=" AND (main.status='active' ) AND main.profile_id!=$profileId 
";

            $searchqueryT=$searchquery;
            $db->Query($searchqueryT);
            $totalCount = $db->RowCount();
            
            //ORDER BY has_photo DESC    
            //echo $searchquery;						 
            //for counting total rows
            $res1 = $db->Query($searchquery);
            $st = 0;
            $li = 0;
            $i = 0;
            while ($row1 = @mysql_fetch_array($res1)) {

                

                if ($st == $start AND $li < $limit) {
                	if($row1['Admin1_str_name']!=NULL OR $row1['Admin1_str_name']!='' )
                	{
                		if($row1['Feature_str_name']!=NULL OR $row1['Feature_str_name']!='')
                		{
                		$cl=$row1['Admin1_str_name'].','.$row1['Feature_str_name'];
                		}
                		else 
                		{
                			$cl=$row1['Admin1_str_name'];
                		}
                	}
                	else 
                	{
                	if($row1['Feature_str_name']!=NULL OR $row1['Feature_str_name']!='')
                		{
                		$cl=$row1['Feature_str_name'];
                		}
                		else 
                		{
                			$cl=NULL;
                		}
                	}
                	if ($cl==NULL)
                	{
                	$cl=$row1['custom_location'];
                	}
                    $result[$i] = array('profile_id' => $row1['profile_id'], 'username' => $row1['username'], 'sex' => $row1['sex'], 'Profile_Pic' => $row1['Profile_Pic'], 'DOB' => $row1['DOB'], 'custom_location' => $cl, 'Country_str_name' => $row1['Country_str_name']);
                    //$st++;
                    $li++;
                    $i++;
                } else {
                    $st++;
//$li++;
                }
            }


            $final = array();
            // Assigning to array Ends here
            if (is_array($result)) {
                foreach ($result as $array) {

                    array_push($final, $array);
                }
            }
//echo $i;			//$li=$li-1;
            if ($i == 0) {
                echo $profile = '{"Total rows": 0,"count": 0, "result": [0]}';
            } else {
                $final = '{"Total rows":' . $totalCount . ',"count": ' . $li . ',"result": ' . json_encode($final) . '}';
                echo str_replace("},]", "}]", $final);
            }
        }
    }










public function sSearchByLimit1($kv)
	{
	mysql_query('SET CHARACTER SET utf8');	
$secure     =   new secure();
		
		$profileId		=	isset($kv["id"])?$kv["id"]:NULL;
		$skey			=	isset($kv["skey"])?$kv["skey"]:NULL;
		$res = $secure->CheckSecure($profileId,$skey);
		//$res=1;
		if($res==1)
		{
	    /*
		 	Search Members MS2
			Input given
			Gender, LookingFor, AgeRangeFrom, AgeRangeTo, MilesFrom, ZIP, OnlineOnly, WithPhotoOnly
			Output Desired
			List of profiles each with DisplayName, Gender, Age, Place, OnlineStatus
		*/
		//print_r($kv);
		$this->actualSearchByLimit($kv);
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
	}

	private function actualSearchByLimit1($kv)
	{
		
		mysql_query('SET CHARACTER SET utf8'); 

		//echo "hai";
		
		$profileId		=	isset($kv["id"])?$kv["id"]:NULL;
		$sex			=	isset($kv["sex"])?$kv["sex"]:NULL;
		$matchSex		=	isset($kv["matchSex"])?$kv["matchSex"]:NULL;
		$ageRangeFrom	=	isset($kv["ageRangeFrom"])?$kv["ageRangeFrom"]:NULL;
		$ageRandeTo		=	isset($kv["ageRangeTo"])?$kv["ageRangeTo"]:NULL;
		$milesFrom		=	isset($kv["milesFrom"])?$kv["milesFrom"]:NULL;
		$zip			=	isset($kv["zip"])?$kv["zip"]:NULL;
		$onlineOnly		=	isset($kv["onlineOnly"])?$kv["onlineOnly"]:'n';
		$withPhotoOnly	=	isset($kv["withPhotoOnly"])?$kv["withPhotoOnly"]:'n';
		$start		=	 isset($kv["start"])?$kv["start"]:'0';
		$limit	    =	isset($kv["limit"])?$kv["limit"]:'';
		if($milesFrom ==0)
		{
			$milesFrom =0.01;
		}
		
		
		$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_tbl_extended	=	$essence->tblPrefix().'profile_extended';
				$pic_tbl				=	$essence->tblPrefix().'profile_photo';
				$profile_tbl_online		=	$essence->tblPrefix().'profile_online';
				$location_tbl			=	$essence->tblPrefix().'location_zip';
				$country_tbl			=	$essence->tblPrefix().'location_country';
				$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		        $membership_srv		=	$essence->tblPrefix().'link_membership_service_limit'; 
				











mysql_query('SET CHARACTER SET utf8'); 


				$searchqueryT = "SELECT DISTINCT  main.profile_id,username,sex,
CONCAT( '/','userfiles/thumb_', CAST(ph.profile_id AS CHAR ) , '_',CAST(photo_id AS CHAR ) , '_',
 CAST(ph.index AS CHAR ) , '.jpg' ) AS Profile_Pic,
FLOOR((TO_DAYS(NOW())- TO_DAYS(main.birthdate)) / 365.25) AS DOB,custom_location,Country_str_name
 
FROM

skadate_profile AS main
LEFT JOIN  skadate_profile_extended ex ON ex.profile_id=main.profile_id
LEFT JOIN skadate_profile_online online ON  ex.profile_id=online.profile_id
LEFT JOIN skadate_location_country ctbl ON main.country_id= ctbl.Country_str_code

LEFT JOIN skadate_profile_photo ph ON ph.profile_id=main.profile_id AND ph.number= 0";
if($zip and $milesFrom)
							{
								 $searchqueryT	.= " INNER JOIN 
										( SELECT `zip` FROM `$location_tbl` 
										WHERE 
										 ( 3963.0*acos
										 ( sin( ".($lat/57.29577951).") * sin(`latitude`/57.29577951) 
										 + cos(".($lat/57.29577951).") * cos(`latitude`/57.29577951)
										 * cos(`longitude`/57.29577951 --".($long/57.29577951).") ) )
										 <= '$milesFrom' )
										 AS `zip_third` ON ( `zip_third`.`zip` = `main`.`zip` )";
							} 

if($withPhotoOnly == 'y')
{
	$searchqueryT .= " WHERE ( `has_photo`='$withPhotoOnly' )";
}
else if($withPhotoOnly == 'n')
							{
									 $searchqueryT	.=		 	" WHERE ( `has_photo`='y'  OR `has_photo`='n')";
							}
							if($onlineOnly == 'y')
							{
									$searchqueryT	.=			 " AND ( `online`.`hash` IS NOT NULL )";
							}
							if($matchSex and $sex)
							{
									$searchqueryT	.=			 " AND ($matchSex&main.sex)  AND (main.match_sex&$sex)";
							}
if($ageRandeTo and $ageRangeFrom)
							{
$searchqueryT	.=			 "AND YEAR(NOW())-YEAR(birthdate)- IF( DAYOFYEAR(birthdate) > DAYOFYEAR(NOW()),1,0) >= $ageRangeFrom AND 
 YEAR(NOW())-YEAR(birthdate)-IF( DAYOFYEAR(birthdate) > DAYOFYEAR(NOW()),1,0) <=$ageRandeTo";

}
$searchqueryT	.=" AND (main.status='active' ) AND main.profile_id!=$profileId 
ORDER BY has_photo DESC";


$db->Query($searchqueryT);
  $totalCount=$db->RowCount();
				if($zip)
				{
						$zip1 = explode(" ",$zip);
					
					
					$latlongsql		=	"SELECT `latitude` AS `lat`, `longitude` AS `lon` FROM `".$location_tbl."` WHERE `zip`='$zip1[0]'";
					
					if ($db->Query($latlongsql))
					{
						if($db->RowCount())
						{	
							$row			=	$db->Row();
							//print_r($row);
							$lat			=	$row->lat;
							$long			=	$row->lon;
						}
					}
				}
				mysql_query('SET CHARACTER SET utf8'); 
$searchquery = "SELECT DISTINCT  main.profile_id,username,sex,
CONCAT( '/','userfiles/thumb_', CAST(ph.profile_id AS CHAR ) , '_',CAST(photo_id AS CHAR ) , '_',
 CAST(ph.index AS CHAR ) , '.jpg' ) AS Profile_Pic,
FLOOR((TO_DAYS(NOW())- TO_DAYS(main.birthdate)) / 365.25) AS DOB,custom_location,Country_str_name
 
FROM

skadate_profile AS main
LEFT JOIN  skadate_profile_extended ex ON ex.profile_id=main.profile_id
LEFT JOIN skadate_profile_online online ON  ex.profile_id=online.profile_id
LEFT JOIN skadate_location_country ctbl ON main.country_id= ctbl.Country_str_code

LEFT JOIN skadate_profile_photo ph ON ph.profile_id=main.profile_id AND ph.number= 0";
if($zip and $milesFrom)
							{
								 $searchquery	.= " INNER JOIN 
										( SELECT `zip` FROM `$location_tbl` 
										WHERE 
										 ( 3963.0*acos
										 ( sin( ".($lat/57.29577951).") * sin(`latitude`/57.29577951) 
										 + cos(".($lat/57.29577951).") * cos(`latitude`/57.29577951)
										 * cos(`longitude`/57.29577951 --".($long/57.29577951).") ) )
										 <= '$milesFrom' )
										 AS `zip_third` ON ( `zip_third`.`zip` = `main`.`zip` )";
							} 

if($withPhotoOnly == 'y')
{
	$searchquery .= " WHERE ( `has_photo`='$withPhotoOnly' )";
}
else if($withPhotoOnly == 'n')
							{
									 $searchquery	.=		 	" WHERE ( `has_photo`='y'  OR `has_photo`='n')";
							}
							if($onlineOnly == 'y')
							{
									$searchquery	.=			 " AND ( `online`.`hash` IS NOT NULL )";
							}
							if($matchSex and $sex)
							{
									$searchquery	.=			 " AND ($matchSex&main.sex)  AND (main.match_sex&$sex)";
							}
if($ageRandeTo and $ageRangeFrom)
							{
$searchquery	.=			 "AND YEAR(NOW())-YEAR(birthdate)- IF( DAYOFYEAR(birthdate) > DAYOFYEAR(NOW()),1,0) >= $ageRangeFrom AND 
 YEAR(NOW())-YEAR(birthdate)-IF( DAYOFYEAR(birthdate) > DAYOFYEAR(NOW()),1,0) <=$ageRandeTo";

}
$searchquery	.=" AND (main.status='active' ) AND main.profile_id!=$profileId 
ORDER BY has_photo DESC LIMIT $start,$limit";
									 
//echo $searchquery;

				if ($db->Query($searchquery))
				{
					if($db->RowCount())
					{	
						$profile	=	'{"Total rows":'.$totalCount.',"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo $profile		= str_replace("},]", "}]", $profile);
					}
					else
					{
						echo $profile	=	'{"count": 0, "result": [0]}';
					}
				}
				
			}
	}
/**********search by limit ends here************/	
/**********new API for search events starts here************/
public function SearchEvents($id,$pid,$skey)
	{
		$essence	=	new Essentials();
		$secure     =   new secure();
		$event_table 	 =	$essence->tblPrefix().'event';
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		
		//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
		if (!$db->Error())
			{
				
				$sql	=	"SELECT id,title,description,create_date,start_date,end_date from  $event_table where title LIKE '%$id%'";
				if ($db->Query($sql))
				{
					if($db->RowCount())
					{	
						$profile	=	'{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace(",]}","]}",$profile);
					}
					else
					{
						echo $profile	=	'{"Status":"Live","count": 0, "result": [0]}';
					}
				}
			}
		}
		else
		{
			echo '{"Message":"Session Expired"}';
		}
			
	}		
	/**********new API for search events ends here************/
	/**********new API for search events  by Period wise starts here************/

public function SearchEventsByPeriod($kv)
	{
		$fromDate	=	isset($kv["fromDate"])?$kv["fromDate"]:NULL;
		$toDate 	=	isset($kv["toDate"])?$kv["toDate"]:NULL;
		$pid	=	isset($kv["pid"])?$kv["pid"]:NULL;
		$skey 	=	isset($kv["skey"])?$kv["skey"]:NULL;
		$essence	=	new Essentials();
		$event_table 	 =	$essence->tblPrefix().'event';
		$secure     =   new secure();

		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		//check user sign in or not
		$res = $secure->CheckSecure($pid,$skey);
		if($res==1)
		{
		if (!$db->Error())
			{
				if($fromDate and $toDate)
				{
				$sql	=	"SELECT id,title,description,FROM_UNIXTIME(create_date,'%Y %M %d') as create_date FROM  $event_table 
				             WHERE FROM_UNIXTIME(`create_date`, '%Y %M %d') >= '$fromDate' AND  FROM_UNIXTIME(`create_date`, '%Y %M %d')<= '$toDate'";
				}
				if ($db->Query($sql))
				{
					if($db->RowCount())
					{	
						$profile	=	'{"Status":"Live","count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
						echo str_replace(",]}","]}",$profile);
					}
					else
					{
						echo $profile	=	'{"Status":"Live","count": 0, "result": [0]}';
					}
				}
			}
	}		
		
		else
		{
			echo '{"Message":"Session Expired"}';
		}	
	}	
/**********new API for search events  by Period wise end here************/
/**********search with membership starts here************/
	public function sSearch($kv)
	{
		$secure     =   new secure();
		
		$profileId		=	isset($kv["id"])?$kv["id"]:NULL;
		$skey			=	isset($kv["skey"])?$kv["skey"]:NULL;	
		$res = $secure->CheckSecure($profileId,$skey);
		if($res==1)
		{
	    /*	
		 	Search Members MS2
			Input given
			Gender, LookingFor, AgeRangeFrom, AgeRangeTo, MilesFrom, ZIP, OnlineOnly, WithPhotoOnly
			Output Desired
			List of profiles each with DisplayName, Gender, Age, Place, OnlineStatus
		*/
		//print_r($kv);
		$this->actualSearch($kv);
		}
		else
		{
		echo '{"Message":"Session Expired"}';
		}
	}
	private function actualSearch($kv)
	{
	//	mysql_query('SET CHARACTER SET utf8'); 

		$profileId		=	isset($kv["id"])?$kv["id"]:NULL;
		$sex			=	isset($kv["sex"])?$kv["sex"]:NULL;
		$matchSex		=	isset($kv["matchSex"])?$kv["matchSex"]:NULL;
		$ageRangeFrom	=	isset($kv["ageRangeFrom"])?$kv["ageRangeFrom"]:NULL;
		$ageRandeTo		=	isset($kv["ageRangeTo"])?$kv["ageRangeTo"]:NULL;
		$milesFrom		=	isset($kv["milesFrom"])?$kv["milesFrom"]:NULL;
		$zip			=	isset($kv["zip"])?$kv["zip"]:NULL;
		$onlineOnly		=	isset($kv["onlineOnly"])?$kv["onlineOnly"]:'n';
		$withPhotoOnly	=	isset($kv["withPhotoOnly"])?$kv["withPhotoOnly"]:'n';
		if($milesFrom ==0)
		{
			$milesFrom =0.01;
		}
		$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		if (!$db->Error())
			{
				$profile_tbl			=	$essence->tblPrefix().'profile';
				$profile_tbl_extended	=	$essence->tblPrefix().'profile_extended';
				$pic_tbl				=	$essence->tblPrefix().'profile_photo';
				$profile_tbl_online		=	$essence->tblPrefix().'profile_online';
				$location_tbl			=	$essence->tblPrefix().'location_zip';
				$country_tbl			=	$essence->tblPrefix().'location_country';
				$membership_limit		=	$essence->tblPrefix().'link_membership_type_service';
		        $membership_srv		=	$essence->tblPrefix().'link_membership_service_limit'; 
				
				
				
				if($zip)
				{
					$zip1 = explode(" ",$zip);
					
					
					$latlongsql		=	"SELECT `latitude` AS `lat`, `longitude` AS `lon` FROM `".$location_tbl."` WHERE `zip`='$zip1[0]'";
					
					if ($db->Query($latlongsql))
					{
						if($db->RowCount())
						{	
							$row			=	$db->Row();
							//print_r($row);
							$lat			=	$row->lat;
							$long			=	$row->lon;
						}
					}
				}
				/*
				$searchquery	=	"SELECT * FROM	(SELECT *,hash as OnlineStatus, year(CURRENT_TIMESTAMP)-year(birthdate) as DOB FROM `$profile_tbl` AS `main` 
										LEFT JOIN `$profile_tbl_extended` AS `extend` USING( `profile_id` ) 
										LEFT JOIN `$profile_tbl_online` AS `online` USING( `profile_id` )
										LEFT JOIN `$country_tbl` AS `ctbl` ON (main.`country_id`= ctbl.`Country_str_code`)
										";
				*/
				$searchquery	=	"SELECT profile_id,username,sex,Profile_Pic,DOB,custom_location,Country_str_name  FROM	(SELECT `profile_id`, `email`,`username`, `password`, `sex`, `match_sex`, `birthdate`, `headline`, `general_description`, `match_agerange`, `custom_location`, `country_id`, main.`zip`, `state_id`, `city_id`, `join_stamp`, `activity_stamp`, `membership_type_id`, `affiliate_id`, `email_verified`, `reviewed`, `has_photo`, `has_media`, `status`, `featured`, `register_invite_score`, `rate_score`, main.`rates`, `language_id`, `join_ip`, `neigh_location`, `neigh_location_distance`, `bg_color`, `bg_image`, `bg_image_url`, `bg_image_mode`, `bg_image_status`, `has_music`, `is_private`,hash as OnlineStatus, FLOOR((TO_DAYS(NOW())- TO_DAYS(`birthdate`)) / 365.25) as DOB,Country_str_name FROM `$profile_tbl` AS `main` 
										LEFT JOIN `$profile_tbl_extended` AS `extend` USING( `profile_id` ) 
										LEFT JOIN `$profile_tbl_online` AS `online` USING( `profile_id` )
										LEFT JOIN `$country_tbl` AS `ctbl` ON (main.`country_id`= ctbl.`Country_str_code`)
										";
						
							if($zip and $milesFrom)
							{
								 $searchquery	.= " INNER JOIN 
										( SELECT `zip` FROM `$location_tbl` 
										WHERE 
										 ( 3963.0*acos
										 ( sin( ".($lat/57.29577951).") * sin(`latitude`/57.29577951) 
										 + cos(".($lat/57.29577951).") * cos(`latitude`/57.29577951)
										 * cos(`longitude`/57.29577951 --".($long/57.29577951).") ) )
										 <= '$milesFrom' )
										 AS `zip_third` ON ( `zip_third`.`zip` = `main`.`zip` )";
							} 
							if($withPhotoOnly == 'y')
							{
									 $searchquery	.=		 	" WHERE ( `has_photo`='$withPhotoOnly' )";
							}
							else if($withPhotoOnly == 'n')
							{
									 $searchquery	.=		 	" WHERE ( `has_photo`='y'  OR `has_photo`='n')";
							}
							if($onlineOnly == 'y')
							{
									$searchquery	.=			 " AND ( `online`.`hash` IS NOT NULL )";
							}
							if($matchSex and $sex)
							{
									$searchquery	.=			 " AND ($matchSex&main.sex)  AND (main.match_sex&$sex)";
							}
							if($ageRandeTo and $ageRangeFrom)
							{
									$searchquery	.=		 " AND YEAR(NOW())-YEAR(`main`.`birthdate`)- IF( DAYOFYEAR(`main`.`birthdate`) > DAYOFYEAR(NOW()),1,0) >= $ageRangeFrom 
										 AND YEAR(NOW())-YEAR(`main`.`birthdate`)-IF( DAYOFYEAR(`main`.`birthdate`) > DAYOFYEAR(NOW()),1,0) <=$ageRandeTo";
							}
									$searchquery	.=		 " AND ( `main`.`status`='active' ) AND `main`.`profile_id`!=$profileId ORDER BY `main`.`has_photo` 
										 DESC, IF( `online`.`profile_id` <> NULL, 0 , 1 ), `activity_stamp` DESC LIMIT 500)X
										 
										 LEFT JOIN 
( select photo_id,`$pic_tbl`.`profile_id` as profid, `index`, status, `number`, `description`,CONCAT( '/','userfiles/thumb_', CAST( $pic_tbl.profile_id AS CHAR ) , 
'_',CAST( $pic_tbl.photo_id AS CHAR ) , '_',
CAST( $pic_tbl.index AS CHAR ) , '.jpg' ) as Profile_Pic from
`$pic_tbl`)Y 
ON X.`profile_id` = Y.`profid` AND Y.`number` = 0";
										 
				//echo $searchquery;						 
								
						if ($db->Query($searchquery))
						{
							if($db->RowCount())
							{	
								$profile	=	'{"Total rows":'.$db->RowCount().',"count": '.$db->RowCount().',"result": ['.$db->GetJSON().']}';
								echo $profile		= str_replace("},]", "}]", $profile);
							}
							else
							{
								echo $profile	=	'{"count": 0, "result": [0]}';
							}
						}
			    
		}
			
}
/**********search with membership ends here************/

			
	

}
?>
