<?php

class app_Profile
{
    private static $profile_fields_value = array();

    /**
     * Get values of profile fields
     * If nessesary fields array not specified get all fields
     *
     * @param integer $profile_id
     * @param array|string $fields
     *
     * @return array|mixed
     *
     */
    public static function getFieldValues( $profile_id, $fields = null, $caching = true )
    {
        if ( !(int) $profile_id )
        {
            return is_array($fields) ? array() : null;
        }

        $_query_ins = $_join_tbl = '';

        $return_single = false;

        if ( isset($fields) && is_string($fields) )
        {
            if ( isset(self::$profile_fields_value[$profile_id]) && array_key_exists($fields, self::$profile_fields_value[$profile_id]) )
            {
                return self::$profile_fields_value[$profile_id][$fields];
            }

            $return_single = true;
            $fields = array($fields);
        }
        
        if ( !$return_single && isset($fields) )
        {
            if ( isset(self::$profile_fields_value[$profile_id]) && count(array_diff($fields, array_keys(self::$profile_fields_value[$profile_id]))) == 0 )
            {
                $result = array();
                foreach ( $fields as $field )
                {
                    $result[$field] = self::$profile_fields_value[$profile_id][$field];
                }

                return $result;
            }
        }

        if ( isset($fields) && count($fields) )
        {
            $joined_tables = array();
            $_query_ins .= "`main`.*,";
            foreach ( $fields as $field_key => $_field )
            {
                if ( $caching && isset(self::$profile_fields_value[$profile_id][$_field]) )
                {
                    continue;
                }

                switch ( $_field )
                {
                    case 'country':
                        $_query_ins.= "`location_country`.`Country_str_name` AS `country`,";

                        if ( !in_array(TBL_LOCATION_COUNTRY, $joined_tables) )
                            $_join_tbl .= "LEFT JOIN `" . TBL_LOCATION_COUNTRY . "` AS `location_country`
										ON `main`.`country_id`=`location_country`.`Country_str_code`";

                        $joined_tables[] = TBL_LOCATION_COUNTRY;

                        break;

                    case 'state':
                        $_query_ins.= "`location_state`.`Admin1_str_name` AS `state`,";

                        if ( !in_array(TBL_LOCATION_STATE, $joined_tables) )
                            $_join_tbl .= "LEFT JOIN `" . TBL_LOCATION_STATE . "` AS `location_state`
								ON `main`.`state_id`=`location_state`.`Admin1_str_code`";

                        $joined_tables[] = TBL_LOCATION_STATE;

                        break;

                    case 'city':
                        $_query_ins.= "`location_city`.`Feature_str_name` AS `city`,";

                        if ( !in_array(TBL_LOCATION_CITY, $joined_tables) )
                            $_join_tbl .= "LEFT JOIN `" . TBL_LOCATION_CITY . "` AS `location_city`
								ON `main`.`city_id`=`location_city`.`Feature_int_id`";

                        $joined_tables[] = TBL_LOCATION_CITY;

                        break;

                    case 'join_ip':
                        $_query_ins .= "INET_NTOA( `join_ip` ) AS `join_ip`";
                        break;                  
                    default:
                        try
                        {
                            $_query_ins.= SK_ProfileFields::get($_field)->base_field ? '' : "`extend`.`$_field`,";

                            if ( !SK_ProfileFields::get($_field)->base_field && !in_array(TBL_PROFILE_EXTEND, $joined_tables) )
                            {
                                $_join_tbl.= "LEFT JOIN `" . TBL_PROFILE_EXTEND . "` AS `extend` ON ( `extend`.`profile_id` = `main`.`profile_id` )";
                                $joined_tables[] = TBL_PROFILE_EXTEND;
                            }
                        }
                        catch ( SK_ProfileFieldException $e )
                        {
                            continue;
                        }
                        break;
                }
            }

            $_query_ins = substr($_query_ins, 0, -1);
        }
        else
        {
            $_query_ins = "`main`.*, `extend`.*, INET_NTOA( `main`.`join_ip` ) AS `join_ip`, `main`.`profile_id` AS `profile_id`,
				`location_country`.`Country_str_name` AS `country`, `location_state`.`Admin1_str_name` AS `state`,
				`location_city`.`Feature_str_name` AS `city`";

            $_join_tbl = "LEFT JOIN `" . TBL_LOCATION_COUNTRY . "` AS `location_country`
				ON `main`.`country_id`=`location_country`.`Country_str_code`";
            $_join_tbl .= "LEFT JOIN `" . TBL_LOCATION_STATE . "` AS `location_state`
				ON `main`.`state_id`=`location_state`.`Admin1_str_code`";
            $_join_tbl .= "LEFT JOIN `" . TBL_LOCATION_CITY . "` AS `location_city`
				ON `main`.`city_id`=`location_city`.`Feature_int_id`";
            $_join_tbl.= "LEFT JOIN `" . TBL_PROFILE_EXTEND . "` AS `extend` ON ( `extend`.`profile_id` = `main`.`profile_id` )";
        }

        $_profile_info = null;

        if ( !empty($_query_ins) )
        {
            $_query = SK_MySQL::placeholder("SELECT $_query_ins FROM `" . TBL_PROFILE . "` AS `main`
                            $_join_tbl
                            WHERE `main`.`profile_id` =?", $profile_id);

            $_profile_info = SK_MySQL::query($_query)->fetch_assoc();
        }

        $_profile_info = $_profile_info ? $_profile_info : array();

        if ( !isset(self::$profile_fields_value[$profile_id]) )
        {
            self::$profile_fields_value[$profile_id] = $_profile_info;
        }
        else
        {
            self::$profile_fields_value[$profile_id] = array_merge(self::$profile_fields_value[$profile_id], $_profile_info);
        }

        if( !isset($fields) )
        {
            return $_profile_info;
        }

        if( empty($_profile_info) )
        {
            foreach ( $fields as $field )
            {
                self::$profile_fields_value[$profile_id][$field] = null;
            }
        }

        if ( $return_single )
        {
            return isset(self::$profile_fields_value[$profile_id][$fields[0]]) ? self::$profile_fields_value[$profile_id][$fields[0]] : null;
        }

        $result = array();

        foreach ( $fields as $field )
        {
            $result[$field] = isset(self::$profile_fields_value[$profile_id][$field]) ? self::$profile_fields_value[$profile_id][$field] : null;
        }

        return $result;
    }

    public static function getFieldValuesForUsers( array $profileIdList, array $fields )
    {
        if ( empty($profileIdList) || empty($fields) )
        {
            return array();
        }

        $joined_tables = array();
        $_query_ins = "`main`.*,";
        foreach ( $fields as $field_key => $_field )
        {
            switch ( $_field )
            {
                case 'country':
                    $_query_ins.= "`location_country`.`Country_str_name` AS `country`,";

                    if ( !in_array(TBL_LOCATION_COUNTRY, $joined_tables) )
                        $_join_tbl .= "LEFT JOIN `" . TBL_LOCATION_COUNTRY . "` AS `location_country`
										ON `main`.`country_id`=`location_country`.`Country_str_code`";

                    $joined_tables[] = TBL_LOCATION_COUNTRY;

                    break;

                case 'state':
                    $_query_ins.= "`location_state`.`Admin1_str_name` AS `state`,";

                    if ( !in_array(TBL_LOCATION_STATE, $joined_tables) )
                        $_join_tbl .= "LEFT JOIN `" . TBL_LOCATION_STATE . "` AS `location_state`
								ON `main`.`state_id`=`location_state`.`Admin1_str_code`";

                    $joined_tables[] = TBL_LOCATION_STATE;

                    break;

                case 'city':
                    $_query_ins.= "`location_city`.`Feature_str_name` AS `city`,";

                    if ( !in_array(TBL_LOCATION_CITY, $joined_tables) )
                        $_join_tbl .= "LEFT JOIN `" . TBL_LOCATION_CITY . "` AS `location_city`
								ON `main`.`city_id`=`location_city`.`Feature_int_id`";

                    $joined_tables[] = TBL_LOCATION_CITY;

                    break;

                case 'join_ip':
                    $_query_ins .= "INET_NTOA( `join_ip` ) AS `join_ip`";
                    break;
                default:

                    try
                    {
                        $_query_ins.= SK_ProfileFields::get($_field)->base_field ? "" : "`extend`.`$_field`,";

                        if ( !SK_ProfileFields::get($_field)->base_field && !in_array(TBL_PROFILE_EXTEND, $joined_tables) )
                        {
                            $_join_tbl.= "LEFT JOIN `" . TBL_PROFILE_EXTEND . "` AS `extend` ON ( `extend`.`profile_id` = `main`.`profile_id` )";
                            $joined_tables[] = TBL_PROFILE_EXTEND;
                        }
                    }
                    catch ( SK_ProfileFieldException $e )
                    {
                        continue;
                    }
                    break;
            }
        }

        $_query_ins = substr($_query_ins, 0, -1);
        $_query = SK_MySQL::placeholder("SELECT $_query_ins FROM `" . TBL_PROFILE . "` AS `main` $_join_tbl WHERE `main`.`profile_id` IN (?@)", $profileIdList);
        $result = SK_MySQL::queryForList($_query);

        $arrayToReturn = array();
        
        foreach ( $result as $item )
        {
            if( !isset(self::$profile_fields_value[$item['profile_id']]) )
            {
                self::$profile_fields_value[$item['profile_id']] = $item;
            }
            else
            {
                self::$profile_fields_value[$item['profile_id']] = array_merge(self::$profile_fields_value[$item['profile_id']], $item);
            }

            $arrayToReturn[$item['profile_id']] = $item;
        }

        foreach ( $profileIdList as $id )
        {
            if( !isset(self::$profile_fields_value[$id]) )
            {
                self::$profile_fields_value[$id] = array();

                foreach ( $fields as $field )
                {
                    self::$profile_fields_value[$id][$field] = null;
                }
            }
        }

        $returnArray = array();

        foreach ( $profileIdList as $id )
        {
            $returnArray[$id] = array();

            if( isset(self::$profile_fields_value[$id]) )
            {
                foreach ( $fields as $field )
                {
                    if( isset(self::$profile_fields_value[$id][$field]) )
                    {
                        $returnArray[$id][$field] = self::$profile_fields_value[$id][$field];
                    }
                }
            }
        }

        return $returnArray;
    }

    public static function username( $profile_id = null, $system = false )
    {
        $profile_id = isset($profile_id) ? $profile_id : SK_HttpUser::profile_id();

        $field = $system ? "username" : SK_Config::section("profile_fields")->Section("advanced")->default_username_field_display;

        $out = self::getFieldValues($profile_id, $field);
        if ( $out )
        {
            return $out;
        }

        return $field;
    }

    public static function getUsernamesForUsers( array $profileIds, $system = false )
    {
        if( empty($profileIds) )
        {
            return array();
        }

        $profileIds = array_unique($profileIds);

        $field = $system ? "username" : SK_Config::section("profile_fields")->Section("advanced")->default_username_field_display;

        $foundFields = self::getFieldValuesForUsers($profileIds, array($field));
        
        $arrayToReturn = array();

        foreach ( $profileIds as $id )
        {
            $arrayToReturn[$id] = empty($foundFields[$id][$field]) ? $field : $foundFields[$id][$field];
        }
        return $arrayToReturn;
    }

    private static $profileDeletedCache = array();

    public static function isProfileDeleted( $profile_id )
    {
        if( isset(self::$profileDeletedCache[(int)$profile_id]) )
        {
            return self::$profileDeletedCache[(int)$profile_id];
        }

        $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE . "` WHERE `profile_id`=?", $profile_id);
        self::$profileDeletedCache[$profile_id] = !( SK_MySQL::query($query)->fetch_cell() );
        return self::$profileDeletedCache[$profile_id];
    }

