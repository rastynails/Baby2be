<?php 
/*
Project 	  : Skadate
Author        : Gopika.G
Created Date  : 11-04-2012
Modified By   : 
Modified Date :
*/
/************class for signout********/
class signout
{
	private $id = "";
	
	
	public function CheckSignOut($id)
	{
		$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$profile_tbl			=	$essence->tblPrefix().'profile';
		$profile_tbl_extended	=	$essence->tblPrefix().'profile_extended';
		$profile_online			=	$essence->tblPrefix().'profile_online';
		$location_map			=	$essence->tblPrefix().'location_map';
		
		//check user available
		$sql0 ="SELECT * FROM $profile_online WHERE profile_id = '$id'";
		$result = $db->Query($sql0);
		$resultArry = mysql_fetch_array($result);
		if($resultArry!=NULL)
		{
		
			$sql = "DELETE FROM $profile_online WHERE profile_id='$id'";
			if($db->Query($sql))
			{
			$sqlMap = "DELETE FROM $location_map WHERE profile_id='$id'";
			$db->Query($sqlMap);
				echo '{"Message":"Sign out successfully"}';
			}
			else
			{
			  echo '{"Message":"Failed"}';
			}
	     }
		 else
		 {
			  echo '{"Message":"Cant Sign Out"}';
		 }
     }
}	 
	 
?>
