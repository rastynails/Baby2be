<?php

$file_key = '';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
//require_once( 'inc/class.admin_membership.php' );

require_once( DIR_ADMIN_INC.'fnc.report.php' );

$frontend = new AdminFrontend();

$profile_id = intval( $_GET['profile_id'] );

if ( !isset( $_GET['profile_id'] ) )
	fail_error( 'Undefined profile id' );
	
if ( !isset( $_GET['media_id'] ) )
	fail_error( 'Undefined media id' );

$video = getReportedVideo( $profile_id, $_GET['media_id'] );

$video['href'] = getMediaFileURL( $video['hash'], $video['extension'] );

$frontend->assign_by_ref( 'video', $video );

$frontend->assign( 'media_mode', SK_Config::section('video')->get('media_mode') );

$frontend->assign( 'swf_player_src', URL_FLASH_MEDIA_PLAYER.'mediaplayer.swf' );

$frontend->IncludeJsFile( URL_ADMIN_JS.'reports.js' );
$frontend->includeJsFile( URL_FLASH_MEDIA_PLAYER.'swfobject.js' );

$frontend->display( 'frame_reported_video.html' );
