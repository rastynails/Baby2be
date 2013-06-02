<?php

$file_key = 'finance';
$active_tab = 'point_payments';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile.php' );
require_once( DIR_ADMIN_INC.'class.user_points.php');

$frontend = new AdminFrontend( );

require_once( 'inc.admin_menu.php' );

$search_type = @$_REQUEST['transaction_search_type'];
$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 1;

$on_page = isset( $_REQUEST['on_page'] ) ? $_REQUEST['on_page'] : 20;

switch ( $search_type )
{
	case 'order':
		$points_payments = UserPoints::GetTransactionsByOrder($_REQUEST['order']);
		break;
	case 'profile':
		$points_payments = UserPoints::GetTransactionsByProfile($_REQUEST['profile'], $page, $on_page);
		break;
	default:
		$points_payments = UserPoints::getTransactionsByDate(@$_REQUEST['date_from'], @$_REQUEST['date_to'], $page, $on_page);
		break;
}

$total_num = $points_payments['total_num'];
$count = ceil( $total_num/$on_page );
for ( $i = 1; $i < $count+1; $i++ )
	$arr[] = $i;

$frontend->assign('trans', $trans[0]);
$frontend->assign('number', $total_num);
$frontend->assign('count', count($arr));
$frontend->assign('arr', $arr);
$frontend->assign('on_page', $on_page);
$on_page_arr = array (20 => 20, 50 => 50);
$frontend->assign('on_page_arr', $on_page_arr);
$frontend->assign('page', $page);
$frontend->assign('search_type', $search_type);
$frontend->assign('order', @$_REQUEST['order']);
$frontend->assign('profile', @$_REQUEST['profile']);

$frontend->assign('payments', $points_payments['list']);
$frontend->assign('ord_num', $points_payments['total_num']);
$frontend->assign('total', $points_payments['total'] );

$frontend->assign( 'date_from', @$_REQUEST['date_from'] );
$date_to = (@$_REQUEST['date_to'] == '') ? date( 'Y-m-d' ) : $_REQUEST['date_to'];
$frontend->assign( 'date_to', $date_to );

$new_obj = new adminProfile();
$frontend->register_function('profile_url', array( &$new_obj, 'frontendGetProfileURL'));

$frontend->IncludeJsFile( URL_ADMIN_JS.'transaction.js' );

$_page['title'] = 'User Credits Payments';

$template = 'point_payments.html';
$frontend->display( $template );
