<?php

class component_ProfileNote extends SK_Component
{
    private $event_id;

	private $profile_id;
	
	private $opponent_id;
	
	private $cmp_params;
	
	private $note; 
	
	public function __construct( array $params = null )
	{
		parent::__construct('profile_note');
		
	    if( $this->profile_id === null )
        {
            if( !SK_HttpUser::is_authenticated() )
            {
                SK_HttpRequest::showFalsePage();    
            }
            
            $this->profile_id = SK_HttpUser::profile_id();
        }
		
        if (!$params['opponent_id'] || $this->profile_id == $params['opponent_id'])
			$this->annul();
		else
		{
            $this->event_id = $params['event_id'];
        	$this->opponent_id = $params['opponent_id'];
			$this->cmp_params = array( 'profile_id' => $this->opponent_id, 'viewer_id' => $this->profile_id );
			$this->note = app_ProfileNotes::getNoteAboutProfile($this->opponent_id, $this->profile_id);
		}
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('ProfileNote');
		
		$handler->construct($this->event_id, $this->opponent_id, $this->profile_id);
        $handler->message( SK_Language::text("components.event.speed_dating.session_end_message", 
                                              array( "username"=>app_Profile::getFieldValues( $this->opponent_id, 'username' ) )
                                            )
                         );
		
		$this->frontend_handler = $handler;
				
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{	
		//$Layout->assign( 'profile_notes', new component_ProfileNotes( $this->cmp_params ) );
		if (is_array($this->note)) {
			$note = $this->note;
			$Layout->assign('note', $note['note_text']);
		}
		else 
			$Layout->assign('msg', SK_Language::text('components.profile_notes.add_note'));	
		$Layout->assign('username', app_Profile::getFieldValues($this->opponent_id, 'username'));
		return parent::render($Layout);
	}
	
	public function handleForm(SK_Form $form)
	{
		$form->getField('on_profile')->setValue($this->opponent_id);
		$form->getField('by_profile')->setValue($this->profile_id);
		
		$note_text = $this->note['note_text'];
		$form->getField('note')->setValue($note_text);
		
		//$form->frontend_handler->bind("success", "function(data) { this.ownerComponent.reload({profile_id: data.opponent_id, viewer_id: data.profile_id}); }");
	}
	
	public static function ajax_BookmarkHandler($params, SK_ComponentFrontendHandler $handler)
	{
		$profile_id = (int)$params->profile_id;
        $event_id = (int)$params->event_id;
        $note = $params->note;

		$error_section = SK_Language::section('components.profile_references.messages.error');
		$msg_section = SK_Language::section('components.profile_references.messages.success');

               
        $note_text = app_ProfileNotes::getNoteAboutProfile($profile_id, SK_HttpUser::profile_id());
        if (!empty($note_text['note_text']))
        {
            if ($note !== $note_text['note_text'])
            {
                if ( app_ProfileNotes::updateNote(SK_HttpUser::profile_id(), $profile_id, $note ) )
                {
                    $handler->message( SK_Language::section('forms.edit_note.msg')->text('note_updated'));
                }
                else
                {
                    $handler->error( SK_Language::section('forms.edit_note.error_msg')->text('not_updated') );
                    return false;
                }
            }
        }
        else
        {
            if ( app_ProfileNotes::addNote(SK_HttpUser::profile_id(), $profile_id, $note ) )
            {
				$handler->message( SK_Language::section('forms.edit_note.msg')->text('note_added'));
			}
			else
            {
				$handler->error( SK_Language::section('forms.edit_note.error_msg')->text('not_added') );
                return false;
			}
        }

        app_EventService::BookmarkSpeedDatingProfile($event_id, SK_HttpUser::profile_id(), $profile_id);

		$handler->message($msg_section->text('bookmark'));				
		$handler->close();
	}
	
}
