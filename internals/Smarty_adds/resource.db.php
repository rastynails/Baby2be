<?php

/**
 * Smarty plugin
 * 
 * File:     resource.db.php
 * Type:     resource
 * Name:     db
 * Gets template from db
 * 
 */

function smarty_resource_db_source( $tpl_str, &$tpl_source, &$smarty )
{
    $tpl = explode(':', $tpl_str);
    
    if ( !strlen($tpl[0]) || !strlen($tpl[1]) )
    {
        return false;
    }
    
	$query = SK_MySQL::placeholder("SELECT `".$tpl[1]."` FROM `" .TBL_THEME."` 
	   WHERE `theme_name`='?'", $tpl[0]);
	
	$res = SK_MySQL::query($query);
	
	if ( SK_MySQL::affected_rows() ) 
	{
		$tpl_source = $res->fetch_cell();
		return true;
	} 
	else 
	{
		return false;
	}
}

function smarty_resource_db_timestamp( $tpl_str, &$tpl_timestamp, &$smarty )
{
    $tpl = explode(':', $tpl_str);
    
    if ( !strlen($tpl[0]) || !strlen($tpl[1]) )
    {
        return false;
    }
    
	$query = SK_MySQL::placeholder("SELECT `tpl_timestamp` FROM `" .TBL_THEME."` 
        WHERE `theme_name`='?'", $tpl[0]);
	
	$res = SK_MySQL::query($query);
	
	if ( SK_MySQL::affected_rows() ) 
	{
		$tpl_timestamp = $res->fetch_cell();
		return true;
	} 
	else 
	{
		return false;
	}
}

function smarty_resource_db_secure( $theme_name, &$smarty )
{
    return true;
}

function smarty_resource_db_trusted( $theme_name, &$smarty )
{
	return true;
}
