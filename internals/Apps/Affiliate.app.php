<?php

/**
 * Class for working with affiliates
 *
 * @package SkaDate
 * @subpackage SkaDate5
 * @link http://www.skadate.com
 * @version 5.0
 */
class app_Affiliate
{
	
	/**
	 * Returns affiliate config value.
	 *
	 * @param string $name
	 * @return string
	 */
	public static function getAffiliateConfig( $name )
	{
		$config = MySQL::fetchArray( "SELECT `name`,`value` FROM `".TBL_AFFILIATE_PLAN."` WHERE 1", 1 );
		
		if( !isset( $config[$name] ) || $config[$name] === '' )
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() undefined config var: '.$name , E_USER_WARNING);
		
		return $config[$name];
	}

	
	/**
	 * Checks and catches visits for traffic.
	 *
	 * @return none
	 */
	public static function AffiliateProgram()
	{
		// check if is a robot or not:
		$affiliate_id = intval( @$_GET['aff_id'] );
		if ( isset($_COOKIE['affiliate']) && strlen( $_COOKIE['affiliate'] ) )
		{			
			$affiliate_arr = explode( ',', $_COOKIE['affiliate'] );
			$affiliate_arr[1] = intval( $affiliate_arr[1] );
			
			if ( $affiliate_arr[1] & 1 )
				return;
			
			// get last visit from this ip(when affiliate id was passed in GET array):
			$visit = MySQL::fetchRow( sql_placeholder( "SELECT `time_stamp`,`type` FROM `".TBL_AFFILIATE_VISIT."` WHERE 
				`ip`=INET_ATON(?)", $_SERVER['REMOTE_ADDR'] ) );
			$last_visit_time = $visit['time_stamp'];
			
			// check period and type\level
			if ( ( $visit['type'] & 1 ) && ( time() - (int)$last_visit_time ) < (int)self::getAffiliateConfig( 'period' ) )
				return;
			
			// insert last time from this ip:
			if ( !$last_visit_time )
			{
				self::catchForTraffic( $affiliate_arr[0], $last_visit_time );
				SK_HttpUser::set_cookie( 'affiliate', $affiliate_arr[0].',1', self::getAffiliateConfig( 'period' ) );
				MySQL::fetchResource( sql_placeholder("INSERT INTO `".TBL_AFFILIATE_VISIT."` (`ip`,`time_stamp`,`type`) VALUES(INET_ATON(?),'".time()."','1')", $_SERVER['REMOTE_ADDR']) );
			}
			else
			{
				if ( ( time() - (int)$last_visit_time ) > (int)self::getAffiliateConfig( 'period' ) )
				{
					// get visits (traffic):
					self::catchForTraffic( $affiliate_arr[0], $last_visit_time );
					SK_HttpUser::set_cookie( 'affiliate', $affiliate_arr[0].',1', self::getAffiliateConfig( 'period' ));
					MySQL::fetchResource( "UPDATE `".TBL_AFFILIATE_VISIT."` SET `time_stamp`='".time()."',`type`='1' WHERE 
						`ip`=INET_ATON('{$_SERVER['REMOTE_ADDR']}')" );
				}
				elseif( !( $visit['type'] & 1 ) )
				{
					self::catchForTraffic( $affiliate_arr[0], $last_visit_time );
					SK_HttpUser::set_cookie( 'affiliate', $affiliate_arr[0].','.($visit['type'] + 1), self::getAffiliateConfig( 'period' ) );
					MySQL::fetchResource( "UPDATE `".TBL_AFFILIATE_VISIT."` SET `time_stamp`='".time()."',`type`=`type`+1 WHERE 
						`ip`=INET_ATON('{$_SERVER['REMOTE_ADDR']}')" );
				}
			}
		}
		elseif ( $affiliate_id )
		{
			$url =  sk_make_url(null, array('aff_id'=> null));

			// save in cookie:
			SK_HttpUser::set_cookie('affiliate', $affiliate_id.',0', self::getAffiliateConfig( 'period' ) );
			SK_HttpRequest::redirect( $url );
			exit();
		}
	}
	
	
	/**
	 * Catches visits for traffic.
	 *
	 * @param string $affiliate
	 * @return boolean
	 */
	public static function catchForTraffic( $affiliate_id, $last_visit_time )
	{
	    if ( SK_Navigation::isCurrentArea(SITE_URL . 'admin/') )
	    {
	       return false;
	    }
	    
		$affiliate_id = intval( $affiliate_id );
		if ( !$affiliate_id )
			return false;
		
		if ( !self::isActiveAffiliate( $affiliate_id ) )
			return false;
			
		// update last time from this ip:
		MySQL::fetchResource( "UPDATE `".TBL_AFFILIATE_VISIT."` SET `time_stamp`='".time()."',`type`=`type`+1 WHERE 
			`ip`=INET_ATON('{$_SERVER['REMOTE_ADDR']}')" );
		
		// catch traffic:
		$amount = self::getAffiliateConfig( 'traffic_amount' );
		MySQL::fetchResource( sql_placeholder("INSERT INTO `".TBL_AFFILIATE_TRAFFIC."` (`time_stamp`,`amount`,`affiliate_id`) VALUES( ?, ?, ? )", time(), (int)$amount, (int)$affiliate_id) );
	
		// count clicks:
		$url = $_SERVER['HTTP_REFERER'].'?';
		preg_match("/^([0-9a-zA-Z_:\/.-]+)\?/", $url, $url_arr);
		$url = ( $url_arr[1] )? $url_arr[1] : 'empty';
		( MySQL::fetchRow( sql_placeholder( "SELECT * FROM `?#TBL_AFFILIATE_SITE` WHERE `affiliate_id`=? AND `site`=?", $affiliate_id, $url ) ) )?
			MySQL::fetchResource( sql_placeholder( "UPDATE `?#TBL_AFFILIATE_SITE` SET `count`=`count`+1 WHERE `affiliate_id`=? AND `site`=?", $affiliate_id, $url ) ) :
			MySQL::fetchResource( sql_placeholder( "INSERT INTO `?#TBL_AFFILIATE_SITE` VALUES( ?@ )", array( $affiliate_id, $url, 1 ) ) );

		return true;
	}
	
	
	/**
	 * Checks and catches affiliate registrations. Returns affiliate id.
	 *
	 * @param integer $profile_id
	 * @return integer
	 */
	public static function catchForRegistration( $profile_id )
	{
		$profile_id = intval( $profile_id );
		if ( !$profile_id )
			return 0;
		
		// check cookie:
		if (!strlen( @$_COOKIE['affiliate'] ) )
			return 0;
		
		$affiliate_arr = explode( ',', @$_COOKIE['affiliate'] );
		
		$affiliate_arr[0] = intval( $affiliate_arr[0] );
		$affiliate_arr[1] = intval( $affiliate_arr[1] );
		
		if ( !self::isActiveAffiliate( $affiliate_arr[0] ) )
			return 0;
		
		if ( !( $affiliate_arr[1] & 2 ) )
		{
			// get last visit from this ip (when affiliate id was passed in GET array):
			$visit = MySQL::fetchRow( "SELECT `time_stamp`,`type` FROM `".TBL_AFFILIATE_VISIT."` WHERE `ip`=INET_ATON('{$_SERVER['REMOTE_ADDR']}')" );
			if ( $visit['type'] & 2 )
				return false;
				
			$last_visit_time = $visit['time_stamp'];
			
			// check time period:
			if ( ( time() - (int)$last_visit_tme ) > (int)self::getAffiliateConfig( 'period' ) )
			{
				$amount = self::getAffiliateConfig( 'registration_amount' );
				MySQL::fetchResource( sql_placeholder("INSERT INTO `".TBL_AFFILIATE_REGISTRATION."` (`profile_id`,`time_stamp`,`amount`,`affiliate_id`)
					VALUES( ?, ?, ?, ? )", $profile_id, time(), $amount, $affiliate_arr[0]));
				
				// update profile field affiliate_id
				MySQL::fetchResource( sql_placeholder("UPDATE `".TBL_PROFILE."` SET `affiliate_id`=? WHERE `profile_id`=?", $affiliate_arr[0], $profile_id));
				$str = $affiliate_arr[0].','.(intval( $affiliate_arr[1] ) + 2);
				SK_HttpUser::set_cookie('affiliate',$str,self::getAffiliateConfig( 'period' ) );
				
				// update visit info
				MySQL::fetchResource( "UPDATE `".TBL_AFFILIATE_VISIT."` SET `time_stamp`='".time()."',`type`=`type`+2 WHERE 
					`ip`=INET_ATON('{$_SERVER['REMOTE_ADDR']}')" );
				
				return $affiliate_arr[0];
			}
		}
	
		return 0;
	}
	
	
	/**
	 * Checks and catches affiliate subscriptions. Returns true if it is affiliate subscription.
	 *
	 * @param integer $profile_id
	 * @param integer $fin_sale_id
	 * @param decimal $amount
	 * @return boolean
	 */
	public static function catchForSale( $profile_id, $fin_sale_id, $amount )
	{
		$fin_sale_id = intval( $fin_sale_id );
		$profile_id = intval( $profile_id );
		if ( !$profile_id || !$fin_sale_id || !is_numeric( $amount ) )
			return false;
		
		// get profile's affiliate id:
		$affiliate_id = app_Profile::getFieldValues($profile_id, 'affiliate_id');

		// check if the profile is an affiliate profile:
		if ( !$affiliate_id )
			return false;

		if ( !self::isActiveAffiliate( $affiliate_id ) )
			return false;
		
		$sale_type_value = self::getAffiliateConfig( 'subscription_type_value' );
		$amount = ( $sale_type_value == 'subscription_amount' )?
			self::getAffiliateConfig( 'subscription_amount' ) : $amount * self::getAffiliateConfig( 'subscription_percent' ) / 100;
		
		MySQL::fetchResource( sql_placeholder("INSERT INTO `".TBL_AFFILIATE_SUBSCRIPTION."` (`fin_sale_id`,`time_stamp`,`amount`,`affiliate_id`)
			VALUES( ?, ?, ?, ? )", (int)$fin_sale_id, time(), (int)$amount, (int)$affiliate_id) );
		
		return true;
	}
	
	
	/**
	 * Checks if it is an active affiliate.
	 *
	 * @param integer $affiliate_id
	 * @return boolean
	 */
	public static function isActiveAffiliate( $affiliate_id )
	{
		$affiliate_id = intval( $affiliate_id );
		
		if ( !$affiliate_id )
			return false;
		
		$affiliate = MySQL::fetchRow( sql_placeholder("SELECT * FROM `".TBL_AFFILIATE."` WHERE `affiliate_id`=?", $affiliate_id) );
		
		return ( $affiliate['active'] == 'yes' )? true : false;
	}
	
	/**
	 * Inserts a new affiliate into affiliate table.
	 *
	 * @param array $affiliate
	 * @return integer|boolean affiliate id OR false if the inserting failed
	 */
	public static function insertNewAffiliate( $affiliate )
	{
		// check affiliate info
		if ( strlen( $affiliate['full_name'] ) < 4 || strlen( $affiliate['full_name'] ) > 30 
			|| strlen( $affiliate['password'] ) < 4 || strlen( $affiliate['password'] ) > 30
			|| !preg_match( SK_ProfileFields::get('email')->regexp, $affiliate['email'] )
		)
			return false;
		
		return ( MySQL::fetchResource( sql_placeholder( "INSERT INTO `?#TBL_AFFILIATE` (`full_name`,`password`,`email`) VALUES( ?@ )", array( $affiliate['full_name'], $affiliate['password'], $affiliate['email'] ) ) ) )?
			mysql_insert_id() : false;
	}

	/**
	 * Returns affiliate info:
	 *
	 * @param integer $affiliate_id
	 * @param string $email
	 * @return array
	 */
	public static function getAffiliateInfo( $affiliate_id = 0, $email = '' )
	{
		$affiliate_id = intval( $affiliate_id );
		$email = trim( $email );
		if ( !$affiliate_id && !$email )
			return array();
	
		return ( !$affiliate_id )? 
			MySQL::fetchRow( SK_MySQL::placeholder("SELECT * FROM `".TBL_AFFILIATE."` WHERE `email`='?'", $email)  )	:
			MySQL::fetchRow( SK_MySQL::placeholder("SELECT * FROM `".TBL_AFFILIATE."` WHERE `affiliate_id`=?", $affiliate_id) );
	}
	
	
	/**
	 * Adds request email verification.
	 *
	 * @param integer $affiliate_id
	 * @return boolean
	 */
	public static function addRequestEmailVerification( $affiliate_id = 0, $email = '' )
	{
		$affiliate_id = intval( $affiliate_id );
		$email = trim( $email );
		if ( !$affiliate_id && !$email )
			return false;
		
		$affiliate_info = self::getAffiliateInfo( $affiliate_id, $email );
		
		if ( !$affiliate_info )
			return false;
		
		$email = ( $email )? $email : $affiliate_info['email'];
		$code = sha1( time() );
		
		$query = sql_placeholder( "INSERT INTO `".TBL_AFFILIATE_EMAIL_VERIFY_CODE."`( `affiliate_id`,`code`,`create_stamp`,`expiration_stamp` )
			VALUES( ?@ )", array( $affiliate_info['affiliate_id'], $code, time(), ( time() + 7*86400 ) ) );
		
		MySQL::fetchResource( $query );
		
		// sending email
		$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
				->setRecipientEmail($email)
				->setTpl('affiliate_email_verify')
				->assignVar('affiliate_name', $affiliate_info['full_name'])
				->assignVar('verify_email_url', self::getEmailVerificationURL( $code, $affiliate_info['affiliate_id']));
		app_Mail::send($msg);
				
		return true;
	}
	
	
	/**
	 * Returns email verification url
	 *
	 * @param string $code
	 * @param integer $affiliate_id
	 * @return string
	 */
	public static function getEmailVerificationURL( $code, $affiliate_id )
	{
		return URL_AFFILIATE . 'index.php?verification_code='.trim($code).'&affiliate_id='.$affiliate_id;
	}
	
	/**
	 * Returns verification code from query string
	 *
	 * @return string
	 */
	public static function getVerificationCodeFromQueryString()
	{
		return trim( $_GET['verification_code'] );
	}
	
	
	/**
	 * Catchs and checks affiliate verification code.
	 *
	 * @return boolean
	 */
	public static function processCheckVerificationCode()
	{
		$affiliate_id = intval( $_GET['affiliate_id'] );
		if ( !$affiliate_id )
			return false;
		
		// redirect if email verified
		if ( self::isAffiliateEmailVerified( $affiliate_id ) )
			SK_HttpRequest::redirect( SK_Navigation::href('affiliate_home') );
		
		$code = self::getVerificationCodeFromQueryString();

		if ( !$code )
			return false;

		// check if the code exists
		$query = sql_placeholder( "SELECT `affiliate_id` FROM `".TBL_AFFILIATE_EMAIL_VERIFY_CODE."` 
			WHERE `code`=? AND `expiration_stamp`>'".time()."'", $code );

		if ( MySQL::fetchField( $query ) == $_GET['affiliate_id'] )
		{
			// delete code
			MySQL::fetchResource( sql_placeholder( "DELETE FROM `".TBL_AFFILIATE_EMAIL_VERIFY_CODE."` 
				WHERE `affiliate_id`=?", $affiliate_id ) );
			
			// update affiliate field
			MySQL::fetchResource( SK_MySQL::placeholder("UPDATE `".TBL_AFFILIATE."` SET `email_verified`='yes' WHERE `affiliate_id`=?", (int)$affiliate_id));
			
			SK_HttpRequest::redirect( SK_Navigation::href('affiliate_home') );
		}
		else 
			return -1;
	}
	
	
	/**
	 * Checks if affiliate's email was verified.
	 *
	 * @param integer $affiliate_id
	 * @return boolean
	 */
	public static function isAffiliateEmailVerified( $affiliate_id )
	{
		$affiliate_id = intval( $affiliate_id );
		if ( !$affiliate_id )
			return false;
	
		return ( MySQL::fetchField( SK_MySQL::placeholder("SELECT `affiliate_id` FROM `".TBL_AFFILIATE."` WHERE `affiliate_id`=? AND `email_verified`='yes'", (int)$affiliate_id) ) )? true : false;
	}
	
	/**
	 * Checks if the email are used by other affiliate.
	 *
	 * @param integer $affiliate_id
	 * @param string $email
	 * @return boolean
	 */
	public static function checkIsAffiliateEmailUnique( $email, $affiliate_id = 0 )
	{
		$email = trim( $email );
		if ( !strlen($email) )
			return false;
		
		if ($affiliate_id != 0)
			$aff_id = MySQL::fetchField( sql_placeholder( "SELECT `affiliate_id` FROM `?#TBL_AFFILIATE` 
				WHERE `email`=? AND `affiliate_id`!=?", $email, $affiliate_id ) );
		else	
			$aff_id = MySQL::fetchField( sql_placeholder( "SELECT `affiliate_id` FROM `?#TBL_AFFILIATE` WHERE `email`=?", $email ) );
		
		if ( $aff_id )
			return false;
			
		return true;
	}
	
	public static function getAffiliateBanners( $affiliate_id )
	{
        if ( !$affiliate_id )
        {
            return null;
        }
        
        $sql = SK_MySQL::placeholder("SELECT * FROM `".TBL_AFFILIATE_BANNER."` 
            WHERE `affiliate_id` = ?", $affiliate_id);
        
        $res = SK_MySQL::query($sql);
        $list = array();
        
        while ( $row = $res->fetch_assoc() )
        {
            $row['image_url'] = URL_USERFILES.$row['filename'];
            $row['code'] = '<a href="'.SITE_URL.'?aff_id='.$row['affiliate_id'].'"><img src="'.$row['image_url'].'" /></a>';
            $list[] = $row;
        }
        
        return $list;
	}
	
	public static function addBanner( $affiliate_id, $file )
	{
        if ( !$affiliate_id || !$file['tmp_name'] || !is_uploaded_file($file['tmp_name']) )
        {
            return false;
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        
        if ( !in_array(strtolower($ext), array('jpg', 'jpeg', 'png', 'gif')) )
        {
            return false;
        }
        
        $sql = SK_MySQL::placeholder("INSERT INTO `".TBL_AFFILIATE_BANNER."` 
            (`affiliate_id`) VALUES (?)", $affiliate_id);
        
        $res = SK_MySQL::query($sql);
        $banner_id = SK_MySQL::insert_id();
        
        $filename = 'affb-'.$banner_id.'.'.$ext;
        
        $dest = DIR_USERFILES.$filename;
        
        if ( move_uploaded_file($file['tmp_name'], $dest) )
        {
            $sql = SK_MySQL::placeholder("UPDATE `".TBL_AFFILIATE_BANNER."` 
                SET `filename` = '?' WHERE `id` = ?", $filename, $banner_id);
            
            SK_MySQL::query($sql);
            
            return true;
        }
        
        else {
            $sql = SK_MySQL::placeholder("DELETE FROM `".TBL_AFFILIATE_BANNER."` 
                WHERE `id` = ?", $banner_id);
            
            SK_MySQL::query($sql);
            
            return false;
        }
	}
	
	public static function deleteBanner( $id, $affiliate_id )
	{
        if ( !$id || !$affiliate_id )
        {
            return false;
        }
        
        $sql = SK_MySQL::placeholder("SELECT * FROM `".TBL_AFFILIATE_BANNER."`
            WHERE `id` = ?", $id);
        
        $banner = SK_MySQL::query($sql)->fetch_assoc();
        
        if ( !$banner || $banner['affiliate_id'] != $affiliate_id )
        {
            return false;
        }
        
        $sql = SK_MySQL::placeholder("DELETE FROM `".TBL_AFFILIATE_BANNER."`
            WHERE `id` = ?", $id);
        
        SK_MySQL::query($sql);
        
        @unlink(DIR_USERFILES.$banner['filename']);
        
        return true;
	}
}
