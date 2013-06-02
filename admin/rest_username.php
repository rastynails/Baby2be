<?php

$file_key = 'rest_username';
$active_tab = 'rest_username';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_APPS.'Username.app.php');

$frontend = new AdminFrontend( );

$frontend->includeJsFile( URL_ADMIN_JS . 'rest_username.js' );

if ( @$_POST['command'] == 'add_username' )
{
	switch ( app_Username::addUsename( $_POST['username'] ) )
	{
		case -1:
			$frontend->registerMessage( 'Username missing', 'error' );
			break;
			
		case -2:
			$frontend->registerMessage( 'This username was already added', 'error' );
			break;
			
		default:
			$frontend->registerMessage( 'Your username was successfully added' );
	}
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_POST['command'] == 'del_username' )
{
	if ( ( $del_num = app_Username::deleteUsernames($_POST['usernames_arr'] ) ) )
		$frontend->registerMessage( $del_num.' username(s) was(were) deleted' );
	else 
		$frontend->registerMessage( 'Please check username(s) to delete', 'error' );
	redirect( $_SERVER['REQUEST_URI'] );
}
$tempVar = app_Username::getRestrictedList( ( isset( $_GET['page'] ) ? (int)$_GET['page'] : 1 ) );
$frontend->assign_by_ref( 'usernames',  $tempVar);

require_once( 'inc.admin_menu.php' );

$_page['title'] = 'Restricted Usernames';

$frontend->display('rest_username.html' );


?>
