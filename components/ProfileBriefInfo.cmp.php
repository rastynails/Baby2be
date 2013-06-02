<?php

class component_ProfileBriefInfo extends SK_Component
{
	private $profile_id;
	
	private $viewer_id;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( $params )
	{
		if( empty( $params ) || !isset( $params['profile_id'] ) || (int)$params['profile_id'] <= 0 )
		{
			$this->annul();
			return;
		}
		
		parent::__construct( 'profile_brief_info' );
				
		$this->profile_id = (int)$params['profile_id'];
		
		$this->viewer_id = SK_HttpUser::profile_id();
	}
	
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$profile = app_Profile::getFieldValues($this->profile_id, array('sex', 'activity_stamp', 'birthdate'));
        
        $birthdate_fields = app_ProfileField::getProfileListBirthdateFields();
        $birthdate_fields_permissions = array();

        foreach ( $birthdate_fields as $field )
        {
            $birthdate_fields_permissions[$field] = SK_ProfileFields::permissions($field);
        }
		
		$lang_section = SK_Language::section('profile_fields')->section('value');
		
		$profile['profile_id'] = $this->profile_id;
		$profile['sex_label'] = $lang_section->text('sex_'.$profile['sex']);
		$profile['location'] = app_Profile::getFieldValues( $this->profile_id, array( 'country', 'state', 'city', 'zip', 'custom_location' ) );
		$profile['online'] = app_Profile::isOnline($this->profile_id);
        
        if( $birthdate_fields )
        {
            $f_profile_list_section = SK_Language::section('profile_fields')->section('label_profile_list');
            $birthdate_fields_values = app_Profile::getFieldValues( $profile['profile_id'], $birthdate_fields );

            $age_values = array();
            foreach( $birthdate_fields_values as $age_key => $val )
            {
                $profile_field = SK_ProfileFields::get( $age_key );

                if ( !($birthdate_fields_permissions[$age_key] & $profile['sex']) )
                {
                    continue;
                }

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
                            $text = SK_Language::exec( $text, array( 'value' => app_Profile::getAge( $val ) ) );
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
        
        $profile['age'] = $age_values;
		
		if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $this->profile_id)) {
			$profile['activity_info']['item'] = false;
		} else {
			$profile['activity_info'] = app_Profile::ActivityInfo( $profile['activity_stamp'], $profile['online'] );
			$profile['activity_info']['item_label'] = isset($profile['activity_info']['item']) ? SK_Language::section('profile.labels')->text('activity_'.$profile['activity_info']['item']) : false;
		}
				
		$Layout->assign('profile', $profile);

		$Layout->assign('viewer', $this->viewer_id);
				
		
		return parent::render( $Layout );	
	}
	
}