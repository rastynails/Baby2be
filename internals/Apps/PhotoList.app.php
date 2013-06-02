<?php

class app_PhotoList
{
	private static function page()
	{
		return ( isset( SK_HttpRequest::$GET['page'] ) && intval( SK_HttpRequest::$GET['page'] ) ) ? SK_HttpRequest::$GET['page'] : 1;
	}
	
	public static function activeSqlStr($alias = "", $pp_and_fo = true) {
		$alias = strlen($alias) ? "`" . $alias . "`." : "";
		$add_where = !$pp_and_fo ? " AND $alias`publishing_status`='public'" : '';
		return "$alias`number`!=0 AND $alias`status`='active'$add_where";
	}
	
	public static function LatestPhotos($pp_and_fo = true)
	{
		$page = self::page();
		$result_per_page = SK_Config::section("photo")->Section("general")->per_page;
		
		$limit = $result_per_page  *( $page-1 ).", ".$result_per_page;
		
		$join .= "LEFT JOIN `" . TBL_PROFILE . "` AS `profile` ON `profile`.`profile_id` = `photo`.`profile_id` ";
		
		$join .= "LEFT JOIN `" . TBL_PHOTO_ALBUM_ITEMS . "` AS `pai` ON `photo`.`photo_id` = `pai`.`photo_id` ";
		$join .= "LEFT JOIN `" . TBL_PHOTO_ALBUMS . "` AS `pa` ON `pai`.`album_id` = `pa`.`id` ";
		
		$query = "SELECT `photo`.* FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
					$join
					WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo) . "
					ORDER BY `added_stamp` DESC
					LIMIT $limit";
		$result = SK_MySQL::query($query);
		
		$list = array('items'=>array(), 'total'=>0);
		while ($item = $result->fetch_assoc()) {
			$list['items'][$item["photo_id"]] = $item;
		}

