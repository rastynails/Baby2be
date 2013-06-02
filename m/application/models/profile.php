<?php defined('SYSPATH') OR die('No direct access allowed.');

class Profile_Model extends Model {

	private $profile_id;
	
	public function __construct( $profile_id )
	{
		parent::__construct();
		
		$this->profile_id = $profile_id;
	}
	
	public function get_field_values( $step )
	{
		$location_fields = array("country_id", "state_id", "zip", "city_id", "custom_location");
				
		app_ProfileField::orderLocationFields($location_fields);
		
		$page_fields = app_FieldView::fields();
		$fields = array();
		foreach ( $page_fields as $step_fields ) {
			foreach ( $step_fields as $item ) {
				if ( $item == "location" ) {
					foreach ( $location_fields as $loc_item ) {
						$fields[] = (in_array($loc_item, array('zip', 'custom_location'))) ? $loc_item : substr($loc_item, 0, strlen($loc_item)-3);						
					}					
				} else {
					$fields[] = $item;
				}
			}
		}
		
		$field_values = app_Profile::getFieldValues($this->profile_id, $fields);
		$display_age = SK_Config::section('profile_fields')->Section('advanced')->date_display_config == 'age';
		
		$out = array();
		foreach ( $page_fields as $step => $step_fields ) {
			
			if ( isset($step_fields[SK_ProfileFields::get("location")->profile_field_id]) ) {
				
				foreach ( $location_fields as $loc_item ) {
					$step_fields[] = (in_array($loc_item, array('zip', 'custom_location'))) ? $loc_item : substr($loc_item, 0, strlen($loc_item)-3);
				}				
				unset($step_fields[SK_ProfileFields::get("location")->profile_field_id]);
			}
			
			foreach ( $step_fields as $name ) {
				$value = $field_values[$name];
				if ( in_array($name, array('state', "city", "country")) ) {
					$name = $name . "_id";
				}
			    try {
                    $pr_f = SK_ProfileFields::get($name);
                } catch (SK_ProfileFieldException $e) {
                    continue;
                }				

			    if ($pr_f->presentation == 'birthdate') {
                    if (!$value || $value == '0000-00-00') {
                        continue;
                    }

                    if ($display_age)
                    {
                        $value = app_Profile::getAge($value);
                    }
                    else
                    {
                       $value = $this->formatDate($value);
                    }
                }
                
                if ($pr_f->presentation == 'date') 
                {
                    if (!$value || $value == '0000-00-00') {
                        continue;
                    }
                    
                    $value = $this->formatDate($value);
                }
                
				$f_type = $this->field_type($pr_f->presentation);
				
				if ( !isset($value) ) {
					continue;
				}
				
				if ( $f_type=="text" ) {
					if ( !strlen(trim($value)) ) {
						continue;
					}
					$value = SK_Language::htmlspecialchars($value);
				} 
				else if ( $f_type=="int" ) {
					if ( !intval($value) ) {
						continue;
					}
					$_value = $value;
					$value = array();
					if ( count($pr_f->values) ) {
						foreach ( $pr_f->values as $item ) 
						{
							if ( in_array($pr_f->presentation, array('fselect', 'fradio')) ) 
							{
								if ( $item == $_value ) {
									$value[] = $item;
									$f_type = "array";	
								}
							} else {
								if ( (int)$item & (int)$_value ) {
									$value[] = $item;
									$f_type = "array";	
								}
							}
						}
					}
				}
				
				$section = in_array($pr_f->name, $location_fields)
					? SK_ProfileFields::get("location")->profile_field_section_id
					: $pr_f->profile_field_section_id;
				
				$out[$step][$section][$pr_f->profile_field_id] = array(
					'value'			=> $value,
					'presentation'	=> $pr_f->presentation,
					'match'	=> $pr_f->matching,
					'type'	=> $f_type,
					'name'	=> $pr_f->name
				);
			}
		}

		return array_filter($out, "count");
	}
	
