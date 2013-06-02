<?php

/**
 * This class was depricated and implemented for backward compatibility only,
 * In designing a new applicatrions use SK_MySQL enstead.
 * MySQL class allows you to access MySQL database server.
 *
 * @package SkaDate6
 */
class MySQL extends SK_MySQL
{
	/**
	 * Sends an SQL query and return a resource result
	 *
	 * @param string $query
	 * @return resource result
	 */
	public static function fetchResource( $query )
	{
        self::$logger->loggerStart($query);
		$result = mysql_query($query, parent::$link_id);
		self::$logger->loggerStop();
        
		if ( $result === false ) {
			self::error($query);
		}
		
		return $result;
	}
	
	
	/**
	 * Fetch a result row as a numbered array
	 *
	 * @param resource $result
	 * @return array
	 */
	public static function resource2NumArray( $result )
	{
        self::$resourcelogger->loggerStart(null);
        $result = mysql_fetch_array($result, MYSQL_NUM);
        self::$resourcelogger->loggerStop();

		return $result;
	}
	
	
	/**
	 * Fetch a result row as an associative array
	 *
	 * @param resource $result
	 * @return array
	 */
	public static function resource2Assoc( $result )
	{
        self::$resourcelogger->loggerStart(null);
        $result = mysql_fetch_array($result, MYSQL_ASSOC);
        self::$resourcelogger->loggerStop();

        return $result;
	}
	
