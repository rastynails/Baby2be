<?php

class SKM_User
{	
	/**
	 * Keeps the reference to $_SESSION['%http_user%']
	 *
	 * @var array
	 */
	private $session;
		
	/**
	 * Constructor. Starts a session.
	 */
	public static function session_start()
	{
		header('Content-Type: text/html; charset=UTF-8');
		
		if ( !self::is_authenticated() ) {
			if ( isset($_COOKIE['_username']) && isset($_COOKIE['_unique'])
				&& app_Profile::checkCookiesLogin($_COOKIE['_username'], $_COOKIE['_unique']) )
			{
				app_Profile::cookiesLogin($_COOKIE['_username']);
			}
		}
		
		// check profile 0nline status
		self::update_activity();
	}
	
	/**
	 * Autchenticates an user and creates `%http_user%` session.
	 *
	 * @param string $login
	 * @param string $password
	 * @return bool true if authentication success
	 */
	public static function authenticate( $login, $password, $addSalt = true )
	{
		if ( !$login || !$password ) {
			return false;
		}
		
		// detect if var $username is email or username
		if ( strstr( $login, '@' ) )
			$query_ins = 'email';
		else 
			$query_ins = 'username';
		
		$query = SK_MySQL::placeholder(
			"SELECT `profile_id`,`username`,`password`
				FROM `".TBL_PROFILE."`
				WHERE `$query_ins`='?'", $login
		);
		
		$result = SK_MySQL::query($query);
		
		if ( !$result->num_rows() ) {
			throw new SKM_UserException(SKM_Language::text('%forms.sign_in.error_msg.not_member'), 1);
		}
		
		$user_data = $result->fetch_assoc();
		
		$password = $addSalt ? app_Passwords::hashPassword($password) : $password;
		
		if ( $password !== $user_data['password'] ) {
			throw new SKM_UserException(SKM_Language::text('%forms.sign_in.error_msg.invalid_pass'), 2);
		}
		
		if (self::isIpBlocked()){
			throw new SKM_UserException(SKM_Language::text('%forms.sign_in.error_msg.blocked_member'), 3);
		}
		
		// get hash
		$session_id = Session::instance()->id();
		
		$query = SK_MySQL::placeholder("DELETE FROM `".TBL_PROFILE_ONLINE."` WHERE `profile_id`=?", $user_data['profile_id'] );
		SK_MySQL::query($query);
		
		$query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_ONLINE."`( `profile_id`, `hash`, `agent` ) 
			VALUES( ?, '?', 'mobile')", $user_data['profile_id'], $session_id);
		SK_MySQL::query($query);
		
		Session::instance()->set('%httpuser%', array(
			'profile_id'=>	$user_data['profile_id'],
			'username'	=>	$user_data['username'],
			'session_id'=>	$session_id
		));
		
		return $user_data['profile_id'];
	}
	
	/**
	 * Log-off the user.
	 */
	public static function logoff() {
		SKM_Profile::Logoff($_SESSION['%httpuser%']['profile_id']);
	}
		
	/**
	 * Checks if the user is authenticated.
	 * 
	 * @return boolean
	 */
	public static function is_authenticated() {
		return isset($_SESSION['%httpuser%']['profile_id']) && intval($_SESSION['%httpuser%']['profile_id']);
	}
	
	public static function session_id(){
		return $_SESSION['%httpuser%']['session_id'];
	}
	
	/**
	 * Returns an authenticated user profile id.
	 * If a user is not authenticated - FALSE returns.
	 *
	 * @return integer|false
	 */
	public static function profile_id() {
		if (self::is_authenticated()) {
			return $_SESSION['%httpuser%']['profile_id'];
		} else 
			return false;
	}
	
	public static function session_end() {
		unset($_SESSION['%httpuser%']);
	}
	
	public static function update_activity( $profile_id = null )
	{
		if ( !$profile_id )
		{
			$session_id = SKM_User::session_id();
			if ( !isset($session_id) )
				return false;
		
			// check if profile online
			$query = "SELECT `profile_id` FROM `".TBL_PROFILE_ONLINE."` WHERE `hash`='{$session_id}'";
			$profile_id = SK_MySQL::query($query)->fetch_cell();
		}
		
		if ( !$profile_id  || SKM_User::isIpBlocked() )
		{
			SKM_User::logoff();
			return false;
		}
		else
		{
			$query = "UPDATE `".TBL_PROFILE."` SET `activity_stamp`='".time()."' WHERE `profile_id`='".$profile_id."'";			
			SK_MySQL::query($query);
			
			$query = "UPDATE `".TBL_PROFILE_ONLINE."` SET `expiration_time`='".self::get_session_expire_time()."' WHERE `profile_id`='$profile_id'";
			SK_MySQL::query($query );
			
			return true;
		}
	}
	
	public static function language_id() {				
		$lang_id = self::is_authenticated() 
					? app_Profile::getFieldValues(self::profile_id(), 'language_id') 
					: @$_COOKIE['language_id'];
		
		return $lang_id ? $lang_id : SK_Config::Section('languages')->default_lang_id;
	}
	
	public static function get_session_expire_time()
	{
		return time() + SK_Config::section('site.additional.profile')->member_logout_sec;
	}
	
	/**
	 * Returns an authenticated user `username`.
	 * If not authenticated - NULL returns.
	 *
	 * @return string
	 */
	public static function username() {
		if (self::is_authenticated()) {
			return $_SESSION['%httpuser%']['username'];
		} else 
			return false;
	}
		
	public static function isIpBlocked( $ip = null )
	{
		$ip = 	( $ip ) ? $ip : $_SERVER['REMOTE_ADDR'];
		
		if ( !$ip )
			return false;
		
		$query = SK_MySQL::placeholder("SELECT `block_ip` FROM `".TBL_BLOCK_IP."`
			WHERE `block_ip`=INET_ATON('?')", $ip );
		
		return ( SK_MySQL::query( $query )->fetch_cell() ) ? true : false;
	}
	
}

class SKM_UserException extends Exception 
{
	
}