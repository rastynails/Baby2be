<?php

class app_VideoResource
{
    private $code;

    const RESOURCE_MYSPACE = 'myspace';
    const RESOURCE_YOUTUBE = 'youtube';
    const RESOURCE_METACAFE = 'metacafe';
    const RESOURCE_VIMEO = 'vimeo';
    const RESOURCE_BREAKCOM = 'breakcom';
    const RESOURCE_GOOGLEVIDEO = 'googlevideo';
    const RESOURCE_DAILYMOTION = 'dailymotion';

    const RESOURCE_UNDEFINED = 'unknown';

    private static $resources;

    public function __construct( $code )
    {
        $this->code = $code;

        $this->init();
    }

    private function init()
    {
        if ( !isset(self::$resources) )
        {
            self::$resources = array(
                self::RESOURCE_MYSPACE => 'http://mediaservices.myspace.com/',
                self::RESOURCE_YOUTUBE => 'http://www.youtube.com/',
                self::RESOURCE_METACAFE => 'http://www.metacafe.com/',
                self::RESOURCE_VIMEO => 'http://(player\.)?vimeo.com/',
                self::RESOURCE_BREAKCOM => 'http://www.break.com/index/',
                self::RESOURCE_GOOGLEVIDEO => 'http://video.google.com/',
                self::RESOURCE_DAILYMOTION => 'http://www.dailymotion.com/'
            );
        }
    }

    public function detectResource()
    {
        foreach ( self::$resources as $name => $url )
        {
            if ( preg_match("~$url~", $this->code) )
            {
                return $name;
            }
        }
        return self::RESOURCE_UNDEFINED;
    }

    public function getResourceThumbUrl()
    {
        $res = $this->detectResource();

        $className = 'VideoResource' . ucfirst($res);

        $class = new $className;

        $thumb = $class->getThumbUrl($this->code);

        return $thumb;
    }
}

class VideoResourceMyspace
{
    const clipUidPattern = 'http:\/\/mediaservices\.myspace\.com.*embed.aspx\/m=([0-9]*)';
    const thumbXmlPattern = 'http://mediaservices.myspace.com/services/rss.ashx?type=video&videoID=()';

    private static function getUid( $code )
    {
        $pattern = self::clipUidPattern;

        return preg_match("~{$pattern}~", $code, $match) ? $match[1] : null;
    }

    public static function getThumbUrl( $code )
    {
        if ( ($uid = self::getUid($code)) !== null )
        {
            $xmlUrl = str_replace('()', $uid, self::thumbXmlPattern);

            $cont = @file_get_contents($xmlUrl);
            
            if ( strlen($cont) )
            {
                $xml = @simplexml_load_string(str_replace('media:thumbnail', 'mediathumbnail', $cont));
                $url = $xml->channel->item->mediathumbnail['url'];
            }
            
            return strlen($url) ? $url : app_VideoResource::RESOURCE_UNDEFINED;
        }

        return app_VideoResource::RESOURCE_UNDEFINED;
    }
}

class VideoResourceYoutube
{
    const clipUidPattern = 'http:\/\/www\.youtube\.com\/(v|embed)\/([^?&"]+)[?&"]';
    const thumbUrlPattern = 'http://img.youtube.com/vi/()/default.jpg';

    private static function getUid( $code )
    {
        $pattern = self::clipUidPattern;

        return preg_match("~{$pattern}~", $code, $match) ? $match[2] : null;
    }

    public static function getThumbUrl( $code )
    {
        if ( ($uid = self::getUid($code)) !== null )
        {
            $url = str_replace('()', $uid, self::thumbUrlPattern);

            return strlen($url) ? $url : app_VideoResource::RESOURCE_UNDEFINED;
        }

        return app_VideoResource::RESOURCE_UNDEFINED;
    }
}

class VideoResourceMetacafe
{
    const clipUidPattern = 'http://www.metacafe.com/fplayer/([^/]+)/';
    const thumbApiPattern = 'http://www.metacafe.com/api/item/()/';

    private static function getUid( $code )
    {
        $pattern = self::clipUidPattern;

        return preg_match("~{$pattern}~", $code, $match) ? $match[1] : null;
    }

