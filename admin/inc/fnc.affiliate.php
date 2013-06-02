<?php

/**
 * Returns affiliate list with their stat:
 *
 * @param string $sort_by
 * @param string $sort_order
 * @param integer $page
 * @param integer $num_on_page
 * @param integer $total
 * @return array
 */
function GetAffiliateListWithTheirInfo( $sort_by, $sort_order, $page, $num_on_page, &$total )
{
	switch ( $sort_by )
	{
		case 'traffic':
		case 'registration':
		case 'sale':
		case 'paid':
		case 'active':
		case 'balance':
			break;
		default:
			$sort_by = 'full_name';
			break;
	}

	$page = ( $page )? $page : 1;
	$num_on_page = ( $num_on_page )? $num_on_page : 10;
	if ( $sort_order != 'DESC' )
		$sort_order = 'ASC';
	
	$_query = "SELECT `a`.`affiliate_id`, `a`.`full_name`,`a`.`active`,
	 `traffic`,`traffic_amount`,`registration`,`registration_amount`,`sale`,`subscription_amount`,`paid`,
	 (IFNULL(`traffic_amount`, 0) + IFNULL(`registration_amount`, 0) + IFNULL(`subscription_amount`, 0) - IFNULL(`paid`,0)) as balance
	FROM
	(
	SELECT `a`.`affiliate_id`, `a`.`full_name`,`a`.`active`,
	( SELECT COUNT( `t`.`affiliate_id` ) FROM `".TBL_AFFILIATE_TRAFFIC."` AS `t` WHERE 
			`t`.`affiliate_id`=`a`.`affiliate_id` GROUP BY `t`.`affiliate_id`) AS `traffic`,
	( SELECT SUM( `t`.`amount` ) FROM `".TBL_AFFILIATE_TRAFFIC."` AS `t` WHERE 
			`t`.`affiliate_id`=`a`.`affiliate_id` GROUP BY `t`.`affiliate_id`) AS `traffic_amount`,
	( SELECT COUNT( `r`.`affiliate_id` ) FROM `".TBL_AFFILIATE_REGISTRATION."` AS `r` WHERE 
			`r`.`affiliate_id`=`a`.`affiliate_id` GROUP BY `r`.`affiliate_id`) AS `registration`,
	( SELECT SUM( `r`.`amount` ) FROM `".TBL_AFFILIATE_REGISTRATION."` AS `r` WHERE 
			`r`.`affiliate_id`=`a`.`affiliate_id` GROUP BY `r`.`affiliate_id`) AS `registration_amount`,
	( SELECT COUNT( `s`.`affiliate_id` ) FROM `".TBL_AFFILIATE_SUBSCRIPTION."` AS `s` WHERE 
			`s`.`affiliate_id`=`a`.`affiliate_id` GROUP BY `s`.`affiliate_id`) AS `sale`,
	( SELECT SUM( `s`.`amount` ) FROM `".TBL_AFFILIATE_SUBSCRIPTION."` AS `s` WHERE 
			`s`.`affiliate_id`=`a`.`affiliate_id` GROUP BY `s`.`affiliate_id`) AS `subscription_amount`,
	( SELECT SUM( `p`.`amount` ) FROM `".TBL_AFFILIATE_PAYMENT."` AS `p` WHERE 
			`p`.`affiliate_id`=`a`.`affiliate_id` GROUP BY `p`.`affiliate_id`) AS `paid`
	FROM `".TBL_AFFILIATE."` AS `a`	)
	AS `a` WHERE 1 ORDER BY $sort_by $sort_order LIMIT ".( ( $page-1 )*$num_on_page ).",$num_on_page";
	
	// get number of the affiliates
	$total = MySQL::fetchField( "SELECT COUNT( `affiliate_id` ) FROM `".TBL_AFFILIATE."`" );
	
	return MySQL::fetchArray( $_query );
}


/**
 * Returns affiliate info using email.
 *
 * @param string $affiliate_email
 * @return array
 */
