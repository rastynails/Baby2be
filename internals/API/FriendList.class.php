<?php

class SK_FriendList
{
	/**
	 * Returns friends `profile_id` index list (array($profile_id => $profile_id)).
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public static function friend_id_index( $profile_id )
	{
		if ( !($profile_id = (int)$profile_id) ) {
			throw new Exception('Invalid argument $profile_id');
		}
		
		$result = SK_MySQL::query(
			'SELECT `friend_id` FROM `'.TBL_PROFILE_FRIEND_LIST.'`
				WHERE `profile_id`='.$profile_id.' AND `status`="active"'
		);
		
		$id_index = array();
		
		while ( $friend_id = $result->fetch_cell() ) {
			$id_index[$friend_id] = $friend_id;
		}
		
		return $id_index;
	}
	
	
	
}
