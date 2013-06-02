<?php

class form_GroupInviteMembers extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('group_invite_members');
	}
	
	public function setup()
	{
		$group_id = new fieldType_hidden('group_id');
		parent::registerField($group_id);
		
		$members = new fieldType_textarea('members');
		parent::registerField($members);
				
		parent::registerAction('form_GroupInviteMembers_Invite');
	}
}

class form_GroupInviteMembers_Invite extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('invite');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('group_id', 'members');

		parent::setup($form);
	}

	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$group_id = intval(trim($post_data['group_id']));
		
		if ( $group_id )
		{
			$members = trim($post_data['members']);
			
			$invited = app_Groups::inviteMembers( $group_id, $members, SK_HttpUser::profile_id() );
			
			if ($invited) 
			{
				$response->addMessage(SK_Language::text('forms.group_invite_members.invited', array('number' => $invited)));
				$response->redirect(SK_Navigation::href('group_edit', array('group_id' => $group_id, 'action' => 'invite')));
			}
			else
				$response->addError(SK_Language::text('forms.group_invite_members.nobody_invited'));
		}
	}
}