function getAffiliateInfoUsingEmail( $affiliate_email )
{
	$affiliate_email = trim( $affiliate_email );
	if ( !$affiliate_email )
		return array();
	
	return MySQL::fetchRow( "SELECT * FROM `".TBL_AFFILIATE."` WHERE `email`='$affiliate_email'" );
}


/**
 * Updates affiliate info.
 *
 * @param integer $affiliate_id
 * @param array $info
 * @return boolean|integer
 */
function updateAffiliateInfo( $affiliate_id, $info )
{
	$affiliate_id = intval( $affiliate_id );
	if ( !$affiliate_id || !is_array( $info ) || !$info)
		return false;
	
	$_colums = array( 'full_name', 'password', 'email', 'phone', 'address', 'im', 'payment_details' );
	foreach ( $info	 as $_key=>$_value )
	{
		if ( !$_key )
			continue;
		
		if ( $_key == 'active' )
			$_query[$_key] = ( $_value == 'yes' )? 'yes' : 'no';
		
		if ( in_array( $_key, $_colums ) )
			$_query[$_key] = $_value;
	}
	
	if ( !$_query )
		return false;
	
	return MySQL::affectedRows( sql_placeholder( "UPDATE `?#TBL_AFFILIATE` SET ?% WHERE `affiliate_id`=?", $_query, $affiliate_id ) );
}


/**
 * Deletes affiliate.
 *
 * @param integer $affiliate_id
 * @return boolean
 */
function deleteAffiliate( $affiliate_id )
{
	$affiliate_id = intval( $affiliate_id );
	if ( !$affiliate_id )
		return false;

	// delete clicks:
	MySQL::fetchResource( "DELETE FROM `".TBL_AFFILIATE_TRAFFIC."` WHERE `affiliate_id`='$affiliate_id'" );
	
	// delete signups:
	MySQL::fetchResource( "DELETE FROM `".TBL_AFFILIATE_REGISTRATION."` WHERE `affiliate_id`='$affiliate_id'" );
	
	// delete subscriptions:
	MySQL::fetchResource( "DELETE FROM `".TBL_AFFILIATE_SUBSCRIPTION."` WHERE `affiliate_id`='$affiliate_id'" );
	
	//delete payments:
	MySQL::fetchResource( "DELETE FROM `".TBL_AFFILIATE_PAYMENT."` WHERE `affiliate_id`='$affiliate_id'" );
	
	// delete affiliate sites:
	MySQL::fetchResource( "DELETE FROM `".TBL_AFFILIATE_SITE."` WHERE `affiliate_id`='$affiliate_id'" );
	
	// update affiliate's profiles
	MySQL::fetchResource( "UPDATE `".TBL_PROFILE."` SET `affiliate_id`=0 WHERE `affiliate_id`=$affiliate_id" );
	
	// delete affiliate:
	return ( MySQL::affectedRows( "DELETE FROM `".TBL_AFFILIATE."` WHERE `affiliate_id`='$affiliate_id'" ) )? true : false;
}


/**
 * Returns affiliate total amount.
 *
 * @param integer $affiliate_id
 * @return decimal
 */
function getAffiliateTotalAmount( $affiliate_id )
{
	$affiliate_id = intval( $affiliate_id );
	if ( !$affiliate_id )
		return 0;

	$_affiliate_info = MySQL::fetchRow( "SELECT (SELECT SUM( `amount` ) FROM `".TBL_AFFILIATE_TRAFFIC."` WHERE 
			`affiliate_id`='$affiliate_id' GROUP BY `affiliate_id`) AS `traffic_amount`, 
		( SELECT SUM( `amount` ) FROM `".TBL_AFFILIATE_REGISTRATION."` WHERE 
			`affiliate_id`='$affiliate_id' GROUP BY `affiliate_id`) AS `registration_amount`,	
		( SELECT SUM( `amount` ) FROM `".TBL_AFFILIATE_SUBSCRIPTION."` WHERE 
			`affiliate_id`='$affiliate_id' GROUP BY `affiliate_id`) AS `subscription_amount`
		FROM `".TBL_AFFILIATE."` WHERE `affiliate_id`='$affiliate_id'" );
	
	return $_affiliate_info['traffic_amount'] + $_affiliate_info['registration_amount'] + $_affiliate_info['subscription_amount'];
}


