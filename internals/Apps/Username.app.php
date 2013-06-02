<?php

class app_Username
{
	public static function addUsename( $username )
	{
		if ( !strlen( trim( $username ) ) )
			return -1;
			
		if ( in_array( trim( $username ), app_Username::getRestrictedListArray() ) )
			return -2;
		
		$query = sql_placeholder( "INSERT INTO `?#TBL_REST_USERNAME` ( `username` ) VALUES( ?@ )", array( trim( $username ) ) );
		
		return MySQL::insertId( $query );
	}
	
	public static function getRestrictedListArray()
	{
		$query = sql_placeholder( "SELECT * FROM `?#TBL_REST_USERNAME`" );
		
		$return_array = array();
		
		foreach ( MySQL::fetchArray( $query ) as $value )
			$return_array[$value['username_id']] = $value['username'];
			
		return $return_array;
	}
	
	public static function getUsedListArray()
	{
		$query = sql_placeholder( "SELECT `username` FROM `?#TBL_PROFILE`" );
		
		$return_array = array();
		
		foreach ( MySQL::fetchArray( $query ) as $value )
			$return_array[] = $value['username'];
			
		return $return_array;
		
	}
	
	public static function isUsernameRegistered( $username )
	{
		$query = sql_placeholder( "SELECT COUNT(*) FROM `?#TBL_PROFILE` WHERE `username`=?", trim( $username ) );
		
		return (int)MySQL::fetchField( $query );
	}
	
	public static function isUsernameInRestrictedList( $username )
	{
		$query = sql_placeholder( "SELECT COUNT(*) FROM `?#TBL_REST_USERNAME` WHERE `username`=?", trim( $username ) );
		
		return (int)MySQL::fetchField( $query );
	}
	
	public static function checkUsername( $username )
	{
		if ( !strlen( trim( $username ) ) )
			return -1;
			
		if ( app_Username::isUsernameInRestrictedList( $username ) )
			return -2;
			
		if ( app_Username::isUsernameRegistered( $username ) )
			return -3;
		
		$regexp = MySQL::fetchField(sql_placeholder("SELECT `regexp` from `?#TBL_PROF_FIELD` where `name`='username'"));	
			
		if( $regexp && !preg_match($regexp,$username) )
			return -4;	
				
		return 1;
		
	}
	
	public static function getRestrictedList( $page = 1, $num_on_page = 30 )
	{
		$usernames = MySQL::fetchArray( sql_placeholder( "SELECT * FROM `?#TBL_REST_USERNAME` LIMIT ?, ?", ( $page-1 ) * $num_on_page, $num_on_page ) );
		
		$num_of_pages = intval( intval( $num = MySQL::fetchField( sql_placeholder( "SELECT COUNT(*) FROM `?#TBL_REST_USERNAME`" ) ) )/$num_on_page ) + ( $num % $num_on_page > 0 ? 1 : 0 );
		
		$pages = array();
		
		for ( $i=1;$i<=$num_of_pages;$i++ )
			$pages[$i] = array( 'label' => $i, 'url' => ( $page == $i ? false : URL_ADMIN.'rest_username.php?page='.$i ) );
			
		return array( 'usernames' => $usernames, 'pages' => $pages );

	}
	
	public static function deleteUsernames( $del_array )
	{
		if ( sizeof( $del_array ) == 0 )
			return false;
		
		$del_array = array_keys($del_array);
			
		return MySQL::affectedRows( sql_placeholder("DELETE FROM `?#TBL_REST_USERNAME` WHERE `username_id` IN (?@)", $del_array) );
	}
	
	
	
}

