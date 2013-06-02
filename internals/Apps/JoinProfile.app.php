<?php

require_once DIR_SITE_ROOT . 'facebook_connect' . DIRECTORY_SEPARATOR . 'init.php';

class app_JoinProfile
{
	private static $session;

	private static $active_step;


	public static function construct()
	{

		if (!isset($_SESSION["%join%"])) {
			$_SESSION["%join%"] = array();
		}
		self::$session = & $_SESSION["%join%"];
		self::$session["steps"] = count(self::$session["steps"]) ? self::$session["steps"] : array();
		self::$session["fields"] = count(self::$session["fields"]) ? self::$session["fields"] : array();

		/*if (!SK_HttpRequest::isXMLHttpRequest()) {
			printArr($_SESSION["%join%"]);
		}*/
		//self::clearSession();
	}

	public static function session() {
		return self::$session;
	}

	public static function getValue($field_name) {
		return @self::$session["fields"][$field_name];
	}

	public static function setValue($field_name, $value) {
		self::$session["fields"][$field_name] = $value;
	}

	public static function step() {
		$step =  @self::$session["steps"][0];
		if (!isset($step) || $step == 1) {
			return 1;
		}
		return $step;
	}

	public static function setSteps($reliant_field_value) {
		$reliant_field_value = !isset($reliant_field_value) ? false : $reliant_field_value;
		$steps = app_FieldForm::formSteps(app_FieldForm::FORM_JOIN, $reliant_field_value);
		self::$session["steps"] = array();
		foreach ($steps as $item) {
			self::$session["steps"][] = $item;
		}
	}

	public static function setStepProcessed($step) {
		array_shift(self::$session["steps"]);
	}

	public static function lastStep($step = null) {

		if (isset($step) && intval($step)) {
			return (count(self::$session["steps"]) == 1);
		} else {
			return self::$session["steps"][count(self::$session["steps"])];
		}
	}

	public static function clearSession() {
		self::$session["steps"] = array();
		self::$session["fields"] = array();
	}

	public static function hasCaptcha($step=null) {
		$captcha = (bool)SK_Config::section('site')->Section('additional')->Section('profile')->captcha_on_join;
		return isset($step) ? ($captcha && $step == 1) : $captcha;
	}



	/**
	 * Method for analyse form session fields adn save their to databse
	 *
	 */
	public static function joinProfile()
	{
		$session_fields = self::$session["fields"];

		$fields = array();

		$query = 'SELECT `field`.`profile_field_id`, `field`.`name`, `field`.`presentation` FROM `'.TBL_PROF_FIELD_PAGE_LINK.'` AS `page`
					LEFT JOIN `'.TBL_PROF_FIELD.'` AS `field` USING(`profile_field_id`)
					WHERE `page`.`profile_field_page_type`="join"';
		$result = SK_MySQL::query($query);

        $profile_fields_for_review = array();

		while ($field = $result->fetch_object()){

			if (!isset($session_fields[$field->name])){
				continue;
			}

			switch ($field->presentation)
			{
				case 'location':
					$location = &$session_fields[$field->name];

					foreach ($location as $key => $value){
						$fields[$key] = $value;
					}
					break;
				case 'age_range':
					$fields[$field->name] = $session_fields[$field->name][0] . '-' . $session_fields[$field->name][1];
					break;

				case 'password':
                    $fields[$field->name] = app_Passwords::hashPassword($session_fields[$field->name]);
                    break;

				case 'birthdate':
				case 'date':

                    if ( empty($session_fields[$field->name]['year']) || empty($session_fields[$field->name]['month']) || empty($session_fields[$field->name]['day']) )
                    {
                        $fields[$field->name] = null;
                    }
                    else
                    {
                        $fields[$field->name] = $session_fields[$field->name]['year'] . '-' . $session_fields[$field->name]['month'] . '-' . $session_fields[$field->name]['day'];
                    }

					break;

				case 'multiselect':
				case 'multicheckbox':
					$fields[$field->name] = array_sum($session_fields[$field->name]);
					break;

				default:
					$fields[$field->name] = @$session_fields[$field->name];

			}

            $profile_fields_for_review[] = $field->profile_field_id;
		}

		$base = array();
		$extended = array();

		foreach ($fields as $name => $value)
		{
			if ($name == "photo_upload") {
				continue;
			}
			try {
				if (SK_ProfileFields::get($name)->base_field)
				{

					/*$base['fields'][$name] = preg_match("/\D+/",$value)
						|| ($name=='zip') ? SK_MySQL::placeholder("'?'", $value) : (isset($value) && strlen($value) ? $value : 'NULL');	*/
					$base['fields'][$name] = isset($value)? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)";
				}
				else
				{
					//$extended['fields'][$name] = preg_match("/\D+/",$value) ? SK_MySQL::placeholder("'?'", $value) : (isset($value) && strlen($value) ? $value : 'NULL');
					$extended['fields'][$name] = isset($value)? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)";
				}
			} catch (SK_ProfileFieldException $e){}
		}

		$base['fields']['language_id'] = SK_Language::current_lang_id();
		$base['fields']['join_stamp'] = time();

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

        foreach($profile_fields_for_review as $profile_field_id)
        {
            app_ProfileField::markProfileFieldForReview($id, $profile_field_id);
        }

		try{
			if (isset($session_fields["photo_upload"]) && strlen($session_fields["photo_upload"])) {
				$photo_id = app_ProfilePhoto::upload($session_fields["photo_upload"], 1, $id);
				app_ProfilePhoto::createThumbnail($photo_id);

				app_UserPoints::earnCreditsForActionAlternative($id, 'avatar_add', true);
			}
		} catch (SK_ProfilePhotoException $e) {}

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

        // give default membership
		app_Membership::giveDefaultMembershipOnRegistration( $profile_id );

		// catch affiliate
		app_Affiliate::catchForRegistration( $profile_id );

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

		$profile_info = app_Profile::getFieldValues($profile_id, array( 'email', 'username') );

		if ( !app_Profile::email_verified($profile_id ) ){
			app_EmailVerification::addRequestEmailVerification( $profile_id, $profile_info['email'] );
		}

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

		$profile_info = app_Profile::getFieldValues($profile_id, array( 'password', 'username') );

		SK_HttpUser::authenticate($profile_info['username'], $profile_info['password'], false);

        // Update profile activity info
		app_Profile::updateProfileActivity( $profile_id );

		// Update profile status
		app_Profile::updateProfileStatus( $profile_id, SK_Config::section('site')->Section('automode')->set_profile_status_on_join );

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
		app_UserPoints::earnCreditsForAction($profile_id, 'new_user');

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

		return true;
	}

}


app_JoinProfile::construct();