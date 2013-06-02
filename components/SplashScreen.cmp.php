<?php

class component_SplashScreen extends SK_Component
{
	public function __construct( array $params = null )
	{
                if(!empty($_POST['enter']))
                {
                    SK_HttpUser::set_cookie('splash_screen_viewed', true);
                    $back_url = !empty($_COOKIE['splash_screen_back_uri']) ? SITE_URL.substr($_COOKIE['splash_screen_back_uri'], 1) : SITE_URL;
                    
                    SK_HttpRequest::redirect(SITE_URL.substr($_COOKIE['splash_screen_back_uri'], 1));
                }

		parent::__construct('splash_screen');
	}

	public function render( SK_Layout $Layout )
	{
                $back_url = !empty($_COOKIE['splash_screen_back_uri']) ? SITE_URL.substr($_COOKIE['splash_screen_back_uri'], 1) : SITE_URL;
                
                $splash_screen_config = SK_Config::section("site")->Section("splash_screen");
                $leave_url = !empty($splash_screen_config->leave_url)?$splash_screen_config->leave_url : 'http://google.com';

		$Layout->assign('back_url', $back_url);
                $Layout->assign('leave_url', $leave_url);
		return parent::render($Layout);
	}
        
}