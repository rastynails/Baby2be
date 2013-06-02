<?php

class app_Referral
{
	public static function trackReferralsPurchase( $referral_id, $sale_id )
	{					
		$query = SK_MySQL::placeholder("
			SELECT `rl`.`referrer_id`
			FROM `".TBL_REFERRAL_RELATION."`  AS `rl`
			WHERE `rl`.`referral_id`=?
			", $referral_id);
		$referrer_id = SK_MySQL::query($query)->fetch_cell();
		
		if ( $referrer_id )
		{
			$query = 'UPDATE `'.TBL_REFERRAL_RELATION."` SET `paid`='y' WHERE `referral_id`=$referral_id AND `paid`='n'";
			SK_MySQL::query($query);
			
			if ( SK_MySQL::affected_rows() )
			{		
				self::increasePurchaseReferralsField($referrer_id);
			}
			self::increaseTransactionsField($referrer_id);

			$comissionType = SK_Config::section('referrals')->comission_type;

			$earning = 0;
			switch ( $comissionType )
			{
			    case 'amount':
			        $earning = floatval(SK_Config::section('referrals')->subscription_amount);
			        break;
			        
			    case 'percent':
                    $query = 'SELECT `amount` FROM`'.TBL_FIN_SALE."` WHERE `fin_sale_id`=$sale_id";
                    $amount = SK_MySQL::query($query)->fetch_cell();

                    $percent = floatval(SK_Config::section('referrals')->subscription_percent);
                    $earning = round($amount * $percent / 100, 2);
			        break;
			}
			
			$query = SK_MySQL::placeholder("UPDATE `".TBL_REFERRAL."` SET `earn`=`earn`+'?' WHERE `referral_id`=?", $earning, $referrer_id);
			SK_MySQL::query($query);
		}
	}
	
	public static function trackReferralAdd( $referrer_id )
	{
	    $earning = floatval(SK_Config::section('referrals')->registration_amount);
	    
	    if ( $earning != 0 )
	    {
            $query = SK_MySQL::placeholder("UPDATE `".TBL_REFERRAL."` SET `earn`=`earn`+'?' WHERE `referral_id`=?", $earning, $referrer_id);
            SK_MySQL::query($query);
	    }
	}
	
	public static function getAccount($whole_order, $whole_sort_order , $order_by, $sort_order, $page, $num_per_page, $stamps=null, $referral_id=null)
	{
		switch ($order_by)
		{
			case 'total_invites':
			case 'referral_id':
			case 'total_referrals':
			case 'total_purchaser_referrals':
			case 'total_transactions':
			case 'username':
			case 'email':				
				break;
			default:
				$order_by = 'referral_id';
				break;				
		}
		
		if($sort_order != 'DESC')
			$sort_order = 'ASC';
		$_between = ( $stamps == array() )
		?
			''
		:
			( 
				($stamps[0] && $stamps[1])
				?
					"AND `p`.`join_stamp` BETWEEN {$stamps[0]} AND {$stamps[1]}"
				:
					''
			);

		$_inner_join = (intval($referral_id))
		?
			sql_placeholder('INNER JOIN `?#TBL_REFERRAL_RELATION` AS `rl` ON(`ref`.`referral_id`=`rl`.`referral_id` AND `rl`.`referrer_id`=?)',$referral_id)
		:
			''
		;
		
		$lb = ($page-1)*$num_per_page;
		$_limit = "LIMIT $lb, $num_per_page";
		$_query = sql_placeholder("SELECT * FROM(
		SELECT
			`ref`.`referral_id`,
			`ref`.`total_invites`,
			`ref`.`total_referrals`,
			`ref`.`earn`,
			`ref`.`total_purchaser_referrals`,
			`ref`.`total_transactions`,
			`p`.`username`,
			`p`.`email`
		
		FROM `?#TBL_REFERRAL` AS `ref`
		$_inner_join
		INNER JOIN `?#TBL_PROFILE` AS `p`
		      ON (`ref`.`referral_id` = `p`.`profile_id` $_between)
		/*ORDER BY `ref`.`$whole_order` $whole_sort_order*/
		/*$_limit*/	) AS `q` ORDER BY `$order_by` $sort_order $_limit	
		");
		$_referrals = MySQL::fetchArray($_query, 'referral_id');
		
		$_query = sql_placeholder("
		SELECT
			COUNT(*)
		FROM `?#TBL_REFERRAL` AS `ref`
		$_inner_join
		INNER JOIN `?#TBL_PROFILE` AS `p`
		      ON (`ref`.`referral_id` = `p`.`profile_id` $_between)	
		");
		$_total = MySQL::fetchField($_query);
		return array( 'referrals'=>$_referrals, 'total'=>$_total);	
	}
	
	public static function increasePurchaseReferralsField($referrer_id)
	{
		$referrer_id = intval($referrer_id);
		
		$query = SK_MySQL::placeholder('UPDATE `'.TBL_REFERRAL.'` SET `total_purchaser_referrals`=`total_purchaser_referrals`+1 WHERE `referral_id`=?', $referrer_id );
		SK_MySQL::query($query);
	}
	
	public static function increaseTransactionsField($referrer_id)
	{
		$referrer_id = intval($referrer_id);
		$query = SK_MySQL::placeholder('UPDATE `'.TBL_REFERRAL.'` SET `total_transactions`=`total_transactions`+1 WHERE `referral_id`=?', $referrer_id );
		SK_MySQL::query($query);
	}
	
	public static function getPersonalReferralInfo($referrer_id, $lb = null, $ub = null, $start_stamp = null, $end_stamp= null)
	{
		$between = ($start_stamp && $end_stamp)
		?
			"AND `p`.`join_stamp` BETWEEN $start_stamp AND $end_stamp"
		:
		'';
		$limit = ( ($lb && $ub) || (!intval($lb) && $ub) )
		?
		"LIMIT $lb, $ub "
		:
		'';

			$_query = sql_placeholder("
				SELECT 
					`rl`.`date_reffered` AS `date_referred`,
					`rl`.`paid` AS `paying_membership`,
					`p`.`join_stamp` AS `date_registered`,
					`p`.`email`,
					`p`.`username`
				FROM `?#TBL_REFERRAL_RELATION` AS `rl`
					INNER JOIN `?#TBL_REFERRAL` AS `ref`
						ON(`rl`.`referral_id` = `ref`.`referral_id`)
					INNER JOIN `?#TBL_PROFILE` AS `p`
						ON(`rl`.`referral_id`=`p`.`profile_id` $between)
				WHERE `rl`.`referrer_id` = ?
				$limit			
			", $referrer_id);		
		return MySQL::fetchArray($_query);
	}
	
	public static function getPersonalReferralsNum($referrer_id, $start_stamp=null, $end_stamp=null)
	{
		if($start_stamp && $end_stamp)
			$between = "AND `pr`.`join_stamp` BETWEEN $start_stamp AND $end_stamp";
		else 
			$between = "";

		$_query = sql_placeholder("
		SELECT
			COUNT(`referrer_id`) 
		FROM `?#TBL_REFERRAL_RELATION` AS `rl`
		 WHERE `referrer_id`=?
		", $referrer_id);
		return MySQL::fetchField($_query);
	}
	
	public static function GetTransactionsByDate( $date_from, $date_to, $page, $on_page, $include_arr, $referrer_id = null)
	{
		
		$inner_join_referral_relation = ( intval($referrer_id) )?
			"INNER JOIN `".TBL_REFERRAL_RELATION."` AS `rl` ON( `s`.`profile_id`=`rl`.`referral_id` AND `rl`.`referrer_id`=$referrer_id )"
		:
			''
		;
		
		$inner_join_referral = ( !$inner_join_referral_relation ) 
		?
			"INNER JOIN `".TBL_REFERRAL."` AS `ref` ON( `s`.`profile_id`=`ref`.`referral_id` )"
		:
		'';
		
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
			$inner_join_referral_relation
			$inner_join_referral		 
			INNER JOIN `".TBL_PROFILE."` as `p` ON(`s`.`profile_id` = `p`.`profile_id`)
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

	public static function GetTransactionsByOrder( $order, $payment_provider_id, $referrer_id = null )
	{
		$inner_join_referral_relation = ( intval($referrer_id) )?
			"INNER JOIN `".TBL_REFERRAL_RELATION."` AS `rl` ON( `s`.`profile_id`=`rl`.`referral_id` AND `rl`.`referrer_id`=$referrer_id )"
		:
			''
		;
		
		$inner_join_referral = ( !$inner_join_referral_relation ) 
		?
			"INNER JOIN `".TBL_REFERRAL."` AS `ref` ON( `s`.`profile_id`=`ref`.`referral_id` )"
		:
		'';
				
		$payment_provider_id = intval( $payment_provider_id );
		if ( !$payment_provider_id )
			return array();
		
		$_query = "SELECT `s`.`fin_sale_id`,`s`.`payment_provider_order_number`,`s`.`order_stamp`,`s`.`amount`,`s`.`status`,
			`s`.`fin_payment_provider_id`,`p`.`username`,`p`.`profile_id`,`mt`.`membership_type_id`,`r`.`amount_fine` 
			FROM `".TBL_FIN_SALE."` AS `s`
			$inner_join_referral_relation
			$inner_join_referral		
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
	
	public static function GetTransactionsByProfile( $profile, $page, $on_page, $include_arr, $referrer_id )
	{
		$inner_join_referral_relation = ( intval($referrer_id) )?
			"INNER JOIN `".TBL_REFERRAL_RELATION."` AS `rl` ON( `s`.`profile_id`=`rl`.`referral_id` AND `rl`.`referrer_id`=$referrer_id )"
		:
			''
		;
		
		$inner_join_referral = ( !$inner_join_referral_relation ) 
		?
			"INNER JOIN `".TBL_REFERRAL."` AS `ref` ON( `s`.`profile_id`=`ref`.`referral_id` )"
		:
		'';
				
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
			$inner_join_referral_relation
			$inner_join_referral			
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
	
	public static function getAllReferralTransactionsInfo($lb=null, $ub=null, $start_stamp=null, $end_stamp=null, $with_refund = false, $referrer_id = null)
	{
		if($referrer_id)
		{
			$subquery[0] = '`rl`.`referral_id`';
			$subquery[1] = 'INNER JOIN `'.TBL_REFERRAL_RELATION."` AS `rl`
     ON (`rl`.`referral_id` = `s`.`profile_id` AND `rl`.`referrer_id` = $referrer_id)";
		}
		else 
		{
			$subquery[0] = '`ref`.`referral_id`';
			$subquery[1] = 'INNER JOIN `'.TBL_REFERRAL."` AS `ref`
     ON (`ref`.`referral_id` = `s`.`profile_id`)";
		}
		$between = ($start_stamp && $end_stamp)
		?
			"AND `s`.`order_stamp` BETWEEN $start_stamp AND $end_stamp"
		:
			'';
		$limit = ( ($lb && $ub) || (!intval($lb) && $ub) )
		?
		"LIMIT $lb, $ub "
		:
		'';
		$refund = ($with_refund)
		?
			'AND `r`.`refund_stamp`IS NULL'
		:
			'';
		$_query = "SELECT 
			`s`.`fin_payment_provider_id`, `s`.`payment_provider_order_number`, `s`.`order_stamp`,`s`.`unit`,
      		`s`.`period`,`s`.`amount`,
      		`s`.`status`,`mt`.`type`,`mt`.`limit`,
      		`r`.`amount_fine` AS `refund_fine`,`r`.`refund_stamp`,
      		`r`.`comment` AS `refund_comment`,
      		{$subquery[0]}
      		FROM `".TBL_FIN_SALE."` AS `s`
			{$subquery[1]}
      		LEFT JOIN `".TBL_MEMBERSHIP_TYPE.'` AS `mt` ON (`mt`.`membership_type_id` = `s`.`membership_type_id`)
      		LEFT JOIN `'.TBL_FIN_REFUND."` AS `r` ON(`s`.`fin_sale_id`=`r`.`fin_sale_id`)
      		WHERE
      		`status`='approval'
     		AND
     		`s`.`fin_payment_provider_id`!=0 $refund $between
			ORDER BY `order_stamp` $limit";
		$transactions_info['transactions'] = MySQL::fetchArray($_query);
		$_query = "SELECT COUNT(*) FROM ($_query) AS `q`";
		$transactions_info['transactions_count'] = MySQL::fetchField($_query);		     		
		return $transactions_info;
	}

	public static function getAllReferralTransactionsCount( $start_stamp=null, $end_stamp=null, $with_refund = false, $referrer_id = null)
	{
		if($referrer_id)
		{
			$subquery[0] = '`rl`.`referral_id`';
			$subquery[1] = 'INNER JOIN `'.TBL_REFERRAL_RELATION."` AS `rl`
     ON (`rl`.`referral_id` = `s`.`profile_id` AND `rl`.`referrer_id` = $referrer_id)";
		}
		else 
		{
			$subquery[0] = '`ref`.`referral_id`';
			$subquery[1] = 'INNER JOIN `'.TBL_REFERRAL."` AS `ref`
     ON (`ref`.`referral_id` = `s`.`profile_id`)";
		}
		$between = ($start_stamp && $end_stamp)
		?
			"AND `s`.`order_stamp` BETWEEN $start_stamp AND $end_stamp"
		:
			'';
		$limit = ( ($lb && $ub) || (!intval($lb) && $ub) )
		?
		"LIMIT $lb, $ub "
		:
		'';
		$refund = ($with_refund)
		?
			'AND `r`.`refund_stamp`IS NULL'
		:
			'';
		$_query = "SELECT 
			`s`.`fin_payment_provider_id`, `s`.`payment_provider_order_number`, `s`.`order_stamp`,`s`.`unit`,
      		`s`.`period`,`s`.`amount`,
      		`s`.`status`,`mt`.`type`,`mt`.`limit`,
      		`r`.`amount_fine` AS `refund_fine`,`r`.`refund_stamp`,
      		`r`.`comment` AS `refund_comment`,
      		{$subquery[0]}
      		FROM `".TBL_FIN_SALE."` AS `s`
			{$subquery[1]}
      		LEFT JOIN `".TBL_MEMBERSHIP_TYPE.'` AS `mt` ON (`mt`.`membership_type_id` = `s`.`membership_type_id`)
      		LEFT JOIN `'.TBL_FIN_REFUND."` AS `r` ON(`s`.`fin_sale_id`=`r`.`fin_sale_id`)
      		WHERE
      		`status`='approval'
     		AND
     		`s`.`fin_payment_provider_id`!=0 $refund $between
			ORDER BY `order_stamp` $limit";
		$_query = "SELECT COUNT(*) FROM ($_query) AS `q`";
		$transactions_count = MySQL::fetchField($_query);		     		
		return $transactions_count;
	}
	
	public static function getAllReferralTransactionsTotalAmount($lb=null, $ub=null, $start_stamp=null, $end_stamp=null, $with_refund = false, $referrer_id = null)
	{
		$subquery = ($referrer_id)
		?
			'INNER JOIN `'.TBL_REFERRAL_RELATION."` AS `rl`
     ON (`rl`.`referral_id` = `s`.`profile_id` AND `rl`.`referrer_id` = $referrer_id)"
		:
			'INNER JOIN `'.TBL_REFERRAL."` AS `ref`
     ON (`ref`.`referral_id` = `s`.`profile_id`)";	

		$between = ($start_stamp && $end_stamp)
		?
			"AND `s`.`order_stamp` BETWEEN $start_stamp AND $end_stamp"
		:
			'';
		$limit = ( ($lb && $ub) || (!intval($lb) && $ub) )
		?
		"LIMIT $lb, $ub "
		:
		'';
		$refund = ($with_refund)
		?
			''
		:
			'AND `r`.`refund_stamp`IS NULL';
					
		$_query = 'SELECT 
			SUM(`s`.`amount`)
      		FROM `'.TBL_FIN_SALE."` AS `s`
      		$subquery
      		LEFT JOIN `".TBL_MEMBERSHIP_TYPE.'` AS `mt` ON (`mt`.`membership_type_id` = `s`.`membership_type_id`)
      		LEFT JOIN `'.TBL_FIN_REFUND."` AS `r` ON(`s`.`fin_sale_id`=`r`.`fin_sale_id`)
      		WHERE
      		`status`='approval'
     		AND
     		`s`.`fin_payment_provider_id`!=0 $refund $between
			ORDER BY `order_stamp` $limit";
		return MySQL::fetchField($_query);
	}
	
	public static function getReferralInfo($referral_id, $fields=null)
	{
		if( $count = count($fields) )
		{
			if($count == 1)
				return MySQL::fetchField("SELECT `{$fields[0]}` FROM `".TBL_REFERRAL."` WHERE `referral_id`=$referral_id");
			else
			{
				foreach ($fields as $field)	
					$fetch .= "`$field`,";
				$fetch = substr( $fetch, 0, (strlen($fetch)-1) );
				return MySQL::fetchRow("SELECT $fetch FROM `".TBL_REFERRAL."` WHERE `referral_id`=$referral_id");
			}
		}
		else 
			return MySQL::fetchRow('SELECT * FROM `'.TBL_REFERRAL."` WHERE `referral_id`=$referral_id");		
	}
	
		/**
	 * Returns checked affiliate fields.
	 *
	 * @param array $affiliate_arr
	 * @return array
	 */
	public static function checkReferralFields( $referral_arr )
	{
		
		if ( !is_array( $referral_arr ) )
			return array();
		
		foreach ($referral_arr as $_key=>$_value)
		{
			switch ( $_key )
			{
				case 'mode_id':
					$_result_arr[$_key] = $_value;
					break;
				case 'im':
				case 'phone':
				case 'address':
				case 'payment_details':
					$_result_arr[$_key] = $_value;
					break;
			}
		}
		return $_result_arr;
	}

	/**
	 * Updates referral info.
	 *
	 * @param integer $referral_id
	 * @param array $info
	 * @return boolean|integer
	 */
	public static function updateReferralInfo( $referral_id, $info )
	{
		$referral_id = intval( $referral_id );
		if ( !$referral_id || !is_array( $info ) || !$info)
			return false;
		
		$_colums = array( 'phone', 'address', 'im', 'payment_details' );
		foreach ( $info	 as $_key=>$_value )
		{
			if ( !$_key )
				continue;
			
			if ( in_array( $_key, $_colums ) )
				$_query[$_key] = $_value;
		}
		
		if ( !$_query )
			return false;
			
		return MySQL::affectedRows( sql_placeholder( "UPDATE `?#TBL_REFERRAL` SET ?% WHERE `referral_id`=?", $_query, $referral_id ) );
	}

	public static function getTotalPurchaserReferrals($referrer_id=null)
	{
		if( $referrer_id && intval($referrer_id) )
			$referrer_id_equal = " AND `referrer_id`=$referrer_id";
		$_query = 'SELECT COUNT(`paid`) FROM `'.TBL_REFERRAL_RELATION."` WHERE `paid`='y' $referrer_id_equal";
		return MySQL::fetchField($_query);
	}
	
	public static function getReferrerId($referral_id){
			$_query = sql_placeholder('SELECT `referrer_id` FROM `?#TBL_REFERRAL_RELATION` WHERE `referral_id`=?', $referral_id);
		return MySQL::fetchField($_query);
	}
	
	public static function getReferralAdminPaymentList($referral_id, $start_stamp=null, $end_stamp=null)
	{
		if( !intval($referral_id))
			return -1;
		$between = ( $start_stamp && $end_stamp)
		?
			"AND `time_stamp` BETWEEN $start_stamp and $end_stamp"
		:
			(!$start_stamp && $end_stamp)
			?
				"AND `time_stamp` < $end_stamp"
			:
				'';
		$_query = 'SELECT * FROM `'.TBL_REFERRAL_PAYMENT."` WHERE `referral_id` = $referral_id $between";
		return MySQL::fetchArray($_query);  			
	}	

	public static function updateReferralPaymentInfo($referral_id, $payment_id, $amount )
	{
		$referral_id = intval($referral_id);
		$payment_id = intval($payment_id);
		
		if( !$referral_id || !$payment_id || !is_numeric($amount) )
			return -1;
			
			$_query = 'SELECT `payment_id` FROM `'.TBL_REFERRAL_PAYMENT."` WHERE `referral_id`=$referral_id ORDER BY `time_stamp` DESC";
		$_last_payment_id = MySQL::fetchField($_query);
		if($payment_id != $_last_payment_id)
			return -2;
		$_query = 'UPDATE `'.TBL_REFERRAL_PAYMENT."` SET `amount`=$amount,`edited_time_stamp`=".time()." WHERE `referral_id`=$referral_id AND `payment_id`=$payment_id";
		return MySQL::affectedRows( $_query );
	}

	public static function addReferralPayment($referral_id, $amount)
	{
		$referral_id = intval($referral_id);
		$amount = intval($amount);
		if( !$referral_id || !$amount)
			return -1;
			
		$_query = 'INSERT INTO `'.TBL_REFERRAL_PAYMENT."` (`referral_id`, `amount`, `time_stamp`,`edited_time_stamp`) VALUES($referral_id, $amount, ".time().', 0)';
		return MySQL::fetchResource($_query);
	}	

	public static function getReferralPaymentInfo($referral_id, $payment_id)
	{
		$referral_id = (int)$referral_id;
		$payment_id = (int)$payment_id;

		if(!$referral_id || !$payment_id )	
			return array();
		$_query = 'SELECT * FROM `'.TBL_REFERRAL_PAYMENT."` WHERE `referral_id`=$referral_id AND `payment_id`=$payment_id";
		return MySQL::fetchRow($_query);
	}	
}
?>