	/**
	 * Sends an SQL query and returns the number of affected rows
	 *
	 * @param string $query
	 * @return integer
	 */
	public static function affectedRows( $query )
	{
		MySQL::fetchResource($query);

        self::$resourcelogger->loggerStart($query);
        $result = mysql_affected_rows(parent::$link_id);
        self::$resourcelogger->loggerStop();

		return $result;
	}
	
	
	/**
	 * Sends an SQL query and returns first field of a result row
	 * or <code>null</code> if empty
	 *
	 * @param string $query
	 * @return string|null
	 */
	public static function fetchField( $query )
	{
		$result = MySQL::fetchResource($query);
		
        self::$resourcelogger->loggerStart($query);
        $array = mysql_fetch_array($result, MYSQL_NUM);
        self::$resourcelogger->loggerStop();
		
		return $array ? $array[0] : null;
	}
	
	
	/**
	 * Sends an SQL query
	 * and returns one row of mysql result
	 * in form of associative array
	 *
	 * @param string $query
	 * @return array
	 */
	public static function fetchRow( $query )
	{
		$result = MySQL::fetchResource($query);

        self::$resourcelogger->loggerStart($query);
		$array = mysql_fetch_assoc($result);
        self::$resourcelogger->loggerStop();
		
		return $array;
	}
	
	
	/**
	 * Sends an SQL query
	 * and returns multidemention array
	 * Example:
	 * <code>
	 * $mysql = new MySQL();
	 * $result = $mysql->FetchArray( "SELECT `user_id`, `name`, `email`, `join_stamp` FROM `tbl_user` WHERE `status`='active'" );
	 * </code>
	 * <pre>
	 * $result = array
	 * (
	 *     '0' => array
	 *     (
	 * 	       'user_id'    =>   '123',
	 * 	       'name'       =>   'Jane',
	 * 	       'email'      =>   'example@mail.com',
	 *	       'join_stamp' =>   '1141721482'
	 *     ),
	 *
	 *     '1' => array
	 *     (
	 * 	       'user_id'    =>   '212',
	 * 	       'name'       =>   'Alex',
	 * 	       'email'      =>   'example@mail.com',
	 *	       'join_stamp' =>   '1135812576'
	 *     ),
	 * )
	 * </pre>
	 *
	 * If second optional parametr <var>$key</var> is not <i>null</i>
	 * <var>$key</var> field value will used as array keys
	 * Example:
	 * <code>
	 * $mysql = new MySQL();
	 * $result = $mysql->FetchArray( "SELECT * FROM `tbl_user` WHERE `membership_type`='gold'", 'user_id' );
	 * </code>
	 * <pre>
	 * $result = array
	 * (
	 *     '56' => array
	 *     (
	 * 	       'user_id'           =>   '56',
	 *         'membership_type'   =>   'gold',
	 * 	       'name'              =>   'Erix',
	 * 	       'email'             =>   'example@mail.com',
	 *	       'join_stamp'        =>   '1145657436'
	 *     ),
	 *
	 *     '212' => array
	 *     (
	 * 	       'user_id'           =>   '212',
	 *         'membership_type'   =>   'gold',
	 * 	       'name'              =>   'Alex',
	 * 	       'email'             =>   'example@mail.com',
	 *	       'join_stamp'        =>   '1135812576'
	 *     ),
	 * )
	 * </pre>
	 * 
	 * @param string $query
	 * @param string $key (optional)
	 * @return array
	 */
	public static function fetchArray( $query, $key = null )
	{
		$_result = MySQL::fetchResource($query);
		
		$array = array();

        self::$resourcelogger->loggerStart($query);
		switch( true )
		{
			case ($key === 0):
				
				while( $_row = mysql_fetch_array($_result, MYSQL_NUM) )
					$array[] = $_row[0];
				
				break;
			
			case ($key === 1):
				
				while( $_row = mysql_fetch_array($_result, MYSQL_NUM) )
					$array[$_row[0]] = $_row[1];
				
				break;
			
			case is_string($key):
				
				while( $_row = MySQL::resource2Assoc($_result) )
					$array[$_row[$key]] = $_row;
				
				break;
			
			default:
			
				while( $_row = MySQL::resource2Assoc($_result) )
					$array[] = $_row;
		}
        self::$resourcelogger->loggerStop();
		
		return $array;
	}
	
	
	/**
	 * Sends an SQL query
	 * and returns multidemention array
	 * Example:
	 * <code>
	 * $mysql = new MySQL();
	 * $result = $mysql->FetchMultiSectionArray( "SELECT `user_id`, `membership_type`, `name` FROM `tbl_user`", 'membership_type', 'user_id' );
	 * </code>
	 * <pre>
	 * $result = array
	 * (
	 *     'gold' => array
	 *     (
	 *         '56' => array
	 *         (
	 * 	           'user_id'           =>   '56',
	 *             'membership_type'   =>   'gold',
	 * 	           'name'              =>   'Erix'
	 *         ),
	 *
	 *         '212' => array
	 *         (
	 * 	           'user_id'           =>   '212',
	 *             'membership_type'   =>   'gold',
	 * 	           'name'              =>   'Alex'
	 *     ),
	 *
	 *     'free' => array
	 *     (
	 *         '12' => array
	 *         (
	 * 	           'user_id'           =>   '12',
	 *             'membership_type'   =>   'free',
	 * 	           'name'              =>   'Bob'
	 *         ),
	 *
	 *         '123' => array
	 *         (
	 * 	           'user_id'           =>   '123',
	 *             'membership_type'   =>   'free',
	 * 	           'name'              =>   'Jane'
	 *     ),
	 * )
	 * </pre>
	 *
	 * @param string $query
	 * @param string $section
	 * @param string $key (optional)
	 * @return array
	 */
	public static function fetchMultiSectionArray( $query, $section, $key = null )
	{
		$result = MySQL::fetchResource($query);
		
		$array = array();
		self::$resourcelogger->loggerStart($query);
		if( $key )
			while( $row = mysql_fetch_assoc($result) )
				$array[$row[$section]][$row[$key]] = $row;
		else
			while( $row = mysql_fetch_assoc($result) )
				$array[$row[$section]][] = $row;
        self::$resourcelogger->loggerStop();
		
		return $array;
	}
	
	/**
	 * Sends an SQL qeury 
	 * and returns last inserted id
	 *
	 * @param string $query
	 * @return integer
	 */
	
	public static function insertId( $query )
	{
		MySQL::fetchResource($query);
		
		return mysql_insert_id();
	}
	
	/**
	 * Debug MySQL method Error
	 * Prints a last occured error
	 *
	 * @param string $query
	 * @return boolean always false
	 */
	public static function error( $query )
	{
		$err_msg = sprintf(
			'MySQL error: <code>%s</code><br />in query: <code>%s</code><br />'
			, mysql_error(), $query
		);
		
		trigger_error($err_msg, E_USER_WARNING);
		
		return false;
	}
	
	/**#@-*/
}
