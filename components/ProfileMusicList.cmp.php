<?php

class component_ProfileMusicList extends SK_Component
{
	public function __construct( array $params = null )
	{
		parent::__construct('profile_music_list');
		
		if ( !app_Features::isAvailable(40)) {
		    $this->annul();
		}
	}
	
	public function render( SK_Layout $Layout )
	{
		$profile_id = SK_HttpUser::profile_id();
		
		$profile_music = app_ProfileMusic::getProfileUploadedMusic($profile_id);
			
		$Layout->assign('music', $profile_music['list']);
		$Layout->assign('music_count',$profile_music['total']);
		
		return parent::render($Layout);
	}

}
