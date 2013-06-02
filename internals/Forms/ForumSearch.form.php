<?php

class form_ForumSearch extends SK_Form
{
	
	public function __construct()
	{
		parent::__construct('forum_search');
	}
	
	
	public function setup()
	{
		$search_keyword = new fieldType_text('forum_search');
		$forum_search = new field_forums_select("search_in_forums");
		
		$search_keyword->minlength = 2;
		$forum_search->setMultiple();
		
		parent::registerField($search_keyword);
		parent::registerField($forum_search);
		
		parent::registerAction('form_ForumSearch_Process');
	}
	
}

class form_ForumSearch_Process extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('search');
	}
	
	public function setup( SK_Form $form )
	{			
		$this->required_fields = array('forum_search');
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		
		$service = new SK_Service('forum_search', SK_HttpUser::profile_id());
		
		if( $service->checkPermissions()!= SK_Service::SERVICE_FULL ){
			return array( 'error'=>$service->permission_message['message'] );
		}
		
		//get search info			
		if ( $post_data['forum_search'] )
			$_SESSION['forum_search'] = $post_data['forum_search'];
		if ( in_array('all', $post_data['search_in_forums']) )
			$forums_ready = 'all';
		else 
		{
			foreach ( $post_data['search_in_forums'] as $forum )
			{
				if ( strpos($forum, 'group')!==false )
				{
					$group_forums = app_Forum::getForumsOfGroup( substr($forum, 6) );
					
					foreach ( $group_forums as $group_forum )
						$forums_ready[] = $group_forum['forum_id'];
				}
				else
					$forums_ready[] = $forum;
			}
		}
		$_SESSION['search_in_forums'] = $forums_ready;

		$service->trackServiceUse();
		
		return true;
	}
}
