<?php

class component_ForumProfilePosts extends SK_Component
{
	private $profile_id;
	
	public function __construct( array $params = null )
	{	
		parent::__construct('forum_profile_posts');
		$this->profile_id = $params['profile_id'];
	}
			
	public function render( SK_Layout $Layout )
	{				
		$posts = app_Forum::getProfilePosts( $this->profile_id );
		
		$Layout->assign('posts', $posts);
		$Layout->assign('username', app_Profile::username($this->profile_id));
	}
}