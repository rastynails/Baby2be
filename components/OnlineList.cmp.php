<?php
class component_OnlineList extends component_ProfileList 
{
	public function __construct()
	{
		parent::__construct('online_list');
	}
	
	protected function profiles()
	{
		return app_ProfileList::OnlineList(@SK_HttpRequest::$GET['sex']);
	}
	
	protected function tabs()
	{
		if(!SK_Config::section('site')->Section('additional')->Section('profile_list')->online_gender_separate)
			return array();
		$tabs = array();
		
		$document = SK_HttpRequest::getDocument();
		
		$tabs[] = array(
						'href'	=>$document->url,
						'label'	=>SK_Language::section('profile.list')->text('online_label'),
						'active'=>!isset(SK_HttpRequest::$GET['sex'])
						);
		foreach (SK_ProfileFields::get('sex')->values as $sex)
			$tabs[] = array(
						'href'	=>sk_make_url($document->url,array('sex'=>$sex)),
						'label'	=>SK_Language::section('profile.list')->text('online_sex_label',array(
																										'sex' => SK_Language::section('profile_fields.value')->text('sex_'.$sex)
																										)),
						'active'=>(@SK_HttpRequest::$GET['sex']==$sex)
						);
				
		return $tabs;
	}
	
	public function setup()
	{
		//$this->cache_lifetime = 60;
	}
	
	protected function is_permitted()
	{
		
		return true;
	}
	
}