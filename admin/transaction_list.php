<?php

$file_key = 'finance';
$active_tab = 'finance';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile.php' );
require_once( DIR_ADMIN_INC.'fnc.finance.php' );

//$membership = new AdminMembership();
$frontend = new AdminFrontend( );

require_once( 'inc.admin_menu.php' );

//Get and adapt input data
$search_type = @$_REQUEST['transaction_search_type'];
if ( isset( $_REQUEST['page'] ) )
	$page = $_REQUEST['page'];
else
	$page = 1;

if ( isset( $_REQUEST['on_page'] ) )
	$on_page = $_REQUEST['on_page'];
else
	$on_page = 20;
	
if( isset($_REQUEST['referrer_id']) )	
	$referrer_id = intval($_REQUEST['referrer_id']);	

$_constraint_arr['refunded'] = ( @$_REQUEST['refunded'] != 'no' || !$_REQUEST['refunded'] )? 'yes' : 'no';
$_constraint_arr['deleted'] = ( @$_REQUEST['deleted'] != 'no' || !$_REQUEST['deleted'] )? 'yes' : 'no';
$_constraint_arr['approval'] = ( @$_REQUEST['approval'] != 'no' || !$_REQUEST['approval'] )? 'yes' : 'no';

if(isset($referrer_id))
	switch ( $search_type )
	{
		case 'order':
			$trans = app_Referral::GetTransactionsByOrder( $_REQUEST['order'], $_REQUEST['payment_provider'], $referrer_id );
			break;
		case 'profile':		
			$trans = app_Referral::GetTransactionsByProfile( $_REQUEST['profile'], $page, $on_page, $_constraint_arr, $referrer_id );
			break;
		default:		
			$trans = app_Referral::GetTransactionsByDate( $_REQUEST['date_from'], $_REQUEST['date_to'], $page, $on_page, $_constraint_arr, $referrer_id);
			break;
	}
else 
	switch ( $search_type )
	{
		case 'order':
			$trans = GetTransactionsByOrder( $_REQUEST['order'], $_REQUEST['payment_provider'] );
			break;
		case 'profile':
			$trans = GetTransactionsByProfile( $_REQUEST['profile'], $page, $on_page, $_constraint_arr );
			break;
		default:
			$trans = GetTransactionsByDate( @$_REQUEST['date_from'], @$_REQUEST['date_to'], $page, $on_page, $_constraint_arr);
			break;
	}


$total_num = intval( $trans[4] );
$count = ceil( $total_num/$on_page );
for ( $i = 1; $i < $count+1; $i++ )
	$arr[] = $i;

$frontend->assign( 'trans', $trans[0] );	// array of the result transaction.
$frontend->assign( 'number', $total_num );	// total transaction's number of the result.
$frontend->assign( 'count', count( $arr ) );	// count of the pages.
$frontend->assign( 'arr', $arr );	// array of the pages.
$frontend->assign( 'on_page', $on_page ); // number of the result on a page.
$on_page_arr = array ( 20=>20, 50=>50 );
$frontend->assign( 'on_page_arr', $on_page_arr );	// for values select's fields.


// Get list of the payment providers, and pass to the template:
$payment_providers = GetPaymentProviders();
$frontend->assign( 'payment_providers', $payment_providers );	// list of the payment providers.
$frontend->assign( 'total', $trans[1] );	// total paid sum.
$frontend->assign( 'refunded', $trans[2] );	// refunded sum. 
$frontend->assign( 'fines', $trans[3] );	// fined sum.
$frontend->assign( 'include_arr', array( 
	'refunded'	=>	($_constraint_arr['refunded'] == 'yes')? 'yes' : 'no',
	'deleted'	=>	($_constraint_arr['deleted'] == 'yes')? 'yes' : 'no',
	'approval'	=>	($_constraint_arr['approval'] == 'yes')? 'yes' : 'no' 
	) );


$frontend->assign( 'page', $page );		// current page.
$frontend->assign( 'search_type', $search_type );	// search type(order, profile, date).

$frontend->assign( 'order', @$_REQUEST['order'] );	// value of the order textbox.
$frontend->assign( 'payment_provider', @$_REQUEST['payment_provider'] );	// selected payment provider.

$frontend->assign( 'profile', @$_REQUEST['profile'] );	// value of profile textbox.

$frontend->assign( 'date_from', @$_REQUEST['date_from'] );	// value of date from textbox.
if ( @$_REQUEST['date_to'] == '' )
	$date_to = date( 'Y-m-d' );
else
	$date_to = $_REQUEST['date_to'];
$frontend->assign( 'date_to', $date_to );	// value of date to textbox.

$new_obj = new adminProfile();
$frontend->register_function( 'profile_url', array( &$new_obj, 'frontendGetProfileURL') );

$_page['title'] = "Finance";

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'transaction.js' );

$template = 'transaction_list.html';

$frontend->display( $template );

?>