    public static function checkProfileDeletedForUsers( $profile_id_list )
    {
        $profile_id_list = array_unique($profile_id_list);

        if( empty($profile_id_list) )
        {
            return array();
        }

        $result = SK_MySQL::queryForList(SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE . "` WHERE `profile_id` IN (?@)", $profile_id_list));

        $foundIds = array();

        foreach ( $result as $item )
        {
            $foundIds[$item['profile_id']] = $item['profile_id'];
        }

        $returnArray = array();

        foreach ( $profile_id_list as $id )
        {
            self::$profileDeletedCache[$id] = !in_array($id, $foundIds);
            $returnArray[$id] = self::$profileDeletedCache[$id];
        }
        
        return $returnArray;
    }

    public static function unregisterProfile( $profile_id, $msg = '', $with_profile_content = 0 )
    {
        if ( !($profile_id = intval($profile_id)) )
            return false;

        $profile_details = app_Profile::getFieldValues($profile_id, array('username', 'email'));

        // delete profile from all profile list
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_LINK_PR_LIST_PR . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        // delete profile from membership claim
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_MEMBERSHIP_CLAIM . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        // delete from block list
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_BLOCK_LIST . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_BLOCK_LIST . "` WHERE `blocked_id`=?", $profile_id);
        SK_MySQL::query($query);

        // delete from friend list
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_FRIEND_LIST . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_FRIEND_LIST . "` WHERE `friend_id`=?", $profile_id);
        SK_MySQL::query($query);

        // delete from bookmark list
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_BOOKMARK_LIST . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_BOOKMARK_LIST . "` WHERE `bookmarked_id`=?", $profile_id);
        SK_MySQL::query($query);

        //delete profile claims
        SK_MySQL::query(SK_MySQL::placeholder("DELETE FROM `" . TBL_MEMBERSHIP_CLAIM . "` WHERE `profile_id`=? AND `claim_result`='claim'", $profile_id));

        //delete from forum lists
        app_Forum::deleteProfile($profile_id);

        // delete chuppo key
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_CHUPPO_ID . "`
			WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        // delete profile preferences
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_PREFERENCE_DATA . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_VIEW_HISTORY . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        //delete search lists
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_SEARCH_CRITERION . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        //delete site moderator
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_SITE_MODERATORS . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        // Facebook Connect
        require_once DIR_SITE_ROOT . 'facebook_connect' . DIRECTORY_SEPARATOR . 'init.php';
        FBC_AuthService::getInstance()->deleteByUserId($profile_id);

        // delete profile messages/kisses
        app_MailBox::deleteProfileMailboxMessages($profile_id);

        // delete profile's memberships
        app_Membership::onProfileUnregister($profile_id);
        app_Membership::deleteProfileMemberships($profile_id);

        if ( $with_profile_content == 1 )
        {
            app_BlogService::stDeleteProfilePosts($profile_id);
            app_CommentService::stDeleteEntityComments(FEATURE_PROFILE, $profile_id, ENTITY_TYPE_PROFILE_COMMENT);

            app_CommentService::stDeleteEntityComments(FEATURE_NEWSFEED, $profile_id, ENTITY_TYPE_PROFILE_AVATAR_CHANGE);
            app_CommentService::stDeleteEntityComments(FEATURE_NEWSFEED, $profile_id, ENTITY_TYPE_PROFILE_EDIT);
            app_CommentService::stDeleteEntityComments(FEATURE_NEWSFEED, $profile_id, ENTITY_TYPE_PROFILE_JOIN);
            app_CommentService::stDeleteEntityComments(FEATURE_NEWSFEED, $profile_id, ENTITY_TYPE_FRIEND_ADD);

            // Comments
            app_CommentService::newInstance(FEATURE_BLOG)->deleteProfileComments($profile_id, ENTITY_TYPE_BLOG_POST_ADD);
            app_CommentService::newInstance(FEATURE_EVENT)->deleteProfileComments($profile_id, ENTITY_TYPE_EVENT_ADD);

            app_CommentService::newInstance(FEATURE_PHOTO)->deleteProfileComments($profile_id, ENTITY_TYPE_PHOTO_UPLOAD);
            app_CommentService::newInstance(FEATURE_VIDEO)->deleteProfileComments($profile_id, ENTITY_TYPE_MEDIA_UPLOAD);
            app_CommentService::newInstance(FEATURE_PROFILE)->deleteProfileComments($profile_id, ENTITY_TYPE_PROFILE_COMMENT);
            app_CommentService::newInstance(FEATURE_CLASSIFIEDS)->deleteProfileComments($profile_id, ENTITY_TYPE_POST_CLASSIFIEDS_ITEM);
            app_CommentService::newInstance(FEATURE_GROUP)->deleteProfileComments($profile_id, ENTITY_TYPE_GROUP_ADD);

            app_CommentService::newInstance(FEATURE_MUSIC)->deleteProfileComments($profile_id, ENTITY_TYPE_MUSIC_UPLOAD);
        }

        $newsfeedActions = app_Newsfeed::newInstance()->findActionsByUserId($profile_id);

        foreach ( $newsfeedActions as $action )
        {
            app_Newsfeed::newInstance()->removeActionById($action->getId());
        }

        // delete profile photos
        $query = SK_MySQL::placeholder("SELECT `photo_id` FROM `" . TBL_PROFILE_PHOTO . "` WHERE `profile_id`=?", $profile_id);
        $result = SK_MySQL::query($query);

        while ( $photo_id = $result->fetch_cell() )
        {
            app_ProfilePhoto::delete($photo_id);
        }

        app_ProfileVideo::deleteProfileVideos($profile_id);
        app_ProfileMusic::deleteProfileMusics($profile_id);

        app_RateService::stDeleteProfileRates($profile_id);

        app_Groups::deleteProfileGroupParticipation($profile_id);

        // delete from registration invite table
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_REGISTER_INVITE_CODE . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        // delete from main profile info tables
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_EXTEND . "` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        //update referrals referrer `total_referrals` field -=-=-=-=-=-=-

        $query = SK_MySQL::placeholder("SELECT `referrer_id`, `paid` FROM `" . TBL_REFERRAL_RELATION . "` WHERE `referral_id`=?", $profile_id);
        $referral_info = SK_MySQL::query($query)->fetch_assoc();

        if ( $referral_info['referrer_id'] )
        {
            $query = SK_MySQL::placeholder("SELECT `total_referrals`, `total_purchaser_referrals` FROM `" . TBL_REFERRAL . "` WHERE `referral_id`=?
			", $referral_info['referrer_id']);
            $referrer_info = SK_MySQL::query($query)->fetch_assoc();
            $total_referrals = $referrer_info['total_referrals'] - 1;
            $total_purchaser_referrals = ($referral_info['paid'] == 'y') ? $referrer_info['total_purchaser_referrals'] - 1 : $referrer_info['total_purchaser_referrals'];

            $query = SK_MySQL::placeholder("UPDATE `" . TBL_REFERRAL . "` SET `total_referrals`=?, `total_purchaser_referrals`=? WHERE `referral_id`=?",
                    $total_referrals, $total_purchaser_referrals, $referral_info['referrer_id']);
            SK_MySQL::query($query);
        }

        //delete referral relations
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_REFERRAL_RELATION . "` WHERE `referrer_id`=? OR `referral_id`=?", $profile_id, $profile_id);
        SK_MySQL::query($query);

        //delete from referrals
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_REFERRAL . "` WHERE `referral_id`=?", $profile_id);
        SK_MySQL::query($query);
        app_ProfileComponentService::stDeleteProfileViewInfo($profile_id);
        app_EventService::newInstance()->deleteProfileAttendance($profile_id);
        //app_EventService::stDeleteProfileEvents($profile_id);


        self::Logoff($profile_id);

        $config = SK_Config::section("site")->Section("official");

        $mail = app_Mail::createMessage(app_Mail::NOTIFICATION)
                ->setRecipientEmail($config->site_email_main)
                ->setTpl('profile_unregister')
                ->assignVar('msg', $msg)
                ->assignVar('username', $profile_details['username'])
                ->assignVar('email', $profile_details['email']);
        app_Mail::send($mail);

        return true;
    }

    public static function Logoff( $profile_id, $agent = 'base' )
    {
        if ( !( $profile_id = (int) $profile_id ) )
            return false;

        $agent_cond = $agent == 'base' ? " AND `agent` = 'base'" : "";

        // delete profile from online table
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_ONLINE . "`
		  WHERE `profile_id`=?" . $agent_cond, $profile_id);
        SK_MySQL::query($query);

        $_tmp_list_id = app_TempProfileList::getListSessionInfo('search', 'list_id');

        if ( $_tmp_list_id )
        {
            // delete search result list id from tmp table
            $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_TMP_PR_LIST . "`
				WHERE `profile_list_id`=?", $_tmp_list_id);

            SK_MySQL::query($query);

            // delete all profile in search result list id
            $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_LINK_PR_LIST_PR . "`
				WHERE `profile_list_id`=?", $_tmp_list_id);

            SK_MySQL::query($query);
        }


        if ( $profile_id == SK_HttpUser::profile_id() )
        {
            // destroy login info in session
            SK_HttpUser::session_end();
        }
        return true;
    }

    public static function LogoffByUserIdList( $profile_list_id, $agent = 'base' )
    {
        if ( !( $profile_list_id ) )
            return false;

        $agent_cond = $agent == 'base' ? " AND `agent` = 'base'" : "";

        // delete profile from online table
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_ONLINE . "`
		  WHERE `profile_id` IN ( '?@' ) " . $agent_cond, $profile_list_id);
        SK_MySQL::query($query);

        $_tmp_list_id = app_TempProfileList::getListSessionInfo('search', 'list_id');

        if ( $_tmp_list_id )
        {
            // delete search result list id from tmp table
            $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_TMP_PR_LIST . "`
				WHERE `profile_list_id`=?", $_tmp_list_id);
            SK_MySQL::query($query);

            // delete all profile in search result list id
            $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_LINK_PR_LIST_PR . "`
				WHERE `profile_list_id`=?", $_tmp_list_id);
            SK_MySQL::query($query);
        }

        if ( $profile_id == SK_HttpUser::profile_id() )
        {
            // destroy login info in session
            SK_HttpUser::session_end();
        }
        return true;
    }

    public static function SqlActiveString( $alias_name = null )
    {
        $_alias = ( strlen(trim($alias_name)) ) ? "`$alias_name`." : '';
        $_return_str = (!SK_Config::section("site")->Section("additional")->Section("profile")->not_reviewed_profile_access ) ? "( $_alias`status`='active' AND $_alias`reviewed`='y' )" : "( $_alias`status`='active' )";
        return $_return_str;
    }

    /**
     * Returns age of profile
     *
     * @param string $birtdate
     * @return integer
     */
    public static function getAge( $birtdate )
    {
        $_date_info = explode('-', $birtdate);

        $_month_correct = ( ( date('j') - $_date_info[2] ) < 0 ) ? 1 : 0;
        $_year_correct = ( ( date('n') - $_date_info[1] - $_month_correct ) < 0 ) ? 1 : 0;

        return date("Y") - $_date_info[0] - $_year_correct;
    }

    /**
     * Returns Profile ID by specified profile Username
     *
     * @param string $username
     * @return integer|boolean
     */
    public static function getProfileIdByUsername( $username )
    {
        if ( !strlen(trim($username)) )
            return false;

        $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE . "`
			WHERE `username`='?'", $username);

        return (int) SK_MySQL::query($query)->fetch_cell();
    }

    /**
     * Update one field of profile.
     * Can update ony base field.
     * Returns affected rows of mysql query
     *
     * @param integer $profile_id
     * @param string $field_name
     * @param mixed $field_value
     * @return integer
     */
    public static function setFieldValue( $profile_id, $field_name, $field_value )
    {
        if ( !($profile_id = intval($profile_id)) )
        {
            return false;
        }

        $dao = SK_MySQL::describe(TBL_PROFILE, $field_name);

        $textual = array('varchar', 'text', 'date', 'enum');
        $field_pointer = in_array($dao->type(), $textual) ? "'?'" : "?";

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE . "` SET `$field_name` = $field_pointer
			WHERE `profile_id`=?", $field_value, $profile_id);
        SK_MySQL::query($query);
        return SK_MySQL::affected_rows();
    }

    /**
     * Generate profile activity information
     * Profile can be online or visit some time ago.
     * LastActivity display config is considered.
     * Returns arra with following items:<br />
     * <li>online - can be 1 or 0</li>
     * <li>item_num - number of items </li>
     * <li>item - type of item, can be: d ( day ); h ( hour ); m ( minute );</li>
     *
     * @param integer $activity_stamp
     * @param integer $online_status
     *
     * @return array
     */
    public static function ActivityInfo( $activity_stamp, $online_status, $admin_mode = false )
    {
        $_time_interval = time() - $activity_stamp;
        $config = SK_Config::section('site')->Section('additional')->Section('profile_list');

        $display_activity_mode = ( $admin_mode ) ? 'all' : $config->show_last_act;

        switch ( $display_activity_mode )
        {
            case 'day':
                if ( ( $_time_interval / 86400 ) > $config->show_last_act_day )
                {
                    $_info = array();
                    break;
                }
            case 'all':
                if ( $online_status )
                {
                    $_info['online'] = 1;
                    break;
                }
                if ( ( $_time_interval / 86400 ) >= 1 )
                {
                    $_info['item_num'] = floor($_time_interval / 86400);
                    $_info['item'] = 'd';
                }
                elseif ( ( $_time_interval / 3600 ) >= 1 )
                {
                    $_info['item_num'] = floor($_time_interval / 3600);
                    $_info['item'] = 'h';
                }
                elseif ( ( $_time_interval / 60 ) >= 1 )
                {
                    $_info['item_num'] = floor($_time_interval / 60);
                    $_info['item'] = 'm';
                }
                else
                {
                    $_info['item_num'] = 1;
                    $_info['item'] = 'm';
                }
                break;
            case 'off':

                $_info = array();
                break;
        }

        return $_info;
    }

    /**
     * Returns Membership Type id of profile with $profile_id or logged profile
     *
     * @param int $profile_id
     * @return int
     */
    public static function MembershipTypeId( $profile_id = null )
    {
        $profile_id = intval($profile_id) ? intval($profile_id) : SK_HttpUser::profile_id();

        if ( !intval($profile_id) )
            return 1;

        $res = (int) SK_MySQL::query(SK_MySQL::placeholder("SELECT `membership_type_id` FROM `" . TBL_PROFILE . "` WHERE `profile_id`=?", $profile_id))->fetch_cell();

        return $res ? $res : 1;
    }

