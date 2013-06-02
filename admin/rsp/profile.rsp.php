<?php

require_once( '../../internals/Header.inc.php' );

require_once( DIR_ADMIN_INC . 'class.admin_profile.php' );
require_once( DIR_ADMIN_INC . 'fnc.auth.php' );
require_once( DIR_ADMIN_INC . 'fnc.blocked_ip.php' );



rsp_Profile::construct();

class rsp_Profile
{
    private static $response = array();

    public static function construct()
    {
        $apply_function = (string) $_GET["apply_func"];
        if ( !strlen($apply_function) )        {
            return;
        }
        if ( !isAdminAuthed(false) )        {
            self::exec("alert('No changes made. Demo mode.')");
        }        else        {
            $result = call_user_func(array(__CLASS__, $apply_function), $_POST);

            self::$response["result"] = $result;
        }

        echo json_encode(self::$response);
    }

    private static function exec( $script )    {
        self::$response["script"] .= $script . ";\n";
    }

    private static function alert( $msg )    {
        self::exec("alert('" . $msg . "')");
    }

    public static function changeProfileStatus( $params = null )
    {
        if ( !isAdminAuthed(false) )
        {
            self::alert('No changes made. Demo mode.');
            return;
        }

        $profile_id = intval($params['profile_id']);
        $status = trim($params['status']);

        if ( !$profile_id )        {
            self::alert('Undefined profile ID');
            return false;
        }

        if ( !in_array($status, array('active', 'on_hold', 'suspended')) )
        {
            self::alert('Undefined profile status');
            return false;
        }

        /* $_response->addAssign( 'main_label_profile_status', 'innerHTML', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$status );
          $_response->addAssign( 'main_label_profile_status', 'className', 'label_profile_status_'.$status );

          $_response->addAssign( 'profile_status', 'className', 'profile_select_status_'.$status );
         */
        $changed = adminProfile::setProfileStatus($profile_id, $status);

        $aList = app_UserActivities::getWhere(" `type`='profile_comment' and `item` = {$profile_id} ");

        foreach ( $aList as $a )
        {
            if ( $status == 'active' )
                app_UserActivities::setStatus($a['skadate_user_activity_id'], 'active');
            else
                app_UserActivities::setStatus($a['skadate_user_activity_id'], 'approval');
        }

        if ( $changed )        {
            return true;
        }
    }

    public static function setEmailVerified( $params = null )
    {
        $profile_id = intval($params['profile_id']);
        $email_verified = trim($params["status"]);

        if ( !$profile_id )
        {
            self::alert('Undefined profile ID');
            return false;
        }

        if ( !in_array($email_verified, array('yes', 'no', 'undefined')) )
        {
            self::alert('Undefined EmailVerified status');
            return false;
        }

        //$_response->addAssign( 'profile_email_verified', 'className', 'profile_email_verified_'.$email_verified );

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE . "` SET `email_verified`='?'
			WHERE `profile_id`=?", $email_verified, $profile_id);
        SK_MySQL::query($query);

        if ( SK_MySQL::affected_rows() )        {
            return true;
        }        else        {
            self::alert('Profile EmailVerified status not changed');
            return false;
        }

    }

    public static function setReviewedStatus( $params = null )
    {
        $profile_id = intval($params['profile_id']);
        $reviewed = trim($params["status"]);

        if ( !$profile_id )
        {
            self::alert('Undefined profile ID');
            return false;
        }

        if ( !in_array($reviewed, array('y', 'n')) )
        {
            self::alert('Undefined `Reviewed` status');
            return false;
        }


        $query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE . "` SET `reviewed`='?'
			WHERE `profile_id`=?", $reviewed, $profile_id);
        SK_MySQL::query($query);
        if ( !SK_MySQL::affected_rows() )        {
            self::alert('Profile `Reviewed` status not changed');
            return false;
        }

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $status = ( $reviewed == 'y' ) ? 'active' : 'approval';
            $newsfeedAction = app_Newsfeed::newInstance()->getAction('profile_join', $profile_id);
            if ( !empty($newsfeedAction) )
            {
                $newsfeedAction->setStatus($status);
                app_Newsfeed::newInstance()->saveAction($newsfeedAction);
            }

            $newsfeedAction = app_Newsfeed::newInstance()->getAction('profile_edit', $profile_id);
            if ( !empty($newsfeedAction) )
            {
                $newsfeedAction->setStatus($status);
                app_Newsfeed::newInstance()->saveAction($newsfeedAction);
            }
            
            $newsfeedAction = app_Newsfeed::newInstance()->getAction('profile_avatar_change', $profile_id);
            if ( !empty($newsfeedAction) )
            {
                $newsfeedAction->setStatus($status);
                app_Newsfeed::newInstance()->saveAction($newsfeedAction);
            }
        }

