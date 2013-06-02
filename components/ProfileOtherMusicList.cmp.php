<?php

class component_ProfileOtherMusicList extends SK_Component
{
	private $profile_id;
	
	private $except_music;
	
	public function __construct( array $params = null )
	{
		parent::__construct('profile_other_music_list');
		
		$this->profile_id = $params['profile_id'];
		$this->except_music = $params['except_music'];
	}
	
	
	public function render( SK_Layout $Layout )
	{
		$other_list = app_ProfileMusic::getProfileOtherMusic($this->profile_id, $this->except_music);
                
		$Layout->assign('view_more_link', SK_Navigation::href('profile_music', array('profile_id' => $this->profile_id)));

        $Layout->assign('music', $other_list);
		
		$Layout->assign('owner_name', app_Profile::username($this->profile_id));
		
		return parent::render($Layout);
	}
	
}
