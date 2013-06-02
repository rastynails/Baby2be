<?php
require_once( DIR_ADMIN_INC.'fnc.finance.php' );
require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );

/**
 *
 * Class for working with membership types and services (for admin panel)
 * 
 */
class AdminMembership
{	
	/**
	 * Changes default membership type. This membership type is given to profiles, who has no other membership type.
	 *
	 * @param integer $membership_type_id
	 * @return boolean
	 */
	public static function ChangeDefaultMembershipType( $membership_type_id )
	{
		
		if ( !is_numeric( $membership_type_id ) )
			return false;
		
		$memership_type_info = self::GetMembershipTypeInfo($membership_type_id);
		
		if ( $memership_type_info['type'] == 'subscription'  && $memership_type_info['limit'] == 'unlimited' )
		{
			if ( SK_Config::Section('membership')->default_membership_type_id == $membership_type_id )
				return true;
			$all_sexs = 0;
			foreach ( SK_ProfileFields::get('sex')->values as $sex)
				$all_sexs += $sex;

			self::UpdateMembershipTypeAvailability( $membership_type_id, intval($all_sexs) );
			adminConfig::SaveConfig('membership','default_membership_type_id',$membership_type_id);
					
			return true;
		}
		else
			return false;
	}
	
	
	/**
	 * Makes available default membership type for a new sex.
	 *
	 * @param integer $value
	 * @return boolean
	 */
	public static function MakeNewSexAvailableForDeafultMembershipType( $value )
	{
		$value = intval( $value );
		if ( !$value )
			return false;
		
		$_membership_type_id = SK_Config::section('membership')->default_membership_type_id;
		$_all_sexs = 0;
		foreach ( SK_ProfileFields::get('sex')->values as $_sex)
			$_all_sexs += $_sex;

		self::UpdateMembershipTypeAvailability($_membership_type_id, intval($_all_sexs));
		
		return true;
	}
	
	
	/**
	 * Deletes sex from membership types "available for" fields.
	 *
	 * @param integer $value deleted sex value
	 */
	public static function ClearMembershipTypeAvailabilityFromDeletedSex( $value )
	{
		$value = intval( $value );
		if ( !$value )
			return;
		
		$_membership_type_arr = self::GetAllMembershipTypes();
		foreach ( $_membership_type_arr as $_membership_type )
		{
			if ( $_membership_type['available_for'] & $value )
			{
				$_new_value = $_membership_type['available_for'] - $value;
				self::UpdateMembershipTypeAvailability( $_membership_type['membership_type_id'], $_new_value );
			}
		}
	}
	
	
	/**
	 * Changes default registration membership type. 
	 * This membership type is given to profiles depending on sex when they sign up.
	 *
	 * @param integer $membership_type_id
	 * @param integer $given_on_reg
	 * @return boolean
	 */
	public static function changeDefaultRegMembershipType( $array )
	{
		if ( !is_array( $array ) )
			return false;

		$_membership_types = self::GetAllMembershipTypes();
		foreach ( $_membership_types as $_membership_type )
		{
			$_given_on_reg = 0;
			foreach ( SK_ProfileFields::get('sex')->values as $_sex )
			{
				if ( intval(@$array[$_membership_type['membership_type_id']]) & intval($_sex) )
					$_given_on_reg += intval( $_sex );
			}
			
			SK_MySQL::query("UPDATE `".TBL_MEMBERSHIP_TYPE."` SET `given_on_registration`=$_given_on_reg 
				WHERE `membership_type_id`={$_membership_type['membership_type_id']}");
		}
		
		return true;
	}
	
	/**
	 * Inserts a new membership type into the database.
	 * Returns code which defines the result:<br>
	 * <li>-1 - incorrect params were passed</li>
	 * <li>-2 - the membership type was created, but language's data was not created</li>
	 * <li>1 - the membership type was successfully created</li>
	 *
	 * @param string $membership_name membership type's name
	 * @param string $membership_descripton - description on default language
	 * @param string $membership_type membership's type - 'subscription', 'trial'
	 * @param string $membership_limit define membership's limit - 'limited', 'unlimited'
	 * @param array $available_for
	 *
	 * @return integer
	 */
	public static function AddNewMembershipType( $membership_name, $membership_descripton, $membership_type, $membership_limit, $available_for )
	{
		$default_lang_id = SK_Config::Section('languages')->default_lang_id;
		if ( !strlen( $membership_name[$default_lang_id] ) || !strlen($membership_type) || !strlen( $membership_limit ) )
			return -1;
			
		if ( !is_array( $available_for ) )
			$available_for = array();
		
		switch ( $membership_limit )
		{
			case 'limited':
			case 'unlimited':
				break;
			default:
				return -1;
		}
		switch ( $membership_type )
		{
			case 'subscription':
			case 'trial':
				break;
			default:
				return -1;
		}
		
		$_sex = 0;
		foreach ( $available_for as $_value )
			if ( in_array( $_value, SK_ProfileFields::get('sex')->values ) )
				$_sex = $_sex + $_value;
		
		// Get max order number:
		$_max_order = MySQL::fetchField( "SELECT `order` FROM `".TBL_MEMBERSHIP_TYPE."` ORDER BY `order` DESC LIMIT 1" );
		
		$_query = "INSERT INTO `".TBL_MEMBERSHIP_TYPE."` ( `type`,`limit`,`order`,`available_for` ) VALUES( '$membership_type','$membership_limit','".($_max_order+1)."',$_sex )";
		$membership_type_id = MySQL::insertId( $_query );
		
		// insert access for menu items
		$_menu_items = MySQL::fetchArray("SELECT * FROM `" . TBL_MENU_ITEM . "` ORDER BY `menu_id`");
		
		foreach ( $_menu_items as $_menu )
		{
                    $_query = sql_placeholder( "INSERT INTO `".TBL_LINK_MENU_ITEM_MEMBERSHIP."`(`menu_item_id`,`membership_type_id`) 
                                    VALUES( ?@ )", array( $_menu['menu_item_id'], $membership_type_id ) );
                    MySQL::fetchResource( $_query );
		}
		
		try {
			SK_LanguageEdit::setKey('membership.types',$membership_type_id, $membership_name);
			SK_LanguageEdit::setKey('membership.types.desc',$membership_type_id,$membership_descripton);
		}catch (SK_LanguageEditException $e ){
			return -2;
		}
			
		//set default service limits
		$services = MySQL::fetchArray( "SELECT `membership_service_key` FROM `".TBL_MEMBERSHIP_SERVICE."` WHERE `is_limited`='y'", 0 );
		foreach ( $services as $service_key )
			MySQL::fetchResource( sql_placeholder("INSERT INTO `?#TBL_MEMBERSHIP_LINK_SERVICE_LIMIT` (`membership_service_key`,`membership_type_id`,`limit`) VALUES( ?@ )", array($service_key, $membership_type_id, 2)) );
		
		return 1;
	}

	
	/**
	 * Delete membership type.
	 * Returns:<br>
	 * <li>1 - the membership type was successfully deleted</li>
	 * <li>0 - one of the mysql-queries failed</li>
	 * <li>-1 - incorrect parameter</li>
	 * <li>-2 - there are memberships on this membership type</li>
	 * <li>-3 - this membership type is the default</li>
	 * <li>-5 - system guest membership type</li>
	 *
	 * @param integer $membership_type_id membersip type's id
	 *
	 * @return integer
	 */
	public static function DelMembershipType( $membership_type_id )
	{
		$membership_type_id = intval( $membership_type_id );
		
		if ( !$membership_type_id )
			return -1;
		
		if ( $membership_type_id == 1 )
			return -5;
		
		$_query1 = "SELECT `m`.`membership_id`, `s`.`profile_id` FROM `".TBL_MEMBERSHIP."` AS `m`
			INNER JOIN `".TBL_FIN_SALE."` AS `s` ON (`m`.`fin_sale_id`=`s`.`fin_sale_id`)
			INNER JOIN `".TBL_PROFILE."` AS `pr` ON (`pr`.`profile_id`=`s`.`profile_id`)
			WHERE `m`.`membership_type_id`='$membership_type_id' OR 
				`m`.`prev_membership_type_id`='$membership_type_id'";
			
		$_query2 = "SELECT `profile_id` FROM `".TBL_PROFILE."` WHERE `membership_type_id`='$membership_type_id'";
		
		if ( MySQL::affectedRows( $_query1 ) > 0 || MySQL::affectedRows( $_query2 ) > 0 )
			return -2;
		
		if ( $membership_type_id == SK_Config::Section('membership')->default_membership_type_id )
			return -3;
		
		//delete membership type's all info
		SK_LanguageEdit::deleteKey('membership.types', $membership_type_id);
		SK_LanguageEdit::deleteKey('membership.types.desc', $membership_type_id);
		MySQL::fetchResource( "DELETE FROM `".TBL_MEMBERSHIP_TYPE."` WHERE `membership_type_id`=$membership_type_id" );
		MySQL::fetchResource( "DELETE FROM `".TBL_MEMBERSHIP_CLAIM."` WHERE `membership_type_id`='$membership_type_id'" );
		MySQL::fetchResource( "DELETE FROM `".TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE."` WHERE `membership_type_id`=$membership_type_id" );
		MySQL::fetchResource( "DELETE FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` WHERE `membership_type_id`=$membership_type_id" );
		MySQL::fetchResource( sql_placeholder("DELETE FROM `?#TBL_MEMBERSHIP_LINK_SERVICE_LIMIT` WHERE `membership_type_id`=?", $membership_type_id ) );
		
		self::deleteMembershipTypePlans( $membership_type_id );
		
		return 1;
	}
	
	
	public static function deleteMembershipTypePlans( $membership_type_id )
	{
		$plans = MySQL::fetchArray( sql_placeholder("SELECT `membership_type_plan_id` FROM `?#TBL_MEMBERSHIP_TYPE_PLAN` WHERE `membership_type_id`=?", $membership_type_id ));
		foreach ( $plans as $plan )
		{
			MySQL::fetchResource( sql_placeholder("DELETE FROM `".TBL_FIN_PAYMENT_PROVIDER_PLAN."` WHERE `membership_type_plan_id`=?", $plan['membership_type_plan_id']));
		}

		MySQL::fetchResource( "DELETE FROM `".TBL_MEMBERSHIP_TYPE_PLAN."` WHERE `membership_type_id`=$membership_type_id" );
	}
	
	
	/**
	* Move the order of the membership type's for payment selection page.
	*
	* @param integer $membership_type_id
	* @param string $move
	*
	* @return boolean
	*/
	public static function MoveOrderMembershipType( $membership_type_id, $move )
	{
		if ( !is_numeric( $membership_type_id ) )
			return false;
	
		//get old order
		$_query = "SELECT `order` FROM `".TBL_MEMBERSHIP_TYPE."` WHERE `membership_type_id`='$membership_type_id'";
		$_old_order = MySQL::fetchRow( $_query );
		
		switch ( $move )
		{
			case 'up':
				$_query = "SELECT `membership_type_id`,`order` FROM `".TBL_MEMBERSHIP_TYPE."` WHERE `order`<'{$_old_order['order']}' ORDER BY `order` DESC LIMIT 1";
				break;
			case 'down':
				$_query = "SELECT `membership_type_id`,`order` FROM `".TBL_MEMBERSHIP_TYPE."` WHERE `order`>'{$_old_order['order']}' ORDER BY `order` ASC LIMIT 1";
				break;
			default:
				return false;
		}
		$_cur_order = MySQL::fetchRow( $_query );
		
		if ( !$_old_order || !$_cur_order )
			return false;
		
		MySQL::affectedRows( "UPDATE `".TBL_MEMBERSHIP_TYPE."` SET `order`='{$_cur_order['order']}' WHERE `membership_type_id`='$membership_type_id'" );
		MySQL::affectedRows( "UPDATE `".TBL_MEMBERSHIP_TYPE."` SET `order`='{$_old_order['order']}' WHERE `membership_type_id`='{$_cur_order['membership_type_id']}'" );
		
		return true;
	}

	
	/**
	* Update membership type's permissions.
	*
	* @param array $service_key_arr sevices' key, which are available for the membership type
	* @param integer $membership_type_id
	*
	* @return boolean
	*/
	public static function updateMembershipPermission( $service_permission, $membership_type_id )
	{
		if ( !intval( $membership_type_id ) )
			return false;
		
		MySQL::fetchResource( "DELETE FROM `".TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE."` WHERE `membership_type_id`='$membership_type_id'" );
	
		$result = true;
		if ( is_array( $service_permission ) )
		{
			foreach ( $service_permission as $_service_key )
			{
				if ( !strlen( $_service_key ) )
					continue;
					
				if ( !MySQL::fetchResource( sql_placeholder("INSERT INTO `?#TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE` VALUES( ?,? )", $membership_type_id, $_service_key) ) )
					$result = false;
			}
		}
		return $result;
	}

	public static function updateServiceLimitForMembershipType( $service_limit, $membership_type_id )
	{
		if ( !intval( $membership_type_id ) || !is_array($service_limit) )
			return false;
		
		foreach ( $service_limit as $service_key => $limit )
		{
			if( self::ServiceIsLimited($service_key) && intval($limit) > -1 && strlen($service_key) )
				
				$limit_record = MySQL::fetchRow(sql_placeholder("SELECT * FROM `?#TBL_MEMBERSHIP_LINK_SERVICE_LIMIT`
					WHERE `membership_type_id`=? AND `membership_service_key`=?", $membership_type_id, $service_key));

				if ($limit_record)
					$res = MySQL::affectedRows( sql_placeholder("UPDATE `?#TBL_MEMBERSHIP_LINK_SERVICE_LIMIT` 
						SET `limit`=? WHERE `membership_type_id`=? AND `membership_service_key`=?", 
						(int)$limit, $membership_type_id, $service_key) );
				else 
					$res = MySQL::insertId(sql_placeholder("INSERT INTO `?#TBL_MEMBERSHIP_LINK_SERVICE_LIMIT` 
						(`membership_service_key`, `membership_type_id`, `limit`) VALUES(?,?,?)", 
						$service_key, $membership_type_id, $limit)); 
		}
	
		return $res ? true : false;
	}
	
	/**
	 * Add a new membership type's plan.
	 * Returns:<br>
	 * <li>1 - successfully was added</li>
	 * <li>0 - failed</li>
	 * <li>-1 - incorrect parameters were passed</li>
	 * <li>-2 - trial membership type is already has plan</li>
	 *
	 * @param integer $membership_type_id
	 * @param array $plan_arr
	 * 		<UL>plan's info 
	 * 			<li>price</li>
	 * 			<li>period</li>
	 * 			<li>unit</li></UL>
	 *
	 * @return integer
	 */
	public static function AddMembershipTypePlan( $membership_type_id, $plan_arr )
	{
		$membership_type_id = intval( $membership_type_id );
		if ( $membership_type_id < 2 || !is_numeric( $plan_arr['price'] ) )
			return -1;
		
		$_membership_type_info = self::GetMembershipTypeInfo( $membership_type_id );
		
        if ( $_membership_type_info['type'] == 'subscription' )
		{
			if ( !is_numeric( $plan_arr['period'] ) || ( $plan_arr['unit'] != 'days' && $plan_arr['unit'] != 'months' ) )
				return -1;
		
			$_query = "INSERT INTO `".TBL_MEMBERSHIP_TYPE_PLAN."` (`membership_type_id`,`price`,`period`,`units`,`is_recurring` ) 
				VALUES( '$membership_type_id','{$plan_arr['price']}','{$plan_arr['period']}','{$plan_arr['unit']}','{$plan_arr['is_recurring']}' )";
		}
		else	//membership type is a trial.
		{
			// Check if trial membership type has plan. If has then return error:
			if ( self::GetMembershipTypePlan( $membership_type_id ) )
				return -2;
			
			$_query = "INSERT INTO `".TBL_MEMBERSHIP_TYPE_PLAN."` ( `membership_type_id`,`price`,`period`,`units` ) VALUES( '$membership_type_id','{$plan_arr['price']}','{$plan_arr['period']}','{$plan_arr['unit']}' )";
		}
		return  ( MySQL::fetchResource( $_query ) )? 1 : 0;
	}
	
	
	/**
	 * Delete membership type's plans.
	 *
	 * @param array $plan_arr
	 * @return integer 
	 */
	public static function DeleteMembershipPlans( $plan_arr )
	{
		$_i = 1;
		foreach ( $plan_arr as $_plan )
		{
			if ( !is_numeric( $_plan ) ) 
				return -1;
			
			if ( $_i == 1 )
				$_in_q = $_plan;
			else
				$_in_q.= ','.$_plan;
			$_i++;
		}
		$_query = "DELETE FROM `".TBL_MEMBERSHIP_TYPE_PLAN."` WHERE `membership_type_plan_id` IN( $_in_q )";
		
		MySQL::fetchResource( "DELETE FROM `".TBL_FIN_PAYMENT_PROVIDER_PLAN."` WHERE `membership_type_plan_id` IN($_in_q)" );
		
		return ( MySQL::fetchResource( $_query ) )? 1 : 0;
	}
	
	
	/**
	* Updates the membership type's plans.
	*
	* @param integer $membership_type_id
	* @param array $plan_arr<br>
	* 		the membership type's plan information:
	* 		<li>plan_id</li>
	* 		<li>price</li>
	* 		<li>period</li>
	* 		<li>unit</li>
	* 		<li>type</li>
	*
	* @return boolean
	*/
	
	public static function UpdateMembershipPlans( $membership_type_id, $plan_arr )
	{
		$_result = true;
		
		foreach ( $plan_arr as $_plan )
		{
			if ( is_numeric( $_plan['price'] ) && is_numeric( $_plan['period'] ) && ( $_plan['unit'] == 'days' || $_plan['unit'] == 'months' ) )
			{
				$_query = "UPDATE `".TBL_MEMBERSHIP_TYPE_PLAN."` SET `price`='{$_plan['price']}',`period`='{$_plan['period']}',
					`units`='{$_plan['unit']}',`is_recurring`='{$_plan['type']}' WHERE `membership_type_plan_id`='{$_plan['id']}'";
				
				if ( !MySQL::fetchResource( $_query ) )
					$_result = false;
			}
			else
			{
				$_result = false;
			}
		}
		
		return $_result;
	}


	public static function UpdateMembershipTypeAvailability( $membership_type_id, $availability )
	{
		$availability = intval( $availability );
		
		$_membership_type = self::GetMembershipTypeInfo( $membership_type_id );
		if ( !$_membership_type )
			return false;
		$_given_on_reg=0;
		for( $_i = 1; $_i <= $_membership_type['given_on_registration']; $_i = $_i * 2 )
			if ( intval($availability) & intval($_i) && intval($_membership_type['given_on_registration']) & intval($_i) )
				$_given_on_reg += $_i;
		$_given_on_reg = intval( $_given_on_reg );
		if ( $_given_on_reg == $_membership_type['given_on_registration'] && $availability == $_membership_type['available_for'] )
			return true;
		
		$query = SK_MySQL::placeholder("UPDATE `".TBL_MEMBERSHIP_TYPE."` SET `available_for`=?,
			`given_on_registration`=? WHERE `membership_type_id`=?",$availability, $_given_on_reg, $membership_type_id);
		
		SK_MySQL::query($query);
		
		return SK_MySQL::affected_rows();
	}
	

	/**
	* Get profiles' claims of trial membership types. Returns all claims, which were not refused or granted.
	*
	* @param integer $page
	* @param integer $num_on_page num of the claims on page: 10-50
	* @param string $sort_by sort fields: profile_id or claim_stamp
	* @param string $sort_type sort order type: ASC or DESC
	*
	* @return array
	*/
	public static function GetClaimsTrialMembership( $page, $num_on_page, $sort_by, $sort_type )
	{
		if ( !is_numeric( $page ) || $page < 1 )
			$page = 1;
	
		if ( !is_numeric( $num_on_page ) || $num_on_page < 10 || $num_on_page > 50 )
			$num_on_page = 10;

		if ( $sort_by != 'claim_stamp' )
			$sort_by = '`p`.`username`';
		else
			$sort_by = '`m`.`claim_stamp`';
		
		if ( $sort_type != 'DESC' )
			$sort_type = 'ASC';
		
		$_query = "SELECT `m`.`profile_id`,`m`.`membership_type_id`,`m`.`claim_stamp`,`p`.`username` FROM `".TBL_MEMBERSHIP_CLAIM."` AS `m`
			INNER JOIN `".TBL_PROFILE."` AS `p` USING(`profile_id`)
			WHERE `m`.`claim_result`='claim' ORDER BY $sort_by $sort_type LIMIT ".( ( $page-1 )*$num_on_page ).",$num_on_page";
		
		return MySQL::fetchArray( $_query );
	}

	
	/**
	* Returns count of claims, which were not refused or granted.
	*
	* @return integer
	*/
	public static function GetCountClaimsTrialMembership()
	{
		return MySQL::fetchField( "SELECT COUNT(*) FROM `".TBL_MEMBERSHIP_CLAIM."` WHERE `claim_result`='claim'" );
	}
	
	
	/**
	* Grant or refuse profiles' claims.
	*
	* @param string $claim_result Command which will be executed: granted, refused.
	* @param array $claims List of the claims which will be processed. 
	* Format:<br>
	* <code>
	* 	Array( array(0=>1, 1=>3) )
	* </code>
	*
	* @return boolean
	*/
	public static function GrantOrRefuseClaims( $claim_result, $claims )
	{	
		$_res = true;
		
		foreach ( $claims as $_claim )
		{
			// get membership type's plan info:
			$_plan_info = self::GetMembershipTypePlan( $_claim[1] );
			// get profile info
			$_profile_info = app_Profile::getFieldValues( $_claim[0], array( 'email', 'username' ) );
			
			if ( $claim_result == 'granted' )
			{
				$_query = "UPDATE `".TBL_MEMBERSHIP_CLAIM."` SET `claim_result`='$claim_result' 
					WHERE `profile_id`='{$_claim[0]}' AND `membership_type_id`='{$_claim[1]}'";
				
				if ( !MySQL::fetchResource( $_query ) )
					$_res = false;
				else
				{
					// fill fin sale table
					$_fin_sale_id = app_Finance::FillSale( $_claim[0], 0, 'trial', $_plan_info[0]['units'], $_plan_info[0]['period'], 0, $_claim[1] );
					
					// fill membership table
					app_Membership::BuySubscriptionTrialMembership( $_claim[0], $_claim[1], $_plan_info[0]['period'], $_plan_info[0]['units'], $_fin_sale_id );
					
					// send mail
					$_period_str = $_plan_info[0]['period'].' '.( ( $_plan_info[0]['units'] == 'days' ) ? SK_Language::section('membership')->text('unit_days') : SK_Language::section('membership')->text('unit_months'));
					
					$msg = app_Mail::createMessage();
					$msg->setRecipientProfileId($_claim[0])
						->setTpl('profile_claim_granted')
						->assignVar('membership_type', SK_Language::section('membership.types')->text($_claim[1]))
						->assignVar('period', $_period_str);
					app_Mail::send($msg);
				}
			}
			else
			{
				if ( !MySQL::fetchResource( "DELETE FROM `".TBL_MEMBERSHIP_CLAIM."` WHERE `profile_id`='{$_claim[0]}' AND `membership_type_id`='{$_claim[1]}'" ) )
					$_res = false;
				else
				{
					// send mail
					$_period_str = $_plan_info[0]['period'].' '. SK_Language::section('membership')->text('unit_months');
					
					$msg = app_Mail::createMessage();
					$msg->setRecipientProfileId($_claim[0])
						->setTpl('profile_claim_refused')
						->assignVar('membership_type', SK_Language::section('membership.types')->text($_claim[1]))
						->assignVar('period', $_period_str);
					app_Mail::send($msg);
				}
			}
		}
		
		return $_res;
	}
	
	/**
	 * Returns array with all membership types' info:
	 * membership_type_id, type, limit.
	 *
	 * @return array
	 */
	public static function GetAllMembershipTypes()
	{
		$_query = "SELECT * FROM  `".TBL_MEMBERSHIP_TYPE."` ORDER BY `order`";
		
		return MySQL::fetchArray( $_query );
	}

	
	/**
	 * Returns array with the membership type's info : id, type and limit.
	 *
	 * @param integer $membership_type_id
	 * @return array
	 */
	public static function GetMembershipTypeInfo( $membership_type_id )
	{
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MEMBERSHIP_TYPE."` 
			WHERE `membership_type_id`=?", $membership_type_id);
		
		return SK_MySQL::query($query)->fetch_assoc();
	}

	
	/**
	 * Return list of all services with their info:
	 * <UL>
	 * <li>membership_service_key</li>
	 * <li>description</li>
	 * <li>credits</li>
	 * <li>is_promo</li>
	 * <li>active</li>
	 * <li>show - show on permission diagram</li>
	 * <li>order</li>
	 * </UL>
	 *
	 * @return array
	 */
	public static function GetMembershipServiceList( $membership_type_id = 0 )
	{
		$_query = "SELECT `s`.*,`f`.`active` FROM `".TBL_MEMBERSHIP_SERVICE."` AS `s`
			INNER JOIN `".TBL_FEATURE."` AS `f` USING( `feature_id` ) WHERE `f`.`active`='yes' ORDER BY `s`.`order`";
		
		$_service_list = MySQL::fetchArray( $_query );
		if ( $membership_type_id == 1 )
		{
			foreach ( $_service_list as $_key=>$_service )
				if ( !self::ServiceIsAvailableForGuest( $_service['membership_service_key'] ) )
					unset( $_service_list[$_key] );
		}

		return $_service_list;
	}
	
	
	/**
	 * Return list of all services and membership type's permissions.
	 *
	 * @param integer $membership_type_id
	 * @return array
	 */
	public static function GetMembershipServiceListPermissions( $membership_type_id )
	{
		$return = self::GetMembershipServiceList( $membership_type_id );
		$service_limit = MySQL::fetchArray( "SELECT `membership_service_key`,`limit` FROM `".TBL_MEMBERSHIP_LINK_SERVICE_LIMIT."` WHERE `membership_type_id`=$membership_type_id", 1 );
		
		foreach( $return as $_key=>$service )
		{
			$_query = "SELECT `membership_type_id` FROM `".TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE."` 
				WHERE `membership_type_id`='$membership_type_id' AND `membership_service_key`='{$service['membership_service_key']}' LIMIT 1";
			
			$return[$_key]['available']  = ( mysql_num_rows( MySQL::fetchResource( $_query) ) > 0 )? 'checked' : '';
			if ( $service['is_limited'] == 'y' )
				$return[$_key]['service_limit'] = @$service_limit[$service['membership_service_key']];
		}
		
		return $return;
	}
	
	
	/**
	 * Updates Membership Services' credits, promo-flags(free and available for all) and show-flags (show on permission-digram).
	 * These arrays must have the follow structures:
	 *
	 * @param array $credits
	 * <code>
	 * array
	 *		(
	 *			'view_profile' => 1.02,
	 *			'send_message' => 0.45
	 * 		)
	 * </code>
	 * @param array $promo
	 * <code>
	 * array
	 *		(
	 *			'view_profile' => 'yes',
	 *			'send_message' => 'no'
	 * 		)
	 * </code>
	 * @param array $show
	 * <code>
	 * array
	 *		(
	 *			'view_profile' => 'yes',
	 *			'send_message' => 'no'
	 * 		)
	 *			
	 * @return boolean
	 */
	public static function UpdateMembershipServiceList( $promo, $show )
	{
		if ( !is_array( $promo ) || !is_array( $show ) )
			return false;

		$_result = true;
		foreach ( $promo as $_key=>$_credit )
		{
			if (( $promo[$_key] == 'yes' || $promo[$_key] == 'no' ) && ( $show[$_key] == 'yes' || $show[$_key] == 'no' ) )
			{
				if ( !MySQL::fetchResource( "UPDATE `".TBL_MEMBERSHIP_SERVICE."` SET `is_promo`='{$promo[$_key]}', 
						`show`='{$show[$_key]}' WHERE `membership_service_key`='$_key'" ) )
					$_result = false;
			}
			else
				$_result = false;
		}
		return $_result;
	}
	
	
	/**
	* Move the order of the membership service for permission diagram.
	*
	* @param string $service_key
	* @param string $move
	*
	* @return boolean
	*/
	public static function MoveOrderMembershipService( $service_key, $move )
	{
		if ( !strlen( $service_key ) )
			return false;
	
		//get old order
		$_query = "SELECT `order` FROM `".TBL_MEMBERSHIP_SERVICE."` WHERE `membership_service_key`='$service_key'";
		$_old_order = MySQL::fetchField( $_query );
		
		switch ( $move )
		{
			case 'up':
				$_query = "SELECT `membership_service_key`,`order` FROM `".TBL_MEMBERSHIP_SERVICE."` WHERE `order`<'$_old_order' ORDER BY `order` DESC LIMIT 1";
				break;
			case 'down':
				$_query = "SELECT `membership_service_key`,`order` FROM `".TBL_MEMBERSHIP_SERVICE."` WHERE `order`>'$_old_order' ORDER BY `order` ASC LIMIT 1";
				break;
			default:
				return false;
		}
		$_cur_order = MySQL::fetchRow( $_query );
		
		if ( !$_old_order || !$_cur_order )
			return false;

		MySQL::affectedRows( "UPDATE `".TBL_MEMBERSHIP_SERVICE."` SET `order`='{$_cur_order['order']}' WHERE `membership_service_key`='$service_key'" );
		MySQL::affectedRows( "UPDATE `".TBL_MEMBERSHIP_SERVICE."` SET `order`='$_old_order' WHERE `membership_service_key`='{$_cur_order['membership_service_key']}'" );
		
		return true;
	}

	
	/**
	 * Returns list of the membership type's plans with their info: id, price, period, unit.
	 *
	 * @param integer membership type's id
	 * @return array
	 */
	public static function GetMembershipTypePlan( $membership_type_id )
	{
		$_query = "SELECT * FROM `".TBL_MEMBERSHIP_TYPE_PLAN."`
			WHERE `membership_type_id`='$membership_type_id' ORDER BY `membership_type_plan_id`";
		
		return MySQL::fetchArray( $_query );
	}
	

	/**
	 * Return list of the membership features.
	 *
	 * @return array
	 */
	public static function GetMembershipFeatureList($dating = false)
	{
		return MySQL::fetchArray( "SELECT `feature_id`,`name`,`active` FROM `".TBL_FEATURE."` 
		  WHERE `hidden`=0 AND `dating`=" . intval($dating)." ORDER BY `order` ASC" );
	}

	
	/**
	 * Update status of the membership features.
	 *
	 * @param array $features
	 * @return integer returns<br>
	 * <li>0 - failed</li>
	 * <li>-1 - incorrect parameters were posted</li>
	 * <li>1 - updated successfully</li> 
	 */
	public static function UpdateMembershipFeatureList( $features, $dating = false )
	{
		if ( !is_array( $features ) )
			return -1;
		
		$_i = 0;
		foreach ( $features as $_value )
		{
			if ( $_i == 0 )
				$_in_q = "'$_value'";
			else
				$_in_q.= ",'$_value'";
			$_i++;
		}
		
		return ( MySQL::fetchResource( "UPDATE `".TBL_FEATURE."` SET `active`='yes' WHERE `feature_id` IN( $_in_q ) AND `hidden`<>1 AND `dating`=" . intval($dating) ) &&
			MySQL::fetchResource( "UPDATE `".TBL_FEATURE."` SET `active`='no' WHERE `feature_id` NOT IN( $_in_q ) AND `hidden`<>1 AND `dating`=" . intval($dating) ) )? 1 : 0;
	}


	/**
	 * Update status of the feature.
	 *
	 * @param integer $feature_id
	 * @param boolean $is_active
	 * @return boolean
	 */
	public static function UpdateMembershipFeature( $feature_id, $is_active = false )
	{
		if( !is_numeric( $feature_id ) )
			return false;
		
		$_active_str = ( $is_active )? 'yes' : 'no';
		return MySQL::fetchResource( "UPDATE `".TBL_FEATURE."` SET `active`='$_active_str' WHERE `feature_id`='$feature_id'" );
	}
	
	
	/**
	 * Checks if the membership type exists in the database, not deleted.
	 *
	 * @param integer $membership_type_id
	 * @return boolean
	 */
	public static function IsExistMembershipType( $membership_type_id )	
	{
		return ( MySQL::fetchField( "SELECT `membership_type_id` FROM `".TBL_MEMBERSHIP_TYPE."` 
						WHERE `membership_type_id`='$membership_type_id'" ) )? true : false;
	}


	/**
	 * Refunds transaction. It means delete profile membership using fin sale id.
	 *
	 * @param integer $fin_sale_id
	 * @param decimal $fine_amount
	 * @param string $comment
	 * @return boolean
	 */
	public static function refundTransaction( $fin_sale_id, $fine_amount, $comment ='' )
	{
		$fin_sale_id = intval( $fin_sale_id );
		if ( !$fin_sale_id || !is_numeric( $fine_amount ) )
			return false;
		
		// get transaction info
		$_query = "SELECT `f`.*,`mt`.`type`,`mt`.`limit` 
				FROM `".TBL_FIN_SALE."` AS `f` 
				INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` USING(`membership_type_id`)
				WHERE `f`.`fin_sale_id`='$fin_sale_id'";
		
		$_transaction_info = MySQL::fetchRow( $_query );
		if ( !$_transaction_info )
			return false;
		
		// add transaction into refund list/table
		RefundOrder( $fin_sale_id, $fine_amount, $comment );

		// get the membership info:
		$_query = "SELECT `membership_id`,`start_stamp`,`credits` FROM `".TBL_MEMBERSHIP."` WHERE `fin_sale_id`='{$_transaction_info['fin_sale_id']}'";
		
		$_membership_info = MySQL::fetchRow( $_query );
		if ( !$_membership_info )
			return true;
	
		$_query = "UPDATE `".TBL_MEMBERSHIP."` SET `start_stamp`='".(time()-1)."', `expiration_stamp`='".(time()-1)."'
			WHERE `membership_id`='{$_membership_info['membership_id']}'";

		MySQL::affectedRows( $_query );

		// send notify mail
		$msg = app_Mail::createMessage();
		$msg->setRecipientProfileId($_transaction_info['profile_id'])
			->assignVar('membership_type', SK_Language::section('membership.types')->text($_transaction_info['membership_type_id']))
			->setTpl('membership_deleted_by_admin');
				
		// replace all profile's memberships following by the deleted
		if ( $_transaction_info['limit'] != 'unlimited' )
			self::replaceProfileMemberships( $_transaction_info['profile_id'], $_membership_info['start_stamp'] );
		
		app_Membership::CheckSubscriptionTrialMembership( $_transaction_info['profile_id'] );
		
		return true;
	}
	
	
	/**
	 * Replaces profile's memberships by time.
	 *
	 * @access private
	 * @param integer $profile_id
	 * @param integer $start_stamp
	 * @return boolean
	 */
	public static function replaceProfileMemberships( $profile_id, $start_stamp )
	{
		$profile_id = intval( $profile_id );
		$start_stamp = intval( $start_stamp );
		if ( !$profile_id || !$start_stamp )
			return false;

		$start_stamp = ( $start_stamp < time() )? time() : $start_stamp;
		
		// check if the profile has other membership following the deleted
		$_query = "SELECT `m`.*,`mt`.`type`,`mt`.`limit` FROM `".TBL_MEMBERSHIP."` AS `m` 
			INNER JOIN `".TBL_FIN_SALE."` AS `f` USING(`fin_sale_id`)
			INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` ON(`m`.`membership_type_id`=`mt`.`membership_type_id`)
			WHERE `f`.`profile_id`='$profile_id' AND `m`.`start_stamp`>'$start_stamp' ORDER BY `m`.`start_stamp` ASC LIMIT 1";

		$_membership_info = MySQL::fetchRow( $_query  );
	
		if ( !$_membership_info )
			return true;
		
		// check limit
		if ( $_membership_info['limit'] == 'limited' )
		{
			$_start_stamp = $start_stamp + 1;
			$_expiration_stamp = $_membership_info['expiration_stamp'] - $_membership_info['start_stamp'] + $_start_stamp;
		}
		else
		{
			$_start_stamp = $start_stamp + 1;
			$_expiration_stamp = $_start_stamp;
		}
		
		$_query = "UPDATE `".TBL_MEMBERSHIP."` SET `start_stamp`='$_start_stamp',`expiration_stamp`='$_expiration_stamp'
			WHERE `membership_id`='{$_membership_info['membership_id']}'";

		MySQL::affectedRows( $_query );
		
		return adminMembership::replaceProfileMemberships( $profile_id, $_expiration_stamp + 1 );
	}
	
	
	/**
	 * Deletes profile's membership using membership id if the membership has or 
	 * only profile id if the profile has unlimited subscription membership.
	 *
	 * @access public
	 * @param object $language
	 * @param integer $membership_id
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function deleteProfileMembership( $membership_id = 0, $profile_id = 0 )
	{
		$membership_id = intval( $membership_id );
		$profile_id  = intval( $profile_id );
		if ( !$membership_id && !$profile_id )
			return false;
		
		// call proper method
		return ( $membership_id )?
			self::deleteProfileMembershipUsingMembershipId( $membership_id, $profile_id )
			:
			self::deleteProfileUnlimitedMembershipUsingProfileId( $profile_id );
	}
	

	/**
	 * Deletes profile's unlimited subscription membership.
	 *
	 * @access private
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function deleteProfileUnlimitedMembershipUsingProfileId( $profile_id )
	{
		$profile_id  = intval( $profile_id );
		if ( !$profile_id )
			return false;
		
		// check if the profile has only unlimited subscription membership
		$query = SK_MySQL::placeholder("SELECT `mt`.* FROM `".TBL_PROFILE."` AS `p` 
			INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` USING( `membership_type_id` ) 
			WHERE `p`.`profile_id`=?", $profile_id);
		
		$_membership_info = SK_MySQL::query($query)->fetch_assoc();
		
		if ( $_membership_info['type'] != 'subscription' || $_membership_info['limit'] != 'unlimited' )
			return false;
		
		// get system default membership type
		$_def_membership_type_id = SK_Config::section('membership')->get('default_membership_type_id');
		
		// send notify mail
		$msg = app_Mail::createMessage();
		$msg->setRecipientProfileId($profile_id)
			->setTpl('membership_deleted_by_admin')
			->assignVar('membership_type', SK_Language::section('membership.types')->text($_membership_info['membership_type_id']));
		app_Mail::send($msg);
		
		// check if it is the same membership type
		if ( $_def_membership_type_id == $_membership_info['membership_type_id'] )
			return true;
		
		// update profile membership type
		return ( MySQL::affectedRows( "UPDATE `".TBL_PROFILE."` SET `membership_type_id`='$_def_membership_type_id' WHERE `profile_id`='$profile_id'" ) )? true : false;
	}
	
	
	/**
	 * Deletes profile's membership using membership id.
	 *
	 * @access private
	 * @param object $language
	 * @param integer $membership_id
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function deleteProfileMembershipUsingMembershipId( $membership_id, $profile_id = 0 )
	{
		$membership_id = intval( $membership_id );
		$profile_id  = intval( $profile_id );
		if ( !$membership_id )
			return false;
		
		// get the membership info:
		$_query = ( $profile_id )?
			"SELECT `mt`.`type`,`mt`.`limit`,`m`.`start_stamp`,`m`.`fin_sale_id`,`m`.`membership_id`,$profile_id AS `profile_id`,`m`.`membership_type_id`,`m`.`prev_membership_type_id` 
				FROM `".TBL_MEMBERSHIP."` AS `m` 
				INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` USING( `membership_type_id` ) WHERE `m`.`membership_id`='$membership_id'" :
			"SELECT `mt`.`type`,`mt`.`limit`,`m`.`start_stamp`,`f`.`profile_id`,`m`.`fin_sale_id`,`m`.`membership_id`,`m`.`membership_type_id` 
				FROM `".TBL_MEMBERSHIP."` AS `m`
				INNER JOIN `".TBL_FIN_SALE."` AS `f` USING( `fin_sale_id` )
				INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` ON( `m`.`membership_type_id`=`mt`.`membership_type_id` ) 
				WHERE `m`.`membership_id`='$membership_id'";
		
		$_membership_info = SK_MySQL::query($_query)->fetch_assoc();
		if ( !$_membership_info )
			return false;
		
		// add transaction into refund list/table
		RefundOrder( $_membership_info['fin_sale_id'], 0, 'Profile membership deleted by admin.' );

		$current_membership = app_Membership::profileCurrentMembershipInfo( $_membership_info['profile_id'] );
		
		$query = SK_MySQL::placeholder("SELECT `m`.* FROM `".TBL_FIN_SALE."` AS `f` 
			INNER JOIN `".TBL_MEMBERSHIP."` AS `m` USING(`fin_sale_id`)
			WHERE `f`.`profile_id`=? AND `m`.`membership_id`!=? AND `m`.`start_stamp`>=? 
			ORDER BY `m`.`start_stamp` LIMIT 1", $_membership_info['profile_id'], $membership_id, $_membership_info['start_stamp']);
		
		$next_membership = SK_MySQL::query($query)->fetch_assoc();
		
		if( $current_membership['membership_id'] == $membership_id )
		{
			if( $next_membership )
			{
				$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE."` SET `membership_type_id`=? 
					WHERE `profile_id`=?", $next_membership['membership_type_id'], $_membership_info['profile_id'] );
				SK_MySQL::query($query);
				
				$query = SK_MySQL::placeholder("UPDATE `".TBL_MEMBERSHIP."` SET `prev_membership_type_id`=? 
					WHERE `membership_id`=?", $_membership_info['prev_membership_type_id'], $next_membership['membership_id'] );
				SK_MySQL::query($query);
									
				self::replaceProfileMemberships( $_membership_info['profile_id'], $_membership_info['start_stamp'] );
			}
			else 
			{
				$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE."` SET `membership_type_id`=? 
					WHERE `profile_id`=?", $_membership_info['prev_membership_type_id'], $_membership_info['profile_id']);
				SK_MySQL::query($query);
			}
		}
		else
		{
			if( $next_membership )
			{
				$query = SK_MySQL::placeholder("UPDATE `".TBL_MEMBERSHIP."` SET `prev_membership_type_id`=? 
					WHERE `membership_id`=?", $_membership_info['prev_membership_type_id'], $next_membership['membership_id']);
				SK_MySQL::query($query);
				
				self::replaceProfileMemberships( $_membership_info['profile_id'], $_membership_info['start_stamp'] );
			}
		}

		$query = SK_MySQL::placeholder("DELETE FROM `".TBL_MEMBERSHIP."` WHERE `membership_id`=?", $membership_id);
		SK_MySQL::query($query);
		
		// get profile info
		$_profile_info = app_Profile::getFieldValues( $_membership_info['profile_id'], array( 'username', 'email' ) );
		// send notify mail
		$msg = app_Mail::createMessage();
		$msg->setRecipientProfileId($_membership_info['profile_id'])
			->setTpl('membership_deleted_by_admin')
			->assignVar('membership_type', SK_Language::section('membership.types')->text($_membership_info['membership_type_id']));
		app_Mail::send($msg);
		
		app_Membership::CheckSubscriptionTrialMembership( $_membership_info['profile_id'] );
		
		return true;
	}
	
	
	/**
	 * Returns membership type list which can be given.
	 *
	 * @access public
	 * @return array
	 */
	public static function getGivenMembershipTypes( $sex = null )
	{
		if ($sex){
			return MySQL::fetchArray( "SELECT * FROM `".TBL_MEMBERSHIP_TYPE."` WHERE `membership_type_id`!=1 
				AND (`available_for` & ".$sex.") AND `paid_by_sms`=0" );
		}
		else
			return MySQL::fetchArray( "SELECT * FROM `".TBL_MEMBERSHIP_TYPE."` WHERE `membership_type_id`!=1 AND `paid_by_sms`=0" );
	}


	/**
	 * Gives membership to a profile.
	 *
	 * @access public
	 * @param object &$language
	 * @param integer $profile_id
	 * @param array $membership_info=>array
	 * (
	 * 		'membership_type_id'=>1,
	 * 		'amount'=>12.23,
	 * 		'period'=>3,
	 * 		'units'=>'days'
	 * )
	 * @return boolean
	 */
	public static function GiveMembershipToProfile( $profile_id, $membership_info )
	{
		$profile_id = intval( $profile_id );
		$_membership_type_id = intval( $membership_info['membership_type_id'] );
		if ( !$profile_id || !$_membership_type_id )
			return false;
		
		//get the given membership info
		$_given_membership_type = app_Membership::GetMembershipTypeInfo( $_membership_type_id );
		
		// get profile info
		$_profile_info = app_Profile::getFieldValues($profile_id, array( 'profile_id', 'language_id', 'email', 'username' ) );
		
		if ( !$_given_membership_type || !$_profile_info )
			return false;
				
		if ( $_given_membership_type['limit'] == 'unlimited' && $_given_membership_type['type'] == 'subscription' )
		{
			// check profile memberships
			$_current_membership_info = app_Membership::profileCurrentMembershipInfo( $profile_id );
			if ( $_current_membership_info['type'] == 'subscription' && $_current_membership_info['limit'] == 'unlimited' )
			{
				// replace profile's current membership
				$query = SK_MySQL::placeholder("UPDATE `".TBL_PROFILE."` SET `membership_type_id`=? 
					WHERE `profile_id`=?", $_membership_type_id, $profile_id);

				SK_MySQL::query($query);	
			}
			else
			{
				// get the profile's first membership
				$query = SK_MySQL::placeholder("SELECT `m`.`membership_id` FROM `".TBL_PROFILE."` AS `p`
					INNER JOIN `".TBL_FIN_SALE."` AS `fs` USING(`profile_id`)
					INNER JOIN `".TBL_MEMBERSHIP."` AS `m` ON(`fs`.`fin_sale_id`=`m`.`fin_sale_id`)
					INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` ON(`m`.`membership_type_id`=`mt`.`membership_type_id`)
					WHERE `p`.`profile_id`=? ORDER BY `m`.`start_stamp` LIMIT 1", $profile_id);
				
				$_membership_id = SK_MySQL::query($query)->fetch_cell();
				
				//replace first membership's previous membership type
				$query = SK_MySQL::placeholder("UPDATE `".TBL_MEMBERSHIP."` SET `prev_membership_type_id`=? 
					WHERE `membership_id`=?", $_membership_type_id, $_membership_id);
				
				SK_MySQL::query($query);	
			}

			//sending mail
			self::sendNotifyGivenMembershipMail( 'admin_give_unlimited_membership', $_profile_info, $_membership_type_id, $_membership_id, $membership_info['amount'] );
		}
		elseif( $_given_membership_type['type'] == 'subscription' && intval($membership_info['period']) )
		{
			// Put transaction info into the sale table. And get sale's id.
			$_sale_id = app_Finance::FillSale( $profile_id, 0, 'admin', $membership_info['units'], $membership_info['period'], 0, $_membership_type_id );

			if ( $_sale_id === false )
				return false;
			
			// put info into membership table
			$_membership_id = app_Membership::BuySubscriptionTrialMembership( $profile_id, $_membership_type_id, $membership_info['period'], $membership_info['units'], $_sale_id );
		
			//sending mail
			self::sendNotifyGivenMembershipMail( 'admin_give_membership', $_profile_info, $_membership_type_id, $_membership_id, $membership_info['amount'] );
		}
		elseif ( $_given_membership_type['type'] == 'trial' && intval($membership_info['period']) )
		{
			// Put transaction info into the sale table. And get sale's id.
			$_sale_id = app_Finance::FillSale( $profile_id, 0, 'trial', $membership_info['units'], $membership_info['period'], 0, $_membership_type_id );
					
			if ( $_sale_id === false )
				return false;
			
			// put info into membership table
			$_membership_id = app_Membership::BuySubscriptionTrialMembership($profile_id, $_membership_type_id, $membership_info['period'], $membership_info['units'], $_sale_id );
			
			//sending mail
			self::sendNotifyGivenMembershipMail( 'admin_give_trial_membership', $_profile_info, $_membership_type_id, $_membership_id, $membership_info['amount'] );
		}
		else
			return false;

		return true;
	}

	
	/**
	 * Sends notify mail making all needed replacing.
	 *
	 * @access private
	 * @param string $mail_template
	 * @param array $profile_info
	 * @param integer $membership_type_id
	 * @param integer $membership_id
	 * @param decimal $amount
	 * @return boolean
	 */
	public static function sendNotifyGivenMembershipMail( $mail_template, $profile_info, $membership_type_id, $membership_id = 0, $amount = 0 )
	{
		if ( !strlen($mail_template) || !$profile_info['email'] )
			return false;
		
		$_replace_arr = $profile_info;
		
		$membership_id = intval( $membership_id );
		$membership_type_id = intval( $membership_type_id );
		
		$_replace_arr['membership_type'] = SK_Language::section('membership')->section('types')->text($membership_type_id); 
		if ( $amount > 0 )
			$_replace_arr['amount'] = $amount;
		
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MEMBERSHIP."` WHERE `membership_id`=?", $membership_id);
		
		$_membership_info =  SK_MySQL::query($query)->fetch_assoc();

		if ( $_membership_info ) //it means that not subscription unlimited membership was given
		{			
			$_replace_arr['start_date'] = SK_I18n::getSpecFormattedDate($_membership_info['start_stamp'], false, true);
			$_replace_arr['end_date'] = SK_I18n::getSpecFormattedDate($_membership_info['expiration_stamp'], false, true);
		}
		
		$msg = app_Mail::createMessage()
				->setRecipientProfileId($profile_info['profile_id'])
				->setTpl($mail_template)
				->assignVarRange($_replace_arr);
				
		return app_Mail::send($msg);
	}
	
	
	public static function getMembershipTypeIconUrl( $params )
	{
		$membership_type_id = intval( $params['type_id'] );
		if( !$membership_type_id )
			return '';
		
		$query = SK_MySQL::placeholder("SELECT `has_icon` from `".TBL_MEMBERSHIP_TYPE."` 
			WHERE membership_type_id=?", $membership_type_id);
		
		$_has_icon = SK_MySQL::query($query)->fetch_cell();
		
		if($_has_icon)
			return URL_USERFILES.'membership_type_icon_'.$membership_type_id.'.png';
		else 
			return '';
	}
	
	public static function isMembershipTypeIconExist( $membership_type_id )
	{
		$query = SK_MySQL::placeholder("SELECT `has_icon` FROM `".TBL_MEMBERSHIP_TYPE."` 
			WHERE membership_type_id=?", $membership_type_id);
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	public static function setMembershipTypeHasIcon($membership_type_id)
	{
		$query = SK_MySQL::placeholder("UPDATE `".TBL_MEMBERSHIP_TYPE."` SET has_icon='1' 
			WHERE membership_type_id=?", $membership_type_id);
		
		SK_MySQL::query($query);
		return  SK_MySQL::affected_rows();
	}
	
	public static function setMembershipTypeHasNotIcon($membership_type_id)
	{
		$query = SK_MySQL::placeholder("UPDATE `".TBL_MEMBERSHIP_TYPE."` SET has_icon='0' 
			WHERE membership_type_id=?", $membership_type_id);
		
		SK_MySQL::query($query);
		return  SK_MySQL::affected_rows();
	}
	
	public static function ServiceIsAvailableForGuest($service)
	{
		$query = SK_MySQL::placeholder( "SELECT `membership_service_key` FROM `".TBL_MEMBERSHIP_SERVICE."`
			WHERE `membership_service_key`='?' 
			AND `is_available_for_guest`='y' LIMIT 1", $service );
		
		$serv = SK_MySQL::query($query)->fetch_cell();
	
		return (bool)$serv;
	}
	
	public static function ServiceIsLimited($service)
	{
		$query = MySQL::placeholder( "SELECT `membership_service_key` FROM `".TBL_MEMBERSHIP_SERVICE."`
			WHERE `membership_service_key`='?' 
			AND `is_limited`='y' LIMIT 1", $service );
		
		$serv = MySQL::query($query)->fetch_cell();
	
		return (bool)$serv;
	}

}
?>