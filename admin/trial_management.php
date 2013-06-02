<?php

$file_key = 'profiles';
$active_tab = 'profiles';

require_once '../internals/Header.inc.php';

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

// Get and execute command.
if ( is_array( $_POST['claims'] ) )
{
	foreach ( $_POST['claims'] as $_claim )
	{
		$_claim_arr[] = explode( ',', $_claim );
	}

	if ( isset( $_POST['grant_claims'] ) )
	{
		if ( AdminMembership::GrantOrRefuseClaims( 'granted', $_claim_arr ) )
			$frontend->RegisterMessage( 'Claims have been satisfied' );
		else
			$frontend->RegisterMessage( 'Process failed', 'error' );
	}

	if ( isset( $_POST['refuse_claims'] ) )
	{
		if ( AdminMembership::GrantOrRefuseClaims( 'refused', $_claim_arr ) )
			$frontend->RegisterMessage( 'Claims refused' );
		else
			$frontend->RegisterMessage( 'Process failed', 'error' );
	}
	
	redirect( $_SERVER['REQUEST_URI'] );
}


// Get page navigation parameters
$sort_by = $_GET['sort_by'];

/* here we make check for the first click on the value
 * in case we change our incoming value we set order type to ASCENDING
 * in other case we check for additional clicks and change the image
 * $sort_type - ASCENDING or DESCENDING
*/
if ( !isset( $_SESSION['sort_by'] ) ) $_SESSION['sort_by'] = "profile_id";

if ( isset( $_GET['sort_type'] ) )
if ( $sort_by != $_SESSION['sort_by'] )
{
	$_SESSION['sort_type'] = 'DESC';
	$_SESSION['sort_image'] = '<img src="./img/arr_down.gif">';
}
else
{
	if ($_SESSION['sort_type'] == 'ASC')
	{
		$_SESSION['sort_type'] = 'DESC';
		$_SESSION['sort_image'] = '<img src="./img/arr_down.gif">';
	}
	else
	{
		$_SESSION['sort_type'] = 'ASC';
		$_SESSION['sort_image'] = '<img src="./img/arr_up.gif">';
	}
}

if ( !isset( $_SESSION['sort_type'] ) )	$_SESSION['sort_type'] = 'DESC';
if ( !isset( $_SESSION['sort_image'] ) )	$_SESSION['sort_image'] = 'ASC';

$frontend->assign( 'sort_image', $_SESSION['sort_image'] );

$num_on_page = $_GET['num_on_page'];
$page = $_GET['page'];

// Get profiles' claims
$claims = AdminMembership::GetClaimsTrialMembership( &$page, &$num_on_page, $sort_by, $_SESSION['sort_type'] );

$i = 0;
foreach ( $claims as $claim )
{
	$claims_n[$i] = $claim;
	$claims_n[$i]['m_name'] = SK_Language::section('membership.types')->text($claim['membership_type_id']);
	$i++;
}

// sort order by membership type's name if sort_by equals "m_name".
if ( $_GET['sort_by'] == 'm_name' )
{
	sort_order_mem_name();
}

// sort function, order by membership type name.
function sort_order_mem_name()
{
	global $claims_n;
	
	for ( $i = 0; $i < count( $claims_n )-1; $i++ )
	{
		for ( $j = 0; $j < count( $claims_n )-$i-1; $j++ )
		{
			if (( $claims_n[$j]['m_name'] > $claims_n[$j+1]['m_name'] && $_SESSION['sort_type'] == 'ASC') || ( $claims_n[$j]['m_name'] < $claims_n[$j+1]['m_name'] && $_SESSION['sort_type'] == 'DESC') )
			{
				$vr_claim = $claims_n[$j+1];
				$claims_n[$j+1] = $claims_n[$j];
				$claims_n[$j] = $vr_claim;
			}
		}
	}
}

$frontend->assign( 'claims', $claims_n );

// save in session
$_SESSION['sort_by'] = $sort_by;

// Get count of the claims
$total = AdminMembership::GetCountClaimsTrialMembership();
$frontend->assign( 'total', $total );

// Pass page navigation parameters
$frontend->assign( 'sort_by', $sort_by );
$frontend->assign( 'num_on_page', $num_on_page );
$frontend->assign( 'page', $page );
$j = ceil( $total/$num_on_page );
for ( $i=1; $i<=$j; $i++ )
	$pages[$i] = $i;
$frontend->assign( 'pages', $pages );

// Option tag's data.
$frontend->assign( 'options_data', array( 10=>10, 20=>20, 30=>30, 40=>40 ) );

$new_obj = new adminProfile();
$frontend->register_function( 'profile_url', array( &$new_obj, 'frontendGetProfileURL') );

$_page['title'] = "Trial Memberships";
$template = 'trial_management.html';
$frontend->display( $template );

