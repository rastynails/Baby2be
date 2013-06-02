<?php

$file_key = 'affiliate';
$active_tab = 'affiliate';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

require_once( DIR_ADMIN_INC.'fnc.affiliate.php' );

$frontend = new AdminFrontend( );
require_once( 'inc.admin_menu.php' );


// get and adapt posted data
if ( isset($_GET['delete_affiliate']) )
{
	controlAdminGETActions();
	
	if ( deleteAffiliate( $_GET['delete_affiliate'] ) )
		$frontend->registerMessage( 'Affiliate has been deleted' );
	else
		$frontend->registerMessage( 'Affiliate not deleted', 'error' );
	
	redirect( $_SERVER['PHP_SELF'] );
}

// Get page navigation parameters
$sort_by = ( @$_GET['sort_by'] )? @$_GET['sort_by'] : 'full_name';
$page = ( @$_GET['page'] )? @$_GET['page'] : 1;
$num_on_page = ( @$_REQUEST['num_on_page'] )? @$_REQUEST['num_on_page'] : 10;
$sort_order = ( @$_GET['sort_order'] == 'DESC' )? 'DESC' : 'ASC';
$change_sort_order = ( $sort_order == 'DESC' )? 'ASC' : 'DESC';

// get affiliate list and their info
$affiliate_list = GetAffiliateListWithTheirInfo( $sort_by, $sort_order, $page, $num_on_page, $total );

$frontend->assign( 'affiliate_list', $affiliate_list );


$frontend->assign( 'sort_by', $sort_by );
$frontend->assign( 'sort_order', $change_sort_order );
// number on the page - select box values and labels
$frontend->assign( 'num_on_page', $num_on_page );
$num_on_page_arr = array ( 10=>10, 20=>20, 50=>50 );
$frontend->assign( 'num_on_page_arr', $num_on_page_arr );

// page navigation
$frontend->assign( 'total', $total );
$frontend->assign( 'page', $page );
$count = ceil( $total/$num_on_page );
for ( $i = 1; $i < $count+1; $i++ )
	$page_arr[] = $i;
$frontend->assign( 'page_arr', $page_arr );
$frontend->assign( 'count', $count );


// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

$_page['title'] = "Affiliates";
$template = 'affiliate_list.html';

$frontend->display( $template );



?>
