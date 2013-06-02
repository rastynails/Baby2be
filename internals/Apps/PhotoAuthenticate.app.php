<?php

class app_PhotoAuthenticate
{
    /**
     *
     * @param $profileId
     * @param $code
     * @return dto_PhotoAuthenticate
     */
    private static function addRequest( $profileId, $code )
    {
        $item = self::findRequest($profileId);
        if ( !$item )
        {
            $item = new dto_PhotoAuthenticate();
            $item->profileId = $profileId;
        }

        $item->state = dto_PhotoAuthenticate::STATE_IN_PROCESS;
        $item->code = $code;
        $item->timeStamp = time();

        return dao_PhotoAuthenticate::getInstance()->save($item);
    }

    private static function generateRequestCode()
    {
        return rand(0, 999999999);
    }

    public static function getPhotoPath( $profileId, $code )
    {
        return DIR_USERFILES . 'photo_auth_' . intval($profileId) . '_' . trim($code) . '.jpg';
    }

    public static function getPhotoUrl( $profileId, $code )
    {
        return URL_USERFILES . 'photo_auth_' . intval($profileId) . '_' . trim($code) . '.jpg';
    }

    public function getPhotoUrlByRequest( dto_PhotoAuthenticate $request )
    {
        return self::getPhotoUrl($request->profileId, $request->code);
    }

    public function getPhotoPathByRequest( dto_PhotoAuthenticate $request )
    {
        return self::getPhotoUrl($request->profileId, $request->code);
    }

    public static function sendRequest( $profileId )
    {
        $code = self::generateRequestCode();
        $item = self::addRequest($profileId, $code);
        try
        {
            $mail = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
                ->setRecipientProfileId($profileId)
                ->assignVar('code', $item->code)
                ->assignVar('confirmation_url', SK_Navigation::href('photo_auth'))
                ->setTpl('photo_verification');

            app_Mail::send($mail);
        }
        catch ( SK_EmailException $e )
        {
            return false;
        }

        return true;
    }

    /**
     *
     * @param $profileId
     * @return dto_PhotoAuthenticate
     */
    public static function findRequest( $profileId )
    {
        return dao_PhotoAuthenticate::getInstance()->findByProfileId($profileId);
    }

    public static function addResponce( $profileId, SK_TemporaryFile $file )
    {
        $request = self::findRequest($profileId);

        app_Image::resize($file->getPath(), 400, 400);
        app_Image::convert($file->getPath(), IMAGETYPE_JPEG, app_PhotoAuthenticate::getPhotoPath($request->profileId, $request->code));

        $file->move(self::getPhotoPath($request->profileId, $request->code));
        $request->state = dto_PhotoAuthenticate::STATE_RESPONDED;

        return dao_PhotoAuthenticate::getInstance()->save($request);
    }

    public static function isResponded( dto_PhotoAuthenticate $request )
    {
        return $request->state == dto_PhotoAuthenticate::STATE_RESPONDED;
    }


    public static function findRequestList($limit = array())
    {
        return dao_PhotoAuthenticate::getInstance()->findListByState(dto_PhotoAuthenticate::STATE_IN_PROCESS, $limit);
    }

    public static function findResponceList($limit = array())
    {
        return dao_PhotoAuthenticate::getInstance()->findListByState(dto_PhotoAuthenticate::STATE_RESPONDED, $limit);
    }


    public static function findRequestCount()
    {
        return dao_PhotoAuthenticate::getInstance()->findCountByState(dto_PhotoAuthenticate::STATE_IN_PROCESS);
    }

    public static function findResponceCount()
    {
        return dao_PhotoAuthenticate::getInstance()->findCountByState(dto_PhotoAuthenticate::STATE_RESPONDED);
    }


    public static function findPendingResponceCount()
    {
        return dao_PhotoAuthenticate::getInstance()->findPendingResponceCount();
    }

    public static function findPendingResponceList($limit = array())
    {
        return dao_PhotoAuthenticate::getInstance()->findPendingResponceList($limit);
    }

    public static function authenticatePhoto( $photoId, $authed = true )
    {
        $query = SK_MySQL::placeholder(
            'UPDATE `' . TBL_PROFILE_PHOTO . '` SET `authed`=? WHERE `photo_id`=?'
        , (int) $authed, (int) $photoId);

        SK_MySQL::query($query);

        return (bool) SK_MySQL::affected_rows();
    }

    public static function getIconPath()
    {
        $icon = SK_Config::section('photo_auth')->icon;
        return empty($icon) ? null : DIR_USERFILES . "photo_auth_icon_$icon->rand.$icon->ext";
    }

    public static function getIconUrl()
    {
        $icon = SK_Config::section('photo_auth')->icon;
        return empty($icon) ? null : URL_USERFILES . "photo_auth_icon_$icon->rand.$icon->ext";
    }

    public static function deleteIcon()
    {
        $path = self::getIconPath();
        if ( !empty($path) )
        {
            @unlink(self::getIconPath());
        }
        SK_Config::section('photo_auth')->set('icon', false);
    }

