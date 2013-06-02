<?php

class Profile_View_Controller extends Controller {
	
	private $profile_id;
	
	function __construct()
	{
		parent::__construct();
		
		if ( !SKM_User::is_authenticated() ) 
		{
			Session::instance()->set("requested_url", "/".url::current());
			SKM_Template::addMessage(SKM_Language::text('%nav_doc_item.headers.sign_in'), 'notice'); 
			url::redirect('sign_in');
		}
	}
	
	public function view( $profile = '__self__' ) 
	{
		$view = new View('default');

		if ( $profile == '__self__' || app_Profile::getProfileIdByUsername($profile) == SKM_User::profile_id() )
		{
			$is_owner = true;
			$this->profile_id = SKM_User::profile_id();
		}	
		else 
		{
			$is_owner = false;
			$this->profile_id = app_Profile::getProfileIdByUsername($profile);
			
			if ( !$this->profile_id )
				url::redirect('not_found');
		}

		$pr_info = app_Profile::getFieldValues($this->profile_id, array('sex', 'activity_stamp', 'birthdate', 'username', 'headline'));
		
		$lang_section = SKM_Language::section('profile_fields')->section('value');
		
		$pr_info['profile_id'] = $this->profile_id;
		$pr_info['sex_label'] = $lang_section->text('sex_'.$pr_info['sex']);
		$pr_info['location'] = app_Profile::getFieldValues( $this->profile_id, array( 'country', 'state', 'city', 'zip' ) );
		$pr_info['online'] = app_Profile::isOnline($this->profile_id);

        $birthdate_fields = app_ProfileField::getProfileListBirthdateFields();
        if ( $birthdate_fields )
        {
            $f_profile_list_section = SKM_Language::section('profile_fields')->section('label_profile_list');
            $birthdate_fields_values = app_Profile::getFieldValues( $this->profile_id, $birthdate_fields );

            $age_values = array();
            foreach ( $birthdate_fields_values as $age_key => $val )
            {
                $profile_field = SK_ProfileFields::get( $age_key );

                if( $val && $profile_field )
                {
                    $profile_field_id = $profile_field->profile_field_id;
                    try
                    {
                        $text = $f_profile_list_section->cdata($profile_field_id);

                        if ( !$text ) {
                            continue;
                        }

                        if ( strpos($text, '{') !== false ) {
                            $text = SKM_Language::exec( $text, array( 'value' => app_Profile::getAge( $val ) ) );
                        }

                        $age_values[$age_key] = $text;
                    }
                    catch( Exception $ex )
                    {
                        // ignore;
                    }
                }
            }
        }

        $pr_info['age'] = $age_values;
		
		if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $this->profile_id)) {
			$pr_info['activity_info']['item'] = false;
		} else {
			$pr_info['activity_info'] = app_Profile::ActivityInfo( $pr_info['activity_stamp'], $pr_info['online'] );
			$pr_info['activity_info']['item_label'] = isset($pr_info['activity_info']['item']) ? SKM_Language::section('profile.labels')->text('activity_'.$pr_info['activity_info']['item']) : false;
		}

		$view->header = SKM_Template::header(); 
		
		// menu
		$m = new SKM_Menu('main', 'profile', SKM_Language::text('%nav_doc_item.profile') );
		$view->menu = $m->get_view();

		SKM_Template::addTplVar('profile', $pr_info);
		SKM_Template::addTplVar('profile_id', $this->profile_id);
		SKM_Template::addTplVar('is_owner', $is_owner);

		SKM_Template::addTplVar('allow_bookmarking', false);
		
		if ( !$is_owner ) {
			SKM_Template::addTplVar('url_sendmsg', url::base() . 'mailbox/new/' . $profile);
			
			if ( !app_Bookmark::isProfileBookmarked( SKM_User::profile_id(), $this->profile_id) ) {
				$bm_ref = 'bookmark';
			}
			else {
				$bm_ref = 'unbookmark';
			}
			SKM_Template::addTplVar('bm_ref', $bm_ref);
			
            $doc = SK_Navigation::getDocument('bookmark_list');
            SKM_Template::addTplVar('allow_bookmarking', $doc->status && $doc->access_member && app_Features::isAvailable(18));
            
            app_ProfileViewHistory::track(SKM_User::profile_id(), $this->profile_id);
		}
		
		$pr_model = new Profile_Model($this->profile_id);
		$fields = $pr_model->get_field_values(1);
			
		SKM_Template::addTplVar('fields', $fields);
		
		$page = 1; $limit = 3; $viewer = SKM_User::profile_id();
		$photos = $pr_model->get_photos( $this->profile_id, $page, $limit);

		SKM_Template::addTplVar('photos', $photos['list']); 
		SKM_Template::addTplVar('photo_url', url::base() . 'profile/'.$pr_info['username'].'/photo/');
		SKM_Template::addTplVar('base_url', url::base());
		SKM_Template::addTplVar('allphotos_url', url::base() . 'profile/'.$pr_info['username'].'/photos/');
		SKM_Template::addTplVar('photo_sect', SKM_Language::text('%mobile.user_photos', array('username' => $pr_info['username']))); 
		SKM_Template::addTplVar('fields_sect', SKM_Language::text('%components.profile_details.title', array('username' => $pr_info['username'])));
		
		// proifle view
		$profile = new View('profile', SKM_Template::getTplVars());
		$view->content = $profile;
		$view->footer = SKM_Template::footer();
		
		$view->render(TRUE);
	}
	
	public function bookmark( $profile )
	{
		if (strlen(trim($profile)))
		{
			$profile_id = app_Profile::getProfileIdByUsername($profile);
			
			$service = new SK_Service('bookmark_members', SKM_User::profile_id());
			if ($service->checkPermissions() != SK_Service::SERVICE_FULL) {
				SKM_Template::addMessage($service->permission_message['alert'], 'notice' );
				url::redirect('profile/' . $profile);
			}
			else if ( !app_Bookmark::BookmarkProfile(SKM_User::profile_id(), $profile_id) ) {
				SKM_Template::addMessage(SKM_Language::text('%components.profile_references.messages.error.bookmark'), 'notice' );
			}
			else {
				$service->trackServiceUse();
				SKM_Template::addMessage(SKM_Language::text('%components.profile_references.messages.success.bookmark') );
			}
		}
		url::redirect('profile/' . $profile);
	}
	
	public function unbookmark( $profile )
	{
		if (strlen(trim($profile)))
		{
			$profile_id = app_Profile::getProfileIdByUsername($profile);
			
			if ( !app_Bookmark::UnbookmarkProfile(SKM_User::profile_id(), $profile_id) ) {
				SKM_Template::addMessage(SKM_Language::text('%components.profile_references.messages.error.unbookmark'), 'notice' );
			}
			else {
				SKM_Template::addMessage(SKM_Language::text('%components.profile_references.messages.success.unbookmark') );
			}
		}
		url::redirect('profile/' . $profile);
	}
}
