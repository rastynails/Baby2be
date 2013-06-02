<?php

/**
 * Class implementing Groups feature
 *
 */
class app_Groups
{

    private static $groupMemberCountCache = array();
    private static $photoUrlCache = array();
    private static $groupsMember = array();
    private static $isGroupsCreator = array();
    private static $group = array();

    /**
     * Returns group list
     *
     * @param int $page
     * @param string $type
     * @return array
     */
    public static function getGroupList( $page, $type = 'active' )
    {
        $page = isset($page) ? $page : 1;
        $per_page = SK_Config::Section('site')->Section('additional')->Section('groups')->result_per_page;
        $limit = " LIMIT " . ( ( $page - 1 ) * $per_page ) . ",$per_page";

        switch ( $type )
        {
            case 'all':
                $query = SK_MySQL::placeholder("SELECT `g`.*, `p`.`profile_id`, `p`.`profile_id`
					FROM `" . TBL_GROUP . "` AS `g`
					LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`g`.`owner_id` = `p`.`profile_id`)
					ORDER BY `g`.`creation_stamp` DESC $limit");
                $res = SK_MySQL::query($query);
                break;

            case 'active':
            case 'approval':
                $query = SK_MySQL::placeholder("SELECT `g`.*, `p`.`profile_id`, `p`.`profile_id`
					FROM `" . TBL_GROUP . "` AS `g`
					LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`g`.`owner_id` = `p`.`profile_id`)
					WHERE `g`.`status`='?'
					ORDER BY `g`.`creation_stamp` DESC $limit", $type);
                $res = SK_MySQL::query($query);
                break;
        }

        $groups = array();
        $rowList = array();
        $profileIdList = array();
        $groupIdList = array();
        $groupPhotoList = array();

        while ( $row = $res->fetch_assoc() )
        {
            $rowList[] = $row;
            $profileIdList[] = $row['profile_id'];
            $groupIdList[] = $row['group_id'];
            $groupPhotoList[$row['group_id']] =  $row['photo'];
        }

        self::getGroupImageUrlList($groupPhotoList);
        self::getGroupMembersCountList($groupIdList);
        app_Profile::getUsernamesForUsers($profileIdList);

        foreach( $rowList as $row )
        {
            $row['members_count'] = self::getGroupMembersCount($row['group_id']);
            $row['username'] = app_Profile::username($row['profile_id']);
            $row['thumb'] = self::getGroupImageURL($row['group_id'], $row['photo']);
            self::$group[$row['group_id']] = $row;
            $groups[] = $row;
        }
        
        return $groups;
    }

    /**
     * Count all groups
     *
     * @param string $status
     * @return int
     */
    public static function getGroupsCount( $status = 'active' )
    {
        switch ( $status )
        {
            case 'active':
                $cond = "`status`='active'";
                break;

            case 'approval':
                $cond = "`status`='approval'";
                break;

            case 'all':
            default:
                $cond = "1";
        }
        $query = "SELECT COUNT(`group_id`) FROM `" . TBL_GROUP . "` WHERE " . $cond;

        $count = SK_MySQL::query($query)->fetch_cell();

        return $count;
    }

    /**
     * Returns the number of members in the group
     *
     * @param int $group_id
     * @return int
     */
    public static function getGroupMembersCount( $group_id )
    {
        if ( !$group_id )
        {
            return false;
        }

        if ( isset(self::$groupMemberCountCache[$group_id]) )
        {
            return self::$groupMemberCountCache[$group_id];
        }

        $query = SK_MySQL::placeholder("SELECT COUNT(`g`.`group_id`)
			FROM `" . TBL_GROUP . "` AS `g`
			LEFT JOIN `" . TBL_GROUP_MEMBER . "` AS `gm` ON (`g`.`group_id`=`gm`.`group_id`)
			LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`p`.`profile_id`=`gm`.`member_id`)
			WHERE `g`.`group_id`=?", $group_id);

        $count = SK_MySQL::query($query)->fetch_cell();
        self::$groupMemberCountCache[$group_id] = $count;

        return $count;
    }

    public static function getGroupMembersCountList( $group_id_list )
    {
        if ( empty($group_id_list) )
            return array();

        $query = SK_MySQL::placeholder("SELECT `g`.`group_id`, COUNT(`gm`.`member_id`) as `count`
			FROM `" . TBL_GROUP . "` AS `g`
			LEFT JOIN `" . TBL_GROUP_MEMBER . "` AS `gm` ON (`g`.`group_id`=`gm`.`group_id`)
			LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`p`.`profile_id`=`gm`.`member_id`)
			WHERE `g`.`group_id` IN ( ?@ ) GROUP BY `g`.`group_id` ", $group_id_list);

        $result = SK_MySQL::query($query);
        $countList = array();
        while ( $row = $result->fetch_assoc() )
        {
            $countList[$row['group_id']] = $row['count'];
            self::$groupMemberCountCache[$row['group_id']] = $row['count'];
        }

        return $countList;
    }

    /**
     * Add group
     *
     * @param int $owner_id
     * @param string $title
     * @param string $description
     * @param string $browse_type
     * @param string $join_type
     * @param string $photo
     * @return int | boolean
     */
    public static function AddGroup( $owner_id, $title, $description, $browse_type, $join_type, $photo = null )
    {
        if ( !$owner_id )
            return false;

        $img_id = !empty($photo) ? rand(0, 99999) : null;
        $status = SK_Config::section('site')->Section('automode')->get('set_active_group_on_creation') ? 'active' : 'approval';

        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_GROUP . "`
			(`owner_id`, `title`, `description`, `browse_type`, `join_type`, `creation_stamp`, `photo`, `status`, `allow_claim`)
			VALUES (?, '?', '?', '?', '?', ?, ?, '?', ?)", $owner_id, $title, $description, $browse_type, $join_type['select'], time(), $img_id, $status, $join_type['checkbox']);

        SK_MySQL::query($query);
        $group_id = SK_MySQL::insert_id();

        if ( isset($group_id) && intval($group_id) )
        {
            if ( !empty($photo) )
            {
                try
                {
                    $img_saved = self::saveGroupImage($group_id, $photo, $img_id);
                }
                catch ( SK_ImageException $e )
                {
                    $img_saved = false;
                }
            }

            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            {
                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_GROUP,
                        'entityType' => ENTITY_TYPE_GROUP_ADD,
                        'entityId' => $group_id,
                        'userId' => SK_HttpUser::profile_id(),
                        'status' => $status
                    )
                );
                app_Newsfeed::newInstance()->action($newsfeedDataParams);
            }

            self::addGroupMember($owner_id, $group_id);
            self::addGroupForum($group_id);

