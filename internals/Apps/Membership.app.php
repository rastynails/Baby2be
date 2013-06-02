<?php

/**
 * Class for working with memberships
 *
 * @package SkaDate
 * @link http://www.skadate.com
 * @version 7.0
 */
class app_Membership
{
	/**
	 * Returns membership types available
	 * for different sex
	 *
	 * @param integer $sex
	 * @return array
	 */
	public static function getAvailableMembershipTypes( $sex )
	{
		$sex = intval( $sex );
		if ( !$sex )
			return array();
		
		$query = "SELECT `membership_type_id`,`type`,`limit` FROM `".TBL_MEMBERSHIP_TYPE."` 
			WHERE `available_for` & $sex AND `paid_by_sms`=0 ORDER BY `order`";
		
		return MySQL::FetchArray( $query );
	}
	
	/**
	 * Returns membership type info:
	 * id, type and limit.
	 *
	 * @param integer $membership_type_id
	 * @return array
	 */
	public static function getMembershipTypeInfo( $membership_type_id )
	{
		if ( !is_numeric( $membership_type_id ) || !intval( $membership_type_id ) )
			return array();
		
		$query = "SELECT * FROM `".TBL_MEMBERSHIP_TYPE."` WHERE `membership_type_id`='$membership_type_id'";

		return MySQL::fetchRow( $query );
	}

	/**
	 * Returns membership type info by plan_id:
	 * id, type and limit.
	 *
	 * @param integer $plan_id
	 * @return array
	 */
	public static function getMembershipTypeInfoByPlanId( $plan_id )
	{
		if ( !is_numeric( $plan_id ) || !intval( $plan_id ) )
			return array();
		
		$query = "SELECT * FROM `".TBL_MEMBERSHIP_TYPE."` 
			WHERE `membership_type_id`=(SELECT `membership_type_id` FROM `".TBL_MEMBERSHIP_TYPE_PLAN."` 
											WHERE `membership_type_plan_id`='$plan_id')";

		return MySQL::fetchRow( $query );
	}
	
	/**
	 * Returns list of services available for the membership type.
	 *
	 * @param integer $membership_type_id
	 * @return array
	 */
	public static function getMembershipTypeServices( $membership_type_id )
	{
		if ( !is_numeric( $membership_type_id ) || !intval( $membership_type_id ) )
			return array();
		
		$query = "SELECT `s`.`membership_service_key`,`s`.`credits`
			FROM `".TBL_MEMBERSHIP_SERVICE."` AS `s` 
			INNER JOIN `".TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE."` AS `ms` USING(`membership_service_key`) 
			INNER JOIN `".TBL_FEATURE."` AS `f` ON(`f`.`feature_id`=`s`.`feature_id` AND `f`.`active`='yes')
			WHERE `ms`.`membership_type_id`='$membership_type_id'";
		
		return MySQL::FetchArray( $query );
	}
	
	/**
	 * Returns a list of the membership type plans 
	 * including id, price, period, unit
	 *
	 * @param integer $membership_type_id
	 * @return array
	 */
	public static function getMembershipTypePlan ( $membership_type_id )
	{
		if ( !is_numeric( $membership_type_id ) || !intval( $membership_type_id ) )
			return array();
		
		$query = "SELECT * FROM `".TBL_MEMBERSHIP_TYPE_PLAN."` 
			WHERE `membership_type_id`='$membership_type_id' ORDER BY `membership_type_plan_id`";
	
		return MySQL::FetchArray( $query );
	}
	
	/**
	 * Returns membership type plan info: period, units.
	 * 
	 * @param integer $membership_type_plan_id
	 * @return array
	 */
	public static function getMembershipTypePlanInfo( $membership_type_plan_id )
	{
		if ( !is_numeric( $membership_type_plan_id ) || !intval( $membership_type_plan_id ) )
			return array();
		
		$query = "SELECT * FROM `".TBL_MEMBERSHIP_TYPE_PLAN."` WHERE 
			`membership_type_plan_id`='$membership_type_plan_id'";
		
		return MySQL::fetchRow( $query );
	}
	
