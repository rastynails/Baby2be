<?php

// flash video paths
if (SK_Config::section('video')->media_mode == "flash_video")
{
	define( 'PATH_FLV_MPLAYER', SK_Config::section('video')->Section('flash_mode')->get('mplayer_path') );
	define( 'PATH_FLV_MENCODER', SK_Config::section('video')->Section('flash_mode')->get('mencoder_path') );
	define( 'PATH_FLV_FFMPEG', SK_Config::section('video')->Section('flash_mode')->get('ffmpeg_path') );
}

/**
 * Class for working with profile video sharing
 *
 */
class app_ProfileVideo
{
	/**
	 * Maximum number of video files to be converted at once
	 */
	const VIDEOS_NUM_CONVERT = 10;
	
	/**
	 * Returns video url
	 *
	 * @param string $file_hash
	 * @param string $file_ext
	 * @return string
	 */
	public static function getVideoURL( $file_hash, $file_ext )
	{
		return URL_USERFILES.'pr_media_'.$file_hash.'.'.$file_ext;
	}

	/**
	 * Returns video dir
	 *
	 * @param string $file_hash
	 * @param string $file_ext
	 * @return string
	 */
	public static function getVideoDir( $file_hash, $file_ext )
	{
		return DIR_USERFILES.'pr_media_'.$file_hash.'.'.$file_ext;
	}
	
	/**
	 * Returns name of temporary video file 
	 *
	 * @param integer $file_id
	 * @param string $file_ext
	 * @return string
	 */
	public static function getTmpVideoDir( $file_id, $file_ext )
	{
		return DIR_USERFILES.'conv_tmp_media_'.$file_id.'.'.$file_ext;
	}
	
	/**
	 * Returns url of video frame image 
	 *
	 * @param string $file_hash
	 * @return string
	 */
	public static function getVideoFrameURL( $file_hash )
	{
		return URL_USERFILES.'pr_media_frame_'.$file_hash.'.jpg';
	}
	
	/**
	 * Returns dir of video frame image
	 *
	 * @param string $file_hash
	 * @return string
	 */
	public static function getVideoFrameDir( $file_hash )
	{
		return DIR_USERFILES.'pr_media_frame_'.$file_hash.'.jpg';
	}

	/**
	 * Returns dir of video thumbnail
	 *
	 * @param string $file_hash
	 * @return string
	 */
	public static function getVideoThumbnailDir( $file_hash )
	{
		return DIR_USERFILES.'pr_media_preview_'.$file_hash.'.jpg';
	}
	
	/**
	 * Returns url of video thumbnail
	 *
	 * @param string $file_hash
	 * @return string
	 */
	public static function getVideoThumbnailURL( $file_hash )
	{
		return URL_USERFILES.'pr_media_preview_'.$file_hash.'.jpg';
	}
	
