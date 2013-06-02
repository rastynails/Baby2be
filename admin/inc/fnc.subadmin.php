<?php

function getAdminId()
{
	if( !(  strlen($admin_username = $_SESSION['administration']['admin_username'])) || !( strlen($admin_password = $_SESSION['administration']['admin_password'])) )
		return false;
	
	$_query = sql_placeholder("SELECT `admin_id` FROM `?#TBL_ADMIN` WHERE `admin_username`=? AND `admin_password`=?", $admin_username, $admin_password);

	return MySQL::fetchField($_query);
}

function getSubAdminInfo($admin_id=null)
{
	$_query = ( $admin_id = intval($admin_id) )?
		sql_placeholder("SELECT * FROM `?#TBL_ADMIN` WHERE `admin_id`=?", $admin_id):
		sql_placeholder("SELECT * FROM `?#TBL_ADMIN`");
		return MySQL::fetchArray($_query);
}

function isSAdmin()
{
    if( $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'] )
    {
        return true;
    }

	$admin_username = $_SESSION['administration']['admin_username'];
	$admin_password = $_SESSION['administration']['admin_password'];
	
	if( ( !strlen($admin_password) || !strlen($admin_username) ) )
		return false;
		
	$_query = sql_placeholder("SELECT `admin_id` FROM `?#TBL_ADMIN` WHERE `admin_username`=? AND `admin_password`=?", $admin_username, $admin_password);
	if(MySQL::fetchField($_query))
		return true;	
		
	return false;	
}

function isSAdminSectionAccessControl($admin_id, $file_key)
{
	switch (true)
	{
		case ( !(strlen($_file_key = $file_key)) ):
			return -1;
			break;
			
		case ( !(intval($_admin_id = $admin_id)) ):
			return -2;
			break;
	}
		
	$_query = sql_placeholder("SELECT `file_key` FROM `?#TBL_LINK_ADMIN_DOCUMENT` WHERE `file_key`=? AND `admin_id`=?", $_file_key, $_admin_id );
	if(MySQL::fetchField($_query))
		return true;
		
	return false;
}

function getSAdminDefaultPageUrl($admin_id)
{
	global $sidebar_menu_items;
	
	if(!($_admin_id = intval($admin_id)))
	{
		return URL_ADMIN.'logout.php';
	}
		
	$_query = sql_placeholder("SELECT `file_key` FROM `?#TBL_LINK_ADMIN_DOCUMENT` WHERE `admin_id`=?", $_admin_id);
	$_file_key_set = MySQL::fetchArray($_query);

	if(!count($_file_key_set))
		return URL_ADMIN.'logout.php';
		
	foreach ($_file_key_set as $row)
	{
		$in_array[] = $row['file_key'];
	}
	
	foreach ($sidebar_menu_items[0]['items'] as $key=>$value)
	{
		if( in_array($key, $in_array) )
			return $value['href'];
	}
	
	foreach ($sidebar_menu_items[1]['items'] as $key=>$value)
	{
		if( in_array($key, $in_array) )
			return $value['href'];
	}	
	
	return URL_ADMIN.'logout.php';
}

function getSadminAllowedSections($admin_id)
{
	if(!intval($_admin_id = $admin_id))
		return -1;
		
	$_query = sql_placeholder("SELECT * FROM `?#TBL_LINK_ADMIN_DOCUMENT` WHERE `admin_id`=?", $_admin_id);
	return MySQL::fetchArray($_query);
}
