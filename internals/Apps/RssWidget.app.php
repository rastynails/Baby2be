<?php

class app_RssWidget
{
    private static $cacheTime = 3600;
    
    public static function save($dto)
    {
        $query = SK_MySQL::placeholder(
            "REPLACE INTO `" . TBL_RSS_WIDGET . "` SET `cmpId`=?, `url`='?', title='?', `count`=?, `showDesc`=?"
        , $dto->cmpId, $dto->url, $dto->title, $dto->count, $dto->showDesc);
        
        SK_MySQL::query($query);
        $result = (bool) SK_MySQL::affected_rows();
        
        if ($result)
        {
            SK_Cache::set('rss-widget-' . $dto->cmpId, null);
        }
        
        return $result;
    }
    
    public static function getRss($cmpId, $count = null)
    {
        $cahce = SK_Cache::get('rss-widget-' . $cmpId);

        if ($cahce)
        {
            return json_decode($cahce, true);
        }
        
        $cmp = self::find($cmpId);
        if (!$cmp || empty($cmp->url))
        {
            return false;
        }
        
        $rss = app_Rss::read($cmp->url, $count);
        
        if ($rss)
        {
            SK_Cache::set('rss-widget-' . $cmpId, json_encode($rss), self::$cacheTime);
        }
        
        return $rss;
    }
    
    public static function find($cmpId)
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_RSS_WIDGET . "` WHERE `cmpId`=?", $cmpId);
        
        return SK_MySQL::query($query)->fetch_object();
    }
    
    public static function remove($cmpId)
    {
        $query = SK_MySQL::placeholder("DELETE FROM `" . TBL_RSS_WIDGET . "` WHERE `cmpId`=?", $cmpId);
        SK_MySQL::query($query);
        
        $result = (bool) SK_MySQL::affected_rows();
        
        if ($result)
        {
            SK_Cache::set('rss-widget-' . $cmpId, null);
        }
        
        return $result;        
    }
}