<?php
$file_key = 'user_points';
$active_tab = 'spend_credits';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'class.user_points.php' );

$membership = new AdminMembership();

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( isset( $_POST['use_credits'] ) )
{
	$res = UserPoints::updateServices($_POST['use_credits'], $_POST['cost'], $_POST['show']);
	if ( $res )
		$frontend->RegisterMessage( 'Services settings updated' );
	else
		$frontend->RegisterMessage( 'Update error', 'notice' );
		
	redirect( $_SERVER['REQUEST_URI'] );
}

$service_list = $membership->GetMembershipServiceList();

$frontend->assign( 'services', $service_list );

$_page['title'] = 'Spending Credits';

$template = 'spend_credits.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'service_list.js' );

// display template
$frontend->display( $template );
