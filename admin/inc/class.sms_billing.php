<?php

class SMS_Billing {

	public static function getSMSProviders()
	{
		$query = "SELECT `sms_provider_id` FROM `".TBL_SMS_PROVIDER."`";
		
		$res = SK_MySQL::query($query);
		
		$provider_list = array();
		while ($pr_id = $res->fetch_cell())
			$provider_list[] = self::getProviderInfo($pr_id);
							
		return $provider_list;
	}
	
	public static function getProviderInfo($provider_id)
	{
		if ( !is_numeric($provider_id) )
			return array();
		
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_SMS_PROVIDER."` 
			WHERE `sms_provider_id`=?", $provider_id);
		
		$info = SK_MySQL::query($query)->fetch_assoc();
		$info['fields'] = self::getProviderFields($provider_id);
		
		return $info;
	}
	
	public static function getProviderFields($provider_id)
	{
		if( !intval($provider_id) )
			return array();
		
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_SMS_PROVIDER_FIELD."` 
			WHERE `sms_provider_id`=? ORDER BY `order`", $provider_id);
		
		$res = SK_MySQL::query($query);
		
		$fields = array();
		while ($row = $res->fetch_assoc()) {
			$fields[$row['field_name']] = $row;
		}
		
		return $fields;
	}
	
	public static function getSMSServices()
	{
		$query = "SELECT * FROM `".TBL_SMS_SERVICE."`";
		
		$res = SK_MySQL::query($query);
		
		$services = array();
		while ($row = $res->fetch_assoc())
			$services[] = $row;

		return $services;
	}
	
