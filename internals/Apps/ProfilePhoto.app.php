<?php

require_once DIR_INTERNALS . 'watermark.class.php';

class app_ProfilePhoto
{
    const ERROR_WRONG_PROFILE_ID =1;
    const ERROR_WRONG_SLOT_NUMBER = 2;
    const ERROR_WRONG_PHOTO_ID = 3;
    const ERROR_RESIZING_FAILD = 4;
    const ERROR_MAX_RESOLUTION = 5;
    const ERROR_IMAGE_TYPE = 6;
    const ERROR_MAX_FILESIZE = 7;

    const PHOTOTYPE_PREVIEW = 'preview';
    const PHOTOTYPE_ORIGINAL = 'original';
    const PHOTOTYPE_THUMB = 'thumb';
    const PHOTOTYPE_FULL_SIZE = 'full_size';
    const PHOTOTYPE_VIEW = 'view';


    private static $PHOTOTYPE_LIST = array(
        self::PHOTOTYPE_FULL_SIZE,
        self::PHOTOTYPE_ORIGINAL,
        self::PHOTOTYPE_PREVIEW,
        self::PHOTOTYPE_THUMB,
        self::PHOTOTYPE_VIEW
    );

    private static $photoUrlCache = array();

    public static $process_album = false;

    public static function setProcessAlbum( $album_id )
    {
        if ( $album_id = intval($album_id) )
        {
            self::$process_album = $album_id;
        }
    }

    public static function unsetProcessAlbum()
    {
        self::$process_album = false;
    }

    public static function getSlotPhoto( $profile_id, $slot )
    {
        if ( self::$process_album === false )
        {
            $query = SK_MySQL::placeholder("
                            SELECT * FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
                                LEFT JOIN `" . TBL_PHOTO_ALBUM_ITEMS . "` as `album` ON `photo`.`photo_id`=`album`.`photo_id`
                                WHERE `photo`.`profile_id`=? AND `photo`.`number`=? AND `album`.`photo_id` IS NULL
                        ", $profile_id, $slot);
        }
        else
        {
            $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
				INNER JOIN `" . TBL_PHOTO_ALBUM_ITEMS . "` as `album` ON `photo`.`photo_id`=`album`.`photo_id`
				WHERE `profile_id`=? AND `number`=? AND `album`.`album_id`=?"
                    , $profile_id, $slot, self::$process_album);
        }

        return SK_MySQL::query($query)->fetch_object();
    }
    /**
     * Storage photo information
     */
    private static $photos_info = array();

    public static function getPhoto( $photo_id )
    {
        if ( !($photo_id = intval($photo_id)) )
        {
            return null;
        }

        if ( !isset(self::$photos_info[$photo_id]) )
        {
            $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_PROFILE_PHOTO . "` WHERE `photo_id` = ?", $photo_id);
            self::$photos_info[$photo_id] = SK_MySQL::query($query)->fetch_object();
        }

