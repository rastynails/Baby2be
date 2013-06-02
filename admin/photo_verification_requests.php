<?php

$file_key = 'profiles';
$active_tab = 'statistic';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

require_once( DIR_ADMIN_INC.'class.admin_profile.php' );

$frontend = new AdminFrontend( );

$_page['title'] = 'Photo Verification';

require_once( 'inc.admin_menu.php' );

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1; 
$per_page = 30;


$limit = array($per_page *( $page-1 ), $per_page);

$requestList = array();
$requestDtoList = app_PhotoAuthenticate::findPendingResponceList($limit);
foreach ( $requestDtoList as $item )
{
    /* @var $item dto_PhotoAuthenticate */
    
    $responce = & $requestList[];
    $responce['username'] = app_Profile::username($item->profileId, true);
    $responce['userlink'] = URL_ADMIN . 'profile.php?profile_id=' . $item->profileId;
    $responce['date'] = SK_I18n::getSpecFormattedDate($item->timeStamp, false, true);
    $responce['url'] = URL_ADMIN . 'photo_verification.php?profile_id=' . $item->profileId;    
}

$frontend->assign('requestList', $requestList);

$paging["total"] = app_PhotoAuthenticate::findPendingResponceCount();
$paging["on_page"] = $per_page;
$paging["pages"] = 10;
$frontend->assign('paging', $paging);


$frontend->display( 'photo_verification_requests.html' );