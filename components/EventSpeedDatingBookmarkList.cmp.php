<?php

class component_EventSpeedDatingBookmarkList extends component_ProfileList 
{
    private $event_id;
    private $event;


    public function __construct()
	{
		parent::__construct('event_speed_dating_bookmark_list');

        $this->event_id =  @SK_HttpRequest::$GET['event_id'];
        if (!empty ($this->event_id))
            $this->event = app_EventService::stFindEventInfo($this->event_id);

	}
	
	protected function profiles()
	{        
		return app_EventService::BookmarkSpeedDatingList(SK_HttpUser::profile_id(), $this->event_id );
	}
	
	public function render( SK_Layout $Layout )
    {
        parent::render($Layout);

        $event_title = !empty($this->event) ? $this->event['dto']->getTitle() : SK_Language::text('event.speed_dating_events');
        SK_Language::defineGlobal( array('speed_dating_title' => $event_title) );

    }
}