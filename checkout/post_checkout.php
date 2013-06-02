<?php

/** 
 * This script registers payments and updates membership types  
 * NOTE: this script requires $__CUSTOM (string variable) - custom hash of the transaction.
 */

if ( !isset($__CUSTOM) )
	return;

$sale_info_arr = app_Finance::GetSaleInfo( $__CUSTOM );
if ( !$sale_info_arr )
	return;

if ( $sale_info_arr['status'] != 'approval' )
	return;

// Get membership type plan's info: period, units, membership_type_id.
$membership_type_plan_info = app_Membership::GetMembershipTypePlanInfo( $sale_info_arr['membership_type_plan_id'] );

// get membership type info:
if ( $membership_type_plan_info )
	$membership_type_info = app_Membership::GetMembershipTypeInfo( $membership_type_plan_info['membership_type_id'] );

// get profile info
$profile_info = app_Profile::getFieldValues( $sale_info_arr['profile_id'], array( 'profile_id', 'language_id', 'email' ) );

// if membership plan or membership type not found:
if ( !$membership_type_info || !$membership_type_plan_info || !$profile_info )
{
	/*app_Finance::FillSale( 
		$sale_info_arr['profile_id'], 
		$sale_info_arr['fin_payment_provider_id'],
		$sale_info_arr['payment_provider_order_number'], 
		$membership_type_plan_info['units'], 
		$membership_type_plan_info['period'], 
		$sale_info_arr['amount'], 
		$membership_type_plan_info['membership_type_id'], 
		false, 'deleted' 
	);*/
	return;
}

// --- CREDITS --- //
if ( $membership_type_info['type'] == 'credits' )
{
	// Register transaction, get sale id.
	$sale_id = app_Finance::FillSale( 
		$sale_info_arr['profile_id'], 
		$sale_info_arr['fin_payment_provider_id'], 
		$sale_info_arr['payment_provider_order_number'],
		$membership_type_plan_info['units'], 
		$membership_type_plan_info['period'], 
		$sale_info_arr['amount'], 
		$membership_type_plan_info['membership_type_id'] 
	);
	
	// Add record to membership table
	app_Membership::BuyCreditMembership( $sale_id, $sale_info_arr['profile_id'], $sale_info_arr['amount'], $membership_type_info['membership_type_id'] );
	
	// Send mail notification
	app_Membership::sendNotifyPurchaseMembershipMail( 'credits', $profile_info, $membership_type_plan_info['membership_type_id'], 0, $sale_info_arr['amount'] );
	
	// affiliate program:
	app_Affiliate::catchForSale( $sale_info_arr['profile_id'], $sale_id, $sale_info_arr['amount'] );
	
	app_Referral::trackReferralsPurchase($sale_info_arr['profile_id'], $sale_id);
}
// --- SUBSCRIPTION --- //
elseif ( $membership_type_info['type'] == 'subscription' )	
{
	// handle ccbill recurring payments
	if ( ($sale_info_arr['fin_payment_provider_id'] == 4 
			|| $sale_info_arr['fin_payment_provider_id'] == 5) 
			&& $sale_info_arr['is_recurring'] == 'y' )
	{
		$query = SK_MySQL::placeholder("SELECT `merchant_field` FROM `".TBL_FIN_SALE_INFO."` 
			WHERE `payment_provider_order_number`='?' AND `fin_payment_provider_id`=?", 
			$sale_info_arr['payment_provider_order_number'], $sale_info_arr['fin_payment_provider_id']);
		
		$new_trid = SK_MySQL::query($query)->fetch_cell();
	}
	 
	$trans_id = strlen($new_trid) ? $new_trid : $sale_info_arr['payment_provider_order_number'];  
		
	// Register transaction, get sale id.
	$sale_id = app_Finance::FillSale( 
		$sale_info_arr['profile_id'], 
		$sale_info_arr['fin_payment_provider_id'], 
		$trans_id,
		$membership_type_plan_info['units'], 
		$membership_type_plan_info['period'], 
		$sale_info_arr['amount'], 
		$membership_type_plan_info['membership_type_id'], 
		( $sale_info_arr['is_recurring'] == 'y' ), 
		'approval', $sale_info_arr['coupon']
	);

	// handle PayPal subscription period
	if ( $sale_info_arr['fin_payment_provider_id'] == 3 && $membership_type_plan_info['units'] == 'months' )
	{
		$membership_type_plan_info['period'] = app_Membership::getDaysCountFromMonths( $membership_type_plan_info['period'] );
		$membership_type_plan_info['units'] = 'days';
	}
	
	// Add record to membership table
	$membership_id = app_Membership::BuySubscriptionTrialMembership( $sale_info_arr['profile_id'], $membership_type_plan_info['membership_type_id'], $membership_type_plan_info['period'], $membership_type_plan_info['units'], $sale_id );

	// affiliate program:
	app_Affiliate::catchForSale( $sale_info_arr['profile_id'], $sale_id, $sale_info_arr['amount'] );
	
	app_Referral::trackReferralsPurchase($sale_info_arr['profile_id'], $sale_id);
	
	// Send mail notification
	$type = ( $sale_info_arr['is_recurring'] == 'y' )? 'recurring_subscription' : 'subscription';
	app_Membership::sendNotifyPurchaseMembershipMail( $type, $profile_info, $membership_type_plan_info['membership_type_id'], $membership_id );
}
// --- TRIAL --- //
else 	
{
	// check if profile claimed before and if true then result of claim:
	$result = app_Membership::CheckIfProfileClaimedTheMembership( $sale_info_arr['profile_id'], $membership_type_info['membership_type_id'], 'type' );
	if ( $result == 1 ) //profile can get this membership.
	{
		// Register transaction, get sale id.
		$sale_id = app_Finance::FillSale( 
			$sale_info_arr['profile_id'], 
			$sale_info_arr['fin_payment_provider_id'], 
			$sale_info_arr['payment_provider_order_number'],
			$membership_type_plan_info['units'], 
			$membership_type_plan_info['period'], 
			$sale_info_arr['amount'], 
			$membership_type_plan_info['membership_type_id'],
			false, 'approval', $sale_info_arr['coupon']
		);

		// put info into membership table
		$membership_id = app_Membership::BuySubscriptionTrialMembership( $sale_info_arr['profile_id'], $membership_type_info['membership_type_id'], $membership_type_plan_info['period'], $membership_type_plan_info['units'], $sale_id );
		
		// Send mail notification
		app_Membership::sendNotifyPurchaseMembershipMail( 'trial', $profile_info, $membership_type_plan_info['membership_type_id'], $membership_id );
		// affiliate program:
		app_Affiliate::catchForSale( $sale_info_arr['profile_id'], $sale_id, $sale_info_arr['amount'] );
		
		app_Referral::trackReferralsPurchase($sale_info_arr['profile_id'], $sale_id);
	}
	elseif ( $result == -2 )
		app_Finance::SetTransactionResult( $__CUSTOM, 'internal_error_trial_in_consideration' );
	elseif ( $result == -3 )
		app_Finance::SetTransactionResult( $__CUSTOM, 'internal_error_trial_used' );
	
}

?>