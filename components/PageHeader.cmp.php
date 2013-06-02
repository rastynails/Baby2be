<?php

class component_PageHeader extends SK_Component
{

	public function __construct( array $params = null )
	{
		parent::__construct('page_header');		

	}

	public function prepare(SK_Layout $Layout, SK_Frontend $Frontend) {

	    $is_authenticated = SK_HttpUser::is_authenticated();
	    
	    $Layout->assign('is_authenticated', $is_authenticated);
	    
	    $topSelectBox = ( SK_Config::section('facebook_connect')->enabled && !SK_HttpUser::is_authenticated() ) 
	       || SK_Config::section("site")->Section("additional")->Section("profile")->allow_lang_switch;
	       
	    $Layout->assign('topSelectBox', $topSelectBox);
	    
		if( (boolean) SK_Config::section('123_wm')->enable_123wm && $is_authenticated )
		{
			$data = app_Profile::getFieldValues( SK_HttpUser::profile_id(), array('username', 'password') );

			$Layout->assign('u', $data['username'] );
			$Layout->assign('p', $data['password'] );

			$jspath = URL_123WM.'client';

			$Frontend->include_js_file("{$jspath}/js/123webmessenger.js");
			$Frontend->include_js_file("{$jspath}/js/config.js");
			$Frontend->include_js_file("{$jspath}/js/cookies.js");
			$Frontend->include_js_file("{$jspath}/js/fly.js");
			$Frontend->include_js_file("{$jspath}/js/dc.js");
			
			$Frontend->onload_js('window.is_123wm = true;');
			$Frontend->onload_js('window.initiate123wm = function(username){
				$.post("'.SITE_URL.'login_wm.php", {\'with\': username} , function(data){if(data.allowed==0 ){alert(data.alert);return;} FC_invite_1to1_chat(data.username);}, "json");
			};');			

			$Layout->assign('is_123wm', true );
		}
	}
}