    private function formatDate( $dateString )
    {
        list($year, $month, $day) = explode('-', $dateString);
        
        $dmy = array(
            'm' => $month,
            'd' => $day,
            'y' => $year
        );
        
        $dateFormatString = SK_Config::section('site.official')->date_format;
        $dateFormatString = !empty($dateFormatString) ? $dateFormatString : 'd-m-y';  
        $dateFormat = explode('-', $dateFormatString);
        
        $date = array();
        
        foreach ( $dateFormat as $i )
        {
            array_push($date, $dmy[$i]);
        }
        
        return implode('-', $date);
    }
	
    private function field_type($presentation)
    {
        switch ($presentation) {
            case "text":
            case "system_text":
            case "textarea":
            case "password":
            case "date":
            case "birthdate":
            case "age_range":
            case "callto":
            case "email":
            case "url":
                return "text";
                break;
            default:
                return "int";
        }
    }
	
	public function get_photos( $profile_id, $page = 1, $limit = 0 )
	{
		$viewer_id = SKM_User::profile_id();
		
		$is_owner = ( $viewer_id == $profile_id );
				
		$where_definition = ( !$is_owner ) ? ' AND `status`="active"' : '';
		
		if ( app_Features::isAvailable(9) )
		{
			$is_friend = app_FriendNetwork::isProfileFriend($profile_id, $viewer_id);
			if (!$is_friend)
				$where_definition .= ' AND `publishing_status`!="friends_only"';
		}
		else
		{
			$where_definition .= ' AND `publishing_status`!="friends_only"';
		}		
		$where_definition .= ' AND `publishing_status`!="password_protected"'; 
		$lim_query = $limit ? sql_placeholder(" LIMIT ?, ?", ($page - 1) * $limit, $limit) : '';
		
		$query = SK_MySQL::placeholder( 'SELECT `photo`.* FROM `'.TBL_PROFILE_PHOTO.'`  AS `photo` 
			LEFT JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
			WHERE `profile_id`=? AND `number`!=0 AND `album`.`photo_id` IS NULL '.$where_definition.'
			ORDER BY `number` ASC' . $lim_query, $profile_id);
		
		$result = SK_MySQL::query($query);
		$photos = array();
		
		while ( $row = $result->fetch_assoc() )
		{
			$row['preview_url'] = app_ProfilePhoto::getUrl($row['photo_id'], app_ProfilePhoto::PHOTOTYPE_PREVIEW );
			$row['thumb_url'] = app_ProfilePhoto::getUrl($row['photo_id'], app_ProfilePhoto::PHOTOTYPE_THUMB );
			$row['view_url'] = app_ProfilePhoto::getUrl($row['photo_id'], app_ProfilePhoto::PHOTOTYPE_VIEW );
			$row['fullsize_url'] = app_ProfilePhoto::getUrl($row['photo_id'], app_ProfilePhoto::PHOTOTYPE_FULL_SIZE );
			$photos[] = $row;			
		}
		
		$query = SK_MySQL::placeholder( 'SELECT COUNT(`photo`.`photo_id`) FROM `'.TBL_PROFILE_PHOTO.'`  AS `photo` 
			LEFT JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
			WHERE `profile_id`=? AND `number`!=0 AND `album`.`photo_id` IS NULL '.$where_definition, $profile_id);
		
		$total = SK_MySQL::query($query)->fetch_cell();
		
		return array('list' => $photos, 'total' => $total);
	}
	
	public function get_prev_photo( $profile_id, $photo_id )
	{
		$viewer_id = SKM_User::profile_id();
		
		$is_owner = ( $viewer_id == $profile_id );
				
		$where_definition = ( !$is_owner ) ? ' AND `status`="active"' : '';
		
		if ( app_Features::isAvailable(9) )
		{
			$is_friend = app_FriendNetwork::isProfileFriend($profile_id, $viewer_id);
			if (!$is_friend)
				$where_definition .= ' AND `publishing_status`!="friends_only"';
		}
		else
		{
			$where_definition .= ' AND `publishing_status`!="friends_only"';
		}
		$where_definition .= ' AND `publishing_status`!="password_protected"';

		$number = SK_MySQL::query(SK_MySQL::placeholder("SELECT `number` FROM `".TBL_PROFILE_PHOTO."` WHERE `photo_id`=? LIMIT 1", $photo_id))->fetch_cell();
		
		$query = SK_MySQL::placeholder( 'SELECT `photo`.`photo_id` FROM `'.TBL_PROFILE_PHOTO.'`  AS `photo` 
			LEFT JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
			WHERE `profile_id`=? AND `number`!=0 AND `album`.`photo_id` IS NULL
			AND `number` < ? '.$where_definition.' ORDER BY `number` DESC LIMIT 1', $profile_id, $number);
		
		$result = SK_MySQL::query($query)->fetch_cell();
		
		return $result;
	}
	
	public function get_next_photo( $profile_id, $photo_id )
	{
		$viewer_id = SKM_User::profile_id();
		
		$is_owner = ( $viewer_id == $profile_id );
				
		$where_definition = ( !$is_owner ) ? ' AND `status`="active"' : '';
		
		if ( app_Features::isAvailable(9) )
		{
			$is_friend = app_FriendNetwork::isProfileFriend($profile_id, $viewer_id);
			if (!$is_friend)
				$where_definition .= ' AND `publishing_status`!="friends_only"';
		}
		else
		{
			$where_definition .= ' AND `publishing_status`!="friends_only"';
		}
		$where_definition .= ' AND `publishing_status`!="password_protected"';

		$number = SK_MySQL::query(SK_MySQL::placeholder("SELECT `number` FROM `".TBL_PROFILE_PHOTO."` WHERE `photo_id`=? LIMIT 1", $photo_id))->fetch_cell();
		
		$query = SK_MySQL::placeholder( 'SELECT `photo`.`photo_id` FROM `'.TBL_PROFILE_PHOTO.'`  AS `photo` 
			LEFT JOIN `' . TBL_PHOTO_ALBUM_ITEMS . '` AS `album` ON `photo`.`photo_id` = `album`.`photo_id`
			WHERE `profile_id`=? AND `number`!=0 AND `album`.`photo_id` IS NULL
			AND `number` > ? '.$where_definition.' ORDER BY `number` ASC LIMIT 1', $profile_id, $number);
		
		$result = SK_MySQL::query($query)->fetch_cell();
		
		return $result;
	}
	
    public function request_email_verification( $email )
    {
        $reg_exp = SK_ProfileFields::get('email')->regexp;
          
        if ( ((isset($reg_exp) && strlen(trim($reg_exp))) && !preg_match($reg_exp, $email)) || !strlen(trim($email)) )
        {
            return -2;
        }   
        $_code = sha1(time());
        
        $query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_EMAIL_VERIFY_CODE."` 
            (`profile_id`, `code`, `create_date`, `expiration_date`) 
            VALUES( ?, '?', ?, ? )", 
            $this->profile_id, $_code, time(), (time() + 7 * 86400)
        );
        
        SK_MySQL::query($query);
        
        // get profile info
        $_email = app_Profile::getFieldValues($this->profile_id, 'email');

        // update email if changed
        if ( ($_email != trim($email)) && (app_Profile::setFieldValue($this->profile_id, 'email', $email) < 0) )
        {
            return -3;
        }
        
        $msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
                ->setRecipientProfileId($this->profile_id)
                ->setTpl('email_verify')
                ->assignVar('site_url', url::base())
                ->assignVar('verify_email_url', url::base() . 'emailverify/' . trim($_code));
                
        app_Mail::send($msg);   
    
        return 1;
    }
}