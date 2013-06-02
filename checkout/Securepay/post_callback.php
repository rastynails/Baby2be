<?php

require_once( '../../internals/Header.inc.php' );

$_ARRAY = ConvertArrayKeysToLower( $_REQUEST );
$custom = ( strlen(trim( $_ARRAY['custom'])) )? trim( $_ARRAY['custom']) : trim( $_ARRAY['unique_id']);
$amount = $_ARRAY['amount'];
$trans_order = trim( $_ARRAY['approv_num']);
$status = trim($_ARRAY['return_code']);

// check sale info id:
if ( !strlen( $custom ) )
	exit;


// TRACK USER POINTS PACKAGE SALE
$points_sale = app_UserPoints::getUserPointPackageSaleByHash($custom);

if ( $points_sale )
{
    if ( $points_sale['pp_id'] != 6 )
        exit();
    
    app_UserPoints::logSale($custom, print_r($_REQUEST, true), __FILE__, __LINE__);
    require_once 'uppc.php';
    exit();
}
// END of TRACK USER POINTS PACKAGE SALE
	
	
/**
 * Here are possible values of Return_Code:
 * N - declined
 * Y - approved
 */
switch ( strtoupper( $status ) )
{
	case 'Y':
		if ( !is_numeric( $amount ) || !strlen( $trans_order ) )
		{
			app_Finance::SetTransactionResult( $custom, 'error' );
		}
		elseif ( app_Finance::SetTransactionResult( $custom, 'approval', $amount, $trans_order ) )
		{
			$__CUSTOM = $custom;
			// processing come data by Membership System:
			require_once( '../post_checkout.php' );
		}
		break;
		
	case 'N':
		app_Finance::SetTransactionResult( $custom, 'declined', $amount, $trans_order );
		break;
	default:		// error
		app_Finance::SetTransactionResult( $custom, 'error' );
}


function ConvertArrayKeysToLower( $array )
{
	if ( !is_array( $array ) )
		return array();
	
	foreach ( $array as $_key=>$_value )
	{
		if( is_array( $_value ) )
			$_value = ConvertArrayKeysToLower( $_value );
		
		$_return_arr[strtolower($_key)] = $_value;
	}
	
	return $_return_arr;
}

