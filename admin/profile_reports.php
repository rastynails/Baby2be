<?php

$file_key = 'reports';

$active_tab = 'reports_list';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'fnc.report.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_configs.php' );

$frontend = new AdminFrontend();

if ( !isset($_GET['profile_id']) || !intval($_GET['profile_id']) )
	redirect( "reports.php" );
	
switch ($_POST['content'])
{
	case 'photo':
		if ( isset( $_POST['reports'] ) )
		{
			switch ( $_POST['command'] )
			{
				case 'suspend':
					foreach ( $_POST['reports'] as $entity_id ) {
						adminProfile::setPhotoStatus($entity_id, 'suspended');
						$profile_id = app_ProfilePhoto::photoOwnerId($entity_id);
						app_ProfilePhoto::updateHasPhotoStatus($profile_id);				
					}
					deleteReports( $_POST['reports'] );
					$frontend->registerMessage( 'Photos suspended' );
					break;
				
				case 'delete':
					foreach ( $_POST['reports'] as $entity_id ) {
						app_ProfilePhoto::delete($entity_id);
						$profile_id = app_ProfilePhoto::photoOwnerId($entity_id);
						app_ProfilePhoto::updateHasPhotoStatus($profile_id);			
					}
					deleteReports( $_POST['reports'] );
					$frontend->registerMessage( 'Photos deleted' );
					break;
				
				case 'dismiss':
					$dismissed = dismissReports( $_POST['reports'], 'photo' );
					$frontend->registerMessage( $dismissed. ' reports dismissed' );
					break;
			}
			redirect( URL_ADMIN.'profile_reports.php?profile_id='.$_GET['profile_id'] );
		}
		else {
			$frontend->registerMessage( 'Select report', 'notice' );
		}	
		break;
		
	case 'profile':
		if ( isset( $_POST['reports'] ) )
		{
			switch ( $_POST['command'] )
			{		
				case 'dismiss':
					$dismissed = dismissReports( $_POST['reports'], 'profile' );
					$frontend->registerMessage( $dismissed. ' reports dismissed' );
					break;
			}
			redirect( URL_ADMIN.'profile_reports.php?profile_id='.$_GET['profile_id'] );
		}
		else {
			$frontend->registerMessage( 'Select report', 'notice' );
		}
		break;

	case 'blog':
		if ( isset( $_POST['reports'] ) )
		{
			switch ( $_POST['command'] )
			{		
				case 'dismiss':
					$dismissed = dismissReports( $_POST['reports'], 'blog' );
					$frontend->registerMessage( $dismissed. ' reports dismissed' );
					break;
			}
			redirect( URL_ADMIN.'profile_reports.php?profile_id='.$_GET['profile_id'] );
		}
		else {
			$frontend->registerMessage( 'Select report', 'notice' );
		}
		break;
		
	case 'forum':
		if ( isset( $_POST['reports'] ) )
		{
			switch ( $_POST['command'] )
			{		
				case 'dismiss':
					$dismissed = dismissReports( $_POST['reports'], 'forum' );
					$frontend->registerMessage( $dismissed. ' reports dismissed' );
					break;
			}
			redirect( URL_ADMIN.'profile_reports.php?profile_id='.$_GET['profile_id'] );
		}
		else {
			$frontend->registerMessage( 'Select report', 'notice' );
		}
		break;
		
	case 'video':
		if ( isset( $_POST['reports'] ) )
		{
			switch ( $_POST['command'] )
			{
				case 'suspend':
					foreach ( $_POST['reports'] as $entity_id ) {
						adminProfile::setMediaStatus($entity_id, 'suspended');
						$profile_id = app_ProfileVideo::getVideoOwnerById($entity_id);
						adminProfile::updateProfileHasVideoStatus($profile_id);						
					}
					deleteReports( $_POST['reports'] );
					$frontend->registerMessage( 'Video suspended' );
					break;
				
				case 'delete':
					foreach ( $_POST['reports'] as $entity_id ) {
						app_ProfileVideo::deleteVideo($entity_id);
						$profile_id = app_ProfileVideo::getVideoOwnerById($entity_id);
						adminProfile::updateProfileHasVideoStatus($profile_id);
					}
					deleteReports( $_POST['reports'] );
					$frontend->registerMessage( 'Video deleted' );
					break;
				
				case 'dismiss':
					$dismissed = dismissReports( $_POST['reports'], 'video' );
					$frontend->registerMessage( $dismissed. ' reports dismissed' );
					break;
			}
			redirect( URL_ADMIN.'profile_reports.php?profile_id='.$_GET['profile_id'] );
		}
		else {
			$frontend->registerMessage( 'Select report', 'notice' );
		}		

	case 'classifieds':
		if ( isset( $_POST['reports'] ) )
		{
			switch ( $_POST['command'] )
			{		
				case 'dismiss':
					$dismissed = dismissReports( $_POST['reports'], 'classifieds' );
					$frontend->registerMessage( $dismissed. ' reports dismissed' );
					break;
			}
			redirect( URL_ADMIN.'profile_reports.php?profile_id='.$_GET['profile_id'] );
		}
		else {
			$frontend->registerMessage( 'Select report', 'notice' );
		}
		break;

	case 'message':
		if ( isset( $_POST['reports'] ) )
		{
			switch ( $_POST['command'] )
			{				
				case 'dismiss':
					$dismissed = dismissReports( $_POST['reports'], 'message' );
					$frontend->registerMessage( $dismissed. ' reports dismissed' );
					break;
			}
			redirect( URL_ADMIN.'profile_reports.php?profile_id='.$_GET['profile_id'] );
		}
		else {
			$frontend->registerMessage( 'Select report', 'notice' );
		}		
}
	
$user_reports = getProfileAllReports( $_GET['profile_id'] );

if (!is_array($user_reports))
	redirect( "reports.php" );
	
$full_profile_info = app_Profile::getFieldValues( $_GET['profile_id'] );
$full_profile_info['thumb_href'] = app_ProfilePhoto::getThumbURL( $_GET['profile_id'] );
$full_profile_info['activity_info'] = app_Profile::ActivityInfo( $full_profile_info['activity_stamp'], app_Profile::isOnline($_GET['profile_id']), true );

$full_profile_info['age'] = app_Profile::getAge( $full_profile_info['birthdate'] );

$frontend->assign_by_ref( 'reports', $user_reports );
$frontend->assign_by_ref( 'full_profile_info', $full_profile_info );
$frontend->IncludeJsFile( URL_ADMIN_JS.'reports.js' );
require_once( 'inc.admin_menu.php' );
 
$_page['title'] = 'Reports Administration';

$frontend->display( 'profile_reports.html' );
