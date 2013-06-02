<?php
/**
 * These functions are intended for working with finance - view, refund transactions.
 */

/**
 * Returns array with the transaction's information from fin_sale table using transaction's order and payment provider id.
 *
 * @param string $order transaction order number on the payment provider side.
 * @param integer $payment_provider_id payment provider id.
 *
 * @return array
 */
function GetTransactionsByOrder( $order, $payment_provider_id )
{
	$payment_provider_id = intval( $payment_provider_id );
	if ( !$payment_provider_id )
		return array();
	
	$_query = "SELECT `s`.`fin_sale_id`,`s`.`payment_provider_order_number`,`s`.`order_stamp`,`s`.`amount`,`s`.`status`,
		`s`.`fin_payment_provider_id`,`p`.`username`,`p`.`profile_id`,`mt`.`membership_type_id`,`r`.`amount_fine` 
		FROM `".TBL_FIN_SALE."` AS `s` 
		LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`s`.`profile_id`=`p`.`profile_id`) 
		LEFT JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` ON( `mt`.`membership_type_id`=`s`.`membership_type_id` ) 
		LEFT JOIN `".TBL_FIN_REFUND."` AS `r` ON( `s`.`fin_sale_id`=`r`.`fin_sale_id` ) 
		WHERE `s`.`payment_provider_order_number`='$order' AND `s`.`fin_payment_provider_id`='$payment_provider_id' LIMIT 1";
	
	$_fin_info[0] = MySQL::fetchRow( $_query );

	if ( $_fin_info[0] )
	{
		if ( $_fin_info[0]['amount_fine'] != '' )
		{
			$_refunded = $_fin_info[0]['amount'];
			$_fines = $_fin_info[0]['amount_fine'];
		}
		else
		{
			$_refunded = 0;
			$_fines = 0;
		}
		return array( $_fin_info, $_fin_info[0]['amount'], $_refunded, $_fines );
	}
	else
		return array( array(), 0, 0, 0);
}


/**
 * Returns array with the transactions' information from fin_sale table using profile's nickname or email.
 *
 * @param string $profile profile's email or nickname;
 * @param integer $page
 * @param integer $on_page
 *
 * @return array
 */
function GetTransactionsByProfile( $profile, $page, $on_page, $include_arr )
{
	// check if argument passed or not:
	if ( !strlen( $profile ) || !is_array( $include_arr ) )
		return array();
	
	// check the argument is a email or username, then generate part of query:
	if ( preg_match( '/@/', $profile ) )
		$_profile_q = 'email';
	else
		$_profile_q = 'username';
	
	// generate constrait query string
	if ( $include_arr['refunded'] == 'yes' )
		$_refunded_q_add = " OR `r`.`fin_sale_id`>0";
	
	if ( $include_arr['deleted'] != 'yes' )
		$_deleted_q.= " AND (`s`.`status`!='deleted'$_refunded_q_add)";
	
	if ( $include_arr['approval'] != 'yes' )
		$_approval_q.= " AND (`s`.`status`!='approval'$_refunded_q_add)";
	
	$_query = "SELECT `s`.`fin_sale_id`,`s`.`payment_provider_order_number`,`s`.`order_stamp`,`s`.`amount`,`s`.`status`,
		`s`.`fin_payment_provider_id`,`p`.`username`,`p`.`profile_id`,`mt`.`membership_type_id`,`r`.`amount_fine` 
		FROM `".TBL_FIN_SALE."` AS `s` 
		LEFT JOIN `".TBL_PROFILE."` AS `p` ON( `s`.`profile_id`=`p`.`profile_id` ) 
		LEFT JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` ON( `mt`.`membership_type_id`=`s`.`membership_type_id` ) 
		LEFT JOIN `".TBL_FIN_REFUND."` AS `r` ON( `s`.`fin_sale_id`=`r`.`fin_sale_id`)
		WHERE `p`.`$_profile_q`='$profile'$_deleted_q $_approval_q AND `s`.`fin_payment_provider_id`!=0 ORDER BY `s`.`order_stamp` DESC";
	
	$_fin_info = MySQL::fetchArray( $_query );
	if ( count( $_fin_info ) > 0 )
	{
		$_i = 1;
		$_j = 0;
		$_total = 0;
		$_refunded = 0;
		$_fines = 0;
		foreach ( $_fin_info as $_value )
		{
			if ( $include_arr['refunded'] != 'yes' && $_value['amount_fine'] != '' )
				continue;
			
			if ( $_i > ( $page - 1 )*$on_page && $_i <= $page*$on_page )
			{
				$_transactions[$_j] = $_value;
				$_j++;
			}
			$_total += $_value['amount'];
			if ( $_value['amount_fine'] != '' )
			{
				$_refunded += $_value['amount'];
				$_fines += $_value['amount_fine'];
			}
			
			$_i++;
		}
		return array( $_transactions, $_total, $_refunded, $_fines, $_i-1 );
	}
	else
		return array( array(), 0, 0, 0);
}