		$list['total'] = SK_MySQL::query("
			SELECT COUNT( DISTINCT `photo`.`photo_id` ) FROM `" . TBL_PROFILE_PHOTO . "` AS `photo` 
			$join
			WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo) 
		)->fetch_cell();
		
		return $list;
	}
	
	public static function MostViewed($pp_and_fo = true)
	{
		$page = self::page();
		$result_per_page = SK_Config::section("photo")->Section("general")->per_page;
		
		$limit = $result_per_page  *( $page-1 ).", ".$result_per_page;
		
		//$join = "INNER JOIN `" . TBL_PHOTO_VIEW . "` AS `view` USING(`photo_id`) ";
		$join .= "INNER JOIN `" . TBL_PROFILE . "` AS `profile` ON `profile`.`profile_id` = `photo`.`profile_id` ";
		
		$join .= "LEFT JOIN `" . TBL_PHOTO_ALBUM_ITEMS . "` AS `pai` ON `photo`.`photo_id` = `pai`.`photo_id` ";
        $join .= "LEFT JOIN `" . TBL_PHOTO_ALBUMS . "` AS `pa` ON `pai`.`album_id` = `pa`.`id` ";
		
		$query = "SELECT photo.*, `photo`.`view_count` as `viewed` FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
					$join 
					WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo) . "
                          AND `photo`.`view_count` > 0
					ORDER BY `viewed` DESC
					LIMIT $limit";
        
		$result = SK_MySQL::query($query);
		
		$list = array('items'=>array(), 'total'=>0);
		while ($item = $result->fetch_assoc()) {
			$list['items'][$item["photo_id"]] = $item;
		}
		
		$list['total'] = SK_MySQL::query("
			SELECT COUNT( DISTINCT `photo`.`photo_id` ) FROM `" . TBL_PROFILE_PHOTO . "` AS `photo` 
			$join
			WHERE `photo`.`view_count` > 0 AND (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo)
		)->fetch_cell();
		
		return $list;
	}
	
	public static function TopRated($pp_and_fo = true, $sex = null)
	{
		$page = self::page();
		$result_per_page = SK_Config::section("photo")->Section("general")->per_page;
		
		$limit = $result_per_page  *( $page-1 ).", ".$result_per_page;
		
		$min_rates_count = (int)SK_Config::section("photo")->Section("general")->min_rates_count;
		
		$sex_condition = '1';
		if ( isset($sex) && ($sex = intval($sex)) ) {
			$sex_condition = '`profile`.`sex`=' . $sex; 
		}
		
		$join = " INNER JOIN 
		( 
			SELECT `entity_id` , AVG( `score` ) AS `avg_score` , COUNT( * ) AS `count` 
			FROM `" . TBL_PHOTO_RATE . "` 
			GROUP BY `entity_id` 
			HAVING `count` >= $min_rates_count
		) AS `rates` ON `photo`.`photo_id` = `rates`.`entity_id` ";
		
		$join .= "INNER JOIN `" . TBL_PROFILE . "` AS `profile` ON `profile`.`profile_id` = `photo`.`profile_id` ";
		
		$join .= "LEFT JOIN `" . TBL_PHOTO_ALBUM_ITEMS . "` AS `pai` ON `photo`.`photo_id` = `pai`.`photo_id` ";
        $join .= "LEFT JOIN `" . TBL_PHOTO_ALBUMS . "` AS `pa` ON `pai`.`album_id` = `pa`.`id` ";
		
		$query = "SELECT photo.* , `rates`.avg_score, `rates`.`count`  FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
					$join 
					WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND $sex_condition AND " . self::activeSqlStr('photo', $pp_and_fo) . " 
					ORDER BY `avg_score` DESC
					LIMIT $limit";

		$result = SK_MySQL::query($query);
		
		$list = array('items'=>array(), 'total'=>0);
		while ($item = $result->fetch_assoc()) {
			$list['items'][$item["photo_id"]] = $item;
		}
		
		$query = "SELECT COUNT(*) AS `count` FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
                                            $join 
                                            WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND $sex_condition AND " . self::activeSqlStr('photo', $pp_and_fo) . " 
                                        ";
        $list['total'] = SK_MySQL::query($query)->fetch_cell();
        return $list;
	}
	
    public static function TaggedPhotoList( $tag )
    {
        $page = self::page();
        $result_per_page = SK_Config::section("photo")->Section("general")->per_page;
        
        $limit = $result_per_page  *( $page-1 ).", ".$result_per_page;
        
        $photoIds = app_TagService::stFindEntityIdsForTag('photo', $tag);
        
        if ( !count($photoIds) )
        {
            return array('items' => array(), 'total' => 0);
        }
        
        $join = "INNER JOIN `" . TBL_PROFILE . "` AS `profile` ON `profile`.`profile_id` = `photo`.`profile_id` ";
        $join .= "LEFT JOIN `" . TBL_PHOTO_ALBUM_ITEMS . "` AS `pai` ON `photo`.`photo_id` = `pai`.`photo_id` ";
        $join .= "LEFT JOIN `" . TBL_PHOTO_ALBUMS . "` AS `pa` ON `pai`.`album_id` = `pa`.`id` ";
        
        $query = "SELECT photo.* FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
                    $join 
                    WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo) . " 
                        AND `photo`.`photo_id` IN ( " . implode(', ', $photoIds) . " )
                    ORDER BY `added_stamp` DESC
                    LIMIT $limit";

        $result = SK_MySQL::query($query);
        
        $list = array('items'=>array(), 'total'=>0);
        while ($item = $result->fetch_assoc()) {
            $list['items'][$item["photo_id"]] = $item;
        }
        
        $query = "SELECT COUNT(*) AS `count` FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
                                            $join 
                                            WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo) . "
                                                 AND `photo`.`photo_id` IN ( " . implode(', ', $photoIds) . " )
                                        ";
        $list['total'] = SK_MySQL::query($query)->fetch_cell();
        
        return $list;
    }
	
	public static function MostCommented($pp_and_fo = true)
	{
		$page = self::page();
		$result_per_page = SK_Config::section("photo")->Section("general")->per_page;
		
		$limit = $result_per_page  *( $page-1 ).", ".$result_per_page;
		
		$join = "INNER JOIN `" . TBL_PHOTO_COMMENT . "` AS `coment` ON `photo`.`photo_id` = `coment`.`entity_id` ";
		$join .= "INNER JOIN `" . TBL_PROFILE . "` AS `profile` ON `profile`.`profile_id` = `photo`.`profile_id` ";
		
		$join .= "LEFT JOIN `" . TBL_PHOTO_ALBUM_ITEMS . "` AS `pai` ON `photo`.`photo_id` = `pai`.`photo_id` ";
        $join .= "LEFT JOIN `" . TBL_PHOTO_ALBUMS . "` AS `pa` ON `pai`.`album_id` = `pa`.`id` ";
		
		$query = "SELECT photo.*, COUNT(`coment`.`entity_id`) AS `comented` FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
					$join 
					WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo) . "
					GROUP BY `photo`.`photo_id`
					ORDER BY `comented` DESC
					LIMIT $limit";
        
		$result = SK_MySQL::query($query);
		
		$list = array('items'=>array(), 'total'=>0);
		while ($item = $result->fetch_assoc()) {
			$list['items'][$item["photo_id"]] = $item;
		}
		
		$list['total'] = SK_MySQL::query("
			SELECT COUNT( DISTINCT `photo`.`photo_id` ) FROM `" . TBL_PROFILE_PHOTO . "` AS `photo` 
			$join
			WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo) 
		)->fetch_cell();
		
		return $list;
	}
	
	public static function AlbumPhotos($album_id, $pp_and_fo = true)
	{
		if ( !($album_id = intval($album_id)) ) {
			return array();
		}
		
		$page = self::page();
		$result_per_page = SK_Config::section("photo")->Section("general")->per_page;
		
		$limit = $result_per_page  *( $page-1 ).", ".$result_per_page;
		
		$join = "INNER JOIN `" . TBL_PHOTO_ALBUM_ITEMS . "` AS `album` ON `photo`.`photo_id` = `album`.`photo_id` ";
		$join .= "INNER JOIN `" . TBL_PROFILE . "` AS `profile` ON `profile`.`profile_id` = `photo`.`profile_id` ";
		
		$query = "SELECT photo.* FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
					$join 
					WHERE `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo) . "
					GROUP BY `photo`.`photo_id`
					ORDER BY `album`.`add_stamp` DESC
					LIMIT $limit";
        
		$result = SK_MySQL::query($query);
		
		$list = array('items'=>array(), 'total'=>0);
		while ($item = $result->fetch_assoc()) {
			$list['items'][$item["photo_id"]] = $item;
		}
		
		$list['total'] = SK_MySQL::query("
					SELECT COUNT(`album`.`photo_id`) FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
					$join 
					WHERE `profile`.`status`='active' AND " . self::activeSqlStr('photo', $pp_and_fo)
		)->fetch_cell();
		
		return $list;
	}
}