    public static function saveIcon(SK_TemporaryFile $file)
    {
        SK_Config::section('photo_auth')->set('icon', array(
            'rand' => rand(0, 9999),
            'ext' => $file->getExtension()
        ));

        $file->move(self::getIconPath());
    }
}

class dao_PhotoAuthenticate extends dao_Base
{
    private static $instance;

    /**
     *
     * @return dao_PhotoAuthenticate
     */
    public static function getInstance()
    {
        if ( !isset(self::$instance) )
        {
            self::$instance = new self();
        }

        return self::$instance;
    }


    protected function getTable()
    {
        return TBL_PHOTO_AUTHENTICATE;
    }

    protected function getDtoClass()
    {
        return 'dto_PhotoAuthenticate';
    }

    /**
     *
     * @param $profileId
     * @return dto_PhotoAuthenticate
     */
    public function findByProfileId( $profileId )
    {
        return $this->fetchItem(SK_MySQL::placeholder('
           SELECT * FROM `' . $this->getTable() . '` WHERE `profileId`=?
        ', $profileId));
    }

    public function findListByState( $state, $limit = array() )
    {
        $limitStr = empty($limit) ? '' : "LIMIT {$limit[0]}, {$limit[1]}";

        $query = SK_MySQL::placeholder("SELECT * FROM `" . $this->getTable() . "`
            WHERE `state`='?' $limitStr", $state);

        return $this->fetchList($query);
    }

    public function findCountByState( $state )
    {
        $query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `" . $this->getTable() . "`
            WHERE `state`='?'", $state);

        return SK_MySQL::query($query)->fetch_cell();
    }

    public function findPendingResponceCount()
    {
        $query = SK_MySQL::placeholder(
            "SELECT COUNT( DISTINCT a.`profileId`) FROM `" . $this->getTable() . "` as `a`
                INNER JOIN `" . TBL_PROFILE_PHOTO . "` `p` ON `a`.`profileId` = `p`.profile_id
                WHERE `a`.`state`='?' AND `p`.`authed`=0 AND `p`.`number`!=0", dto_PhotoAuthenticate::STATE_RESPONDED);

        return SK_MySQL::query($query)->fetch_cell();
    }

    public function findPendingResponceList($limit = null)
    {
        $limitString = '';
        if ( !empty($limit) )
        {
            $limitString = ' LIMIT ' . $limit[0] . ', ' . $limit[1];
        }

        $query = SK_MySQL::placeholder(
            "SELECT DISTINCT a.* FROM `" . $this->getTable() . "` as `a`
                INNER JOIN `" . TBL_PROFILE_PHOTO . "` `p` ON `a`.`profileId` = `p`.profile_id
                WHERE `a`.`state`='?' AND `p`.`authed`=0 AND `p`.`number`!=0" . $limitString, dto_PhotoAuthenticate::STATE_RESPONDED);

        return $this->fetchList($query, 'id');
    }
}


class dao_Base
{
    public function delete( $item )
    {
        SK_MySQL::query(
            'DELETE FROM `' . $this->getTable() . '` WHERE `id`=' . intval($item->id)
        );

        return SK_MySQL::affected_rows();
    }

    public function deleteById( $id )
    {
        SK_MySQL::query(
            'DELETE FROM `' . $this->getTable() . '` WHERE `id`=' . intval($id)
        );

        return SK_MySQL::affected_rows();
    }

    public function save( $item )
    {
        $fields = array();
        foreach ( $item as $key => $val )
        {
            if ( !empty($val) )
            {
                $fields[] = "`$key`='" . SK_MySQL::realEscapeString($val) . "'";
            }
        }

        if ( empty($item->id) )
        {
            $query = 'INSERT INTO `' . $this->getTable() . '` SET ' . implode(', ', $fields);
            SK_MySQL::query($query);

            $item->id = SK_MySQL::insert_id();
        }
        else
        {
            $query = 'UPDATE `' . $this->getTable() . '` SET ' . implode(', ', $fields)
                . " WHERE `id`=" . intval($item->id);
            SK_MySQL::query($query);
        }

        return $item;
    }

    public function fetchList( $query, $keyField = null )
    {
        $r = SK_MySQL::query($query);

        $out = array();
        while ( $item = $r->fetch_object($this->getDtoClass()) )
        {
            if ( !empty($keyField) )
            {
                $out[$item->$keyField] = $item;
            }
            else
            {
                $out[] = $item;
            }
        }

        return $out;
    }

    public function fetchItem( $query )
    {
        $r = SK_MySQL::query($query);

        return $r->fetch_object($this->getDtoClass());
    }

    public function findAll()
    {
        $query = "SELECT * FROM `" . $this->getTable() . "`";

        return $this->fetchList($query);
    }
}

class dto_PhotoAuthenticate
{
    const STATE_IN_PROCESS = 'in_process';
    const STATE_RESPONDED = 'responded';

    public $id;
    public $profileId;
    public $code;
    public $state;
    public $timeStamp;
}