	/**
	 * Sets status 'active' for specified SMS provider
	 *
	 * @param integer $provider_id
	 * @return boolean
	 */
	public static function activateProvider($provider_id)
	{
		if( !intval($provider_id) )
			return array();
			
		$query = "UPDATE `".TBL_SMS_PROVIDER."` SET `active` = 0 WHERE 1";
		
		SK_MySQL::query($query);
		
		$query = SK_MySQL::placeholder("UPDATE `".TBL_SMS_PROVIDER."` SET `active` = 1 
			WHERE `sms_provider_id`=?", $provider_id);
		
		SK_MySQL::query($query);
		
		return SK_MySQL::affected_rows() ? true : false;
	}
	
	/**
	 * Deactivates SMS provider
	 *
	 * @param integer $provider_id
	 * @return boolean
	 */
	public static function disableProvider($provider_id)
	{
		if( !intval($provider_id) )
			return array();
			
		$query = SK_MySQL::placeholder("UPDATE `".TBL_SMS_PROVIDER."` SET `active` = 0 
			WHERE `sms_provider_id`=?", $provider_id);
			
		SK_MySQL::query($query);
			
		return SK_MySQL::affected_rows() ? true : false;
	}
	
	/**
	 * Updates SMS provider info.
	 *
	 * @param integer $provider_id
	 * @param string $merchant_key
	 * @param array $fields
	 * @return boolean
	 */
	public static function updatePaymentProvider( $provider_id, $merchant_key, $fields )
	{
		$provider_id = intval($provider_id);
		if ( !$provider_id )
			return false;
			
		//update provider info
		$query = SK_MySQL::placeholder("UPDATE `".TBL_SMS_PROVIDER."` 
			SET `merchant_key`='?' 
			WHERE `sms_provider_id`=?", $merchant_key, $provider_id);
			
		SK_MySQL::query($query);
	
		if (is_array($fields)) {
			//update provider fields
			$compiled_q = SK_MySQL::compile_placeholder("UPDATE `".TBL_SMS_PROVIDER_FIELD."` SET `value`='?' 
				WHERE `sms_provider_id`=? AND `field_name`='?'");
			
			foreach ( $fields as $name => $value ) {
				$query = SK_MySQL::placeholder($compiled_q, $value, $provider_id, $name);
				SK_MySQL::query($query);
			}
		}
		return true;
	}

	/**
	 * Updates SMS services' data
	 *
	 * @param array $services
	 * @return boolean
	 */
	public static function updateSMSServices($services)
	{
		if (!is_array($services))
			return false;
			
		if (!self::getActiveProvider())
			return false;
			
		$compiled_q = SK_MySQL::compile_placeholder("UPDATE `".TBL_SMS_SERVICE ."` SET `active`=?, `cost`='?' WHERE `service_key`='?'");
		foreach ($services as $key => $value) {
			$active = isset($value['active']) ? 1 : 0; 
			$query = SK_MySQL::placeholder($compiled_q, $active, $value['cost'], $key);
			SK_MySQL::query($query);
		}
		
		return true;
	}
	
	/**
	 * Returns SMS service's fields
	 *
	 * @param string $service_key
	 * @param integer $provider_id
	 * @return array
	 */
	public static function getServiceFields($service_key, $provider_id = null)
	{
		if (!strlen($service_key)) 			
			return false;
		
		if (!$provider_id)
			$provider_id = self::getActiveProvider();
		
		$query = SK_MySQL::placeholder("SELECT `f`.* FROM `".TBL_SMS_SERVICE_FIELD."` AS `f`
			LEFT JOIN `".TBL_SMS_SERVICE."` AS `s` ON (`f`.`service_key`=`s`.`service_key`)
			WHERE `s`.`service_key`='?' AND `sms_provider_id`=? 
			AND `s`.`active`=1 ORDER BY `order` ASC", $service_key, $provider_id);
	
		$res = SK_MySQL::query($query);
		
		while ($row = $res->fetch_assoc())
			$sfields[] = $row;

		return $sfields;
	}
	
	/**
	 * Returns active provider id
	 *
	 * @return integer
	 */
	public static function getActiveProvider()
	{
		$query = "SELECT `sms_provider_id` FROM `" . TBL_SMS_PROVIDER . "` WHERE `active`=1 LIMIT 1";
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Gets service info
	 *
	 * @param string $service_key
	 * @return array
	 */
	public static function getServiceInfo($service_key)
	{
		if (!strlen($service_key))
			return array();

		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_SMS_SERVICE."` WHERE `service_key`='?'", $service_key);
		
		return SK_MySQL::query($query)->fetch_assoc();
	}
	
	/**
	 * Update service fields
	 *
	 * @param string $service_key
	 * @param array $s_fields
	 * @return boolean
	 */
	public static function updateServiceFields($service_key, $s_fields)
	{
		if (!is_array($s_fields) || !strlen($service_key))
			return false;
			
		$compiled_q = SK_MySQL::compile_placeholder("UPDATE `".TBL_SMS_SERVICE_FIELD ."` 
			SET `value`='?' WHERE `field_name`='?' AND `service_key`='?' AND `sms_provider_id`=?");

		foreach ($s_fields as $key => $field) {
			$query = SK_MySQL::placeholder($compiled_q, $field, $key, $service_key, self::getActiveProvider() );
			SK_MySQL::query($query);
		}
		
		return true;
	}
	
	/**
	 * Returns SMS transactions by specified interval of time.
	 *
	 * @param string $date_from, format YYYY-mm-dd
	 * @param string $date_to, format YYYY-mm-dd
	 * @param integer $page
	 * @param integer $on_page
	 *
	 * @return array
	 */
	public static function getTransactionsByDate( $date_from, $date_to, $page, $on_page )
	{
		// make up "date from":
		if ( !strlen( $date_from ) || !preg_match ('/^[0-9]{4}-([0-9]{2})-[0-9]{2}$/', $date_from ) ) {
			$date_from_q = '';
		} else {
			$data_arr = explode( '-', $date_from );
			$date_from = mktime (0, 0, 0, $data_arr[1], $data_arr[2], $data_arr[0]);
	
			$date_from_q = " AND `p`.`timestamp`>'$date_from'";
		}
		
		$page = isset($page) ? $page : 1;
		$limit_q = " LIMIT ".( ( $page - 1 ) * $on_page ).",$on_page";
		
		// make up "date to":
		if ( !strlen( $date_to ) || !preg_match ('/^[0-9]{4}-([0-9]{2})-[0-9]{2}$/', $date_to ) ) {
			$date_to = time();
		} else {
			$data_arr = explode ( '-', $date_to );
			$date_to = mktime (0, 0, 0, $data_arr[1], $data_arr[2] + 1, $data_arr[0]);
		}

		$query = "SELECT `p`.*, `pr`.`username`,`pr`.`profile_id`, `s`.`description`, `s`.`cost`, `sp`.`name`
			FROM `".TBL_SMS_PAYMENT."` AS `p`
			LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`p`.`profile_id`=`pr`.`profile_id`)
			LEFT JOIN `".TBL_SMS_SERVICE."` AS `s` ON(`s`.`service_key`=`p`.`service_key`)
			LEFT JOIN `".TBL_SMS_PROVIDER."` AS `sp` ON(`sp`.`sms_provider_id`=`p`.`sms_provider_id`)
			WHERE `p`.`timestamp`<'$date_to'$date_from_q AND `p`.`verified`=1 
			ORDER BY `p`.`timestamp` DESC $limit_q";
		
		$res = SK_MySQL::query($query);
		
		$fin_info = array();
		$total = 0;
		while ($row = $res->fetch_assoc())
		{
			$total += $row['cost'];
			$fin_info['list'][] = $row;
		}
		$fin_info['ord_num'] = $number;
		$fin_info['total'] = $total;
		
		$query = "SELECT COUNT(`p`.`payment_id`)
			FROM `".TBL_SMS_PAYMENT."` AS `p`
			LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`p`.`profile_id`=`pr`.`profile_id`)
			LEFT JOIN `".TBL_SMS_SERVICE."` AS `s` ON(`s`.`service_key`=`p`.`service_key`)
			LEFT JOIN `".TBL_SMS_PROVIDER."` AS `sp` ON(`sp`.`sms_provider_id`=`p`.`sms_provider_id`)
			WHERE `p`.`timestamp`<'$date_to'$date_from_q AND `p`.`verified`=1";

		$fin_info['total_num'] = SK_MySQL::query($query)->fetch_cell();
		
		return $fin_info;
	}
	
	/**
	 * Returns SMS transactions by profile.
	 *
	 * @param string $profile
	 * @param integer $page
	 * @param integer $on_page
	 *
	 * @return array
	 */
	public static function getTransactionsByProfile( $profile, $page, $on_page )
	{
		// check if argument passed or not:
		if ( !strlen($profile) )
			return array();
		
		// check the argument is a email or username, then generate part of query:
		$profile_q = preg_match( '/@/', $profile ) ? 'email' : 'username';
				
		$page = isset($page) ? $page : 1;
		$limit_q = " LIMIT ".( ( $page - 1 ) * $on_page ).",$on_page";

		$query = "SELECT `p`.*, `pr`.`username`,`pr`.`profile_id`, `s`.`description`, `s`.`cost`, `sp`.`name`
			FROM `".TBL_SMS_PAYMENT."` AS `p`
			LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`p`.`profile_id`=`pr`.`profile_id`)
			LEFT JOIN `".TBL_SMS_SERVICE."` AS `s` ON(`s`.`service_key`=`p`.`service_key`)
			LEFT JOIN `".TBL_SMS_PROVIDER."` AS `sp` ON(`sp`.`sms_provider_id`=`p`.`sms_provider_id`)
			WHERE `pr`.`$profile_q`='$profile' AND `p`.`verified`=1 
			ORDER BY `p`.`timestamp` DESC $limit_q";
		
		$res = SK_MySQL::query($query);
		
		$fin_info = array();
		$total = 0;
		while ($row = $res->fetch_assoc())
		{
			$total += $row['cost'];
			$fin_info['list'][] = $row;
		}
		$fin_info['ord_num'] = $number;
		$fin_info['total'] = $total;
		
		$query = "SELECT COUNT(`p`.`payment_id`)
			FROM `".TBL_SMS_PAYMENT."` AS `p`
			LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`p`.`profile_id`=`pr`.`profile_id`)
			LEFT JOIN `".TBL_SMS_SERVICE."` AS `s` ON(`s`.`service_key`=`p`.`service_key`)
			LEFT JOIN `".TBL_SMS_PROVIDER."` AS `sp` ON(`sp`.`sms_provider_id`=`p`.`sms_provider_id`)
			WHERE `pr`.`$profile_q`='$profile' AND `p`.`verified`=1";

		$fin_info['total_num'] = SK_MySQL::query($query)->fetch_cell();
		
		return $fin_info;
	}
	
	/**
	 * Returns SMS transactions by order.
	 *
	 * @param string $order
	 *
	 * @return array
	 */
	public static function getTransactionsByOrder( $order )
	{
		if ( !strlen($order) )
			return array();
		
		$query = "SELECT `p`.*, `pr`.`username`,`pr`.`profile_id`, `s`.`description`, `s`.`cost`, `sp`.`name`
			FROM `".TBL_SMS_PAYMENT."` AS `p`
			LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`p`.`profile_id`=`pr`.`profile_id`)
			LEFT JOIN `".TBL_SMS_SERVICE."` AS `s` ON(`s`.`service_key`=`p`.`service_key`)
			LEFT JOIN `".TBL_SMS_PROVIDER."` AS `sp` ON(`sp`.`sms_provider_id`=`p`.`sms_provider_id`)
			WHERE `p`.`order_number`='$order' AND `p`.`verified`=1 
			ORDER BY `p`.`timestamp` DESC $limit_q";
		
		$res = SK_MySQL::query($query);
		
		$fin_info = array();
		$total = 0;
		while ($row = $res->fetch_assoc())
		{
			$total += $row['cost'];
			$fin_info['list'][] = $row;
		}
		$fin_info['ord_num'] = $number;
		$fin_info['total'] = $total;
		
		$query = "SELECT COUNT(`p`.`payment_id`)
			FROM `".TBL_SMS_PAYMENT."` AS `p`
			LEFT JOIN `".TBL_PROFILE."` AS `pr` ON(`p`.`profile_id`=`pr`.`profile_id`)
			LEFT JOIN `".TBL_SMS_SERVICE."` AS `s` ON(`s`.`service_key`=`p`.`service_key`)
			LEFT JOIN `".TBL_SMS_PROVIDER."` AS `sp` ON(`sp`.`sms_provider_id`=`p`.`sms_provider_id`)
			WHERE `p`.`order_number`='$order' AND `p`.`verified`=1";

		$fin_info['total_num'] = SK_MySQL::query($query)->fetch_cell();
		
		return $fin_info;
	}
	
}

