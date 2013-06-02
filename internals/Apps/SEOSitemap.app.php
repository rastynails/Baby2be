<?php

require_once DIR_INTERNALS . 'Debug.func.php';
require_once DIR_ADMIN_INC . 'class.admin_navigation.php';

class app_SEOSitemap
{
    private static $header = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    private static $footer = '</urlset>';

    private static $mobileSetup = false;

    private static $mobileDomain = null;

    static function generate()
    {
        self::$mobileSetup = self::isMobileSetup();
        self::$mobileDomain = self::getMobileDomain();

        $nav_doc = new AdminNavigation();
        $docTree = $nav_doc->GetAllDocuments();

        return self::$header . self::getUrls($docTree) . self::getMobileUrls() . self::$footer;
    }

    static private function getUrls( $docTree )
    {
        $str = '';
        foreach ( $docTree as $key => $node )
        {
            switch ( $node['document_key'] )
            {
                case 'profile':

                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getProfileURLs($url);
                    break;

                case 'profile_photo':

                    if ( !$node['access_guest'] )
                        break;

                    $url = SITE_URL . $node['url'];
                    $str .= self::getPhotoURLs($url);
                    break;

                case 'profile_video_view':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getVideoURLs($url);
                    break;

                case 'profile_music':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getProfileMusicURLs($url);
                    break;


                case 'music_view':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getMusicURLs($url);
                    break;

                case 'blog_post_view':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getBlogEntryURLs($url);
                    break;

                case 'topic':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getForumTopicURLs($url);
                    break;

                case 'forum':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getForumURLs($url);
                    break;

                case 'event':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getEventURLs($url);
                    break;

                case 'classifieds_item':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getClassifiedsItemURLs($url);
                    break;

                case 'classifieds_category':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getClassifiedsCategoryURLs($url);
                    break;

                case 'classifieds_item_list':
                    if ( !$node['access_guest'] )
                        break;
                    $url = SITE_URL . $node['url'];
                    $str .= self::getClassifiedsItemListURLs($url);
                    break;
                default:
                    if ( !$node['access_guest'] )
                        break;

                    $url = SITE_URL . $node['url'];
                    $str .= "<url><loc>{$url}</loc></url>\n";
                    break;
            }

            if ( !empty($node['sub_docs']) )
            {
                foreach ( $node['sub_docs'] as $child )
                {
                    $str .= self::getUrls($child);
                }
            }
        }

        return $str;
    }

    private static function isMobileSetup()
    {
        $dir = DIR_SITE_ROOT . SK_Config::section('mobile')->get('mobile_directory') . DIRECTORY_SEPARATOR;
        $conf_file = $dir . 'mconfig.php';

        return file_exists($conf_file);
    }

    private static function getMobileDomain()
    {
        if ( !self::$mobileSetup )
        {
            return false;
        }
        $dir = DIR_SITE_ROOT . SK_Config::section('mobile')->get('mobile_directory') . DIRECTORY_SEPARATOR;
        $conf_file = $dir . 'mconfig.php';
        require_once $conf_file;

        return MOBILE_SITE_DOMAIN;
    }

    private static function getMobileUrls()
    {
        $str = '';
        if ( self::$mobileSetup )
        {
            $str .= "<url><loc>" . self::$mobileDomain . "</loc></url>\n";
            $str .= "<url><loc>" . self::$mobileDomain . "about</loc></url>\n";
            $str .= "<url><loc>" . self::$mobileDomain . "terms</loc></url>\n";
            $str .= "<url><loc>" . self::$mobileDomain . "privacy</loc></url>\n";
            $str .= "<url><loc>" . self::$mobileDomain . "members</loc></url>\n";
            $str .= "<url><loc>" . self::$mobileDomain . "search</loc></url>\n";
            $str .= "<url><loc>" . self::$mobileDomain . "sign_in</loc></url>\n";
        }

        return $str;
    }

    private static function getProfileURLs( $baseUrlPart )
    {
        $str = '';
        $modRewrite = SK_Config::section("navigation")->section("settings")->mod_rewrite === true;

        $start = 0;
        $count = 100;
        while ( true )
        {
            $query = 'SELECT `profile_id`, `username` from `' . TBL_PROFILE . '` AS `p`
                WHERE ' . app_Profile::SqlActiveString('p') .
                  " ORDER BY `p`.`profile_id` ASC
                LIMIT {$start}, {$count}";

            $list = SK_MySQL::queryForList($query);

            if ( !$list )
            {
                break;
            }

            foreach ( $list as $row )
            {
                if ( $modRewrite )
                {
                    $row['username'] = htmlspecialchars($row['username']);
                    $str .= "<url><loc>" . SITE_URL . "member/profile_" . $row['username'] . ".html</loc></url>\n";
                }
                else
                {
                    $str .= "<url><loc>{$baseUrlPart}?profile_id={$row['profile_id']}</loc></url>\n";
                }

                if ( self::$mobileSetup )
                {
                    $str .= "<url><loc>" . self::$mobileDomain . 'profile/' . $row['username'] . "</loc></url>\n";
                }
            }

            $start += $count;
        }

        return $str;
    }

