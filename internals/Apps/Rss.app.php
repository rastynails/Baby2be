<?php

require_once DIR_INTERNALS . 'rss_reader' . DIRECTORY_SEPARATOR . 'rss.php';

class app_Rss
{
    public static function read($url, $count = null)
    {
        $rss = array();
        
        try
        {
            $rssIterator = new RSSIterator($url, $count);
        }
        catch (Exception $e)
        {
            return false;
        }
        
        foreach ( $rssIterator as $item )
        {
            $item->time = strtotime($item->date); 
            $rss[] = (array) $item;
        }
        
        return $rss;
    }
}