	/**
	 * Sets a frame for video file 
	 *
	 * @param string $file_hash
	 * @param string $file_ext
	 * @return integer
	 */
	public static function setVideoFrame( $file_hash, $video_id, $file_ext )
	{
		if ( !self::captureVideoFrame( self::getTmpVideoDir( $video_id, $file_ext ), self::getVideoFrameDir($file_hash), 5 ) )
			return false;

		// resize frame
		app_Image::resize(self::getVideoFrameDir($file_hash), SK_Config::section('video')->get('video_width'), SK_Config::section('video')->get('video_height'));
		
		$query = SK_MySQL::placeholder( "INSERT INTO ".TBL_PROFILE_VIDEO_FRAME."( `hash` )
			VALUES( '?' )
			ON DUPLICATE KEY UPDATE `hash`='?'", $file_hash, $file_hash );
		
		return SK_MySQL::query($query);;
	}
	
	/**
	 * Sets thumbnail for video
	 *
	 * @param string $file_hash
	 * @param string $file_ext
	 * @return boolean
	 */
	private static function setVideoThumb( $file_hash, $video_id, $file_ext )
	{
		if ( !$file_hash || !$file_ext || !$video_id)
			return false;
		
		if ( self::captureVideoFrame( self::getTmpVideoDir( $video_id, $file_ext ), self::getVideoThumbnailDir($file_hash), 1 ) )
			app_Image::resize(self::getVideoThumbnailDir($file_hash), SK_Config::section('video')->get('video_thumb_width'), SK_Config::section('video')->get('video_thumb_height'));
		
		return true;
	}
	
	private static function convertVideo2FLV( $video_id, $file_source, $file_dest, $remove_original = true )
	{
		$file_source = trim( $file_source );
		$file_dest = trim( $file_dest );
		
		if (!file_exists( $file_source ))
			return false;
				
		// for local
		//exec( DIR_SITE_ROOT.'ffmpeg'.DIRECTORY_SEPARATOR."ffmpeg.exe -i $file_source -ar 22050 $file_dest");
		
		// using mencoder
		// exec( escapeshellcmd( PATH_FLV_MENCODER." $file_source -o $file_dest -of lavf -oac mp3lame -lameopts abr:br=56 -ovc lavc -lavcopts vcodec=flv:vbitrate=9600:mbd=2:mv0:trell:v4mv:cbp:last_pred=3 -lavfopts i_certify_that_my_video_stream_does_not_use_b_frames -srate 22050" ), $p );
		
        // using ffmpeg
		exec( escapeshellcmd( PATH_FLV_FFMPEG." -i $file_source -b 9600k -acodec libfaac -ab 128k -ar 22050 $file_dest" ), $p );
		
		$query = SK_MySQL::placeholder("DELETE FROM `".TBL_TMP_VIDEOFILE."` WHERE `video_id`=?", $video_id);
		SK_MySQL::query($query);
		
		$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE_VIDEO."` SET `is_converted`='yes', `extension`='flv' 
			WHERE `video_id`=?", $video_id);
		SK_MySQL::query($query);
		
		if ($remove_original)
			@unlink($file_source);
		
		return true;
	}
	
	/**
	 * Shedules video file for convertation by ffmpeg
	 *
	 * @param integer $video_id
	 * @param string $tmp_filename
	 * @return boolean
	 */
	public static function sheduleVideoForConvert( $video_id, SK_TemporaryFile $tmp_file, $file_hash )
	{
		if (!isset($tmp_file) || !$video_id)
			return false;
			
		$destination = self::getTmpVideoDir($video_id, $tmp_file->getExtension());
						
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_TMP_VIDEOFILE."` 
			(`video_id`, `file_ext`, `hash`, `upload_stamp`, `in_process`) 
			VALUES(?, '?', '?', ?, 'no')", $video_id, $tmp_file->getExtension(), $file_hash, time() );
		SK_MySQL::query($query);
		
		$tmp_file->move($destination);
				
		return SK_MySQL::insert_id();
	}
	
	/**
	 * Method for starting video convert (invocated by crontab)  
	 *
	 */
	public static function runCronVideoConvert()
	{
		$query = SK_MySQL::placeholder("SELECT COUNT(`id`) FROM `".TBL_TMP_VIDEOFILE."` 
			WHERE `in_process`='yes' ORDER BY `upload_stamp` ASC" );
		
		$are_processed = SK_MySQL::query($query)->fetch_cell();
		$can_process = self::VIDEOS_NUM_CONVERT - intval($are_processed);
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_TMP_VIDEOFILE."` 
			WHERE `in_process`='no' ORDER BY `upload_stamp` ASC LIMIT ?", $can_process);
		
		$res = SK_MySQL::query($query);
		while($video = $res->fetch_assoc())
		{
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_TMP_VIDEOFILE."` 
				SET `in_process`='yes' WHERE `video_id`=? AND `hash`='?'", $video['video_id'], $video['hash'] );
			SK_MySQL::query($query);
		
			self::setVideoFrame($video['hash'], $video['video_id'], $video['file_ext'] ); 
			self::setVideoThumb($video['hash'], $video['video_id'], $video['file_ext'] );
			
			$converted = self::convertVideo2FLV( 
				$video['video_id'],
				self::getTmpVideoDir( $video['video_id'], $video['file_ext']),
				self::getVideoDir( $video['hash'], 'flv')
			);
		}
	}
		
	private static function captureVideoFrame( $file_source, $img_dest, $frame_number = 2 )
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
		
        for ( $i = 1; $i <= 5; $i++ )
        {
            @unlink( $tmp_folder.'/0000000'.$i.'.jpg' );
        }
		@rmdir( $tmp_folder );
			
		return file_exists($img_dest);
	}
	
	
	/**
	 * Returns list of all profile's video
	 *
	 * @param integer $profile_id
	 * @return array('list'=>array(),'total'=>integer)
	 */
	public static function getProfileUploadedVideo( $profile_id )
	{
		$profile_id = intval( $profile_id );

		if ( !$profile_id )
			return array();
		
		$query = SK_MySQL::placeholder( "SELECT `video`.*,
			(SELECT COUNT(`video_id`) FROM `".TBL_PROFILE_VIDEO_VIEW."` WHERE `video_id`=`video`.`video_id`) AS `view_count`,
			(SELECT COUNT(`id`) FROM `".TBL_VIDEO_COMMENT."` WHERE `entity_id`=`video`.`video_id`) AS `comment_count`,
			(SELECT ROUND(AVG(`score`),2) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`video`.`video_id`) AS `rate_score`,
			(SELECT COUNT(`id`) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`video`.`video_id`) AS `rate_count`
			FROM `".TBL_PROFILE_VIDEO."` AS `video`
			WHERE `video`.`profile_id`=? 
			ORDER BY `video`.`upload_stamp` DESC", $profile_id );
		$res = SK_MySQL::query($query);
		
		$ret = array();
		while ($item = $res->fetch_assoc())
		{
			$item['video_page'] = self::getVideoViewURL($item['hash']);
			$item['thumb_img'] = self::getVideoThumbnail($item);
			$ret[] = $item;
		}
			
		$return['list'] = $ret;
		
		$query = SK_MySQL::placeholder("SELECT COUNT( `video_id` ) FROM `".TBL_PROFILE_VIDEO."`
			WHERE `profile_id`=?", $profile_id );
		$return['total'] = SK_MySQL::query($query)->fetch_cell();
		
		return $return;
	}
	
	
/**
	 * Returns list of profile's active video
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public static function getProfileVideo( $profile_id, $status = '', $admin_mode = false, $page = null )
	{
		$profile_id = intval( $profile_id );
		$username = app_Profile::getFieldValues($profile_id, 'username');

		if ( !$profile_id )
			return array();
		$page = intval($page);
		if ($page) {
			$on_page = SK_Config::Section('video')->Section('other_settings')->display_media_list_limit;
			$limit = " LIMIT " . ($page - 1) * $on_page . ", " . $on_page; 
		}
		
		switch ($status)
		{
			case 'approval':
				$status_q = "AND `video`.`status` = 'approval' AND `video`.`is_converted`='yes'";
				break;
			case 'suspended':
				$status_q = "AND `video`.`status` = 'suspended' AND `video`.`is_converted`='yes'";
				break;
			case 'active':
				$status_q = "AND `video`.`status` = 'active' AND `video`.`is_converted`='yes'";
				break;
			default:
				$status_q = "AND `video`.`is_converted`='yes'";
				break;
		}
		
		$query = SK_MySQL::placeholder( "SELECT `video`.*,
			(SELECT COUNT(`video_id`) FROM `".TBL_PROFILE_VIDEO_VIEW."` WHERE `video_id`=`video`.`video_id`) AS `view_count`,
			(SELECT COUNT(`id`) FROM `".TBL_VIDEO_COMMENT."` WHERE `entity_id`=`video`.`video_id`) AS `comment_count`,
			(SELECT ROUND(AVG(`score`),2) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`video`.`video_id`) AS `rate_score`,
			(SELECT COUNT(`id`) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`video`.`video_id`) AS `rate_count`
			FROM `".TBL_PROFILE_VIDEO."` AS `video`
			WHERE `video`.`profile_id`=? ".$status_q." 
			ORDER BY `video`.`upload_stamp` DESC" . $limit, $profile_id);
		$res = SK_MySQL::query($query);
		
		$ret = array();
		while ($item = $res->fetch_assoc())
		{
			$item['video_page'] = self::getVideoViewURL($item['hash']);
			$item['thumb_img'] = self::getVideoThumbnail($item, $admin_mode);
			$item['username'] = $username;
			$ret[] = $item;
		}
			
		$return['list'] = $ret;
		
		$query = SK_MySQL::placeholder("SELECT COUNT( `video_id` ) FROM `".TBL_PROFILE_VIDEO."`
			WHERE `profile_id`=? AND `status`='active' AND `is_converted`='yes'", $profile_id );
		$return['total'] = SK_MySQL::query($query)->fetch_cell();
		
		return $return;
	}
	
	
	public static function getProfileOtherVideo( $profile_id, $video_id )
	{
		if ( !$profile_id )
			return array();	
		
		$query = SK_MySQL::placeholder( "SELECT `video`.*,
			(SELECT COUNT(`video_id`) FROM `".TBL_PROFILE_VIDEO_VIEW."` WHERE `video_id`=`video`.`video_id`) AS `view_count`,
			(SELECT COUNT(`id`) FROM `".TBL_VIDEO_COMMENT."` WHERE `entity_id`=`video`.`video_id`) AS `comment_count`,
			(SELECT ROUND(AVG(`score`),2) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`video`.`video_id`) AS `rate_score`,
			(SELECT COUNT(`id`) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`video`.`video_id`) AS `rate_count`
			FROM `".TBL_PROFILE_VIDEO."` AS `video`
			WHERE `video`.`profile_id`=? 
			AND `video`.`status`='active' AND `video`.`is_converted`='yes' AND `video`.`video_id` != ?
			ORDER BY `video`.`upload_stamp` DESC LIMIT 5", $profile_id, $video_id );
		$res = SK_MySQL::query($query);
		
		$return = array();
		while ($item = $res->fetch_assoc())
		{
			$item['video_page'] = self::getVideoViewURL($item['hash']);
			$item['thumb_img'] = self::getVideoThumbnail($item);
			$return[] = $item;
		}
		return $return;
	}

	public static function getVideoThumbnail( $video, $admin_mode = false )
	{
		$video_mode = SK_Config::section('video')->get('media_mode');
		$viewer = SK_HttpUser::profile_id();
		if ( !app_FriendNetwork::isProfileFriend($video['profile_id'], $viewer) && $viewer != $video['profile_id'] && $video['privacy_status'] == 'friends_only' && !$admin_mode )
			return 'friends_only';
		elseif ( $viewer != $video['profile_id'] && $video['privacy_status'] == 'password_protected' )
		    return 'password_protected';
		elseif( $video['video_source'] == 'file' && $video_mode == 'flash_video' )
			return self::getVideoThumbnailURL($video['hash']);
		elseif (($video['video_source'] == 'file' && $video_mode == 'windows_media') || $video['video_source'] == 'unknown' || $video['is_converted'] == 'no')
			return 'default';
		else
			return self::detectEmbedVideoThumb($video['video_source'],$video['code']);
	}
	
	/**
	 * Returns list of profile's video depending on passed parameter 'status'
	 *
	 * @param integer $profile_id
	 * @param string $status
	 * @return array('list'=>array(),'total'=>integer)
	 */
    public static function getProfileAlbumVideo( $profile_id, $video_list = 'latest' )
	{
		$profile_id = intval( $profile_id );

		if ( !$profile_id )
			return array();

		switch($video_list)
		{
			case 'latest':
				$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_PROFILE_VIDEO."`
					WHERE `profile_id`=? AND `status`='active' AND `is_converted` = 'yes' 
					ORDER BY `upload_stamp` DESC LIMIT 0, 1", $profile_id );
				
				$query_for_thumbs = SK_MySQL::placeholder( "SELECT * FROM `".TBL_PROFILE_VIDEO."`
					WHERE `profile_id`=? AND `status`='active' AND `is_converted` = 'yes' 
					ORDER BY `upload_stamp` DESC LIMIT 1, 3", $profile_id );
				break;
				
			case 'toprated':
				$query = SK_MySQL::placeholder( "SELECT `v`.*, 
					(SELECT ROUND(AVG(`score`),2) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`v`.`video_id`) AS `rate_score`
					FROM `".TBL_PROFILE_VIDEO."` AS `v`
					WHERE `profile_id`=? AND `status`='active' AND `is_converted` = 'yes' 
					ORDER BY `rate_score` DESC LIMIT 0, 1", $profile_id );
				
				$query_for_thumbs = SK_MySQL::placeholder( "SELECT `v`.*, 
					(SELECT ROUND(AVG(`score`),2) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`v`.`video_id`) AS `rate_score`
					FROM `".TBL_PROFILE_VIDEO."` AS `v`
					WHERE `profile_id`=? AND `status`='active' AND `is_converted` = 'yes' 
					ORDER BY `rate_score` DESC LIMIT 1, 3", $profile_id );
				break; 
		}	

		$item = SK_MySQL::query($query)->fetch_assoc();
		$item['video_page'] = app_ProfileVideo::getVideoViewURL($item['hash']);
		if($item['video_source'] == 'file')
			$item['video_url'] =  self::getVideoURL($item['hash'], $item['extension']);
		
		$items = SK_MySQL::query($query_for_thumbs);
		while($video = $items->fetch_assoc())
		{
			if($video['video_source'] == 'file')
				$video['video_url'] =  self::getVideoURL($video['hash'], $video['extension']);
				
			$video['thumb_img'] = self::getVideoThumbnail($video);
			$video['video_page'] = self::getVideoViewURL($video['hash']);
			$list[] = $video;
		}
		
		$ret['for_player'] = $item;
		$ret['for_thumbs'] = $list;

		return $ret;
	}
	
	/**
	 * Adds uploaded video to profile's list of video files.
	 * Returns video_id - identificator of new added video file.
	 *
	 * @param integer $profile_id
	 * @param string $title
	 * @param string $desc
	 * @return integer
	 */
	public static function addVideo( $profile_id, $title, $desc, $privacy_status, $hash, $extension, $is_converted = false, $cat_id = null, $password = null )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return -1;
			
		if ( !strlen( trim( $title ) ) )
			return -2;
			
		if ( !in_array($privacy_status, array('public', 'friends_only', 'password_protected')) )
			$privacy_status = 'public';
		
		//check limits
		$query = SK_MySQL::placeholder( "SELECT COUNT(`video_id`) FROM `".TBL_PROFILE_VIDEO."` 
			WHERE `profile_id`=?", $profile_id );
		
		if ( SK_MySQL::query($query)->fetch_cell() >= SK_Config::section('video')->Section('other_settings')->get('upload_media_files_limit') )
			return -4;
		
		$query = "UPDATE `".TBL_PROFILE."` SET `has_media`='y' WHERE `profile_id`='$profile_id'";
		SK_MySQL::query($query);
		
		$video_status = (SK_Config::section('site')->Section('automode')->get('set_active_video_on_upload')) ? 'active' : 'approval';
		
		$is_converted = $is_converted ? 'yes' : 'no';
		
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_PROFILE_VIDEO."`
			( `profile_id`, `category_id`, `title`, `description`, `status`, `privacy_status`, `extension`, `video_source`, `hash`, `upload_stamp`, `is_converted`, `password`) 
			VALUES( ?, ?, '?', '?', '?', '?', '?', 'file', '?', ?, '?', '?' ) ", 
			$profile_id, $cat_id, $title, $desc, $video_status, $privacy_status, $extension, $hash, time(), $is_converted, $password );
		
		$res = SK_MySQL::query($query);
			
		return SK_MySQL::insert_id();
	}
	
	public static function addEmbeddableVideo( $profile_id, $title, $desc, $privacy_status, $hash, $code, $cat_id = null, $password = null )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return -1;

		$title = trim($title);
		$desc = trim($desc);
		$code = trim($code);	
			
		if ( !strlen($title) )
			return -2;
			
		// TODO: check if embeddable code is valid
			
		if ( !in_array($privacy_status, array('public', 'friends_only', 'password_protected')) )
			$privacy_status = 'public';
		
		//check limits
		$query = SK_MySQL::placeholder( "SELECT COUNT(`video_id`) FROM `".TBL_PROFILE_VIDEO."` 
			WHERE `profile_id`=?", $profile_id );
		
		if ( SK_MySQL::query($query)->fetch_cell() >= SK_Config::section('video')->Section('other_settings')->get('upload_media_files_limit') )
			return -4;
			
		$query = "UPDATE `".TBL_PROFILE."` SET `has_media`='y' WHERE `profile_id`='$profile_id'";
		SK_MySQL::query($query);

		$video_status = (SK_Config::section('site')->Section('automode')->get('set_active_video_on_upload')) ? 'active' : 'approval';
		
		$source = self::detectEmbedVideoSource($code);
		
		//$code = self::addEmbedWmodeParam($code);
		//$code = self::formatEmbedCode($code);
		
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_PROFILE_VIDEO."`
			( `profile_id`, `category_id`, `title`, `description`, `status`, `privacy_status`, `video_source`, `hash`, `upload_stamp`, `code`, `is_converted`, `password`) 
			VALUES( ?, ?, '?', '?', '?', '?', '?', '?', ?, '?', 'yes', '?' ) ", 
			$profile_id, $cat_id, $title, $desc, $video_status, $privacy_status, $source, $hash, time(), $code, $password);
		
		$res = SK_MySQL::query($query);
			
		return SK_MySQL::insert_id();	
	}
	
	
	public static function updateVideo( $hash, $title, $desc, $status, $cat_id = null, $password = null )
	{
		$title = trim($title);
		$desc = trim($desc);
		$status = trim($status);
		
		if (!strlen($title) || !strlen($status) || !strlen($hash))
			return false;

		$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE_VIDEO."` 
            SET `title`='?', `description`='?', `privacy_status`='?', `category_id`=?, `password`='?'
			WHERE `hash`='?'", $title, $desc, $status, $cat_id, $password, $hash);

		SK_MySQL::query($query);
		
		if (SK_MySQL::affected_rows())
			return true;
			
		return false;
	}
	
	
	/**
	 * Returns count of profile video files.
	 *
	 * @param integer $profile_id
	 * @param string $status
	 * @return integer
	 */
	public static function getProfileVideoCount( $profile_id, $status = null )
	{
		if ( !is_numeric($profile_id) || !intval($profile_id) )
			return 0;
		
		$query_ins = isset($status) ? ( (in_array($status, array('approval', 'active', 'suspended'))) ? "AND `status`='".$status."'" : '') : '';
		
		$query = SK_MySQL::placeholder( "SELECT COUNT(`video_id`) FROM `".TBL_PROFILE_VIDEO."` 
			WHERE `profile_id`=? $query_ins", $profile_id );
		
		return intval( SK_MySQL::query($query)->fetch_cell() );
	}
	
	/**
	 * Returns profile_id of video file owner 
	 *
	 * @param string $mediafile_hash
	 * @return integer
	 */
	public static function getVideoOwner( $mediafile_hash )
	{
		if ( !$mediafile_hash )
			return false;
			
		$query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_PROFILE_VIDEO."`
			WHERE `hash`='?' LIMIT 1", $mediafile_hash );
		
		return SK_MySQL::query($query)->fetch_cell();
	}

	
	/**
	 * Returns profile_id of video file owner 
	 *
	 * @param integer $video_id
	 * @return integer
	 */
	public static function getVideoOwnerById( $video_id )
	{
		if ( !$video_id )
			return false;
			
		$query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_PROFILE_VIDEO."`
			WHERE `video_id`=? LIMIT 1", $video_id );
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Returns video info
	 *
	 * @param integer $profile_id
	 * @param string $hash
	 * @return array
	 */
	public static function getVideoInfo( $profile_id, $hash )
	{
		$profile_id = intval($profile_id);
		$hash = trim($hash);
		
		$query = SK_MySQL::placeholder("SELECT `video`.*, 
			(SELECT AVG(`score`) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`video`.`video_id`) AS `rate_score`,
			(SELECT COUNT(`id`) FROM `".TBL_VIDEO_RATE."` WHERE `entity_id`=`video`.`video_id`) AS `rate_count`,
			(SELECT COUNT(`video_id`) FROM `".TBL_PROFILE_VIDEO_VIEW."` WHERE `video_id`=`video`.`video_id`) AS `view_count`
			FROM `".TBL_PROFILE_VIDEO."` AS `video` WHERE `profile_id`=? AND `hash`='?'", $profile_id, $hash );
		
		$res = SK_MySQL::query($query)->fetch_assoc();
		
		return $res;
	}
	
	/** Adds record to video view table
	 * Identifies new viewer by profile_id or by IP (if the third parameter is specified)
	 *
	 * @param integer $video_id
	 * @param integer $profile_id
	 * @param boolean $track_ip
	 * @return boolean
	 */
	public static function updateViews( $video_id, $profile_id, $track_ip = true )
	{
		$video_id = intval($video_id);
		$profile_id = intval($profile_id);
		
		if (!$video_id)
			return false;
		
		if (!$profile_id && !$track_ip )
			return false;

		if ($profile_id)
		{
			$query = SK_MySQL::placeholder("SELECT `video_id` FROM `".TBL_PROFILE_VIDEO_VIEW."`
				WHERE `video_id` = ? AND `viewer_profile_id`=?", $video_id, $profile_id);
			
			if ( !SK_MySQL::query($query)->fetch_cell() )
			{
				$query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_VIDEO_VIEW."`
					( `video_id`, `viewer_profile_id`, `ip` )
					VALUES( ?,?, INET_ATON( '?' ) )", $video_id, $profile_id, SK_HttpRequest::getRequestIP() );
				SK_MySQL::query($query);
			}
		}
		else 
		{
			$query = SK_MySQL::placeholder("SELECT `video_id` FROM `".TBL_PROFILE_VIDEO_VIEW."`
				WHERE `video_id`=? AND `ip`=INET_ATON( '?' )", $video_id, SK_HttpRequest::getRequestIP() );
			if ( !SK_MySQL::query($query)->fetch_cell() )
			{
				$query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_VIDEO_VIEW."`( `video_id`, `ip` )
					VALUES( ?, INET_ATON( '?' ) )", $video_id, SK_HttpRequest::getRequestIP() );
				SK_MySQL::query($query);
			}
		}
		
		return true;
	}

	/**
	 * Returns URL to page with video
	 *
	 * @param string $file_hash
	 * @return string
	 */
	public static function getVideoViewURL( $file_hash )
	{
		if ( !$file_hash )
			return false;
		SK_Navigation::LoadModule('video');
		return SK_Navigation::href('profile_video_view', 'videokey='.$file_hash);
	}
	
	public static function detectEmbedVideoSource($code)
	{
	    $r = new app_VideoResource($code);
	    
	    $res = $r->detectResource();
	    
	    return $res == 'undefined' ? 'unknown' : $res;
	}

	public static function detectEmbedVideoThumb($provider, $code)
	{
	    $r = new app_VideoResource($code);
        
        $thumb = $r->getResourceThumbUrl();
        
        return $thumb == 'unknown' ? 'default' : $thumb;
	}
	
	/**
	 * Return the hash of video file by video_id
	 *
	 * @param integer $video_id
	 * @return string
	 */
	public static function getVideoHash( $video_id )
	{
		$video_id = intval( $video_id );
		
		if ( !$video_id )
			return false;
		
		$query = SK_MySQL::placeholder( "SELECT `hash` FROM `".TBL_PROFILE_VIDEO."`
			WHERE `video_id` =?", $video_id );
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Deletes profile video info from db and file system.
	 *
	 * @param integer $video_id
	 * @return boolean
	 */
	public static function deleteVideo( $video_id )
	{
		if ( !is_numeric( $video_id ) || !intval( $video_id ) )
			return false;
			
		$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_PROFILE_VIDEO."` WHERE `video_id`=?", $video_id );
		$file_info = SK_MySQL::query($query)->fetch_assoc();
		
		// delete video record from database
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_VIDEO."` WHERE `video_id`=?", $video_id );
		SK_MySQL::query($query);
		
		//delete comments
		app_CommentService::stDeleteEntityComments('video', $video_id, ENTITY_TYPE_MEDIA_UPLOAD);
		
		app_TagService::stUnlinkAllTags('video', $video_id);
		
		//delete activity

		app_UserActivities::deleteActivities($video_id, 'media_upload');
		
		app_UserActivities::deleteActivities($video_id, 'video_comment');
		
        //Newsfeed
        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            app_Newsfeed::newInstance()->removeAction(ENTITY_TYPE_MEDIA_UPLOAD, $video_id);
        }             

		//delete views
		self::deleteViewScore($video_id);
		
		if( $file_info['video_source'] == 'file')
		{
			@unlink( self::getVideoDir( $file_info['hash'], $file_info['extension'] ) );
			
			// remove video frame image
			self::deleteVideoFrame($file_info['hash']);
			
			// remove video thumb image
			self::deleteVideoThumb($file_info['hash']);
		}
		
		// check if was last media file with active status
		$query = SK_MySQL::placeholder( "SELECT COUNT(`video_id`) FROM `".TBL_PROFILE_VIDEO."` 
			WHERE `profile_id`=? ", $file_info['profile_id'] );
		
		$media_count = SK_MySQL::query($query)->fetch_cell();
		
		if ( !$media_count )
		{
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_PROFILE."` SET `has_media`='n' 
				WHERE `profile_id`=?", $file_info['profile_id'] );

			SK_MySQL::query($query);		
		}
				
		return true;
	}
	
	/**
	 * Deletes video frame image from db and file system
	 *
	 * @param string $hash
	 * @return boolean
	 */
	private static function deleteVideoFrame( $hash )
	{
		if ( !$hash )
			return false;
		
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_VIDEO_FRAME."`
			WHERE `hash` = '?'", $hash );
		SK_MySQL::query($query);
		
		@unlink( self::getVideoFrameDir($hash) );
		
		return true;
	}
	
	/**
	 * Delete video preview image from file system
	 *
	 * @param string $mediafile_hash
	 * @return boolean
	 */
	private static function deleteVideoThumb( $hash )
	{
		@unlink( self::getVideoThumbnailDir($hash) );
		
		return true;
	}
	
	/**
	 * Remove info about video views
	 *
	 * @param int $video_id
	 */
	private static function deleteViewScore( $video_id )
	{
		$video_id = intval( $video_id );
		if ( !$video_id )
			return false;
			
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_PROFILE_VIDEO_VIEW."`
			WHERE `video_id`=?", $video_id );
		
		SK_MySQL::query($query);
		
		return SK_MySQL::affected_rows();
	}
	
	public static function formatEmbedCode($code, $width, $height)
	{
		$code = self::validateEmbedCode($code);
		
		if (!strlen($code))
			return '';
			
		//adjust width and height
		$code = preg_replace("/width=(\"|')?[\d]+(px)?(\"|')?/i",'width="'.$width.'"', $code);
		$code = preg_replace("/height=(\"|')?[\d]+(px)?(\"|')?/i",'height="'.$height.'"', $code);
		
		$code = preg_replace("/width:?[\d]+(px)?/i",'width: '.$width.'px', $code);
		$code = preg_replace("/height:?[\d]+(px)?/i",'height: '.$height.'px', $code);
				
		//remove YouTube search bar 
		if (preg_match('~http://www.youtube.com/~', $code)) {
			$code = preg_replace('~"http://www.youtube.com/v/([^"]+)"~i', 'http://www.youtube.com/v/$1&rel=0', $code);
		}

		// add wmode param
		$code = self::addEmbedWmodeParam($code);
		
		return $code;
	}
	
	public static function getVideoEmbedCode( $video_id )
	{
		$query = SK_MySQL::placeholder("SELECT `code` FROM `".TBL_PROFILE_VIDEO."` WHERE `video_id`=?", $video_id);
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	public static function addEmbedWmodeParam($code)
	{
		$repl = $code;
		
		if (preg_match("/<object/i", $code))
		{
			$search_pattern = "<param";
			$pos = stripos($code, $search_pattern);//printArr($pos);
			if ($pos) {
				$add_param = '<param name="wmode" value="transparent"></param><param';
				$repl = substr_replace($code, $add_param, $pos, strlen($search_pattern));
			}
		}
		
		if (preg_match("/<embed/i", isset($repl) ? $repl : $code))
			$repl = preg_replace("/<embed/i", '<embed wmode="transparent"', isset($repl) ? $repl : $code);

		return $repl;
	}

	public static function deleteProfileVideos($profile_id)
	{
		$profile_id = intval($profile_id);

		if (!$profile_id)
			return array();
		
		$query = SK_MySQL::placeholder("SELECT `video_id` FROM `".TBL_PROFILE_VIDEO."` WHERE `profile_id`=?", $profile_id);
		$res = SK_MySQL::query($query);

		if( $res->num_rows())
		{
			while ($video = $res->fetch_cell() )
				self::deleteVideo($video);
				
			return true;
		}	
		return false;
	}

	public static function validateEmbedCode($code)
	{
	    $code = preg_replace('#(\n?<script[^>]*?>.*?</script[^>]*?>)|(\n?<script[^>]*?/>)#is', '', $code);
	    
        $objStart = '<object';
        $objEnd = '</object>';
        $objEndS = '/>';

        $posObjStart = stripos($code, $objStart);
        $posObjEnd = stripos($code, $objEnd);

        $posObjEnd = $posObjEnd ? $posObjEnd : stripos($code, $objEndS);

        if ( $posObjStart !== false && $posObjEnd !== false )
        {
            $posObjEnd += strlen($objEnd);
            return substr($code, $posObjStart, $posObjEnd - $posObjStart);
        }
        else
        {
            $embStart = '<embed';
            $embEnd = '</embed>';
            $embEndS = '/>';

            $posEmbStart = stripos($code, $embStart);
            $posEmbEnd = stripos($code, $embEnd) ? stripos($code, $embEnd) : stripos($code, $embEndS);

            if ( $posEmbStart !== false && $posEmbEnd !== false )
            {
                $posEmbEnd += strlen($embEnd);
                return substr($code, $posEmbStart, $posEmbEnd - $posEmbStart);
            }
            else
            {
                $r = new app_VideoResource($code);
                $res = $r->detectResource();

                if ( $res != "unknown" )
                {
                    $frmStart = '<iframe ';
                    $frmEnd = '</iframe>';
                    $posFrmStart = stripos($code, $frmStart);
                    $posFrmEnd = stripos($code, $frmEnd);
                    if ( $posFrmStart !== false && $posFrmEnd !== false )
                    {
                        $posFrmEnd += strlen($frmEnd);
                        return substr($code, $posFrmStart, $posFrmEnd - $posFrmStart);
                    }
                }
                else
                {
                    return '';
                }
            }
        }
	}

    public static function unlockVideo( $hash ) 
    {
        $_SESSION["%unlocked_video%"][] = $hash;
    }
    
    public static function isUnlocked( $hash ) 
    {
        if ( !isset($_SESSION["%unlocked_video%"]) || !is_array($_SESSION["%unlocked_video%"])) 
        {
            return false;
        }
        else {
            return in_array($hash, $_SESSION["%unlocked_video%"]);
        }
    }
}