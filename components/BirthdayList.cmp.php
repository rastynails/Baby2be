<?php

class component_BirthdayList extends component_ProfileList 
{
	private $mode;
	
	public function __construct()
	{
		parent::__construct('birthday_list');
	}
	
	protected function profiles()
	{
		$mode = @SK_HttpRequest::$GET['mode'];
		$this->mode = isset($mode) ? $mode : 'today';
		
		return app_ProfileList::BirthdayList($mode);
	}
	
	protected function tabs()
	{
		$tabs = array();
		
		foreach (array( 'today', 'tomorrow') as $key => $item) {
			$tabs[] = array(
						'href'	=> sk_make_url(SK_Navigation::href('profile_birthday_list',$item=='tomorrow' ? array('mode'=>$item) : array() ) ),
						'label'	=> SK_Language::section('label')->text('birthday_list_'.($key+1)),
						'active'=> $this->mode == $item
			);	
		}
		
		return $tabs;
	}
	
	protected function is_permitted()
	{
		return true;
	}
	
}