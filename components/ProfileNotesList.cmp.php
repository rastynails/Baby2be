<?php

class component_ProfileNotesList extends component_ProfileList 
{
	public function __construct()
	{
		parent::__construct('note_list');
		
	}
	
	protected function setup()
	{
		$this->view_mode = 'details';
		$this->change_view_mode = false;
	}
	
	protected function profiles()
	{
		$items = app_ProfileNotes::getNoteProfiles(SK_HttpUser::profile_id() );
		return $items;
	}
		
	protected function prepare_list()
	{
		parent::prepare_list();

		if (count($this->list)) {
			foreach ( array_keys($this->list) as $key )
			{
				$profile = &$this->list[$key];
				
				$profile['general_description'] = SK_Language::text('components.profile_notes.my_note') . " " . $profile['note_text'];
			}
		} 
	}
	
}