	/**
	* Checks subscription memberships and deletes membershhips with expired period.
	* Returns count of the deleted memberships. 
	*
	* @param integer $profile_id
	* @return integer|boolean
	*/
	public static function checkSubscriptionTrialMembership( $profile_id = 0 )
	{
		$i = 0;
		if ( $profile_id )
			$profile_q = " `s`.`profile_id`='$profile_id' AND";
		else
			$profile_q = "";

		// we'll give the membership 3 hour chance to expire:
		$exp_time = time() - 3 * 3600;
			
		// select old memberships:
        $query = "SELECT `m`.`membership_id`, `s`.`profile_id`, `m`.`prev_membership_type_id`,`m`.`membership_type_id`
            FROM `".TBL_MEMBERSHIP_TYPE."` AS `mt` 
            INNER JOIN `".TBL_MEMBERSHIP."` AS `m` USING(`membership_type_id`) 
            INNER JOIN `".TBL_FIN_SALE."` AS `s` ON(`m`.`fin_sale_id` = `s`.`fin_sale_id`) 
            WHERE".$profile_q." 
            (
                ( `s`.`fin_payment_provider_id` NOT IN ('4','5') AND `m`.`expiration_stamp` < ".time()." )
                OR
                ( `s`.`fin_payment_provider_id` IN ('4','5') AND `m`.`expiration_stamp` < ".$exp_time." )
            )
            AND `mt`.`type`!='credits' 
            AND `mt`.`limit`='limited' ORDER BY `m`.`expiration_stamp`";
		$memberships_arr = MySQL::FetchArray( $query );
		
		foreach ( $memberships_arr as $membership_arr )
		{
			$query = "SELECT `m`.`membership_id`, `m`.`membership_type_id` 
				FROM `".TBL_MEMBERSHIP."` AS `m` 
				INNER JOIN `".TBL_FIN_SALE."` AS `s` USING(`fin_sale_id`) 
				WHERE `s`.`profile_id`='{$membership_arr['profile_id']}' 
				AND `m`.`membership_id` != '{$membership_arr['membership_id']}' ORDER BY `m`.`expiration_stamp` LIMIT 1";
			$membership2_arr = MySQL::fetchRow( $query );
			
			$prev_mship_type_id = MySQL::fetchField( "SELECT `prev_membership_type_id` FROM `".TBL_MEMBERSHIP."` 
				WHERE `membership_id`='{$membership_arr['membership_id']}'" );
			
			if ($prev_mship_type_id)
			{
				if ( !$membership2_arr )
				{
					MySQL::fetchResource( "UPDATE `".TBL_PROFILE."` SET `membership_type_id`='$prev_mship_type_id' 
						WHERE `profile_id`='{$membership_arr['profile_id']}'" );
				}
				else
				{
					// check next membership type
					$next_m_type = MySQL::fetchField( "SELECT `type` FROM `".TBL_MEMBERSHIP_TYPE."` 
						WHERE `membership_type_id`='{$membership2_arr['membership_type_id']}'" );
	
					MySQL::fetchResource( "UPDATE `".TBL_MEMBERSHIP."` SET `prev_membership_type_id`='$prev_mship_type_id' 
						WHERE `membership_id`='{$membership2_arr['membership_id']}'" );
					
					MySQL::fetchResource( "UPDATE `".TBL_PROFILE."` SET `membership_type_id`='{$membership2_arr['membership_type_id']}' 
						WHERE `profile_id`='{$membership_arr['profile_id']}'" );				
				}
			}
			
			// delete membership
			MySQL::fetchResource( "DELETE FROM `".TBL_MEMBERSHIP."` WHERE `membership_id`='{$membership_arr['membership_id']}'" );
			
			// get profile info
			$profile_info = app_Profile::getFieldValues( $membership_arr['profile_id'], array( 'language_id', 'email', 'username' ) );
			
			// send notify mail
			$msg = app_Mail::createMessage()
					->setRecipientProfileId($membership_arr['profile_id'])
					->setTpl('membership_expired')
					->assignVar('membership_type', SK_Language::section('membership.types')->text($membership_arr['membership_type_id']));
			app_Mail::send($msg);
						
			$i++;
		}
		
		return $i;
	}
	
	public static function sendExpirationNotifications( )
	{
	    $notify = (bool)SK_Config::section('membership.expiration')->get('notify_before');
	    
	    if ( !$notify )
	    {
            return true;
	    }
	    
	    $days = (int)SK_Config::section('membership.expiration')->get('notify_before_days');
	    $period = $days * 24 * 60 * 60;
	    	    
        $query = SK_MySQL::placeholder("SELECT `m`.*, `s`.`profile_id` FROM `".TBL_MEMBERSHIP."` AS `m`
            LEFT JOIN `".TBL_FIN_SALE."` AS `s` ON (`m`.`fin_sale_id`=`s`.`fin_sale_id`)
            WHERE `m`.`notified`=0 
            AND `m`.`expiration_stamp` - `m`.`start_stamp` > ?
            AND `m`.`expiration_stamp` < ?", $period, time() + $period);

        $result = SK_MySQL::query($query);

        $idListToUpdate = array();

        while ( $row = $result->fetch_assoc() )
        {
            if ( $row['profile_id'] )
            {
                $lang_id = (int)app_Profile::getFieldValues($row['profile_id'], 'language_id');
                
                if ( !$lang_id )
                    continue;
                    
                $ins  = SK_Language::instance($lang_id);
                $sect = SK_Language::section('membership.types', $ins);
                
                $msg = app_Mail::createMessage(app_Mail::CRON_MESSAGE)
                    ->setRecipientProfileId($row['profile_id'])
                    ->setTpl('membership_expiration_notification')
                    ->setRecipientLangId($lang_id)
                    ->assignVarRange(array(
                        'days' => $days,
                        'membership_name' => $sect->text($row['membership_type_id'])
                    ));
                
                if ( app_Mail::send($msg) )
                {
                    $idListToUpdate[] = $row['membership_id'];
                }       
            }
        }

        if( !empty ($idListToUpdate) )
        {
            SK_MySQL::query(SK_MySQL::placeholder("UPDATE `".TBL_MEMBERSHIP."` SET `notified`=1 WHERE `membership_id` IN (?@)", $idListToUpdate));
        }
        
        return true;
	}

	/**
	* Registers profile trial membership claiming
	*
	* @param integer $profile_id
	* @param integer $plan_id
	*
	* @return boolean
	*/
	public static function claimTrialMembership( $profile_id, $plan_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $plan_id ) || !is_numeric( $plan_id ) || !intval( $plan_id ) )
			return false;
		
		$plan_info = MySQL::fetchRow( "SELECT `membership_type_id` FROM `".TBL_MEMBERSHIP_TYPE_PLAN."` WHERE 
			`membership_type_plan_id`='$plan_id'" );
		if ( $plan_info )
			MySQL::fetchResource( "INSERT INTO `".TBL_MEMBERSHIP_CLAIM."` 
				(`profile_id`,`membership_type_id`,`claim_stamp`,`claim_result`) 
				VALUES('$profile_id','{$plan_info['membership_type_id']}','".time()."','claim')" );
		else
			return false;
		
		return true;
	}
	
	/**
	* Checks if profile claimed or bought the TRIAL membership type. 
	* If isset returns result of his claiming.
	*
	* Returns:
	* <ul>
	* <li> 1 - profile has never claimed this membership type OR profile has claimed and admin refused</li>
	* <li>-1 - incorrect parameters</li>
	* <li>-2 - profile claimed and it is in the admin's consideration</li>
	* <li>-3 - profile claimed and admin granted OR profile bought and used before</li>
	* </ul>
	* 
	* @param integer $profile_id 
	* @param integer $id 			| identificator of the membership type or plan 
	* @param string $id_type		| if $id_type eq 'plan' then $id is a membership type plan
	* 								| if $id_type is any string but 'plan' then $id is a membership type
	* @return integer 
	*/
	public static function checkIfProfileClaimedTheMembership( $profile_id, $id, $id_type )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) || !is_numeric( $id ) || !intval( $id ) )
			return -1;
		
		if ( $id_type == 'plan' )  // membership type's plan id was passed
		{
			$membership_type_id = MySQL::fetchField( "SELECT `membership_type_id` FROM `".TBL_MEMBERSHIP_TYPE_PLAN."` 
				WHERE `membership_type_plan_id`='$id'" );
			if ( !$membership_type_id )
				return -1;
		}
		else  // membership type id was passed
		{
			$membership_type_id = $id;
		}
		
		$result = MySQL::fetchField( "SELECT `claim_result` FROM `".TBL_MEMBERSHIP_CLAIM."` 
				WHERE `profile_id`='$profile_id' AND `membership_type_id`='$membership_type_id'" );
		if ( $result == 'claim' )
			return -2;
		
		$used = MySQL::fetchField( "SELECT `fin_sale_id` FROM `".TBL_FIN_SALE."` 
				WHERE `profile_id`='$profile_id' AND `membership_type_id`='$membership_type_id' 
				AND `status`='approval' AND `payment_provider_order_number`='trial' LIMIT 1" );
		
		if ( $result == 'granted' || $used )
			return -3;
		
		return 1;
	}
	
	/**
	* Returns profile all memberships info: 
	* type, limit, membership_type_id, start and expiration stamp.
	*
	* @param integer $profile_id
	* @return array
	*/
	public static function getProfileAllMemberships( $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return array();
		
		$is_exist_membership = MySQL::fetchRow( "SELECT `m`.* FROM `".TBL_FIN_SALE."`  AS `fs`
				INNER JOIN `".TBL_MEMBERSHIP."` AS `m` USING( `fin_sale_id` ) WHERE `fs`.`profile_id`='$profile_id'" );

		// get default membership type id
		$def_membership_type_id = SK_Config::section('membership')->default_membership_type_id;
		
		if ( $is_exist_membership )
		{
			$query = "SELECT `mt`.`type`,`mt`.`limit`,`mt`.`membership_type_id`,`m`.`membership_id`,
				`m`.`prev_membership_type_id`,`m`.`start_stamp`, `m`.`expiration_stamp`,
				IF(`mt`.`membership_type_id`='$def_membership_type_id','yes','no') AS `is_default`
				FROM `".TBL_PROFILE."` AS `p`
				INNER JOIN `".TBL_FIN_SALE."` AS `fs` USING(`profile_id`)
				INNER JOIN `".TBL_MEMBERSHIP."` AS `m` ON(`fs`.`fin_sale_id`=`m`.`fin_sale_id`)
				INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` ON(`m`.`membership_type_id`=`mt`.`membership_type_id`)
				WHERE `p`.`profile_id`='$profile_id' ORDER BY `m`.`start_stamp`";
			
			$memberships = MySQL::fetchArray( $query );
			$memberships[] = MySQL::fetchRow( "SELECT * FROM `".TBL_MEMBERSHIP_TYPE."` WHERE 
				`membership_type_id`='{$memberships[0]['prev_membership_type_id']}'" );

			return $memberships;
		}
		else
		{
			return ( MySQL::fetchArray( "SELECT `mt`.*,IF(`mt`.`membership_type_id`='$def_membership_type_id','yes','no') AS `is_default`
				FROM `".TBL_PROFILE."` AS `p` INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` 
				USING( `membership_type_id` ) WHERE `p`.`profile_id`='$profile_id'" ) );
		}
	}
	
	/**
	* Returns profile current membership info: 
	* type, limit, membership_type_id, start and expiration stamp.
	*
	* @param integer $profile_id
	* @return array
	*/
	public static function profileCurrentMembershipInfo( $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return array();
		
		$query = "SELECT `m`.`membership_id`,`m`.`fin_sale_id`,`mt`.`type`,`mt`.`limit`,`mt`.`membership_type_id`,
			`m`.`start_stamp`,`m`.`expiration_stamp`,`fs`.`status`,`fs`.`is_recurring`,`fs`.`payment_provider_order_number` 
			FROM `".TBL_MEMBERSHIP_TYPE."` AS `mt` 
			INNER JOIN `".TBL_PROFILE."` AS `p` USING(`membership_type_id`) 
			LEFT JOIN `".TBL_FIN_SALE."` AS `fs` USING(`profile_id`) 
			INNER JOIN `".TBL_MEMBERSHIP."` AS `m` ON(`fs`.`fin_sale_id`=`m`.`fin_sale_id` AND `m`.`membership_type_id`=`p`.`membership_type_id`) 
			WHERE `p`.`profile_id`='$profile_id' AND `p`.`membership_type_id`=`mt`.`membership_type_id` ORDER BY `m`.`start_stamp` LIMIT 1";
		
		$res = SK_MySQL::query( $query )->fetch_assoc();
		
		if ( !$res )
			return ( SK_MySQL::query( "SELECT `mt`.* FROM `".TBL_PROFILE."` AS `p` INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` 
					USING( `membership_type_id` ) WHERE `p`.`profile_id`='$profile_id'" )->fetch_assoc() );
		else
			return $res;
	}
	
	/**
	* Returns profile membership type id 
	* Returns 1 if guest or incorrect datas passed.
	*
	* @param integer $profile_id
	* @return integer
	*/
	public static function getProfileMembershipTypeId( $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return 1;
		
		$query = SK_MySQL::placeholder( "SELECT `membership_type_id` FROM `".TBL_PROFILE."` WHERE `profile_id`=?", $profile_id);	
		$res = (int)SK_MySQL::query($query)->fetch_cell();
		
		return $res ? $res : 1;
	}

	/**
	 * Update profile memberships. Put a new subscription and trial memberships. 
	 *
	 * @param integer $profile_id
	 * @param integer $membership_type_id
	 * @param integer $period
	 * @param string $units
	 * @param integer $fin_sale_id
	 *
	 * @return integer|boolean
	 */
	public static function buySubscriptionTrialMembership( $profile_id, $membership_type_id, $period, $units, $fin_sale_id = 0 )
	{
		if ( !is_numeric( $period ) || !is_numeric( $profile_id ) || ( $units != 'months' && $units != 'days' ) || 
			!is_numeric( $membership_type_id ) || !is_numeric( $fin_sale_id ) )
			return false;
		
		// Check profile's membership:
		self::checkSubscriptionTrialMembership( $profile_id );
		
		// transfer period into seconds:
		if ( $units == 'months')
			$add = (int)$period*24*3600*30;
		else
			$add = (int)$period*24*3600;
	
		// current membership's type:
		$current_m = MySQL::fetchRow( "SELECT * FROM `".TBL_MEMBERSHIP_TYPE."` WHERE 
			`membership_type_id`=(SELECT `membership_type_id` FROM `".TBL_PROFILE."` WHERE `profile_id`='$profile_id')" );
		
		if ( $current_m['type'] == 'subscription' && $current_m['limit'] == 'unlimited' )
		{
			// It means that it is only membership, so we may start to update profile's membership:
			$start_s = time();
			$expiration_s = $start_s + $add;
			$prev_membership_type_id = $current_m['membership_type_id'];
			
			MySQL::fetchResource( "UPDATE `".TBL_PROFILE."` SET `membership_type_id`='$membership_type_id' 
				WHERE `profile_id`='$profile_id'" );

			$action = 'insert';	
		}
		else
		{
			// Get info about last membership:
			$last_membership_info = MySQL::fetchRow( "SELECT `m`.`membership_id`,`m`.`membership_type_id`,
				`m`.`prev_membership_type_id`,`m`.`start_stamp`,`m`.`expiration_stamp` 
				FROM `".TBL_MEMBERSHIP."` AS `m` 
				INNER JOIN `".TBL_FIN_SALE."` AS `s` USING(`fin_sale_id`) 
				WHERE `s`.`profile_id`='$profile_id' ORDER BY `m`.`start_stamp` DESC LIMIT 1" );
			
			if (!$last_membership_info)
				return false;
			
			$last_membership_type = MySQL::fetchField( "SELECT `type` FROM `".TBL_MEMBERSHIP_TYPE."` 
				WHERE `membership_type_id`='{$last_membership_info['membership_type_id']}'" );
			
			$action = $last_membership_info['membership_type_id'] == $membership_type_id ? 'update' : 'insert'; 
			$start_s = $last_membership_info['expiration_stamp'];
			$expiration_s = $start_s + $add;
            $prev_membership_type_id = $last_membership_info['membership_type_id'];
		}
		
		switch ($action)
		{
			case 'insert':
				
				// Insert into the table a new subs(trial) limited membership.
		        MySQL::fetchResource( "INSERT INTO `".TBL_MEMBERSHIP."` 
		            (`fin_sale_id`,`start_stamp`,`expiration_stamp`,`membership_type_id`,`prev_membership_type_id`) 
		            VALUES('$fin_sale_id','$start_s','$expiration_s','$membership_type_id','$prev_membership_type_id')" );
				
	            $return_id = mysql_insert_id(); 
				break;
				
			case 'update':
				
				$return_id = $last_membership_info['membership_id'];
	
				// Update old subs(trial) limited membership expiration timestamp.
	            MySQL::fetchResource( "UPDATE `".TBL_MEMBERSHIP."` SET `fin_sale_id` = '$fin_sale_id', `expiration_stamp` = '$expiration_s'
	                WHERE `membership_id` = '$return_id'" );
				
				break;
		}
	
		return $return_id;
	}

	/**
	 * Deletes profile memberships.
	 *
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function deleteProfileMemberships( $profile_id )
	{
		$profile_id = intval( $profile_id );
		if ( !$profile_id )
			return false;
		
		return MySQL::fetchResource( "DELETE FROM `".TBL_MEMBERSHIP."` WHERE `fin_sale_id` IN( 
			SELECT `fin_sale_id` FROM `".TBL_FIN_SALE."` WHERE `profile_id`='$profile_id')" );
	}

	/**
	 * Sends notify mail to profiles when they successfully upgrade.
	 *
	 * @param string $type
	 * @param array $profile_info<br>
	 * <li>profile_id</li>
	 * <li>email</li>
	 * <li>language_id</li>
	 * @param integer $membership_type_id
	 * @param integer $membership_id
	 * @param decimal $amount
	 * 
	 * @return boolean
	 */
	public static function sendNotifyPurchaseMembershipMail( $type, $profile_info, $membership_type_id, $membership_id = 0, $amount = 0 )
	{
		switch ( $type )
		{
			case 'subscription':
				$email_template = 'purchase_subscription_membership';
				break;
			case 'trial':
				$email_template = 'purchase_trial_membership';
				break;
				
			case 'recurring_subscription':
				$email_template = 'purchase_recurring_subscription_membership';
				break;
		}
		
		if ( !$membership_id )
			return false;
		
		// except credits purchases
		$membership_info = MySQL::fetchRow( "SELECT * FROM `".TBL_MEMBERSHIP."` WHERE `membership_id`='$membership_id'" );
		
		$lang = SK_Language::instance($profile_info['language_id']);
		
        $start_date = SK_I18n::getSpecFormattedDate($membership_info['start_stamp'], false, true);
        $end_date = SK_I18n::getSpecFormattedDate($membership_info['expiration_stamp'], false, true);
        
		// send notify mail
		$msg = app_Mail::createMessage()
				->setRecipientProfileId($profile_info['profile_id'])
				->setTpl($email_template)
				->assignVar('membership_type', SK_Language::section('membership.types', $lang)->text($membership_type_id))
				->assignVar('start_date', $start_date)
				->assignVar('end_date', $end_date);
		app_Mail::send($msg);

		return true;
	}
	
	/**
	 * Returns permission diagram for selected membership types or for all.
	 *
	 * @param array $membership_type_list: membership types' id
	 * @return array
	 */
	public static function getPermissionDiagram( $membership_type_list = array() )
	{
		if ( !is_array( $membership_type_list ) )
			return array() ;
		
		if ( !$membership_type_list )
			$membership_type_list = MySQL::fetchArray( "SELECT `membership_type_id` FROM `".TBL_MEMBERSHIP_TYPE."` 
				WHERE `membership_type_id`!=1 AND `paid_by_sms`=0 ORDER BY `order`" );
		
		$query = "SELECT `s`.`membership_service_key` FROM `".TBL_MEMBERSHIP_SERVICE."` AS `s`
			INNER JOIN `".TBL_FEATURE."` AS `f` USING(`feature_id`)
			WHERE `f`.`active`='yes' AND `s`.`show`='yes' ORDER BY `s`.`order`";
		
		$diagram['services'] = MySQL::fetchArray( $query );

		$res = array();
		foreach ( $membership_type_list as $membership_type )
		{
			$limit_q = "IF((SELECT `link`.`membership_type_id` FROM `".TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE."` AS `link` 
				WHERE `link`.`membership_service_key`=`s`.`membership_service_key` 
				AND `link`.`membership_type_id`='{$membership_type['membership_type_id']}' LIMIT 1),
				(SELECT `lim`.`limit` FROM `".TBL_MEMBERSHIP_LINK_SERVICE_LIMIT."` AS `lim`
				WHERE `lim`.`membership_service_key`=`s`.`membership_service_key` 
				AND `lim`.`membership_type_id`='{$membership_type['membership_type_id']}' LIMIT 1)
				,'0') AS `service_limit`";

			$availability_q = "IF((SELECT `link`.`membership_type_id` FROM `".TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE."` AS `link` 
				WHERE `link`.`membership_service_key`=`s`.`membership_service_key` 
				AND `link`.`membership_type_id`='{$membership_type['membership_type_id']}' LIMIT 1), 'yes','no') AS `available`";
				
			$query = "SELECT `s`.`membership_service_key`,`s`.`is_promo`, `s`.`credits`, `s`.`is_limited`, $limit_q , $availability_q
				FROM `".TBL_MEMBERSHIP_SERVICE."` AS `s`
				INNER JOIN `".TBL_FEATURE."` AS `f` USING(`feature_id`)
				WHERE `f`.`active`='yes' AND `s`.`show`='yes' ORDER BY `s`.`order`";
							
			$result = SK_MySQL::query($query);
			
			while ( $services = $result->fetch_assoc() ) 
			{
				$res[$membership_type['membership_type_id']]['services'][] = $services;
			}
			$res[$membership_type['membership_type_id']]['type_info'] = app_Membership::getMembershipTypeInfo($membership_type['membership_type_id']);			
		}
		$diagram['types'] = $res;
		return $diagram;
	}
	
	/**
	 * Returns default registration membership type info depending on profile sex.
	 *
	 * @param integer $sex
	 * @return array
	 */
	public static function getDeafultRegistrationMembershipType( $sex )
	{
		$sex = intval( $sex );
		if( !$sex )
			return array();
		
		$membership_info = SK_MySQL::query( "SELECT * FROM `".TBL_MEMBERSHIP_TYPE."` WHERE `available_for` & $sex 
			AND `given_on_registration` & $sex LIMIT 1" )->fetch_assoc();
		return ( $membership_info )? $membership_info : self::getMembershipTypeInfo( SK_Config::Section('membership')->default_membership_type_id );
	}
	
	/**
	 * Returns formated payment plans for the subscribe page.
	 *
	 * @param integer $period
	 * @param string $unit - 'days' | 'months'
	 * @param decimal $price
	 * @param string $plan_type - 'single' | 'recurring' | 'free_trial'
	 * @return string
	 */
	public static function getFormatedPlan( $period = 0, $unit = 'days', $price = 0, $plan_type = 'single' )
	{
		if ( !is_numeric( $period ) || !is_numeric( $price ) )
			return false;
		
		if ( $unit != 'days' )
			$unit = 'months';
		
		switch ( $plan_type )
		{
			case 'single':
				$format = 'plan_structure';
				break;
				
			case 'recurring':
				$format = 'plan_structure_recurring';
				break;
							
			case 'free_trial':
				$format = 'plan_structure_free_trial';
				break;
			
			default:
				return false;
		}
		
		$format = SK_Language::section('membership')->text($format);
		
		return str_replace( array( '{$period}', '{$unit}', '{$price}', '{$sign}' ), array( $period, SK_Language::section('membership')->text('unit_'.$unit), $price, SK_Language::section('label')->text('currency_sign') ), $format );
	}

	/**
	 * Returns parsed language template for a payment provider order form.
	 *
	 * @param array $membership_info
	 * @return string
	 */
	public static function getMembershipDescription( $membership_info )
	{
		$membership_type_id = $membership_info['membership_type_id'];
		$membership_type_info = self::getMembershipTypeInfo( $membership_type_id );
		
		$replace_arr = array(
			'{$amount}' => $membership_info['price'],
			'{$currency_sign}'=>SK_Language::section('label')->text('currency_sign'),
			'{$membership_type_name}' => SK_Language::section('membership.types')->text($membership_info['membership_type_id']),
		);
		
		$replace_arr['{$period}'] = $membership_info['period'];
		$replace_arr['{$units}'] = SK_Language::section('membership')->text( 'unit_'.$membership_info['units']);
		if ( $membership_type_info['type'] == 'subscription' && $membership_info['is_recurring'] == 'y' )
			$template = 'recurring_subscription_membership_details';
		elseif( $membership_type_info['type'] == 'subscription' && $membership_info['is_recurring'] == 'n' )
			$template = 'subscription_membership_details';
		else
			$template = 'trial_membership_details';
		
		return str_replace( array_keys($replace_arr), array_values($replace_arr), SK_Language::section('membership')->text($template) );
	}

	/**
	 * Deletes expired track of membership service use.
	 *
	 * @return integer
	 */
	function deleteExpiredServiceTrack()
	{
		return MySQL::affectedRows( "DELETE FROM `".TBL_MEMBERSHIP_SERVICE_TRACK."` WHERE ".time()."-`time_stamp`>86400" );
	}
	
	
	function isMembershipTypeIconExist( $membership_type_id )
	{
		return 	MySQL::fetchField( "SELECT `has_icon` from `".TBL_MEMBERSHIP_TYPE."` WHERE membership_type_id='".$membership_type_id."'");
	}

	
	
	/*function getMembershipTypeIconUrl( $membership_type_id )
	{
		$has_icon=MySQL::fetchField( "SELECT `has_icon` from `".TBL_MEMBERSHIP_TYPE."` WHERE membership_type_id='".$membership_type_id."'");
		return $has_icon ? URL_USERFILES . 'membership_type_icon_'.$membership_type_id.'.jpeg' : '' ;
	}
	
	function getProfileMembershipTypeIconUrl( $profile_id )
	{
		return appMembership::getMembershipTypeIconUrl(appMembership::GetProfileMembershipTypeId( $profile_id ));
	}
	
	function isProfileMembershipTypeIconExist( $profile_id )
	{
		return appMembership::isMembershipTypeIconExist(appMembership::GetProfileMembershipTypeId( $profile_id ));
	}*/
	
	function onProfileUnregister($profileId)
	{
		if( !($profileId = intval($profileId)) )
			return;
		
		$membership = self::profileCurrentMembershipInfo($profileId);
		
		if( $membership['type'] != 'subscription' || $membership['limit'] != 'limited' || !$membership['fin_sale_id'] )
			return;
		
		if ( $membership['status']!='approval' || $membership['is_recurring']!='y' )
			return;
		
		$profileInfo = app_Profile::getFieldValues($profileId, array('username', 'email', 'real_name') );

		$message = "Profile '{$profileInfo['username']}' unregistered. {$profileInfo['username']}'s current membership was recurring.\n
		Last payment transactiion order number: {$membership['payment_provider_order_number']}\n\n
		Profile details:\n
		username - {$profileInfo['username']}\n
		real name - {$profileInfo['real_name']}\n
		email - {$profileInfo['email']}\n
		";
		
		$msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
				->setRecipientEmail(SK_Config::section('site')->Section('official')->get('site_email_billing'))
				->setSubject(SK_Config::section('site')->Section('official')->get( 'site_name' ))
				->setContent($message);
		app_Mail::send($msg);
	}
	
	
	/**
	 * Calls after profile signed up for giving him default registration membership.
	 *
	 * @param  integer $profile_id
	 * @return boolean
	 */
	
	public static function giveDefaultMembershipOnRegistration($profile_id)
	{
		$profile_id = intval( $profile_id );
		if ( !$profile_id )
			return false;
		
		// get default registration membership type info
		$sex = app_Profile::getFieldValues($profile_id,'sex');
		$reg_membership_type_info = self::getDeafultRegistrationMembershipType($sex);
		
		if ( !$reg_membership_type_info )
			return false;
		
		// get default membership type id
		$def_membership_type_id = SK_Config::Section('membership')->default_membership_type_id;
		
		// update profile membership type id field
		SK_MySQL::query("UPDATE `".TBL_PROFILE."` SET `membership_type_id`={$reg_membership_type_info['membership_type_id']} WHERE `profile_id`=$profile_id" );
		
		if ( $reg_membership_type_info['type'] == 'trial' )
		{
			// get trial membership type's plan
			$plan_info = self::GetMembershipTypePlan( $reg_membership_type_info['membership_type_id'] );
			$plan_info = $plan_info[0];
			
			// fill fin sale table
			$fin_sale_id = app_Finance::FillSale( $profile_id, 0, 'trial', $plan_info['units'], $plan_info['period'], 0, $reg_membership_type_info['membership_type_id'] );
			
			// --- fill membership table --- //
			$add = ( @$plan_info['units'] == 'months' )? (int)@$plan_info['period']*24*3600*30 : (int)@$plan_info['period']*24*3600;
			$expiration_stamp = time() + $add;
			
			SK_MySQL::query("INSERT INTO `".TBL_MEMBERSHIP."` 
                (`fin_sale_id`,`start_stamp`,`expiration_stamp`,`membership_type_id`,`prev_membership_type_id`) 
                VALUES('$fin_sale_id','".time()."','$expiration_stamp','{$reg_membership_type_info['membership_type_id']}','$def_membership_type_id')");
			// --- END --//

			// send mail
			$period_str = $plan_info['period'].' '.( ( $plan_info['units'] == 'days' )?
				SK_Language::text('%i18n.date.period_d'):
				SK_Language::text('%i18n.date.period_m'));
				
			// send notify mail
			$msg = app_Mail::createMessage()
					->setRecipientProfileId($profile_id)
					->setTpl('profile_get_trial_on_signup')
					->assignVar('membership_type', SK_Language::section('membership.types')->text($reg_membership_type_info['membership_type_id']))
					->assignVar('period', $period_str);
			app_Mail::send($msg);
		}
		
		return true;
	}
	
	
	/**
	* Returns calendar days count from months. Define months from current time.
	* Example: today is 27 Jan 2006; parameter $months is 2; 
	* function returns 62 because there are 31 days in Dec and 31 in Jan.
	*
	* @param string $string
	* @return string
	*/
	public static function getDaysCountFromMonths( $months, $days = null )
	{
		$months = intval($months);
		$days = intval($days);
		
		$time = time() + $days*24*3600;
		$days += intval( date("t", $time) );
		if ( $months < 2 )
			return $days;
		else
		{
			return self::getDaysCountFromMonths( $months-1, $days );
		}
	}
	
	public static function checkTrialMembershipAvailability( $profile_id )
	{
		$profile_sex = app_Profile::getFieldValues( $profile_id, 'sex' );
		
		if ( !$profile_sex )
			return array();
		
		$query = "SELECT `membership_type_id` FROM `".TBL_MEMBERSHIP_TYPE."` 
			WHERE `available_for` & $profile_sex AND `type`='trial' AND `paid_by_sms` = 0 ORDER BY `order`";

		$result = SK_MySQL::query($query);
		$ret = array();	
		while( $id = $result->fetch_cell() )
		{
			$current = self::profileCurrentMembershipInfo($profile_id);
			$can_claim = self::checkIfProfileClaimedTheMembership($profile_id, $id, 'type');
			
			$plans = app_Membership::getMembershipTypePlan( $id );
			$is_free = $plans[0]['price'] == 0 ? true : false;
			
			if ( $current['membership_type_id'] != $id && $can_claim == 1 )
				$ret[] = array('type_id'=>$id,'is_available'=>true, 'is_free'=>$is_free);
			
			else 
				$ret[] = array('type_id'=>$id,'is_available'=>false, 'is_free'=>$is_free);
		}
		
		return $ret;
	}

    public static function expireProfileMembership( $profile_id, $membership_id )
    {
        $current_mship_type_id = MySQL::fetchField("SELECT `membership_type_id` FROM `".TBL_MEMBERSHIP."` 
            WHERE `membership_id`='$membership_id'" );
                
        $prev_mship_type_id = MySQL::fetchField("SELECT `prev_membership_type_id` FROM `".TBL_MEMBERSHIP."` 
            WHERE `membership_id`='$membership_id'" );
        
        if ( $prev_mship_type_id )
        {
            $query = "SELECT `m`.`membership_id`, `m`.`membership_type_id` 
                FROM `".TBL_MEMBERSHIP."` AS `m` 
                INNER JOIN `".TBL_FIN_SALE."` AS `s` USING(`fin_sale_id`) 
                WHERE `s`.`profile_id`='$profile_id' 
                AND `m`.`membership_id` != '$membership_id' ORDER BY `m`.`expiration_stamp` LIMIT 1";
            
            $prev_ms = MySQL::fetchRow($query);
        
            if ( !$prev_ms )
            {
                MySQL::fetchResource("UPDATE `".TBL_PROFILE."` SET `membership_type_id`='$prev_mship_type_id' 
                    WHERE `profile_id`='{$profile_id}'");
            }
            else
            {
                MySQL::fetchResource("UPDATE `".TBL_MEMBERSHIP."` SET `prev_membership_type_id`='$prev_mship_type_id' 
                    WHERE `membership_id`='{$prev_ms['membership_id']}'");
                
                MySQL::fetchResource("UPDATE `".TBL_PROFILE."` SET `membership_type_id`='{$prev_ms['membership_type_id']}' 
                    WHERE `profile_id`='{$profile_id}'");
            }
        }
        
        // delete membership
        MySQL::fetchResource("DELETE FROM `".TBL_MEMBERSHIP."` WHERE `membership_id`='{$membership_id}'");
        
        // get profile info
        $profile_info = app_Profile::getFieldValues($profile_id, array('language_id', 'email', 'username'));
        
        $lang = SK_Language::instance($profile_info['language_id']);
        
        // send notify mail
        $msg = app_Mail::createMessage()
                ->setRecipientProfileId($profile_id)
                ->setTpl('membership_expired')
                ->assignVar('membership_type', SK_Language::section('membership.types', $lang)->text($current_mship_type_id));
        app_Mail::send($msg);
    }
}
