<?php
$file_key = 'membership_types_list';
$active_tab = 'service_list';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'fnc.finance.php' );

$membership = new AdminMembership();

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

// --- Get an adapt posted datas --- //
// Update services info
if ( isset( $_POST['s_promo'] ) )
{
	$res = $membership->UpdateMembershipServiceList( @$_POST['s_promo'], @$_POST['show'] );
	if ( $res )
		$frontend->RegisterMessage( 'Services settings updated' );
	else
		$frontend->RegisterMessage( 'Update error', 'notice' );
		
	redirect( $_SERVER['REQUEST_URI'] );
}

// change order membership type
if ( strlen( @$_GET['move_order'] ) && strlen( @$_GET['service_key'] ) )
{
	controlAdminGETActions();
	
	if ( $membership->MoveOrderMembershipService( @$_GET['service_key'], @$_GET['move_order'] ) )
		$frontend->RegisterMessage( 'Service order changed' );
	else
		$frontend->RegisterMessage( 'Service order change failed', 'notice' );
	
	redirect( $_SERVER['PHP_SELF'] );
}
//  -----  END  -----  //


//----- Generate Output ------ //
// get and pass service list:
$service_list = $membership->GetMembershipServiceList();

$frontend->assign( 'services', $service_list );

$_page['title'] = 'Membership Services';

$template = 'service_list.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'service_list.js' );

// display template
$frontend->display( $template );
?>
