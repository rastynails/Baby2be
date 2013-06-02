<?php

$file_key = 'profile';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile_field.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile.php' );


$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC.'fnc.profile.php' );

$profile_id = intval( $_GET['profile_id'] );

if ( !is_numeric( $_GET['profile_id'] ) || !intval( $_GET['profile_id'] ) )
	throw new Exception('Undefined profile id');

if ( in_array( $_GET['status'], array( 'active', 'approval', 'suspended' ) ) )
	$media_status = $_GET['status'];

	
	
if ( $_POST['delete_music'] )
{
	if ( is_array( $_POST['music_file_arr'] ) )
	{
		foreach ( $_POST['music_file_arr'] as $_media_id ) {
			app_ProfileMusic::deleteMusic($_media_id);
		}
		$frontend->registerMessage( 'Files deleted' );
	}
	else {
		$frontend->registerMessage( 'Select file', 'notice' );
	}
}

if ( $_POST['set_status'] )
{
    
	if ( is_array( $_POST['music_file_arr'] ) )
	{
		foreach ( $_POST['music_file_arr'] as $_media_id )
		{
			adminProfile::setMusicStatus( $_media_id, $_POST['music_status'] );
			
				$list = app_UserActivities::getWhere(" `type` IN( 'music_upload', 'music_comment') and item={$_media_id} ");

				foreach( (is_array($list) ? $list : array()) as $action )
				{
					if( $_POST['music_status'] == 'active' )
						app_UserActivities::setStatus($action['skadate_user_activity_id'], 'active');
					else
						app_UserActivities::setStatus($action['skadate_user_activity_id'], 'approval');
				}

                if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
                {
                    $newsfeedDataParams = array(
                        'params' => array(
                            'feature' => FEATURE_MUSIC,
                            'entityType' => 'music_upload',
                            'entityId' => $_media_id,
                            'userId' => $profile_id,
                            'status' => ($_POST['music_status'] == 'active') ? 'active' : 'approval'
                        )
                    );
                    app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);
                }
		}
			
		$frontend->registerMessage( 'Selected files status changed' );
	}
	else 
	{
		$frontend->registerMessage( 'Select file', 'notice' );
	}
}

$music_arr = app_ProfileMusic::getProfileMusic($profile_id, $_GET['status'], true );


$musics = $music_arr['list'];

foreach ( $musics as $_key => $_music_file )
{
        $musics[$_key]['title'] = strip_tags($_music_file['title']);
        $musics[$_key]['description'] = strip_tags($_music_file['description']);
	$musics[$_key]['href'] = app_ProfileMusic::getMusicURL( $_music_file['hash'], $_music_file['extension'] );
}

$frontend->assign_by_ref( 'music_arr', $musics );

$frontend->assign( 'media_mode','flash_video' );

$frontend->assign( 'swf_player_src', URL_FLASH_MEDIA_PLAYER.'mediaplayer.swf' );

$frontend->IncludeJsFile( URL_ADMIN_JS.'profile.js' );
$frontend->includeJsFile( URL_STATIC.'swfobject.js' );

$frontend->display( 'frame_profile_music.html' );
?>
