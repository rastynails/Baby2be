<?php

$file_key = 'profile';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC . 'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_membership.php' );
require_once( DIR_ADMIN_INC . 'class.admin_profile_field.php' );
require_once( DIR_ADMIN_INC . 'class.admin_profile.php' );


$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC . 'fnc.profile.php' );

$profile_id = intval($_GET['profile_id']);

if ( !is_numeric($_GET['profile_id']) || !intval($_GET['profile_id']) )
    throw new Exception('Undefined profile id');

if ( in_array($_GET['status'], array('active', 'approval', 'suspended')) )
    $media_status = $_GET['status'];



if ( $_POST['delete_media'] )
{
    if ( is_array($_POST['media_file_arr']) )
    {
        foreach ( $_POST['media_file_arr'] as $_media_id )        {
            app_ProfileVideo::deleteVideo($_media_id);
        }
        $frontend->registerMessage('Files deleted');
    }
    else    {
        $frontend->registerMessage('Select file', 'notice');
    }
}

if ( $_POST['set_status'] )
{
    if ( is_array($_POST['media_file_arr']) )
    {
        foreach ( $_POST['media_file_arr'] as $_media_id )
        {
            adminProfile::setMediaStatus($_media_id, $_POST['media_status']);

            $list = app_UserActivities::getWhere("type IN ('media_upload', 'video_comment') and item={$_media_id}");
            if ( is_array($list) )
            {
                foreach ( $list as $action )
                {
                    switch ( $_POST['media_status'] )
                    {
                        case 'active':
                        case 'approval':

                            app_UserActivities::setStatus($action['skadate_user_activity_id'], $_POST['media_status']);
                            break;

                        case 'suspended':
                            app_UserActivities::setStatus($action['skadate_user_activity_id'], 'approval');
                    }
                }
            }

            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            {
                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => FEATURE_VIDEO,
                        'entityType' => 'media_upload',
                        'entityId' => $_media_id,
                        'userId' => $profile_id,
                        'status' => ( $_POST['media_status'] == 'active' ) ? 'active' : 'approval'
                    )
                );
                app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);
            }
        }

        $frontend->registerMessage('Selected files status changed');
    }
    else
    {
        $frontend->registerMessage('Select file', 'notice');
    }
}

$video_arr = app_ProfileVideo::getProfileVideo($profile_id, $_GET['status'], true);

$videos = $video_arr['list'];

foreach ( $videos as $_key => $_video_file )
{
    $videos[$_key]['href'] = app_ProfileVideo::getVideoURL($_video_file['hash'], $_video_file['extension']);
    $videos[$_key]['title'] = strip_tags($videos[$_key]['title']);
    $videos[$_key]['description'] = strip_tags($videos[$_key]['description']);
}

$frontend->assign_by_ref('video_arr', $videos);

$frontend->assign('media_mode', SK_Config::section('video')->media_mode);

$frontend->assign('swf_player_src', URL_FLASH_MEDIA_PLAYER . 'mediaplayer.swf');

$frontend->IncludeJsFile(URL_ADMIN_JS . 'profile.js');
$frontend->includeJsFile(URL_STATIC . 'swfobject.js');

$frontend->display('frame_profile_media.html');
?>