/**
 * Returns affiliate payment list.
 *
 * @param integer $affiliate_id
 * @return array
 */
function getAffiliatePaymentList( $affiliate_id )
{
	$affiliate_id = intval( $affiliate_id );
	if ( !$affiliate_id )
		return array();

	return MySQL::fetchArray( "SELECT `payment_id`,`time_stamp`,`amount`,`edited_time_stamp` FROM `".TBL_AFFILIATE_PAYMENT."` WHERE `affiliate_id`='$affiliate_id'" );
}


/**
 * Returns affiliate payment info:
 *
 * @param integer $affiliate_id
 * @param integer $payment_id
 * @return array
 */
function getAffiliatePaymentInfo( $affiliate_id, $payment_id )
{
	$affiliate_id = intval( $affiliate_id );
	$payment_id = intval( $payment_id );
	if ( !$affiliate_id || !$payment_id )
		return array();
	
	return MySQL::fetchRow( "SELECT * FROM `".TBL_AFFILIATE_PAYMENT."` WHERE `affiliate_id`='$affiliate_id' AND `payment_id`='$payment_id'" );
}


/**
 * Updates affiliate payment amount.
 *
 * @param integer $affiliate_id
 * @param integer $payment_id
 * @param decimal $amount
 * @return integer
 */
function updateAffiliatePaymentInfo( $affiliate_id, $payment_id, $amount )
{
	$affiliate_id = intval( $affiliate_id );
	$payment_id = intval( $payment_id );

	if ( !$affiliate_id || !$payment_id || !is_numeric( $amount ) )
		return -1;
	
	// check if it is last payment
	$_last_payment_id = MySQL::fetchField( "SELECT `payment_id` FROM `".TBL_AFFILIATE_PAYMENT."` WHERE `affiliate_id`='$affiliate_id' ORDER BY `time_stamp` DESC LIMIT 1" );

	if ( $_last_payment_id != $payment_id )
		return -2;
	
	return MySQL::affectedRows( "UPDATE `".TBL_AFFILIATE_PAYMENT."` SET `amount`='$amount',`edited_time_stamp`='".time()."' WHERE `affiliate_id`='$affiliate_id' AND `payment_id`='$payment_id'" );
}


/**
 * Adds a new affiliate payment amount.
 *
 * @param integer $affiliate_id
 * @param decimal $amount
 * @return array
 */
function addAffiliatePayment( $affiliate_id, $amount )
{
	$affiliate_id = intval( $affiliate_id );
	if ( !$affiliate_id || !is_numeric( $amount ) )
		return -1;
	
	return MySQL::fetchResource( "INSERT INTO `".TBL_AFFILIATE_PAYMENT."` (`affiliate_id`,`amount`,`time_stamp`,`edited_time_stamp`)
		VALUES( '$affiliate_id','$amount','".time()."',0 )" );
}


/**
 * Updates affiliate configs.
 *
 * @param string $name
 * @param string $value
 * @return boolean|integer
 */
function updateAffiliateConfig( $name, $value )
{
	if ( !$name || !strlen( $value ) )
		return false;
	
	switch ( $name )
	{
		case 'subscription_type_value':
			if ( $value != 'subscription_amount' && $value != 'subscription_percent' )
				return false;
			break;
		case 'subscription_amount':
		case 'subscription_percent':
		case 'period':
		case 'registration_amount':
		case 'traffic_amount':
			if ( !is_numeric( $value ) || $value < 0 )
				return false;
			break;
	}
	return ( app_Affiliate::getAffiliateConfig( $name ) == $value )? 
		true :  MySQL::affectedRows( "UPDATE `".TBL_AFFILIATE_PLAN."` SET `value`='$value' WHERE `name`='$name'" );
}
?>