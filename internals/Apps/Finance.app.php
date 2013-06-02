<?php
 /**
  * Definitions of functions 
  * for working with finance (transactions)
  */
 

//require_once( DIR_APP.'app.membership.php' );

class app_Finance
{
	/**
	 * Returns array with the sale information from sale_info table.
	 *
	 * @param string $hash
	 * @return array
	 */
	public static function GetSaleInfo( $hash )
	{
		if ( !strlen( $hash ) )
			return array();
		
		return MySQL::FetchRow( sql_placeholder( "SELECT * FROM `?#TBL_FIN_SALE_INFO` WHERE `hash`=?", $hash ) );
	}
	
	
	/**
	 * Returns array with the sale information from sale_info table using fin_sale_info_id.
	 *
	 * @param integer $sale_info_id
	 * @return array
	 */
	public static function GetSaleInfoUsingId( $sale_info_id )
	{
		$sale_info_id = intval( $sale_info_id );
		if ( !$sale_info_id )
			return array();
		
		return MySQL::FetchRow( sql_placeholder( "SELECT * FROM `?#TBL_FIN_SALE_INFO` WHERE `fin_sale_info_id`=?", $sale_info_id ) );
	}
	
	
	/**
	 * Fill the sale info table by the PRECHECKOUT info.
	 *
	 * @param integer $profile_id
	 * @param integer $payment_provider_id
	 * @param integer $plan_id
	 * @param char $is_recurring - y | n
	 *
	 * @return array
	 */
	public static function FillSaleInfo( $profile_id, $payment_provider_id, $plan_id, $is_recurring = 'n', $discount = null, $code = null )
	{
		if ( !is_numeric( $payment_provider_id ) || !is_numeric( $plan_id ) )
			return array();
		
		$_membership_plan_arr = MySQL::fetchRow( sql_placeholder( "SELECT * FROM `?#TBL_MEMBERSHIP_TYPE_PLAN` 
				WHERE `membership_type_plan_id`=?", $plan_id ) );
		if ( !$_membership_plan_arr )
			return array();
		
		$_provider_info = app_Finance::GetPaymentProviderInfo( $payment_provider_id );
		if ( !$_provider_info )
			return array();
		
		$is_recurrring = ( $is_recurring == 'y' && $_membership_plan_arr['is_recurring'] == 'y' && $_provider_info['is_recurring'] == 'y' )? 'y' : 'n';
		
		$_hash = md5( rand( 100, 10000000 ) );
		
		$plan_price = $_membership_plan_arr['price'];
		$amount = $discount ? round($plan_price - $plan_price * $discount / 100, 2) : $plan_price;
		
		$_query = SK_MySQL::placeholder( "INSERT INTO `".TBL_FIN_SALE_INFO."` 
			(`fin_payment_provider_id`, `merchant_field`, `currency`,`claim_stamp`,`amount`,`profile_id`,`membership_type_plan_id`,`hash`,`is_recurring`,`coupon`) 
			VALUES( ?, '?', '?', ?, '?', ?, ?, '?', '?', '?' )", 
		    $payment_provider_id, $_provider_info['merchant_field'], $_provider_info['active_currency'], time(), 
			$amount, $profile_id, $plan_id, $_hash, $is_recurrring, $code );
	
		SK_MySQL::query($_query);
		
		$_fill_sale_info['custom'] = $_hash;
		$_fill_sale_info['sale_info_id'] = SK_MySQL::insert_id();
		$_fill_sale_info['provider_name'] = $_provider_info['name'];
		$_fill_sale_info['provider_info'] = $_provider_info;
		$_fill_sale_info['currency'] = $_provider_info['active_currency'];
		$_fill_sale_info['membership_type_id'] = $_membership_plan_arr['membership_type_id'];
		$_fill_sale_info['membership_type_plan_id'] = $_membership_plan_arr['membership_type_plan_id'];
		$_fill_sale_info['price'] = $amount;
		$_fill_sale_info['period'] = $_membership_plan_arr['period'];
		$_fill_sale_info['units'] = $_membership_plan_arr['units'];
		$_fill_sale_info['is_recurring'] = $is_recurrring;
		
		return $_fill_sale_info;
	}
	
	
	/**
	 * Fill the sale table by the POSTCHECKOUT info. It means that the transaction was successfully executed.
	 *
	 * @param integer $profile_id
	 * @param integer $payment_provider_id
	 * @param string $order_num transaction order number on the payment provider side.
	 * @param string $unit
	 * @param integer $period
	 * @param decimal $amount
	 * @param integer $membership_type_id
	 * @param string $status
	 * @param string $coupon
	 *
	 * @return boolean|integer
	 */
	public static function FillSale( $profile_id, $payment_provider_id, $order_num, $unit, $period, $amount, $membership_type_id, $is_recurring = false, $status ='approval', $coupon = null )
	{
		if ( !is_numeric( $payment_provider_id ) || !is_numeric( $amount ) || !is_numeric( $profile_id ) || 
			!is_numeric( $membership_type_id ) || !strlen( $order_num ) || !is_numeric( $period ) || ( $unit != 'days' && $unit != 'months' ) )
			return false;
	
		if ( $status != 'approval' )
			$status = 'deleted';
	
		$_recurring_q = ( $is_recurring )? 'y' : 'n';
		
		MySQL::fetchResource( sql_placeholder( "INSERT INTO `?#TBL_FIN_SALE` (`profile_id`,`fin_payment_provider_id`,
				`payment_provider_order_number`,`order_stamp`,`unit`,`period`,`amount`,`membership_type_id`,`status`,`is_recurring`,`coupon`) VALUES
				( ?@ )", array( $profile_id, $payment_provider_id, $order_num, time(), $unit, $period, $amount, $membership_type_id, $status, $_recurring_q, $coupon ) ) );
	
		$sale_id = mysql_insert_id();
		
		if ( strlen($coupon) && $sale_id )
		{
		    app_CouponCodes::trackCoupon($coupon, $profile_id, $membership_type_id);
		}
		
		return $sale_id;
	}
	
	
	/**
	 * Check transaction information:
	 * <UL>
	 * <li>Check if returned incorrect transaction amount<li>
	 * <li>Check if the membership type's plan was deleted when the transaction was processing</li>
	 * <li>Check if the bought membership type was deleted when transactoin was processing</li>
	 * <li>Check if the transaction amount is correct( the posted amount does not equal the returned amount )</li>
	 * <li>Optionally Checks if the transaction currency is correct(posted and returned)</li>
	 * </UL>
	 * Returns:
	 * <UL>
	 * <li>1 - all data are correct</li>
	 * <li>-1 - the sale information record (which was put to the db by the pre_checkout script) was not found</li>
	 * <li>-2 - the membership type's plan was deleted in the transaction processing</li>
	 * <li>-3 - if the membership type was deleted in the transaction processing</li>
	 * <li>-4 - if the returned amount does not equal posted one</li>
	 * <li>-5 - if the returned currency does not equal posted one</li>
	 * <li>-7 - if the transaction was executed before</li>
	 * </UL>
	 *
	 * @param string $hash sale's hash
	 * @param string $transaction_order
	 * @param decimal $amount transaction amount (posted by payment provider)
	 * @param string $currency - currency (posted by payment provider)
	 *
	 * @return integer
	 */
	public static function CheckTransactionInfo( $hash, $transaction_order = '', $amount = '', $currency = '', $new_tid = '' )
	{
		$_sale_arr = MySQL::fetchRow( sql_placeholder( "SELECT * 
			FROM `?#TBL_FIN_SALE_INFO` WHERE `hash`=?", $hash ) );
		if ( !$_sale_arr )
			return -1;
	
		if (strlen($new_tid))
		{
			if ( MySQL::fetchRow( "SELECT `fin_sale_id` FROM `".TBL_FIN_SALE."` WHERE `payment_provider_order_number`='$new_tid' AND `fin_payment_provider_id`={$_sale_arr['fin_payment_provider_id']}" ) )
				return -7;
			else {
				$query = SK_MySQL::placeholder("UPDATE `".TBL_FIN_SALE_INFO."` SET `merchant_field` = '?' 
					WHERE `payment_provider_order_number`='$transaction_order' AND `fin_payment_provider_id`={$_sale_arr['fin_payment_provider_id']}", $new_tid);
				SK_MySQL::query($query);
			}
		}
		else
		{
			if ( $transaction_order )
				if ( MySQL::fetchRow( "SELECT `fin_sale_id` FROM `".TBL_FIN_SALE."` WHERE `payment_provider_order_number`='$transaction_order' AND `fin_payment_provider_id`={$_sale_arr['fin_payment_provider_id']}" ) )
					return -7;
		}
		
		
		$_plan_arr = MySQL::fetchRow( sql_placeholder( "SELECT `membership_type_id` FROM `?#TBL_MEMBERSHIP_TYPE_PLAN` 
										WHERE `membership_type_plan_id`=?", $_sale_arr['membership_type_plan_id'] ) );
		if ( !$_plan_arr )
			return -2;
			
		if ( !( MySQL::fetchRow( sql_placeholder( "SELECT `membership_type_id` FROM `?#TBL_MEMBERSHIP_TYPE` 
									WHERE `membership_type_id`=?", $_plan_arr['membership_type_id'] ) ) ) )
			return -3;

		if ( $amount != '' )
			if ( $amount != $_sale_arr['amount'] )
				return -4;
		
		if ( $currency )
			if ( $currency != $_sale_arr['currency'] )
				return -5;
		
		return 1;
	}

	/**
	 * Complete the sale info table by the POSTCHECKOUT info. 
	 * Parameter $result must be<br>
	 * <UL>
	 * <li>approval</li>
	 * <li>cancel - profile has canceled recurring payments</li>
	 * <li>declined</li>
	 * <li>error</li>
	 * <li>processing</li>
	 * <li>internal_error_trial_in_consideration</li>
	 * <li>internal_error_trial_used</li>
	 * </UL>
	 * Parameter $transaction_order and $currency can be passed when $result is 'approval'.
	 *
	 * @param string $hash
	 * @param string $result
	 * @param decimal $amount
	 * @param string $transaction_order
	 * @param string $currency - currency (posted by payment provider)
	 * @return boolean
	 */
	public static function SetTransactionResult( $hash, $result, $amount = '', $transaction_order = '', $currency = '', $new_tid = '' )
	{
		if ( !strlen( $hash ) )
			return false;
		
		if ( $result == 'approval' )
		{
			if ( !strlen( $transaction_order ) )
				$_query_arr = array( 'status' => 'error' );
			else
			{
				$check_res = app_Finance::CheckTransactionInfo( $hash, $transaction_order, $amount, $currency, $new_tid );
				switch ( $check_res )
				{
					case 1:
						$_query_arr = array( 'status'=>$result, 'payment_provider_order_number'=>$transaction_order );
						break;
					
					case -1:
					case -4:
					case -5:
					case -7:
						return false;
						break;
						
					case -2:
					case -3:
						$_query_arr = array( 'status' => 'internal_error' );
						break;
					
					default:
						$_query_arr = array( 'status' => 'error' );
				}
			}	
			MySQL::affectedRows( sql_placeholder( "UPDATE `?#TBL_FIN_SALE_INFO` SET ?%
						WHERE `hash`=?", $_query_arr, $hash ) );
			return true;
		}
		elseif ( $result =='declined' || $result == 'error' || $result == 'processing' || $result == 'cancel' || 
			$result == 'internal_error_trial_in_consideration' || $result == 'internal_error_trial_used' )
		{
			MySQL::affectedRows( sql_placeholder( "UPDATE `?#TBL_FIN_SALE_INFO` SET `status`=? 
								WHERE `hash`=?", $result, $hash ) );
			return true;
		}
		else
			return false;
	}
	
	
	/**
	 * Returns list of the active(parameter is "active") or all payment providers (parameter - any string except "active")
	 *
	 * @param boolean $only_active
	 * @return array
	 */
	public static function GetPaymentProviders ()
	{
		$provider_list = MySQL::FetchArray( "SELECT `fin_payment_provider_id` FROM `".TBL_FIN_PAYMENT_PROVIDERS."` WHERE `is_available`='y' AND `status`='active'" );
		
		foreach ($provider_list as $key=>$provider)
			$provider_list[$key] = app_Finance::GetPaymentProviderInfo($provider['fin_payment_provider_id']);
		
		return $provider_list;
	}

	public static function getSyncPaymentProviders( )
    {
        $query = SK_MySQL::placeholder("SELECT `fin_payment_provider_id` 
            FROM `".TBL_FIN_PAYMENT_PROVIDERS."` WHERE `is_required_plan_synchronizing` = 'y'");
        
        $res = SK_MySQL::query($query);
        
        $provider_list = array();
        
        while ( $field = $res->fetch_cell() )
        {
            $provider_list[] = $field;
        }
                
        return $provider_list;
    }
	
	
	/**
	 * Returns Payment provider's info: provider id, name, fields, description.
	 *
	 * @param integer $provider_id
	 * @return array
	 */
	public static function GetPaymentProviderInfo( $provider_id )
	{
		if ( !is_numeric( $provider_id ) )
			return array();
		
		static $_payment_provider_info;
		
		if ( !$_payment_provider_info || $_payment_provider_info['fin_payment_provider_id'] != $provider_id )
		{
			$_payment_provider_info = MySQL::FetchRow( sql_placeholder( "SELECT * FROM `?#TBL_FIN_PAYMENT_PROVIDERS` WHERE `fin_payment_provider_id`=?", $provider_id ) );
			$_payment_provider_info['fields'] = app_Finance::getPaymentProviderFields( $provider_id );
		}
		
		return $_payment_provider_info;
	}
	
	
	public static function getPaymentProviderFields( $provider_id )
	{
		if ( !is_numeric($provider_id) )
		{
		    $sql = SK_MySQL::placeholder("SELECT `fin_payment_provider_id` FROM `".TBL_FIN_PAYMENT_PROVIDERS."` WHERE `name`='?'", $provider_id);
		    
		    if ( $id = SK_MySQL::query($sql)->fetch_cell() )
		    {
                $provider_id = $id;
		    }
		    else 
		    {
		        return array();
		    }
		}
		
		return MySQL::fetchArray(sql_placeholder("SELECT `name`,`value` FROM `?#TBL_FIN_PAYMENT_PROVIDER_FIELD` WHERE `fin_payment_provider_id`=?", $provider_id), 1);
	}

	
	public static function isPlanConfiguredForProvider( $membership_plan_id, $provider_id )
	{
		return ( MySQL::fetchRow( sql_placeholder("SELECT `provider_plan_id` FROM `?#TBL_FIN_PAYMENT_PROVIDER_PLAN` WHERE `fin_payment_provider_id`=? AND `membership_type_plan_id`=?", $provider_id, $membership_plan_id) ) );
	}
	
	
	public static function getProviderPlanIdForMembershipPlan( $provider_id, $membership_plan_id )
	{
		return MySQL::fetchField( sql_placeholder("SELECT `provider_plan_id` FROM `?#TBL_FIN_PAYMENT_PROVIDER_PLAN` WHERE `fin_payment_provider_id`=? AND `membership_type_plan_id`=?", $provider_id, $membership_plan_id) );
	}
	
	
	public static function updateLastRequestTime( $provider_id )
	{
		if( !intval($provider_id) )
			return false;
		
		return ( MySQL::affectedRows(sql_placeholder("UPDATE `?#TBL_FIN_PAYMENT_PROVIDERS` SET `last_request_time`=UNIX_TIMESTAMP() WHERE `fin_payment_provider_id`=?", $provider_id) ) > 0 );
	}
	
	
	/**
	 * Clears fin sale info table. (For CRON)
	 *
	 * @return integer - deleted rows
	 */
	public static function clear_FinSaleInfoTable()
	{
		return MySQL::affectedRows( "DELETE FROM `".TBL_FIN_SALE_INFO."` WHERE 
			((`is_recurring`='y' AND `status`!='approval') OR `is_recurring`='n') AND 
						`claim_stamp` + 15552000 < ".time() );
	}
	
	
	/**
	 * Logs payment data.
	 *
	 * @param misc $stringORdata
	 * @param string $file
	 * @param string $line
	 * @param boolean|string $update_now_and_hash_is
	 * @return boolean
	 */
	public static function LogPayment( $stringORdata, $file, $line, $update_now_and_hash_is = false )
	{
		static $log_string = '';
		
		if ( is_array($stringORdata) )
			foreach ( $stringORdata as $key=>$value )
				$string.= "$key => $value\n";
		elseif( is_bool($stringORdata) )
			$string = ( $stringORdata )? 'true' : 'false';
		else
			$string = $stringORdata;
		
		$log_string.= "File-$file; line-$line.\n$string\n\n";
		
		if ( strlen($update_now_and_hash_is) )
		{
			 $res = ( MySQL::affectedRows( sql_placeholder("UPDATE `".TBL_FIN_SALE_INFO."` SET `log`=?
			     WHERE `hash`=?", $log_string, $update_now_and_hash_is) ) )? true : false;
			$log_string = '';
			return $res;
		}
		
		return true;
	}
	
	/**
	 * Checks if the  payment provider requires extra fields to be filled in by member
	 * on the checkout.php page
	 * 
	 * @param string $provider
	 * 
	 * @return boolean
	 */
	public static function checkIfProviderExtraFieldsRequired( $provider )
	{
		if (!isset($provider))
			return false;
		
		$query = SK_MySQL::placeholder("SELECT `fin_payment_provider_id` FROM `".TBL_FIN_PAYMENT_PROVIDERS."` 
			WHERE `name`='?' AND `requires_extra_fields`='y'", $provider);
		
		$result = SK_MySQL::query($query);
		if ($result->num_rows())
			return true;
	}
	
	public static function getUnsubscribeBtn( $profile_id )
	{
	    $ms = app_Membership::profileCurrentMembershipInfo($profile_id);
	    
	    if ( $ms['limit'] != 'limited' )
	    {
	        return '';	        
	    }
	    
	    $query = SK_MySQL::placeholder("SELECT `s`.`fin_payment_provider_id` FROM `".TBL_FIN_SALE."` AS `s`
	       LEFT JOIN `".TBL_MEMBERSHIP."` AS `ms` ON (`s`.`fin_sale_id`=`ms`.`fin_sale_id`)
	       WHERE `s`.`profile_id` = ? AND `s`.`is_recurring` = 'y' AND `s`.`status` = 'approval'
	       AND `s`.`fin_payment_provider_id` != 0 AND `ms`.`expiration_stamp` > ?
	       ORDER BY `order_stamp` DESC
	       LIMIT 1", $profile_id, time());
	    
	    $provider = SK_MySQL::query($query)->fetch_cell();
	    
	    if ( !$provider )
	    {
	       return '';	        
	    }
	    	    
	    switch ( $provider )
	    {
	        case 3:
	            $fields = app_Finance::getPaymentProviderFields('PayPal');
	            $code = 
'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_subscr-find&alias='.$fields['merchant_id'].'">
<img src="https://www.paypal.com/en_US/i/btn/btn_unsubscribe_SM.gif" border="0">
</a>';
	            break;
	            
	        default:
	            $code = '';
	    }
	    
	    return $code;
	    
	}
	
}
?>
