<?php

/**
 * The file registeres messages and displays result of the transaction. 
 * Deletes transaction record from `fin_sale_info` table, if the transaction status is not 'processing'.
 * NOTE: this script requires $__CUSTOM (string variable) - custom hash of the transaction.
 *  
 */

// include header
require_once( '../../internals/Header.inc.php' );

if ( isset( $__CUSTOM ) )
{
    $package_sale = app_UserPoints::getUserPointPackageSaleByHash($__CUSTOM);
    
    if ( $package_sale )
    {
        switch ( $package_sale['verified'] )
        {
            case 1:
                $msg['message'] = SK_Language::section('membership')->text('transaction_approval');
                $msg['type'] = 'message';
                break;
            
            case 0:
            default:
                $msg['message'] = SK_Language::section('membership')->text('transaction_processing');
                $msg['type'] = 'notice';
                break;
        }
        
        $redirect_url = SK_Navigation::href('points_purchase');
    }
    else 
    {
    	$result_info = app_Finance::GetSaleInfo( $__CUSTOM );
    	
    	if ( !$result_info )
    		exit("Error. Record about transaction not found");
    	
    	$msg = array();
    	
    	switch ( $result_info['status'] )
    	{
    		case 'error':
    			$msg['message'] = SK_Language::section('membership')->text('transaction_error');
    			$msg['type'] = 'error';
    			break;
    		case 'processing':
    			$msg['message'] = SK_Language::section('membership')->text('transaction_processing');
    			$msg['type'] = 'notice';
    			break;
    		case 'declined':
    			$msg['message'] = SK_Language::section('membership')->text('transaction_declined');
    			$msg['type'] = 'error';
    			break;
    		case 'approval':
    			$msg['message'] = SK_Language::section('membership')->text('transaction_approval');
    			$msg['type'] = 'message';
    			break;
    		case 'cancel':
    			$msg['message'] = SK_Language::section('membership')->text('transaction_cancel_recurring');
    			$msg['type'] = 'notice';
    			break;
    		case 'internal_error_trial_in_consideration':
    			$msg['message'] = SK_Language::section('membership')->text('transaction_internal_error_trial_in_consideration');
    			$msg['type'] = 'error';
    			break;
    		case 'internal_error_trial_used':
    			$msg['message'] = SK_Language::section('membership')->text('transaction_internal_error_trial_used');
    			$msg['type'] = 'error';
    			break;
    	}
    	
    	$redirect_url = SK_Navigation::href('payment_selection');
    }
}
else 
{
    if ( $_SESSION['order_item'] == 'points_package' )
    {
        $redirect_url = SK_Navigation::href('points_purchase');
    }
    else
    {
        $redirect_url = SK_Navigation::href('payment_selection'); 
    }
}

$_SESSION['messages'][] = array('message' => $msg['message'], 'type' => $msg['type']);

SK_HttpRequest::redirect($redirect_url);
