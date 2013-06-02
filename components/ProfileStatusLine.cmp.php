<?php

class component_ProfileStatusLine extends SK_Component
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
        parent::__construct('profile_status_line');
        
        $this->profile_id = empty($params['profile_id']) ? SK_HttpUser::profile_id() : (int) $params['profile_id'];
        
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
        $profile_status = app_Profile::getFieldValues($this->profile_id, 'status');
        $status_label = SK_Language::text('components.profile_status.status.' . $profile_status);
        
        $layout->assign('status', $status_label);
        
        // getting membership info
        $membership = app_Membership::ProfileCurrentMembershipInfo($this->profile_id);
        if ( isset($membership['expiration_stamp']) ) {
            $membership['expiration_time'] = SK_I18n::period(time(), $membership['expiration_stamp']);
        }
        $layout->assign('membership', $membership);
        
        return parent::render($layout);
    }
}