//--------------------------------------< Remember Me >---------------------------------------------------------

    public static function deleteCookiesLogin( $username )
    {
        SK_HttpUser::unset_cookie('_username');
        SK_HttpUser::unset_cookie('_unique');
        $query = SK_MySQL::placeholder('DELETE FROM `' . TBL_COOKIES_LOGIN . '` WHERE `username`="?"', $username);
        SK_MySQL::query($query);
    }

    public static function checkCookiesLogin( $username, $unique )
    {
        $query = SK_MySQL::placeholder('SELECT `username` FROM `' . TBL_COOKIES_LOGIN . '` WHERE `username`="?" AND `unique` = "?"', $username, $unique);
        $username = SK_MySQL::query($query)->fetch_cell();
        return $username;
    }

    public static function cookiesLogin( $username )
    {
        $query = SK_MySQL::placeholder('SELECT `password` FROM `' . TBL_PROFILE . '` WHERE `username`="?"', $username);
        $password = SK_MySQL::query($query)->fetch_cell();
        SK_HttpUser::authenticate($username, $password, false);
        self::deleteCookiesLogin($username);
        self::setLoginCookies($username);
    }

    public static function setLoginCookies( $username )
    {
        self::deleteCookiesLogin($username);

        $unique = self::getCookiesLoginUniqueValue();
        $expiratinTimestamp = self::getCookiesLoginExpTS();

        SK_HttpUser::set_cookie('_username', $username, $expiratinTimestamp);
        SK_HttpUser::set_cookie('_unique', $unique, $expiratinTimestamp);

        $query = SK_MySQL::placeholder('INSERT INTO `' . TBL_COOKIES_LOGIN . '`(`username`, `unique`) VALUES("?", "?")', $username, $unique);
        SK_MySQL::query($query);
    }

    private static function getCookiesLoginUniqueValue()
    {
        return md5(uniqid(rand(), 1));
    }

    private static function getCookiesLoginExpTS()
    {
        return ((int) SK_Config::section('site')->Section('additional')->Section('profile')->cookies_auth_exp_period_days) * 24 * 60 * 60;
    }

