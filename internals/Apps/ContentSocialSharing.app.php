<?php

class app_ContentSocialSharing
{
    public static function getBlogPostById( $blog_id )
    {
        if ( empty( $blog_id ) )
        {
            return;
        }
        
        $query = SK_MySQL::placeholder("SELECT * FROM ". TBL_BLOG_POST. " WHERE `id` = ?", $blog_id);
        
        return SK_MySQL::query($query)->fetch_assoc();
    }
    
    public static function getVideoByHash( $hash )
    {
        if ( empty( $hash ) )
        {
            return;
        }
        
        $profile_id = app_ProfileVideo::getVideoOwner( $hash );
        $video_info = app_ProfileVideo::getVideoInfo( $profile_id, $hash );
        $video_info['video_url'] = app_ProfileVideo::getVideoURL($hash, $video_info['extension']);
        return $video_info;
    }
}