    private static function getPhotoURLs( $baseUrlPart )
    {
        $str = '';
        $modRewrite = SK_Config::section("navigation")->section("settings")->mod_rewrite === true;

        $start = 0;
        $count = 100;
        while ( true )
        {
            $query = 'SELECT `ph`.`photo_id`, `p`.`username`  FROM `' . TBL_PROFILE_PHOTO . '` AS `ph`
		         LEFT JOIN `' . TBL_PROFILE . '` as `p` ON `p`.`profile_id` = `ph`.`profile_id`
		         WHERE ' . app_PhotoList::activeSqlStr('ph') .
                  " ORDER BY `ph`.`photo_id` ASC
                LIMIT {$start}, {$count}";

            $list = SK_MySQL::queryForList($query);

            if ( !$list )
            {
                break;
            }

            foreach ( $list as $row )
            {
                if ( $modRewrite )
                {
                    $row['username'] = htmlspecialchars($row['username']);
                    $str .= "<url><loc>" . SITE_URL . "member/photos/" . $row['username'] . "/" . $row['photo_id'] . "</loc></url>\n";
                }
                else
                {
                    $str .= "<url><loc>{$baseUrlPart}?photo_id={$row['photo_id']}</loc></url>\n";
                }

                if ( self::$mobileSetup )
                {
                    $str .= "<url><loc>" . self::$mobileDomain . 'profile/' . $row['username'] . "/photo/".$row['photo_id']."</loc></url>\n";
                }
            }

            $start += $count;
        }

        return $str;
    }

    private static function getVideoURLs( $baseUrlPart )
    {
        $str = '';
        $modRewrite = SK_Config::section("navigation")->section("settings")->mod_rewrite === true;

        $start = 0;
        $count = 100;
        while ( true )
        {
            $query = 'SELECT `video_id`,`hash` FROM `' . TBL_PROFILE_VIDEO . '` AS `v`
		         WHERE ' . app_VideoList::sqlActiveCond('v') .
                  " ORDER BY `v`.`video_id` ASC
                LIMIT {$start}, {$count}";

            $list = SK_MySQL::queryForList($query);

            if ( !$list )
            {
                break;
            }

            foreach ( $list as $row )
            {
                if ( $modRewrite )
                {
                    $str .= "<url><loc>" . SITE_URL . "member/video/" . $row['hash'] . "</loc></url>\n";
                }
                else
                {
                    $str .= "<url><loc>{$baseUrlPart}?videokey={$row['hash']}</loc></url>\n";
                }
            }

            $start += $count;
        }

        return $str;
    }

    private static function getMusicURLs( $baseUrlPart )
    {
        $str = '';
        $modRewrite = SK_Config::section("navigation")->section("settings")->mod_rewrite === true;

        $start = 0;
        $count = 100;
        while ( true )
        {
            $query = 'SELECT `music_id`, `profile_id`, `hash` FROM `' . TBL_PROFILE_MUSIC . '` AS `m`
		         WHERE ' . app_MusicList::sqlActiveCond('m') .
                  " ORDER BY `m`.`music_id` ASC
                LIMIT {$start}, {$count}";

            $list = SK_MySQL::queryForList($query);

            if ( !$list )
            {
                break;
            }

            foreach ( $list as $row )
            {
                if ( $modRewrite )
                {
                    $str .= "<url><loc>" . SITE_URL . "member/music/" . $row['hash'] . "</loc></url>\n";
                }
                else
                {
                    $str .= "<url><loc>{$baseUrlPart}?musickey={$row['hash']}</loc></url>\n";
                }
            }

            $start += $count;
        }

        return $str;
    }

    private static function getProfileMusicURLs( $baseUrlPart )
    {
        $str = '';
        $start = 0;
        $count = 100;
        while ( true )
        {
            $query = 'SELECT `profile_id` FROM `' . TBL_PROFILE_MUSIC . '` AS `m`
		         WHERE ' . app_MusicList::sqlActiveCond('m') .
                  " ORDER BY `m`.`music_id` ASC
                LIMIT {$start}, {$count}";

            $list = SK_MySQL::queryForList($query);

            if ( !$list )
            {
                break;
            }

            foreach ( $list as $row )
            {
                $str .= "<url><loc>{$baseUrlPart}?profile_id={$row['profile_id']}</loc></url>\n";
            }

            $start += $count;
        }

        return $str;
    }

