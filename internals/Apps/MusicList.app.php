<?php

/**
 * Class for requesting different music lists (latest, top, most duscussed)
 *
 */
class app_MusicList
{
	/**
	 * Returns list of latest music
	 *
	 * @param int $page_number
	 * @param int $page_limit
	 * @return array
	 */
	public static function getMusicList( $list_type, $page_number, $page_limit = null )
	{
		$page_number = intval($page_number);
		$page_limit = intval($page_limit);

		switch ($list_type)
		{
			case 'latest':
				$order_by = 'upload_stamp';
				break;

			case 'toprated':
				$order_by = 'rate_score';
				break;

			case 'discussed':
				$order_by = 'comment_count';
				break;

			default:
				$order_by = 'upload_stamp';
		}

		$page_limit = $page_limit ? $page_limit : SK_Config::Section('music')->display_music_list_limit;

		$page_number = $page_number ? $page_number : 1;

		$query = SK_MySQL::placeholder( "SELECT `music`.*, `profile`.`profile_id`,
			(SELECT COUNT(`id`) FROM `".TBL_MUSIC_COMMENT."` WHERE `entity_id`=`music`.`music_id`) AS `comment_count`,
			(SELECT COUNT(`music_id`) FROM `".TBL_PROFILE_MUSIC_VIEW."` WHERE `music_id`=`music`.`music_id`) AS `view_count`,
			(SELECT AVG(`score`) FROM `".TBL_MUSIC_RATE."` WHERE `entity_id`=`music`.`music_id`) AS `rate_score`,
			(SELECT COUNT(`id`) FROM `".TBL_MUSIC_RATE."` WHERE `entity_id`=`music`.`music_id`) AS `rate_count`
			FROM `".TBL_PROFILE_MUSIC."` AS `music`
			LEFT JOIN `".TBL_PROFILE."` AS `profile` USING ( `profile_id` )
			WHERE ".self::sqlActiveCond( 'music' )."  AND ".app_Profile::SqlActiveString( 'profile' ).
			" ORDER BY `$order_by` DESC
			LIMIT ?, ?", ($page_number - 1) * $page_limit, $page_limit );

		$res = SK_MySQL::query($query);

		while($music = $res->fetch_assoc())
		{
			$music['music_page'] = app_ProfileMusic::getMusicViewURL($music['hash']);
			$music['thumb_img'] = app_ProfileMusic::getMusicThumbnail( $music );
                        $music['username'] = app_Profile::username($music['profile_id']);
			$list[] = $music;
		}

		$return['list'] = $list;

		$query = SK_MySQL::placeholder( "SELECT COUNT( `music_id` ) FROM `".TBL_PROFILE_MUSIC."` AS `music`
			LEFT JOIN `".TBL_PROFILE."` AS `profile` USING (`profile_id`)
			WHERE ".self::sqlActiveCond('music')." AND ".app_Profile::SqlActiveString( 'profile' )."
			ORDER BY `upload_stamp` DESC" );

		$return['total'] = SK_MySQL::query($query)->fetch_cell();

		$return['page'] = $page_number;

		return $return;
	}

	/**
	 * Returns music for index page depending on passed parameter 'list_type'
	 *
	 * @param string $list_type
	 * @return array
	 */
	public static function getIndexMusic( $list_type = 'latest' )
	{
		$viewer = SK_HttpUser::profile_id();

		switch($list_type)
		{
			case 'latest':
				$order_by = 'upload_stamp';
				break;

			case 'toprated':
				$order_by = 'rate_score';
				break;

			case 'discussed':
				$order_by = 'comment_count';
				break;
		}

		$query = "SELECT `music`.*,
            (SELECT COUNT(`music_id`) FROM `".TBL_PROFILE_MUSIC_VIEW."` WHERE `music_id`=`music`.`music_id`) AS `view_count`,
            (SELECT COUNT(`id`) FROM `".TBL_MUSIC_COMMENT."` WHERE `entity_id`=`music`.`music_id`) AS `comment_count`,
            (SELECT ROUND(AVG(`score`),2) FROM `".TBL_MUSIC_RATE."` WHERE `entity_id`=`music`.`music_id`) AS `rate_score`
            FROM `".TBL_PROFILE_MUSIC."` AS `music`
            LEFT JOIN `".TBL_PROFILE."` AS `profile` ON ( `music`.`profile_id`=`profile`.`profile_id` )
            WHERE `music`.`status`='active'
            AND `music`.`privacy_status` = 'public' AND ".app_Profile::SqlActiveString( 'profile' )."
            ORDER BY `$order_by` DESC LIMIT 1";

        $query_for_thumbs = "SELECT `music`.*,
            (SELECT COUNT(`music_id`) FROM `".TBL_PROFILE_MUSIC_VIEW."` WHERE `music_id`=`music`.`music_id`) AS `view_count`,
            (SELECT COUNT(`id`) FROM `".TBL_MUSIC_COMMENT."` WHERE `entity_id`=`music`.`music_id`) AS `comment_count`,
            (SELECT ROUND(AVG(`score`),2) FROM `".TBL_MUSIC_RATE."` WHERE `entity_id`=`music`.`music_id`) AS `rate_score`
            FROM `".TBL_PROFILE_MUSIC."` AS `music`
            LEFT JOIN `".TBL_PROFILE."` AS `profile` ON ( `music`.`profile_id`=`profile`.`profile_id` )
            WHERE `music`.`status`='active'
            AND `music`.`privacy_status` = 'public' AND ".app_Profile::SqlActiveString( 'profile' )."
            ORDER BY `$order_by` DESC LIMIT 1, 3";

		$item = SK_MySQL::query($query)->fetch_assoc();
		$item['music_page'] = app_ProfileMusic::getMusicViewURL($item['hash']);
		if($item['music_source'] == 'file')
			$item['music_url'] =  app_ProfileMusic::getMusicURL($item['hash'], $item['extension']);
		else
			$item['code'] = app_ProfileMusic::formatEmbedCode($item['code'], '100%', 20);

		$items = SK_MySQL::query($query_for_thumbs);
		while($music = $items->fetch_assoc())
		{
			if($music['music_source'] == 'file')
				$music['music_url'] =  app_ProfileMusic::getMusicURL($music['hash'], $music['extension']);

			$music['thumb_img'] = app_ProfileMusic::getMusicThumbnail($music);
			$music['music_page'] = app_ProfileMusic::getMusicViewURL($music['hash']);
			$list[] = $music;
		}

		$ret['for_player'] = $item;
		$ret['for_thumbs'] = $list;

		return $ret;
	}

	public static function sqlActiveCond( $alias_name = null )
	{
		$_alias = ( strlen( trim( $alias_name ) ) ) ? "`$alias_name`." : '';

		$_return_str = "( $_alias`status`='active' )";

		return $_return_str;
	}

}

?>