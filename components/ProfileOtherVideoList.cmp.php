<?php

class component_ProfileOtherVideoList extends SK_Component
{
	private $profile_id;
	
	private $except_video;
	
	public function __construct( array $params = null )
	{
		parent::__construct('profile_other_video_list');
		
		$this->profile_id = $params['profile_id'];
		$this->except_video = $params['except_video'];
	}
	
	
	public function render( SK_Layout $Layout )
	{
		$other_list = app_ProfileVideo::getProfileOtherVideo($this->profile_id, $this->except_video);
				
		$Layout->assign('view_more_link', SK_Navigation::href('profile_video', array('profile_id' => $this->profile_id)));
		$Layout->assign('video', $other_list);
		
		$Layout->assign('owner_name', app_Profile::getFieldValues($this->profile_id, 'username'));
		
		return parent::render($Layout);
	}
	
}
