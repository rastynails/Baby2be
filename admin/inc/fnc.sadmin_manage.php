<?php
function addSAdmin($admin_username, $admin_password, $admin_email)
{
	switch (true)
	{
		case ( !strlen($_admin_username = $admin_username) ):
			return -1;
			break;
			
		case ( !strlen($_admin_password = $admin_password) ):
			return -2;
			break;
			
	}
	
	$config = SK_Config::section('site')->Section('admin');
	
	if($config->admin_username == $_admin_username)	
		return -4;
		
	$_query = sql_placeholder("SELECT `admin_id` FROM `?#TBL_ADMIN` WHERE `admin_username`=?", $_admin_username);
	if(MySQL::fetchField($_query))
		return -3;
		
	$_admin_password = app_Passwords::hashPassword($_admin_password);	
		
	$_query = (strlen($admin_email))?
		sql_placeholder("INSERT INTO `?#TBL_ADMIN`(`admin_id`, `admin_username`, `admin_password`, `email`) VALUES(null, ?, ?, ?)", 
			$_admin_username, $_admin_password, $admin_email) :
		sql_placeholder("INSERT INTO `?#TBL_ADMIN`(`admin_id`, `admin_username`, `admin_password`, `email`) VALUES(null, ?, ?, '')", 
			$_admin_username, $_admin_password); 
				
	return MySQL::insertId($_query); 
}

function deleteSAdmin($admin_id)
{
	if(!intval($_admin_id = $admin_id)) 
		return -1;
		
		$_query = sql_placeholder("DELETE FROM `?#TBL_ADMIN` WHERE `admin_id`=?", $_admin_id);
		MySQL::fetchResource($_query);
		return true;
}

function updateSAdminInfo($admin_id, $admin_username, $admin_password, $admin_email)
{
	switch(true)
	{
		case (!intval($_admin_id = $admin_id)):
			return -1;

		case (!strlen($_admin_username = $admin_username)):
			return -2;
			
		case(( strlen($_admin_email = $admin_email) && !preg_match('/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-]+?.[a-zA-Z0-9_]{2,}(\.\w{2})?$/i', $_admin_email )) ):
			return -4;
	}
	$_admin_email = (strlen($_admin_email))?$_admin_email:'';	
	
	$_query = sql_placeholder("SELECT `admin_username` FROM `?#TBL_ADMIN` WHERE `admin_username` = ? AND `admin_id` !=?", $_admin_username, $_admin_id);
	if(MySQL::fetchField($_query))
		return  -5;
		
	$passSql = empty($admin_password) ? '' : SK_MySQL::placeholder(", `admin_password`='?'", app_Passwords::hashPassword($admin_password));	
		
	$_query = sql_placeholder("UPDATE `?#TBL_ADMIN` SET `admin_username`=?, `email`=? $passSql WHERE `admin_id`=?", 
		$_admin_username,  $_admin_email, $_admin_id);
	Mysql::fetchResource($_query);
	return true;		
}


function setSectionsAccess2SAdmin($admin_id, $sections)
{
	switch(true)
	{
		case (!intval($_admin_id = $admin_id)):
			return -1;
			break;
		case (!count($_sections = $sections)):
			return -2;
			break;			
	}
	
	$_query = sql_placeholder("DELETE FROM `?#TBL_LINK_ADMIN_DOCUMENT` WHERE `admin_id`=?", $_admin_id);
	MySQL::fetchResource($_query);
	
	foreach ($_sections as $_section)
	{
		$_query = sql_placeholder("INSERT INTO `?#TBL_LINK_ADMIN_DOCUMENT`(`admin_id`, `file_key`) VALUES(?, ?)", $_admin_id, $_section);
		MySQL::fetchResource($_query);
	}
	
	return true;
}
?>
