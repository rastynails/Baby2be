<?php

class app_ProfileMusic
{
	
	
	/**
	 * Returns music url
	 *
	 * @param string $file_hash
	 * @param string $file_ext
	 * @return string
	 */
	public static function getMusicURL( $file_hash, $file_ext )
	{
	       return URL_USERFILES.'pr_music_'.$file_hash.'.'.$file_ext;
	}

	/**
	 * Returns music dir
	 *
	 * @param string $file_hash
	 * @param string $file_ext
	 * @return string
	 */
	public static function getMusicDir( $file_hash, $file_ext )
	{
		return DIR_USERFILES.'pr_music_'.$file_hash.'.'.$file_ext;
	}
	
	/**
	 * Returns name of temporary music file
	 *
	 * @param integer $file_id
	 * @param string $file_ext
	 * @return string
	 */
	public static function getTmpMusicDir( $file_id, $file_ext )
	{
		return DIR_USERFILES.'conv_tmp_music_'.$file_id.'.'.$file_ext;
	}
	
	/**
	 * Returns url of music frame image
	 *
	 * @param string $file_hash
	 * @return string
	 */
	public static function getMusicFrameURL( $file_hash )
	{
		return URL_USERFILES.'pr_music_frame_'.$file_hash.'.jpg';
	}
	
	/**
	 * Returns dir of music frame image
	 *
	 * @param string $file_hash
	 * @return string
	 */
	public static function getMusicFrameDir( $file_hash )
	{
		return DIR_USERFILES.'pr_music_frame_'.$file_hash.'.jpg';
	}

	/**
	 * Returns dir of music thumbnail
	 *
	 * @param string $file_hash
	 * @return string
	 */
	public static function getMusicThumbnailDir( $file_hash )
	{
		return DIR_USERFILES.'pr_music_preview_'.$file_hash.'.jpg';
	}
	
	/**
	 * Returns url of music thumbnail
	 *
	 * @param string $file_hash
	 * @return string
	 */
	public static function getMusicThumbnailURL( $file_hash )
	{
		return URL_USERFILES.'pr_music_preview_'.$file_hash.'.jpg';
	}
	
