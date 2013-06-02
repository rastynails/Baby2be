<?php

$file_key = 'profile';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile.php' );


$frontend = new AdminFrontend();

//require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC.'fnc.profile.php' );

$profile_id = intval( $_GET['profile_id'] );

if ( !is_numeric( $_GET['profile_id'] ) || !intval( $_GET['profile_id'] ) )
	throw new Exception('Undefined profile id');

$arr = adminProfile::getMailboxConversations($profile_id, intval($_GET['page']));

$frontend->assign_by_ref('conversations', $arr['conversations']);
$frontend->assign('total', $arr['total']);

$frontend->assign('paging',array(
	'total'		=> $arr['total'],
	'on_page'	=> SK_Config::Section('site')->Section('additional')->Section('mailbox')->mails_per_page,
	'pages'		=> SK_Config::Section('site')->Section('additional')->Section('profile_list')->nav_per_page,
));

$frontend->IncludeJsFile( URL_ADMIN_JS.'profile.js' );

$frontend->display( 'frame_profile_mails.html' );
?>
