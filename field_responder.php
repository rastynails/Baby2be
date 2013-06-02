<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'internals'.DIRECTORY_SEPARATOR.'Header.inc.php';

switch ($_REQUEST['action']){
	case 'check_username_exists':
		$username = trim($_REQUEST['username']);
		$result = 0;
		
		$regexp = SK_ProfileFields::get('username')->regexp;
		if ( !strlen(trim($username)) || ($regexp && !preg_match($regexp, $username))) {
			$result = 3;
		}
		else 
		{
			if ( app_Username::isUsernameInRestrictedList( $username ) ) {
				$result = 4;
			} else {
				$query = SK_MySQL::placeholder("SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `username`='?'",$username);
				$result = (bool)SK_MySQL::query($query)->fetch_cell() ? 2 : 1;
			}
		}	
        if ($result == 1)
        {
            echo json_encode($result);
        }
        else
        {
            switch ($result)
            {
                case 2:
                    echo json_encode(SK_Language::text('%forms._fields.username.errors.already_exists'));
                    break;
                case 4:
                    echo json_encode(SK_Language::text('%forms._fields.username.errors.is_restricted'));
                    break;
                default:
                    echo json_encode(SK_Language::text('%profile_fields.error_msg.'.SK_ProfileFields::get('username')->profile_field_id));
                    break;
            }            
        }		
		
		break;
		
	case 'check_email_exists':
		
		$email= trim($_REQUEST['email']);
        $email = strtolower($email);
		$result = 0;
		$regexp = SK_ProfileFields::get('email')->regexp;
		
		if (!strlen(trim($email)) || ($regexp && !preg_match($regexp, $email))) {
			$result = 3;
		}
		else 
		{
			$query = SK_MySQL::placeholder("SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `email`='?'",$email);
			$result = (bool)SK_MySQL::query($query)->fetch_cell() ? 2 : 1;
		}	
        
        if ($result == 1)
        {
            echo json_encode($result);
        }
        else
        {
            switch ($result)
            {
                case 2:
                    echo json_encode(SK_Language::text('%forms._fields.email.errors.already_exists'));
                    break;
                default:
                    echo json_encode(SK_Language::text('%profile_fields.error_msg.'.SK_ProfileFields::get('email')->profile_field_id));
                    break;
            }           
        }
		
		break;
		
	case 'process_location':
		
		$result= array();
		
		$result = field_location::ajax_handler($_REQUEST);
		
		echo json_encode($result);
		
		break;
		
	case 'process_mileage':
		
		$result= array();
		$result = field_mileage::ajax_handler($_REQUEST);
		
		echo json_encode($result);
		
		break;
		
	case 'change_captcha_image':
		
		$result = field_captcha::generateNumber();
		echo json_encode($result);
		
		break;
		
	case 'location_get_city':
		$result = array();
		
		$str = trim($_REQUEST['str']);
		$state_id = trim($_REQUEST['state_id']);
		$result = app_Location::getSaggestCities($str, $state_id);
		echo json_encode($result);
		break;
		
	case 'suggest_mailbox_recipients':
                
        $str = trim($_REQUEST['str']);
        $result = app_MailBox::suggestRecipients(SK_HttpUser::profile_id(), $str);
                
        echo json_encode($result);
        break;		
}