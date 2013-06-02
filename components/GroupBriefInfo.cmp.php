<?php

class component_GroupBriefInfo extends SK_Component 
{
	private $group_id;
	
	private $group;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params = null )
	{
		$doc_key = SK_HttpRequest::getDocument()->document_key;

		$show_on_pages = array('group_members', 'forum', 'forum_new_topic', 'topic');
		
		if ( !in_array($doc_key, $show_on_pages) )
		{
			$this->annul();
		}
		else 
		{
			if ( intval(SK_HttpRequest::$GET['group_id']))
			{
				$this->group_id = SK_HttpRequest::$GET['group_id'];
				$this->group = app_Groups::getGroupById($this->group_id);
			}
			else if (intval(SK_HttpRequest::$GET['forum_id']) && intval($group_id = app_Groups::getGroupByForumID(SK_HttpRequest::$GET['forum_id']))
					|| intval(SK_HttpRequest::$GET['topic_id']) && intval($group_id = app_Groups::getGroupByForumTopicID(SK_HttpRequest::$GET['topic_id'])) )
			{
				$this->group_id = $group_id;
				$this->group = app_Groups::getGroupById($this->group_id);
			}
			else 
			{
				$this->annul();	
			}
		}

		parent::__construct('group_brief_info');
	}
	
	public function render( SK_Layout $Layout )
	{		
        $Layout->assign('group', $this->group);
		$Layout->assign('group_image', app_Groups::getGroupImageURL($this->group_id, $this->group['photo'], true));
		$Layout->assign('members_count', app_Groups::getGroupMembersCount($this->group_id));
		
		return parent::render( $Layout );
	}
}