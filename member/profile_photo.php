<?php

require_once dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

$Layout = SK_Layout::getInstance();

$albumId = null;

if ( !empty(SK_HttpRequest::$GET['album']) )
{
    $albumId = SK_HttpRequest::$GET['album'];
}
else if ( !empty(SK_HttpRequest::$GET["photo_id"]) )
{
    $albumId = app_PhotoAlbums::getPhotoAlbum(SK_HttpRequest::$GET["photo_id"]);
}

if ( empty($albumId) )
{
    $httpdoc = new component_PhotoView;
}
else
{
    $album = app_PhotoAlbums::getAlbum($albumId);

    if ( $album->getPrivacy() == 'public' || $album->isAccessable(SK_HttpUser::profile_id()) || ( $album->getPrivacy() == 'friends_only' && $album->getProfile_id() == SK_HttpUser::profile_id()) )
    {
        $httpdoc = new component_PhotoView;
    }
    else
    {
        $httpdoc = new component_PhotoAlbumAccess($album);
    }
}

$Layout->display($httpdoc);
