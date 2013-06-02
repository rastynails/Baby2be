<?php

class form_GroupAddModerators extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('group_add_moderators');
	}
	
	public function setup()
	{
		$group_id = new fieldType_hidden('group_id');
		parent::registerField($group_id);
		
		$mod = new fieldType_text('moderators');
		parent::registerField($mod);
				
		parent::registerAction('form_GroupAddModerators_Add');
	}
}

class form_GroupAddModerators_Add extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('add');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('group_id', 'moderators');
		
		parent::setup($form);
	}

	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$group_id = intval(trim($post_data['group_id']));
		
		if ( $group_id )
		{
			$moderators = trim($post_data['moderators']);
			
			$added = app_Groups::addModerators( $group_id, $moderators );
			
			if ($added) 
			{
				$response->addMessage(SK_Language::text('forms.group_add_moderators.moderators_added', array('number' => $added)));
				$response->redirect(SK_Navigation::href('group_edit', array('group_id' => $group_id)));
			}
			else
				$response->addError(SK_Language::text('forms.group_add_moderators.not_found'));
		}
	}
}