/**
 * Returns array with the transactions' information from fin_sale table using transactions' dates.
 *
 * @param string $date_from date from, format YYYY-mm-dd;
 * @param string $date_to date to, format YYYY-mm-dd;
 * @param integer $page
 * @param integer $on_page
 *
 * @return array
 */
function GetTransactionsByDate( $date_from, $date_to, $page, $on_page, $include_arr )
{
	// generate "date from":
	if ( !strlen( $date_from ) || !preg_match ('/^[0-9]{4}-([0-9]{2})-[0-9]{2}$/', $date_from ) || !is_array( $include_arr ) )
	{
		$_date_from_q = '';
	}
	else
	{
		$_data_arr = explode( '-', $date_from );
		$date_from = mktime (0, 0, 0, $_data_arr[1], $_data_arr[2], $_data_arr[0]);

		$_date_from_q = " AND `s`.`order_stamp`>'$date_from'";
	}
	
	// generate "date to":
	if ( !strlen( $date_to ) || !preg_match ('/^[0-9]{4}-([0-9]{2})-[0-9]{2}$/', $date_to ) )
	{
		$date_to = time();
	}
	else
	{
		$_data_arr = explode ( '-', $date_to );
		$date_to = mktime (0, 0, 0, $_data_arr[1], $_data_arr[2]+1, $_data_arr[0]);
	}
	
	$_deleted_q = ""; $_approval_q = "";
	// generate constrait query string
	if ( $include_arr['refunded'] == 'yes' )
		$_refunded_q_add = " OR `r`.`fin_sale_id`>0";
	
	if ( $include_arr['deleted'] != 'yes' )
		$_deleted_q.= " AND (`s`.`status`!='deleted'$_refunded_q_add)";
	
	if ( $include_arr['approval'] != 'yes' )
		$_approval_q.= " AND (`s`.`status`!='approval'$_refunded_q_add)";

	// --- QUERY ----
	$_query = "SELECT `s`.`fin_sale_id`,`s`.`payment_provider_order_number`,`s`.`order_stamp`,`s`.`amount`,`s`.`status`,
		`s`.`fin_payment_provider_id`,`p`.`username`,`p`.`profile_id`,`mt`.`membership_type_id`,`r`.`amount_fine` 
		FROM `".TBL_FIN_SALE."` AS `s` 
		LEFT JOIN `".TBL_PROFILE."` AS `p` ON( `s`.`profile_id`=`p`.`profile_id` ) 
		LEFT JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` ON( `mt`.`membership_type_id`=`s`.`membership_type_id` ) 
		LEFT JOIN `".TBL_FIN_REFUND."` AS `r` ON(`s`.`fin_sale_id`=r.`fin_sale_id`) 
		WHERE `s`.`order_stamp`<'$date_to'$_date_from_q $_deleted_q $_approval_q AND `s`.`fin_payment_provider_id`!=0 ORDER BY `s`.`order_stamp` DESC";

	$_fin_info = MySQL::fetchArray( $_query );
	if ( count( $_fin_info ) > 0 )
	{
		$_i = 1;
		$_j = 0;
		$_total = 0;
		$_refunded = 0;
		$_fines = 0;
		foreach( $_fin_info as $_value )
		{
			if ( $include_arr['refunded'] != 'yes' && $_value['amount_fine'] != '' )
				continue;
				
			if ( $_i > ( $page - 1 )*$on_page && $_i <= $page*$on_page )
			{
				$_transactions[$_j] = $_value;
				$_j++;
			}
			
			$_total += $_value['amount'];
			if ( $_value['amount_fine'] != '' )
			{
				$_refunded += $_value['amount'];
				$_fines += $_value['amount_fine'];
			}
			
			$_i++;
		}
		return array( $_transactions, $_total, $_refunded, $_fines, $_i-1 );
	}
	else
		return array( array(), 0, 0, 0);
}


/**
 * Returns array with the transaction's information.
 *
 * @param string $order payment provider order number
 * @param integer $provider_id payment provider id
 *
 * @return array
 */
function GetTransactionInfo( $order, $provider_id )
{
	$_query = "SELECT `s`.`fin_sale_id`,`s`.`profile_id`,`s`.`fin_payment_provider_id`,`s`.`payment_provider_order_number`,`s`.`order_stamp`,`s`.`amount`,`s`.`status`,`s`.`coupon`,
		`s`.`membership_type_id`,IFNULL(`m`.`start_stamp`,`s`.`order_stamp`) AS `start_stamp`,`s`.`is_recurring`,
		IF(`m`.`expiration_stamp`,`m`.`expiration_stamp`,IF(`s`.`unit`='months',`s`.`period`*30*24*3600,`s`.`period`)) AS `expiration_stamp`,
		`p`.`username`,`p`.`email`,`p`.`activity_stamp`,`r`.`refund_stamp`,`r`.`amount_fine`,`r`.`comment` 
		FROM `".TBL_FIN_SALE."` AS `s` 
		LEFT JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` ON( `s`.`membership_type_id`=`mt`.`membership_type_id` )
		LEFT JOIN `".TBL_MEMBERSHIP."` AS `m` ON( `m`.`fin_sale_id`=`s`.`fin_sale_id` )
		LEFT JOIN `".TBL_PROFILE."` AS `p` ON( `s`.`profile_id`=`p`.`profile_id` )
		LEFT JOIN `".TBL_FIN_REFUND."` AS `r` ON( `s`.`fin_sale_id`=`r`.`fin_sale_id` )
		WHERE `s`.`fin_payment_provider_id`='$provider_id' AND `s`.`payment_provider_order_number`='$order' LIMIT 1";
	
	return MySQL::FetchRow( $_query );
}


