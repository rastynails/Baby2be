<?php

class app_Slideshow
{
    const IMAGE_PREFIX = 'slide_';

    private static $imageExtensions = array('jpg', 'jpeg', 'png', 'gif');

    public static function addSlide( $file, $label = null, $url = null )
    {
        if ( strlen($url) && !strstr($url, 'http://') && !strstr($url, 'https://') )
        {
            $url  = 'http://'.$url;
        }

        $time = time();
        $ext = self::getExtension($file['name']);
        $order = self::getNextOrder();

        $query = SK_MySQL::placeholder("INSERT INTO `" . TBL_SLIDESHOW_SLIDE . "`
            SET `label` = '?', `url` = '?', `order` = ?,
            `ext` = '?', `addStamp` = ?",
            $label, $url, $order, $ext, $time);

        SK_MySQL::query($query);

        $id = SK_MySQL::insert_id();

        $path = self::getImagePath($id, $time, $ext);

        if ( !move_uploaded_file($file['tmp_name'], $path) )
        {
            self::deleteSlide($id);

            return false;
        }
        else {
            list($width, $height) = getimagesize($path);

            if ( $width > 1000 )
            {
                try {
                    app_Image::resize($path, 1000, null, false, $path);
                }
                catch ( SK_ImageException $e )
                {
                    self::deleteSlide($id);

                    return false;
                }
            }

            $query = SK_MySQL::placeholder("UPDATE `" . TBL_SLIDESHOW_SLIDE . "` SET `width` = ?, `height` = ?
               WHERE `id` = ?", $width, $height, $id);

            SK_MySQL::query($query);
        }

        return $id;
    }

    public static function getImagePath( $imageId, $hash, $ext )
    {
        return DIR_USERFILES . self::IMAGE_PREFIX . $imageId . '_' . $hash . '.' . $ext;
    }

    public static function getImageUrl( $imageId, $hash, $ext )
    {
        return URL_USERFILES . self::IMAGE_PREFIX . $imageId . '_' . $hash . '.' . $ext;
    }

    public static function validateImage( $fileName )
    {
        if ( !( $fileName = trim($fileName) ) )
        {
            return false;
        }

        $extension = self::getExtension($fileName);

        return in_array($extension, self::$imageExtensions);
    }

    public static function getExtension( $fileName )
    {
        return strtolower(substr($fileName, (strrpos($fileName, '.') + 1)));
    }

    public static function getNextOrder()
    {
        return self::getMaxOrder() + 1;
    }

    public static function getSlideList()
    {
        $query = "SELECT * FROM `" . TBL_SLIDESHOW_SLIDE . "` ORDER BY `order` ASC";

        $list = SK_MySQL::queryForList($query);

        if ( !$list )
        {
            return array();
        }

        foreach ( $list as &$image )
        {
            $image['imageUrl'] = self::getImageUrl($image['id'], $image['addStamp'], $image['ext']);
        }

        return $list;
    }

    public static function deleteSlide( $id )
    {
        if ( !$id )
        {
            return false;
        }

        $slide = self::getSlideById($id);

        if ( !$slide )
        {
            return false;
        }

        $path = self::getImagePath($id, $slide['addStamp'], $slide['ext']);

        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_SLIDESHOW_SLIDE . "` WHERE `id` = ?", $id);
        SK_MySQL::query($query);

        @unlink($path);

        return true;
    }

    public static function changeSlidesOrder( $id, $move = 'up' )
    {
        $slide = self::getSlideById($id);
        if ( !$slide )
        {
            return false;
        }

        $currentOrder = $slide['order'];

        switch ( $move )
        {
            case 'up':
                $destSlide = self::findPreviousSlide($id, $currentOrder);
                break;

            case 'down':
            default:
                $destSlide = self::findNextSlide($id, $currentOrder);
                break;
        }

        if ( !$destSlide )
        {
            return false;
        }

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_SLIDESHOW_SLIDE . "` SET `order` = ?
            WHERE `id` = ?", $destSlide['order'], $slide['id']);
        SK_MySQL::query($query);

        $query = SK_MySQL::placeholder("UPDATE `" . TBL_SLIDESHOW_SLIDE . "` SET `order` = ?
            WHERE `id` = ?", $currentOrder, $destSlide['id']);
        SK_MySQL::query($query);

        return true;
    }

    public static function getSlideById( $id )
    {
        if ( !$id )
        {
            return false;
        }

        $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_SLIDESHOW_SLIDE . "`
            WHERE `id` = ?", $id);

        return SK_MySQL::query($query)->fetch_assoc();
    }

    public static function getSizes( $slides )
    {
        if ( !$slides )
        {
            return null;
        }

        $res = array();
        foreach ( $slides as $slide )
        {
            $res[$slide['id']] = array('width' => $slide['width'], 'height' => $slide['height']);
        }

        return $res;
    }

    /*  Private methods   */

    private static function getMaxOrder()
    {
        $query = "SELECT MAX(`order`) FROM `" . TBL_SLIDESHOW_SLIDE . "`";

        return SK_MySQL::query($query)->fetch_cell();
    }

    private static function findPreviousSlide( $imageId, $order )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_SLIDESHOW_SLIDE . "`
            WHERE `id` != ? AND `order` < ?
            ORDER BY `order` DESC
            LIMIT 1", $imageId, $order);

        return SK_MySQL::query($query)->fetch_assoc();
    }

    private static function findNextSlide( $imageId, $order )
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_SLIDESHOW_SLIDE . "`
            WHERE `id` != ? AND `order` > ?
            ORDER BY `order` ASC
            LIMIT 1", $imageId, $order);

        return SK_MySQL::query($query)->fetch_assoc();
    }
}