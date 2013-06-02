<?php

$file_key = 'blocked_ip';
$active_tab = 'main';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );

require_once( DIR_ADMIN_INC.'fnc.blocked_ip.php' );

$frontend = new AdminFrontend();


require_once( 'inc.admin_menu.php' );


if ( isset($_GET['search_ip']) )
{
	$find_ip = searchBlockedIp( @$_GET['search_pattern'] );

	if ( $find_ip )
	{
		$frontend->registerMessage( 'IP was found' );
		$frontend->assign( 'find_ip', $find_ip );
	}
	else
	{
		$frontend->registerMessage( 'Specified IP not found', 'notice' );
		redirect( $_SERVER['PHP_SELF'] );
	}
}

if ( isset($_GET['delete_ip']) )
{
	controlAdminGETActions();

	if ( deleteBlockedIp( $_GET['delete_ip'] ) )
		$frontend->registerMessage( 'IP was deleted' );
	else
		$frontend->registerMessage( 'IP was not deleted', 'notice' );

	redirect( $_SERVER['PHP_SELF'] );
}

if ( isset($_POST['add_ip']) )
{
	switch ( @addBlockedIp( $_POST['add_pattern'] ) )
	{
		case -1:
			$frontend->registerMessage( 'Please, specify IP', 'error' );
			break;
		case -2:
			$frontend->registerMessage( 'Specified IP already exists', 'notice' );
			break;
		case 1:
			$frontend->registerMessage( 'IP was added' );
			break;
	}

	redirect( $_SERVER['PHP_SELF'] );
}

$_page['title'] = "Blocked IP";

$frontend->display( 'blocked_ip.html' );

?>
