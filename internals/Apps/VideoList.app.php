<?php

/**
 * Class for requesting different video lists (latest, top, most duscussed)
 *
 */
class app_VideoList
{
	/**
	 * Returns list of latest video
	 *
	 * @param int $page_number
	 * @param int $page_limit
	 * @return array
	 */
	public static function getVideoList( $list_type, $page_number, $page_limit = null, $cat_id = null )
	{
		$page_number = intval($page_number);
		$page_limit = intval($page_limit);

		switch ($list_type)
		{
			case 'latest':
			case 'categories':
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

		$category_cond = ($list_type == 'categories') && $cat_id ? SK_MySQL::placeholder(" AND `video`.`category_id` = ?", $cat_id) : '';

		$page_limit = $page_limit ? $page_limit : SK_Config::Section('video')->Section('other_settings')->display_media_list_limit;
		$page_number = $page_number ? $page_number : 1;

		$query = SK_MySQL::placeholder( "SELECT `video`.*, `video`.`profile_id`, `video`.`category_id`,
            
			(COUNT(c.`id`)) AS `comment_count`,
			(COUNT(v.`video_id`)) AS `view_count`,
			(AVG(r.`score`)) AS `rate_score`,
			(COUNT(r.`id`)) AS `rate_count`
            
			FROM `".TBL_PROFILE_VIDEO."` AS `video` 
			INNER JOIN `".TBL_PROFILE."` AS `profile` USING ( `profile_id` )
            LEFT JOIN `".TBL_VIDEO_COMMENT."` AS `c` ON ( c.`entity_id`=`video`.`video_id` )
            LEFT JOIN `".TBL_PROFILE_VIDEO_VIEW."` AS `v` ON ( v.`video_id`=`video`.`video_id` )
            LEFT JOIN `".TBL_VIDEO_RATE."` AS `r` ON ( r.`entity_id`=`video`.`video_id` )

			WHERE ".self::sqlActiveCond( 'video' )."  AND ".app_Profile::SqlActiveString( 'profile' ).$category_cond.
			"

            GROUP BY  `video`.`video_id`
            ORDER BY `$order_by` DESC
           
			LIMIT ?, ?", ($page_number - 1) * $page_limit, $page_limit );

		$res = SK_MySQL::query($query);

		while($video = $res->fetch_assoc())
		{
			$video['video_page'] = app_ProfileVideo::getVideoViewURL($video['hash']);
			$video['thumb_img'] = app_ProfileVideo::getVideoThumbnail( $video );
            $video['username'] = app_Profile::username($video['profile_id']);
			$list[] = $video;
		}

		$return['list'] = $list;

		$query = SK_MySQL::placeholder( "SELECT COUNT( `video_id` ) FROM `".TBL_PROFILE_VIDEO."` AS `video`
			INNER JOIN `".TBL_PROFILE."` AS `profile` USING (`profile_id`)
            LEFT JOIN `".TBL_VIDEO_CATEGORY."` AS `c` ON (`video`.`category_id`=`c`.`category_id`)
			WHERE ".self::sqlActiveCond('video')." AND ".app_Profile::SqlActiveString( 'profile' ).$category_cond." " );

		$return['total'] = SK_MySQL::query($query)->fetch_cell();
        
		$return['page'] = $page_number;

		return $return;
	}

	public static function getTaggedVideo( $tag, $page_number )
	{
		$tag = trim($tag);
		$page_number = intval($page_number);

		$page_limit = SK_Config::Section('video')->Section('other_settings')->display_media_list_limit;
		$page_number = $page_number ? $page_number : 1;

		$video_arr = app_TagService::stFindEntityIdsForTag('video', $tag);
		if (!count($video_arr))
			return array();

		$ids = implode(", ", $video_arr);

		$query = SK_MySQL::placeholder( "SELECT `video`.*, `profile`.`profile_id`, `profile`.`username`,
			(COUNT(c.`id`)) AS `comment_count`,
			(COUNT(v.`video_id`)) AS `view_count`
						FROM `".TBL_PROFILE_VIDEO."` AS `video`
			INNER JOIN `".TBL_PROFILE."` AS `profile` USING ( `profile_id` )
            LEFT JOIN `".TBL_VIDEO_COMMENT."` AS `c` ON ( c.`entity_id`=`video`.`video_id` )
            LEFT JOIN `".TBL_PROFILE_VIDEO_VIEW."` AS `v` ON ( v.`video_id`=`video`.`video_id` )
                
			WHERE ".self::sqlActiveCond( 'video' )."  AND ".app_Profile::SqlActiveString( 'profile' ).
			" AND `video`.`video_id` IN (".$ids.") ORDER BY `upload_stamp` DESC
			LIMIT ?, ?", ($page_number - 1) * $page_limit, $page_limit);

		$res = SK_MySQL::query($query);

		while($video = $res->fetch_assoc())
		{
			$video['video_page'] = app_ProfileVideo::getVideoViewURL($video['hash']);
			$video['thumb_img'] = app_ProfileVideo::getVideoThumbnail( $video );
            $video['username'] = $video['username'];

			$list[] = $video;
		}

		$return['list'] = $list;

		$query = SK_MySQL::placeholder( "SELECT COUNT(`video`.`video_id`)
            FROM `".TBL_PROFILE_VIDEO."` AS `video`
            LEFT JOIN `".TBL_PROFILE."` AS `profile` USING ( `profile_id` )
            WHERE ".self::sqlActiveCond( 'video' )."  AND ".app_Profile::SqlActiveString( 'profile' ).
            " AND `video`.`video_id` IN (".$ids.")" );
        
		$return['total'] = SK_MySQL::query($query)->fetch_cell();

		$return['page'] = $page_number;

		return $return;
	}


	/**
	 * Returns video for index page depending on passed parameter 'list_type'
	 *
	 * @param string $list_type
	 * @return array
	 */
	public static function getIndexVideo( $list_type = 'latest', $width = null, $height = null )
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

		$query = "SELECT `video`.*,
			(COUNT(c.`id`)) AS `comment_count`,
			(COUNT(v.`video_id`)) AS `view_count`,
			(AVG(r.`score`)) AS `rate_score`,
			(COUNT(r.`id`)) AS `rate_count`
            
			FROM `".TBL_PROFILE_VIDEO."` AS `video`
			INNER JOIN `".TBL_PROFILE."` AS `profile` USING ( `profile_id` )
            LEFT JOIN `".TBL_VIDEO_COMMENT."` AS `c` ON ( c.`entity_id`=`video`.`video_id` )
            LEFT JOIN `".TBL_PROFILE_VIDEO_VIEW."` AS `v` ON ( v.`video_id`=`video`.`video_id` )
            LEFT JOIN `".TBL_VIDEO_RATE."` AS `r` ON ( r.`entity_id`=`video`.`video_id` )

            WHERE `video`.`status`='active' AND `video`.`is_converted` = 'yes'
            AND `video`.`privacy_status` = 'public' AND ".app_Profile::SqlActiveString( 'profile' )."
            ORDER BY `$order_by` DESC LIMIT 1";

        $query_for_thumbs = "SELECT `video`.*,
			(COUNT(c.`id`)) AS `comment_count`,
			(COUNT(v.`video_id`)) AS `view_count`,
			(AVG(r.`score`)) AS `rate_score`,
			(COUNT(r.`id`)) AS `rate_count`

			FROM `".TBL_PROFILE_VIDEO."` AS `video`
			INNER JOIN `".TBL_PROFILE."` AS `profile` USING ( `profile_id` )
            LEFT JOIN `".TBL_VIDEO_COMMENT."` AS `c` ON ( c.`entity_id`=`video`.`video_id` )
            LEFT JOIN `".TBL_PROFILE_VIDEO_VIEW."` AS `v` ON ( v.`video_id`=`video`.`video_id` )
            LEFT JOIN `".TBL_VIDEO_RATE."` AS `r` ON ( r.`entity_id`=`video`.`video_id` )

            WHERE `video`.`status`='active' AND `video`.`is_converted` = 'yes'
            AND `video`.`privacy_status` = 'public' AND ".app_Profile::SqlActiveString( 'profile' )."
            ORDER BY `$order_by` DESC LIMIT 1, 3";

		$item = SK_MySQL::query($query)->fetch_assoc();
		$item['video_page'] = app_ProfileVideo::getVideoViewURL($item['hash']);
		if ( $item['video_source'] == 'file' )
		{
			$item['video_url'] =  app_ProfileVideo::getVideoURL($item['hash'], $item['extension']);
		}
		else
		{
			$item['code'] = app_ProfileVideo::formatEmbedCode(
                $item['code'],
                $width ?  $width : SK_Config::section('video')->get('small_video_width'),
                $height ? $height : SK_Config::section('video')->get('small_video_height')
            );
		}
		$items = SK_MySQL::query($query_for_thumbs);
		while($video = $items->fetch_assoc())
		{
			if($video['video_source'] == 'file')
				$video['video_url'] =  app_ProfileVideo::getVideoURL($video['hash'], $video['extension']);

			$video['thumb_img'] = app_ProfileVideo::getVideoThumbnail($video);
			$video['video_page'] = app_ProfileVideo::getVideoViewURL($video['hash']);
			$list[] = $video;
		}

		$ret['for_player'] = $item;
		$ret['for_thumbs'] = $list;

		return $ret;
	}

	public static function sqlActiveCond( $alias_name = null )
	{
		$_alias = ( strlen( trim( $alias_name ) ) ) ? "`$alias_name`." : '';

		$_return_str = "( $_alias`status`='active' AND $_alias`is_converted`='yes' )";

		return $_return_str;
	}

	public static function getVideoCategories( $id_only = false )
	{
        $query = "SELECT * FROM `".TBL_VIDEO_CATEGORY."` WHERE 1 ORDER BY `category_id` ASC";

        $catArr = $id_only ? array(0) : array();

        $res = SK_MySQL::query($query);

        while ( $row = $res->fetch_assoc() )
        {
            $label = SK_Language::text('%video_categories.cat_'.$row['category_id']);
            if ( $id_only )
            {
                $catArr[] = $row['category_id'];
            }
            else
            {
                $catArr[] = array('id' => $row['category_id'], 'label' => $label);
            }
        }

        return $catArr;
	}


    public static function getVideoCategoriesWithCount( )
    {
        $query = "SELECT `c`.*,
            count(`v`.`video_id`) AS `videoCount`
            FROM `".TBL_VIDEO_CATEGORY."` AS `c`
            LEFT JOIN `".TBL_PROFILE_VIDEO."` AS `v` ON (`v`.`category_id` = `c`.`category_id`)
            LEFT JOIN `".TBL_PROFILE."` AS `profile` ON (`v`.`profile_id`=`profile`.`profile_id`)

            WHERE ".app_VideoList::sqlActiveCond( 'v' )."  AND ".app_Profile::SqlActiveString( 'profile' )
            ."
            GROUP BY `c`.`category_id`
            ORDER BY `c`.`category_id` ASC";

        $catArr = array();

        $res = SK_MySQL::query($query);

        while ( $row = $res->fetch_assoc() )
        {
            $row['label'] = SK_Language::text('%video_categories.cat_'.$row['category_id']);
            $catArr[$row['category_id']] = $row;
        }

        usort($catArr, array('app_VideoList', 'sortCategoryByAsc'));

        return $catArr;
    }

    public static function sortCategoryByAsc( $el1, $el2 )
    {
        return strcmp($el1['label'], $el2['label']);
    }

	public static function addVideoCategory( $labels )
	{
	    if ( !$labels )
	    {
	        return false;
	    }

	    SK_MySQL::query("INSERT INTO `".TBL_VIDEO_CATEGORY."` (`active`) VALUES(1)");
	    $cat_id = SK_MySQL::insert_id();

	    if ( $cat_id )
	    {
            SK_LanguageEdit::setKey('video_categories', 'cat_'.$cat_id, $labels);

            return true;
	    }

	    return false;
	}

	public static function deleteVideoCategory( $cat_id )
	{
	    if ( !$cat_id )
	    {
	        return false;
	    }

	    $query = SK_MySQL::placeholder("DELETE FROM `".TBL_VIDEO_CATEGORY."` WHERE `category_id`=?", $cat_id);
	    SK_MySQL::query($query);

	    if ( SK_MySQL::affected_rows() )
	    {
	        SK_LanguageEdit::deleteKey('video_categories', 'cat_'.$cat_id);
	        return true;
	    }

	    return false;
	}

	public static function getVideoCategoryById( $cat_id )
	{
        if ( !$cat_id )
        {
            return false;
        }

        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_VIDEO_CATEGORY."` WHERE `category_id`=?", $cat_id);

        return SK_MySQL::query($query)->fetch_assoc();
	}
}

?>