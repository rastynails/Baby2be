<?php

class form_EditNote extends SK_Form 
{
	
	public function __construct()
	{
		parent::__construct('edit_note');
	}

	public function setup()
	{
		$by_profile = new fieldType_hidden('by_profile');
		parent::registerField($by_profile);
			
		$on_profile = new fieldType_hidden('on_profile');
		parent::registerField($on_profile);
		
		$note = new fieldType_textarea('note');
		parent::registerField($note);
				
		parent::registerAction('formEditNote_Update');
		parent::registerAction('formEditNote_Add');
	}
}

class formEditNote_Update extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('update');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('by_profile', 'on_profile', 'note');
				
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$by_profile = $post_data['by_profile'];
		$on_profile = $post_data['on_profile'];
		$note = SK_Language::htmlspecialchars(trim($post_data['note']));

		// check service permission
		$add_notes_service = new SK_Service('add_profile_notes', $by_profile);
			
		if ( $add_notes_service->checkPermissions() != SK_Service::SERVICE_FULL ) {
			$response->addError($add_notes_service->permission_message['alert']);
		}
		else {
			
			if ( app_ProfileNotes::updateNote($by_profile, $on_profile, $note ) ) {
				$add_notes_service->trackServiceUse();
				$response->addMessage( SK_Language::section('forms.edit_note.msg')->text('note_updated'));				
			}
			else {
				$response->addError( SK_Language::section('forms.edit_note.error_msg')->text('not_updated') );
			}
		}
		
		return array('profile_id' => $on_profile, 'viewer_id' => $by_profile); 
	}
}

class formEditNote_Add extends SK_FormAction 
{
	public function __construct()
	{
		parent::__construct('add');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('by_profile', 'on_profile', 'note');
				
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $form)
	{
		$by_profile = $post_data['by_profile'];
		$on_profile = $post_data['on_profile'];
		$note = SK_Language::htmlspecialchars(trim($post_data['note']));

		// check service permission
		$add_notes_service = new SK_Service('add_profile_notes', $by_profile);
			
		if ( $add_notes_service->checkPermissions() != SK_Service::SERVICE_FULL ) {
			$response->addError($add_notes_service->permission_message['alert']);
		}
		else {
			
			if ( app_ProfileNotes::addNote($by_profile, $on_profile, $note ) ) {
				$add_notes_service->trackServiceUse();
				$response->addMessage( SK_Language::section('forms.edit_note.msg')->text('note_added'));				
			}
			else {
				$response->addError( SK_Language::section('forms.edit_note.error_msg')->text('not_added') );
			}
		}
		$response->reload();
		return array('profile_id' => $on_profile, 'viewer_id' => $by_profile);
	}
}
