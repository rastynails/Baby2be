<?php

$file_key = 'profiles';
$active_tab = 'statistic';


require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_profile.php' );


$frontend = new AdminFrontend( $language);

$_page['title'] = 'Photo Verification';

require_once( 'inc.admin_menu.php' );

$profileId = (int) $_GET['profile_id'];

if ( $_POST['send_request'] )
{
    app_PhotoAuthenticate::sendRequest($profileId);

    $frontend->registerMessage( 'Verification request was sent');
    SK_HttpRequest::redirect(sk_make_url());
}

if ( isset($_POST["auth"]) || isset($_POST["unauth"]) )
{
    $auth = isset($_POST["auth"]) ? 1 : 0;

    if ( !empty($_POST["photos"]) )
    {
        $photos = $_POST["photos"];
        foreach ($photos as $item)
        {
            app_PhotoAuthenticate::authenticatePhoto($item, $auth);
        }

        $frontend->registerMessage( 'Selected photos were ' . ($auth ? 'verified' : 'unverified') );
    }
    else
    {
        $frontend->registerMessage( 'Select photo', 'notice' );
    }

    SK_HttpRequest::redirect(sk_make_url());
}

if ( isset($_POST["delete_photo"]) )
{
    if ( !empty($_POST["photos"]) )
    {
        $photos = $_POST["photos"];
        foreach ($photos as $item)
        {
            app_ProfilePhoto::delete($item);
        }
        $frontend->registerMessage( 'Photo files deleted' );
    }
    else
    {
        $frontend->registerMessage( 'Select photo', 'notice' );
    }

    SK_HttpRequest::redirect(sk_make_url());
}

if ( $_POST['set_status'] )
{
    if ( !empty($_POST['photos']) )
    {
        foreach ( $_POST['photos'] as $item ) {
            if($_POST['photo_status'] == "active")
            {
                $list = app_UserActivities::getWhere("type = 'photo_upload' and item={$item}");

                $action = (isset($list[0]))?$list[0] : false;

                if( is_array($action) && $action['status'] == 'approval' )
                {
                    app_UserActivities::setStatus($action['skadate_user_activity_id'], 'active');
                }
            }
            adminProfile::setPhotoStatus( $item, $_POST['photo_status'] );
        }

        $frontend->registerMessage( 'Selected photo files status set to : <code>'.$_POST['photo_status'].'</code>' );
    }
    else
    {
        $frontend->registerMessage( 'Select photo', 'notice' );
    }

    SK_HttpRequest::redirect(sk_make_url());
}


$photos = adminProfile::getPhotos($profileId);

$request = app_PhotoAuthenticate::findRequest($profileId);
$responcePhotoUrl = false;
if ( $request && app_PhotoAuthenticate::isResponded($request) )
{
    $responcePhotoUrl = app_PhotoAuthenticate::getPhotoUrl($request->profileId, $request->code);
}

$frontend->assign('request', $request);
$frontend->assign('responcePhotoUrl', $responcePhotoUrl);

$frontend->assign('profile', array(
    'url' => URL_ADMIN . 'profile.php?profile_id=' . $profileId,
    'username' => app_Profile::username($profileId, true)
));

$frontend->assign('photo_auth_icon', app_PhotoAuthenticate::getIconUrl());

$frontend->assign('photos', $photos);
$frontend->display( 'photo_verification.html' );
