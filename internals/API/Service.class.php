<?php

class SK_Service
{
    private static $serviceInfoCache = array();
    private static $membershipTypeCache = array();
    private static $limitedServiceList = array();
    private static $guestServiceCache = array();

    private static function initCache()
    {
        $query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_MEMBERSHIP_SERVICE."` INNER JOIN `".TBL_FEATURE."` USING(`feature_id`)");
        $result = SK_MySQL::queryForList($query);

        foreach ( $result as $item )
        {
            self::$serviceInfoCache[$item['membership_service_key']] = $item;
            if( $item['is_limited'] == 'y' )
            {
                self::$limitedServiceList[] = $item['membership_service_key'];
            }
        }

        if( !SK_HttpUser::is_authenticated() )
        {
            $query = SK_MySQL::placeholder( "SELECT `membership_service_key` FROM `".TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE."` WHERE `membership_type_id`='1'");
            $result = SK_MySQL::queryForList($query);

            foreach ( $result as $item )
            {
                self::$guestServiceCache[] = $item['membership_service_key'];
            }
        }
		
    }

    private $profile_id;
	
	private $service_key;

    private $use_credits;
    
    private $credits_cost = null;
    
	public $permission_message;
		
	// Membership service states
	const SERVICE_NO 			= 0;
	const SERVICE_FULL 			= 1;
	const SERVICE_DENIED 		= 2;
	const SERVICE_NO_CREDITS 	= 4;
	const SERVICE_GUEST			= 8;
	const SERVICE_LIMITED		= 16; 
	
	/**
	 * Constructor.
	 *
	 * @param string $service_key
	 * @param integer $profile_id
	 */
	public function __construct( $service_key, $profile_id = null ) 
	{
		if ( !strlen($service_key) ) {
			throw new SK_ServiceException(
				'empty argument $service_key',
				SK_ServiceException::EMPTY_ARGUMENT_SERVICE_KEY
			);
		}

        if( empty(self::$serviceInfoCache) )
        {
            self::initCache();
        }

		$this->service_key = $service_key;
		
		$this->profile_id = isset($profile_id) ? $profile_id : SK_HttpUser::profile_id();
		
		$this->use_credits = false;
	}
	
	public function getServiceKey()
	{
        return $this->service_key;
	}
	
	public function useCredits()
	{
        return $this->use_credits;
	}
	
	public function setCreditsCost( $credits )
	{
        $this->credits_cost = $credits;
	}
	
	/**
	 * Returns service info.
	 *
	 * @return array
	 */
	private function getServiceInfo()
	{
        return self::$serviceInfoCache[$this->service_key];
	}
	
	public function getServiceCreditsCost()
	{
        $info = $this->getServiceInfo();
        
        return $info['credits'];
	}
	
	/**
	 * Checks profile permissions for the service.
	 * 
	 * Returns:
	 * <ul>
	 *   <li>SERVICE_FULL        - the service is available</li>
	 *   <li>SERVICE_DENIED      - profile has no access for this service</li>
	 *   <li>SERVICE_NO_CREDITS  - profile has not enough credits to use this service</li>
	 *   <li>SERVICE_GUEST       - the service is not available for a GUESTs</li>
	 *   <li>SERVICE_LIMITED     - profile exhausted the service limit</li>
	 *   <li>SERVICE_NO          - service disabled</li>
	 * </ul>
	 *
	 * @return integer
	 */
	public function checkPermissions()
	{
		$service_info = self::getServiceInfo();

		$this->use_credits = false;
		
		#check if the feature to which the service belongs is active:
		if ( $service_info['active'] == 'no' ) {
			$this->permission_message = self::getPermissionMessage( self::SERVICE_NO );
			return self::SERVICE_NO;
		}
		
		#check profile permissions

		if ( !intval($this->profile_id) )
		{
                    #check if the service in promo action
			if ( $service_info['is_promo'] == 'yes' && self::isAvailableForGuest() ) {
				$this->permission_message = self::getPermissionMessage( self::SERVICE_FULL ); 
				return self::SERVICE_FULL;
			}
			
			if ( in_array($this->service_key, self::$guestServiceCache) ) {
				$this->permission_message = self::getPermissionMessage( self::SERVICE_FULL ); 	
				return self::SERVICE_FULL;
			}
			else
			{
				$this->permission_message = self::getPermissionMessage( self::SERVICE_GUEST ); 
				return self::SERVICE_GUEST;
			}
		}
		else
		{
			#check if the service in promo action
			if ( $service_info['is_promo'] == 'yes' ) {
				$this->permission_message = self::getPermissionMessage( self::SERVICE_FULL ); 
				return self::SERVICE_FULL;
			}

            if( !isset(self::$membershipTypeCache[$this->profile_id][$this->service_key]) )
            {
                #get profile membership's type
                $query = SK_MySQL::placeholder("SELECT `mt`.`type`
                    FROM `".TBL_MEMBERSHIP_LINK_MEMBERSHIP_TYPE_SERVICE."` AS `ms`
                    INNER JOIN `".TBL_PROFILE."` AS `p` USING(`membership_type_id`)
                    INNER JOIN `".TBL_MEMBERSHIP_TYPE."` AS `mt` USING(`membership_type_id`)
                    WHERE `p`.`profile_id`=? AND `ms`.`membership_service_key`='?' LIMIT 1", $this->profile_id, $this->service_key);

                self::$membershipTypeCache[$this->profile_id][$this->service_key] = SK_MySQL::query($query)->fetch_cell();
            }

			$membership_type = self::$membershipTypeCache[$this->profile_id][$this->service_key];
			
			#check if the service allowed by admin
			if ( $membership_type )
			{
    			#check the service limit
                if ( self::isLimited() && self::getLimit(app_Membership::getProfileMembershipTypeId($this->profile_id)) <= self::getUsageCount() )
                {
                    $this->permission_message = self::getPermissionMessage( self::SERVICE_LIMITED );
                    $return = self::SERVICE_LIMITED;
                }
                else 
                {
                    $this->permission_message = self::getPermissionMessage( self::SERVICE_FULL );
                    return self::SERVICE_FULL;
                }
			}
			else
			{
			    $this->permission_message = self::getPermissionMessage( self::SERVICE_DENIED );
                $return = self::SERVICE_DENIED;
			}
			
			// we can try to use credits
            if ( app_Features::isAvailable(44) && app_UserPoints::useCreditsForService($this->service_key) )
            {
                $servCost = $this->credits_cost ? $this->credits_cost : app_UserPoints::getServiceCost($this->service_key);
                
                if ( $servCost )
                {
                    $enoughCredits = app_UserPoints::checkBalance($this->profile_id, $servCost);
                    
                    if ( !$enoughCredits )
                    {
                        $service_label = SK_Language::text('%membership.services.' . $this->service_key);
                        $this->permission_message = app_UserPoints::getErrorMessage($service_label, 'all');
                        $return = self::SERVICE_NO_CREDITS;
                    }
                    else
                    {
                        $this->permission_message = self::getPermissionMessage(self::SERVICE_FULL);
                        $return = self::SERVICE_FULL;
                        $this->use_credits = true;
                    }
                }
            }

            return $return;
		}
	}
	
	/**
	* Returns service permission message.
	*
	* @param string $state
	*
	* @return array
	*/
	private function getPermissionMessage($state = null)
	{
		if ( !in_array($state, array(
				self::SERVICE_NO,
				self::SERVICE_DENIED, 
				self::SERVICE_NO_CREDITS, 
				self::SERVICE_GUEST, 
				self::SERVICE_LIMITED)
			) )
				return $state;
		
		$msg = array();
		$payment_page = SK_Navigation::href('payment_selection');
			
		switch ( $state )
		{
			case self::SERVICE_NO:
				$str = SK_Language::section('membership')->text('service_feature_inactive_for_alert');
				$msg['alert'] = str_replace( array('{$service}'),  array(SK_Language::section('membership.services')->text($this->service_key)), $str );
				  
				$str = SK_Language::section('membership')->text( 'service_feature_inactive_for_alert');
				$msg['message'] = str_replace( array('{$service}'),  array(SK_Language::section('membership.services')->text($this->service_key)), $str );
				return $msg;
				
			case self::SERVICE_DENIED:
				$str = SK_Language::section('membership')->text('service_no_permission_for_alert');
				$msg['alert'] = str_replace( array('{$service}', '<url>', '</url>'),  array(SK_Language::section('membership.services')->text($this->service_key), '<a href="'.$payment_page.'">', '</a>'), $str );
				  
				$str = SK_Language::section('membership')->text( 'service_no_permission');
				$msg['message'] = str_replace( array('{$service}', '<url>', '</url>'),  array(SK_Language::section('membership.services')->text($this->service_key), '<a href="'.$payment_page.'" target="_blank">', '</a>'), $str );
				return $msg;				
				
			case self::SERVICE_NO_CREDITS:
				$str = SK_Language::section('membership')->text( 'service_permission_no_credits_for_alert' );
				$msg['alert'] = str_replace( array('{$service}', '<url>', '</url>'), array(SK_Language::section('membership.services')->text($this->service_key), '<a href="'.$payment_page.'">', '</a>'), $str );

				$str = SK_Language::section('membership')->text( 'service_permission_no_credits');
				$msg['message'] = str_replace( array('{$service}', '<url>', '</url>'), array(SK_Language::section('membership.services')->text($this->service_key), '<a href="'.$payment_page.'" target="_blank">', '</a>'), $str );
				return $msg; 

			case self::SERVICE_GUEST:
				$str = SK_Language::section('membership')->text('service_no_permission_for_guest_for_alert');  
				$msg['alert'] = str_replace( '{$service}', SK_Language::section('membership.services')->text($this->service_key), $str );
				
				$str = SK_Language::section('membership')->text('service_no_permission_for_guest');
				$msg['message'] = str_replace( array('{$service}', '<url>', '</url>'), array(SK_Language::section('membership.services')->text($this->service_key), '<a href="#" onclick="SK_SignIn(); return false">', '</a>'), $str );
				return $msg;
				
			case self::SERVICE_LIMITED:
				$str = SK_Language::section('membership')->text('service_no_permission_over_the_limit_for_alert');
				$msg['alert'] = str_replace( array('{$service}', '<url>', '</url>'), array(SK_Language::section('membership.services')->text($this->service_key), '<a href="'.$payment_page.'">', '</a>'), $str );
				
				$str = SK_Language::section('membership')->text( 'service_no_permission_over_the_limit');
				$msg['message'] = str_replace( array('{$service}', '<url>', '</url>'), array(SK_Language::section('membership.services')->text($this->service_key), '<a href="'.$payment_page.'" target="_blank">', '</a>'), $str );
				return $msg;
							
			default:
				return $state;
		}
	}
	
	/**
	* Checks if the service is limited.
	*
	* @return boolean
	*/
	public function isLimited()
	{
        return in_array($this->service_key, self::$limitedServiceList);
	}
	
	/**
	 * Checks if the service is available for guest.
	 *
	 * @return boolean
	 */
	public function isAvailableForGuest()
	{
		$query = MySQL::placeholder( "SELECT `membership_service_key` FROM `".TBL_MEMBERSHIP_SERVICE."`
			WHERE `membership_service_key`='?' 
			AND `is_available_for_guest`='y' LIMIT 1", $this->service_key );
		
		$service = MySQL::query($query)->fetch_cell();
	
		return (bool)$service;
	}
	
	/**
	* Returns the number of service usage by a profile
	*
	* @return boolean|integer
	*/
	public function getUsageCount()
	{
		if( !$this->profile_id )
			return false;
		
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM `".TBL_MEMBERSHIP_SERVICE_TRACK."` 
			WHERE `profile_id`=? AND `membership_service_key`='?'", $this->profile_id, $this->service_key );
		
		$service_count = SK_MySQL::query($query)->fetch_cell();
		
		return intval($service_count);
	}

	/**
	* Returns limit of the service
	* for specified membership type
	*
	* @param integer $membership_type_id
	*
	* @return integer
	*/
	public function getLimit($membership_type_id)
	{
		$query = SK_MySQL::placeholder( "SELECT `limit` FROM `".TBL_MEMBERSHIP_LINK_SERVICE_LIMIT."` 
			WHERE `membership_service_key`='?' 
			AND `membership_type_id`=?", $this->service_key, $membership_type_id );
		
		$limit = SK_MySQL::query($query)->fetch_cell();

		return ($limit == 0) ? 1000000 : $limit;
	}
	
	/**
	* Tracks profile using of service. 
	* Tracks service use count and updates(decreases) profiles credits.
	*/
	public function trackServiceUse()
	{
		if ( !($this->profile_id) )
			return;

		$service_info = self::getServiceInfo();
        
        #check if the service has promo status (free and available for all)
        if ( $service_info['is_promo'] == 'yes' )
            return;
			
		if ( $this->use_credits )
		{
		    $servCost = $this->credits_cost ? $this->credits_cost : app_UserPoints::getServiceCost($this->service_key);
                
            if ( $servCost )
            {
                $enoughCredits = app_UserPoints::checkBalance($this->profile_id, $servCost);
                    
                if ( $enoughCredits )
                {
                    $updated = app_UserPoints::updateProfilePointsBalance($this->profile_id, $servCost, 'dec');
                    if ( $updated )
                    {
                        app_UserPoints::logAction($this->profile_id, $this->service_key, $servCost, true, '-');
                    }
                }
            }
		}
		else
		{
            #track service
            if ( self::isLimited() ) 
            {
                $query = SK_MySQL::placeholder("INSERT INTO `".TBL_MEMBERSHIP_SERVICE_TRACK."` 
                    (`profile_id`,`membership_service_key`,`time_stamp`) 
                    VALUES(?,'?',?)", $this->profile_id, $this->service_key, time());
                
                SK_MySQL::query($query);
            }
		}
	}
	
}


class SK_ServiceException
{
	const	EMPTY_ARGUMENT_SERVICE_KEY = 1;
	
}