    private static function getForumTopicURLs( $baseUrlPart )
    {
        $str = '';
        $start = 0;
        $count = 100;
        while ( true )
        {
            $query = 'SELECT `forum_topic_id` FROM ' . TBL_FORUM_TOPIC .
                " ORDER BY `forum_topic_id` ASC
                LIMIT {$start}, {$count}";

            $list = SK_MySQL::queryForList($query);

            if ( !$list )
            {
                break;
            }

            foreach ( $list as $row )
            {
                $str .= "<url><loc>{$baseUrlPart}?topic_id={$row['forum_topic_id']}</loc></url>\n";
            }

            $start += $count;
        }

        return $str;
    }

    private static function getForumURLs( $baseUrlPart )
    {
        $str = '';
        $start = 0;
        $count = 100;
        while ( true )
        {
            $query = 'SELECT `forum_id` FROM ' . TBL_FORUM .
                " ORDER BY `forum_id` ASC
                LIMIT {$start}, {$count}";

            $list = SK_MySQL::queryForList($query);

            if ( !$list )
            {
                break;
            }

            foreach ( $list as $row )
            {
                $str .= "<url><loc>{$baseUrlPart}?forum_id={$row['forum_id']}</loc></url>\n";
            }

            $start += $count;
        }

        return $str;
    }

    private static function getBlogEntryURLs( $baseUrlPart )
    {
        $str = '';
        $service = app_BlogService::newInstance();
        $list = $service->findAllActivePostList();
        foreach ( $list as $elem )
        {
            $post = $elem['dto'];
            $str .= "<url><loc>{$baseUrlPart}?postId={$post->getId()}</loc></url>\n";
        }

        return $str;
    }

    private static function getEventURLs( $baseUrlPart )
    {
        $str = '';

        $service = app_EventService::newInstance();
        $list = $service->findAllActiveEvents();
        foreach ( $list as $elem )
        {
            $dto = $elem['dto'];
            $str .= "<url><loc>{$baseUrlPart}?eventId={$dto->getId()}</loc></url>\n";
        }

        return $str;
    }

    private static function getClassifiedsItemURLs( $baseUrlPart )
    {
        $str = '';

        $service = app_ClassifiedsItemService::newInstance();
        $list = $service->findAllActiveItems();

        foreach ( $list as $elem )
        {
            $str .= "<url><loc>{$baseUrlPart}?item_id={$elem->getId()}</loc></url>\n";
        }

        return $str;
    }

    private static function getClassifiedsCategoryURLs( $baseUrlPart )
    {
        $str = '';

        $service = app_ClassifiedsGroupService::newInstance();
        $list = $service->findAllActiveCategories();

        foreach ( $list as $elem )
        {
            $str .= "<url><loc>{$baseUrlPart}?cat_id={$elem->getId()}</loc></url>\n";
        }

        return $str;
    }

    private static function getClassifiedsItemListURLs( $baseUrlPart )
    {
        $str = '';

        $str .= "<url><loc>{$baseUrlPart}?entity=wanted&amp;type=latest</loc></url>\n";
        $str .= "<url><loc>{$baseUrlPart}?entity=wanted&amp;type=ending_soon</loc></url>\n";
        $str .= "<url><loc>{$baseUrlPart}?entity=offer&amp;type=latest</loc></url>\n";
        $str .= "<url><loc>{$baseUrlPart}?entity=offer&amp;type=ending_soon</loc></url>\n";

        return $str;
    }

    public static function cronDoJob()
    {
        $configs = SK_Config::section('seo-sitemap')->getConfigsList();

        $timestamp = $configs['updateTimestamp']->value;

        if ( $configs['isUpdated']->value && ( time() < $timestamp + 60 * 60 * 24 ) )
            return;

        $map = app_SEOSitemap::generate();
        $fd = fopen(DIR_SITE_ROOT . 'sitemap.xml', 'w');
        fwrite($fd, $map);

        if ( $configs['doPingGoogle']->value )
        {
            self::pingGoogle();
        }

        if ( $configs['doPingYahoo']->value && strlen($configs['yahoo_appId']->value) )
            self::pingYahoo($configs['yahoo_appId']->value);

        $section = new SK_Config_Section('seo-sitemap', 0, true);
        $section->set('isUpdated', 1);
        $section->set('updateTimestamp', time());
    }

    static function pingGoogle()
    {
        $sitemapURL = SITE_URL . 'sitemap.xml';

        @file_get_contents("http://www.google.com/webmasters/sitemaps/ping?sitemap={$sitemapURL}");
    }

    static function pingYahoo( $appId )
    {
        $sitemapURL = SITE_URL . 'sitemap.xml';

        @file_get_contents("http://search.yahooapis.com/SiteExplorerService/V1/updateNotification?appid={$appId}&url={$sitemapURL}");
    }
}