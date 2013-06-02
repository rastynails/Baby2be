<?php

class app_SMSBilling {
	
	/**
	 * Get activated SMS payment provider info
	 * Notice: only one provider can be activated at a time
	 *
	 * @return array
	 */
	public static function getActiveProvider()
	{
		$query = "SELECT * FROM `" . TBL_SMS_PROVIDER . "` WHERE `active`=1 LIMIT 1";
		
		return SK_MySQL::query($query)->fetch_assoc();
	}
	
	/**
	 * Get SMS service info
	 *
	 * @param string $service_name
	 * @return array
	 */
	public static function getService($service_name)
	{
		if (!strlen($service_name))
			return array();
			
		$query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_SMS_SERVICE ."` 
			WHERE `service_key`='?'", $service_name);
		$service = SK_MySQL::query($query)->fetch_assoc();
		$service['cost'] = round($service['cost'], 2); 
		return $service;
	}
	
	/**
	 * Get merchant key of SMS payment provider
	 *
	 * @param string $provider
	 * @return string
	 */
	public static function getProviderMerchantKey($provider)
	{
		if (!strlen($provider))
			return false;
			
		$query = SK_MySQL::placeholder("SELECT `merchant_key` 
			FROM `" . TBL_SMS_PROVIDER . "`
			WHERE `name`='?'", $provider);

		return SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Returns SMS provider ID by its name
	 *
	 * @param string $name
	 * @return integer
	 */
	public static function getProviderIdByName($name)
	{
		$query = SK_MySQL::placeholder("SELECT `sms_provider_id` FROM `" . TBL_SMS_PROVIDER . "` 
			WHERE `name`='?' LIMIT 1", $name);
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Returns SMS service field value
	 *
	 * @param string $service_key
	 * @param string $field_name
	 * @param string $provider
	 * @return string
	 */
	public static function getServiceField($service_key, $field_name, $provider = null)
	{
		if (!strlen($service_key) || !strlen($field_name))
			return false;

		if (is_null($provider)) { 
			$provider_info = self::getActiveProvider();
			$provider = intval($provider_info['sms_provider_id']);
		}
		else 
			$provider = self::getProviderIdByName($provider);
	
		$query = SK_MySQL::placeholder("SELECT `value` FROM `".TBL_SMS_SERVICE_FIELD."`
			WHERE `service_key`='?' AND `field_name`='?' AND `sms_provider_id`=?", $service_key, $field_name, $provider);

		return SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Returns SMS provider field value
	 *
	 * @param string $field_name
	 * @param string $provider
	 * @return string
	 */
	public static function getProviderField($field_name, $provider = null)
	{
		if (!strlen($field_name))
			return false;

		if (is_null($provider)) { 
			$provider_info = self::getActiveProvider();
			$provider = (int)$provider_info['sms_provider_id'];
		}
		else 
			$provider = self::getProviderIdByName($provider);
	
		$query = SK_MySQL::placeholder("SELECT `value` FROM `".TBL_SMS_PROVIDER_FIELD."`
			WHERE `field_name`='?' AND `sms_provider_id`=?", $field_name, $provider);

		return SK_MySQL::query($query)->fetch_cell();
	}
		
	/**
	 * Returns SMS services with status 'active'=1
	 *
	 * @return array
	 */
	public static function getActiveServices()
	{
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_SMS_SERVICE."` WHERE `active`=1");
		
		$res = SK_MySQL::query($query);
		
		$services = array();
		while ($row = $res->fetch_assoc()) {
			$row['cost'] = round($row['cost'], 2);
			$services[] = $row;
		}
		
		return $services; 
	}
	
	/**
	 * Checks if SMS service is active
	 *
	 * @param string $service_key
	 * @return boolean
	 */
	public static function serviceIsActive($service_key)
	{
		$query = SK_MySQL::placeholder("SELECT `active` FROM `".TBL_SMS_SERVICE."` 
			WHERE `service_key`='?'", $service_key);
		
		return SK_MySQL::query($query)->fetch_cell() ? true : false;
	}
	
	/**
	 * Add record about SMS payment attempt
	 *
	 * @param array $params
	 * @return boolean
	 */
	public static function registerPaymentAttempt(array $params)
	{
		if (!is_array($params))
			return false;
		
		$query_fields = "";
		$query_values = "";
		$provider = app_SMSBilling::getActiveProvider();
		
		foreach ($params as $field => $value) {
			$query_fields .= "`$field`, "; 
			$query_values .= is_string($value) ? "'$value', " : "$value, "; 
		}
		
		$query_fields .= "`sms_provider_id`, `verified`";
		$query_values .= $provider['sms_provider_id'] . ", 0";
		
		$query = "INSERT INTO `".TBL_SMS_PAYMENT."` ($query_fields) VALUES ($query_values)";

		SK_MySQL::query($query);
		
		return SK_MySQL::affected_rows() ? true : false;
	}
	
	/**
	 * Returns payment info by payment hash
	 *
	 * @param string $hash
	 * @return array
	 */
	public static function getPaymentByHash($hash)
	{
		if (!strlen($hash))
			return false;
			
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_SMS_PAYMENT."` 
			WHERE `hash`='?' LIMIT 1", $hash);
			
		return SK_MySQL::query($query)->fetch_assoc();
	}
	
	/**
	 * Updates SMS payment's status to 'verified'
	 * It means that member's SMS transaction was approved by payment provider,
	 * so we can grant member the service he has bought 
	 *
	 * @param string $hash
	 * @param array $params
	 * @return boolean
	 */
	public static function setPaymentStatusVerified($hash, $params = null)
	{
		if (!strlen($hash))
			return false;
		
		$query_fields = "";
		if ($params) {
			foreach ($params as $field => $value) {
				$val = is_string($value) ? "'$value', " : "$value, ";
				$query_fields .= "`$field`=" . $val;
			}
		}
		$query_fields .= "`timestamp`=" . time() . ", `verified`=1";
		
		$query = SK_MySQL::placeholder("UPDATE `".TBL_SMS_PAYMENT."` 
			SET $query_fields WHERE `hash`='?'", $hash);

		SK_MySQL::query($query);
		
		$status_set = SK_MySQL::affected_rows() ? true : false;
		
		if ($status_set) {
			if (self::giveProfileService($hash))
				return true;
		}
		
		return false;
	}
	
	/**
	 * Grants member with the service he has bought
	 *
	 * @param string $payment_hash
	 * @return unknown
	 */
	public static function giveProfileService($payment_hash)
	{
		if (!strlen($payment_hash))
			return false;
			
		$payment = self::getPaymentByHash($payment_hash);

		if ($payment['profile_id'] && strlen($payment['service_key'])) {
			$service = self::getService($payment['service_key']);
			switch ($payment['service_key']) {
				
				case 'hot_list':
					if (!app_HotList::isHot($payment['profile_id'])) {
						if (app_HotList::addProfile($payment['profile_id']))
							return true;
					}
					break;
				
				case '24hour_membership':
					$m_type_id = self::getServiceField($payment['service_key'], 'membership_type_id');
					if (self::giveMembership($payment['profile_id'], array('units' => 'days', 'period' => 1, 'type_id' => $m_type_id, 'cost' => $service['cost'])))
						return true;

					break;
			}
		}
		return false;
	}
	
	/**
	 * Give provile 24-hour trial membership.
	 *
	 * @param integer $profile_id
	 * @param array $membership
	 * 
	 * @return boolean
	 */
	public static function giveMembership($profile_id, $membership)
	{
		if (!$profile_id)
			return false;

		// check if profile has claimed trial membership before
		//$result = app_Membership::CheckIfProfileClaimedTheMembership($profile_id, $membership['type_id'], 'type');
		
		//if ($result == 1) {
			// add record to fin sale table
			$sale_id = app_Finance::FillSale($profile_id, 0, '24hour', $membership['units'], $membership['period'], $membership['cost'], $membership['type_id'] );
								
			if ( $sale_id === false )
				return false;

			// add record to membership table
			$membership_id = app_Membership::BuySubscriptionTrialMembership($profile_id, $membership['type_id'], $membership['period'], $membership['units'], $sale_id);
			if ($membership_id) { 
				$profile_info = app_Profile::getFieldValues($profile_id, array('profile_id', 'language_id', 'email'));
			
				app_Membership::sendNotifyPurchaseMembershipMail('trial', $profile_info, $membership['type_id'], $membership_id);
				return true;
			}
		//}
		//return false;
	}
	
	/**
	 * Check payment status
	 *
	 * @param string $hash
	 * @return boolean
	 */
	public static function paymentIsVerified($hash)
	{
		$query = SK_MySQL::placeholder("SELECT `verified` FROM `".TBL_SMS_PAYMENT."` 
			WHERE `hash`='?'", $hash);
		
		$status = SK_MySQL::query($query)->fetch_cell();
		
		return $status == 1 ? true : false;
	}
	
	/**
	 * Delete expired payment attempts (by cron)
	 *
	 */
	public static function deleteExpiredPayments()
	{
		$query = "DELETE FROM `".TBL_SMS_PAYMENT."` WHERE `timestamp` < " . (time() - 60 * 60 * 24 * 30) . " AND `verified`=0";
		SK_MySQL::query($query);		
	}

}
