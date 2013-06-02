<?php

class app_Invitation
{
	/**
	 * Detect is profile allow to send registration invite
	 *
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function isProfileAllowInvite( $profile_id )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return false;

		$score = app_Profile::getFieldValues( $profile_id, 'register_invite_score', false );

		$config = SK_Config::section('profile_registration');

		if (
				( $config->allow_max_invitation && $config->limit_invitation > $score )
				|| !$config->allow_max_invitation
			)
			return true;
		else
			return false;

	}

	private static function getInviteResetPeriod() {
		$config = SK_Config::section('profile_registration');
		return $config->invite_reset_period * 3600 * 24;
	}

	public function setInviteResetTime($profile_id) {
		$time = time();
		$query = SK_MySQL::placeholder("
			INSERT INTO `" . TBL_INVITE_RESET . "`
			SET `profile_id`=?, `reset_stamp`=?
			ON DUPLICATE KEY
			UPDATE `reset_stamp`=?
		", $profile_id, $time, $time);
		SK_MySQL::query($query);
	}

	private static function addInviteResetTime($profile_id) {
		$query = SK_MySQL::placeholder("
			SELECT COUNT(*) FROM `".TBL_INVITE_RESET."`
			WHERE `profile_id`=?
		", $profile_id);

		if (!SK_MySQL::query($query)->fetch_cell()) {
			self::setInviteResetTime($profile_id);
		}
	}

	public static function cron_UpdateInviteLimit() {
		$query = "SELECT * FROM `" . TBL_INVITE_RESET . "`";
		$result = SK_MySQL::query($query);
		$config = SK_Config::section('profile_registration');

		while ($item = $result->fetch_object()) {
			if ( ($item->reset_stamp + self::getInviteResetPeriod()) <  time()) {
				app_Profile::setFieldValue($item->profile_id, 'register_invite_score', '0');
				self::setInviteResetTime($item->profile_id);
			}
		}
	}

	/**
	 * Adds request for sending email with invitation for registration
	 *
	 * @param string $reciever_email
	 * @return integer
	 */
	public static function addRequestRegister( $reciever_email, $user_message = "" )
	{
		if (!($profile_id = SK_HttpUser::profile_id())){
			throw new Exception('not_profile',1);
		}

		$config = SK_Config::section('profile_registration');

		// get profile info
		$profile_info = app_Profile::getFieldValues( $profile_id, array( 'language_id', 'username', 'email', 'register_invite_score' ) );

		self::addInviteResetTime($profile_id);

		// check invitation limit
		if ( !self::isProfileAllowInvite($profile_id) ) {
			throw new Exception('limit_exceeded',2);
		}


		//----
		$code = sha1( time() );
		$query = SK_MySQL::placeholder(
			"SELECT `email` FROM `".TBL_REFERRAL_INVITE."` WHERE (`email`='?' AND `referrer_id`=?)", $reciever_email, $profile_id );

		if(!SK_MySQL::query($query)->fetch_cell())
		{
			$query = SK_MySQL::placeholder('INSERT INTO `'.TBL_REFERRAL_INVITE.'`(`referrer_id`, `email`, `code`, `time_stamp`) VALUES(?,"?","?",?)',
																						$profile_id,
																						$reciever_email,
																						$code,
																						time()
																						);
		}
		else
		{
			$query = SK_MySQL::placeholder("
			UPDATE `".TBL_REFERRAL_INVITE."` SET `time_stamp`=?, `code`='?' WHERE (`referrer_id`=? AND `email`='?')
			", time(), $code, $profile_id, $reciever_email );
		}

		SK_MySQL::query( $query );

		$query = 'SELECT `total_invites` FROM `'.TBL_REFERRAL."` WHERE `referral_id`=$profile_id";
		$total_invites = SK_MySQL::query($query)->fetch_cell();

		$total_invites = $total_invites + 1;
		$query = 'UPDATE `'.TBL_REFERRAL."` SET `total_invites`=$total_invites WHERE `referral_id`=$profile_id";
		SK_MySQL::query($query );
		//----

		$query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_REGISTER_INVITE_CODE."`( `profile_id`, `code`, `create_date`, `expiration_date` )
			VALUES( ?, '?', ?, ? )",
								$profile_id,
								$code,
								time(),
								( time() + $config->invite_time_interval*86400 )
							 );

		SK_MySQL::query($query);

		// update counter of profile sending invitation
		$query = "UPDATE `".TBL_PROFILE."` SET `register_invite_score`=`register_invite_score`+1 WHERE `profile_id`='".$profile_id."'";
		SK_MySQL::query($query);

		if (trim($user_message)) {
			$tpl = 'invite_friend_code';
		} else {
			$tpl = 'invite_friend';
		}

		// sending email
		$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
				->setRecipientLangId($profile_info['language_id'])
				->setRecipientEmail($reciever_email)
				->setTpl($tpl)
				->assignVar('invite_url', self::InviteURL($code))
				->assignVar('username', $profile_info['username'])
				->assignVar('user_message', $user_message);

		return app_Mail::send($msg);
	}

