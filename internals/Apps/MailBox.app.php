<?php

/**
 * Class for internal profile mailbox
 *
 * @package SkaDate
 * @link http://www.skadate.com
 * @version 7.0
 */
class app_MailBox
{
    const INITIATOR_FLAG = 1;
    const INTERLOCUTOR_FLAG = 2;

    private static $newMessageCountCache = array();

    /**
     * Get list of profile mailbox conversations.
     *
     * @param integer $profile_id
     * @param string $type
     * @return array
     */
    public static function getProfileConversations( $profile_id, $type, $page = 1 )
    {
        if ( !intval( $profile_id ) )
            return array();

        $res_per_page = SK_Config::Section('site')->Section('additional')->Section('mailbox')->mails_per_page;
        $limit_q = " LIMIT ".( ( $page - 1 ) * $res_per_page ).",$res_per_page";

        switch ($type) {
            case 'inbox':
                $owner_field = 'recipient_id';
                break;
            case 'sentbox':
                $owner_field = 'sender_id';
                break;
            default:
                $owner_field = 'recipient_id';
        }

        // check config for filter condition
        $filter_cond = SK_Config::section('mailbox')->section('spam_filter')->mailbox_message_filter ?  " AND IF (`sender_id`!=$profile_id, `status`='a', 1)" : '';

        $query = "SELECT `m`.`message_id`, COUNT(`m`.`message_id`) AS `mails_count`, `m`.`sender_id`, `m`.`recipient_id`, `m`.`is_kiss`,
			`m`.`text`, `m`.`is_readable`, `m`.`time_stamp` AS `last_message_ts`, `c`.`conversation_id`, `c`.*, `ms`.`is_replied`
			FROM `".TBL_MAILBOX_CONVERSATION."` AS `c` 
			INNER JOIN (
				SELECT * FROM `".TBL_MAILBOX_MESSAGE."` 
				WHERE `$owner_field`=$profile_id $filter_cond ORDER BY `time_stamp` DESC  
			) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`) 
			INNER JOIN (
				SELECT `conversation_id`, IF(`sender_id`=$profile_id,'yes','no') AS `is_replied` FROM `".TBL_MAILBOX_MESSAGE."` 
				WHERE (`recipient_id`=$profile_id OR `sender_id`=$profile_id) ORDER BY `time_stamp` DESC 
			) AS `ms` ON(`ms`.`conversation_id`=`c`.`conversation_id`)
			WHERE (`c`.`initiator_id`=$profile_id OR `interlocutor_id`=$profile_id)
			AND `c`.`bm_deleted` NOT IN (IF(`c`.`initiator_id`=$profile_id, '".self::getBitMaskValues(self::INITIATOR_FLAG)."','".self::getBitMaskValues(self::INTERLOCUTOR_FLAG)."'))			
			$filter_cond
			GROUP BY `c`.`conversation_id`
			ORDER BY `m`.`time_stamp` DESC $limit_q";

        $result = SK_MySQL::query( $query );

        $conv = array();
        $valueList = array();
        $profileIdList = array();
        $templateList = array();

        while ( $conversation = $result->fetch_assoc() )
        {
            $valueList[] = $conversation;
            if ( !empty($conversation['initiator_id']) )
            {
                $profileIdList[$conversation['initiator_id']] = $conversation['initiator_id'];
            }

            if ( !empty($conversation['interlocutor_id']) )
            {
                $profileIdList[$conversation['interlocutor_id']] = $conversation['interlocutor_id'];
            }

            app_VirtualGift::replaceGiftImagePattern($conversation['text']);

            if ( preg_match("/\[gift\](\d+)\[\/gift\]/", $conversation['text'], $match) )
            {
                if ( isset($match[0]) && isset($match[1]) )
                {
                    $templateList[] = $match[1];
                }
            }
        }

        app_Profile::getUsernamesForUsers($profileIdList);
        app_VirtualGift::getGiftTemplateList($templateList);

        foreach ( $valueList as $conversation )
        {
            $conversation['profile_href'] = SK_Navigation::href('profile', array('profile_id'=> $conversation['sender_id']));
            $conversation['conv_href'] = SK_Navigation::href('mailbox_conversation', array('conv_id'=> $conversation['conversation_id']));
            $username = ($type == 'inbox') ? 'sender_id' : 'recipient_id';
            $conversation['username'] = app_Profile::username($conversation[$username]);
            $mask_arr = self::getBitMaskValues( $conversation['initiator_id'] == $profile_id ? self::INITIATOR_FLAG : self::INTERLOCUTOR_FLAG, false);
            $conversation['new_msg'] = in_array($conversation['bm_read'], $mask_arr)? 0 : 1;
            $conversation['mails_count'] = intval($conversation['mails_count']);
            $conversation['text'] = trim(strip_tags($conversation['text'], '<a>'));
            $conversation['text'] = app_VirtualGift::replaceGiftImagePattern($conversation['text']);
            $conv[] = $conversation;
        }

        // count all messages
        $query = "SELECT COUNT(`m`.`message_id`) AS `mails_count`
			FROM `".TBL_MAILBOX_CONVERSATION."` AS `c`
			INNER JOIN (
				SELECT * FROM `".TBL_MAILBOX_MESSAGE."` 
				WHERE `$owner_field`=$profile_id $filter_cond ORDER BY `time_stamp` DESC  
			) AS `m` ON(`m`.`conversation_id`=`c`.`conversation_id`)
			WHERE (`c`.`initiator_id`=$profile_id OR `interlocutor_id`=$profile_id)
			AND `c`.`bm_deleted` NOT IN (IF(`c`.`initiator_id`=$profile_id, '".self::getBitMaskValues(self::INITIATOR_FLAG)."','".self::getBitMaskValues(self::INTERLOCUTOR_FLAG)."'))			
			GROUP BY `c`.`conversation_id`";

        $result = SK_MySQL::query($query);

        $count = 0;
        while ( $conv_mails = $result->fetch_assoc() )
        {
            if ($conv_mails['mails_count'] < 1 && $type == 'sentbox' ) {
            } else
                $count++;
        }

        return array('conversations' => $conv, 'total' => $count);
    }

    public static function conversationIsRead( $conv_id, $profile_id )
    {
        $conv_info = self::getConversation($conv_id);

        if ( !is_array($conv_info) )
        {
            return false;
        }

        $mask_arr = self::getBitMaskValues($conv_info['initiator_id'] == $profile_id ? self::INITIATOR_FLAG : self::INTERLOCUTOR_FLAG, false);
        $is_read = in_array($conv_info['bm_read_special'], $mask_arr) ? 1 : 0;

        return (bool)$is_read;
    }


    /**
     * Get all messages in specified mailbox conversation
     *
     * @param integer $conv_id
     * @return array
     */
    public static function getConversationMessages( $conv_id, $profile_id )
    {
        if ( !intval( $conv_id ) || !$profile_id )
            return -1;

        // check if conversation is alien
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MAILBOX_CONVERSATION."` AS `c`
			LEFT JOIN `".TBL_MAILBOX_MESSAGE."` AS `m` USING (`conversation_id`)
			WHERE `c`.`conversation_id`=? AND (`m`.`sender_id`=? OR `m`.`recipient_id`=?)", $conv_id, $profile_id, $profile_id);
        $conv = SK_MySQL::query($query)->fetch_assoc();

        if (!SK_MySQL::query($query)->fetch_cell())
            return -2;

        // check config for filter condition
        $filter_cond = SK_Config::section('mailbox')->section('spam_filter')->mailbox_message_filter ?  " AND IF (`m`.`sender_id`!=$profile_id, `m`.`status`='a', 1)" : '';

        $query = SK_MySQL::placeholder( "SELECT `m`.*, `p`.`username` FROM `".TBL_MAILBOX_MESSAGE."` AS `m`
			LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`m`.`sender_id`=`p`.`profile_id`) 
			WHERE `conversation_id`=? $filter_cond 
			ORDER BY `time_stamp` ASC", $conv_id );
        $result = SK_MySQL::query( $query );

        $msg_arr = array();
        $msg = 1;

        $valueList = array();
        $profileIdList = array();
        $templateList = array();
        while ( $message = $result->fetch_assoc() )
        {
            $valueList[] = $message;
            $profileIdList[$message['sender_id']] = $message['sender_id'];
            $profileIdList[$message['recipient_id']] = $message['recipient_id'];

            if ( preg_match("/\[gift\](\d+)\[\/gift\]/", $message['text'], $match) )
            {
                if ( isset($match[0]) && isset($match[1]) )
                {
                    $templateList[] = $match[1];
                }
            }
        }

        app_Profile::getUsernamesForUsers($profileIdList);
        app_VirtualGift::getGiftTemplateList($templateList);

        foreach( $valueList as $message )
        {
            if ( $msg == 1)
            {
                $message['text'] = app_VirtualGift::replaceGiftImagePattern($message['text']);
            }

            $message['username'] = !empty($message['username']) ? app_Profile::username($message['sender_id']) : null;

            $msg_arr[] = $message;
        }

        $query = SK_MySQL::placeholder( "SELECT `c`.*, `s`.`username` as `initiator_username`,
			`r`.`username` as `interlocutor_username`, 
			IF(`s`.`profile_id`=".$profile_id.",`r`.`username`,`s`.`username`) AS `opponent`,
			IF(`s`.`profile_id`=".$profile_id.",`r`.`profile_id`,`s`.`profile_id`) AS `opponent_id`  
			FROM `".TBL_MAILBOX_CONVERSATION."` AS `c`
			LEFT JOIN  `".TBL_PROFILE."` AS `s` ON(`s`.`profile_id`=`c`.`initiator_id`)
			LEFT JOIN  `".TBL_PROFILE."` AS `r` ON(`r`.`profile_id`=`c`.`interlocutor_id`)
			WHERE `conversation_id`=?", $conv_id );

        $conv_info = SK_MySQL::query( $query )->fetch_array();
        $conv_info['new_msg_sender'] = $profile_id;
        $conv_info['initiator_username'] = app_Profile::username($conv_info['initiator_id']);
        $conv_info['opponent'] = !empty($conv_info['opponent_id']) ? app_Profile::username($conv_info['opponent_id']) : NULL;

        $recipient = ($conv_info['new_msg_sender'] == $conv_info['initiator_id']) ? $conv_info['interlocutor_id'] : $conv_info['initiator_id'];
        $conv_info['new_msg_recipient'] = $recipient;
        $conv_info['opp_status'] = self::opponentConversationStatus($conv_id, $profile_id);

        $query = SK_MySQL::placeholder( "SELECT COUNT(`m`.`message_id`) FROM ".TBL_MAILBOX_MESSAGE." AS `m`
			WHERE `m`.`conversation_id`=?".$filter_cond, $conv_id );

        return array (
            'info'	=> $conv_info,
            'mails' => $msg_arr,
            'total'	=> SK_MySQL::query($query)->fetch_cell(),
        );
    }

    /**
     * Get array of values that make up mask.
     *
     * @param integer $mask
     * @param boolean $string_presentation
     * @return string|array
     */
    static function getBitMaskValues( $mask, $string_presentation = true )
    {
        $available_mask_arr = array(self::INITIATOR_FLAG, self::INTERLOCUTOR_FLAG);

        if ( in_array( $mask, $available_mask_arr ) )
        {
            $values_count = pow( 2, 2 );
            if ( $string_presentation )
            {
                $return_arr = "";
                for ( $i = 1; $i < $values_count; $i++ )
                    if ( ($mask & $i) == $mask )
                        $return_arr .= $i.", ";

                return substr( $return_arr, 0, -2 );
            }
            else
            {
                $return_arr = array();
                for ( $i = 1; $i < $values_count; $i++ )
                    if ( ($mask & $i) == $mask )
                        $return_arr[$i] = $i;
                return $return_arr;
            }
        }
    }

    /**
     * Start new conversation
     *
     * @param integer $initiator_id
     * @param integer $interlocutor_id
     * @param string $subject
     *
     * @return integer - new added conversation identificator in database
     *
     */
    static function startConversation( $initiator_id, $interlocutor_id, $subject )
    {
        if ( !$initiator_id || !$interlocutor_id || !strlen( trim( $subject ) ) )
            return -1;

        $_query = SK_MySQL::placeholder( "INSERT INTO `".TBL_MAILBOX_CONVERSATION."`
            (`initiator_id`, `interlocutor_id`, `subject`, `bm_read`, `bm_read_special`, `conversation_ts`) 
			VALUES ( ?, ?, '?', '1', '1', ? )", $initiator_id, $interlocutor_id, $subject, time() );

        $res = SK_MySQL::query( $_query );
        $conversation_id = SK_MySQL::insert_id();

        return isset($conversation_id) ? $conversation_id : null;
    }

    /**
     * Sends message to member's mailbox and starts conversation
     *
     * @param integer $sender_id
     * @param integer $recipient_id
     * @param string  $text
     * @param string  $subject
     * @param boolean $is_readable
     *
     * @return integer:
     * <ul>
     * <li>  1 - successfully inserted</li>
     * <li>  0 - was not inserted</li>
     * <li>	-1 - sender or recipient not found</li>
     * <li> -2 - attempt to send message to oneself</li>
     * <li> -3 - could not start conversation</li>
     * </ul>
     */
    public static function sendMessage( $sender_id, $recipient_id, $text, $subject, $is_readable = false )
    {
        if ( !isset($sender_id) || !isset($recipient_id))
            return -1;

        // check sending to oneself
        if ( $recipient_id == $sender_id )
            return -2;

        if ($sender_id != SK_HttpUser::profile_id())
            return -1;

        $conversation_id = self::startConversation( $sender_id, $recipient_id, $subject );
        if ( $conversation_id == null )
            return -3;

        $readable = ( $is_readable )? 'yes' : 'no';

        $hash = self::generateMessageHash( $sender_id, $subject, $text );

        #scan for scam keywords
        $status_q = ( SK_Config::section('mailbox')->section('spam_filter')->mailbox_message_filter
            && (self::scanForScamKeyword( $text ) || self::scanForScamKeyword( $subject ) || self::scanForSpam( $hash )) ) ? 'p' : 'a';
        $is_kiss = 'no';

        $query = SK_MySQL::placeholder( "INSERT INTO `".TBL_MAILBOX_MESSAGE."` (`conversation_id`, `time_stamp`,`sender_id`,
			`recipient_id`,`text`, `is_kiss`, `is_readable`,`status`,`hash`) VALUES( ?, ?, ?, ?, '?', '?', '?', '?', '?' )",
            $conversation_id,  time(), $sender_id, $recipient_id, $text, $is_kiss, $readable, $status_q, $hash );

        $res = SK_MySQL::query( $query );

        return SK_MySQL::affected_rows() ? ($status_q == 'a' ? 1 : 2) : 0;
    }

    public static function getConversation( $conv_id )
    {
        $query = SK_MySQL::placeholder("SELECT *
            FROM `".TBL_MAILBOX_CONVERSATION."`
            WHERE `conversation_id`=?", $conv_id);

        return SK_MySQL::query($query)->fetch_assoc();
    }

    public static function conversationIsReadable( $conv_id )
    {
        if ( !$conv_id )
        {
            return false;
        }

        $query = SK_MySQL::placeholder("SELECT `is_readable` FROM `".TBL_MAILBOX_MESSAGE."` WHERE `conversation_id` = ?
            ORDER BY `time_stamp` ASC LIMIT 1", $conv_id);

        $is_readable = SK_MySQL::query($query)->fetch_cell();

        return $is_readable == 'yes';
    }

    /**
     * Adds reply to the message in member's mailbox
     *
     * @param integer $conversation_id
     * @param integer $sender_id
     * @param integer $recipient_id
     * @param string  $text
     * @param boolean $is_readable
     *
     * @return integer:
     * <ul>
     * <li>  1 - successfully inserted</li>
     * <li>  0 - was not inserted</li>
     * <li>	-1 - sender or recipient not found</li>
     * <li> -2 - attempt to send message to oneself</li>
     * <li> -3 - conversation is alien</li>
     * </ul>
     */
    public static function sendReplyMessage( $conversation_id, $sender_id, $recipient_id, $text, $is_readable = false )
    {
        if ( !isset($sender_id) || !isset($recipient_id) || !isset($conversation_id))
            return -1;

        // check sending to oneself
        if ( $recipient_id == $sender_id )
            return -2;

        if ($sender_id != SK_HttpUser::profile_id())
            return -1;

        //check owners of the conversation
        $owner_arr = implode(", ", array($sender_id, $recipient_id));
        $query = SK_MySQL::placeholder( "SELECT `conversation_id` FROM `".TBL_MAILBOX_CONVERSATION."`
			WHERE `conversation_id`=? AND `initiator_id` IN ($owner_arr) AND `interlocutor_id` IN ($owner_arr)", $conversation_id );
        $conv_check = SK_MySQL::query( $query )->fetch_cell();

        if (!isset($conv_check))
            return -3;

        $readable = ( $is_readable )? 'yes' : 'no';

        $hash = self::generateMessageHash( $sender_id, '', $text );

        #scan for scam keywords
        $status_q = ( SK_Config::section('mailbox')->section('spam_filter')->mailbox_message_filter && (self::scanForScamKeyword( $text ) || self::scanForSpam( $hash )) )? 'p' : 'a';

        $query = SK_MySQL::placeholder( "INSERT INTO `".TBL_MAILBOX_MESSAGE."` (`conversation_id`, `time_stamp`,`sender_id`,`recipient_id`,`text`,
			`is_readable`,`status`,`hash`) VALUES( ?, ?, ?, ?, '?', '?', '?', '?' )",
            $conversation_id,  time(), $sender_id, $recipient_id, $text, $readable, $status_q, $hash );

        $res = SK_MySQL::query($query);
        $affected = SK_MySQL::affected_rows();

        if ( $status_q == 'a')
        {
            self::restoreConversation($conversation_id, $recipient_id);
            self::markConversationsUnread(array($conversation_id), $recipient_id);
        }

        return $affected ? ($status_q == 'a' ? 1 : 2) : 0;
    }

    /**
     * Sends system message to profile mailbox with notification
     *
     * @param integer $profile_id
     * @param string $subject
     * @param string $text
     *
     * @return integer
     *
     */
    public static function sendSystemMessage( $profile_id, $subject, $text )
    {
        if ( !$profile_id || !strlen( trim( $subject ) ) || !strlen( trim( $text ) ) )
            return -1;

        // sender must have id = 0
        $sender_id = 0;

        $query = SK_MySQL::placeholder( "INSERT INTO `".TBL_MAILBOX_CONVERSATION."`
			(`initiator_id`, `interlocutor_id`, `subject`, `conversation_ts`, `is_system`) 
			VALUES ( ?, ?, '?', ?, 'yes' )", $sender_id, $profile_id, $subject, time() );
        $res = SK_MySQL::query( $query );
        $conversation_id = SK_MySQL::insert_id();

        $hash = self::generateMessageHash( 0, $subject, $text );

        // add message
        if ($conversation_id)
        {
            $query = SK_MySQL::placeholder( "INSERT INTO `".TBL_MAILBOX_MESSAGE."`
				(`conversation_id`, `time_stamp`,`sender_id`,`recipient_id`,`text`, `status`,`hash`) 
				VALUES( ?, ?, '0', ?, '?', 'a', '?' )",
                $conversation_id,  time(), $profile_id, $text, $hash );

            $res = SK_MySQL::query($query);
            $ins_id = SK_MySQL::insert_id();

            if ($ins_id)
                return 1;
        }

        return -2;
    }

    /**
     * Sends notification letter to profile's email when he gets new message.
     *
     * @param integer $recipient_id
     * @param integer $sender_id
     * @return boolean
     */
    public static function sendEmailNotification( $recipient_id, $sender_id )
    {
        $recipient_id = intval( $recipient_id );
        $sender_id = intval( $sender_id );
        if ( !$recipient_id || !$sender_id )
            return false;

        $recipient_info = app_Profile::getFieldValues($recipient_id, 'email');
        if( !$recipient_info )
            return false;

        if ( app_ProfilePreferences::get('mailbox', 'notify_messages', $recipient_id) )
        {
            $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                ->setRecipientProfileId($recipient_id)
                ->setTpl('notify_about_new_msg')
                ->assignVarRange(array(
                'recipient_name' => app_Profile::username($recipient_id),
                'sender_name' => app_Profile::username($sender_id),
                'contact_url' => SK_Navigation::href('mailbox', array('folder' => 'inbox') )
            ));

            return app_Mail::send($msg);
        }
    }

    /**
     * Mark conversation(s) unread.
     *
     * @param integer | array of integer $conversations_arr
     * @param integer $mark_for
     *
     * @return boolean:<br>
     * <li> true : successfully marked read</li>
     * <li> false : incorrect parameters passed</li>
     */
    public static function markConversationsUnread( $conversations_arr, $mark_for )
    {
        if ( !is_array( $conversations_arr ) || !count( $conversations_arr ) )
            return false;

        $marked = 0;

        foreach ( $conversations_arr as $conv_id )
        {
            $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MAILBOX_CONVERSATION."`
				WHERE (`initiator_id`=? OR `interlocutor_id`=?) AND `conversation_id`=?", $mark_for, $mark_for, $conv_id);

            $conv = SK_MySQL::query($query)->fetch_assoc();
            if ( $conv )
            {
                if ( $conv['initiator_id'] == $mark_for )
                    $mark_mask = self::INITIATOR_FLAG;
                elseif ( $conv['interlocutor_id'] == $mark_for )
                    $mark_mask = self::INTERLOCUTOR_FLAG;

                $query_if = "(IF( `bm_read` IN (".self::getBitMaskValues($mark_mask)."), ".$mark_mask.", 0))";
                $query = "UPDATE `".TBL_MAILBOX_CONVERSATION."` SET `bm_read`=`bm_read`-".$query_if." WHERE `conversation_id`=".$conv_id;

                $res = SK_MySQL::query($query);
                $marked += SK_MySQL::affected_rows();
            }
        }

        return $marked ? true : false;
    }

    /**
     * Mark conversation(s) read.
     *
     * @param integer | array of integer $conversations_arr
     * @param integer $mark_for
     *
     * @return integer:<br>
     * <li> true : successfully marked read</li>
     * <li> false : incorrect parameters passed</li>
     */
    public static function markConversationsRead( $conversations_arr, $mark_for, $auto = false )
    {
        if ( !is_array( $conversations_arr ) || !count( $conversations_arr ) )
            return false;

        $marked = 0;

        foreach ( $conversations_arr as $conv_id )
        {
            $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MAILBOX_CONVERSATION."`
				WHERE (`initiator_id`=? OR `interlocutor_id`=?) AND `conversation_id`=?", $mark_for, $mark_for, $conv_id);

            $conv = SK_MySQL::query($query)->fetch_assoc();
            if ( $conv )
            {
                if ( $conv['initiator_id'] == $mark_for )
                    $mark_mask = self::INITIATOR_FLAG;
                elseif ( $conv['interlocutor_id'] == $mark_for )
                    $mark_mask = self::INTERLOCUTOR_FLAG;

                if ( $auto )
                {
                    $query_if = "(IF( `bm_read` NOT IN (".self::getBitMaskValues($mark_mask)."), ".$mark_mask.", 0))";
                    $query_if_special = "(IF( `bm_read_special` NOT IN (".self::getBitMaskValues($mark_mask)."), ".$mark_mask.", 0))";
                    $query = "UPDATE `".TBL_MAILBOX_CONVERSATION."` SET `bm_read`=`bm_read`+".$query_if.", 
                        `bm_read_special`=`bm_read_special`+".$query_if_special." WHERE `conversation_id`=".$conv_id;
                }
                else
                {
                    $query_if = "(IF( `bm_read` NOT IN (".self::getBitMaskValues($mark_mask)."), ".$mark_mask.", 0))";
                    $query = "UPDATE `".TBL_MAILBOX_CONVERSATION."` SET `bm_read`=`bm_read`+".$query_if." WHERE `conversation_id`=".$conv_id;
                }

                $res = SK_MySQL::query($query);
                $marked += SK_MySQL::affected_rows();
            }
        }

        return $marked ? true : false;
    }

    /**
     * Delete conversations and their messages.
     *
     * @param array of integer $conversations_arr
     * @param integer $delete_for
     *
     * @return boolean:<br>
     * <li> true : successfully marked as deleted</li>
     * <li> false : incorrect parameters passed</li>
     */
    static function deleteConversations( $conversations_arr, $delete_for )
    {
        if ( !is_array( $conversations_arr ) || !count( $conversations_arr ) ||  !is_numeric( $delete_for ) )
            return false;

        $marked = 0;

        foreach ( $conversations_arr as $conv_id )
        {
            $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MAILBOX_CONVERSATION."`
				WHERE (`initiator_id`=? OR `interlocutor_id`=?) 
				AND `conversation_id`=?", $delete_for, $delete_for, $conv_id);

            $conv = MySQL::query($query)->fetch_assoc();
            if ( $conv )
            {
                if ( $conv['initiator_id'] == $delete_for )
                    $mark_mask = self::INITIATOR_FLAG;
                elseif ( $conv['interlocutor_id'] == $delete_for )
                    $mark_mask = self::INTERLOCUTOR_FLAG;

                $query_if = "(IF( `bm_deleted` NOT IN (".self::getBitMaskValues($mark_mask)."), ".$mark_mask.", 0))";
                $query = "UPDATE `".TBL_MAILBOX_CONVERSATION."` SET `bm_deleted`=`bm_deleted`+".$query_if."
				WHERE `conversation_id`=".$conv_id;

                $res = SK_MySQL::query($query);
                $marked += SK_MySQL::affected_rows();

                // check if conversation and its messages can be deleted
                $query = "SELECT `bm_deleted` FROM `".TBL_MAILBOX_CONVERSATION."`
					WHERE `conversation_id`='".$conv_id."'";

                $bm_deleted_for = SK_MySQL::query($query)->fetch_cell();

                if ( intval($bm_deleted_for) == (self::INITIATOR_FLAG + self::INTERLOCUTOR_FLAG) || $conv['is_system'] == 'yes' )
                {
                    // delete messages
                    $query = SK_MySQL::placeholder("DELETE FROM `".TBL_MAILBOX_MESSAGE."`
						WHERE `conversation_id`=?", $conv_id );
                    $res = SK_MySQL::query($query);

                    if (SK_MySQL::affected_rows())
                    {
                        // delete conversation
                        $query = SK_MySQL::placeholder("DELETE FROM `".TBL_MAILBOX_CONVERSATION."`
							WHERE `conversation_id`=?", $conv_id );
                        $res = SK_MySQL::query($query);
                    }
                }

            }
            else return false;
        }

        return $marked ? true : false;
    }

    /**
     * Restore conversation (mark it undeleted for profile).
     *
     * @param integer $conv_id
     * @param integer $restore_for
     *
     * @return boolean
     */
    public static function restoreConversation( $conv_id, $restore_for )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MAILBOX_CONVERSATION."`
			WHERE (`initiator_id`=? OR `interlocutor_id`=?) 
			AND `conversation_id`=?", $restore_for, $restore_for, $conv_id);

        $conv = MySQL::query($query)->fetch_assoc();
        if ( $conv )
        {
            if ( $conv['initiator_id'] == $restore_for )
                $mark_mask = self::INITIATOR_FLAG;
            elseif ( $conv['interlocutor_id'] == $restore_for )
                $mark_mask = self::INTERLOCUTOR_FLAG;

            $query_if = "(IF( `bm_deleted` IN (".self::getBitMaskValues($mark_mask)."), ".$mark_mask.", 0))";
            $query = "UPDATE `".TBL_MAILBOX_CONVERSATION."` SET `bm_deleted`=`bm_deleted`-".$query_if."
			WHERE `conversation_id`=".$conv_id;

            $res = SK_MySQL::query($query);
            if (SK_MySQL::affected_rows())
                return true;
        }
        return false;
    }

    /**
     * Counts unread conversations in profile inbox
     *
     * @param integer $profile_id
     * @return integer
     */
    public static function newMessages( $profile_id )
    {
        if (!isset($profile_id) || !is_numeric($profile_id))
            return false;

        if ( isset(self::$newMessageCountCache[$profile_id]) )
        {
            return self::$newMessageCountCache[$profile_id];
        }

        // check config for filter condition
        $filter_cond = SK_Config::section('mailbox')->section('spam_filter')->mailbox_message_filter ? " AND `m`.`status`='a'" : '';

        $query = "SELECT COUNT(DISTINCT `c`.`conversation_id`)
			FROM `".TBL_MAILBOX_CONVERSATION."` AS `c`
			LEFT JOIN `".TBL_MAILBOX_MESSAGE."` AS `m` ON (`c`.`conversation_id` = `m`.`conversation_id`)
			WHERE (`initiator_id`=$profile_id  OR `interlocutor_id`=$profile_id)
			AND (`bm_deleted` IN(0,".self::INTERLOCUTOR_FLAG.") AND `initiator_id`=$profile_id OR `bm_deleted` IN(0,".self::INITIATOR_FLAG.") AND `interlocutor_id`=$profile_id)
 			AND (`bm_read` IN(0,".self::INTERLOCUTOR_FLAG.") AND `initiator_id`=$profile_id OR `bm_read` IN(0,".self::INITIATOR_FLAG.") AND `interlocutor_id`=$profile_id)
 			$filter_cond AND `m`.`recipient_id`=$profile_id
 		";

        self::$newMessageCountCache[$profile_id] = SK_MySQL::query($query)->fetch_cell();
        return self::$newMessageCountCache[$profile_id];
    }

    /**
     * Gets status of conversation (is it read or deleted by opponent)
     *
     * @param integer $conv_id
     * @param integer $profile_id
     */
    public static function opponentConversationStatus( $conv_id, $profile_id )
    {
        if (!isset($conv_id) || !is_numeric($conv_id) || !isset($profile_id) || !is_numeric($profile_id))
            return false;

        $query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MAILBOX_CONVERSATION."`
			WHERE (`initiator_id`=? OR `interlocutor_id`=?) 
			AND `conversation_id`=?", $profile_id, $profile_id, $conv_id);

        $conv = SK_MySQL::query($query)->fetch_assoc();

        if($conv)
        {
            if ( $conv['initiator_id'] == $profile_id )
            {
                $opponent_id = $conv['interlocutor_id'];
                $mark_mask = self::INTERLOCUTOR_FLAG;
            }
            elseif ( $conv['interlocutor_id'] == $profile_id )
            {
                $opponent_id = $conv['initiator_id'];
                $mark_mask = self::INITIATOR_FLAG;
            }

            // check if the last message was sent by profile not opponent
            $query = SK_MySQL::placeholder("SELECT `sender_id` FROM `".TBL_MAILBOX_MESSAGE."`
				WHERE `conversation_id`=? ORDER BY `time_stamp` DESC LIMIT 1", $conv_id);

            if (intval(SK_MySQL::query($query)->fetch_cell()) == $profile_id)
            {
                $is_read_query = "IF( `bm_read` IN (".self::getBitMaskValues($mark_mask)."), true, false) AS `is_read`";

                $query = "SELECT $is_read_query FROM `".TBL_MAILBOX_CONVERSATION."`
				WHERE `conversation_id`=".$conv_id;

                $res = SK_MySQL::query($query)->fetch_cell();
                $status['is_read'] = intval($res) == 1 ? 'yes' : 'no';
                $status['show_status'] = 'yes';
            }
            else
                $status['show_status'] = 'no';

            $send_msg_service = new SK_Service('send_message', $opponent_id);
            $send_rdbl_msg_service = new SK_Service('send_readable_message', $opponent_id);
            $reply_rdbl_msg_service = new SK_Service('reply_readable_message', $opponent_id);

            if( $send_msg_service->checkPermissions() != SK_Service::SERVICE_FULL
                && $send_rdbl_msg_service->checkPermissions() != SK_Service::SERVICE_FULL
                && $reply_rdbl_msg_service->checkPermissions() != SK_Service::SERVICE_FULL
            )

                $status['is_available'] = 'no';
            else
                $status['is_available'] = 'yes';


            return $status;
        }
    }

    /**
     * Scans a message for scam keywords. Returns true if the message contains scam words.
     *
     * @param string $text
     * @return boolean
     */
    static function scanForScamKeyword( $text )
    {
        $scan_text = $text;
        $result = SK_MySQL::query( "SELECT `keyword` FROM `".TBL_MAILBOX_SCAM_KEYWORD."` WHERE 1" );

        while ( $keyword = $result->fetch_assoc() )
            if ( stristr( $scan_text, $keyword['keyword'] ) !== false )
                return true;

        return false;
    }

    /**
     * Scans a message using hash. Returns true if the message is a spam.
     *
     * @param string $hash
     * @return boolean
     */
    static function scanForSpam( $message_hash )
    {
        $query = SK_MySQL::placeholder( "SELECT `hash` FROM `".TBL_MAILBOX_MESSAGE."` WHERE `hash`='?' LIMIT 1", $message_hash );
        return SK_MySQL::query( $query )->fetch_cell() ? true : false;
    }

    /**
     * Returns message md5 hash.
     *
     * @param integer $profile_id
     * @param string $message
     * @return string
     */
    static function generateMessageHash( $profile_id, $subject, $message )
    {
        if (!strlen($subject)) // means it is not the first message
            $subject = md5(time());

        $charsonly = preg_replace('~\s+~', '', "$profile_id-".strtolower(trim($subject).trim($message)));
        //$charsonly = preg_replace('~[^\w]+~', '', $slug);

        $hash = md5($charsonly);

        return $hash;
    }

    static function deleteProfileMailboxMessages($profile_id)
    {
        $profile_id = intval($profile_id);

        if (!$profile_id)
            return array();

        $query = SK_MySQL::placeholder("SELECT `conversation_id` FROM `".TBL_MAILBOX_CONVERSATION."`
			WHERE `initiator_id`=? OR `interlocutor_id`=?", $profile_id, $profile_id);

        $res = SK_MySQL::query($query);

        while ($conv = $res->fetch_cell())
            $conv_arr[] = $conv;

        return self::deleteConversations($conv_arr, $profile_id);
    }

    public static function getConversationCount($profile_id)
    {
        $profile_id = intval($profile_id);

        if (!$profile_id)
            $profile_id = SK_HttpUser::profile_id();

        $query = SK_MySQL::placeholder("SELECT COUNT(DISTINCT(`c`.`conversation_id`)) FROM `".TBL_MAILBOX_CONVERSATION."` AS `c`
            INNER JOIN  `".TBL_MAILBOX_MESSAGE."` AS `m` ON (`m`.`conversation_id`=`c`.`conversation_id` AND IF(`c`.`interlocutor_id`=?,`m`.`status`='a',1))
			WHERE (`bm_deleted` IN(0,".self::INTERLOCUTOR_FLAG.") AND `initiator_id`=? 
			OR `bm_deleted` IN(0,".self::INITIATOR_FLAG.") AND `interlocutor_id`=?)
			", $profile_id, $profile_id, $profile_id);

        return SK_MySQL::query($query)->fetch_cell();
    }

    public static function suggestRecipients( $sender_id, $str )
    {
        if ( !strlen($str) || !intval($sender_id) )
        {
            return array();
        }

        $query = SK_MySQL::placeholder("SELECT `profile`.`profile_id`, `profile`.`username`, `profile`.`sex`, `profile`.`birthdate`
            FROM `".TBL_PROFILE."` AS `profile`
            LEFT JOIN `".TBL_PROFILE_FRIEND_LIST."` AS `f` ON( `f`.`friend_id` = `profile`.`profile_id` )
            WHERE `f`.`profile_id`=?   
            AND `profile`.`username` LIKE '?' 
            ORDER BY `profile`.`username` ASC LIMIT 0, 10", $sender_id, "%$str%");

        $result = SK_MySQL::query($query);
        $out = array();

        $birthdate_fields = app_ProfileField::getProfileListBirthdateFields();
        $f_profile_list_section = SK_Language::section('profile_fields')->section('label_profile_list');

        while ( $item = $result->fetch_array() )
        {
            if ( $birthdate_fields )
            {
                $birthdate_fields_values = app_Profile::getFieldValues( $item['profile_id'], $birthdate_fields );
                $age_values = array();

                foreach ( $birthdate_fields_values as $age_key => $val )
                {
                    $profile_field = SK_ProfileFields::get($age_key);

                    if ( $val && $profile_field )
                    {
                        $profile_field_id = $profile_field->profile_field_id;
                        try
                        {
                            $text = $f_profile_list_section->cdata($profile_field_id);

                            if ( !$text ) {
                                continue;
                            }

                            if ( strpos($text, '{') !== false ) {
                                $text = SK_Language::exec($text, array('value' => app_Profile::getAge($val)));
                            }

                            $age_values[$age_key] = $text;
                        }
                        catch ( Exception $ex )
                        {
                            // ignore;
                        }
                    }
                }
            }

            $age_str = '';
            foreach ( $age_values as $age )
            {
                $age_str .= $age .', ';
            }

            preg_match('/'.$str.'/i', $item['username'], $name_matches);

            $label_name = str_replace( $name_matches, '<span class="suggest_hl"><b>'.$name_matches[0].'</b></span>', $item['username']);
            $label_info = '<br /><span class="small">' . $age_str . SK_Language::text("%profile_fields.value.sex_".$item['sex']).'</span>';

            $out[] = array (
                'id'    =>  $item['profile_id'],
                'name'  =>  $item['username'],
                'suggest_label' =>  $label_name.$label_info
            );
        }

        return $out;
    }

}
