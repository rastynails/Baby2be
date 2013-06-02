<?php defined('SYSPATH') OR die('No direct access allowed.');

class Index_Controller extends Controller {
	
	private $session;
		
	function __construct()
	{
		parent::__construct();
		
		$this->session = Session::instance();
		$form = $_POST;
		
		if ( SKM_User::is_authenticated())
		{
			url::redirect('home');
		}
		
		if ( isset($form['action']) )
		{
            switch ( $form['action'] )
            {
                case 'login':
                    if ( isset($form['login']) && isset($form['password']) )
                    {
                        $login = trim($form['login']);
                        $pass = trim($form['password']);
                        try 
                        {
                            $auth = SKM_User::authenticate($login, $pass);
                        }
                        catch ( SKM_UserException $e )
                        {
                            $field = null;
                            switch ( $e->getCode() )
                            {
                                case 1:
                                    $field = 'login';
                                    break;
                                case 2:
                                    $field = 'password';
                                    break;
                            }

                            SKM_Template::addMessage($e->getMessage(), 'error');
                            $auth = false;
                        }

                        if ( $auth ) 
                        {
                            SKM_Template::addMessage(SKM_Language::text("%mobile.login_complete"));

                            $url = @$this->session->get_once("requested_url");
                            if ( isset($url) )
                            {
                                url::redirect($url);
                            }
                            else
                            {
                                url::redirect('home');
                            }
                        }
                    }
                    
                    url::redirect('index');
                    
                    break;
                    
                case 'join':
                	
                    if ( SK_HttpRequest::isIpBlocked() )
                    {
                        SKM_Template::addMessage(SKM_Language::text('%forms.sign_in.error_msg.blocked_member'));
                        url::redirect('index');
                    }
        
                    $fields = $this->getJoinFields();
                    $error = false;

                    foreach ( $fields as $field )
                    {
                        if ( !isset($form[$field['name']]) || !is_array($form[$field['name']]) && !strlen($form[$field['name']]) ) 
                        {
                            SKM_Template::addMessage(SKM_Language::text("%profile_fields.error_msg." . $field['id']));
                            $error = true;

                            if ( $field['confirm'] && ( !isset($form["re_".$field['name']]) || !strlen($form["re_".$field['name']]) ) )
                            {
                                SKM_Template::addMessage(SKM_Language::text('%forms._errors.required', array('label' => SKM_Language::text("%profile_fields.label_join." . $field['id']))) );
                            }
                        }
                        else 
                        {
                            if ( $field['name'] == 'match_sex' )
                                $session_fields[$field['name']] = array_sum($form[$field['name']]);
                            else if ( $field['name'] == 'birthdate' )
                            {
                                if ( !(int)$form[$field['name']]['year'] || !(int)$form[$field['name']]['month'] || !(int)$form[$field['name']]['day'] )
                                {
                                    SKM_Template::addMessage(SKM_Language::text('%forms._errors.required', array('label' => SKM_Language::text("%profile_fields.label_join." . $field['id']))) );
                                }
                                $session_fields[$field['name']] = $form[$field['name']];
                            }
                            else
                                $session_fields[$field['name']] = $form[$field['name']];
                        }
                        
                        if ( isset($form["re_".$field['name']]) && strlen($form["re_".$field['name']] ) )
                            $session_fields['re_' . $field['name']] = $form['re_'.$field['name']];
                    }
                    
                    if ( !$form['country_id'] )
                    {
                        SKM_Template::addMessage(SKM_Language::text('%forms._errors.required', array('label' => SKM_Language::text('%profile_fields.label.112'))));
                        $error = true;
                    }

                    $this->saveSessionData($session_fields);

                    if ( $error )
                    {
                        url::redirect('index');
                    }
                    else
                    {
                        // check username
                        if ( app_Username::isUsernameInRestrictedList( $form['username'] ) ) {
                            SKM_Template::addMessage(SKM_Language::text('%forms._fields.username.errors.is_restricted'), 'error');
                            url::redirect('index');
                        } else {
                            $query = SK_MySQL::placeholder("SELECT `profile_id` FROM `".TBL_PROFILE."` WHERE `username`='?'", $form['username']);
                            if (SK_MySQL::query($query)->fetch_cell()) {
                                SKM_Template::addMessage(SKM_Language::text('%forms._fields.username.errors.already_exists'), 'error');
                                url::redirect('index');
                            }
                        }

                        if ( !preg_match(SK_ProfileFields::get('username')->regexp, $form['username']) ){
                            SKM_Template::addMessage(SKM_Language::text('%mobile.incorrect_value', array('field' => SKM_Language::text("%profile_fields.label_join.2"))), 'error');
                            url::redirect('index');
                        }

                        // check password
                        if ( !preg_match(SK_ProfileFields::get('password')->regexp, $form['password']) )
                        {
                            SKM_Template::addMessage(SKM_Language::text('%mobile.incorrect_value', array('field' => SKM_Language::text("%profile_fields.label_join.3"))), 'error');
                            url::redirect('index');
                        }

                        if ( SK_ProfileFields::get('password')->confirm && $form['password'] != $form['re_password'] )
                        {
                            SKM_Template::addMessage(SKM_Language::text('%profile_fields.error_msg.confirm_3'), 'error');
                            url::redirect('index');
                        }

                        // check email
                        if ( !preg_match(SK_ProfileFields::get('email')->regexp, $form['email']) )
                        {
                            SKM_Template::addMessage(SKM_Language::text('%mobile.incorrect_value', array('field' => SKM_Language::text("%profile_fields.label_join.1"))), 'error');
                            url::redirect('index');
                        }

                        $query = SK_MySQL::placeholder("SELECT COUNT(`profile_id`) FROM `".TBL_PROFILE."` WHERE `email`='?'", $form['email']);
                        if (SK_MySQL::query($query)->fetch_cell()) {
                            SKM_Template::addMessage(SKM_Language::text('%forms._fields.email.errors.already_exists'), 'error');
                            url::redirect('index');
                        }

                        if ( SK_ProfileFields::get('email')->confirm && $form['email'] != $form['re_email'] )
                        {
                            SKM_Template::addMessage(SKM_Language::text('%profile_fields.error_msg.confirm_1'), 'error');
                            url::redirect('index');
                        }

                        $fields = self::getFieldsArray();
                        
                        foreach ( $fields as $key => $val )
                        {
                            if ( in_array($val, array('birthdate', 'match_agerange')))
                            {
                                unset($fields[$key]);
                            }
                        }

                        $fields_arr = array();

                        foreach ( $fields as $key => $field )
                        {
                            if ( $field == 'match_sex' )
                                $fields_arr[$field] = array_sum($form[$field]);
                            else
                                $fields_arr[$field] = $form[$field];
                        }

                        $base = array();
                        $extended = array();

                        foreach ( $fields_arr as $name => $value )
                        {
                            try 
                            {
                                if ( SK_ProfileFields::get($name)->base_field )
                                {
                                    if ( SK_ProfileFields::get($name)->presentation == 'password' )
                                    {
                                        $value = app_Passwords::hashPassword($value);
                                    }
                                    $base['fields'][$name] = isset($value)? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)";
                                }
                                else
                                {
                                     $extended['fields'][$name] = isset($value)? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)";
                                }
                            } catch ( SK_ProfileFieldException $e ){}
                        }

                        $base['fields']['language_id'] = SKM_Language::current_lang_id();
                        $base['fields']['join_stamp'] = time();

                        if ( isset($form['country_id']) )
                            $base['fields']['country_id'] = SK_MySQL::placeholder("'?'", $form['country_id']);

                        if ( isset($form['state_id']) )
                            $base['fields']['state_id'] = SK_MySQL::placeholder("'?'", $form['state_id']);

                        if ( isset($form['city_id']) )
                            $base['fields']['city_id'] = SK_MySQL::placeholder("'?'", $form['city_id']);

                        if ( isset($form['match_agerange']['from']) && isset($form['match_agerange']['to']) )
                        {
                            $from = min(array($form['match_agerange']['from'], $form['match_agerange']['to']));
                            $to = max(array($form['match_agerange']['from'],  $form['match_agerange']['to']));
                            $base['fields']['match_agerange'] = SK_MySQL::placeholder("'?'", $from . '-' . $to);
                        }
                        
                        if ( isset($form['birthdate']['year']) && isset($form['birthdate']['month']) && isset($form['birthdate']['day']) )
                        { 
                            $base['fields']['birthdate'] = SK_MySQL::placeholder("'?'", sprintf("%04d-%02d-%02d", $form['birthdate']['year'], $form['birthdate']['month'], $form['birthdate']['day'])); 
                        }
                            
                        $fields = array_keys($base['fields']);
                        $values = array_values($base['fields']);
                        $query = "INSERT INTO `".TBL_PROFILE."`(`".implode('`, `', $fields)."`) VALUES(".implode(',',$values).")" ;
                        SK_MySQL::query($query);

                        $id = SK_MySQL::insert_id();
                        $extended['fields']['profile_id'] = $id;
                        $fields = array_keys($extended['fields']);
                        $values = array_values($extended['fields']);

                        $query = "INSERT INTO `".TBL_PROFILE_EXTEND."`(`".implode('`, `', $fields)."`) VALUES(".implode(',',$values).")" ;
                        SK_MySQL::query($query);

                        $this->joinComplete($id);
                    }
                    break;
            }
		}
		
		elseif (isset($form['login']) && isset($form['password'])){
			$login = trim($form['login']);
			$pass = trim($form['password']);  
			try {
				$auth = SKM_User::authenticate($login, $pass);
			}
			catch (SKM_UserException $e){
				$field = null;
				switch ($e->getCode()){
					case 1:
						$field = 'login';
						break;
					case 2:
						$field = 'password';
						break;
				}
				
				SKM_Template::addMessage($e->getMessage(), 'error');
				$auth = false;
			}

			if ( $auth ) {
				SKM_Template::addMessage(SKM_Language::text("%mobile.login_complete"));
				
				$url = @$this->session->get_once("requested_url");
				if (isset($url))
					url::redirect($url);
				else 
					url::redirect('home');
			}
		}
	}
	
	public function index()
	{	
		$view = new View('default');
		
		$view->header = SKM_Template::header(); 
		
		// menu
		$m = new SKM_Menu('main', 'home', SKM_Language::text('%nav_doc_item.sign_in') );
		$view->menu = $m->get_view();

		// join
        $fields = $this->getJoinFields();
        SKM_Template::addTplVar('fields', $fields);
        SKM_Template::addTplVar('fieldnames', self::getFieldsArray());
        SKM_Template::addTplVar('sval', $_SESSION['join_fields']);
        SKM_Template::addTplVar('countries', app_Location::Countries());
        SKM_Template::addTplVar('allow_registration', SK_Config::section('mobile')->get('allow_registration'));
        
		$view->content = new View('index', SKM_Template::getTplVars());
		$view->footer = SKM_Template::footer();
		
		$view->render(TRUE);			
	}
	
	private function getFieldsArray()
	{
        $fields = array('username', 'password', 'sex', 'match_sex', 'email', 'birthdate', 'match_agerange');
        
        try {
            $f = SK_ProfileFields::get('i_am_at_least_18_years_old');
            if ( $f->join_page )
            {
                $fields[] = 'i_am_at_least_18_years_old';
            }
        }
        catch ( SK_ProfileFieldException $e ) {}

        try {
    	    $f = SK_ProfileFields::get('i_agree_with_tos');
            if ( $f->join_page )
            {
                $fields[] = 'i_agree_with_tos';
            }
        }
        catch ( SK_ProfileFieldException $e ) {}
        
        return $fields;
	}
	
    private function getJoinFields()
    {
        $fields = self::getFieldsArray();

        foreach ( $fields as $field )
        {
            $pr_field = SK_ProfileFields::get($field);

            $tpl_field = array();
            
            $tpl_field['name'] = $pr_field->name;
            $tpl_field['presentation'] = $pr_field->presentation;
            $tpl_field['confirm'] = $pr_field->confirm;
            $tpl_field['id'] = $pr_field->profile_field_id;

            if ( $pr_field->name == 'birthdate' )
            {
                list($start, $end) = explode('-', $pr_field->custom);
                //$date = getdate();
                $max = $end;
                $min = $start;
                
                $years = array();
                for ( $a = $max; $a >= $min; $a-- ) {
                    $years[] = $a;
                }
                $months = array();
                for ( $a = 1; $a <= 12; $a++ ) {
                    $months[] = $a;
                }
                $days = array();
                for ( $a = 1; $a <= 31; $a++ ) {
                    $days[] = $a;
                }
                
                $tpl_field['years'] = $years; 
                $tpl_field['months'] = $months;
                $tpl_field['days'] = $days;                
            }
            else if ( $pr_field->name == 'match_agerange' )
            {
                $range = explode('-', $pr_field->match_field->custom);           
                $date = getdate();
                
                $min = $date['year'] - $range[1];
                $max = $date['year'] - $range[0];
                
                $from = array();
                $to = array();
                for ( $a = $min; $a <= $max; $a++ ) 
                    $from[] = $a;
                    
                for ( $a = $max; $a >= $min; $a-- )
                    $to[] = $a;
                    
                $tpl_field['from'] = $from;
                $tpl_field['to'] = $to;
            }
            else if ( $pr_field->name == 'i_agree_with_tos' )
            {
                $tpl_field['label'] = str_replace('/terms.php', url::base().'terms/', SKM_Language::text('profile_fields.label_join.'.$tpl_field['id']));
            }
            
            if (count($pr_field->values[0]))
            {
                foreach ( $pr_field->values as $val )
                {
                    $value['val'] = $val;
                    if ( $pr_field->name == 'match_sex')
                    {
                        $value['checked'] = isset($_SESSION['join_fields'][$pr_field->name]) && ((int)$_SESSION['join_fields'][$pr_field->name] & (int)$val);
                    }
                    $values[] = $value;                    
                }

                $tpl_field['values'] = $values;
                unset($values);
            }

            $tpl_fields[$pr_field->name] = $tpl_field;
        }//printArr($tpl_fields);
        return $tpl_fields;
    }
    
    private function joinComplete( $profile_id )
    {
        // assign default membership
        app_Membership::giveDefaultMembershipOnRegistration( $profile_id );

        $query = SK_MySQL::placeholder('INSERT INTO `'.TBL_REFERRAL."`(`referral_id`) VALUES(?)", $profile_id);

        SK_MySQL::query($query);

        $profile_info = app_Profile::getFieldValues($profile_id, array( 'email', 'username', 'language_id') );

        if ( !app_Profile::email_verified($profile_id ) )
        {
            $model = new Profile_Model($profile_id);
            $model->request_email_verification($profile_info['email']);
        }

        if ( SK_Config::section('site')->Section('additional')->Section('profile')->send_welcome_letter )
        {
            try
            {
                $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                        ->setRecipientProfileId($profile_id)
                        ->setRecipientLangId($profile_info['language_id'])
                        ->setPriority(1)
                        ->setTpl('welcome_letter');

                app_Mail::send($msg);
            }
            catch ( SK_EmailException $e )
            {}
        }

        // Insert default template data
        app_ProfileComponentService::stAddDefaultCmps($profile_id);

        // set profile IP
        app_Profile::updateProfileJoinIP( $profile_id );

        $profile_info = app_Profile::getFieldValues($profile_id, array( 'password', 'username') );

        $auth = SKM_User::authenticate($profile_info['username'], $profile_info['password'], false);

        // Update profile status
        app_Profile::updateProfileStatus( $profile_id, SK_Config::section('site')->Section('automode')->set_profile_status_on_join );

        unset($_SESSION['join_fields']);

        url::redirect('home');
        
        return true;
    }

    private function saveSessionData( $data )
    {
        $_SESSION['join_fields'] = $data;
    }
    
		
}