<?php

class app_FriendNetwork
{
    /**
     * Checks if second profile is in the friend list of the first profile.
     *
     * @param integer $profile_id
     * @param integer $friend_profile_id
     * @return boolean
     */
    public static function isProfileFriend( $profile_id, $friend_profile_id )
    {
        return self::checkRelation($profile_id, $friend_profile_id, 'friends');
    }

    /**
     * Send friend request.
     * Returns boolean
     *
     * @param integer $profile_id
     * @param integer $friend_profile_id
     * @return boolean
     */
    public static function SendFriendRequest( $profile_id, $friend_profile_id )
    {
        if ( !($profile_id = intval($profile_id)) || !($friend_profile_id = intval($friend_profile_id)) )
            return false;

        // check if profile already exists in the member's friend list:
        $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE_FRIEND_LIST . "` WHERE
			`profile_id`=? AND `friend_id`=? AND (`status`='pending' OR `status`='active')", $profile_id, $friend_profile_id);

        if ( SK_MySQL::query($query)->fetch_cell() )
        {
            return true;
        }

        SK_MySQL::query( SK_MySQL::placeholder("INSERT INTO `" . TBL_PROFILE_FRIEND_LIST . "` (`profile_id`, `friend_id`,`status` )
								VALUES( ?, ?, 'pending' )", $profile_id, $friend_profile_id) );

        $upd_query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE_FRIEND_LIST . "` SET `friendship_id`=`id` WHERE `profile_id`=? AND `friend_id`=? AND `status`='pending'", $profile_id, $friend_profile_id);

        SK_MySQL::query($upd_query);

        return SK_MySQL::affected_rows();
    }

    /**
     * Add profile to a friend.
     *
     * @param integer $profile_id
     * @param integer $friend_profile_id
     * @return boolean true if affected
     */
    public static function addToFriend( $profile_id, $friend_profile_id )
    {
        // checking entry params
        if ( !is_numeric($profile_id)
            || !($profile_id = (int) $profile_id)
        )        {
            throw new Exception('invalid argument $profile_id');
        }
        if ( !is_numeric($friend_profile_id)
            || !($friend_profile_id = (int) $friend_profile_id)
        )        {
            throw new Exception('invalid argument $friend_profile_id');
        }

        // pre-compiling query templates
        $sql_check = SK_MySQL::compile_placeholder(
                'SELECT `id` FROM `' . TBL_PROFILE_FRIEND_LIST . '`
				WHERE `profile_id`=? AND `friend_id`=?'
        );

        $sql_update = SK_MySQL::compile_placeholder(
                'UPDATE `' . TBL_PROFILE_FRIEND_LIST . '` SET `status`="active", `friendship_id`=?
				WHERE `profile_id`=? AND `friend_id`=?'
        );

        $sql_insert = SK_MySQL::compile_placeholder(
                'INSERT INTO `' . TBL_PROFILE_FRIEND_LIST . '`
				SET `friendship_id`=?, `profile_id`=?, `friend_id`=?, `status`="active"'
        );

        $total_affected_rows = 0;

        // adding link entry
        $check_query = SK_MySQL::placeholder(
                $sql_check, $profile_id, $friend_profile_id
        );

        $row_exist = MySQL::query($check_query)->num_rows();

        $query_tpl = $row_exist ? $sql_update : $sql_insert;

        if ($row_exist)
        {
            $friendship_id = MySQL::fetchField(SK_MySQL::placeholder('SELECT `friendship_id` FROM `' . TBL_PROFILE_FRIEND_LIST . '`
				WHERE `profile_id`=? AND `friend_id`=?', $friend_profile_id, $profile_id));

            $query_tpl = $sql_update;
        }
        else {
            $friendship_id = MySQL::fetchField('SELECT MAX(`id`) FROM `' . TBL_PROFILE_FRIEND_LIST . '` WHERE 1 ');

            $friendship_id++;

            $query_tpl = $sql_insert;
        }


        SK_MySQL::query(
                SK_MySQL::placeholder($query_tpl, $friendship_id, $profile_id, $friend_profile_id)
        );

        $affected_rows = SK_MySQL::affected_rows();

        if ( $affected_rows )        {
            ; //8aa
        }

        // adding mirror link entry
        $check_query = SK_MySQL::placeholder(
                $sql_check, $friend_profile_id, $profile_id
        );

        $row_exist = SK_MySQL::query($check_query)->num_rows();

        $query_tpl = $row_exist ? $sql_update : $sql_insert;

        SK_MySQL::query(
                SK_MySQL::placeholder($query_tpl, $friendship_id, $friend_profile_id, $profile_id)
        );

        $affected_rows = SK_MySQL::affected_rows();

        if ( $affected_rows )        {

            // tracing an user action

            $user_action = new SK_UserAction('friend_add', $profile_id);
            $user_action->item = (int) $friend_profile_id;
            $user_action->status = 'active';
            app_UserActivities::trace_action($user_action);

            // tracing an user action

            $user_action = new SK_UserAction('friend_add', $friend_profile_id);
            $user_action->item = $profile_id;
            $user_action->status = 'active';

            app_UserActivities::trace_action($user_action);

            if (app_Features::isAvailable(app_Newsfeed::FEATURE_ID))
            {
                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_NEWSFEED,
                        'entityType' => 'friend_add',
                        'entityId' => $friendship_id,
                        'userId' => $profile_id,
                        'status' => 'active'
                    ),
                    'data' => array(
                        'profile_id' => $profile_id,
                        'friend_profile_id' => $friend_profile_id
                    )
                );
                app_Newsfeed::newInstance()->action($newsfeedDataParams);

            }

            $total_affected_rows += 2;
        }

        return $total_affected_rows;
    }

    /**
     * Confirm friend request.
     *
     * @param integer $profile_id
     * @param integer $friend_profile_id
     * @return boolean
     */
    public static function confirmRequest( $profile_id, $friend_profile_id )
    {
        return self::addToFriend($profile_id, $friend_profile_id);
    }

    /**
     * Decline friend request.
     * Returns boolean
     *
     * @param integer $profile_id
     * @param integer $friend_profile_id
     * @return boolean
     */
    public static function declineRequest( $profile_id, $friend_profile_id )
    {
        SK_MySQL::query(SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_FRIEND_LIST . "`
			WHERE `profile_id`=? AND `friend_id`=? AND `status`='pending'", $friend_profile_id, $profile_id));
        return (bool) SK_MySQL::affected_rows();
    }

    /**
     * Deletes profile from friend list.
     * Returns boolean - result of the query OR error code : -1 - incorrect parametrs.
     *
     * @param integer $profile_id
     * @param integer $friend_profile_id
     * @return boolean|integer
     */
    public static function DeleteFromFriendList( $profile_id, $friend_profile_id )
    {
        //Newsfeed
        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $friendship_id = MySQL::fetchField(SK_MySQL::placeholder('SELECT `friendship_id` FROM `' . TBL_PROFILE_FRIEND_LIST . '`
				WHERE `profile_id`=? AND `friend_id`=?', $profile_id, $friend_profile_id));
            app_Newsfeed::newInstance()->removeAction(ENTITY_TYPE_FRIEND_ADD, $friendship_id);
        }

        SK_MySQL::query(SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_FRIEND_LIST . "`
			WHERE (`profile_id`=? AND `friend_id`=?) OR (`profile_id`=? AND `friend_id`=?) AND `status`='active'", $profile_id, $friend_profile_id, $friend_profile_id, $profile_id));

        $affectedRows = (bool) SK_MySQL::affected_rows();

        return $affectedRows;
    }

    /**
     * Cancel friend request.
     *
     * @param integer $profile_id
     * @param integer $friend_profile_id
     * @return boolean true if affected
     */
    public static function cancelRequest( $profile_id, $friend_profile_id )
    {
        if ( !is_numeric($profile_id) || !is_numeric($friend_profile_id) )
            return false;

        SK_MySQL::query(SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_FRIEND_LIST . "`
			WHERE `profile_id`=? AND `friend_id`=? AND `status`='pending'", $profile_id, $friend_profile_id));
        return (bool) SK_MySQL::affected_rows();
    }

    /**
     * Returns member's friend network profiles with their info.
     *
     * @param integer $profile_id
     * @param string $friend_list_type
     * @return array|integer
     */
    public static function FriendNetworkList( $profile_id, $friend_list_type = 'friends', $paging = true )
    {
        $friend_list_type = trim($friend_list_type);

        if ( !is_numeric($profile_id) )
            return array();

        // detect online list result page
        $page = app_ProfileList::getPage();

        $config = SK_Config::section( 'site.additional.profile_list' );
        // get numbers on page:
        $result_per_page = $config->result_per_page;

        $query_parts['limit'] = $paging
? SK_MySQL::placeholder("LIMIT ?, ?", $result_per_page * ($page - 1), $result_per_page)
: '';

        $query_parts['projection'] = "`f`.*,`p`.*,`pe`.*,`online`.`hash` AS `online`";

        if ( $friend_list_type != 'got_requests' )
        {
            switch ( $friend_list_type )
            {
                case 'friends':
                    $status = 'active';
                    break;
                case 'sent_requests':
                    $status = 'pending';
                    break;
                default:
                    return array();
            }

            $query_parts['left_join'] = "
				INNER JOIN `" . TBL_PROFILE . "` AS `p` ON( `f`.`friend_id`=`p`.`profile_id` )
				INNER JOIN `" . TBL_PROFILE_EXTEND . "` AS `pe` ON( `f`.`friend_id`=`pe`.`profile_id` )
				LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` ON( `f`.`friend_id`=`online`.`profile_id` )
			";

            $query_parts['condition'] = SK_MySQL::placeholder(
                    "(`f`.`profile_id`=?) AND " . app_Profile::SqlActiveString('p') . " AND (`f`.`status`='?')"
                    , $profile_id, $status);
        }
        else
        {
            $query_parts['left_join'] = "
				INNER JOIN `" . TBL_PROFILE . "` AS `p` ON( `f`.`profile_id`=`p`.`profile_id` )
				INNER JOIN `" . TBL_PROFILE_EXTEND . "` AS `pe` ON( `f`.`profile_id`=`pe`.`profile_id` )
				LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` ON( `f`.`profile_id`=`online`.`profile_id` )
			";

            $query_parts['condition'] = SK_MySQL::placeholder(
                    "(`f`.`friend_id`=?) AND " . app_Profile::SqlActiveString('p') . " AND (`f`.`status`='pending')"
                    , $profile_id);
        }

        $query_parts['order'] = "";
        
        foreach ( explode("|", $config->order) as $val )
        {
            if ( in_array($val, array('', 'none')) )
                continue;

            app_ProfileList::_configureOrder($query_parts, $val, "p");
        }
//----

        //----
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
        
        $query = "SELECT {$query_parts['projection']} FROM `" . TBL_PROFILE_FRIEND_LIST . "` AS `f`
			{$query_parts['left_join']}
			WHERE {$query_parts['condition']} $sex_condition $gender_exclusion " .
            ( isset($query_parts['group']) && (strlen($query_parts['group'])) ? " GROUP BY {$query_parts['group']}" : "" ) .
            ( (strlen($query_parts['order'])) ? " ORDER BY {$query_parts['order']}" : "" ) .
            " {$query_parts['limit']}";

        $query_result = SK_MySQL::query($query);
        $result['profiles'] = array();
        while ( $item = $query_result->fetch_assoc() )
            $result['profiles'][] = $item;

        $result['total'] = self::countFriendNetworkList($profile_id, $friend_list_type);

        return $result;
    }

    /**
     * Returns member's friends with their info.
     *
     * @param integer $profile_id
     * @param integer $num_on_page
     * @return array|integer
     */
    public static function FriendList( $profile_id, $paging = true )
    {
        return self::FriendNetworkList($profile_id, 'friends', $paging);
    }

    /**
     * Returns member's got requests profiles with their info.
     *
     * @param integer $profile_id
     * @param integer $num_on_page
     * @return array|integer
     */
    public static function GotRequestList( $profile_id, $paging = true )
    {
        return self::FriendNetworkList($profile_id, 'got_requests', $paging);
    }

    /**
     * Returns member's sent request profiles with their info.
     *
     * @param integer $profile_id
     * @param integer $num_on_page
     * @return array|integer
     */
    public static function SentRequestLIst( $profile_id, $paging = true )
    {
        return self::FriendNetworkList($profile_id, 'sent_requests', $paging);
    }

    /**
     * Returns count of friends in the profile friend list.
     *
     * @param integer $profile_id
     * @param string $_type
     * @return integer|boolean
     */
    public static function countFriendNetworkList( $profile_id, $type='friends' )
    {
        $type = trim($type);

        $profile_id = intval($profile_id);
        if ( !$profile_id )
            return 0;

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

        if ( $type != 'got_requests' )
        {
            switch ( $type )
            {
                case 'friends':
                    $status = 'active';
                    break;
                case 'sent_requests':
                    $status = 'pending';
                    break;
                default:
                    return 0;
            }

            return SK_MySQL::query(SK_MySQL::placeholder("SELECT COUNT( `f`.`friend_id` )
				FROM `" . TBL_PROFILE_FRIEND_LIST . "` AS `f`
				INNER JOIN `" . TBL_PROFILE . "` AS `p` ON( `f`.`friend_id`=`p`.`profile_id` )
				WHERE (`f`.`profile_id`=?)
				AND (`f`.`status`='?') AND " . app_Profile::SqlActiveString('p') . $sex_condition . $gender_exclusion, $profile_id, $status))->fetch_cell();
        }
        else
            return SK_MySQL::query(SK_MySQL::placeholder("SELECT COUNT( `f`.`profile_id` )
				FROM `" . TBL_PROFILE_FRIEND_LIST . "` AS `f`
				INNER JOIN `" . TBL_PROFILE . "` AS `p` ON( `f`.`profile_id`=`p`.`profile_id` )
				WHERE (`f`.`friend_id`=?)
				AND (`f`.`status`='pending') AND " . app_Profile::SqlActiveString('p') . $sex_condition . $gender_exclusion, $profile_id))->fetch_cell();
    }

    /**
     * Returns count of got friend requests.
     *
     * @param integer $profile_id
     * @return integer
     */
    public static function countGotRequests( $profile_id )
    {
        return self::countFriendNetworkList($profile_id, 'got_requests');
    }

    /**
     * Returns count of friends.
     *
     * @param integer $profile_id
     * @return integer
     */
    public static function countFriends( $profile_id )
    {
        return self::countFriendNetworkList($profile_id, 'friends');
    }

    /**
     * Returns count of sent requests.
     *
     * @param integer $profile_id
     * @return integer
     */
    public static function countSentRequests( $profile_id )
    {
        return self::countFriendNetworkList($profile_id, $_type = 'sent_requests');
    }

    /**
     * Check if the member sent friend request to profile.
     *
     * @param integer $profile_id
     * @param integer $recipient_profile_id
     * @return boolean
     */
    public static function isMemberSentRequest( $profile_id, $recipient_profile_id )
    {
        return self::checkRelation($profile_id, $recipient_profile_id, 'sent_request');
    }

    /**
     * Check if the profile sent friend request to member.
     *
     * @param integer $member_id
     * @param integer $profile_id
     * @return boolean
     */
    public static function isProfileHasGotRequest( $profile_id, $sender_profile_id )
    {
        return self::checkRelation($profile_id, $sender_profile_id, 'got_request');
    }


    public static function checkRelation( $profile_id, $friend_profile_id, $relation_type )
    {
        if ( !($profile_id = intval($profile_id)) || !($friend_profile_id = intval($friend_profile_id)) )
            return false;

        $relation_type = trim($relation_type);
        if ( !$relation_type )
            return false;

        $compiled = SK_MySQL::compile_placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE_FRIEND_LIST . "`
			WHERE `profile_id`=? AND `friend_id`=? AND `status`='?'");

        switch ( $relation_type )
        {
            case 'friends':
                if( isset(self::$relationCache['friend'][$profile_id.'_'.$friend_profile_id]) )
                {
                    return self::$relationCache['friend'][$profile_id.'_'.$friend_profile_id];
                }
                $_status = 'active';
                self::$relationCache['friend'][$profile_id.'_'.$friend_profile_id] = (bool) SK_MySQL::query(SK_MySQL::placeholder($compiled, $profile_id, $friend_profile_id, $_status))->fetch_cell();
                return self::$relationCache['friend'][$profile_id.'_'.$friend_profile_id];
            case 'sent_request':
                if( isset(self::$relationCache['pending'][$profile_id.'_'.$friend_profile_id]) )
                {
                    return self::$relationCache['pending'][$profile_id.'_'.$friend_profile_id];
                }
                $_status = 'pending';
                self::$relationCache['pending'][$profile_id.'_'.$friend_profile_id] = (bool) SK_MySQL::query(SK_MySQL::placeholder($compiled, $profile_id, $friend_profile_id, $_status))->fetch_cell();
                return self::$relationCache['pending'][$profile_id.'_'.$friend_profile_id];
            case 'got_request':
                if( isset(self::$relationCache['pending'][$friend_profile_id.'_'.$profile_id]) )
                {
                    return self::$relationCache['pending'][$friend_profile_id.'_'.$profile_id];
                }
                $_status = 'pending';
                self::$relationCache['pending'][$friend_profile_id.'_'.$profile_id] = SK_MySQL::query(SK_MySQL::placeholder($compiled, $friend_profile_id, $profile_id, $_status))->fetch_cell();
                return self::$relationCache['pending'][$friend_profile_id.'_'.$profile_id];
            default:
                return false;
        }
    }

    private static $relationCache = array( 'friend' => array(), 'pending' => array() );

    public static function initUserRelationCacheForValues( $profile_id, array $friend_profile_id_list )
    {
        if ( !($profile_id = intval($profile_id)) || empty($friend_profile_id_list) )
            return;

        $query = SK_MySQL::placeholder("
            SELECT `profile_id`, `friend_id`, `status` FROM `" . TBL_PROFILE_FRIEND_LIST . "` WHERE `profile_id`=? AND `friend_id` IN (?@)
                UNION ALL
            SELECT `profile_id`, `friend_id`, `status` FROM `" . TBL_PROFILE_FRIEND_LIST . "` WHERE `profile_id` IN (?@) AND `friend_id`=?
        ", $profile_id, $friend_profile_id_list, $friend_profile_id_list, $profile_id);
        
        $result = SK_MySQL::queryForList($query);

        foreach ( $friend_profile_id_list as $id )
        {
            self::$relationCache['friend'][$id.'_'.$profile_id] = false;
            self::$relationCache['friend'][$profile_id.'_'.$id] = false;
            self::$relationCache['pending'][$id.'_'.$profile_id] = false;
            self::$relationCache['pending'][$profile_id.'_'.$id] = false;
        }


        foreach ( $result as $item )
        {
            if( $item['status'] == 'active' )
            {
                self::$relationCache['friend'][$item['profile_id'].'_'.$item['friend_id']] = true;
                self::$relationCache['friend'][$item['friend_id'].'_'.$item['profile_id']] = true;
            }
            else if( $item['status'] == 'pending' )
            {
                self::$relationCache['pending'][$item['profile_id'].'_'.$item['friend_id']] = true;
            }
        }
    }

    /**
     * Returns network tab url
     *
     * @param string $_tab
     * @param integer $anchor_profile_id
     * @return string
     */
    public static function FriendNetworkTabUrl( $tab, $anchor_profile_id=null )
    {
        $tab = trim($tab);
        if ( !in_array($tab, array('friends', 'sent_requests', 'got_requests')) )
            return false;

        return sk_make_url(SK_Navigation::href('profile_friend_list'), array('tab' => $tab), ( (intval($anchor_profile_id)) ? 'id_' . $anchor_profile_id : null));

    }

    /*     * TODO
     * Sends friend network message
     *
     * @param integer $member_id
     * @param string $message_type
     * @return integer|boolean
     */
    public static function sendFriendNetworkMessage( $profile_id, $message_type )
    {
        $message_type = trim($message_type);
        $profile_id = intval($profile_id);
        if ( !$profile_id || !$message_type )
            return false;

        if ( !app_ProfilePreferences::get('friend_network', 'allow_notifications', $profile_id) )
        {
            return false;
        }

        $email = app_Profile::getFieldValues($profile_id, 'email');

        switch ( $message_type )
        {
            case 'request_accepted':
                $tmp_prefix = 'friend_nw_letter_request_accept';
                $member_url = self::FriendNetworkTabUrl('friends', SK_HttpUser::profile_id());
                break;
            case 'request_declined':
                $tmp_prefix = 'friend_nw_letter_request_declined';
                break;
            case 'request_got':
                $tmp_prefix = 'friend_nw_letter_request_got';
                $member_url = self::FriendNetworkTabUrl('got_requests', SK_HttpUser::profile_id());
                break;
            case 'profile_deleted':
                $tmp_prefix = 'friend_nw_letter_profile_deleted';
                break;
            default:
                return false;
        }

        if ( !app_Unsubscribe::isProfileUnsubscribed($profile_id) )
        {
            $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                    ->setRecipientProfileId($profile_id)
                    ->setTpl($tmp_prefix)
                    ->assignVar('sender_name', SK_HttpUser::username())
                    ->assignVar('profile_url', $member_url);
            return app_Mail::send($msg);
        }
    }

}