//--------------------------------------< / Remember Me >---------------------------------------------------------

    public static function updateProfileJoinIP( $profile_id, $ip = null )
    {
        $detectedIp = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        $ip = ( $ip ) ? $ip : $detectedIp;

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE . "`
			SET `join_ip`=INET_ATON('?') WHERE `profile_id`=?", $ip, $profile_id);
        SK_MySQL::query($query);
        return SK_MySQL::insert_id();
    }

    public static function getActivityExpirationTime()
    {
        return time() + SK_Config::section('site')->Section('additional')->Section('profile')->member_logout_sec;
    }

    public static function updateProfileActivity( $profile_id = null )
    {
        if ( !$profile_id )
        {
            $session_id = SK_HttpUser::session_id();
            if ( !isset($session_id) )
                return false;

            // check if profile online
            $query = "SELECT `profile_id` FROM `" . TBL_PROFILE_ONLINE . "` WHERE `hash`='{$session_id}'";
            $profile_id = SK_MySQL::query($query)->fetch_cell();
        }

        if ( !$profile_id || SK_HttpRequest::isIpBlocked() )
        {
            SK_HttpUser::logoff();
            return false;
        }
        else
        {
            $query = "UPDATE LOW_PRIORITY `" . TBL_PROFILE . "` SET `activity_stamp`='" . time() . "' WHERE `profile_id`='" . $profile_id . "'";
            SK_MySQL::query($query);

            $query = "UPDATE LOW_PRIORITY `" . TBL_PROFILE_ONLINE . "` SET `expiration_time`='" . self::getActivityExpirationTime() . "' WHERE `profile_id`='$profile_id'";
            SK_MySQL::query($query);

            return true;
        }
    }

    public static function updateProfileStatus( $profile_id, $status )
    {
        $profile_id = intval($profile_id);

        if ( !$profile_id || !in_array($status, array('active', 'on_hold', 'suspended')) )
            return false;

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE . "` SET `status`='?'
			WHERE `profile_id`=?", $status, $profile_id);

        SK_MySQL::query($query);

        return SK_MySQL::affected_rows();
    }

    /**
     * Change profile password in database
     * Returns affected rows or error code:<br />
     * <li>-1 if incorrect old password</li>
     * <li>-2 if new password is incorrect</li>
     * <li>-3 in error confirmation</li>
     *
     * @param integer $profile_id
     * @param string $old_pass
     * @param string $new_page
     * @param string $confirm_pass
     *
     * @return integer
     */
    public static function changePassword( $profile_id, $password )
    {
        if ( !($profile_id = intval($profile_id)) )
            return false;

        $password = app_Passwords::hashPassword($password);

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE . "` SET `password`='?'
			WHERE `profile_id`=?", $password, $profile_id);

        SK_MySQL::query($query);
        return (bool) SK_MySQL::affected_rows();
    }

    private static $isOnlineCache = array();

    public static function isOnline( $profile_id )
    {
        $profile_id = intval($profile_id);

        if( !$profile_id )
        {
            return false;
        }

        if( !isset(self::$isOnlineCache[$profile_id]) )
        {
            $query = SK_MySQL::placeholder("SELECT `hash` FROM `" . TBL_PROFILE_ONLINE . "` WHERE `profile_id`=?", $profile_id);
            self::$isOnlineCache[$profile_id] = (SK_MySQL::query($query)->fetch_cell()) ? true : false;
            
        }

        return self::$isOnlineCache[$profile_id];
    }

    public static function getOnlineStatusForUsers( array $profileIdList )
    {
        if( empty($profileIdList) )
        {
            return array();
        }


        $profileIdList = array_unique($profileIdList);

        $profileIdList = array_diff($profileIdList, array_keys(self::$isOnlineCache));

        if( !empty($profileIdList) )
        {
            $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE_ONLINE . "` WHERE `profile_id` IN (?@)", $profileIdList);
            $result = SK_MySQL::queryForList($query);

            $foundProfiles = array();

            foreach ( $result as $item )
            {
                $foundProfiles[] = $item['profile_id'];
            }
        }

        $returnArray = array();

        foreach ( $profileIdList as $id )
        {
            self::$isOnlineCache[$id] = $returnArray[$id] = in_array($id, $foundProfiles) ? true : false;
        }

        return $returnArray;
    }

    public static function reviewed( $profile_id = null )
    {
        $profile_id = isset($profile_id) ? $profile_id : SK_HttpUser::profile_id();
        $reviewed = self::getFieldValues($profile_id, 'reviewed');

        return ( $reviewed == 'y' );
    }

    public static function suspended( $profile_id = null )
    {
        $profile_id = isset($profile_id) ? $profile_id : SK_HttpUser::profile_id();
        $staus = self::getFieldValues($profile_id, 'status');

        return ( $staus == 'suspended' );
    }

    public static function email_verified( $profile_id = null )
    {
        $profile_id = isset($profile_id) ? $profile_id : SK_HttpUser::profile_id();

        $email_verified = self::getFieldValues($profile_id, 'email_verified');

        $config = SK_Config::section('site')->Section('additional')->Section('profile');

        if ( !$config->allow_emailverify_no_access && $email_verified == 'no' )
            return false;

        if ( !$config->allow_emailverify_undefined_access && $email_verified == 'undefined' )
            return false;

        return true;
    }

    public static function isProfileModerator( $profile_id )
    {
        $query = SK_MySQL::placeholder(
                "SELECT COUNT(`profile_id`)
				FROM `" . TBL_SITE_MODERATORS . "`
				WHERE `profile_id`=?"
                , $profile_id
        );

        return (bool) SK_MySQL::query($query)->fetch_cell();
    }

    /**
     * Sends birthday congr. mail
     *
     * @return void
     */
    public static function sendBirthdayCgts()
    {
        $profiles_list = app_ProfileList::BirthdayProfiles();

        foreach ( $profiles_list as $profile )
        {
            if ( !app_Unsubscribe::isProfileUnsubscribed($profile['profile_id']) )
            {
                // send notify mail
                $msg = app_Mail::createMessage()
                        ->setRecipientEmail($profile['email'])
                        ->setRecipientLangId($profile['language_id'])
                        ->setTpl('send_birthday_congratulation')
                        ->assignVar('username', $profile['username']);
                app_Mail::send($msg);
            }

            // give credits
            app_UserPoints::earnCreditsForAction($profile['profile_id'], 'birthday');
        }
    }

    public static function getProfileChuppoGender( $profile_sex )
    {
        $profile_sex = intval($profile_sex);

        if ( !$profile_sex )
            return false;

        $query = SK_MySQL::placeholder("SELECT `chuppo_gender` FROM `" . TBL_LINK_CHUPPO_GENDER . "`
			WHERE `sex`=?", $profile_sex);

        return SK_MySQL::query($query)->fetch_cell();
    }

    public static function defineProfileChuppoKey( $profile_id )
    {
        $profile_id = intval($profile_id);

        if ( !$profile_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `profile_chuppo_key` FROM `" . TBL_PROFILE_CHUPPO_ID . "`
			WHERE `profile_id`=?", $profile_id);
        $query_result = SK_MySQL::query($query);

        if ( !$query_result->fetch_cell() )
        {
            $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_PROFILE_CHUPPO_ID . "` ( `profile_id`, `profile_chuppo_key` )
				VALUES( ?, uuid() )", $profile_id);

            SK_MySQL::query($query);
        }

        return true;
    }

    public static function getProfileIdByUserKey( $userkey )
    {
        $userkey = trim($userkey);

        if ( !$userkey )
            return false;

        $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `" . TBL_PROFILE_CHUPPO_ID . "`
			WHERE `profile_chuppo_key`='?' LIMIT 1", $userkey);

        return SK_MySQL::query($query)->fetch_cell();
    }

    public static function getUserKeyByProfileId( $profileId )
    {
        $profileId = intval($profileId);

        if ( !$profileId )
            return false;

        $query = SK_MySQL::placeholder("SELECT `profile_chuppo_key` FROM `" . TBL_PROFILE_CHUPPO_ID . "`
            WHERE `profile_id`=? LIMIT 1", $profileId);

        return SK_MySQL::query($query)->fetch_cell();
    }

    /**
     * Get profile href.
     *
     * @param integer $profile_id
     * @return string
     */
    public static function href( $profile_id )
    {
        return SK_Navigation::href('profile', array('profile_id' => $profile_id));
    }

    public static function updateUserStatus( $profile_id, $user_status )
    {
        if ( !(
            $profile_id = (int) $profile_id
            ) )
        {
            throw new Exception('invalid argument $profile_id');
        }

        if ( SK_MySQL::query(
                'SELECT `status` FROM `' . TBL_USER_STATUS . '`
				WHERE `profile_id`=' . $profile_id
            )->num_rows()
        )
        {
            $query =
                'UPDATE `' . TBL_USER_STATUS . '`
					SET `status`="?"
					WHERE `profile_id`=' . $profile_id;
        }
        else
        {
            $query =
                'INSERT INTO `' . TBL_USER_STATUS . '`
					SET `profile_id`=' . $profile_id . ', `status`="?"';
        }

        SK_MySQL::query(
                SK_MySQL::placeholder($query, $user_status)
        );

        if ( SK_MySQL::affected_rows() )
        {
            // tracing status update
            $user_action = new SK_UserAction('status_update', $profile_id);
            $user_action->user_status = $user_status;
            $user_action->status = 'active';

            app_UserActivities::trace_action($user_action);


            //$status = app_TextService::stHandleSmiles(SK_Language::htmlspecialchars($user_status));
            $status = SK_Language::htmlspecialchars($user_status);
            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            {
                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_NEWSFEED,
                        'entityType' => 'status_update',
                        'entityId' => SK_HttpUser::profile_id(),
                        'userId' => SK_HttpUser::profile_id(),
                        'replace' => true
                    ),
                    'data' => array(
                        'string' => $status
                    )
                );

                app_Newsfeed::newInstance()->action($newsfeedDataParams);
            }



            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get online user status.
     *
     * @param integer $profile_id
     * @return string user status, NULL if status not set or FALSE if a profile user is offline.
     */
    public static function getUserStatus( $profile_id )
    {
        if ( !(
            $profile_id = (int) $profile_id
            ) )
        {
            throw new Exception('invalid argument $profile_id');
        }

        $result = SK_MySQL::query(
                'SELECT `status` FROM `' . TBL_USER_STATUS . '`
				WHERE `profile_id`=' . $profile_id
        );

        return $result->fetch_cell();
    }

    public static function getUserStatusForList( $profileIdList )
    {
        $query = SK_MYSQL::placeholder("SELECT `status`, `profile_id` FROM `" . TBL_USER_STATUS . "` WHERE `profile_id` IN (?@)", $profileIdList);
        $result = SK_MySQL::queryForList($query);

        $resultArray = array();

        foreach ( $result as $item )
        {
            $resultArray[$item['profile_id']] = $item['status'];
        }

        $returnArray = array();

        foreach ( $profileIdList as $id )
        {
            $returnArray[$id] = isset($resultArray[$id]) ? $resultArray[$id] : '';
        }

        return $returnArray;
    }

    public static function getUrl( $profile_id )
    {
        if ( !(
            $profile_id = (int) $profile_id
            ) )
        {
            $profile_id = SK_HttpUser::profile_id();
        }

        return SK_Navigation::href('profile', array('profile_id' => $profile_id));
    }

    public static function findIdByUsername( $username )
    {
        return MySQL::fetchField(sql_placeholder("SELECT profile_id FROM `?#TBL_PROFILE` WHERE `username`=?", $username));
    }

    public static function isFacebookUser( $profileId = null )
    {
        $profileId = empty($profileId) ? SK_HttpUser::profile_id() : $profileId;

        $tbl = DB_TBL_PREFIX . 'fbconnect_auth';
        $query = SK_MySQL::placeholder('SELECT id FROM ' . $tbl . ' WHERE `userId`=?', $profileId);

        return (bool) SK_MySQL::query($query)->fetch_cell();
    }

    public static function isProfileFieldsCompleted( $profileId = null )
    {
        $profileId = empty($profileId) ? SK_HttpUser::profile_id() : $profileId;

        $t = app_FieldForm::getRequredFields($profileId);

        return self::isFieldsCompleted($profileId, $t);
    }

    public static function isFieldsCompleted( $profileId, $fields )
    {
        $requiredFields = array();
        foreach ( $fields as $f )
        {
            if ( in_array($f, array('location', 'photo_upload')) )
            {
                continue;
            }
            $requiredFields[] = $f;
        }

        $values = app_Profile::getFieldValues($profileId, $requiredFields);

        foreach ( $values as $f => $v )
        {
            if ( !trim($v) )
            {
                return false;
            }
        }

        return true;
    }
}