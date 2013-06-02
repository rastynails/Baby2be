<?php

class SKM_Profile {
	
	
	public static function Logoff( $profile_id )
	{
		if ( !( $profile_id = (int)$profile_id ) )
			return false;
		
		// delete profile from online table
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_ONLINE."` 
		  WHERE `profile_id`=? AND `agent` = 'mobile'", $profile_id );
		SK_MySQL::query( $query );
			
		$_tmp_list_id = app_TempProfileList::getListSessionInfo( 'search', 'list_id');
		
		if ( $_tmp_list_id )
		{
			// delete search result list id from tmp table
			$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_TMP_PR_LIST."` 
				WHERE `profile_list_id`=?", $_tmp_list_id );
		
			SK_MySQL::query($query);
			
			// delete all profile in search result list id
			$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_LINK_PR_LIST_PR."` 
				WHERE `profile_list_id`=?", $_tmp_list_id );
			
			SK_MySQL::query($query);
		}
		
		// destroy login info in session
		SKM_User::session_end();
		return true;
	}
	
	public static function get_thumb_path($profile_id, $active = true) 
	{
		$query_inc = (!$active || $profile_id == SKM_User::profile_id())? '1' : "`status`='active'";
		$query = SK_MySQL::placeholder("SELECT `photo_id` FROM `" . TBL_PROFILE_PHOTO . "` 
			WHERE `profile_id`=? 
			AND `number`=0 
			AND $query_inc", $profile_id);

		$photo_id = SK_MySQL::query($query)->fetch_cell();

		if ($photo_id) {
			if (!($photo_info = self::get_photo($photo_id))) {
				return null;
			}
			return URL_USERFILES.'thumb_' . $photo_info->profile_id . '_' . $photo_id . '_' . $photo_info->index . '.jpg';
			
		} else {
			$sex = app_Profile::getFieldValues($profile_id, 'sex');
			return url::base() . 'application/media/img/sex_'.$sex.'_no_photo.jpg'; 
		}
	}
	
	
	public static function get_photo($photo_id) 
	{
		static $photos_info = array();
		
		if (!($photo_id = intval($photo_id))) {
			return null;
		}
		
		if (!isset($photos_info[$photo_id])) {
			$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_PROFILE_PHOTO."` WHERE `photo_id` = ?", $photo_id);
			$photos_info[$photo_id] = SK_MySQL::query($query)->fetch_object();
		}
		
		return $photos_info[$photo_id];
	}
}