    public static function getThumbUrl( $code )
    {
        if ( ($uid = self::getUid($code)) !== null )
        {
            $xmlUrl = str_replace('()', $uid, self::thumbApiPattern);

            $ch = curl_init($xmlUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $fileCont = curl_exec($ch);
            curl_close($ch);

            if ( strlen($fileCont) )
            {
                preg_match("/media:thumbnail url=\"([^\"]\S*)\"/siU", $fileCont, $match);
                $url = isset($match[1]) ? $match[1] : app_VideoResource::RESOURCE_UNDEFINED;
            }

            return !empty($url) ? $url : app_VideoResource::RESOURCE_UNDEFINED;
        }

        return app_VideoResource::RESOURCE_UNDEFINED;
    }
}

class VideoResourceVimeo
{
    const clipUidPattern = 'http:\/\/vimeo\.com\/([0-9]*)["]|http:\/\/player\.vimeo\.com\/video\/([0-9]*)[\?]';
    const thumbXmlPattern = 'http://vimeo.com/api/v2/video/().xml';

    private static function getUid( $code )
    {
        $pattern = self::clipUidPattern;
        
        preg_match("~{$pattern}~", $code, $match);
        if ( !empty($match[2]) ) return $match[2];
        if ( !empty($match[1]) ) return $match[1];
        
        return null;
    }

    public static function getThumbUrl( $code )
    {
        if ( ($uid = self::getUid($code)) !== null )
        {
            $xmlUrl = str_replace('()', $uid, self::thumbXmlPattern);

            $ch = curl_init($xmlUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $fileCont = curl_exec($ch);
            curl_close($ch);
            
            if ( strlen($fileCont) )
            {
                $xml = @simplexml_load_string($fileCont);
                $url = (string)$xml->video->thumbnail_small;
            }

            return strlen($url) ? $url : app_VideoResource::RESOURCE_UNDEFINED;
        }

        return app_VideoResource::RESOURCE_UNDEFINED;
    }
}

class VideoResourceBreakcom
{
    const clipUidPattern = 'http:\/\/www\.break\.com\/index\/([^"]+)["]';
    const thumbXmlPattern = 'http://api.embed.ly/v1/api/oembed?url=http://www.break.com/index/()&format=xml';

    private static function getUid( $code )
    {
        $pattern = self::clipUidPattern;

        return preg_match("~{$pattern}~", $code, $match) ? $match[1] : null;
    }

    public static function getThumbUrl( $code )
    {
        if ( ($uid = self::getUid($code)) !== null )
        {
            $xmlUrl = str_replace('()', $uid, self::thumbXmlPattern);

            $cont = @file_get_contents($xmlUrl);

            if ( strlen($cont) )
            {
                $xml = @simplexml_load_string($cont);
                $url = (string)$xml->thumbnail_url;
            }

            return strlen($url) ? $url : app_VideoResource::RESOURCE_UNDEFINED;
        }

        return app_VideoResource::RESOURCE_UNDEFINED;
    }
}

class VideoResourceGooglevideo
{
    const clipUidPattern = 'http:\/\/video\.google\.com\/googleplayer\.swf\?docid=([^\"][a-zA-Z0-9-_]+)[&\"]';
    const thumbXmlPattern = 'http://video.google.com/videofeed?docid=()';

    private static function getUid( $code )
    {
        $pattern = self::clipUidPattern;

        return preg_match("~{$pattern}~", $code, $match) ? $match[1] : null;
    }

    public static function getThumbUrl( $code )
    {
        if ( ($uid = self::getUid($code)) !== null )
        {
            $xmlUrl = str_replace('()', $uid, self::thumbXmlPattern);

            $fileCont = @file_get_contents($xmlUrl);

            if ( strlen($fileCont) )
            {
                preg_match("/media:thumbnail url=\"([^\"]\S*)\"/siU", $fileCont, $match);

                $url = isset($match[1]) ? $match[1] : app_VideoResource::RESOURCE_UNDEFINED;
            }

            return !empty($url) ? $url : app_VideoResource::RESOURCE_UNDEFINED;
        }

        return app_VideoResource::RESOURCE_UNDEFINED;
    }
}

class VideoResourceDailymotion
{
    const clipUidPattern = 'http://www.dailymotion.com/(swf|embed)/video/([^"]+)"';
    const thumbUrlPattern = 'http://www.dailymotion.com/thumbnail/video/()';

    private static function getUid( $code )
    {
        $pattern = self::clipUidPattern;

        return preg_match("~{$pattern}~", $code, $match) ? $match[2] : null;
    }

    public static function getThumbUrl( $code )
    {
        if ( ($uid = self::getUid($code)) !== null )
        {
            $url = str_replace('()', $uid, self::thumbUrlPattern);

            return strlen($url) ? $url : app_VideoResource::RESOURCE_UNDEFINED;
        }

        return app_VideoResource::RESOURCE_UNDEFINED;
    }
}


class VideoResourceUnknown
{
    public static function getThumbUrl( $code )
    {
        return app_VideoResource::RESOURCE_UNDEFINED;
    }
}
