<?php

class app_EmailVerification
{

	public static function addRequestEmailVerification( $profile_id, $email )
	{
		if ( !is_numeric( $profile_id ) || !intval( $profile_id ) )
			return -1;
		$reg_exp = SK_ProfileFields::get('email')->regexp;
		if ( ( (isset($reg_exp) && strlen(trim($reg_exp))) && !preg_match( $reg_exp, $email ) ) || !strlen( trim( $email ) ) )
			return -2;

		$_code = sha1( time() );

		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_PROFILE_EMAIL_VERIFY_CODE."`( `profile_id`, `code`, `create_date`, `expiration_date` )
			VALUES( ?, '?', ?, ? )", $profile_id,	$_code,	time(),	( time() + 7*86400 ));

		SK_MySQL::query( $query );

		// get profile info
		$_email = app_Profile::getFieldValues( $profile_id, 'email');

		// update email if changed
		if ( ( $_email != trim( $email ) ) && ( app_Profile::setFieldValue($profile_id, 'email', $email ) < 0 ) )
			return -3;

		$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
				->setRecipientProfileId($profile_id)
				->setRecipientEmail($email)
				->setTpl('email_verify')
				->assignVar('verify_email_url', self::getEmailVerificationURL( $_code ));

		app_Mail::send($msg);

		return 1;
	}

	public static function getEmailVerificationURL( $code )
	{
		return SK_Navigation::href('email_verify',array('verification_code'=>trim( $code )));
	}

	public static function getVerificationCodeFromQueryString()
	{
		return trim( $_GET['verification_code'] );
	}

	public static function processCheckVerificationCode()
	{

		$verified = app_Profile::getFieldValues( SK_HttpUser::profile_id() , 'email_verified' );
                
		if( $verified == 'yes' )
			return true;

		$code = self::getVerificationCodeFromQueryString();

		if ( !$code )
			return false;

		// check if the code exists
		$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `".TBL_PROFILE_EMAIL_VERIFY_CODE."`
			WHERE `code`='?' AND `expiration_date`>?", $code, time() );

		if ( SK_MySQL::query($query)->fetch_cell() == SK_HttpUser::profile_id() )
		{
			// delete code
			$query = SK_MySQL::placeholder("DELETE FROM `".TBL_PROFILE_EMAIL_VERIFY_CODE."`
				WHERE `profile_id`=?",SK_HttpUser::profile_id() );

			SK_MySQL::query($query);

			app_Profile::setFieldValue(SK_HttpUser::profile_id(), 'email_verified', 'yes' );

			return true;
		}
		else {
			return false;
		}
	}

	public static function isProfileEmailVerified($profile_id)
	{
		return app_Profile::getFieldValues($profile_id, 'email_verified') == 'yes';
	}
}

?>