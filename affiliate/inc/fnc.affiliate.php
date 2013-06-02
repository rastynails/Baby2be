<?php

/**
 * These functions are intended for working with finance - view, refund transactions.
 */

/**
 * Returns affiliate id from session. If not isset in the session then redirects.
 *
 * @return integer
 */
function getAffiliateId()
{
	if ( !$_SESSION['affiliate_id'] )
		SK_HttpRequest::redirect( URL_AFFILIATE.'affiliate_login.php' );
	
	return $_SESSION['affiliate_id'];
}


/**
 * Logins affiliate and set affiliate id in session. 
 * Returns true if it is a true affiliate else false.
 *
 * @param string $affiliate_email
 * @param string $affiliate_pass
 * @return boolean
 */
function LoginAffiliate( $affiliate_email, $affiliate_pass )
{
	$affiliate_email = trim( $affiliate_email );
	$affiliate_pass = trim( $affiliate_pass );
	if ( !strlen($affiliate_email) || !strlen($affiliate_pass) )
		return false;
	
	$query = SK_MySQL::placeholder( "SELECT `affiliate_id`,`email`,`password` FROM `".TBL_AFFILIATE."` 
		WHERE `email`='?'", $affiliate_email );
	
	$affiliate = SK_MySQL::query($query)->fetch_assoc();
	
	if ( !$affiliate || $affiliate['password'] != $affiliate_pass )
		return false;
	
	// set data in SESSION
	$_SESSION['affiliate_id'] = $affiliate['affiliate_id'];
	
	return true;
}


/**
 * Sends forgot password to affiliate email address.
 *
 * @param string $affiliate_email
 * @return boolean
 */
function SendPassword( $affiliate_email )
{
	$affiliate_email = trim($affiliate_email);
	if (!strlen($affiliate_email))
		return false;
	
	$affiliate_info = getAffiliateInfoUsingEmail($affiliate_email);
	if (!$affiliate_info)
		return false;

	$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
			->setRecipientEmail($affiliate_info['email'])
			->setTpl('send_affiliate_password')
			->assignVar('affiliate_name', $affiliate_info['full_name'])
			->assignVar('password', $affiliate_info['password']);
	return app_Mail::send($msg);
}


/**
 * Returns affiliate click stats.
 *
 * @param integer $affiliate_id
 * @return array
 */
function getAffiliateTraffic( $affiliate_id )
{
	$affiliate_id = intval($affiliate_id);
	if (!$affiliate_id)
		return array();
	
	$query = SK_MySQL::placeholder("SELECT COUNT(`affiliate_id`) AS `count`, SUM(`amount`) AS `amount` 
		FROM `".TBL_AFFILIATE_TRAFFIC."` 
		WHERE `affiliate_id`=? GROUP BY `affiliate_id`", $affiliate_id);
	
	return SK_MySQL::query($query)->fetch_assoc();
}


/**
 * Returns affiliate registration stats.
 *
 * @param integer $affiliate_id
 * @return array
 */
function getAffiliateRegistration( $affiliate_id )
{
	$affiliate_id = intval($affiliate_id);
	if (!$affiliate_id)
		return array();
	
	$query = SK_MySQL::placeholder("SELECT COUNT(`affiliate_id`) AS `count`, SUM(`amount`) AS `amount` 
		FROM `".TBL_AFFILIATE_REGISTRATION."` WHERE `affiliate_id`=? GROUP BY `affiliate_id`", $affiliate_id);
	
	return SK_MySQL::query($query)->fetch_assoc();
}


/**
 * Returns affiliate subscription stats.
 *
 * @param integer $affiliate_id
 * @return array
 */
function getAffiliateSubscription( $affiliate_id )
{
	$affiliate_id = intval($affiliate_id);
	if (!$affiliate_id)
		return array();
	
	$query = SK_MySQL::placeholder("SELECT COUNT(`affiliate_id`) AS `count`, SUM(`amount`) AS `amount` 
		FROM `".TBL_AFFILIATE_SUBSCRIPTION."` WHERE `affiliate_id`=? GROUP BY `affiliate_id`", $affiliate_id);
	
	return SK_MySQL::query($query)->fetch_assoc();
}


/**
 * Returns affiliate click stats for his sites.
 *
 * @param integer $affiliate_id
 * @param integer $limit
 * @return array
 */
function getAffiliateTrafficForSites( $affiliate_id, $limit )
{
	$affiliate_id = intval($affiliate_id);
	if (!$affiliate_id)
		return array();
	
	$limit = intval($limit);
	if (!$limit)
		$limit = 10;

	$query = SK_MySQL::placeholder("SELECT `site`,`count` FROM `".TBL_AFFILIATE_SITE."` 
		WHERE `affiliate_id`=? ORDER BY `count` DESC LIMIT ?", $affiliate_id, $limit);
	
	$result = SK_MySQL::query($query);
	
	$sites = array();
	while ($site = $result->fetch_assoc())
		$sites[] = $site;
	
	return $sites;
}


/**
 * Logouts affiliate.
 *
 * @return boolean
 */
function LogoutAffiliate()
{
	unset($_SESSION['affiliate_id']);
	
	return true;
}


/**
 * Returns checked affiliate fields.
 *
 * @param array $affiliate_arr
 * @return array
 */
function checkAffiliateFields( $affiliate_arr )
{
	if ( !is_array($affiliate_arr) )
		return array();
	
	foreach ($affiliate_arr as $_key => $_value)
	{
		switch ( $_key )
		{
			case 'full_name':
			case 'password':
				if ( strlen( $_value ) < 4 || strlen( $_value ) > 30 )
					return array();
				$_result_arr[$_key] = $_value;
				break;
			case 'email':
				if ( strlen(trim(SK_ProfileFields::get('email')->regexp)) && !preg_match( SK_ProfileFields::get('email')->regexp, $_value ) )
					return array();
				
				$_result_arr[$_key] = $_value;
				break;
			case 'active':
			case 'email_verified':
				if ( $_value != 'yes' && $_value != 'no' )
					return array();
				
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
 * Returns the result of email checking and sends verification as bool.
 *
 * @param integer $affiliate_id
 * @param string $new_email
 * @return boolean
 */
function processCheckEmailChange( $affiliate_id, $new_email )
{
	$affiliate_id = intval($affiliate_id);
	$new_email = trim($new_email);
	if ( !$affiliate_id || !$new_email )
		return false;
	
	$affiliate_info = app_Affiliate::getAffiliateInfo($affiliate_id);
	if ( $affiliate_info['email'] != $new_email )
		app_Affiliate::addRequestEmailVerification($affiliate_id, $new_email);
	
	return true;
}
