<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller extends Controller_Core {

	function __construct()
	{
		parent::__construct();
				
	    if ( Kohana::config('smarty.integration' ) == TRUE)
        {
            $this->SKM_Smarty = new SKM_Smarty;
        }
        
        if ( router::$method != 'suspended' && !app_Site::active() )
        {
            url::redirect('suspended');
        }

        //$sess = Session::instance();
        
        $allowed_controllers = array('logout', 'sign_in', 'not_found');

		if ( SKM_User::is_authenticated() && !in_array(router::$controller, $allowed_controllers) )
		{
		    $profile_id = SKM_User::profile_id();
		    
		    $allowed = array();
            if ( !in_array(router::$method, $allowed) ) 
            {
                if ( router::$method != 'emailverify') {
                    if ( !app_Profile::email_verified($profile_id) ) {
                        $redirect = 'emailverify';
                    }
                }
            }
            
		    $allowed = array('emailverify', 'profileSuspended');
            if ( !in_array(router::$method, $allowed) ) 
            {
                if ( router::$method != 'review') {
                    if ( !app_Profile::reviewed($profile_id) && !$config = SK_Config::section('site')->Section('additional')->Section('profile')->not_reviewed_profile_access ) {
                        $redirect = 'review';
                    }
                }
            } 
            
            $allowed = array('emailverify', 'review');
            if ( !in_array(router::$method, $allowed) ) 
            {
                if ( router::$method != 'profileSuspended') {
                    if ( app_Profile::suspended($profile_id) ) {
                        $redirect = 'profile-suspended';
                    }
                }
            }
            
            if ( strlen($redirect) )
            {
                url::redirect($redirect);
            }
		}
	}

	public function _kohana_load_view($template, $vars)
	{
		if ( $template == '' )
			return;

		if ( substr(strrchr($template, '.'), 1) === Kohana::config('smarty.templates_ext') )
		{
			// Assign variables to the template
			if ( is_array($vars) AND count($vars) > 0 )
			{
				foreach ( $vars AS $key => $val )
				{
					$this->SKM_Smarty->assign($key, $val);
				}
			}

			// Send Kohana::instance to all templates
			$this->SKM_Smarty->assign('this', $this);

			// Fetch the output
			$output = $this->SKM_Smarty->fetch($template);
		}
		else
		{
			$output = parent::_kohana_load_view($template, $vars);
		}

		return $output;
	}
}
