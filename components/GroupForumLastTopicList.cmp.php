<?php

class component_GroupForumLastTopicList extends SK_Component 
{
	private $group_id;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
	    if ( !app_Features::isAvailable(22) )
	    {
            $this->annul();
	    }
	    
		if (!intval($params['group_id']))
			$this->annul();
		else
			$this->group_id = $params['group_id'];
						
		parent::__construct('group_forum_last_topic_list');
	}
	
	public function render( SK_Layout $Layout )
	{
		$topics = app_Groups::getGroupForumLastTopicList($this->group_id); 	
		
		if (count($topics))
			$Layout->assign('topics', $topics );
		else 
			$Layout->assign('no_topics', true );
		
		$Layout->assign('group_id', $this->group_id);
		$Layout->assign('forum_id', app_Groups::getGroupForumId($this->group_id));
		$Layout->assign('is_member', app_Groups::isGroupMember(SK_HttpUser::profile_id(), $this->group_id));
		
		$configs = new SK_Config_Section('forum');
		$Layout->assign('text_truncate', $configs->topic_text_truncate);
		$Layout->assign('title_truncate', $configs->topic_title_truncate);
		
		return parent::render( $Layout );
	}
}