	public static function addAdminRequestRegister( $reciever_email )
	{
		if ( !strlen( trim( $reciever_email ) ) )
			return -1;
		$code = null;
		if ( SK_Config::section('profile_registration')->type != 'free' )
		{
			$code = sha1( time() );

			$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_PROFILE_REGISTER_INVITE_CODE."`( `profile_id`, `code`, `create_date`, `expiration_date` )
				VALUES( ?, '?', ?, ? )", 0,	$code,	time(),	( time() + SK_Config::section('profile_registration')->invite_time_interval * 86400 ));

			SK_MySQL::query($query);
		}


		// sending email
		$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
				->setRecipientEmail($reciever_email)
				->setTpl('invite_admin_friend_code')
				->assignVar('invite_url', self::InviteURL($code));

		app_Mail::send($msg);
		return 1;
	}


	/**
	 * Returns URL to join page with registration code.
	 *
	 * @param string $code
	 * @return string
	 */
	public static function InviteURL( $code = null )
	{
		$params = $code ? array('registration_code'=>$code) : null;

		return SK_Navigation::href( 'join_profile', $params );
	}

	/**
	 * Regulars access to join page. If online invite registration enable
	 * then checks invite code from query string
	 * with code in database. Return ID of profile who send profile. 0 - if
	 * code send admin
	 *
	 * @return integer
	 */
	public static function controlRegistrationAccess()
	{
		$registration_code = self::getRegistrationCode();

		if (  SK_Config::section('profile_registration')->type == 'free')
		{
			if ( $registration_code )
				self::saveRegistrationCode( $registration_code );
			return null;
		}

		if ( !$registration_code ) {
			SK_HttpRequest::redirect(SK_Navigation::href('index'));
		}

		// check if registration code exists in database
		$query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_PROFILE_REGISTER_INVITE_CODE."`
			WHERE `code`='?' AND `expiration_date`>?", $registration_code, time());

		$code_sender_id = SK_MySQL::query($query)->fetch_cell();

		if ($code_sender_id === false) {
			SK_HttpRequest::redirect(SK_Navigation::href('index'));
		}


		// save code in session
		self::saveRegistrationCode( $registration_code );

		return $code_sender_id;
	}

	/**
	 * Saves registration code in session
	 *
	 * @param string $code
	 */
	public static function saveRegistrationCode($code = null)
	{
		if (!isset($code)) {
			$code = self::getRegistrationCode();
		}
		$_SESSION['registration_invite_code'] = trim( $code ) ;
	}

	/**
	 * Returns registation  code, which is stored in query string
	 * (if exists) or from session.
	 *
	 * @return string
	 */
	public static function getRegistrationCode()
	{
		return isset($_GET['registration_code']) ? $_GET['registration_code'] : @$_SESSION['registration_invite_code'];
	}

	/**
	 * Unset all info about registration code.
	 * Remove it from session and database.
	 *
	 * @return boolean
	 */
	public static function unsetRegistrationCodeInfo()
	{
		if ( !self::getRegistrationCode() )
			return false;

		$query = SK_MySQL::placeholder("DELETE FROM `".TBL_PROFILE_REGISTER_INVITE_CODE."`
			WHERE `code`='?'", self::getRegistrationCode() );

		SK_MySQL::query($query);

		unset( $_SESSION['registration_invite_code'] );

		return true;
	}

	public static function getSenderProfileId()
	{
		$code = self::getRegistrationCode();

		if ( !strlen( $code ) )
			return false;

		$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `".TBL_PROFILE_REGISTER_INVITE_CODE."`
			WHERE `code`='?' LIMIT 1", $code );

		$profile_id =SK_MySQL::query($query)->fetch_cell();

		return $profile_id ? (int)$profile_id : 0;
	}

}
