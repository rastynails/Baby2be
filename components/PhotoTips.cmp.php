<?php

class component_PhotoTips extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('photo_tips');
	}
	
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('PhotoTips');
		$handler->construct();
		$this->frontend_handler = $handler;
		
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$Layout->assign("opened", app_ProfilePreferences::get("hidden", "show_upload_photo_tips"));
		$Layout->assign("file_size",SK_Config::section("photo")->Section("general")->max_filesize);
		return parent::render($Layout);
	}
	
	public static function ajax_saveTipState($params = null, SK_ComponentFrontendHandler $handler) 
	{
		try {
			$val = (bool)app_ProfilePreferences::get("hidden", "show_upload_photo_tips");
			app_ProfilePreferences::set("hidden", "show_upload_photo_tips", $val ? 0 : 1);
			if ($val) {
				return array("msg"=>SK_Language::text("%components.photo_tips.show_btn"), 'show'=> false);
			} else {
				return array("msg"=>SK_Language::text("%components.photo_tips.hide_btn"), 'show'=> true);
			}
			
		} catch (SK_ProfilePreferencesException $e) {return false;}
	}
	
}
