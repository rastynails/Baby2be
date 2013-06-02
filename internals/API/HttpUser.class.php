<?php

class SK_HttpUser
{
	/**
	 * This var temporarily becomes hardcoded.
	 * There are no ways to change this value except here.
	 * Function $HTTP_USER->i18n_lang() is the only way to read this value.
	 *
	 * @var integer
	 */
	private static $lang_id = 1;

	/**
	 * Keeps the reference to $_SESSION['%http_user%']
	 *
	 * @var array
	 */
	private static $session;

	/**
	 * Constructor. Starts a session.
	 */
	public static function session_start()
	{
		header('Content-Type: text/html; charset=UTF-8');

		session_start();

		self::$session =& $_SESSION['%http_user%'];
		if (!self::is_authenticated()) {
			if ( isset($_COOKIE['_username']) && isset($_COOKIE['_unique'])
				&& app_Profile::checkCookiesLogin($_COOKIE['_username'], $_COOKIE['_unique']) )
			{
				app_Profile::cookiesLogin($_COOKIE['_username']);
			}
		}

		/*if ( self::$session['ip'] != SK_HttpRequest::getRequestIP() )
		{
			self::logoff();
			return;
		}*/

                /*
                 * This is done by Ping
                 *
                 * if ( !SK_HttpRequest::isXMLHttpRequest() )
                {
                    // check profile 0nline status
                    app_Profile::updateProfileActivity();
                }*/

		app_Invitation::saveRegistrationCode();
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
			throw new SK_HttpUserException(SK_Language::text('%forms.sign_in.error_msg.not_member'),1);
		}

		$user_data = $result->fetch_assoc();

		$password = $addSalt ? app_Passwords::hashPassword($password) : $password;

		if ( $password !== $user_data['password'] ) {
			throw new SK_HttpUserException(SK_Language::text('%forms.sign_in.error_msg.invalid_pass'),2);
		}

		if (SK_HttpRequest::isIpBlocked()){
			throw new SK_HttpUserException(SK_Language::text('%forms.sign_in.error_msg.blocked_member'),3);
		}

		// give credits for daily login
		app_UserPoints::earnCreditsForActionAlternative($user_data['profile_id'], 'daily_login', false, 1);

		// get hash
		$session_id = session_id();

		$query = SK_MySQL::placeholder("DELETE FROM `".TBL_PROFILE_ONLINE."`
		  WHERE `profile_id`=? OR `hash` = '?'", $user_data['profile_id'], $session_id);
		SK_MySQL::query($query);

		$query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_ONLINE."`( `profile_id`, `hash`, `agent` )
			VALUES( ?, '?', 'base' )", $user_data['profile_id'], $session_id);
		SK_MySQL::query($query);

		self::$session = array(
			'profile_id'=>	$user_data['profile_id'],
			'username'	=>	$user_data['username'],
			'session_id'=>	$session_id
			//'ip'		=>	SK_HttpRequest::getRequestIP()
		);

		return $user_data['profile_id'];
	}


	public static function session_id(){
		return self::$session['session_id'];
	}

	/**
	 * Log-off the user.
	 */
	public static function logoff() {
		app_Profile::Logoff(self::profile_id());
	}


	public static function session_end() {
		unset($_SESSION['%http_user%']);
	}

	/**
	 * Checks if the user is authenticated.
	 *
	 * @return boolean
	 */
	public static function is_authenticated() {
		return isset($_SESSION['%http_user%']);
	}

	/**
	 * Returns an authenticated user profile id.
	 * If a user is not authenticated - FALSE returns.
	 *
	 * @return integer|false
	 */
	public static function profile_id() {
		return self::is_authenticated() ? self::$session['profile_id'] : false;
	}

	/**
	 * Returns an authenticated user `username`.
	 * If not authenticated - NULL returns.
	 *
	 * @return string
	 */
	public static function username() {
		return self::is_authenticated() ? self::$session['username'] : false;
	}

	public static function set_cookie($name, $value, $expired_period = null, $path='/')
	{
		$expiration_time = isset($expired_period) ?  time()+$expired_period : null;
		return setcookie($name, $value, $expiration_time,$path);
	}

	public static function unset_cookie($name)
	{
		return setcookie($name, null, null, '/');
	}

	public static function language_id() {
		if (SK_Navigation::isCurrentArea(URL_ADMIN)) {
			$lang_id = intval($_GET['language_id']);
			return $lang_id
				? $lang_id
				: SK_Config::Section('languages')->default_lang_id;
		}

		if (isset($_GET['language_id'])) {

			$lang_id = intval($_GET['language_id']);

			$profile_id = self::profile_id();

			$result = (bool) SK_MySQL::query(SK_MySQL::placeholder(
				"SELECT COUNT(`lang_id`) FROM `" . TBL_LANG . "` WHERE `lang_id`=?"
				, $lang_id))->fetch_cell();

			if (!$result) {
				return SK_Config::Section('languages')->default_lang_id;
			}

			self::set_cookie('language_id', $lang_id, 604800);

			app_Profile::setFieldValue($profile_id, 'language_id', $lang_id);

		} else {

			$lang_id = self::is_authenticated()
						? app_Profile::getFieldValues(self::profile_id(), 'language_id')
						: @$_COOKIE['language_id'];
		}

                if ( empty($lang_id) && SK_Config::Section('languages')->auto_select )
                {
                    $query = SK_MySQL::placeholder('
                        SELECT `lang`.`lang_id`
                        FROM `' . TBL_LANG_TO_COUNTRY . '` AS `lang_country`
                            INNER JOIN `' . TBL_LOCATION_COUNTRY_IP . '` AS `country`
                                ON(`lang_country`.`Country_str_ISO3166_2char_code` = `country`.`Country_str_ISO3166_2char_code`)
                            INNER JOIN `' . TBL_LANG .'` AS `lang`
                                ON(`lang`.`lang_id` = `lang_country`.`lang_id`)
                        WHERE (? BETWEEN `country`.`startIpNum` AND `country`.`endIpNum`) AND (`lang`.`enabled` >= 1)
                        LIMIT 1;', ip2long($_SERVER['REMOTE_ADDR']) );

                    $lang_id = SK_MySQL::query( $query )->fetch_cell();

                    if ( !empty($lang_id) )
                    {
                        self::set_cookie( 'language_id', $lang_id, 604800 );
                    }
                }

		return $lang_id ? $lang_id : SK_Config::Section('languages')->default_lang_id;
	}


	private static $moderatorFlag = null;

	public static function isModerator()
	{
		if ( !isset(self::$moderatorFlag) ) {
			self::$moderatorFlag = app_Profile::isProfileModerator(SK_httpUser::profile_id());
		}

		return self::$moderatorFlag;
	}

}

class SK_HttpUserException extends Exception
{

}