            return $group_id;
        }

        return false;
    }

    /**
     * Add member to the group
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function addGroupMember( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_GROUP_MEMBER . "`
			WHERE `member_id`=? AND `group_id`=?", $profile_id, $group_id);

        if ( SK_MySQL::query($query)->fetch_assoc() )
            return -1;

        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_GROUP_MEMBER . "` (`member_id`, `group_id`, `join_stamp`)
			VALUES(?, ?, ?)", $profile_id, $group_id, time());

        SK_MySQL::query($query);

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_GROUP,
                    'entityType' => 'group_join',
                    'entityId' => $group_id,
                    'userId' => SK_HttpUser::profile_id(),
                    'replace' => true
                )
            );
            app_Newsfeed::newInstance()->action($newsfeedDataParams);
        }

        return SK_MySQL::affected_rows() ? true : false;
    }

    /**
     * Returns group all members
     *
     * @param int $group_id
     * @param boolean $ids_only
     * @return array
     */
    public static function getGroupMembers( $group_id, $ids_only = false )
    {
        if ( !$group_id )
            return false;

        $members = array();

        if ( $ids_only )
        {
            $query = SK_MySQL::placeholder("SELECT `p`.`profile_id`
				FROM `" . TBL_GROUP_MEMBER . "`AS `gm`
				INNER JOIN `" . TBL_PROFILE . "` AS `p` ON (`p`.`profile_id`=`gm`.`member_id`)
				LEFT JOIN  `" . TBL_GROUP . "` AS `g` ON (`g`.`group_id`=`gm`.`group_id`)
				WHERE `g`.`group_id`=? ORDER BY `gm`.`join_stamp` DESC", $group_id);
            $res = SK_MySQL::query($query);

            while ( $member = $res->fetch_cell() )
                $members[] = $member;
        }
        else
        {
            $query = SK_MySQL::placeholder("SELECT `p`.*
				FROM `" . TBL_GROUP_MEMBER . "`AS `gm`
				INNER JOIN `" . TBL_PROFILE . "` AS `p` ON (`p`.`profile_id`=`gm`.`member_id`)
				LEFT JOIN  `" . TBL_GROUP . "` AS `g` ON (`g`.`group_id`=`gm`.`group_id`)
				WHERE `g`.`group_id`=? ORDER BY `gm`.`join_stamp` DESC", $group_id);

            $res = SK_MySQL::query($query);
            while ( $member = $res->fetch_array() )
                $members[] = $member;
        }
        return $members;
    }

    /**
     * Return group members with paging
     *
     * @param int $group_id
     * @param int $page
     * @return array
     */
    public static function getMembersList( $group_id, $page = 1 )
    {
        if ( !$group_id )
            return false;

        $result = array();

        $query_parts['projection'] = "`p`.*, `online`.`hash` AS `online`, `g`.`group_id`, `gm`.*";
        $query_parts['left_join'] = "
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` ON(`p`.`profile_id` = `online`.`profile_id`)
			LEFT JOIN `" . TBL_GROUP_MEMBER . "`AS `gm` ON (`p`.`profile_id`=`gm`.`member_id`)
			LEFT JOIN `" . TBL_GROUP . "` AS `g` ON (`g`.`group_id`=`gm`.`group_id`) ";

        $query_parts['condition'] = SK_MySQL::placeholder("`g`.`group_id`=?", $group_id) . " AND " . app_Profile::SqlActiveString('p');
        $query_parts['order'] = "`gm`.`join_stamp` DESC";

        $page_limit = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
        ;
        $page = $page ? $page : 1;
        $query_parts['limit'] = ($page - 1) * $page_limit . ", " . $page_limit;

        $query = "SELECT {$query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `p`
		{$query_parts['left_join']}
			WHERE {$query_parts['condition']} GROUP BY `p`.`profile_id`" .
            ( (strlen($query_parts['order'])) ? " ORDER BY {$query_parts['order']}" : "" ) .
            " LIMIT {$query_parts['limit']}";

        $res = SK_MySQL::query($query);

        while ( $member = $res->fetch_assoc() )
            $result['profiles'][] = $member;

        // get total profiles
        $query = "SELECT COUNT( `p`.`profile_id` ) FROM `" . TBL_PROFILE . "` AS `p`
			LEFT JOIN `" . TBL_GROUP_MEMBER . "`AS `gm` ON (`p`.`profile_id`=`gm`.`member_id`)
			LEFT JOIN  `" . TBL_GROUP . "` AS `g` ON (`g`.`group_id`=`gm`.`group_id`) WHERE " .
            $query_parts['condition'];

        $result['total'] = SK_MySQL::query($query)->fetch_cell();

        return $result;
    }

    /**
     * Returns group info by group ID
     *
     * @param int $group_id
     * @return array
     */
    public static function getGroupById( $group_id )
    {
        if ( !$group_id )
            return false;

        if ( isset(self::$group[$row['group_id']]) )
        {
            return self::$group[$row['group_id']];
        }

        $query = SK_MySQL::placeholder("SELECT `g`.*, `p`.`profile_id`
			FROM `" . TBL_GROUP . "` AS `g`
			LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON ( `g`.`owner_id` = `p`.`profile_id` )
			WHERE `g`.`group_id` = ?", $group_id);

        $res = SK_MySQL::query($query)->fetch_assoc();

        if ( isset($res) )
            $res['members_count'] = self::getGroupMembersCount($group_id);
        $res['username'] = app_Profile::username($res['profile_id']);

        return $res;
    }

    /**
     * Returns urls to group image and thumbnail
     *
     * @param int $group_id
     * @param int $photo_id
     * @param boolean $thumb
     * @return string
     */
    public static function getGroupImageURL( $group_id, $photo_id = null, $thumb = true )
    {
        if ( !$group_id )
        {
            return self::getGroupDefaultImageURL();
        }

        if ( !empty(self::$photoUrlCache[$group_id]) )
        {
            return self::$photoUrlCache[$group_id];
        }

        if ( $photo_id === null )
        {
            $query = SK_MySQL::placeholder("SELECT `photo` FROM `" . TBL_GROUP . "` WHERE `group_id`=?", $group_id);
            $photo_id = SK_MySQL::query($query)->fetch_cell();
        }

        if ( $photo_id == 0 )
        {
            return self::getGroupDefaultImageURL();
        }

        if ( $thumb )
            return URL_USERFILES . "thumb_group_" . $group_id . "_" . $photo_id . ".jpg";
        else
            return URL_USERFILES . "group_" . $group_id . "_" . $photo_id . ".jpg";
    }

    public static function getGroupImageUrlList( $photoList,$thumb = true )
    {
        if( empty($photoList) )
        {
            return array();
        }

        $groupsWithEmptyPhoto = array();
        foreach($photoList as $groupId => $photo )
        {
            if ( !$photo )
            {
                $groupsWithEmptyPhoto[$groupId] = $groupId;
            }
        }

        if ( !empty($groupsWithEmptyPhoto) )
        {
            $query = SK_MySQL::placeholder("SELECT `group_id`, `photo` FROM `" . TBL_GROUP . "` WHERE `group_id` IN ( ?@ )", $groupsWithEmptyPhoto);
            $result = SK_MySQL::query($query);

            while ( $row = $result->fetch_assoc() )
            {
                $photoList[$row['group_id']] = $row['photo'];
            }
        }

        $urlList = array();

        foreach ( $photoList as $group_id => $photo_id )
        {
            if( empty($photo_id) )
            {
                $urlList[$group_id] = self::getGroupDefaultImageURL();
            }
            else
            {
                if ( $thumb )
                    $urlList[$group_id] = URL_USERFILES . "thumb_group_" . $group_id . "_" . $photo_id . ".jpg";
                else
                    $urlList[$group_id] = URL_USERFILES . "group_" . $group_id . "_" . $photo_id . ".jpg";
            }

            self::$photoUrlCache[$group_id] = $urlList[$group_id];
        }

        return $urlList;
    }

    public static function getGroupDefaultImageURL()
    {
        return URL_LAYOUT . SK_Layout::theme_dir(true) . 'img/group.jpg';
    }

    /**
     * Adds group moderator
     *
     * @param int $member_id
     * @param int $group_id
     * @return boolean
     */
    public static function addGroupModerator( $member_id, $group_id )
    {
        if ( !$member_id || !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `member_id` FROM `" . TBL_GROUP_MODERATOR . "`
			WHERE `member_id`=? AND `group_id`=?", $member_id, $group_id);

        if ( !SK_MySQL::query($query)->fetch_cell() )
        {
            $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_GROUP_MODERATOR . "`
				(`member_id`, `group_id`) VALUES (?, ?)", $member_id, $group_id);

            SK_MySQL::query($query);
            if ( SK_MySQL::affected_rows() )
                return true;
        }
        return false;
    }

    /**
     * Removes group moderator
     *
     * @param int $member_id
     * @param int $group_id
     * @return boolean
     */
    public static function removeGroupModerator( $member_id, $group_id )
    {
        if ( !$member_id || !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `member_id` FROM `" . TBL_GROUP_MODERATOR . "`
			WHERE `member_id`=? AND `group_id`=?", $member_id, $group_id);

        if ( SK_MySQL::query($query)->fetch_cell() )
        {
            $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP_MODERATOR . "`
				WHERE `member_id`=? AND `group_id`=?", $member_id, $group_id);

            SK_MySQL::query($query);
            if ( SK_MySQL::affected_rows() )
                return true;
        }
        else
            return false;
    }

    public static function removeGroupMember( $member_id, $group_id )
    {
        if ( !$member_id || !$group_id )
            return false;

        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP_MEMBER . "`
            WHERE `member_id`=? AND `group_id`=?", $member_id, $group_id);

        SK_MySQL::query($query);

        return (bool) SK_MySQL::affected_rows();
    }

    /**
     * Parses string with usernames and makes profiles moderators
     *
     * @param int $group_id
     * @param string $moderators
     * @return int | boolean
     */
    public static function addModerators( $group_id, $moderators )
    {
        if ( !$group_id || !strlen($moderators) )
            return false;

        $mod_arr = explode(",", $moderators);
        $counter = 0;

        if ( count($mod_arr) )
        {
            foreach ( $mod_arr as $mod )
            {
                $mod = trim($mod);
                $profile_id = app_Profile::getProfileIdByUsername($mod);
                if ( $profile_id )
                {
                    if ( self::addGroupModerator($profile_id, $group_id) )
                    {
                        if ( !self::isGroupMember($profile_id, $group_id) )
                            self::addGroupMember($profile_id, $group_id);

                        $counter++;
                    }
                }
            }
            return $counter > 0 ? $counter : false;
        }
        return false;
    }

    /**
     * Returns moderators of the group
     *
     * @param int $group_id
     * @return array
     */
    public static function getModerators( $group_id )
    {
        if ( !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `m`.*, `p`.`username`, `p`.`profile_id` FROM `" . TBL_GROUP_MODERATOR . "` AS `m`
			LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`m`.`member_id`=`p`.`profile_id`)
			LEFT JOIN `" . TBL_GROUP . "` AS `g` ON (`g`.`group_id`=`m`.`group_id`)
			WHERE `g`.`group_id`=?", $group_id);

        $res = SK_MySQL::query($query);
        $moders = array();

        $list = array();
        $profileIdList = array();
        while ( $row = $res->fetch_assoc() )
        {
            $list[] = $row;
            $profileIdList[] = $row['profile_id'];
        }
        
        app_Profile::getUsernamesForUsers($profileIdList);
                
        foreach ( $list as $row )
        {
            $row['username'] = app_Profile::username($row['profile_id']);
            $row['href'] = SK_Navigation::href('profile', array('profile_id' => $row['profile_id']));
            $moders[] = $row;
        }
        
        return $moders;
    }

    /**
     * Checks if profile is a group member
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function isGroupMember( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        if( isset(self::$groupsMember[$group_id][$profile_id]) )
        {
            return self::$groupsMember[$group_id][$profile_id];
        }

        $query = SK_MySQL::placeholder("SELECT `member_id` FROM `" . TBL_GROUP_MEMBER . "`
			WHERE `group_id`=? AND `member_id`=?", $group_id, $profile_id);

        $member = SK_MySQL::query($query)->fetch_cell();

        self::$groupsMember[$group_id][$profile_id] = $member > 0 ? true : false;
        
        return $member > 0 ? true : false;
    }

    /**
     * Checks if profile is group creator
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function isGroupCreator( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        if ( isset(self::$isGroupsCreator[$group_id][$profile_id]) )
        {
            return self::$isGroupsCreator[$group_id][$profile_id];
        }

        $query = SK_MySQL::placeholder("SELECT `owner_id` FROM `" . TBL_GROUP . "`
			WHERE `group_id`=?", $group_id);

        $creator = SK_MySQL::query($query)->fetch_cell();

        self::$isGroupsCreator[$group_id][$profile_id] = (strlen($creator) && (int) $creator == $profile_id) ? true : false;

        return (strlen($creator) && (int) $creator == $profile_id) ? true : false;
    }

    /**
     * Get group creator
     *
     * @param int $group_id
     * @return int
     */
    public static function getGroupCreator( $group_id )
    {
        if ( !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `owner_id` FROM `" . TBL_GROUP . "`
			WHERE `group_id`=?", $group_id);

        return SK_MySQL::query($query)->fetch_cell();
    }

    /**
     * Checks if profile is group moderator
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function isGroupModerator( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `member_id` FROM `" . TBL_GROUP_MODERATOR . "`
			WHERE `group_id`=? AND `member_id`=?", $group_id, $profile_id);

        $res = SK_MySQL::query($query);

        return $res->num_rows() ? true : false;
    }

    /**
     * Update group
     *
     * @param int $group_id
     * @param string $title
     * @param string $description
     * @param string $browse_type
     * @param string $join_type
     * @param string $photo
     * @return int | boolean
     */
    public static function updateGroup( $group_id, $title, $description, $browse_type, $join_type, $photo = null )
    {
        if ( !$group_id )
            return false;

        $status = 'active';

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_GROUP . "`
			SET `title`='?', `description`='?', `browse_type`='?', `join_type`='?', `status`='?', `allow_claim`=?
			WHERE `group_id`=?", $title, $description, $browse_type, $join_type['select'], $status, $join_type['checkbox'], $group_id);

        SK_MySQL::query($query);

        if ( isset($photo) )
        {
            try
            {
                self::updateGroupImage($group_id, $photo);
            }
            catch ( SK_ImageException $e )
            {
                return -1;
            }
        }

        return true;
    }

    /**
     * Sets group image.
     * Returns the name of group image file
     *
     * @param int $group_id
     * @param string $file_code
     * @param int $img_id
     * @return string
     */
    public static function saveGroupImage( $group_id, $file_code, $img_id )
    {
        $group_img = 'group_' . $group_id . '_' . $img_id . '.jpg';

        $options = array(
            'file_uniqid' => $file_code,
            'result_path' => DIR_USERFILES . $group_img,
            'result_image_type' => IMAGETYPE_JPEG
        );

        app_Image::upload($options);
        app_Image::resize($options['result_path'], 320, 1000);

        $group_thumb = 'thumb_group_' . $group_id . '_' . $img_id . '.jpg';

        $options_th = array(
            'file_uniqid' => $file_code,
            'result_path' => DIR_USERFILES . $group_thumb,
            'result_image_type' => IMAGETYPE_JPEG
        );

        app_Image::upload($options_th);
        app_Image::resize($options_th['result_path'], 90, 90, true);

        return $group_img;
    }

    /**
     * Changes group image.
     * Returns new name of group image file.
     *
     * @param int $group_id
     * @param string $file_code
     * @return string
     */
    public static function updateGroupImage( $group_id, $file_code )
    {
        $group = self::getGroupById($group_id);
        $img_id = $group['photo'];
        $new_id = rand(0, 99999);

        $old_img = 'group_' . $group_id . '_' . $img_id . '.jpg';
        $group_img = 'group_' . $group_id . '_' . $new_id . '.jpg';

        $options = array(
            'file_uniqid' => $file_code,
            'result_path' => DIR_USERFILES . $group_img,
            'result_image_type' => IMAGETYPE_JPEG
        );

        app_Image::upload($options);
        app_Image::resize($options['result_path'], 320, 1000);

        // remove old image
        if ( file_exists(DIR_USERFILES . $old_img) )
        {
            unlink(DIR_USERFILES . $old_img);
        }

        $old_thumb = 'thumb_group_' . $group_id . '_' . $img_id . '.jpg';
        $group_thumb = 'thumb_group_' . $group_id . '_' . $new_id . '.jpg';

        $options = array(
            'file_uniqid' => $file_code,
            'result_path' => DIR_USERFILES . $group_thumb,
            'result_image_type' => IMAGETYPE_JPEG
        );

        app_Image::upload($options);
        app_Image::resize($options['result_path'], 90, 90, true);

        // remove old thumb
        if ( file_exists(DIR_USERFILES . $old_thumb) )
        {
            unlink(DIR_USERFILES . $old_thumb);
        }

        self::updateGroupImageId($group_id, $new_id);

        return $group_img;
    }

    public static function deleteGroupImage( $group_id, $id )
    {
        $group_img = 'group_' . $group_id . '_' . $id . '.jpg';
        $thumb_img = 'thumb_group_' . $group_id . '_' . $id . '.jpg';

        if ( file_exists(DIR_USERFILES . $group_img) )
            unlink(DIR_USERFILES . $group_img);

        if ( file_exists(DIR_USERFILES . $thumb_img) )
            unlink(DIR_USERFILES . $thumb_img);

        self::updateGroupImageId($group_id, 0);
    }

    /**
     * Sets new id of group image
     *
     * @param int $group_id
     * @param int $id
     */
    private static function updateGroupImageId( $group_id, $id )
    {
        $query = SK_MySQL::placeholder("UPDATE `" . TBL_GROUP . "` SET `photo`=? WHERE `group_id`=?", $id, $group_id);
        SK_MySQL::query($query);
    }

    /**
     * Returns list of groups for index page
     *
     * @param string $listtype
     * @param int $count
     * @return array
     */
    public static function getIndexGroupList( $listtype, $count )
    {
        switch ( $listtype )
        {
            case 'latest':
                $query = SK_MySQL::placeholder("SELECT `g`.*, `p`.`profile_id`
					FROM `" . TBL_GROUP . "` AS `g`
					LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`g`.`owner_id` = `p`.`profile_id`)
					WHERE `g`.`status`='active'
					ORDER BY `g`.`creation_stamp` DESC LIMIT $count");
                $res = SK_MySQL::query($query);
                break;

            case 'most_popular':
                $query = SK_MySQL::placeholder("SELECT `g`.*, `p`.`profile_id`,
					COUNT(`gm`.`member_id`) AS `members_count`
					FROM `" . TBL_GROUP . "` AS `g`
					LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`g`.`owner_id` = `p`.`profile_id`)
					LEFT JOIN `" . TBL_GROUP_MEMBER . "` AS `gm` ON (`gm`.`group_id` = `g`.`group_id`)
					WHERE `g`.`status`='active'
					GROUP BY `gm`.`group_id`
					ORDER BY `members_count` DESC LIMIT $count");
                $res = SK_MySQL::query($query);
                break;
        }
        $groups = array();
        while ( $row = $res->fetch_assoc() )
        {
            $row['img'] = self::getGroupImageURL($row['group_id'], $row['photo']);
            $row['username'] = app_Profile::username($row['profile_id']);
            $row['thumb'] = self::getGroupImageURL($row['group_id'], $row['photo']);
            $groups[] = $row;
        }
        return $groups;
    }

    /**
     * Returns list of latest forum topics posted in group forum
     *
     * @param int $group_id
     * @return array
     */
    public static function getGroupForumLastTopicList( $group_id )
    {
        $configs = new SK_Config_Section('forum');

        $query = SK_MySQL::placeholder("SELECT `p`.`text`, `p`.`forum_post_id`, `p`.`profile_id`,
			`p`.`create_stamp`, `t`.`forum_topic_id`, `t`.`title`
			FROM `" . TBL_FORUM_POST . "` as `p`
			LEFT JOIN `" . TBL_FORUM_TOPIC . "` as `t` ON(`t`.`forum_topic_id`=`p`.`forum_topic_id`)
			LEFT JOIN `" . TBL_FORUM . "` as `f` ON(`f`.`forum_id`=`t`.`forum_id`)
			WHERE `f`.`group_id`=?
			ORDER BY `p`.`create_stamp` DESC LIMIT ?", $group_id, $configs->last_topic_count);
        $query_result = SK_MySQL::query($query);

        while ( $row = $query_result->fetch_assoc() )
        {
            $row['text'] = app_Forum::forumTagsToHtmlChars($row['text']);
            $row['text'] = strip_tags($row['text']);
            $row['title'] = app_TextService::stCensor($row['title'], 'forum', true);
            $row['is_deleted'] = app_Profile::isProfileDeleted($row['profile_id']);
            $row['username'] = app_Profile::username($row['profile_id']);
            $result[] = $row;
        }
        return $result;
    }

    /**
     * Starts forum for group
     *
     * @param int $group_id
     * @return int
     */
    public static function addGroupForum( $group_id )
    {
        $name = $group_id;

        #get max order
        $query = SK_MySQL::placeholder("SELECT MAX(`order`) FROM `" . TBL_FORUM . "`
			WHERE `forum_group_id`=0", $params['group_id']);
        $query_result = SK_MySQL::query($query);

        $order = $query_result->fetch_cell() + 1;

        // insert a new forum
        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_FORUM . "` (`forum_group_id`, `name`, `description`, `order`, `group_id`)
			VALUES(0, '?', '?', ?, ?)", $name, $name, $order, $group_id);
        SK_MySQL::query($query);
        $new_forum_id = SK_MySQL::insert_id();

        if ( !$new_forum_id )
        {
            return 0;
        }

        return $new_forum_id;
    }

    /**
     * Gets id of group forum
     *
     * @param int $group_id
     * @return int
     */
    public static function getGroupForumId( $group_id )
    {
        if ( !$group_id )
            return false;
        $query = SK_MySQL::placeholder("SELECT `forum_id` FROM `" . TBL_FORUM . "`
			WHERE `group_id`=? LIMIT 1", $group_id);

        return SK_MySQL::query($query)->fetch_cell();
    }

    /**
     * Returns group id by group forum id
     *
     * @param int $forum_id
     * @return int
     */
    public static function getGroupByForumID( $forum_id )
    {
        if ( !$forum_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `group_id` FROM `" . TBL_FORUM . "`
			WHERE `forum_id`=? LIMIT 1", $forum_id);

        return SK_MySQL::query($query)->fetch_cell();
    }

    /**
     * Returns group id by forum topic id
     *
     * @param int $topic_id
     * @return int
     */
    public static function getGroupByForumTopicID( $topic_id )
    {
        if ( !$topic_id )
            return false;

        $topic_info = app_Forum::getTopic($topic_id);
        $forum_id = $topic_info['forum_id'];

        $query = SK_MySQL::placeholder("SELECT `group_id` FROM `" . TBL_FORUM . "`
			WHERE `forum_id`=? LIMIT 1", $forum_id);

        return SK_MySQL::query($query)->fetch_cell();
    }

    /**
     * Checks if profile can view group
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function canView( $profile_id, $group_id )
    {
        if ( !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `browse_type` FROM `" . TBL_GROUP . "` WHERE `group_id`=?", $group_id);
        $type = SK_MySQL::query($query)->fetch_cell();

        return ($type == 'private' && !self::isGroupMember($profile_id, $group_id)) ? false : true;
    }

    /**
     * Checks if profile can join group
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function canJoin( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        if ( self::isGroupMember($profile_id, $group_id) )
            return false;

        $group = self::getGroupById($group_id);

        return $group['join_type'] == 'closed' ? false : true;
    }

    /**
     * Updates group's status
     *
     * @param int $group_id
     * @param string $status
     * @return boolean
     */
    public static function setGroupStatus( $group_id, $status )
    {
        if ( !$group_id || !strlen($status) || !in_array($status, array('active', 'approval', 'suspended')) )
            return false;

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_GROUP . "` SET `status`='?'
			WHERE `group_id`=?", $status, $group_id);

        SK_MySQL::query($query);

        $profile_id = self::getGroupCreator($group_id);

        switch ( $status )
        {
            case 'active':
            case 'approval':

                $rows = app_UserActivities::getWhere(" `type` IN('add_group', 'group_comment') AND `item`={$group_id}");

                $rows = is_array($rows) ? $rows : array();

                foreach ( $rows as $a )
                {
                    app_UserActivities::setStatus($a['skadate_user_activity_id'], $status);
                }



                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_GROUP,
                        'entityType' => ENTITY_TYPE_GROUP_ADD,
                        'entityId' => $group_id,
                        'userId' => $profile_id,
                        'status' => $status
                    )
                );
                app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);
                    
                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_GROUP,
                        'entityType' => ENTITY_TYPE_GROUP_JOIN,
                        'entityId' => $group_id,
                        'userId' => $profile_id,
                        'status' => $status
                    )
                );
                app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);
                break;

            case 'suspended':

                $aList = app_UserActivities::getWhere(" `type` IN('add_group', 'group_comment') AND `item`={$group_id}");
                $aList = is_array($aList) ? $aList : array();

                foreach ( $aList as $a )
                {
                    app_UserActivities::setStatus($a['skadate_user_activity_id'], 'approval');
                }

                    $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_GROUP,
                        'entityType' => ENTITY_TYPE_GROUP_ADD,
                        'entityId' => $group_id,
                        'userId' => $profile_id,
                        'status' => 'approval'
                    )
                );
                app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);

                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_GROUP,
                        'entityType' => ENTITY_TYPE_GROUP_JOIN,
                        'entityId' => $group_id,
                        'userId' => $profile_id,
                        'status' => 'approval'
                    )
                );
                app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);

                break;
        }

        return true;
    }

    /**
     * Block access to group
     *
     * @param int $member_id
     * @param int $group_id
     * @return boolean
     */
    public static function blockMember( $member_id, $group_id )
    {
        if ( !$member_id || !$group_id )
            return false;

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_GROUP_MEMBER . "`
			SET `is_blocked`=1 WHERE `group_id`=? AND `member_id`=?", $group_id, $member_id);

        SK_MySQL::query($query);
        return true;
    }

    /**
     * Unblock access to group
     *
     * @param int $member_id
     * @param int $group_id
     * @return boolean
     */
    public static function unblockMember( $member_id, $group_id )
    {
        if ( !$member_id || !$group_id )
            return false;

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_GROUP_MEMBER . "`
			SET `is_blocked`=0 WHERE `group_id`=? AND `member_id`=?", $group_id, $member_id);

        SK_MySQL::query($query);
        return true;
    }

    /**
     * Checks if profile was blocked in the group
     *
     * @param int $member_id
     * @param int $group_id
     * @return boolean
     */
    public static function isBlocked( $member_id, $group_id )
    {
        if ( !$member_id || !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `is_blocked` FROM `" . TBL_GROUP_MEMBER . "`
			WHERE `group_id`=? AND `member_id`=?", $group_id, $member_id);

        return SK_MySQL::query($query)->fetch_cell() == 1 ? true : false;
    }

    /**
     * Checks if profile has claimed group joining
     *
     * @param unknown_type $profile_id
     * @param unknown_type $group_id
     * @return unknown
     */
    public static function hasClaimedAccess( $profile_id, $group_id )
    {
        $query = SK_MySQL::placeholder("SELECT `group_id` FROM `" . TBL_GROUP_JOIN_CLAIM . "`
			WHERE `group_id`=? AND `profile_id`=?", $group_id, $profile_id);

        return SK_MySQL::query($query)->fetch_cell() ? true : false;
    }

    /**
     * Claim group joining
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function claimAccess( $profile_id, $group_id )
    {
        if ( !$group_id || !$profile_id )
            return false;

        $group = self::getGroupById($group_id);

        if ( $group['join_type'] != 'closed' || !$group['allow_claim'] || self::hasClaimedAccess($profile_id, $group_id) )
            return false;

        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_GROUP_JOIN_CLAIM . "`
			(`group_id`, `profile_id`, `claim_time`, `claim_status`)
			VALUES (?, ?, ?, 'approval')", $group_id, $profile_id, time());

        SK_MySQL::query($query);

        self::sendClaimNotification($group_id, $profile_id);
        return true;
    }

    public static function sendClaimNotification( $group_id, $profile_id )
    {
        $group_id = intval($group_id);
        $profile_id = intval($profile_id);
        if ( !$profile_id || !$group_id )
            return false;

        $creator_id = self::getGroupCreator($group_id);
        $creator_email = app_Profile::getFieldValues($creator_id, 'email');

        if ( !$creator_email )
            return false;

        $group = self::getGroupById($group_id);

        if ( !app_Unsubscribe::isProfileUnsubscribed($creator_id) )
        {
            $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                ->setRecipientProfileId($creator_id)
                ->setTpl('group_claim_notification')
                ->assignVarRange(array(
                'group_creator' => app_Profile::username($creator_id),
                'claimer' => app_Profile::username($profile_id),
                'group_title' => $group['title'],
                'group_url' => SK_Navigation::href('group_edit', array('group_id' => $group_id, 'action' => 'claims'))
                ));

            return app_Mail::send($msg);
        }
    }

    /**
     * Accept member claim and make him group member
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function acceptClaim( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        if ( self::addGroupMember($profile_id, $group_id) )
        {
            self::setClaimStatus($profile_id, $group_id, 'approved');
            return true;
        }
        return false;
    }

    /**
     * Decline member's claim
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function declineClaim( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        self::setClaimStatus($profile_id, $group_id, 'declined');
        return true;
    }

    /**
     * Set claim status (approved || declined)
     *
     * @param int $profile_id
     * @param int $group_id
     * @param string $status
     * @return boolean
     */
    private static function setClaimStatus( $profile_id, $group_id, $status )
    {
        if ( !$profile_id || !$group_id || !strlen($status) || !in_array($status, array('approval', 'approved', 'declined')) )
            return false;

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_GROUP_JOIN_CLAIM . "`
			SET `claim_status`='?' WHERE `profile_id`=? AND `group_id`=?", $status, $profile_id, $group_id);
        SK_MySQL::query($query);
    }

    /**
     * Gets all members claims for approval
     *
     * @param int $group_id
     * @return array
     */
    public static function getClaimsList( $group_id, $page = 1 )
    {
        if ( !$group_id )
            return false;

        $result = array();

        $query_parts['projection'] = "`p`.*, `online`.`hash` AS `online`, `g`.`group_id`, `c`.*";
        $query_parts['left_join'] = "
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` ON(`p`.`profile_id` = `online`.`profile_id`)
			LEFT JOIN `" . TBL_GROUP_JOIN_CLAIM . "`AS `c` ON (`p`.`profile_id`=`c`.`profile_id`)
			LEFT JOIN `" . TBL_GROUP . "` AS `g` ON (`g`.`group_id`=`c`.`group_id`) ";

        $query_parts['condition'] = SK_MySQL::placeholder("`g`.`group_id`=?", $group_id) . " AND `c`.`claim_status`='approval'";
        $query_parts['order'] = "`c`.`claim_time` ASC";

        $page_limit = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
        ;
        $page = $page ? $page : 1;
        $query_parts['limit'] = ($page - 1) * $page_limit . ", " . $page_limit;

        $query = "SELECT {$query_parts['projection']} FROM `" . TBL_PROFILE . "` AS `p`
		{$query_parts['left_join']}
			WHERE {$query_parts['condition']} " .
            ( (strlen($query_parts['order'])) ? " ORDER BY {$query_parts['order']}" : "" ) .
            " LIMIT {$query_parts['limit']}";

        $res = SK_MySQL::query($query);

        while ( $member = $res->fetch_assoc() )
            $result['profiles'][] = $member;

        // get total profiles
        $query = "SELECT COUNT( `p`.`profile_id` ) FROM `" . TBL_PROFILE . "` AS `p`
			LEFT JOIN `" . TBL_PROFILE_ONLINE . "` AS `online` ON(`p`.`profile_id` = `online`.`profile_id`)
			LEFT JOIN `" . TBL_GROUP_JOIN_CLAIM . "`AS `c` ON (`p`.`profile_id`=`c`.`profile_id`)
			LEFT JOIN  `" . TBL_GROUP . "` AS `g` ON (`g`.`group_id`=`c`.`group_id`) WHERE " .
            $query_parts['condition'];

        $result['total'] = SK_MySQL::query($query)->fetch_cell();

        return $result;
    }

    /**
     * Sends group mass mailing
     *
     * @param int $profile_id
     * @param int $group_id
     * @param string $message
     * @return int
     */
    public static function sendGroupMailing( $profile_id, $group_id, $subject, $message )
    {
        if ( !$profile_id || !$group_id || !strlen($subject) || !strlen($message) )
            return false;

        if ( !self::isGroupCreator($profile_id, $group_id) )
            return false;

        $members = self::getGroupMembers($group_id, true);
        $count = 0;
        $group = self::getGroupById($group_id);

        if ( $members )
        {
            foreach ( $members as $member )
                if ( $member != $profile_id )
                {
                    $lang_id = app_Profile::getFieldValues($member, 'language_id');
                    $lang = SK_Language::instance($lang_id);
                    $template = $lang->text('%forms.group_send_message.subject_tpl', array('group_title' => $group['title'], 'subject' => $subject));

                    if ( app_MailBox::sendSystemMessage($member, $template, $message) )
                        $count++;
                }
        }
        return $count;
    }

    public static function sendInvitationNotification( $group_id, $profile_id )
    {
        $group_id = intval($group_id);
        $profile_id = intval($profile_id);

        if ( !$profile_id || !$group_id )
            return false;

        $group = self::getGroupById($group_id);

        if ( !app_Unsubscribe::isProfileUnsubscribed($profile_id) )
        {
            $msg = app_Mail::createMessage(app_Mail::CRON_MESSAGE)
                ->setRecipientProfileId($profile_id)
                ->setTpl('group_invitation_notification')
                ->assignVarRange(array(
                'invited_user' => app_Profile::username($profile_id),
                'group_title' => $group['title'],
                'group_url' => SK_Navigation::href('group', array('group_id' => $group_id))
                ));

            return app_Mail::send($msg);
        }
    }

    /**
     * Invite one member to group
     *
     * @param int $profile_id
     * @param int $group_id
     * @return true
     */
    public static function inviteMember( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        // check if already in the group
        if ( self::isGroupMember($profile_id, $group_id) )
            return -2;

        // check if already invited
        $query = SK_MySQL::placeholder("SELECT `profile_id`
			FROM `" . TBL_GROUP_INVITATION . "`
			WHERE `profile_id`=? AND `group_id`=?", $profile_id, $group_id);
        if ( SK_MySQL::query($query)->fetch_cell() )
            return -3;

        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_GROUP_INVITATION . "`
			(`group_id`, `profile_id`, `status`) VALUES (?, ?, 'initial')", $group_id, $profile_id);

        SK_MySQL::query($query);

        if ( SK_MySQL::affected_rows() )
        {
            self::sendInvitationNotification($group_id, $profile_id);
            return true;
        }

        return false;
    }

    /**
     * Parses string with usernames and invites profiles to groups
     *
     * @param int $group_id
     * @param string $members
     * @return int | boolean
     */
    public static function inviteMembers( $group_id, $members, $inviter_id )
    {
        if ( !$group_id || !strlen($members) || !$inviter_id )
            return false;

        if ( !self::isGroupCreator($inviter_id, $group_id) && !self::isGroupModerator($inviter_id, $group_id) )
            return -1;

        $mem_arr = explode(",", $members);
        $counter = 0;

        if ( count($mem_arr) )
        {
            foreach ( $mem_arr as $mem )
            {
                $mem = trim($mem);
                $profile_id = app_Profile::getProfileIdByUsername($mem);
                if ( $profile_id )
                {
                    if ( self::inviteMember($profile_id, $group_id) > 0 )
                        $counter++;
                }
            }
            return $counter > 0 ? $counter : false;
        }
        return false;
    }

    /**
     * Checks if profile was invited to group
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function profileIsInvited( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `profile_id`
			FROM `" . TBL_GROUP_INVITATION . "`
			WHERE `profile_id`=? AND `group_id`=? AND `status`='initial'", $profile_id, $group_id);

        return SK_MySQL::query($query)->fetch_cell() ? true : false;
    }

    /**
     * Returns members invited to group
     *
     * @param int $group_id
     * @return array
     */
    public static function getInvitedMembers( $group_id )
    {
        if ( !$group_id )
            return false;

        $query = SK_MySQL::placeholder("SELECT `p`.`profile_id`, `p`.`profile_id`
			FROM `" . TBL_PROFILE . "` AS `p`
			LEFT JOIN `" . TBL_GROUP_INVITATION . "` AS `i` ON (`i`.`profile_id`=`p`.`profile_id`)
			WHERE `i`.`group_id`=? AND `i`.`status`='initial'", $group_id);

        $res = SK_MySQL::query($query);
        $inv = array();

        while ( $row = $res->fetch_assoc() )
        {
            $row['username'] = app_Profile::username($row['profile_id']);
            $inv[] = $row;
        }


        return $inv;
    }

    /**
     * Accept group invitation and become a member
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function acceptInvitaton( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        if ( self::addGroupMember($profile_id, $group_id) )
        {
            self::setInvitationStatus($profile_id, $group_id, 'accepted');
            return true;
        }
        return false;
    }

    /**
     * Decline group invitation
     *
     * @param int $profile_id
     * @param int $group_id
     * @return boolean
     */
    public static function declineInvitaton( $profile_id, $group_id )
    {
        if ( !$profile_id || !$group_id )
            return false;

        self::setInvitationStatus($profile_id, $group_id, 'declined');
        return true;
    }

    /**
     * Set  invitation status (accepted || declined)
     *
     * @param int $profile_id
     * @param int $group_id
     * @param string $status
     * @return boolean
     */
    private static function setInvitationStatus( $profile_id, $group_id, $status )
    {
        if ( !$profile_id || !$group_id || !strlen($status) || !in_array($status, array('initial', 'accepted', 'declined')) )
            return false;

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_GROUP_INVITATION . "`
			SET `status`='?' WHERE `profile_id`=? AND `group_id`=?", $status, $profile_id, $group_id);
        SK_MySQL::query($query);
    }

    /**
     * Get invitations list for profile
     *
     * @param int $profile_id
     * @return array
     */
    public static function getInvitations( $profile_id )
    {
        if ( !$profile_id )
            return array();

        $query = SK_MySQL::placeholder("SELECT `i`.*, `g`.*
			FROM `" . TBL_GROUP_INVITATION . "` AS `i`
			LEFT JOIN `" . TBL_GROUP . "` AS `g` ON (`i`.`group_id`=`g`.`group_id`)
			WHERE `i`.`profile_id`=? AND `i`.`status`='initial'", $profile_id);

        $res = SK_MySQL::query($query);
        $inv = array();

        while ( $row = $res->fetch_assoc() )
            $inv[] = $row;

        $query = SK_MySQL::placeholder("SELECT COUNT(`i`.`group_id`)
			FROM `" . TBL_GROUP_INVITATION . "` AS `i`
			LEFT JOIN `" . TBL_GROUP . "` AS `g` ON (`i`.`group_id`=`g`.`group_id`)
			WHERE `i`.`profile_id`=?  AND `i`.`status`='initial'", $profile_id);

        $count = SK_MySQL::query($query)->fetch_cell();

        return array('list' => $inv, 'count' => $count);
    }

    /**
     * Remover group and all its content
     *
     * @param int $group_id
     * @param int $profile_id
     * @return boolean
     */
    public static function removeGroup( $group_id )
    {
        if ( !$group_id )
            return false;

        $group = self::getGroupById($group_id);

        // remove group members
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP_MEMBER . "` WHERE `group_id`=?", $group_id);
        SK_MySQL::query($query);

        // remove group invitations
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP_INVITATION . "` WHERE `group_id`=?", $group_id);
        SK_MySQL::query($query);

        // remove group claims
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP_JOIN_CLAIM . "` WHERE `group_id`=?", $group_id);
        SK_MySQL::query($query);

        // remove group moderators
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP_MODERATOR . "` WHERE `group_id`=?", $group_id);
        SK_MySQL::query($query);

        app_UserActivities::deleteActivities($group_id, 'add_group');
        app_UserActivities::deleteActivities($group_id, 'group_comment');
        // remove group forum
        self::removeGroupForum($group_id);

        // remove group comments
        app_CommentService::stDeleteEntityComments(FEATURE_GROUP, $group_id, ENTITY_TYPE_GROUP_ADD);
        app_CommentService::stDeleteEntityComments(FEATURE_GROUP, $group_id, ENTITY_TYPE_GROUP_JOIN);

        //Newsfeed
        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            app_Newsfeed::newInstance()->removeAction(ENTITY_TYPE_GROUP_ADD, $group_id);
            app_Newsfeed::newInstance()->removeAction(ENTITY_TYPE_GROUP_JOIN, $group_id);
        }

        // remove group image & thumb
        $group_img = 'group_' . $group_id . '_' . $group['photo'] . '.jpg';
        $thumb_img = 'thumb_group_' . $group_id . '_' . $group['photo'] . '.jpg';

        if ( file_exists(DIR_USERFILES . $group_img) )
            unlink(DIR_USERFILES . $group_img);
        if ( file_exists(DIR_USERFILES . $thumb_img) )
            unlink(DIR_USERFILES . $thumb_img);

        // finally remove group record
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP . "` WHERE `group_id`=?", $group_id);
        SK_MySQL::query($query);

        return true;
    }

    /**
     * Removes group forum and its topics
     *
     * @param int $group_id
     * @return boolean
     */
    private static function removeGroupForum( $group_id )
    {
        $forum_id = self::getGroupForumId($group_id);

        //delete forum
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_FORUM . "` WHERE `group_id`=?", $group_id);
        SK_MySQL::query($query);

        $query = SK_MySQL::placeholder("SELECT `forum_topic_id` FROM `" . TBL_FORUM_TOPIC . "`
			WHERE `forum_id`=?", $forum_id);
        $query_result = SK_MySQL::query($query);

        while ( $row = $query_result->fetch_assoc() )
        {
            app_Forum::DeleteTopic($row['forum_topic_id']);
        }

        return true;
    }

    /**
     * Returns all groups profile has joined
     *
     * @param int $profile_id
     * @param int $page
     * @return array
     */
    public static function getGroupsProfileParticipates( $profile_id, $page = null )
    {
        if ( !$profile_id )
            return false;

        if ( isset($page) )
        {
            $per_page = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
            $limit = " LIMIT " . ( ( $page - 1 ) * $per_page ) . ",$per_page";

            $query = SK_MySQL::placeholder("SELECT `g`.*, `p`.`profile_id`, `gm`.*
				FROM `" . TBL_GROUP . "` AS `g`
				LEFT JOIN `" . TBL_GROUP_MEMBER . "` AS `gm` ON (`gm`.`group_id` = `g`.`group_id`)
				LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`gm`.`member_id` = `p`.`profile_id`)
				WHERE `gm`.`member_id`=? AND `g`.`status`='active'
				ORDER BY `gm`.`join_stamp` DESC $limit", $profile_id);

            $res = SK_MySQL::query($query);

            while ( $row = $res->fetch_assoc() )
            {
                $row['members_count'] = self::getGroupMembersCount($row['group_id']);
                $row['username'] = app_Profile::username($row['profile_id']);
                $row['thumb'] = self::getGroupImageURL($row['group_id'], $row['photo']);
                $groups['list'][] = $row;
            }

            $query = SK_MySQL::placeholder("SELECT COUNT(`g`.`group_id`)
				FROM `" . TBL_GROUP . "` AS `g`
				LEFT JOIN `" . TBL_GROUP_MEMBER . "` AS `gm` ON (`gm`.`group_id` = `g`.`group_id`)
				LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`gm`.`member_id` = `p`.`profile_id`)
				WHERE `gm`.`member_id`=? AND `g`.`status`='active'", $profile_id);
            $groups['total'] = SK_MySQL::query($query)->fetch_cell();
        }
        else
        {
            $query = SK_MySQL::placeholder("SELECT `g`.*, `p`.`profile_id`, `gm`.*
				FROM `" . TBL_GROUP . "` AS `g`
				LEFT JOIN `" . TBL_GROUP_MEMBER . "` AS `gm` ON (`gm`.`group_id` = `g`.`group_id`)
				LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`gm`.`member_id` = `p`.`profile_id`)
				WHERE `gm`.`member_id`=? AND `g`.`status`='active'
				ORDER BY `gm`.`join_stamp` DESC LIMIT 8", $profile_id);

            $res = SK_MySQL::query($query);
            $groups = array();

            while ( $row = $res->fetch_assoc() )
            {
                $row['username'] = app_Profile::username($row['profile_id']);
                $row['thumb'] = self::getGroupImageURL($row['group_id'], $row['photo']);
                $groups[] = $row;
            }
        }

        return $groups;
    }

    /**
     * Returns all groups profile created
     *
     * @param int $profile_id
     * @param int $page
     * @return array
     */
    public static function getGroupsProfileCreated( $profile_id, $page = null )
    {
        if ( !$profile_id )
            return false;

        $page = (int) $page ? (int) $page : 1;
        $per_page = SK_Config::Section('site')->Section('additional')->Section('profile_list')->result_per_page;
        $limit = " LIMIT " . ( ( $page - 1 ) * $per_page ) . ",$per_page";

        $query = SK_MySQL::placeholder("SELECT `g`.*, `p`.`profile_id`
			FROM `" . TBL_GROUP . "` AS `g`
			LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`g`.`owner_id` = `p`.`profile_id`)
			WHERE `g`.`owner_id`=?
			ORDER BY `g`.`creation_stamp` DESC $limit", $profile_id);

        $res = SK_MySQL::query($query);

        while ( $row = $res->fetch_assoc() )
        {
            $row['members_count'] = self::getGroupMembersCount($row['group_id']);
            $row['username'] = app_Profile::username($row['profile_id']);
            $row['thumb'] = self::getGroupImageURL($row['group_id'], $row['photo']);
            $groups['list'][] = $row;
        }

        $query = SK_MySQL::placeholder("SELECT COUNT(`g`.`group_id`)
			FROM `" . TBL_GROUP . "` AS `g`
			LEFT JOIN `" . TBL_PROFILE . "` AS `p` ON (`g`.`owner_id` = `p`.`profile_id`)
			WHERE `g`.`owner_id`=? ", $profile_id);
        $groups['total'] = SK_MySQL::query($query)->fetch_cell();

        return $groups;
    }

    public static function leaveGroup( $group_id, $member_id )
    {
        if ( !$group_id || !$member_id )
            return false;

        if ( self::isGroupCreator($member_id, $group_id) )
            return false;

        if ( self::isGroupModerator($member_id, $group_id) )
            self::removeGroupModerator($member_id, $group_id);

        // remove invitations
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP_INVITATION . "`
            WHERE `group_id` =? AND `profile_id`=?", $group_id, $member_id);
        SK_MySQL::query($query);

        // remove claims
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP_JOIN_CLAIM . "`
            WHERE `group_id`=? AND `profile_id`=?", $group_id, $member_id);
        SK_MySQL::query($query);

        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_GROUP_MEMBER . "`
			WHERE `group_id`=? AND `member_id`=?", $group_id, $member_id);
        SK_MySQL::query($query);

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedAction = app_Newsfeed::newInstance()->getAction('group_join', $group_id, $member_id);
            if ( !empty($newsfeedAction) )
            {
                app_Newsfeed::newInstance()->removeActionById($newsfeedAction->getId());
            }
        }

        return SK_MySQL::affected_rows() ? true : false;
    }

    public function deleteProfileGroupParticipation( $profile_id )
    {
        $groups = self::getGroupsProfileParticipates($profile_id);

        if ( !$groups )
        {
            return;
        }

        foreach ( $groups as $group )
        {
            self::leaveGroup($group['group_id'], $profile_id);
        }

        return true;
    }
}
