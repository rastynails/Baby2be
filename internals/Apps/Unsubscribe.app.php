<?php

class app_Unsubscribe
{
	/**
	 * Checks if profile is unsubscribed
	 *
	 * @param int $profile_id
	 * @return boolean
	 */
	public static function isProfileUnsubscribed( $profile_id )
	{
		$profile_id = intval($profile_id);

		if (!$profile_id)
			return false;

	    return (bool) app_ProfilePreferences::get('mass_mailing', 'unsubscribed', $profile_id);
	}

	/**
	 * Sets 'unsubscribed' status for profile
	 *
	 * @param int $profile_id
	 * @param boolean $is_unsubscribe
	 * @return boolean
	 */
	public static function setUnsubscribe( $profile_id, $is_unsubscribe = true )
	{
		$profile_id = intval($profile_id);

		$status = $is_unsubscribe ? 1 : 0;

		if (!$profile_id)
			return false;

		app_ProfilePreferences::set('mass_mailing', 'unsubscribed', $status, $profile_id);

		return true;
	}


	public static function getUnsubscribeLink( $profile_id )
	{
                $code = md5(uniqid($profile_id));

                SK_Cache::set('unsubscribe' . $code, $profile_id, 60 * 60 * 24 * 7 );

		return SK_Navigation::href('unsubscribe', array('unsubscribe' => $code));
	}

        public static function getProfileIdByCode( $code )
        {
            return (int) SK_Cache::get('unsubscribe' . $code);
        }

	public static function getUnsubscribeEmailText( $profile_id, $type= 'txt')
	{
		if( !intval($profile_id) )
			return '';

		$unsubscribe_link = self::getUnsubscribeLink($profile_id);

		if( self::isProfileUnsubscribed($profile_id) )
			$result = '';
		else
			$result = ($type == 'txt' ? "\n\n" : '<br><br>') . SK_Language::section('mail_template')->text('unsubscribe_link_'.$type, array('unsubscribe_link' => $unsubscribe_link) );

		return $result;
	}

}