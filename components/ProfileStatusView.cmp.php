<?php

class component_ProfileStatusView extends SK_Component
{
    /**
     * User profile id.
     *
     * @var integer
     */
    private $profile_id;
    
    /**
     * Constructor.
     *
     * @param integer $params['profile_id'] optional
     */
    public function __construct( array $params = null )
    {
        parent::__construct('profile_status_view');
        
        $this->profile_id = (int) $params['profile_id'];
        
        if (!$this->profile_id)
        {
            $this->annul();
        }
    }
    
    /**
     * Rendering component.
     *
     * @param SK_Layout $Layout
     * @return boolean
     */
    public function render( SK_Layout $layout )
    {
        $profile = array();
        $pinfo = app_Profile::getFieldValues($this->profile_id, array('country', 'state', 'city', 'custom_location', 'sex', 'birthdate', 'activity_stamp'));
        
        $profile['id'] = $this->profile_id;
        $profile['status'] = app_Profile::getUserStatus($this->profile_id);
        $profile['status'] = app_TextService::stOutputFormatter($profile['status']);
        $profile['status'] = app_TextService::stCensor($profile['status'], FEATURE_PROFILE);
        $profile['status'] = SK_I18n::getHandleSmile( $profile['status'] );

        $profile['age'] = $pinfo['birthdate'] != '0000-00-00' ? app_Profile::getAge($pinfo['birthdate']) : false;
        $profile['country'] = $pinfo['country'];
        $profile['state'] = $pinfo['state'];
        $profile['city'] = $pinfo['city'];
        $profile['custom_location'] = htmlspecialchars($pinfo['custom_location']);
        $profile['sex'] = $pinfo['sex'];
        $profile['sexLabel'] = SK_Language::text('profile_fields.value.sex_' . $pinfo['sex']);
        $profile['online'] = app_Profile::isOnline($this->profile_id);
        
        if (app_ProfilePreferences::get('my_profile', 'hide_online_activity', $this->profile_id))
        {
            $profile['activity_info']['item'] = false;
        } 
        else
        {
            $profile['activity_info'] = app_Profile::ActivityInfo( $pinfo['activity_stamp'], $profile['online'] );
            $profile['activity_info']['item_label'] = isset($profile['activity_info']['item']) ? SK_Language::section('profile.labels')->text('activity_'.$profile['activity_info']['item']) : false;
        }
        
        $layout->assign('profile', $profile);
        
        return parent::render($layout);
    }
}

