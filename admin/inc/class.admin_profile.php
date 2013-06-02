<?php
//require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
//require_once( DIR_APP.'app.profile_fields.php' );
//require_once( DIR_APPS.'mail.app.php' );
//require_once( DIR_APP.'app.profile_photo.php' );

/**
 * Project:    SkaDate reloaded
 * File:       class.admin_profile.php
 *
 * @link http://www.skadate.com/
 * @package SkaDate
 * @version 4.0
 */

/**
 * Use methods of this class for working with profile
 *
 * @package SkaDate
 * @since 4.0
 * @author Denis J
 * @link http://www.skalinks.com/ca/forum/ Technical Support forum
 */
class adminProfile
{
	/**
	 * Class constructor
	 *
	 * Define language prefixes
	 */
	public function adminProfile()
	{

	}

	public static function getProfileURL( $profile_id )
	{
		return URL_ADMIN.'profile.php?profile_id='.intval( $profile_id );
	}

	public static function frontendGetProfileURL( $params )
	{
		return URL_ADMIN.'profile.php?profile_id='.intval( $params['profile_id'] );
	}

	public static function getCountPhotos( $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return array();

		$_query = sql_placeholder( "SELECT COUNT(`photo_id`) AS `count`, `status` FROM ".TBL_PROFILE_PHOTO." WHERE `profile_id`=?
			GROUP BY `status`", $profile_id );

		$_info = MySQL::fetchArray( $_query, 'status' );

		$_query = sql_placeholder( "SELECT COUNT(`photo_id`) AS `count` FROM ".TBL_PROFILE_PHOTO."
			WHERE `profile_id`=? ", $profile_id );

		$_info['total'] = MySQL::fetchField( $_query );

		return $_info;
	}

	public static function setReviewed( $profile_id, $reviewed )
	{
		$profile_id = intval( $profile_id );

		if ( !$profile_id )
			return false;

		$reviewed = ( $reviewed == 'y' ) ? 'y' : 'n';

		$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE."` SET `reviewed`='?' WHERE `profile_id`=?", $reviewed, $profile_id);

		SK_MySQL::query($query);
		$result = (bool)SK_MySQL::affected_rows();
		if ( $result && ( $reviewed == 'y' ) && SK_Config::section("site")->Section("additional")->Section("profile")->send_mail_when_reviewed )
		{
			$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
					->setRecipientProfileId($profile_id)
					->setPriority(app_Mail::PRIORITY_HIGHT)
					->setTpl('review_notify');
			app_Mail::send($msg);
		}

        if ($reviewed == 'y')
        {
            app_ProfileField::clearMarkedProfileFields($profile_id);
        }

		return $result;
	}

	public static function setProfileStatus( $profile_id, $status )
	{
		$profile_id = intval( $profile_id );

		if ( !$profile_id )
			return false;

		$query = SK_MySQL::placeholder( "UPDATE `".TBL_PROFILE."` SET `status`='?'
			WHERE `profile_id`=?", $status, $profile_id );

		SK_MySQL::query($query);

		return SK_MySQL::affected_rows();
	}

	public static function setPhotoStatus( $photo_id, $status )
	{
		if ( !is_numeric( $photo_id ) || !intval( $photo_id ) )
			return false;

		if ( !in_array( $status, array( 'active', 'approval', 'suspended' ) ) )
			return false;



		$_query = "UPDATE `".TBL_PROFILE_PHOTO."` SET `status`='$status'
			WHERE `photo_id`='$photo_id'";
		MySQL::fetchResource( $_query );
		//update profile has_photo status
		app_ProfilePhoto::updateHasPhotoStatus(app_ProfilePhoto::photoOwnerId($photo_id));
		return true;
	}

	public static function updateProfileHasVideoStatus( $profile_id )
	{
		$profile_id = intval( $profile_id );
		if( !$profile_id )
			return  false;

		$_query = "SELECT COUNT(*) FROM `".TBL_PROFILE_VIDEO."` WHERE `status`='active' AND `profile_id`=$profile_id";
		$_has_video = intval(MySQL::fetchField( $_query ))? 'y' : 'n';

		$_query = sql_placeholder( "UPDATE `".TBL_PROFILE."` SET `has_media`=? WHERE `profile_id`=?",$_has_video, $profile_id );
		MySQL::fetchResource( $_query );
		return true;
	}

	public static function getCountMedia( $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return array();

		$_query = sql_placeholder( "SELECT COUNT(`video_id`) AS `count`, `status` FROM ".TBL_PROFILE_VIDEO."
			WHERE `profile_id`=? AND `is_converted`='yes' GROUP BY `status`", $profile_id );

		$_info = MySQL::fetchArray( $_query, 'status' );

		$_query = sql_placeholder( "SELECT COUNT(`video_id`) AS `count` FROM ".TBL_PROFILE_VIDEO."
			WHERE `profile_id`=? AND `is_converted`='yes' ", $profile_id );

		$_info['total'] = MySQL::fetchField( $_query );

		return $_info;
	}
        public static function getCountMusic( $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return array();

		$_query = sql_placeholder( "SELECT COUNT(`music_id`) AS `count`, `status` FROM ".TBL_PROFILE_MUSIC."
			WHERE `profile_id`=?  GROUP BY `status`", $profile_id );

		$_info = MySQL::fetchArray( $_query, 'status' );

		$_query = sql_placeholder( "SELECT COUNT(`music_id`) AS `count` FROM ".TBL_PROFILE_MUSIC."
			WHERE `profile_id`=?", $profile_id );

		$_info['total'] = MySQL::fetchField( $_query );

		return $_info;
	}

	public static function setMediaStatus( $media_id, $status )
	{
		$media_id = intval( $media_id );

		if ( !is_numeric( $media_id ) )
			return false;

		if ( !in_array( $status, array( 'active', 'approval', 'suspended' ) ) )
			return false;

		$_query = "UPDATE `".TBL_PROFILE_VIDEO."` SET `status`='$status' WHERE `video_id`='$media_id'";
		MySQL::fetchResource( $_query );

		return true;
	}
        public static function setMusicStatus( $media_id, $status )
	{
		$media_id = intval( $media_id );

		if ( !is_numeric( $media_id ) )
			return false;

		if ( !in_array( $status, array( 'active', 'approval', 'suspended' ) ) )
			return false;

		$_query = "UPDATE `".TBL_PROFILE_MUSIC."` SET `status`='$status' WHERE `music_id`='$media_id'";
		MySQL::fetchResource( $_query );

		return true;
	}

	public static function blockProfileIP( $profile_id )
	{
		$profile_id = intval( $profile_id );

		if ( !$profile_id )
			return -1;

		$_query = sql_placeholder( "SELECT `join_ip` FROM `".TBL_PROFILE."`
			WHERE `profile_id`=?", $profile_id );

		$_profile_ip = MySQL::fetchField( $_query );

		if ( !$_profile_ip )
			return -2;

		$_query = sql_placeholder( "SELECT `block_ip` FROM `".TBL_BLOCK_IP."`
			WHERE `block_ip`=?", $_profile_ip );

		if ( MySQL::fetchField( $_query ) )
			return -3;

		$_query = sql_placeholder( "INSERT INTO `".TBL_BLOCK_IP."`(`block_ip`)
			VALUES(?)", $_profile_ip );

		MySQL::fetchResource( $_query );

		return 1;
	}

	public static function getPhotos( $profile_id, $include_thumb = false, $include_private = true, $status = null )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return array();

		$_query_cond = ( in_array( $status, array( 'approval', 'active', 'suspended' ) ) ) ? "AND `status`='$status'" : '';

		$_thumb_cond = ( !$include_thumb ) ? "AND `number`!=0" : '';

		$_query = sql_placeholder( "SELECT * FROM `?#TBL_PROFILE_PHOTO`
			WHERE `profile_id`=? $_thumb_cond $_query_cond
			ORDER BY `number`", $profile_id);

		$_result = MySQL::fetchResource( $_query );

		$_photos = array();

		while( $_row = MySQL::resource2Assoc( $_result ) )
		{
			// Check private status
			$_image_flag = '';

			$_image_url = ( $_row['number'] ) ? app_ProfilePhoto::getUrl($_row['photo_id'], app_ProfilePhoto::PHOTOTYPE_PREVIEW ) : app_ProfilePhoto::getUrl($_row['photo_id'], app_ProfilePhoto::PHOTOTYPE_THUMB );

			$_original_photo_url = ( $_row['number'] ) ? app_ProfilePhoto::getUrl($_row['photo_id'], app_ProfilePhoto::PHOTOTYPE_ORIGINAL ) : false;

			$_view_photo_url = ( $_row['number'] ) ? app_ProfilePhoto::getUrl($_row['photo_id'], app_ProfilePhoto::PHOTOTYPE_VIEW ) : false;

			// Append photo
			$_photos[$_row['photo_id']] = array
			(
				'url'		=> $_image_url,
				'flag'		=> $_image_flag,
				'number'	=> $_row['number'],
				'status'	=> $_row['status'],
				'publishing_status' => $_row['publishing_status'],
			    'authed' => (bool) $_row['authed'],
				'original_photo_url' => $_original_photo_url,
			    'view_photo_url' => $_view_photo_url
			);
		}

		if( !$_photos )
		{
			$_profile_sex = MySQL::FetchField( "SELECT `sex` FROM `".TBL_PROFILE."` WHERE `profile_id`='$profile_id'" );
			$_photos[0] = array
			(
				'url'	=>	app_ProfilePhoto::defaultPhotoUrl( $_profile_sex, app_ProfilePhoto::PHOTOTYPE_PREVIEW ),
				'flag'	=>	'no_photos',
			);
		}

		return $_photos;
	}

	public static function getAdminNotes($profile_id) {
		if(!($profile_id = intval($profile_id))) return array();
		$query = SK_MySQL::placeholder('SELECT * FROM `'.TBL_ADMIN_NOTES.'` WHERE `profile_id`=? ORDER BY `note_date` ASC',$profile_id);
		$result = SK_MySQL::query($query );
		$notes = array();
		while ($item = $result->fetch_object()) {
			$notes[] = array(
				'date' 	=> date('m.d.y',$item->note_date),
				'note'	=> $item->note_body,
				'note_id'	=> $item->note_id,
			);

		}
		return $notes;
	}

	public static function setSiteModeratir($profile_id, $flag = true) {

		if (!($profile_id = intval($profile_id))) {
			return false;
		}

		if ($flag) {
			$query = SK_MySQL::placeholder("INSERT INTO `" . TBL_SITE_MODERATORS . "` VALUES(?, ?)", $profile_id, time());
		} else {
			$query = SK_MySQL::placeholder("DELETE FROM `" . TBL_SITE_MODERATORS . "` WHERE `profile_id`=?", $profile_id);
		}

		SK_MySQL::query($query);
		return (bool) SK_MySQL::affected_rows();
	}

	public static function isSiteModerator($profile_id) {
		$query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `" . TBL_SITE_MODERATORS . "` WHERE `profile_id`=?", $profile_id);
		return (bool)SK_MySQL::query($query)->fetch_cell();
	}

	/**
	 * Get profile mailbox messages.
	 *
	 * @param integer $profile_id
	 * @param string $type
	 * @return array
	 */
	public static function getMailboxConversations( $profile_id, $page )
	{
		if ( !intval( $profile_id ) )
			return array();

		$page = ( isset($page) && intval($page) ) ? $page : 1;
		$res_per_page = SK_Config::Section('site')->Section('additional')->Section('mailbox')->mails_per_page;
		$limit_q = " LIMIT ".( ( $page - 1 ) * $res_per_page ).",$res_per_page";

		$query = "SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `mails_count`, `m`.`sender_id`, `m`.`recipient_id`,
			`m`.`text`, `m`.`time_stamp` AS `last_message_ts`, `c`.`conversation_id`, `c`.*, `c`.`conversation_ts`, `c`.`is_system`
			FROM `".TBL_MAILBOX_CONVERSATION."` AS `c`
			INNER JOIN (
				SELECT * FROM `".TBL_MAILBOX_MESSAGE."`
				WHERE `sender_id`=$profile_id OR `recipient_id`=$profile_id ORDER BY `time_stamp` DESC
			) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`)
			INNER JOIN (
				SELECT `conversation_id`, IF(`sender_id`=$profile_id,'yes','no') AS `is_replied` FROM `".TBL_MAILBOX_MESSAGE."`
				WHERE (`recipient_id`=$profile_id OR `sender_id`=$profile_id) ORDER BY `time_stamp` DESC
			) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`)
			WHERE (`c`.`initiator_id`=$profile_id OR `interlocutor_id`=$profile_id)
			GROUP BY `c`.`conversation_id`
			ORDER BY MAX( `m`.`time_stamp` ) DESC $limit_q";

		$result = SK_MySQL::query( $query );

		$conv = array();
		while ( $conversation = $result->fetch_assoc() ) {
			$conversation['profile_href'] = SK_Navigation::href('profile', array('profile_id'=> $conversation['sender_id']));
			$conversation['username'] = app_Profile::getFieldValues( $conversation['initiator_id'], 'username' );
			$conversation['recipient'] = app_Profile::getFieldValues( $conversation['interlocutor_id'], 'username' );
			$conversation['mails_count'] = intval($conversation['mails_count']);
			$conversation['mails'] = self::getConversationMessages($conversation['conversation_id']);
			$conversation['text'] = trim(strip_tags($conversation['text'], '<a>'));
            $conversation['text'] = app_VirtualGift::replaceGiftImagePattern($conversation['text']);

			if ($conversation['is_system'] == 'no')
				$conversation['thumb'] = app_ProfilePhoto::getThumbURL( $conversation['initiator_id'] , false);

			$conv[] = $conversation;
		}

		$count = self::countMailboxConversations($profile_id);

		return array('conversations' => $conv, 'total' => $count);
	}

	public static function countMailboxConversations($profile_id)
	{
		// count all messages
		$query = "SELECT COUNT(`m`.`message_id`) AS `mails_count`
			FROM `".TBL_MAILBOX_CONVERSATION."` AS `c`
			INNER JOIN (
				SELECT * FROM `".TBL_MAILBOX_MESSAGE."`
				WHERE `sender_id`=$profile_id OR `recipient_id`=$profile_id ORDER BY `time_stamp` DESC
			) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`)
			WHERE (`c`.`initiator_id`=$profile_id OR `interlocutor_id`=$profile_id)
			GROUP BY `c`.`conversation_id`";

		$result = SK_MySQL::query($query);

		$count = 0;
		while ( $conv_mails = $result->fetch_assoc() ) {
			$count++;
		}
		return $count;
	}

	/**
	 * Get all messages in specified mailbox conversation
	 *
	 * @param integer $conv_id
	 * @return array
	 */
	public static function getConversationMessages( $conv_id )
	{
		if ( !intval( $conv_id ) )
			return -1;

		$query = SK_MySQL::placeholder( "SELECT `m`.*, `c`.`is_system`, `p`.`username` FROM `".TBL_MAILBOX_MESSAGE."` AS `m`
			LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`m`.`sender_id`=`p`.`profile_id`)
			LEFT JOIN `".TBL_MAILBOX_CONVERSATION."` AS `c` ON(`m`.`conversation_id`=`c`.`conversation_id`)
			WHERE `m`.`conversation_id`=?
			ORDER BY `time_stamp` ASC", $conv_id );

		$result = SK_MySQL::query( $query );

		$msg_arr = array();
		while ( $message = $result->fetch_assoc() ) {
			if ($message['is_system'] == 'no')
				$message['thumb'] = app_ProfilePhoto::getThumbURL($message['sender_id'], false);

			$message['text'] = app_VirtualGift::replaceGiftImagePattern($message['text']);
			$msg_arr[] = $message;
		}

		return $msg_arr;
	}
        
        /**
         * Add or remover profile from Hot list
         * 
         * @param integer $profile_id 
         */
        public static function managerHotList( $profile_id, $flag )
        {
            if ( $flag )
            {
                return app_HotList::addProfile( $profile_id );
            }
            else
            {
                return app_HotList::removeProfile( $profile_id );
            }
        }        
}


?>