	/**
	 * Sets a frame for music file
	 *
	 * @param string $file_hash
	 * @param string $file_ext
	 * @return integer
	 */
	public static function setMusicFrame( $file_hash, $music_id, $file_ext )
	{
		if ( !self::captureMusicFrame( self::getTmpMusicDir( $music_id, $file_ext ), self::getMusicFrameDir($file_hash), 10 ) )
			return false;

		// resize frame
		app_Image::resize(self::getMusicFrameDir($file_hash), SK_Config::section('music')->get('music_width'), SK_Config::section('music')->get('music_height'));
		
		$query = SK_MySQL::placeholder( "INSERT INTO ".TBL_PROFILE_MUSIC_FRAME."( `hash` )
			VALUES( '?' )
			ON DUPLICATE KEY UPDATE `hash`='?'", $file_hash, $file_hash );
		
		return SK_MySQL::query($query);;
	}
	
	/**
	 * Sets thumbnail for music
	 *
	 * @param string $file_hash
	 * @param string $file_ext
	 * @return boolean
	 */
	private static function setMusicThumb( $file_hash, $music_id, $file_ext )
	{
		if ( !$file_hash || !$file_ext || !$music_id)
			return false;
		
		if ( self::captureMusicFrame( self::getTmpMusicDir( $music_id, $file_ext ), self::getMusicThumbnailDir($file_hash), 1 ) )
			app_Image::resize(self::getMusicThumbnailDir($file_hash), SK_Config::section('music')->get('music_thumb_width'), SK_Config::section('music')->get('music_thumb_height'));
		
		return true;
	}

	private static function captureMusicFrame( $file_source, $img_dest, $frame_number = 2 )
	{
		$file_source = trim($file_source);
		$img_dest = trim($img_dest);

		if ( !file_exists($file_source) )
			return false;
		
		 	
		/* for local
		$tmp_file = DIR_USERFILES.'tmp_frame_'.rand( 1, 100000 ).'.jpeg';
		$cmd_command = DIR_SITE_ROOT."ffmpeg".DIRECTORY_SEPARATOR."ffmpeg.exe -vframes 1 -i $file_source -f image2 $tmp_file";

		exec( $cmd_command );
		
		if ( file_exists( $tmp_file ) )
			copy( $tmp_file, $img_dest );
		
		@unlink($tmp_file);
		*/
			
		// REMOTE
		$tmp_folder = DIR_USERFILES.'tmp_frame_'.rand( 1, 100000 );
	
		$cmd_command = PATH_FLV_MPLAYER." $file_source -ss $frame_number -nosound -vo jpeg:outdir=$tmp_folder -frames 5";
		
		exec( escapeshellcmd( $cmd_command ) );
		
		if ( file_exists( $tmp_folder.'/00000001.jpg' ) )
			copy( $tmp_folder.'/00000001.jpg', $img_dest );
		
		@unlink( $tmp_folder.'/00000001.jpg' );
		@rmdir( $tmp_folder );
			
		return file_exists($img_dest);
	}
	
	
	/**
	 * Returns list of all profile's music
	 *
	 * @param integer $profile_id
	 * @return array('list'=>array(),'total'=>integer)
	 */
	public static function getProfileUploadedMusic( $profile_id )
	{
		$profile_id = intval( $profile_id );

		if ( !$profile_id )
			return array();
		
        $query = SK_MySQL::placeholder( "SELECT `music`.*,
			COUNT(v.`music_id`) AS `view_count`,
			COUNT(c.`id`)  AS `comment_count`,
			ROUND(AVG(r.`score`),2) AS `rate_score`,
			COUNT(r.`id`) AS `rate_count`
			FROM `".TBL_PROFILE_MUSIC."` AS `music`
            LEFT JOIN `".TBL_PROFILE_MUSIC_VIEW."` v ON v.`music_id`=`music`.`music_id`
            LEFT JOIN `".TBL_MUSIC_COMMENT."` c ON c.`entity_id`=`music`.`music_id`
            LEFT JOIN `".TBL_MUSIC_RATE."` r ON r.`entity_id`=`music`.`music_id`
			WHERE `music`.`profile_id`=?
            GROUP BY `music`.`music_id`
			ORDER BY `music`.`upload_stamp` DESC", $profile_id );
   		$res = SK_MySQL::query($query);
		
		$ret = array();
		while ($item = $res->fetch_assoc())
		{
			$item['music_page'] = self::getMusicViewURL($item['hash']);
			$item['thumb_img'] = self::getMusicThumbnail($item);
			$ret[] = $item;
		}
			
		$return['list'] = $ret;
		
		$query = SK_MySQL::placeholder("SELECT COUNT( `music_id` ) FROM `".TBL_PROFILE_MUSIC."`
			WHERE `profile_id`=?", $profile_id );
		$return['total'] = SK_MySQL::query($query)->fetch_cell();
		return $return;
	}
	
	
/**
	 * Returns list of profile's active music
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public static function getProfileMusic( $profile_id, $status = '', $admin_mode = false, $page = null )
	{
		$profile_id = intval( $profile_id );
		$username = app_Profile::username($profile_id);

		if ( !$profile_id )
			return array();
		$page = intval($page);
		if ($page) {
			$on_page = SK_Config::Section('music')->display_music_list_limit;
			$limit = " LIMIT " . ($page - 1) * $on_page . ", " . $on_page; 
		}
		
		switch ($status)
		{
			case 'approval':
				$status_q = "AND `music`.`status` = 'approval'";
				break;
			case 'suspended':
				$status_q = "AND `music`.`status` = 'suspended'";
				break;
			case 'active':
				$status_q = "AND `music`.`status` = 'active'";
				break;
			default:
				$status_q = " ";
				break;
		}
		
		$query = SK_MySQL::placeholder( "SELECT `music`.*,
			COUNT(v.`music_id`) AS `view_count`,
			COUNT(c.`id`)  AS `comment_count`,
			ROUND(AVG(r.`score`),2) AS `rate_score`,
			COUNT(r.`id`) AS `rate_count`
			FROM `".TBL_PROFILE_MUSIC."` AS `music`
            LEFT JOIN `".TBL_PROFILE_MUSIC_VIEW."` v ON v.`music_id`=`music`.`music_id`
            LEFT JOIN `".TBL_MUSIC_COMMENT."` c ON c.`entity_id`=`music`.`music_id`
            LEFT JOIN `".TBL_MUSIC_RATE."` r ON r.`entity_id`=`music`.`music_id`

			WHERE `music`.`profile_id`=? ".$status_q."

            GROUP BY `music`.`music_id`

			ORDER BY `music`.`upload_stamp` DESC" . $limit, $profile_id);
		$res = SK_MySQL::query($query);
		
		$ret = array();
		while ($item = $res->fetch_assoc())
		{
			$item['music_page'] = self::getMusicViewURL($item['hash']);
			$item['thumb_img'] = self::getMusicThumbnail($item, $admin_mode);
			$item['username'] = $username;
			$ret[] = $item;
		}
			
		$return['list'] = $ret;
		
		$query = SK_MySQL::placeholder("SELECT COUNT( `music_id` ) FROM `".TBL_PROFILE_MUSIC."`
			WHERE `profile_id`=? AND `status`='active'", $profile_id );
		$return['total'] = SK_MySQL::query($query)->fetch_cell();
		
		return $return;
	}
	
	
	public static function getProfileOtherMusic( $profile_id, $music_id )
	{
		if ( !$profile_id )
			return array();	
		
		$query = SK_MySQL::placeholder( " SELECT `music`.*,
			COUNT(v.`music_id`) AS `view_count`,
			COUNT(c.`id`)  AS `comment_count`,
			ROUND(AVG(r.`score`),2) AS `rate_score`,
			COUNT(r.`id`) AS `rate_count`
			FROM `".TBL_PROFILE_MUSIC."` AS `music`
            LEFT JOIN `".TBL_PROFILE_MUSIC_VIEW."` v ON v.`music_id`=`music`.`music_id`
            LEFT JOIN `".TBL_MUSIC_COMMENT."` c ON c.`entity_id`=`music`.`music_id`
            LEFT JOIN `".TBL_MUSIC_RATE."` r ON r.`entity_id`=`music`.`music_id`

			WHERE `music`.`profile_id`=?
			AND `music`.`status`='active' AND `music`.`music_id` != ?

            GROUP BY `music`.`music_id`

			ORDER BY `music`.`upload_stamp` DESC LIMIT 5", $profile_id, $music_id );
            	$res = SK_MySQL::query($query);

                $return = array();
		while ($item = $res->fetch_assoc())
		{
			$item['music_page'] = self::getMusicViewURL($item['hash']);
			$item['thumb_img'] = self::getMusicThumbnail($item);
			$return[] = $item;
		}
		return $return;
	}

	public static function getMusicThumbnail( $music, $admin_mode = false )
	{
		$viewer = SK_HttpUser::profile_id();
		if ( !app_FriendNetwork::isProfileFriend($music['profile_id'], $viewer) && $viewer != $music['profile_id'] && $music['privacy_status'] == 'friends_only' && !$admin_mode )
			return 'friends_only';
		else return 'default';
		
	}
	
	/**
	 * Returns list of profile's music depending on passed parameter 'status'
	 *
	 * @param integer $profile_id
	 * @param string $status
	 * @return array('list'=>array(),'total'=>integer)
	 */
    public static function getProfileAlbumMusic( $profile_id, $music_list = 'latest' )
	{
		$profile_id = intval( $profile_id );

		if ( !$profile_id )
			return array();

		switch($music_list)
		{
			case 'latest':
				$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_PROFILE_MUSIC."`
					WHERE `profile_id`=? AND `status`='active' 
					ORDER BY `upload_stamp` DESC LIMIT 0, 1", $profile_id );
				
				$query_for_thumbs = SK_MySQL::placeholder( "SELECT * FROM `".TBL_PROFILE_MUSIC."`
					WHERE `profile_id`=? AND `status`='active'
					ORDER BY `upload_stamp` DESC LIMIT 1, 3", $profile_id );
                                break;
				
			case 'toprated':
				$query = SK_MySQL::placeholder( "SELECT `v`.*, 
					(SELECT ROUND(AVG(`score`),2) FROM `".TBL_MUSIC_RATE."` WHERE `entity_id`=`v`.`music_id`) AS `rate_score`
					FROM `".TBL_PROFILE_MUSIC."` AS `v`
					WHERE `profile_id`=? AND `status`='active'
					ORDER BY `rate_score` DESC LIMIT 0, 1", $profile_id );
				
				$query_for_thumbs = SK_MySQL::placeholder( "SELECT `v`.*, 
					(SELECT ROUND(AVG(`score`),2) FROM `".TBL_MUSIC_RATE."` WHERE `entity_id`=`v`.`music_id`) AS `rate_score`
					FROM `".TBL_PROFILE_MUSIC."` AS `v`
					WHERE `profile_id`=? AND `status`='active' 
					ORDER BY `rate_score` DESC LIMIT 1, 3", $profile_id );


				break; 
		}	

		$item = SK_MySQL::query($query)->fetch_assoc();
                
		$item['music_page'] = app_ProfileMusic::getMusicViewURL($item['hash']);
                if($item['music_source'] == 'file')
			$item['music_url'] =  self::getMusicURL($item['hash'], $item['extension']);
		
		$items = SK_MySQL::query($query_for_thumbs);
               
		while($music = $items->fetch_assoc())
		{
                    
			if($music['music_source'] == 'file')
				$music['music_url'] =  self::getMusicURL($music['hash'], $music['extension']);
				
			$music['thumb_img'] = self::getMusicThumbnail($music);
			$music['music_page'] = self::getMusicViewURL($music['hash']);
			$list[] = $music;
		}
		
		$ret['for_player'] = $item;
		$ret['for_thumbs'] = $list;

		return $ret;
	}
	
	/**
	 * Adds uploaded music to profile's list of music files.
	 * Returns music_id - identificator of new added music file.
	 *
	 * @param integer $profile_id
	 * @param string $title
	 * @param string $desc
	 * @return integer
	 */
	public static function addMusic( $profile_id, $title, $desc, $privacy_status, $hash, $extension )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return -1;
			
		if ( !strlen( trim( $title ) ) )
			return -2;
			
		if ( !in_array($privacy_status, array('public','friends_only')) )
			$privacy_status = 'public';
		
		//check limits
		$query = SK_MySQL::placeholder( "SELECT COUNT(`music_id`) FROM `".TBL_PROFILE_MUSIC."`
			WHERE `profile_id`=?", $profile_id );
		
		if ( SK_MySQL::query($query)->fetch_cell() >= SK_Config::section('music')->get('upload_music_files_limit') )
			return -4;
		
		$query = "UPDATE `".TBL_PROFILE."` SET `has_music`='y' WHERE `profile_id`='$profile_id'";
		SK_MySQL::query($query);
		
		$music_status = (SK_Config::section('site')->Section('automode')->get('set_active_music_on_upload')) ? 'active' : 'approval';

		//$is_converted = $is_converted ? 'yes' : 'no';
		
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_PROFILE_MUSIC."`
			( `profile_id`, `title`, `description`, `status`, `privacy_status`, `extension`, `music_source`, `hash`, `upload_stamp` )
			VALUES( ?, '?', '?', '?', '?', '?', 'file', '?', ? ) ", 
			$profile_id, $title, $desc, $music_status, $privacy_status, $extension, $hash, time());
		$res = SK_MySQL::query($query);
			
		return SK_MySQL::insert_id();
	}
	
	public static function addEmbeddableMusic( $profile_id, $title, $desc, $privacy_status, $hash, $code )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return -1;

		$title = trim($title);
		$desc = trim($desc);
		$code = trim($code);	
			
		if ( !strlen($title) )
			return -2;
			
		// TODO: check if embeddable code is valid
			
		if ( !in_array($privacy_status, array('public','friends_only')) )
			$privacy_status = 'public';
		
		//check limits
		$query = SK_MySQL::placeholder( "SELECT COUNT(`music_id`) FROM `".TBL_PROFILE_MUSIC."`
			WHERE `profile_id`=?", $profile_id );
		
		if ( SK_MySQL::query($query)->fetch_cell() >= SK_Config::section('music')->get('upload_music_files_limit') )
			return -4;
			
		$query = "UPDATE `".TBL_PROFILE."` SET `has_music`='y' WHERE `profile_id`='$profile_id'";
		SK_MySQL::query($query);

	        $music_status = (SK_Config::section('site')->Section('automode')->get('set_active_music_on_upload')) ? 'active' : 'approval';
		
		$source = self::detectEmbedMusicSource($code);
		
		//$code = self::addEmbedWmodeParam($code);
		//$code = self::formatEmbedCode($code);
		
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_PROFILE_MUSIC."`
			( `profile_id`, `title`, `description`, `status`, `privacy_status`, `music_source`, `hash`, `upload_stamp`, `code` )
			VALUES( ?, '?', '?', '?', '?', '?', '?', ?, '?' ) ", 
			$profile_id, $title, $desc, $music_status, $privacy_status, $source, $hash, time(), $code);
		
		$res = SK_MySQL::query($query);
			
		return SK_MySQL::insert_id();	
	}
	
	
	public static function updateMusic( $hash, $title, $desc, $status )
	{
		$title = trim($title);
		$desc = trim($desc);
		$status = trim($status);
		
		if (!strlen($title) || !strlen($status) || !strlen($hash))
			return false;
			
		$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE_MUSIC."` SET `title`='?', `description`='?', `privacy_status`='?'
			WHERE `hash`='?'", $title, $desc, $status, $hash);

		SK_MySQL::query($query);
		
		if (SK_MySQL::affected_rows())
			return true;
			
		return false;
	}
	
	
	/**
	 * Returns count of profile music files.
	 *
	 * @param integer $profile_id
	 * @param string $status
	 * @return integer
	 */
	public static function getProfileMusicCount( $profile_id, $status = null )
	{
		if ( !is_numeric($profile_id) || !intval($profile_id) )
			return 0;
		
		$query_ins = isset($status) ? ( (in_array($status, array('approval', 'active', 'suspended'))) ? "AND `status`='".$status."'" : '') : '';
		
		$query = SK_MySQL::placeholder( "SELECT COUNT(`music_id`) FROM `".TBL_PROFILE_MUSIC."`
			WHERE `profile_id`=? $query_ins", $profile_id );
		
		return intval( SK_MySQL::query($query)->fetch_cell() );
	}
	
	/**
	 * Returns profile_id of music file owner
	 *
	 * @param string $mediafile_hash
	 * @return integer
	 */
	public static function getMusicOwner( $mediafile_hash )
	{
		if ( !$mediafile_hash )
			return false;
			
		$query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_PROFILE_MUSIC."`
			WHERE `hash`='?' LIMIT 1", $mediafile_hash );
		
		return SK_MySQL::query($query)->fetch_cell();
	}

	
	/**
	 * Returns profile_id of music file owner
	 *
	 * @param integer $music_id
	 * @return integer
	 */
	public static function getMusicOwnerById( $music_id )
	{
		if ( !$music_id )
			return false;
			
		$query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_PROFILE_MUSIC."`
			WHERE `music_id`=? LIMIT 1", $music_id );
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Returns music info
	 *
	 * @param integer $profile_id
	 * @param string $hash
	 * @return array
	 */
	public static function getMusicInfo( $profile_id, $hash )
	{
		$profile_id = intval($profile_id);
		$hash = trim($hash);
		
		$query = SK_MySQL::placeholder("SELECT `music`.*,
			COUNT(v.`music_id`) AS `view_count`,
			COUNT(c.`id`)  AS `comment_count`,
			ROUND(AVG(r.`score`),2) AS `rate_score`,
			COUNT(r.`id`) AS `rate_count`
            
			FROM `".TBL_PROFILE_MUSIC."` AS `music`

            LEFT JOIN `".TBL_PROFILE_MUSIC_VIEW."` v ON v.`music_id`=`music`.`music_id`
            LEFT JOIN `".TBL_MUSIC_COMMENT."` c ON c.`entity_id`=`music`.`music_id`
            LEFT JOIN `".TBL_MUSIC_RATE."` r ON r.`entity_id`=`music`.`music_id`

            WHERE `music`.`profile_id`=? AND `music`.`hash`='?'
            GROUP BY `music`.`music_id`
            ", $profile_id, $hash );
		
		$res = SK_MySQL::query($query)->fetch_assoc();
		
		return $res;
	}
	
	/** Adds record to music view table
	 * Identifies new viewer by profile_id or by IP (if the third parameter is specified)
	 *
	 * @param integer $music_id
	 * @param integer $profile_id
	 * @param boolean $track_ip
	 * @return boolean
	 */
	public static function updateViews( $music_id, $profile_id, $track_ip = true )
	{
		$music_id = intval($music_id);
		$profile_id = intval($profile_id);
		
		if (!$music_id)
			return false;
		
		if (!$profile_id && !$track_ip )
			return false;

		if ($profile_id)
		{
			$query = SK_MySQL::placeholder("SELECT `music_id` FROM `".TBL_PROFILE_MUSIC_VIEW."`
				WHERE `music_id` = ? AND `viewer_profile_id`=?", $music_id, $profile_id);
			
			if ( !SK_MySQL::query($query)->fetch_cell() )
			{
				$query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_MUSIC_VIEW."`
					( `music_id`, `viewer_profile_id`, `ip` )
					VALUES( ?,?, INET_ATON( '?' ) )", $music_id, $profile_id, SK_HttpRequest::getRequestIP() );
				SK_MySQL::query($query);
			}
		}
		else 
		{
			$query = SK_MySQL::placeholder("SELECT `music_id` FROM `".TBL_PROFILE_MUSIC_VIEW."`
				WHERE `music_id`=? AND `ip`=INET_ATON( '?' )", $music_id, SK_HttpRequest::getRequestIP() );
			if ( !SK_MySQL::query($query)->fetch_cell() )
			{
				$query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_MUSIC_VIEW."`( `music_id`, `ip` )
					VALUES( ?, INET_ATON( '?' ) )", $music_id, SK_HttpRequest::getRequestIP() );
				SK_MySQL::query($query);
			}
		}
		
		return true;
	}

	/**
	 * Returns URL to page with music
	 *
	 * @param string $file_hash
	 * @return string
	 */
    public static function getMusicViewURL( $file_hash )
	{
		if ( !$file_hash )
			return false;
		SK_Navigation::LoadModule('music');
		return SK_Navigation::href('music_view', array('musickey'=>$file_hash) );
	}
	
	public static function detectEmbedMusicSource($code)
	{
		$music_providers = array(
			'youtube' => 'http://www.youtube.com/',
			'metacafe' => 'http://www.metacafe.com/'
		);
		
		foreach ($music_providers as $name => $url)
		{
			if (preg_match("~$url~", $code))
				return $name;
		}
		return 'unknown';
	}

		
	/**
	 * Return the hash of music file by music_id
	 *
	 * @param integer $music_id
	 * @return string
	 */
	public static function getMusicHash( $music_id )
	{
		$music_id = intval( $music_id );
		
		if ( !$music_id )
			return false;
		
		$query = SK_MySQL::placeholder( "SELECT `hash` FROM `".TBL_PROFILE_MUSIC."`
			WHERE `music_id` =?", $music_id );
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Deletes profile music info from db and file system.
	 *
	 * @param integer $music_id
	 * @return boolean
	 */
	public static function deleteMusic( $music_id )
	{
		if ( !is_numeric( $music_id ) || !intval( $music_id ) )
			return false;
			
		$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_PROFILE_MUSIC."` WHERE `music_id`=?", $music_id );
		$file_info = SK_MySQL::query($query)->fetch_assoc();
		
		// delete music record from database
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_MUSIC."` WHERE `music_id`=?", $music_id );
		SK_MySQL::query($query);
		
		//delete comments
		app_CommentService::stDeleteEntityComments('music', $music_id, ENTITY_TYPE_MUSIC_UPLOAD);
		
		app_TagService::stUnlinkAllTags('music', $music_id);

		app_UserActivities::deleteActivities($music_id, 'music_upload');
		app_UserActivities::deleteActivities($music_id, 'music_comment');

        //Newsfeed
        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            app_Newsfeed::newInstance()->removeAction(ENTITY_TYPE_MUSIC_UPLOAD, $music_id);
        }     
        
		//delete views
		self::deleteViewScore($music_id);
		
		if( $file_info['music_source'] == 'file')
		{
			@unlink( self::getMusicDir( $file_info['hash'], $file_info['extension'] ) );
			
			// remove music frame image
			self::deleteMusicFrame($file_info['hash']);
			
			// remove music thumb image
			self::deleteMusicThumb($file_info['hash']);
		}
		
		// check if was last music file with active status
		$query = SK_MySQL::placeholder( "SELECT COUNT(`music_id`) FROM `".TBL_PROFILE_MUSIC."`
			WHERE `profile_id`=? ", $file_info['profile_id'] );
		
		$music_count = SK_MySQL::query($query)->fetch_cell();
		
		if ( !$music_count )
		{
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_PROFILE."` SET `has_music`='n'
				WHERE `profile_id`=?", $file_info['profile_id'] );

			SK_MySQL::query($query);		
		}
				
		return true;
	}
	
	/**
	 * Deletes music frame image from db and file system
	 *
	 * @param string $hash
	 * @return boolean
	 */
	private static function deleteMusicFrame( $hash )
	{
		if ( !$hash )
			return false;
		
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_MUSIC_FRAME."`
			WHERE `hash` = '?'", $hash );
		SK_MySQL::query($query);
		
		@unlink( self::getMusicFrameDir($hash) );
		
		return true;
	}
	
	/**
	 * Delete music preview image from file system
	 *
	 * @param string $musicfile_hash
	 * @return boolean
	 */
	private static function deleteMusicThumb( $hash )
	{
		@unlink( self::getMusicThumbnailDir($hash) );
		
		return true;
	}
	
	/**
	 * Remove info about music views
	 *
	 * @param int $music_id
	 */
	private static function deleteViewScore( $music_id )
	{
		$music_id = intval( $music_id );
		if ( !$music_id )
			return false;
			
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_MUSIC_VIEW."`
			WHERE `music_id`=?", $music_id );
		
		SK_MySQL::query($query);
		
		return SK_MySQL::affected_rows();
	}
	
	public static function formatEmbedCode( $code, $width = null, $height = null )
	{
		$code = self::validateEmbedCode($code);
		
		if (!strlen($code))
			return '';

        // add wmode param
        $code = self::addEmbedWmodeParam($code);

		if ( !$width || !$height )
		{
            return $code;
		}
		
		//adjust width and height
		$code = preg_replace("/width=(\"|')?[\d]+(px|%)?(\"|')?/i",'width="'.$width.'"', $code);
		$code = preg_replace("/height=(\"|')?[\d]+(px|%)?(\"|')?/i",'height="'.$height.'"', $code);
		
		$code = preg_replace("/width:?[\d]+(px)?/i",'width: '.$width.'px', $code);
		$code = preg_replace("/height:?[\d]+(px)?/i",'height: '.$height.'px', $code);
				
		//remove YouTube search bar 
		if (preg_match('~http://www.youtube.com/~', $code)) {
			$code = preg_replace('~"http://www.youtube.com/v/([^"]+)"~i', 'http://www.youtube.com/v/$1&rel=0', $code);
		}
		
		return $code;
	}
	
	public static function getMusicEmbedCode( $music_id )
	{
		$query = SK_MySQL::placeholder("SELECT `code` FROM `".TBL_PROFILE_MUSIC."` WHERE `music_id`=?", $music_id);
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	public static function addEmbedWmodeParam($code)
	{
		$repl = $code;
		
		if (preg_match("/<object/i", $code))
		{
			$search_pattern = "<param";
			$pos = stripos($code, $search_pattern);
			if ($pos) {
				$add_param = '<param name="wmode" value="transparent"></param><param';
				$repl = substr_replace($code, $add_param, $pos, strlen($search_pattern));
			}
		}
		
		if (preg_match("/<embed/i", isset($repl) ? $repl : $code))
			$repl = preg_replace("/<embed/i", '<embed wmode="transparent"', isset($repl) ? $repl : $code);

		return $repl;
	}

	public static function deleteProfileMusics($profile_id)
	{
		$profile_id = intval($profile_id);

		if (!$profile_id)
			return array();
		
		$query = SK_MySQL::placeholder("SELECT `music_id` FROM `".TBL_PROFILE_MUSIC."` WHERE `profile_id`=?", $profile_id);
		$res = SK_MySQL::query($query);

		if( $res->num_rows())
		{
			while ($music = $res->fetch_cell() )
				self::deleteMusic($music);
				
			return true;
		}	
		return false;
	}

	public static function validateEmbedCode($code)
	{
	    $code = preg_replace('#(\n?<script[^>]*?>.*?</script[^>]*?>)|(\n?<script[^>]*?/>)#is', '', $code);
	    
		$obj_start = "<object";
		$obj_end   = "</object>";
		
		$pos_obj_start = stripos($code, $obj_start);
		$pos_obj_end   = stripos($code, $obj_end);
		
		if ($pos_obj_start !== false && $pos_obj_end !== false) {
			$pos_obj_end += strlen($obj_end);
			return substr($code, $pos_obj_start, $pos_obj_end - $pos_obj_start);
			
		} else {
			$emb_start = "<embed";
			$emb_end   = "</embed>"; $emb_end_s = "/>";
			
			$pos_emb_start = stripos($code, $emb_start);
			$pos_emb_end   = stripos($code, $emb_end) ? stripos($code, $emb_end) : stripos($code, $emb_end_s);
			
			if ($pos_emb_start !== false && $pos_emb_end !== false) {
				$pos_emb_end += strlen($emb_end);
				return substr($code, $pos_emb_start, $pos_emb_end - $pos_emb_start);
				
			} else 
				return '';
		}		
	} 
}