        return self::$photos_info[$photo_id];
    }

    public static function getPhotoByIdList( array $photoIdList )
    {
        if( empty($photoIdList) )
        {
            return array();
        }

        $notCachedIdList = array();
        $result = array();

        foreach( $photoIdList as $photo_id )
        {
            if( empty(self::$photos_info[$photo_id]) )
            {
                $notCachedIdList[$photo_id] = $photo_id;
            }
            else
            {
                $result[$photo_id] = self::$photos_info[$photo_id];
            }
        }

        if ( !empty($notCachedIdList) )
        {
            $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_PROFILE_PHOTO . "` WHERE `photo_id` IN ( ?@ )", $notCachedIdList);
            $res = SK_MySQL::query($query);
            while( $photoInfo = $res->fetch_object() )
            {
                if ( !empty($photoInfo) )
                {
                    $result[$photoInfo->photo_id] = $photoInfo;
                    self::$photos_info[$photoInfo->photo_id] = $photoInfo;
                }
            }
        }

        return $result;
    }

    public static function clearStoragePhotoInfo( $photo_id )
    {
        unset(self::$photos_info[$photo_id]);
    }

    public static function getViewCount( $photo_id )
    {
        $query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `" . TBL_PHOTO_VIEW . "` WHERE `photo_id`=?", $photo_id);
        return SK_MySQL::query($query)->fetch_cell();
    }

    public static function getViewCountByPhotoIdList( $photo_id_list )
    {
        if ( empty($photo_id_list) )
        {
            return;
        }

        $query = SK_MySQL::placeholder("SELECT `photo_id`, COUNT(*) AS `count` FROM `" . TBL_PHOTO_VIEW . "` WHERE `photo_id` IN ( ?@ ) GROUP BY `photo_id` ", $photo_id_list);

        $result = array();

        foreach( $photo_id_list as $id )
        {
            $result[$id] = 0;
        }

        $res = SK_MySQL::query($query);

        while( $value = $res->fetch_assoc() )
        {
             $result[$value['photo_id']] = $value['count'];
        }

        return $result;
    }

    public static function getPhotoInfo( $photo_id, $type = self::PHOTOTYPE_ORIGINAL )
    {
        $photo_info = self::getPhoto($photo_id);
        if ( !$photo_info )
        {
            return array();
        }
        return array
            (
            'id' => (int) $photo_id,
            'src' => self::getUrl((int) $photo_id, $type),
            'index' => $photo_info->index,
            'fullsize_url' => self::getPermalink((int) $photo_id),
            'status' => $photo_info->status,
            'slot' => (int) $photo_info->number,
            'description' => $photo_info->description,
            'html_description' => nl2br(SK_Language::htmlspecialchars($photo_info->description)),
            'publishing_status' => $photo_info->publishing_status,
            'title' => SK_Language::htmlspecialchars($photo_info->title),
            'added' => $photo_info->added_stamp,
            'authed' => (bool) $photo_info->authed
            //'rate_score'		=>	sprintf( '%02.1f', $_row['rate_score'] / $rate_point ),
            //'rates'				=>	$_row['rates']
        );
    }

    public static function upload( $file_unique_id, $slot, $profile_id = null, $status = null )
    {
        $profile_id = isset($profile_id) ? $profile_id : SK_HttpUser::profile_id();

        if ( !$profile_id )
        {
            throw new SK_ProfilePhotoException('wrong_profile_id', self::ERROR_WRONG_PROFILE_ID);
        }

        if ( !($slot = intval($slot)) )
        {
            throw new SK_ProfilePhotoException('wrong_slot_number', self::ERROR_WRONG_SLOT_NUMBER);
        }

        if ( !in_array($status, array('active', 'approval', 'suspended')) )
        {
            $status = SK_Config::section('site')->Section('automode')->set_active_photo_on_upload ? 'active' : 'approval';
        }


        $index = rand(0, 99);

        $slot_photo = self::getSlotPhoto($profile_id, $slot);

        if ( $slot_photo )
        {
            self::delete($slot_photo->photo_id);
        }

        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_PROFILE_PHOTO . "`( `profile_id`, `index`, `number`, `status`, `added_stamp` )
			VALUES ( ?, ?, ?, '?', ?)", $profile_id, $index, $slot, $status, time());
        SK_MySQL::query($query);

        $photo_id = (int) SK_MySQL::insert_id();

        if ( self::$process_album !== false )
        {
            app_PhotoAlbums::moveTo($photo_id, self::$process_album);
        }

        $original_path = self::getPath($photo_id);
        try
        {
            $result = app_Image::upload(array(
                    'file_uniqid' => $file_unique_id,
                    'result_path' => $original_path,
                    'result_image_type' => IMAGETYPE_JPEG,
                ));
            chmod($original_path, 0777);

            self::makeCopies($original_path, $photo_id);
        }
        catch ( SK_ImageException $e )
        {

            SK_MySQL::query("DELETE FROM `" . TBL_PROFILE_PHOTO . "` WHERE `photo_id`=$photo_id");

            switch ( $e->getCode() )
            {
                case app_Image::ERROR_MAX_RESOLUTION :
                    throw new SK_ProfilePhotoException('max_resolution', self::ERROR_MAX_RESOLUTION);
                    break;
                case app_Image::ERROR_WRONG_IMAGE_TYPE :
                    throw new SK_ProfilePhotoException('image_type', self::ERROR_IMAGE_TYPE);
                    break;
                case app_Image::ERROR_MAX_FILESIZE:
                    throw new SK_ProfilePhotoException('filesize', self::ERROR_MAX_FILESIZE);
                    break;

                default:
                    throw new SK_ProfilePhotoException('upload_error', 0);
                    break;
            }
        }

        self::updateHasPhotoStatus($profile_id);

        return $photo_id;
    }

    private static function makeCopies( $sourcePath, $photoId )
    {
        $configs = SK_Config::section('photo')->Section('general');

        $path = self::getPath($photoId, self::PHOTOTYPE_FULL_SIZE);

        app_Image::convert($sourcePath, IMAGETYPE_JPEG, $path);
        @chmod($path, 0777);

        $path = self::getPath($photoId, self::PHOTOTYPE_VIEW);
        app_Image::resize($sourcePath, $configs->view_width, $configs->view_height, false, $path);
        @chmod($path, 0777);

        $path = self::getPath($photoId, self::PHOTOTYPE_PREVIEW);
        app_Image::resize($sourcePath, $configs->preview_width, $configs->preview_height, false, $path);
        @chmod($path, 0777);

        $path = self::getPath($photoId, self::PHOTOTYPE_THUMB);
        app_Image::resize(self::getPath($photoId, self::PHOTOTYPE_PREVIEW), $configs->thumb_width, $configs->thumb_width, true, $path);
        @chmod($path, 0777);

        if ( SK_Config::section('photo')->Section("watermark")->watermark )
        {
            self::setWatermark($sourcePath, self::getPath($photoId, self::PHOTOTYPE_VIEW));
            self::setWatermark($sourcePath, self::getPath($photoId, self::PHOTOTYPE_PREVIEW));
            self::setWatermark($sourcePath, self::getPath($photoId, self::PHOTOTYPE_FULL_SIZE));
        }
    }

    public static function rotatePhoto( $photoId, $angle )
    {
        $originalPath = app_ProfilePhoto::getPath($photoId, app_ProfilePhoto::PHOTOTYPE_ORIGINAL);

        $photoPathList = array();

        foreach ( self::$PHOTOTYPE_LIST as $type )
        {
            $photoPathList[$type] = self::getPath($photoId, $type);
        }

        $originalIndex = self::getPhoto($photoId)->index;

        self::clearStoragePhotoInfo($photoId);

        $query = SK_MySQL::placeholder(
                'UPDATE `' . TBL_PROFILE_PHOTO . '` SET `index`=? WHERE `photo_id`=?'
                , rand(0, 99), $photoId);

        SK_MySQL::query($query);

        $newPath = app_ProfilePhoto::getPath($photoId, app_ProfilePhoto::PHOTOTYPE_ORIGINAL);

        try
        {
            if ( !app_Image::rotate($originalPath, $angle, $newPath) )
            {
                return false;
            }

            self::makeCopies($newPath, $photoId);
        }
        catch ( Exception $e )
        {
            $query = SK_MySQL::placeholder(
                    'UPDATE `' . TBL_PROFILE_PHOTO . '` SET `index`=? WHERE `photo_id`=?'
                    , $originalIndex, $photoId);

            SK_MySQL::query($query);

            throw $e;
        }

        foreach ( $photoPathList as $path )
        {
            @unlink($path);
        }

        return true;
    }

    public static function updateHasPhotoStatus( $profile_id )
    {
        $profile_id = intval($profile_id);
        if ( !$profile_id )
            return false;

        $query = "SELECT COUNT(*) FROM `" . TBL_PROFILE_PHOTO . "` WHERE `status`='active' AND `profile_id`=$profile_id";
        $has_photo = intval(SK_MySQL::query($query)->fetch_cell()) ? 'y' : 'n';

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE . "` SET `has_photo`='?' WHERE `profile_id`=?", $has_photo, $profile_id);
        SK_MySQL::query($query);
        return true;
    }

    public static function getPath( $photo_id, $type = self::PHOTOTYPE_ORIGINAL )
    {
        if ( !in_array($type, self::$PHOTOTYPE_LIST) )
        {
            return null;
        }

        if ( !($photo_info = self::getPhoto($photo_id)) )
        {
            return null;
        }

        return DIR_USERFILES . $type . '_' . $photo_info->profile_id . '_' . $photo_id . '_' . $photo_info->index . '.jpg';
    }

    public static function getUrl( $photo_id, $type = app_ProfilePhoto::PHOTOTYPE_ORIGINAL )
    {
        if ( !in_array($type, self::$PHOTOTYPE_LIST) )
        {
            return null;
        }

        if ( !empty(self::$photoUrlCache[$photo_id]) )
        {
            return self::$photoUrlCache[$photo_id][$type];
        }
        else
        {
            if ( !($photo_info = self::getPhoto($photo_id)) )
            {
                return null;
            }

            foreach ( self::$PHOTOTYPE_LIST as $t )
            {
                self::$photoUrlCache[$photo_id][$t] = self::generateUrl($photo_id, $photo_info->profile_id, $photo_info->index, $t);
            }

            return self::generateUrl($photo_id, $photo_info->profile_id, $photo_info->index, $type);
        }
    }

    public static function generateUrl( $photoId, $profileId, $index, $type )
    {
        return URL_USERFILES . $type . '_' . $profileId . '_' . $photoId . '_' . $index . '.jpg';
    }

    public static function getUrlList( array $photoIdList, $type = app_ProfilePhoto::PHOTOTYPE_ORIGINAL )
    {
        if ( !in_array($type, self::$PHOTOTYPE_LIST) )
        {
            return null;
        }

        $result = array();
        $useCache = true;
        foreach( $photoIdList as $id )
        {
            if ( empty(self::$photoUrlCache[$id]) )
            {
                $useCache = false;
                $result = array();
                break;
            }

            $result[$id] = self::$photoUrlCache[$id][$type];
        }

        if ( $useCache )
        {
            return $result;
        }
        else
        {
            $list = self::getPhotoByIdList($photoIdList);

            if ( empty($list) )
            {
                return array();
            }

            foreach ( $list as $photo_info )
            {
                if ( !empty($photo_info) )
                {
                    $photo_id = $photo_info->photo_id;

                    $result[$photo_id] = self::generateUrl($photo_id, $photo_info->profile_id, $photo_info->index, $type);

                    foreach ( self::$PHOTOTYPE_LIST as $t )
                    {
                        self::$photoUrlCache[$photo_id][$t] = self::generateUrl($photo_id, $photo_info->profile_id, $photo_info->index, $t);
                    }
                }
            }
            
            return $result;
        }

    }

    public static function getUrlListByPhotoIdAndType( array $photoIdList, array $types = array( app_ProfilePhoto::PHOTOTYPE_ORIGINAL ) )
    {
        if ( empty($types) )
        {
            return null;
        }

       $typesList = array_intersect(self::$PHOTOTYPE_LIST, $types);

        if ( empty($typesList) )
        {
            return null;
        }

        $result = array();
        $useCache = true;
        foreach( $photoIdList as $id )
        {

            if ( empty(self::$photoUrlCache[$id]) )
            {
                $useCache = false;
                $result = array();
                break;
            }

            foreach( $typesList as $type )
            {
                $result[$id] = self::$photoUrlCache[$id][$type];
            }
        }

        if ( $useCache )
        {
            return $result;
        }
        else
        {
            $list = self::getPhotoByIdList($photoIdList);

            if ( empty($list) )
            {
                return array();
            }

            foreach ( $list as $photo_info )
            {
                if ( !empty($photo_info) )
                {
                    foreach( $typesList as $type )
                    {
                        $result[$photo_info->photo_id][$type] = self::generateUrl($photo_info->photo_id, $photo_info->profile_id, $photo_info->index, $type);
                    }

                    foreach ( self::$PHOTOTYPE_LIST as $t )
                    {
                        self::$photoUrlCache[$photo_id][$t] = self::generateUrl($photo_id, $photo_info->profile_id, $photo_info->index, $t);
                    }
                }
            }

            return $result;
        }
    }

    public static function delete( $photo_id )
    {
        $photo_info = self::getPhoto($photo_id);

        foreach ( self::$PHOTOTYPE_LIST as $type )
        {
            @unlink(self::getPath($photo_id, $type));
        }

        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_PHOTO . "` WHERE `photo_id`=?", $photo_id);
        SK_MySQL::query($query);

        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PHOTO_ALBUM_ITEMS . "` WHERE `photo_id`=?", $photo_id);
        SK_MySQL::query($query);

        app_CommentService::stDeleteEntityComments("photo", $photo_id, ENTITY_TYPE_PHOTO_UPLOAD);
        app_RateService::stDeleteEntityItemScores("photo", $photo_id);

        //LActivity clean
        app_UserActivities::deleteActivities($photo_id, 'photo_upload');
        app_UserActivities::deleteActivities($photo_id, 'photo_comment');
        //~

        //Newsfeed
        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            app_Newsfeed::newInstance()->removeAction(ENTITY_TYPE_PHOTO_UPLOAD, $photo_id);
        }

        self::updateHasPhotoStatus($photo_info->profile_id);

        return true;
    }

    public static function deleteThumbnail()
    {
        $profile_id = SK_HttpUser::profile_id();
        $query = SK_MySQL::placeholder("SELECT photo_id FROM `" . TBL_PROFILE_PHOTO . "` WHERE `profile_id`=? AND `number`=?", $profile_id, 0);
        $thumb_id = SK_MySQL::query($query)->fetch_cell();

        @unlink(self::getPath($thumb_id, self::PHOTOTYPE_THUMB));

        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_PROFILE_PHOTO . "` WHERE `profile_id`=? AND `number`=?", $profile_id, 0);
        SK_MySQL::query($query);

        self::updateHasPhotoStatus($profile_id);

        return true;
    }

    public static function getPermalink( $photo_id )
    {
        // Mod Rewrite Prepare
        SK_Navigation::LoadModule("photo");

        return SK_Navigation::href("profile_photo", array("photo_id" => $photo_id));
    }

    public static function getUploadedPhotos()
    {
        $profile_id = SK_HttpUser::profile_id();
        $config = SK_Config::section('photo')->Section('general');
        if ( self::$process_album !== false )
        {
            $query = SK_MySQL::placeholder('SELECT `photo`.* FROM `' . TBL_PROFILE_PHOTO . '` AS `photo`
				INNER JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
				WHERE `profile_id`=? AND `number`!=0 AND `album`.`album_id`=?
				ORDER BY `number` ASC LIMIT ?', $profile_id, self::$process_album, $config->max_photos_in_album);
        }
        else
        {
            $query = SK_MySQL::placeholder('SELECT `photo`.* FROM `' . TBL_PROFILE_PHOTO . '`  AS `photo`
				LEFT JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
				WHERE `profile_id`=? AND `number`!=0 AND `album`.`photo_id` IS NULL
				ORDER BY `number` ASC LIMIT ?', $profile_id, $config->max_count);
        }
        //$_rate_point = 100 / (int)getConfig('pr_photo_max_rate_score');

        $result = SK_MySQL::query($query);

        $photos = array();
        while ( $row = $result->fetch_assoc() )
        {
            $photos[$row['number']] = array
                (
                'id' => (int) $row['photo_id'],
                'src' => self::getUrl((int) $row['photo_id'], app_ProfilePhoto::PHOTOTYPE_PREVIEW),
                'fullsize_url' => self::getPermalink((int) $row['photo_id']),
                'index' => $row['index'],
                'status' => $row['status'],
                'slot' => (int) $row['number'],
                'txt_description' => $row['description'],
                'html_description' => nl2br(SK_Language::htmlspecialchars($row['description'])),
                'publishing_status' => $row['publishing_status'],
                'title' => $row['title'],
                'authed' => (bool) $row['authed']
                //'rate_score'		=>	sprintf( '%02.1f', $_row['rate_score'] / $rate_point ),
                //'rates'				=>	$_row['rates']
            );
        }

        return $photos;
    }

    public static function getPhotos( $profile_id, $active_only = true )
    {
        if ( !($profile_id = intval($profile_id)) )
        {
            throw new SK_ProfilePhotoException('wrong_profile_id', self::ERROR_WRONG_PROFILE_ID);
        }

        $viewer_id = SK_HttpUser::profile_id();

        $viewer_is_profile_owner = ( $viewer_id == $profile_id );


        $where_definition = (!$viewer_is_profile_owner && $active_only ) ? ' AND `photo`.`status`="active"' : '';

        if ( app_Features::isAvailable(9) )
        {
            $viewer_is_friend = app_FriendNetwork::isProfileFriend($profile_id, $viewer_id);
        }
        else
        {
            $viewer_is_friend = false;
            $where_definition .= ' AND `photo`.`publishing_status`!="friends_only"';
        }

        if ( !app_Features::isAvailable(24) )
            $where_definition .= ' AND `photo`.`publishing_status`!="password_protected"';

        $config = SK_Config::section('photo')->Section('general');

        if ( self::$process_album !== false )
        {
            $query = SK_MySQL::placeholder('SELECT `photo`.* FROM `' . TBL_PROFILE_PHOTO . '` AS `photo`
				INNER JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
				WHERE `profile_id`=? AND `number`!=0 AND `album`.`album_id`=? ' . $where_definition . '
				ORDER BY `number` ASC LIMIT ?', $profile_id, self::$process_album, $config->max_photos_in_album);
        }
        else
        {
            $query = SK_MySQL::placeholder('SELECT `photo`.* FROM `' . TBL_PROFILE_PHOTO . '`  AS `photo`
				LEFT JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
				WHERE `profile_id`=? AND `number`!=0 AND `album`.`photo_id` IS NULL  ' . $where_definition . '
				ORDER BY `number` ASC LIMIT ?', $profile_id, $config->max_count);
        }

        $result = SK_MySQL::query($query);

        $viewer_can_see_photo_rate_results = false;

        $photos = array();
        $rowList = array();
        $photoIdList = array();
        while ( $row = $result->fetch_assoc() )
        {
            $rowList[] = $row;
            $photoIdList[$row['photo_id']] = $row['photo_id'];
        }

        $types = self::$PHOTOTYPE_LIST;

        $urlList = self::getUrlListByPhotoIdAndType($photoIdList, $types);

        foreach($rowList as $row)
        {
            if ( $row['publishing_status'] == 'public' )
            {
                $preview_url = $urlList[$row['photo_id']][self::PHOTOTYPE_PREVIEW];
                $thumb_url = $urlList[$row['photo_id']][self::PHOTOTYPE_THUMB];
                $view_url = $urlList[$row['photo_id']][self::PHOTOTYPE_VIEW];
                $fullsize_url = $urlList[$row['photo_id']][self::PHOTOTYPE_FULL_SIZE];
            }
            elseif ( $row['publishing_status'] == 'friends_only' )
            {
                $preview_url = $viewer_is_friend ? $urlList[$row['photo_id']][self::PHOTOTYPE_PREVIEW] : self::friend_only_url();
                $thumb_url = $viewer_is_friend ? $urlList[$row['photo_id']][self::PHOTOTYPE_THUMB] : self::friend_only_url();
                $view_url = $viewer_is_profile_owner ? $urlList[$row['photo_id']][self::PHOTOTYPE_VIEW] : self::friend_only_url();
                $fullsize_url = $viewer_is_friend ? $urlList[$row['photo_id']][self::PHOTOTYPE_FULL_SIZE] : self::friend_only_url();
            }
            elseif ( $row['publishing_status'] == 'password_protected' )
            {
                if ( self::isUnlocked($row['photo_id']) )
                {

                    $preview_url = $urlList[$row['photo_id']][self::PHOTOTYPE_PREVIEW];
                    $thumb_url = $urlList[$row['photo_id']][self::PHOTOTYPE_THUMB];
                    $view_url = $urlList[$row['photo_id']][self::PHOTOTYPE_VIEW];
                    $fullsize_url = $urlList[$row['photo_id']][self::PHOTOTYPE_FULL_SIZE];
                }
                else
                {
                    $view_url = $fullsize_url = $preview_url = $thumb_url = self::password_protected_url();
                }
            }



            $photos[$row['number']] = array
                (
                'id' => (int) $row['photo_id'],
                'thumb_src' => $thumb_url,
                'preview_src' => $preview_url,
                'view_src' => $view_url,
                'fullsize_src' => $fullsize_url,
                'fullsize_url' => self::getPermalink((int) $row['photo_id']),
                'number' => (int) $row['number'],
                'status' => $row['status'],
                'publishing_status' => $row['publishing_status'],
                'title' => $row['title'],
                'authed' => (bool) $row['authed'],
                'unlocked' => true
            );


            if ( $row['publishing_status'] != 'password_protected' &&
                ( $row['publishing_status'] != 'friends_only' || $viewer_is_friend)
            )
            {
                $photos[$row['number']]['description'] = $row['description'];
            }
            else
            {
                if ( $row['publishing_status'] == 'password_protected' )
                {
                    $photos[$row['number']]['unlocked'] = self::isUnlocked($row['photo_id']);
                    $photos[$row['number']]['description'] = $row['description'];
                }
                elseif ( $row['publishing_status'] == 'friends_only' )
                {
                    //$photos[$row['number']]['description'] = SK_Language::text("%components.photo_gallery.password_protected_desc", array('username' => app_Profile::getFieldValues($profile_id,'username')));
                    $photos[$row['number']]['unlocked'] = $viewer_is_friend;
                    $photos[$row['number']]['description'] = $row['description'];
                }
            }
        }

        return $photos;
    }

    public static function autoCropImg( $path, $width, $height )
    {
        if ( !$path )
            return array();

        require_once( DIR_SERVICE_AUTOCROP . 'class.image_cropper_proxy.php' );

        $config_section = SK_Config::section("services")->Section("autocrop");

        $cropper = new ImageCropperProxy(AUTOCROP_SERVICE_URL, $config_section->username, $config_section->password);

        $result = $cropper->CropImage($path, $width, $height);

        if ( $result === false )
            return false;
        elseif ( $result )
            return array_shift($result);
        else
            return false;
    }

    /**
     * Create profile thumbnail from photo backup
     *
     * Get size of thumbnail from site configs
     * and resize original photo backup
     * If thumbnail was created return true, else error code:<br/>
     * <li>-1 undefined photo id</li>
     * <li>-2 Gd library is not installed</li>
     * <li>-3 profile not find</li>
     *
     * @param integer $photo_id
     *
     * @return boolean|integer
     *
     */
    public static function createThumbnail( $photo_id )
    {
        if ( !($photo_id = intval($photo_id)) )
        {
            throw new SK_ProfilePhotoException('wrong_photo_id', self::ERROR_WRONG_PHOTO_ID);
        }

        $photo = self::getPhoto($photo_id);

        if ( !$photo->profile_id )
        {
            throw new SK_ProfilePhotoException('wrong_profile_id', self::ERROR_WRONG_PROFILE_ID);
        }

        //generate photo index
        $thumb_index = rand(1, 99);

        // get old thumbnail info
        $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_PROFILE_PHOTO . "`
			WHERE `profile_id`=? AND `number`=0", $photo->profile_id);

        $old_thumb_info = SK_MySQL::query($query)->fetch_object();

        if ( $old_thumb_info->photo_id )
        {

            @unlink(self::getPath($old_thumb_info->photo_id, self::PHOTOTYPE_THUMB));

            $query = SK_MySQL::placeholder("UPDATE `" . TBL_PROFILE_PHOTO . "` SET `index`=? , `status`='?'
				WHERE `photo_id`=?", $thumb_index, $photo->status, $old_thumb_info->photo_id);

            self::clearStoragePhotoInfo($old_thumb_info->photo_id);

            SK_MySQL::query($query);

            $thumb_id = $old_thumb_info->photo_id;
        }
        else
        {

            $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_PROFILE_PHOTO . "`( `profile_id`, `index`, `status` )
				VALUES ( ?, ?, '?' )", $photo->profile_id, $thumb_index, $photo->status);

            SK_MySQL::query($query);

            $thumb_id = SK_MySQL::insert_id();
        }

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $join_stamp = app_Profile::getFieldValues($photo->profile_id, 'join_stamp');

            if ( time() - $join_stamp > 60 )
            {

                $newsfeedAction = app_Newsfeed::newInstance()->getAction('profile_avatar_change', $photo->profile_id, $photo->profile_id);
                if ( !empty($newsfeedAction) )
                {
                    app_Newsfeed::newInstance()->removeActionById($newsfeedAction->getId());
                }

                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_NEWSFEED,
                        'entityType' => 'profile_avatar_change',
                        'entityId' => $photo->profile_id,
                        'userId' => $photo->profile_id,
                        'status' => $photo->status,
                        'replace' => true
                    )
                );
                app_Newsfeed::newInstance()->action($newsfeedDataParams);
            }
        }

        $configs = SK_Config::section('photo')->Section('general');

        $autocrop_conf_section = SK_Config::section("services")->Section("autocrop");

        if ( $autocrop_conf_section->enabled )
        {
            $_thumb = self::autoCropImg(self::getPath($photo_id, self::PHOTOTYPE_ORIGINAL), $configs->thumb_width, $configs->thumb_height);
            if ( $_thumb )
            {
                app_Image::makeSquare($_thumb, imagesx($_thumb), imagesy($_thumb), $configs->crop_thumb, $configs->thumb_fill_color);

                if ( app_Image::resource2image($_thumb, self::getPath($thumb_id, self::PHOTOTYPE_THUMB)) )
                {
                    return true;
                }
            }
        }

        @copy(self::getPath($photo_id, self::PHOTOTYPE_THUMB), self::getPath($thumb_id, self::PHOTOTYPE_THUMB));
        //$result = app_Image::resize(self::getPath($photo_id, self::PHOTOTYPE_ORIGINAL),  $configs->thumb_width,  $configs->thumb_height, true, self::getPath($thumb_id, self::PHOTOTYPE_THUMB));

        return true;
    }

    private static $thumbUrlCache = array();

    public static function getThumbUrl( $profile_id, $active = true )
    {
        if ( !($profile_id = intval($profile_id)) )
        {
            throw new SK_ProfilePhotoException('wrong_profile_id', self::ERROR_WRONG_PROFILE_ID);
        }

        if( empty(self::$thumbUrlCache[$profile_id]) )
        {
            $query_inc = (!$active || $profile_id == SK_HttpUser::profile_id()) ? '1' : "`status`='active'";
            $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_PROFILE_PHOTO . "` WHERE `profile_id`=? AND `number`=0 AND $query_inc", $profile_id);

            $photoInfo = SK_MySQL::query($query)->fetch_assoc();

            if ( $photoInfo )
            {
                self::$thumbUrlCache[$profile_id] = self::generateUrl($photoInfo['photo_id'], $photoInfo['profile_id'], $photoInfo['index'], self::PHOTOTYPE_THUMB);
            }
            else
            {
                $sex = app_Profile::getFieldValues($profile_id, 'sex');
                self::$thumbUrlCache[$profile_id] = self::defaultPhotoUrl($sex, self::PHOTOTYPE_THUMB);
            }
        }

        return self::$thumbUrlCache[$profile_id];
    }

    public static function getThumbUrlList( array $profileIdList, $active = true )
    {
        if( empty($profileIdList) )
        {
            return array();
        }

        $profileIdList = array_unique($profileIdList);

        $idListToRequire = array_diff($profileIdList, array_keys(self::$thumbUrlCache));

        if( !empty($idListToRequire) )
        {
            $query_inc = (!$active || $profile_id == SK_HttpUser::profile_id()) ? '1' : "`status`='active'";
            $query = SK_MySQL::placeholder("SELECT `photo_id`, `profile_id`, `index` FROM `" . TBL_PROFILE_PHOTO . "` WHERE `profile_id` IN (?@) AND `number`=0 AND $query_inc", $idListToRequire);

            $photoIdList = SK_MySQL::queryForList($query);

            foreach ( $photoIdList as $item )
            {
                self::$thumbUrlCache[$item['profile_id']] = self::generateUrl($item['photo_id'], $item['profile_id'], $item['index'], self::PHOTOTYPE_THUMB);
            }
        }

		app_Profile::getFieldValuesForUsers($profileIdList, array('sex'));

        $returnArray = array();

        foreach ( $profileIdList as $id )
        {
            if( !isset(self::$thumbUrlCache[$id]) )
            {
                self::$thumbUrlCache[$id] = self::defaultPhotoUrl(app_Profile::getFieldValues($id, 'sex'), self::PHOTOTYPE_THUMB);
            }

            $returnArray[$id] = self::$thumbUrlCache[$id];
        }

        return $returnArray;
    }

    public static function defaultPhotoUrl( $sex, $type )
    {
        if ($sex == 0)
        {
            return URL_LAYOUT . SK_Layout::theme_dir(true) . 'img/sex_' . $sex . '_no_photo_' . $type . '.jpg';
        }
        return URL_LAYOUT . SK_Layout::theme_dir(true) . 'img/sex_' . $sex . '_no_photo_' . $type . '.gif';
    }

    public static function photoOwnerId( $photo_id )
    {
        if ( !($photo_id = intval($photo_id)) )
            return null;
        $query = SK_MySQL::placeholder('SELECT `profile_id` FROM `' . TBL_PROFILE_PHOTO . '` WHERE `photo_id`=?', $photo_id);
        return (int) SK_MySQL::query($query)->fetch_cell();
    }

    public static function setUnlocked( $photo_id )
    {
        $_SESSION["%unlocked_photos%"][] = $photo_id;
    }

    public static function isUnlocked( $photo_id )
    {
        if ( !isset($_SESSION["%unlocked_photos%"]) || !is_array($_SESSION["%unlocked_photos%"]) )
        {
            return false;
        }
        else
        {
            return in_array($photo_id, $_SESSION["%unlocked_photos%"]);
        }
    }

    public static function trackPhotoView( $photo_id )
    {
        if ( !($photo_id = intval($photo_id)) )
        {
            return false;
        }

        $ip = SK_HttpRequest::getRequestIP();

        $profile_id = SK_HttpUser::profile_id();

        $add_where = $profile_id ? SK_MySQL::placeholder("`viewer_profile_id` =?", $profile_id) : SK_MySQL::placeholder("`ip_address`=INET_ATON('?')", $ip);

        $query = SK_MySQL::placeholder(" SELECT COUNT(*) as `COUNT` FROM `" . TBL_PHOTO_VIEW . "` WHERE `photo_id`=? AND $add_where"
                , $photo_id, $ip, $profile_id);
        if ( SK_MySQL::query($query)->fetch_cell() )
        {
            return false;
        }

        $query = SK_MySQL::placeholder(" UPDATE `" . TBL_PROFILE_PHOTO . "` SET `view_count` = `view_count` + 1 WHERE `photo_id`=? ", $photo_id);
        SK_MySQL::query($query);

        $query = SK_MySQL::placeholder(" INSERT INTO `" . TBL_PHOTO_VIEW . "` VALUES(?, ?, INET_ATON('?'))", $photo_id, $profile_id, $ip);
        SK_MySQL::query($query);

        return (bool) SK_MySQL::affected_rows();
    }

    public static function password_protected_url()
    {
        return URL_LAYOUT . SK_Layout::theme_dir(true) . 'img/password_protected_photo.gif';
    }

    public static function deleted_url()
    {
        return URL_LAYOUT . SK_Layout::theme_dir(true) . 'img/deleted.gif';
    }

    public static function friend_only_url()
    {

        return URL_LAYOUT . SK_Layout::theme_dir(true) . 'img/friends_only_photo.gif';
    }

    /**
     * Make watermark on profile photo
     *
     * Get watermark configs from and aply watermark
     * to profile photo
     *
     * @param string $base_photo_url
     * @param string $changed_photo_dir
     *
     * @return boolean
     */
    public static function setWatermark( $base_photo_dir, $changed_photo_dir )
    {
        $configs_wm_main = SK_Config::section("photo")->Section('watermark');
        $configs_wm_add = SK_Config::section("photo")->Section('watermark')->Section('additional');
        $configs_photo = SK_Config::section("photo")->Section('general');

        if ( !file_exists($base_photo_dir) || !strlen(trim($changed_photo_dir)) )
            return false;

        if ( !app_Image::getGdVersion() )
            return false;

        $watermark = new Thumbnail($base_photo_dir);

        list($width, $height, $type) = getimagesize($changed_photo_dir);
        $_dimensions = array(
            $width,
            $height
        );

        $watermark->size_auto(max($_dimensions));

        // detect watermark align
        switch ( $configs_wm_add->pos )
        {
            case 1:

                $_valign = 'BOTTOM';
                $_align = 'RIGHT';
                break;

            case 2:

                $_valign = 'BOTTOM';
                $_align = 'LEFT';
                break;

            case 3:

                $_valign = 'TOP';
                $_align = 'LEFT';
                break;

            case 4:

                $_valign = 'TOP';
                $_align = 'RIGHT';
                break;
        }

        if ( $configs_wm_main->watermark == 1 )
        {
            // If text watermark

            $watermark->txt_watermark_Hmargin = $configs_wm_add->padding;
            $watermark->txt_watermark_Vmargin = $configs_wm_add->padding;

            $watermark->txt_watermark = $configs_wm_main->txt;
            $watermark->txt_watermark_color = str_replace('#', '', $configs_wm_main->txt_color);
            $watermark->txt_watermark_bg_color = str_replace('#', '', $configs_wm_main->bg_color);
            $watermark->txt_watermark_font = $configs_wm_main->txt_size;
            $watermark->txt_watermark_Valing = $_valign;
            $watermark->txt_watermark_Haling = $_align;
        }
        elseif ( $configs_wm_main->watermark == 2 )
        {
            // If Image watermark

            $watermark->img_watermark_Hmargin = $configs_wm_add->padding;
            $watermark->img_watermark_Vmargin = $configs_wm_add->padding;

            $watermark->img_watermark = DIR_USERFILES . 'photo_watermark_img_' . $configs_wm_main->img . '.png';
            $watermark->img_watermark_Valing = $_valign;
            $watermark->img_watermark_Haling = $_align;
        }

        $watermark->process();
        $watermark->save($changed_photo_dir);

        return true;
    }

    public static function photo_count( $profile_id )
    {

        $query = SK_MySQL::placeholder('SELECT COUNT(`photo`.`photo_id`) FROM `' . TBL_PROFILE_PHOTO . '`  AS `photo`
                LEFT JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
                WHERE `profile_id`=? AND `album`.`photo_id` IS NULL AND ' . app_PhotoList::activeSqlStr('photo'), $profile_id);

        return SK_MySQL::query($query)->fetch_cell();
    }

    public static function photo_count_by_user_id_list( $idList )
    {

        if( empty($idList) )
        {
            return array();
        }

        $query = SK_MySQL::placeholder('SELECT `profile_id`, COUNT(`photo`.`photo_id`) as `count` FROM `' . TBL_PROFILE_PHOTO . '`  AS `photo`
                LEFT JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
                WHERE `profile_id` IN  (?@) AND `album`.`photo_id` IS NULL AND '.app_PhotoList::activeSqlStr('photo').' group by `profile_id`', $idList);

        $result = SK_MySQL::queryForList($query);
        $resultArray = array();

        foreach ( $result as $item )
        {
            $resultArray[$item['profile_id']] = $item['count'];
        }

        $returnArray = array();

        foreach ( $idList as $id )
        {
            $returnArray[$id] = isset($resultArray[$id]) ? $resultArray[$id] : 0;
        }

        return $returnArray;
    }

    public static function random_photo( $sex = 0 )
    {

        $user_id = (int) SK_HttpUser::profile_id();

        if ( !$user_id )
        {
            return array();
        }

        $inner_query = SK_MySQL::placeholder("SELECT `entity_id` FROM `" . TBL_PHOTO_RATE . "` WHERE `profile_id`=?", $user_id);

        $sex_condition_sql = ($sex = (int) $sex) ? SK_MySQL::placeholder(" AND `profile`.`sex`=?", $sex) : '';

        $query = "SELECT `photo`.`photo_id`, `profile`.`profile_id`, `index`
					FROM `" . TBL_PROFILE_PHOTO . "` AS `photo`
					    LEFT JOIN `" . TBL_PHOTO_ALBUM_ITEMS . "` AS `pai` ON `photo`.`photo_id` = `pai`.`photo_id`
					    LEFT JOIN `" . TBL_PHOTO_ALBUMS . "` AS `pa` ON `pai`.`album_id` = `pa`.`id`
						LEFT JOIN `" . TBL_PROFILE . "` AS `profile` ON  `photo`.`profile_id` = `profile`.`profile_id`
					WHERE (`pa`.`privacy` IS NULL OR `pa`.`privacy`='public') AND `profile`.`status`='active' AND `photo`.`status`='active' AND `publishing_status`='public'
						AND `number`!=0 AND `profile`.`profile_id`!=$user_id
						$sex_condition_sql AND `photo`.`photo_id` NOT IN ($inner_query)
					ORDER BY RAND() LIMIT 1";

        $random_photo = SK_MySQL::query($query)->fetch_assoc();

        if ( !$random_photo )
        {
            return array();
        }

        $random_photo['url'] = self::getUrl($random_photo["photo_id"], self::PHOTOTYPE_VIEW);

        return $random_photo;
    }

    public static function hasThumbnail( $profile_id = null )
    {
        if ( !($profile_id = intval($profile_id)) )
        {
            $profile_id = SK_HttpUser::profile_id();
        }

        $query = "SELECT COUNT(*) FROM `" . TBL_PROFILE_PHOTO . "` WHERE `profile_id`=$profile_id AND `number`=0";
        return (bool) SK_MySQL::query($query)->fetch_cell();
    }

    public static function getPhotoCount( $profile_id = null )
    {
        if ( !($profile_id = intval($profile_id)) )
        {
            $profile_id = SK_HttpUser::profile_id();
        }
        $configs = SK_Config::section('photo')->Section('general');

        if ( self::$process_album !== false )
        {
            $query = SK_MySQL::placeholder('SELECT COUNT(`photo`.`photo_id`) FROM `' . TBL_PROFILE_PHOTO . '` AS `photo`
				INNER JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
				WHERE `profile_id`=? AND `number`!=0 AND `album`.`album_id`=?
LIMIT ?', $profile_id, self::$process_album, $configs->max_photos_in_album);
        }
        else
        {
            $query = SK_MySQL::placeholder('SELECT COUNT(`photo`.`photo_id`) FROM `' . TBL_PROFILE_PHOTO . '`  AS `photo`
				LEFT JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
				WHERE `profile_id`=? AND `number`!=0 AND `album`.`photo_id` IS NULL
				ORDER BY `number` ASC LIMIT ?', $profile_id, $configs->max_count);
        }

        return (int) SK_MySQL::query($query)->fetch_cell();
    }

    public static function getAllPhotoCount( $profile_id = null )
    {
        if ( !($profile_id = intval($profile_id)) )
        {
            $profile_id = SK_HttpUser::profile_id();
        }
        $query = 'SELECT COUNT(`photo_id`) FROM `' . TBL_PROFILE_PHOTO . '` WHERE `profile_id`=' . $profile_id;
        return (int) SK_MySQL::query($query)->fetch_cell();
    }
}

class SK_ProfilePhotoException extends Exception
{
    private $error_key;

    /**
     * Constructor.
     *
     * @param string $error_key
     */
    public function __construct( $error_key, $code = null )
    {
        $this->error_key = $error_key;

        parent::__construct('', $code);
    }

    /**
     * Get Image error key.
     *
     * @return string
     */
    public function getErrorKey()
    {
        return $this->error_key;
    }
}
