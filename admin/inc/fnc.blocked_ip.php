<?php

function searchBlockedIp( $ip )
{
	if ( !$ip )
		return '';
		
	$_query = sql_placeholder( "SELECT INET_NTOA( `block_ip` ) 
		FROM `".TBL_BLOCK_IP."` WHERE `block_ip`=INET_ATON( ? )", $ip );
	
	return MySQL::fetchField( $_query );
}


function deleteBlockedIp( $ip )
{
	if ( !$ip )
		return false;
		
	$_query = sql_placeholder( "DELETE FROM `".TBL_BLOCK_IP."` 
		WHERE `block_ip`=INET_ATON( ? )", $ip );
	
	return MySQL::affectedRows( $_query );
}

function addBlockedIp( $ip )
{
	if ( !$ip )
		return -1;
		
	// detect if IP exists
	$_query = sql_placeholder( "SELECT `block_ip` FROM `".TBL_BLOCK_IP."`
		WHERE `block_ip`=INET_ATON( ? )", $ip );
	
	if ( MySQL::fetchField( $_query ) )
		return -2;
		
	$_query = sql_placeholder( "INSERT INTO `".TBL_BLOCK_IP."`( block_ip )
		VALUES( INET_ATON(?) )", $ip );
	
	MySQL::fetchResource( $_query );
	
	return 1;
}

function getBlockedIpList( $page, $limit )
{
	$page = intval( $page );
	$limit = intval( $limit );
	
	if ( !$page )
		$page = 1;
		
	if ( !$limit )
		$limit = 10;
		
	$_query = "SELECT INET_NTOA(`block_ip`) AS `block_ip` FROM `".TBL_BLOCK_IP."` 
		LIMIT ".$limit*($page-1).", $limit";
	
	$_return['list'] = MySQL::fetchArray( $_query, 0 );
	
	$_query = "SELECT COUNT(*) FROM `".TBL_BLOCK_IP."`";
	$_return['total'] = MySQL::fetchField( $_query );
	
	return $_return;
}
?>