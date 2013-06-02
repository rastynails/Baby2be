<?php

$file_key = 'blocked_ip';
$active_tab = 'list';

require_once( '../internals/Header.inc.php' );


// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );

require_once( DIR_ADMIN_INC.'fnc.blocked_ip.php' );
require_once( DIR_ADMIN_INC.'fnc.profile_list.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

$page = isset($_GET['_page']) ? intval( $_GET['_page'] ) : 1;
$limit = isset($_GET['_limit']) ? intval( $_GET['_limit'] ) : 10;

if ( isset($_GET['delete_ip']) )
{
	controlAdminGETActions();
	
	if ( deleteBlockedIp( $_GET['delete_ip'] ) )
		$frontend->registerMessage( 'IP was deleted' );
	else 
		$frontend->registerMessage( 'IP was not deleted', 'notice' );
		
	$_page = isset($_GET['last_ip']) ? $page - 1 : $page;
		
	redirect( $_SERVER['PHP_SELF'].'?limit='.$limit.'&_page='.$page );
}

$blocked_list_info = getBlockedIpList( $page, $limit );

$frontend->assign_by_ref( '_blocked_list_info', $blocked_list_info );
$frontend->assign_by_ref( 'current_page', $page );
$frontend->assign_by_ref( 'current_limit', $limit );
$frontend->assign( 'navigation_pages', navigationPages( ceil( $blocked_list_info['total']/$limit ) ) );

$_page['title'] = "Blocked IP List";

$frontend->display( 'blocked_ip_list.html' );

?>