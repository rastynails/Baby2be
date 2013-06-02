<?php

class component_ProfileNotes extends SK_Component
{
	private $profile_id;
	
	private $viewer_id;
	
	private $note; 
	
	public function __construct( array $params = null )
	{
		parent::__construct('profile_notes');

		$params['viewer_id'] = empty($params['viewer_id']) ? SK_HttpUser::profile_id() : $params['viewer_id'];
		
		if (!app_Features::isAvailable(34))
			$this->annul();
		else {			
			if (!$params['profile_id'] || !$params['viewer_id'] || $params['profile_id'] == $params['viewer_id'])
				$this->annul();
			else {	
				$this->profile_id = $params['profile_id'];
				$this->viewer_id = $params['viewer_id'];
				
				$this->note = app_ProfileNotes::getNoteAboutProfile($this->profile_id, $this->viewer_id);
			}
		}
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('ProfileNotes');
		$handler->construct($this->profile_id, $this->viewer_id);			
		$this->frontend_handler = $handler;
				
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{
		$service = new SK_Service('add_profile_notes', $this->viewer_id);
		
		if ($service->checkPermissions() != SK_Service::SERVICE_FULL) {
			$Layout->assign('permission_msg', $service->permission_message['message']);
		} else {
		
			if (is_array($this->note)) {
				$note = $this->note;
				$Layout->assign('note', $note);
			}
			else 
				$Layout->assign('msg', SK_Language::text('components.profile_notes.add_note'));	
		}
		$Layout->assign('username', app_Profile::username($this->profile_id));
		
		return parent::render($Layout);
	}
	
	public function handleForm(SK_Form $form)
	{
		$form->getField('on_profile')->setValue($this->profile_id);
		$form->getField('by_profile')->setValue($this->viewer_id);
		
		$note_text = $this->note['note_text'];
		$form->getField('note')->setValue($note_text);
		
		$form->frontend_handler->bind("success", "function(data) { this.ownerComponent.reload({profile_id: data.profile_id, viewer_id: data.viewer_id});  }");
	}
	
	public static function ajax_DeleteNote( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$profile_id = $params->profile_id;
		$viewer_id = $params->viewer_id;
		
		if ($profile_id && $viewer_id) {
			if (app_ProfileNotes::deleteNote($viewer_id, $profile_id))
				$handler->message(SK_Language::text('components.profile_notes.deleted'));
			else 
				$handler->error(SK_Language::text('components.profile_notes.not_deleted'));
				
			self::reload( array( 'profile_id' => $profile_id, 'viewer_id' => $viewer_id ), $handler, $response );
		}
		else {
			$handler->error("Error. Wrong params passed");
			return;
		}
	} 
	
}
