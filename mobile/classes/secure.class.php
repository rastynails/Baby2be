<?php 
/*
Project 	  : Skadate
Author        : Gopika.G
Created Date  : 11-04-2012
Modified By   : 
Modified Date :
*/
/************class for secure checking********/

class secure
{
	public function CheckSecure($id,$hash)
	{
		$essence	=	new Essentials();
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());
		$profile_tbl			=	$essence->tblPrefix().'profile';
		$profile_tbl_extended	=	$essence->tblPrefix().'profile_extended';
		$profile_online			=	$essence->tblPrefix().'profile_online';
		$flag = 0;
		$sql = "SELECT * FROM $profile_online WHERE hash = '$hash' and profile_id = '$id'";
		$sqlExe = $db->Query($sql);
		$sqlExeResult = mysql_fetch_array($sqlExe);
		if($sqlExeResult==NULL)
		{
		  $flag = 0;
		}
		else
		{
			$flag = 1;
		}
		return $flag;
	}
}
?>