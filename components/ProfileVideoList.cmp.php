<?php

class component_ProfileVideoList extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('profile_video_list');
	}
	
	public function render( SK_Layout $Layout )
	{
		$profile_id = SK_HttpUser::profile_id();
		
		$profile_video = app_ProfileVideo::getProfileUploadedVideo($profile_id);
			
		$Layout->assign('VideoTagEdit', new component_TagEdit(array('entity_id' => 1, 'feature' => 'video')));
		
		$Layout->assign('video', $profile_video['list']);
		$Layout->assign('video_count',$profile_video['total']);
		
		return parent::render($Layout);
	}

}
