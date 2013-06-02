<?php

class form_UnregisterProfile extends SK_Form
{
	
	public function __construct()
	{
		parent::__construct('unregister_profile');
	}
	
	
	public function setup()
	{
		$field = new fieldType_textarea('reason');
		parent::registerField($field);
		
		parent::registerAction('form_UnregisterProfile_Process');
	}
	
}

class form_UnregisterProfile_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('unregister');
	}

	public function setup( SK_Form $form )
	{
		$this->required_fields = array('reason');

		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		// custom check for group owners
		$groups = app_Groups::getGroupsProfileCreated(SK_HttpUser::profile_id());
		if ( $groups['total'] > 0 )
		{
			foreach ($groups['list'] as $gr)
			{
				app_Groups::removeGroup($gr['group_id']);
			}
		}
		
		$msg = empty($post_data['reason']) ? '' : $post_data['reason']; 
		
		if (app_Profile::unregisterProfile(SK_HttpUser::profile_id(), $msg)) {
			$response->redirect(SK_Navigation::href('index'));
		}
		else {
			$response->exec('location.reload()');
		}
	}
}
