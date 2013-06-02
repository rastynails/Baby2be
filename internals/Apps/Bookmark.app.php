<?php

/**
 * Class for working with block, hot lists
 *
 * @package SkaDate
 * @subpackage SkaDate5
 * @link http://www.skadate.com
 * @version 5.0
 */
class app_Bookmark
{
    private static $blockStatusCache = array();

	/**
	 * Checks if second profile is in the block list of the first profile.
	 *
	 * @param integer $profile_id
	 * @param integer $block_profile_id
	 * @return boolean
	 */
	public static function isProfileBlocked( $profile_id, $block_profile_id )
	{
		if ( !($profile_id = intval($profile_id)) || !($block_profile_id = intval( $block_profile_id )) )
			return false;

        $key = self::getKey($profile_id, $block_profile_id);
        
        if( isset(self::$blockStatusCache[$key]) )
        {
            return self::$blockStatusCache[$key];
        }

		$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `".TBL_PROFILE_BLOCK_LIST."`
			WHERE `profile_id`=? AND `blocked_id`=?", $profile_id, $block_profile_id );

		self::$blockStatusCache[$key] = SK_MySQL::query($query)->fetch_cell() ? true : false;

        return self::$blockStatusCache[$key];
	}

    public static function initUserBlockCacheForValues( $profile_id, array $blockProfileIdList )
	{
		if ( !($profile_id = intval($profile_id)) || empty($blockProfileIdList) )
			return;

		$query = SK_MySQL::placeholder( "SELECT `profile_id`, `blocked_id` FROM `".TBL_PROFILE_BLOCK_LIST."`
			WHERE `profile_id`=? AND `blocked_id` IN (?@)", $profile_id, $blockProfileIdList );

        $result = SK_MySQL::queryForList($query);

        $resultArray = array();

        foreach ( $result as $item )
        {
            $resultArray[] = self::getKey($item['profile_id'], $item['blocked_id']);
        }

        foreach ( $blockProfileIdList as $id )
        {
            $key = self::getKey($profile_id, $id);
            self::$blockStatusCache[$key] = in_array($key, $resultArray) ? true : false;
        }

        $query = SK_MySQL::placeholder( "SELECT `profile_id`, `blocked_id` FROM `".TBL_PROFILE_BLOCK_LIST."`
			WHERE `profile_id` IN (?@) AND `blocked_id`=?", $blockProfileIdList, $profile_id );

        $result = SK_MySQL::queryForList($query);

        $resultArray = array();

        foreach ( $result as $item )
        {
            $resultArray[] = self::getKey($item['profile_id'], $item['blocked_id']);
        }

        foreach ( $blockProfileIdList as $id )
        {
            $key = self::getKey($id, $profile_id);
            self::$blockStatusCache[$key] = in_array($key, $resultArray) ? true : false;
        }
	}

	private static function getKey( $profileId, $blockId )
    {
        return $profileId.'_'.$blockId;
    }


    /**
	 * Adds profile into block list.
	 * Returns boolean - result of the query OR error code : -1 - incorrect parametrs.
	 *
	 * @param integer $member_id
	 * @param integer $block_profile_id
	 * @return boolean|integer
	 */
	public static function BlockProfile( $profile_id, $block_profile_id )
	{
		if ( !($profile_id = intval($profile_id)) || !($block_profile_id = intval( $block_profile_id )) )
			return false;
		
		// check if profile already exists in the member's block list:
		$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `".TBL_PROFILE_BLOCK_LIST."`
			WHERE `profile_id`=? AND `blocked_id`=?", $profile_id, $block_profile_id );
	
		if ( SK_MySQL::query($query)->fetch_cell() )
			return true;
		else
		{ 
			$affected_rows = 0;
			SK_MySQL::query( SK_MySQL::placeholder( "INSERT INTO `".TBL_PROFILE_BLOCK_LIST."` (`profile_id`, `blocked_id`) 
							VALUES( ?@ )", array( $profile_id, $block_profile_id ) ) ) &&
			
			$affected_rows += SK_MySQL::affected_rows();
			
			
			SK_MySQL::query( SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_BOOKMARK_LIST."` WHERE 
							`profile_id`=? AND `bookmarked_id`=?", $profile_id, $block_profile_id ) );
			
			$affected_rows += SK_MySQL::affected_rows();
			
						
			SK_MySQL::query( SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_BOOKMARK_LIST."` WHERE 
							`profile_id`=? AND `bookmarked_id`=?", $block_profile_id, $profile_id ) );
			
			$affected_rows += SK_MySQL::affected_rows();
			
						
			SK_MySQL::query( SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_FRIEND_LIST."` WHERE 
							`profile_id`=? AND `friend_id`=?", $profile_id, $block_profile_id ) );
						
			$affected_rows += SK_MySQL::affected_rows();
			
			
			SK_MySQL::query( SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_FRIEND_LIST."` WHERE 
							`profile_id`=? AND `friend_id`=?", $block_profile_id, $profile_id ) );
			
			$affected_rows += SK_MySQL::affected_rows();
			
			return (bool) $affected_rows;
		}
	}
	
	
	/**
	 * Deletes profile from block list.
	 * Returns boolean - result of the query OR error code : -1 - incorrect parametrs.
	 *
	 * @param integer $profile_id
	 * @param integer $unblock_profile_id
	 * @return boolean|integer
	 */
	public static function UnblockProfile( $profile_id, $unblock_proifle_id )
	{
		if ( !($profile_id = intval($profile_id)) || !($unblock_proifle_id = intval( $unblock_proifle_id )) )
			return false;
	
		SK_MySQL::query( SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_BLOCK_LIST."` 
			WHERE `profile_id`=? AND `blocked_id`=?", $profile_id, $unblock_proifle_id ) );
		return (bool) SK_MySQL::affected_rows();
	}
	

	/**
	 * Returns member's blocked profiles with their info.
	 *
	 * @param integer $profile_id
	 * @param integer $num_on_page
	 * @return array|integer
	 */
	public static function BlockList($profile_id)
	{
		$page = app_ProfileList::getPage();
		
		// get numbers on page:
		$result_per_page = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
		
		$query_parts['limit'] =SK_MySQL::placeholder("LIMIT ?, ?", $result_per_page*($page-1), $result_per_page );
					
		$query_parts['projection'] = "`p`.*,`b`.*,`pe`.*,`online`.`hash` AS `online`";
		
		$query_parts['left_join'] = "
				INNER JOIN `".TBL_PROFILE."` AS `p` ON( `b`.`blocked_id`=`p`.`profile_id` )
				INNER JOIN `".TBL_PROFILE_EXTEND."` AS `pe` ON( `b`.`blocked_id`=`pe`.`profile_id` )
				LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `online` ON( `b`.`blocked_id`=`online`.`profile_id` )
			";
		
		$query_parts['condition'] = SK_MySQL::placeholder(
				"`b`.`profile_id`=? AND ".app_Profile::SqlActiveString( 'p' )
				, $profile_id);

		$query_parts['order'] = "";
		
		foreach ( explode("|",SK_Config::Section('site')->Section('additional')->Section('profile_list')->order) as $val)
		{
			if(in_array($val, array('','none')) )
				continue;
			
			app_ProfileList::_configureOrder($query_parts, $val, "p");				
		}
		
		$query = "SELECT {$query_parts['projection']} FROM `".TBL_PROFILE_BLOCK_LIST."` AS `b`
			{$query_parts['left_join']}
			WHERE {$query_parts['condition']} ".
			( isset($query_parts['group']) && (strlen($query_parts['group']))?" GROUP BY {$query_parts['group']}":"" ).
			( (strlen($query_parts['order']))?" ORDER BY {$query_parts['order']}":"" ).			
			" {$query_parts['limit']}";
		
		$query_result = SK_MySQL::query($query);
		
		while ($item = $query_result->fetch_assoc())
			$result['profiles'][] = $item;
			
		
		$result['total'] = self::countBlockList($profile_id);

		return $result;
	}

	
	/**
	 * Returns number of the blocked members in the profile block list.
	 *
	 * @param integer $profile_id
	 * @return integer|boolean
	 */
	public static function countBlockList( $profile_id )
	{
		$profile_id = intval( $profile_id );
		if ( !$profile_id )
			return false;

                $config = SK_Config::section( 'site.additional.profile_list' );

                $sex_condition = '';

                if ( $config->display_only_looking_for )
                {
                    $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');

                    $sex_condition = !empty( $match_sex ) ? " AND `p`.`sex` & " . $match_sex . " " : '';
                }

                $gender_exclusion = '';

                if ( $config->gender_exclusion )
                {
                    $gender_exclusion = ' AND `p`.`sex` != ' . app_Profile::getFieldValues( $profile_id, 'sex' );
                }

		return SK_MySQL::query( SK_MySQL::placeholder( "SELECT COUNT(`b`.`blocked_id`) 
			FROM `".TBL_PROFILE_BLOCK_LIST."` AS `b`
			INNER JOIN `".TBL_PROFILE."` AS `p` ON( `b`.`blocked_id`=`p`.`profile_id` )
			WHERE `b`.`profile_id`=? AND ".app_Profile::SqlActiveString( 'p' ) . $sex_condition . $gender_exclusion, $profile_id ) )->fetch_cell();
	}
	

	/**
	 * Checks if second profile is in the bookmark list of the first profile.
	 *
	 * @param integer $profile_id
	 * @param integer $bookmark_profile_id
	 * @return boolean
	 */
	public static function isProfileBookmarked( $profile_id, $bookmark_profile_id )
	{
		if ( !($profile_id = intval($profile_id)) || !($bookmark_profile_id = intval( $bookmark_profile_id )) )
			return false;

        if( isset(self::$profileBookmarkCache[$profile_id.'_'.$bookmark_profile_id]) )
        {
            return self::$profileBookmarkCache[$profile_id.'_'.$bookmark_profile_id];
        }

		$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `".TBL_PROFILE_BOOKMARK_LIST."` 
			WHERE `profile_id`=? AND `bookmarked_id`=?", $profile_id, $bookmark_profile_id );
	
		return ( SK_MySQL::query($query )->fetch_cell() )? true : false;
	}

    private static $profileBookmarkCache = array();

    public static function initProfileBookmarkedCache( $profile_id, array $bookmark_profile_id_list )
	{
		if ( !($profile_id = intval($profile_id)) || empty($bookmark_profile_id_list) )
			return false;

        foreach ( $bookmark_profile_id_list as $id )
        {
            self::$profileBookmarkCache[$profile_id.'_'.$id] = false;
        }

		$query = SK_MySQL::placeholder( "SELECT `profile_id`, `bookmarked_id` FROM `".TBL_PROFILE_BOOKMARK_LIST."`
			WHERE `profile_id`=? AND `bookmarked_id` IN (?@)", $profile_id, $bookmark_profile_id_list );

		$result = SK_MySQL::queryForList($query);

        foreach ( $result as $item )
        {
            self::$profileBookmarkCache[$item['profile_id'].'_'.$item['bookmarked_id']] = true;
        }
	}

	
	/**
	 * Adds profile into bookmark list.
	 * Returns boolean - result of the query OR error code : -1 - incorrect parametrs.
	 *
	 * @param integer $profile_id
	 * @param integer $bookmark_profile_id
	 * @return boolean|integer
	 */
	public static function BookmarkProfile( $profile_id, $bookmark_profile_id )
	{
		if ( !($profile_id = intval($profile_id)) || !($bookmark_profile_id = intval( $bookmark_profile_id )) )
			return false;
		
		// check if profile already exists in the member's hotlist:
		$_query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `".TBL_PROFILE_BOOKMARK_LIST."` WHERE 
			`profile_id`=? AND `bookmarked_id`=?", $profile_id, $bookmark_profile_id );
	
		return ( SK_MySQL::query( $_query )->fetch_cell() )
				? true 
				: (bool)( SK_MySQL::query(SK_MySQL::placeholder( 
						"INSERT INTO `".TBL_PROFILE_BOOKMARK_LIST."` (`profile_id`, `bookmarked_id`) VALUES( ?@ )"
						, array( $profile_id, $bookmark_profile_id ) ) ) );
	}
	
	
	/**
	 * Deletes profile from bookmark list.
	 * Returns boolean - result of the query OR error code : -1 - incorrect parametrs.
	 *
	 * @param integer $profile_id
	 * @param integer $unbookmark_profile_id
	 * @return boolean|integer
	 */
	public static function UnbookmarkProfile( $profile_id, $unbookmark_profile_id )
	{
		if ( !($profile_id = intval($profile_id)) || !($unbookmark_profile_id = intval( $unbookmark_profile_id )) )
			return false;
	
		SK_MySQL::query( SK_MySQL::placeholder(
						 "DELETE FROM `".TBL_PROFILE_BOOKMARK_LIST."`
							WHERE `profile_id`=? AND `bookmarked_id`=?", $profile_id, $unbookmark_profile_id ) );
		return (bool)SK_MySQL::affected_rows();
	}
	
	
	/**
	 * Returns member's bookmarked profiles with their info.
	 *
	 * @param integer $profile_id
	 * @return array|integer
	 */
	public static function HotList( $profile_id )
	{
		$page = app_ProfileList::getPage();
		
		// get numbers on page:

                $config = SK_Config::section( 'site.additional.profile_list' );

                $result_per_page = $config->result_per_page;
		
		$query_parts['limit'] =SK_MySQL::placeholder("LIMIT ?, ?", $result_per_page*($page-1), $result_per_page );
					
		$query_parts['projection'] = "`h`.*,`p`.*,`pe`.*,`online`.`hash` AS `online`";
		
		$query_parts['left_join'] = "
				INNER JOIN `".TBL_PROFILE."` AS `p` ON( `h`.`bookmarked_id`=`p`.`profile_id` )
				INNER JOIN `".TBL_PROFILE_EXTEND."` AS `pe` ON( `h`.`bookmarked_id`=`pe`.`profile_id`)
				LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `online` ON( `h`.`bookmarked_id`=`online`.`profile_id` )
			";
		
		$query_parts['condition'] = SK_MySQL::placeholder(
				"`h`.`profile_id`=? AND ".app_Profile::SqlActiveString( 'p' )
				, $profile_id);

		$query_parts['order'] = "";
		
		foreach ( explode("|", $config->order ) as $val)
		{
			if(in_array($val, array('','none')) )
				continue;
			
			app_ProfileList::_configureOrder($query_parts, $val, "p");				
		}

                $sex_condition = "";

                if ( $config->display_only_looking_for )
                {
                    $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');

                    if ( !empty($match_sex) )
                    {
                        $sex_condition = " AND `p`.`sex` & " . $match_sex . " ";
                    }
                }
                
                $gender_exclusion = '';

                if ( $config->gender_exclusion )
                {
                    $gender_exclusion = ' AND `p`.`sex` != ' . app_Profile::getFieldValues( $profile_id, 'sex' );
                }

		$query = "SELECT {$query_parts['projection']} FROM `".TBL_PROFILE_BOOKMARK_LIST."` AS `h`
			{$query_parts['left_join']}
			WHERE {$query_parts['condition']} $sex_condition $gender_exclusion ".
			( isset($query_parts['group']) && (strlen($query_parts['group']))?" GROUP BY {$query_parts['group']}":"" ).
			( (strlen($query_parts['order']))?" ORDER BY {$query_parts['order']}":"" ).			
			" {$query_parts['limit']}";
		
		$query_result = SK_MySQL::query($query);
		
		while ($item = $query_result->fetch_assoc())
			$result['profiles'][] = $item;
			
		
		$result['total'] = self::countHotList($profile_id);

		return $result;
	}
	
	
	/**
	 * Returns number of the bookmarked members in the profile hotlist.
	 *
	 * @param integer $profile_id
	 * @return integer|boolean
	 */
	public static function countHotList( $profile_id )
	{
		$profile_id = intval( $profile_id );
		if ( !$profile_id )
			return false;

                $config = SK_Config::section( 'site.additional.profile_list' );

                $sex_condition = '';

                if ( $config->display_only_looking_for )
                {
                    $match_sex = app_Profile::getFieldValues(SK_HttpUser::profile_id(), 'match_sex');

                    $sex_condition = !empty( $match_sex ) ? " AND `p`.`sex` & " . $match_sex . " " : '';
                }

                $gender_exclusion = '';
                
                if ( $config->gender_exclusion )
                {
                    $gender_exclusion = ' AND `p`.`sex` != ' . app_Profile::getFieldValues( $profile_id, 'sex' );
                }

		$query = SK_MySQL::placeholder(	"SELECT COUNT(`h`.`bookmarked_id`) 
								FROM `".TBL_PROFILE_BOOKMARK_LIST."` AS `h`
								INNER JOIN `".TBL_PROFILE."` AS `p` ON( `h`.`bookmarked_id`=`p`.`profile_id` )
								WHERE `h`.`profile_id`=? AND ".app_Profile::SqlActiveString( 'p' ) . $sex_condition . $gender_exclusion, $profile_id );
		
		return SK_MySQL::query($query)->fetch_cell();
	}


	
}