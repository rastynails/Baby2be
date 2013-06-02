<?php

class component_ForumLastTopics extends SK_Component
{
	private $count = null;
    
	public function __construct( array $params = null )
	{	
		parent::__construct('forum_last_topics');
		
		if (!empty($params['type']))
		{
		    $this->tpl_file = trim($params['type']) . '.tpl';
		}
		
	    if (!empty($params['count']))
        {
            $this->count = (int) $params['count'];
        }
	}
			
	public function render( SK_Layout $Layout )
	{						
		$topics = app_Forum::getLastTopicList($this->count);
		$Layout->assign('topics', $topics);
	}

}