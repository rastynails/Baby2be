<?php

class FBC_Pofile
{
    public static function join($fieldsToSave)
	{
		$fields = array();

		$query = 'SELECT `field`.`profile_field_id`, `field`.`name`,`field`.`presentation` FROM `'.TBL_PROF_FIELD_PAGE_LINK.'` AS `page`
					LEFT JOIN `'.TBL_PROF_FIELD.'` AS `field` USING(`profile_field_id`)
					WHERE `page`.`profile_field_page_type`="join"';
		$result = SK_MySQL::query($query);

        $profile_fields_for_review = array();

		while ($field = $result->fetch_object()){

			if (!isset($fieldsToSave[$field->name])){
				continue;
			}

			$fields[$field->name] = @$fieldsToSave[$field->name];
            $profile_fields_for_review[] = $field->profile_field_id;
		}

		$base = array();
		$extended = array();

		foreach ($fields as $name => $value)
		{
			try {
				if (SK_ProfileFields::get($name)->base_field)
				{
					$base['fields'][$name] = isset($value)? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)";
				}
				else
				{
					$extended['fields'][$name] = isset($value)? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)";
				}
			} catch (SK_ProfileFieldException $e){}
		}

		$base['fields']['language_id'] = SK_Language::current_lang_id();
		$base['fields']['join_stamp'] = time();
		$base['fields']['email_verified'] = '"yes"';

		$fields = array_keys($base['fields']);
		$values = array_values($base['fields']);
		$query = "INSERT INTO `".TBL_PROFILE."`(`".implode('`, `',$fields)."`) VALUES(".implode(',',$values).")" ;


		SK_MySQL::query($query);

		$id = SK_MySQL::insert_id();
		$extended['fields']['profile_id'] = $id;
		$fields = array_keys($extended['fields']);
		$values = array_values($extended['fields']);

		$query = "INSERT INTO `".TBL_PROFILE_EXTEND."`(`".implode('`, `',$fields)."`) VALUES(".implode(',',$values).")" ;


		SK_MySQL::query($query);

	    if (!empty($fieldsToSave["avatar"])) {
		    self::saveAvatar($id, $fieldsToSave["avatar"]);
			app_UserPoints::earnCreditsForActionAlternative($id, 'avatar_add', true);
		}

        foreach($profile_fields_for_review as $profile_field_id)
        {
            app_ProfileField::markProfileFieldForReview($id, $profile_field_id);
        }

		return self::onJoin($id);

	}


	/**
	 * On join handle
	 *
	 * @param int $profile_id
	 */
	private static function onJoin( $profile_id )
	{
                app_Invitation::setInviteResetTime($profile_id);

		$invited2friends = SK_Config::section('site.additional.profile')->invited_to_friends;

		if ($invited2friends)
                {
                    // add join member to friend list, if invitation exists
                    $inviter_profile_ids = array(
                        app_Invitation::getSenderProfileId()
                    );

                    //Facebook Invite
                    if ( !empty($_SESSION['%%facebook_invite_data%%']) )
                    {
                        if ( !empty($_SESSION['%%facebook_invite_data%%']['requestIds']) )
                        {
                            FBC_Service::getInstance()->removeInviteRequests($_SESSION['%%facebook_invite_data%%']['requestIds']);
                        }

                        if ( !empty($_SESSION['%%facebook_invite_data%%']['inviters']) )
                        {
                            foreach ( $_SESSION['%%facebook_invite_data%%']['inviters'] as $invId )
                            {
                                $inviter_profile_ids[] = $invId;
                            }
                        }
                    }

                    foreach ( $inviter_profile_ids as $inviter_profile_id )
                    {
                        $service = new SK_Service('creat_friends_network', $inviter_profile_id);
                        $service_state = $service->checkPermissions();

                        if ( $inviter_profile_id && ($service_state == SK_Service::SERVICE_FULL ) )
                        {
                            if (app_FriendNetwork::addToFriend($inviter_profile_id, $profile_id))
                            {
                                    $service->trackServiceUse();
                            }
                        }
                    }
		}


		// give default membership
		app_Membership::giveDefaultMembershipOnRegistration( $profile_id );

		$profile_info = app_Profile::getFieldValues($profile_id, array( 'email', 'username') );

		if (SK_Config::section('site')->Section('additional')->Section('profile')->send_welcome_letter){
			try
			{
				$msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
						->setRecipientProfileId($profile_id)
						->setPriority(1)
						->setTpl('welcome_letter');

				app_Mail::send($msg);
			}
			catch (SK_EmailException $e)
			{}
		}

		// Insert default template data
		app_ProfileComponentService::stAddDefaultCmps($profile_id);

		// set profile IP
		app_Profile::updateProfileJoinIP( $profile_id );

		// Update profile status
		app_Profile::updateProfileStatus( $profile_id, SK_Config::section('site')->Section('automode')->set_profile_status_on_join );

		app_UserPoints::earnCreditsForAction($profile_id, 'new_user');

                //CATCH REFERRAL FOR REGISTRATION
		$code = app_Invitation::getRegistrationCode();
		if($code)
		{
			$query = SK_MySQL::placeholder( "SELECT `referrer_id`, `time_stamp` FROM `".TBL_REFERRAL_INVITE."` WHERE `code`='?'", $code );
			$invite_info = SK_MySQL::query($query)->fetch_assoc();
		}
		else
			$invite_info=array();

		if($invite_info != array())
		{
			$referrer_id = $invite_info['referrer_id'];
			$date_reffered = $invite_info['time_stamp'];

			$query = 'INSERT INTO `'.TBL_REFERRAL_RELATION."`(`referrer_id`,`referral_id`, `date_reffered`) VALUES($referrer_id, $profile_id, $date_reffered)";
			SK_MySQL::query($query);

			$query = 'SELECT `total_referrals` FROM `'.TBL_REFERRAL."` WHERE `referral_id`=$referrer_id";
			$total_referrals = SK_MySQL::query($query)->fetch_cell();

			$total_referrals = $total_referrals + 1;
			$query = 'UPDATE `'.TBL_REFERRAL."` SET `total_referrals`=$total_referrals WHERE `referral_id`=$referrer_id";
			SK_MySQL::query($query);

			app_Referral::trackReferralAdd($referrer_id);

			// give credits for new invited member
			app_UserPoints::earnCreditsForAction($referrer_id, 'referral');
		}

		$query = SK_MySQL::placeholder('INSERT INTO `'.TBL_REFERRAL."`(`referral_id`) VALUES(?)", $profile_id);

		SK_MySQL::query($query);





		// unset invite registration code
		app_Invitation::unsetRegistrationCodeInfo();

        if (SK_Config::section('site.additional.profile')->send_onjoin_mail)
		{
		    $fields = array('username', 'sex', 'birthdate', 'email', 'country', 'state', 'city', 'zip', 'custom_location');
		    $profileInfo = app_Profile::getFieldValues($profile_id, $fields);
		    if (!empty($profileInfo['sex']))
		    {
		        $profileInfo['sex'] = SK_Language::text('profile_fields.value.sex_' . intval($profileInfo['sex']));
		    }

                    $mail = app_Mail::createMessage()
                        ->setRecipientEmail(SK_Config::section("site.official")->site_email_main)
                        ->setTpl('onjoin_mail')
                        ->assignVarRange($profileInfo);
                    app_Mail::send($mail);
		}

        // Newsfeed
        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_NEWSFEED,
                    'entityType' => 'profile_join',
                    'entityId' => $profile_id,
                    'userId' =>  $profile_id,
                    'status' => ( SK_Config::section('site')->Section('automode')->set_profile_status_on_join == 'active' ) ? 'active' : 'approval'
                )
            );
            app_Newsfeed::newInstance()->action($newsfeedDataParams);
        }

		return $profile_id;
	}

	public static function findProfileIdByEmail($email)
	{
	    $query = SK_MySQL::placeholder('SELECT `profile_id` FROM `' . TBL_PROFILE . '` WHERE `email`="?"', $email);

	    return SK_MySQL::query($query)->fetch_cell();
	}

	public function saveAvatar($profileId, $path)
	{
	    $index = rand(0,99);
	    $slot = 0;

	    $query = SK_MySQL::placeholder("SELECT `photo_id` FROM `" . TBL_PROFILE_PHOTO . "` WHERE `profile_id`=? AND `number`=0", $profileId);
	    $photoId = SK_MySQL::query($query)->fetch_cell();
	    $oldPath = false;
            $status = SK_Config::section('site')->Section('automode')->set_active_photo_on_upload ? 'active' : 'approval';

	    if (empty($photoId))
	    {

	        $query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_PHOTO."`( `profile_id`, `index`, `number`, `status`, `added_stamp` )
				VALUES ( ?, ?, ?, '?', ?)", $profileId, $index, $slot, $status, time());
		    SK_MySQL::query($query);

		    $photoId = SK_MySQL::insert_id();
	    }
	    else
	    {
	        $oldPath = app_ProfilePhoto::getPath($photoId, app_ProfilePhoto::PHOTOTYPE_THUMB);

	        $query = SK_MySQL::placeholder(
	        	"UPDATE `".TBL_PROFILE_PHOTO."` SET `index`=?, `added_stamp`=?, `status`='?' WHERE `photo_id`=?"
	        , $index, time(), $status, $photoId);
		    SK_MySQL::query($query);

		    app_ProfilePhoto::clearStoragePhotoInfo($photoId);
	    }

	    try
	    {
	        $configs = SK_Config::section('photo')->Section('general');

	        app_Image::convert($path);
	        $avatarPath = app_ProfilePhoto::getPath($photoId, app_ProfilePhoto::PHOTOTYPE_THUMB);
	        app_Image::resize($path, $configs->thumb_width, $configs->thumb_width, true, $avatarPath);
	        @chmod($avatarPath, 0777);
	        @unlink($path);
	        if ($oldPath)
	        {
	            @unlink($oldPath);
	        }
	    }
	    catch (Exception $e)
	    {
	        @unlink($avatarPath);
	        SK_MySQL::query("DELETE FROM `" . TBL_PROFILE_PHOTO . "` WHERE `photo_id`=$photoId");

	        return false;
	    }

	    return true;
	}

	public static function edit($profileId, $fieldsToSave)
	{
        $fields = array();

        $query = 'SELECT `field`.`name`,`field`.`presentation`, `field`.`base_field` FROM `'.TBL_PROF_FIELD_PAGE_LINK.'` AS `page`
        			LEFT JOIN `'.TBL_PROF_FIELD.'` AS `field` USING(`profile_field_id`)
        			WHERE `page`.`profile_field_page_type`="edit"';
        $result = SK_MySQL::query($query);

        while ($field = $result->fetch_object()){

        	if (!array_key_exists($field->name, $fieldsToSave)) {
        		continue;
        	}

        	$fields[$field->name] = @$fieldsToSave[$field->name];
        }

        if ( isset($fields['birthdate']) )
        {
            $birthdate = app_Profile::getFieldValues($profileId, 'birthdate');
            if ( !empty($birthdate) && $birthdate != '0000-00-00' )
            {
                unset($fields['birthdate']);
            }
        }

        $base = array();

        $extend = array();
        foreach ($fields as $name => $value)
        {
        	try {
        		$pr_field = SK_ProfileFields::get($name);
        	}
        	catch (Exception $e){
        		continue;
        	}

        	if ($pr_field->base_field)
        	{
        		$base[] = "`$name`=" . (isset($value)? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)");
        	}
        	else
        	{
        		$extend[] = "`$name`=" . (isset($value)? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)");
        	}
        }

        $records_affected = 0;
        if (count($base)) {
        		$query = "UPDATE `".TBL_PROFILE."` SET ".implode(',',$base)." WHERE `profile_id`=" . $profileId;
        		SK_MySQL::query($query);
        }
        $records_affected += SK_MySQL::affected_rows();

        if (count($extend)) {
        		$query = "UPDATE `".TBL_PROFILE_EXTEND."` SET ".implode(',',$extend)." WHERE `profile_id`=" . $profileId;
        		SK_MySQL::query($query);
        }
        $records_affected += SK_MySQL::affected_rows();

	    if (!empty($fieldsToSave["avatar"])) {
		    self::saveAvatar($profileId, $fieldsToSave["avatar"]);
			app_UserPoints::earnCreditsForActionAlternative($profileId, 'avatar_add', true);
		}

        return (bool) $records_affected;
	}

}