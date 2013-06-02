<?php

/**
 * Class for working with temporary profile list ( search results )
 * 
 *
 * @package SkaDate
 * @link http://www.skadate.com
 * @version 5.0
 */
class app_TempProfileList
{
	/**
	 * Create temporary profile list in database table.
	 * Returns array with following items:<br />
	 * <li>profile_list_id - ID of created profile list</li>
	 * <li>profile_list_total - total number of profiles in profile list</li>
	 *
	 * @param string $list_type
	 * @param array $profiles_arr
	 *
	 * @return array
	 */
public static function Create( $list_type, $profiles_arr )
	{
		if ( !is_array( $profiles_arr ) )
			return array();		
		
		$_query = SK_MySQL::placeholder( "INSERT INTO `".TBL_TMP_PR_LIST."`(`form_command`, `expiration_time`) 
			VALUES( '?',? )", $list_type, ( time() + SEARCH_RESULT_EMPTY_TIMEOUT ) );

		SK_MySQL::query($_query);

		$_result['profile_list_id'] = SK_MySQL::insert_id();

		$_result['profile_list_total'] = count( $profiles_arr );
		
		$_counter = 0;
	
		$_comipled_query = sql_compile_placeholder( "INSERT INTO `".TBL_LINK_PR_LIST_PR."`( `profile_list_id`, `profile_id`, `result_number` ) 
			VALUES( ?@ )" );

		foreach ( $profiles_arr as $_profile_id )
		{
			$_counter++;
			
			$_query = sql_placeholder( $_comipled_query, array( $_result['profile_list_id'], $_profile_id, $_counter ) );
			MySQL::fetchResource( $_query );
		}	
		
		return $_result;
	}
	
	/**
	 * Delete temporary profile lst from database table
	 *
	 * @param integer $profile_list_id
	 *
	 * @return integer
	 */
	public static function Delete( $profile_list_id )
	{
		if ( !is_numeric( $profile_list_id ) || !intval( $profile_list_id ) )
			return 0;
		
		$_query = "DELETE FROM `".TBL_LINK_PR_LIST_PR."` WHERE `profile_list_id`='$profile_list_id'";
		$_affected_rows = MySQL::AffectedRows( $_query );
		
		$_query = "DELETE FROM `".TBL_TMP_PR_LIST."` WHERE `profile_list_id`='$profile_list_id'";
		$_affected_rows += MySQL::AffectedRows( $_query );	
		
		return $_affected_rows;
	}
	
	/**
	 * Returns next profile ID in temporary profile list,
	 * followed by specified profile number in list
	 *
	 * @param integer $profile_list_info
	 * @param integer $result_number
	 *
	 * @return integer
	 */
	public static function getNextProfileId( $profile_list_id, $result_number )
	{
		if ( !is_numeric( $result_number ) || !intval( $result_number ) || $result_number == SEARCH_RESULT_MAX )
			return false;		
				
		$_query = sql_placeholder( "SELECT `profile_id` FROM `".TBL_LINK_PR_LIST_PR."` 
			WHERE `profile_list_id`=? AND `result_number`=?", $profile_list_id, ($result_number+1) );
		
		return MySQL::FetchField( $_query );
	}
	
	
	/**
	 * Returns previous profile ID in temporary profile list,
	 * followed by specified profile number in list
	 *
	 * @param integer $profile_list_info
	 * @param integer $result_number
	 *
	 * @return integer
	 */
	public static function getPrevProfileId( $profile_list_id, $result_number )
	{
		if ( !is_numeric( $result_number ) || !intval( $result_number ) || $result_number == 1)
			return false;		
			
		$_query = sql_placeholder( "SELECT `profile_id` FROM `".TBL_LINK_PR_LIST_PR."` 
			WHERE `profile_list_id`=? AND `result_number`=?", $profile_list_id, ($result_number-1) );
		
		return MySQL::FetchField( $_query );
	}

        function getNumberByProfileId( $profile_list_id, $profileId )
	{
		if ( !is_numeric( $profileId ) || !intval( $profileId ) )
			return false;

		$_query = sql_placeholder( "SELECT `result_number` FROM `".TBL_LINK_PR_LIST_PR."`
			WHERE `profile_list_id`=? AND `profile_id`=?", $profile_list_id, $profileId );

		return MySQL::FetchField( $_query );
	}
	
	/**
	 * Returns info about profile list by number of profile in list.
	 * Returened array contains this items:<br />
	 * <li>page - page number of profile in list</li>
	 * <li>form_command - search type</li>
	 *
	 * @param integer $list_id
	 * @param integer $result_number
	 *
	 * @return array
	 */
	public static function getListInfoByNumber( $list_id, $result_number )
	{
		if ( !is_numeric( $list_id ) || !intval( $list_id ) )
			return array();
		
		if ( !is_numeric( $result_number ) || !intval( $result_number ) )
			return array();
		
		$_query = sql_placeholder( "SELECT `result_number` FROM `".TBL_LINK_PR_LIST_PR."` 
			WHERE `result_number`<=? AND `profile_list_id`=?", $result_number, $list_id );
		
		$_results = MySQL::fetchArray( $_query, 0 );
		
		
		$_result['page'] = ceil( count( $_results)/getConfig( 'profile_list_result_per_page' ) );
		
		
		$_query = sql_placeholder( "SELECT `form_command` FROM `".TBL_TMP_PR_LIST."` 
			WHERE `profile_list_id`=?", $list_id );
		
		$_result['form_command'] = MySQL::FetchField( $_query );
		
		return $_result;
	}
	
	/**
	 * Register info about profile list in session
	 *
	 * @param string $list_type
	 * @param array $params
	 *
	 * @return boolean
	 */
	public static function setInfoInSession( $list_type, $params )
	{
		// check input data
		if ( !strlen( trim( $list_type ) ) )
			return false;
		
			
		switch ( $list_type )
		{
			case 'search':
				$_SESSION['search_result']['profile_list_id'] = intval( $params['list_id'] );
				$_SESSION['search_result']['profile_list_total'] = intval( $params['pr_total'] );
				break;
		}
		
		return true;
	}
	
	/**
	 * Delete info about temporary profile list from session
	 *
	 * @param string $list_type
	 *
	 * @return boolean
	 */
	public static function deleteListInfoFromSession( $list_type )
	{
		// check input data
		if ( !strlen( trim( $list_type ) ) )
			return false;
			
		switch ( $list_type )
		{
			case 'search':
				unset( $_SESSION['search_result']['profile_list_id'] );
				unset( $_SESSION['search_result']['profile_list_total'] );		
				break;
		}
			
		return true;
	}
	
	/**
	 * Returns info about temporary profile list from session.
	 * Second argument is array with following items:<br />
	 * <li>var_name - name of variable with list info ( list_id, pr_total )</li>
	 *
	 * @param string $list_type
	 * @param array $params
	 *
	 * @return integer
	 */
	public static function getListSessionInfo( $list_type, $var )
	{
		if ( !strlen( trim( $list_type ) ) )
			return false;
			
		switch ( $list_type )
		{
			case 'search':
				switch ( $var )
				{
					case 'list_id':
						return @$_SESSION['search_result']['profile_list_id'];
					case 'pr_total':
						return @$_SESSION['search_result']['profile_list_total'];
					default:
						return false;			 
				}		
		}	
	}
	
	/**
	 * Delete expired search result from temp table
	 *
	 * @return boolean
	 */
	public static function deleteExpiredResults()
	{
		$_query = sql_placeholder( "SELECT `profile_list_id` FROM `".TBL_TMP_PR_LIST."`
			WHERE `expiration_time` < ?", time() );
		$_sresult_arr = MySQL::fetchArray( $_query );
		
		if ( !$_sresult_arr )
			return false;

        $list = array();
		foreach ( $_sresult_arr as $_key => $_value )
		{
            $list[$_value['profile_list_id']] = $_value['profile_list_id'];
		}

        if ( empty($list) )
        {
            return true;
        }

        $_query = SK_MySQL::placeholder( "DELETE FROM `".TBL_TMP_PR_LIST."`
        WHERE `profile_list_id` IN ( '?@' ) ", $list );
        MySQL::fetchResource( $_query );

        $_query = SK_MySQL::placeholder( "DELETE FROM `".TBL_LINK_PR_LIST_PR."`
        WHERE `profile_list_id` IN ( '?@' ) ", $list );
        MySQL::fetchResource( $_query );
		
		return true;
	}
}
?>