/**
 * Refund order.
 *
 * @access public
 * @param integer $sale_id sale id
 * @param decimal $fine_amount fine
 * @param string $comment admin's comment
 *
 * @return boolean
 */
function RefundOrder( $sale_id, $fine_amount, $comment )
{
	return MySQL::fetchResource( "INSERT INTO `".TBL_FIN_REFUND."` ( `fin_sale_id`,`refund_stamp`,`amount_fine`,`comment` ) 
		VALUES( '$sale_id','".time()."','$fine_amount','$comment' )" );
}


/**
 * Refund order.
 *
 * @access private
 * @param integer $sale_id sale id
 * @param decimal $fine_amount fine
 * @param string $comment admin's comment
 *
 * @return boolean
 */
function refundTransdaction( $sale_id, $fine_amount, $comment )
{
	return MySQL::fetchResource( "INSERT INTO `".TBL_FIN_REFUND."` ( `fin_sale_id`,`refund_stamp`,`amount_fine`,`comment` ) 
		VALUES( '$sale_id','".time()."','$fine_amount','$comment' )" );
}


/**
 * Returns profile's transaction history.
 *
 * @param integer $profile_id
 * @return array
 */
function getProfileTransactionHistory( $profile_id )
{
	$profile_id = intval( $profile_id );
	if ( !$profile_id )
		return array();
	
	return MySQL::fetchArray( "SELECT `s`.`fin_payment_provider_id`,`s`.`payment_provider_order_number`,`s`.`order_stamp`,`s`.`unit`,
		`s`.`period`,`s`.`amount`,`s`.`status`,`mt`.`type`,`mt`.`limit`,`r`.`amount_fine` AS `refund_fine`,`r`.`refund_stamp`,
		`r`.`comment` AS `refund_comment`
		FROM `".TBL_FIN_SALE."` AS `s`
		LEFT JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` USING(`membership_type_id`)
		LEFT JOIN `".TBL_FIN_REFUND."` AS `r` ON(`s`.`fin_sale_id`=`r`.`fin_sale_id`)
		WHERE `profile_id`='$profile_id' AND `status`='approval' AND `s`.`fin_payment_provider_id`!=0 ORDER BY `order_stamp`" );
}


/**
 * Updates a payment provider's info.
 *
 * @param integer $provider_id
 * @param string $status
 * @param string $currency
 * @param array $fields
 * @return boolean
 */
function updatePaymentProvider( $provider_id, $status, $currency, $fields, $plans, $packages, $fileName = '' )
{
	$provider_id = intval($provider_id);
	if ( !$provider_id || !is_array($fields) )
		return false;
	
	$status = ( $status == 'active' )? 'active' : 'inactive';
	$fileNameQuery = ( !strlen($fileName) )? '' : sql_placeholder(', `icon`=?', $fileName );
	
	//update provider info
	MySQL::fetchResource( sql_placeholder( "UPDATE `?#TBL_FIN_PAYMENT_PROVIDERS` SET `status`=?,`active_currency`=? $fileNameQuery WHERE `fin_payment_provider_id`=? AND `is_available`='y'", $status, $currency, $provider_id ) );

	//update provider fields
	foreach ( $fields as $name=>$value )
		MySQL::fetchResource( sql_placeholder("UPDATE `?#TBL_FIN_PAYMENT_PROVIDER_FIELD` SET `value`='".addslashes( $value )."' WHERE `fin_payment_provider_id`=? AND `name`=?", $provider_id, $name) );
	
    if ( is_array($packages) ) 
        app_UserPoints::updateProviderPackages($provider_id, $packages);
    
	return is_array($plans) ? updateProviderPlans( $provider_id, $plans ) : true;
}


function updateProviderPlans( $provider_id, $plans )
{	
	if ( !is_array($plans) )
		return false;
	
	foreach ($plans as $membership_plan_id=>$provider_plan_id) {
		$provider_plan_id = trim($provider_plan_id);
		
		$query = SK_MySQL::placeholder("REPLACE INTO `".TBL_FIN_PAYMENT_PROVIDER_PLAN."` SET 
            `fin_payment_provider_id`=?, `membership_type_plan_id`=?,`provider_plan_id`=?", 
            $provider_id, $membership_plan_id, $provider_plan_id);
		
        SK_MySQL::query($query);
	}
	
	return true;
}


/**
 * Shows notices of payment providers which depends on membership plan changes.
 *
 */
function showPaymentProviderNotices()
{
	global $frontend;
	
	$_payment_provider_arr = array( 4, 5 );
	
	foreach ( $_payment_provider_arr as $_provider )
	{
		$_provider_info = app_Finance::GetPaymentProviderInfo( $_provider );
		if ( $_provider_info['is_available'] == 'y' && $_provider_info['status'] == 'active' )
			$_related_providers[] = SK_Language::section('components.payment_selection')->text($_provider);
	}
	
	if ( $_related_providers )
	{
		foreach ( $_related_providers as $_provider )
			$_provider_str.= "\"$_provider\",";
	
		$_provider_str = substr( $_provider_str, 0, -1 );
		$frontend->registerMessage( 'You changed plans and it can cause errors in your site payment system. Please, inactivate '.$_provider_str.' payment provider(s) and contact SkaDate support team.', 'notice' );
	}
}


function getPaymentProviders()
{
	$provider_list = MySQL::fetchArray( "SELECT `fin_payment_provider_id` FROM `".TBL_FIN_PAYMENT_PROVIDERS."`" );
	
	foreach ($provider_list as $key=>$provider)
		$provider_list[$key] = getPaymentProviderInfo( $provider['fin_payment_provider_id'] );
	
	return $provider_list;
}


function getPaymentProviderInfo( $provider_id )
{
	if ( !is_numeric( $provider_id ) )
		return array();
	
	$info = MySQL::fetchRow( sql_placeholder( "SELECT * FROM `?#TBL_FIN_PAYMENT_PROVIDERS` WHERE `fin_payment_provider_id`=?", $provider_id ) );
	$info['fields'] = getPaymentProviderFields( $provider_id );
	$info['plans'] = ( $info['is_required_plan_synchronizing'] == 'y' )? getPaymentProviderPlans( $provider_id ) : array();
	$info['packages'] = ( $info['is_required_plan_synchronizing'] == 'y' )? getPaymentProviderPointPackages( $provider_id ) : array();
	
	return $info;
}


function getPaymentProviderPlans( $provider_id )
{
	if( !intval($provider_id) )
		return array();

	return MySQL::fetchArray( sql_placeholder("SELECT `m`.*,`t`.*,`p`.`provider_plan_id` 
		FROM `?#TBL_MEMBERSHIP_TYPE_PLAN` AS `m`
		INNER JOIN `?#TBL_MEMBERSHIP_TYPE` AS `t` USING(`membership_type_id`)
	 	LEFT JOIN `?#TBL_FIN_PAYMENT_PROVIDER_PLAN` AS `p` ON(`p`.`fin_payment_provider_id`=? AND `p`.`membership_type_plan_id`=`m`.`membership_type_plan_id`)
	 	WHERE `m`.`price`!=0", $provider_id) );
}

function getPaymentProviderPointPackages( $provider_id )
{
    if ( !$provider_id )
        return false;
        
    $query = SK_MySQL::placeholder("SELECT `pp`.*, `p`.* FROM `".TBL_USER_POINT_PROVIDER_PACKAGE."` AS `pp`
        LEFT JOIN `".TBL_USER_POINT_PACKAGE."` AS `p` ON (`pp`.`package_id`=`p`.`package_id`) 
        WHERE `pp_id`=?", $provider_id);
    
    $packages = array();
    
    $res = SK_MySQL::query($query);
    
    while ( $row = $res->fetch_assoc() )
    {
        $row['desc'] = SK_Language::text('%user_points.packages.package_'.$row['package_id']);
        $packages[] = $row;
    }
    
    return $packages;
}


function getPaymentProviderFields( $provider_id )
{
	if( !intval($provider_id) )
		return array();
	
	$fields = MySQL::fetchArray(sql_placeholder("SELECT * FROM `?#TBL_FIN_PAYMENT_PROVIDER_FIELD` WHERE `fin_payment_provider_id`=? ORDER BY `order`", $provider_id), 'name');
	
	foreach ( $fields as $key=>$field )
		if( $field['type'] == 'select' )
			$fields[$key]['values'] = MySQL::fetchArray(sql_placeholder("SELECT * FROM `?#TBL_FIN_PAYMENT_PROVIDER_FIELD_POSSIBLE_VALUE` WHERE `fin_payment_provider_id`=? AND `name`=?", $field['fin_payment_provider_id'], $field['name']));
	
	return $fields;
}

function deleteProviderIcon( $provider_id )
{
	$provider_id = intval($provider_id);
	$provider_info = getPaymentProviderInfo($provider_id);
	if ( unlink(DIR_USERFILES.$provider_info['icon']) )
		return ( MySQL::fetchResource( sql_placeholder("UPDATE `?#TBL_FIN_PAYMENT_PROVIDERS` SET `icon`='' WHERE `fin_payment_provider_id`=?", $provider_id) ) );					
}

?>