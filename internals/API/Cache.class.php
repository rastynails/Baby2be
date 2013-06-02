<?php

class SK_Cache
{
    public static function set($key, $value, $liveTime = null)
    {
        $liveTime = empty($liveTime) ? PHP_INT_MAX - time(): $liveTime;
        
        if ($value === null)
        {
            $query = SK_MySQL::placeholder(
                'DELETE FROM `' . TBL_CACHE . '` WHERE `key`="?"'
            , $key);
        }
        else
        {
            $query = SK_MySQL::placeholder(
                'REPLACE INTO `' . TBL_CACHE . '` SET `key`="?", `value`="?", expireTime=?'
            , $key, $value, time() + $liveTime);    
        }

        SK_MySQL::query($query);
        
        return (bool) SK_MySQL::affected_rows();
    }
    
    public static function get($key)
    {
        $query = SK_MySQL::placeholder(
            'SELECT `value` FROM `' . TBL_CACHE . '` WHERE `key`="?"'
        , $key);
        
        return SK_MySQL::query($query)->fetch_cell();
    }
    
    public static function clear($key)
    {
        $query = SK_MySQL::placeholder(
            'DELETE FROM `' . TBL_CACHE . '` WHERE `key`="?"'
        , $key);
        
        SK_MySQL::query($query);
    }
    
    public static function clearExpired()
    {
        $query = SK_MySQL::placeholder(
            'DELETE FROM `' . TBL_CACHE . '` WHERE `expireTime` < ?'
        , time());
        
        SK_MySQL::query($query);
    }
}