        if ( $reviewed == 'y' )
        {
            app_ProfileField::clearMarkedProfileFields($profile_id);

            if ( SK_Config::section("site")->Section("additional")->Section("profile")->send_mail_when_reviewed )
            {
                $msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
                        ->setRecipientProfileId($profile_id)
                        ->setTpl('review_notify')
                        ->setPriority(app_Mail::PRIORITY_HIGHT);
                app_Mail::send($msg);
            }
        }
        return true;
    }

    function markAsFeatured( $params = null )
    {
        $profile_id = intval($params['profile_id']);
        $mark = trim($params["status"]);

        if ( !$profile_id )
        {
            self::alert('Undefined profile ID');
            return false;
        }

        $mark = ( $mark == 'true' ) ? 'y' : 'n';

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE . "` SET `featured`='?' WHERE `profile_id`=?", $mark, $profile_id);
        SK_MySQL::query($query);

        return true;
    }

    public static function addAdminNote( $params = null )
    {

        $note = trim($params["note"]);
        $profile_id = intval($params["profile_id"]);
        if ( !$note )        {
            return false;
        }

        $time_stamp = time();

        $date = date('m.d.y', $time_stamp);
        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_ADMIN_NOTES . "` VALUES(null,?,'?',?)", $profile_id, $note, $time_stamp);
        SK_MySQL::query($query);

        if ( $note_id = SK_MySQL::insert_id() )        {
            return array(
                'note_id' => $note_id,
                'note' => $note,
                'date' => $date
            );

        }        else        {
            self::alert('Save note faild!');
            return false;
        }
    }

    public static function deleteAdminNote( $params = null )    {
        $note = trim($params["note_id"]);
        $profile_id = intval($params["profile_id"]);
        if ( !$note )        {
            return false;
        }
        $query = SK_MySQL::placeholder('DELETE FROM `' . TBL_ADMIN_NOTES . '` WHERE `note_id`=?', $note);
        SK_MySQL::query($query);
        if ( !SK_MySQL::affected_rows() )        {
            self::alert('Delete note is failed!');
            return false;
        }
        return true;
    }


    public static function getPhotoInfo( $params=null )
    {
        $profile_id = intval($params['profile_id']);

        if ( !$profile_id )
        {
            self::alert('Undefined profile ID');
            return false;
        }

        $photo_count_info = adminProfile::getCountPhotos($profile_id);

        return array(
            "count" => $photo_count_info,
            "thumb_url" => app_ProfilePhoto::getThumbUrl($profile_id, false)
        );
        /*

          $_response->addAssign( 'profile_photo_link_total', 'innerHTML', '<a href="javascript://" onclick="showPhotoContainer( \''.$profile_id.'\' );" class="label_profile_total_photo">View photos (<b>'.$_photo_count_info['total'].'</b>)</a>' );

          if ( $_photo_count_info['active']['count'] )
          $_div_content = '<a href="javascript://" onclick="showPhotoContainer( \''.$profile_id.'\', \'active\' );" class="label_profile_active_photo"><b>active ('.$_photo_count_info['active']['count'].'</b>)</a>';
          else
          $_div_content = '';

          $_response->addAssign( 'profile_photo_link_active', 'innerHTML', $_div_content );

          if ( $_photo_count_info['approval']['count'] )
          $_div_content = '<a href="javascript://" onclick="showPhotoContainer( \''.$profile_id.'\', \'approval\' );" class="label_profile_approval_photo"><b>approval ('.$_photo_count_info['approval']['count'].'</b>)</a>';
          else
          $_div_content = '';

          $_response->addAssign( 'profile_photo_link_approval', 'innerHTML', $_div_content );

          if ( $_photo_count_info['suspended']['count'] )
          $_div_content = '<a href="javascript://" onclick="showPhotoContainer( \''.$profile_id.'\', \'suspended\' );" class="label_profile_suspended_photo"><b>suspended ('.$_photo_count_info['suspended']['count'].'</b>)</a>';
          else
          $_div_content = '';

          $_response->addAssign( 'profile_photo_link_suspended', 'innerHTML', $_div_content );

          $_response->addAssign( 'profile_thumb_content', 'innerHTML', '<img src="'.appProfilePhoto::getThumbnailURL( $profile_id ).'" />' );

          return $_response; */
    }

    public static function sendMessage( $params = null )
    {
        $profile_id = intval($params["profile_id"]);
        $subject = $params["subject"];
        $message = $params["message"];
        $ignore_unsubscribe = $params["ignore_unsubscribe"];

        if ( !$profile_id )
        {
            self::alert('Undefined profile ID');
            return false;
        }


        if ( app_Unsubscribe::isProfileUnsubscribed($profile_id) && $ignore_unsubscribe != 'true' )
        {
            self::alert('Profile has unsubscribed! ');
            return false;
        }

        if ( !strlen(trim($subject)) )
        {
            self::alert('Missing message subject');
            return false;
        }

        if ( !strlen(trim($message)) )
        {
            self::alert('Missing message text');
            return false;
        }

        $msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE);
        $msg->setRecipientProfileId($profile_id)
            ->setSenderEmail(SK_Config::section('site.official')->site_email_main)
            ->setSubject($subject)
            ->setContent($message)
            ->setPriority(app_Mail::PRIORITY_HIGHT)
            ->assignVar('unsubscribe_url', app_Unsubscribe::getUnsubscribeLink($profile_id));
        $action_send = app_Mail::send($msg);

        if ( $action_send )
        {
            self::exec('$jq("#send_msg_info").html(\'<div class="page_message">Message was sent</div>\')');
            self::exec('$jq("#msg_subject").val("")');
            self::exec('$jq("#msg_text").val("")');
            return true;
        }
        else        {
            self::exec('$jq("#send_msg_info").html(\'<div class="page_error">Error! The message was not sent</div>\')');
            return false;
        }


    }

    public static function blockIP( $params = null )
    {
        $ip = $params["ip"];
        if ( searchBlockedIp($ip) )
        {
            deleteBlockedIp($ip);
            self::alert('Profile Ip was Unblocked.');
            self::exec('$jq("#block_ip").attr("className", "block_profile_ip")');
            self::exec('$jq("#block_ip").attr("title", "block profile IP")');
        }
        else
        {
            addBlockedIp($ip);
            self::alert('Profile IP was Blocked');
            self::exec('$jq("#block_ip").attr("className", "unblock_profile_ip")');
            self::exec('$jq("#block_ip").attr("title", "unblock profile IP")');
        }

        return true;
    }

    public static function setProfileUnsubscribed( $params = null )
    {
        $unsubscribed = ($params["unsubscribe"] == 'true') ? true : false;

        if ( !($profile_id = intval($params['profile_id'])) )        {
            self::alert('Undefined profile!');
            return false;
        }

        if ( app_Unsubscribe::setUnsubscribe($profile_id, $unsubscribed) )
        {
            return true;
        }
        else        {
            self::alert('Unsubscribe status has not changed!');
            self::exec('$jq("#unsubscribed_chkbox").attr("checked", false)');
        }


        return true;
    }

    public static function setSiteModerator( $params = null )    {
        $flag = json_decode($params['flag']);
        return adminProfile::setSiteModeratir($params['profile_id'], $flag);
    }

    public static function getEmbedCode( $params = null )    {
        $video_id = json_decode($params['video_id']);
     		$code = app_ProfileVideo::getVideoEmbedCode($video_id);
		return app_ProfileVideo::validateEmbedCode($code);
    }
    public static function getMusicEmbedCode( $params = null )    {
        $music_id = json_decode($params['music_id']);
        return app_ProfileMusic::getMusicEmbedCode($music_id);
    }

    public static function updateVideoInfo( $params = null )    {
        $profile_id = json_decode($params['profile_id']);
        return adminProfile::getCountMedia($profile_id);
    }
    public static function updateMusicInfo( $params = null )    {
        $profile_id = json_decode($params['profile_id']);
        return adminProfile::getCountMusic($profile_id);
    }

    public static function authenticateAdminAsProfile( $params = null )
    {
        $query = SK_MySQL::placeholder(
			"SELECT `username`,`password`
				FROM `".TBL_PROFILE."`
				WHERE `profile_id`=?", $params['profile_id']
		);
        
        $details = SK_MySQL::query($query)->fetch_assoc();

        SK_HttpUser::authenticate($details['username'], $details['password'], false);
        return array('profile_id'=>$params['profile_id'], 'href'=>  app_Profile::getUrl($params['profile_id']));
    }
    
    public static function managerHotList( $params = null )
    {
        return adminProfile::managerHotList($params['profile_id'], json_decode($params['flag']));
    }

}
