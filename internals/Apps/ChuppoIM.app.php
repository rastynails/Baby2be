<?php

class app_ChuppoIM
{
	/**
	 * Returns an IM session info.
	 *
	 * @param integer $profile_id
	 * @param integer $opponent_id
	 * @return array session info or false if no session found
	 */
	public static function getSession( $profile_id, $opponent_id )
	{
		if (
			!( $profile_id = (int)$profile_id ) ||
			!( $opponent_id = (int)$opponent_id )
		) trigger_error(
			'Invalid argument passed to '.__CLASS__.'::'.__FUNCTION__.'( int $profile_id = '.$profile_id.', int $opponent_id = '.$opponent_id.')',
			E_USER_ERROR
		);
		
		$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_CHUPPO_IM_SESSION."`
			WHERE (`session_opener_id`=? AND `opponent_id`=? )", $opponent_id, $profile_id);		
				
		return SK_MySQL::query($query)->fetch_assoc();
	}
	
	
	public static function deleteReceivedSession( $profile_id, $sender_id  )
	{
		if (
			!( $profile_id = (int)$profile_id ) ||
			!( $sender_id = (int)$sender_id )
		) trigger_error(
			'Invalid argument passed to '.__CLASS__.'::'.__FUNCTION__.'( int $profile_id = '.$profile_id.', int $opponent_id = '.$opponent_id.')',
			E_USER_ERROR
		);
		
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_CHUPPO_IM_SESSION."`
			WHERE (`session_opener_id`=? AND `opponent_id`=?)", $sender_id, $profile_id );
		SK_MySQL::query($query);
		
		return SK_MySQL::affected_rows();
	}
	
	public static function createSession( $profile_id, $opponent_id )
	{
		$session = array();
		
		if (
			!( $session['session_opener_id'] = (int)$profile_id ) ||
			!( $session['opponent_id'] = (int)$opponent_id )
		) trigger_error(
			'Invalid argument passed to '.__CLASS__.'::'.__FUNCTION__.'( int $profile_id = '.$profile_id.', int $opponent_id = '.$opponent_id.')',
			E_USER_ERROR
		);
		
		$session['start_stamp'] = time();
	
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_CHUPPO_IM_SESSION."` 
			WHERE `session_opener_id`=? AND `opponent_id`=?", $session['session_opener_id'], $session['opponent_id'] );
		SK_MySQL::query($query);
		
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_CHUPPO_IM_SESSION."` 
			( `session_opener_id`, `opponent_id`, `start_stamp` ) VALUES( ?, ?, ? )",
			 $session['session_opener_id'], $session['opponent_id'], $session['start_stamp'] );
			
		SK_MySQL::query($query);
		
		$session['im_session_id'] = SK_MySQL::insert_id();
		
		return $session;
	}
}

?>