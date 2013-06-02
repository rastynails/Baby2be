<?php

class form_ForumBanProfile extends SK_Form
{
	
	public function __construct()
	{
		parent::__construct('forum_ban_profile');
	}
	
	
	public function setup()
	{
		$period = new field_forums_select('period');
		$profile_id = new fieldType_hidden('profile_id');
				
		parent::registerField($period);
		parent::registerField($profile_id);
		
		parent::registerAction('form_ForumBanProfile_Process');
	}
	
}

class form_ForumBanProfile_Process extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('ban');
	}
	
	public function setup( SK_Form $form )
	{			
		$this->required_fields = array('period', 'profile_id');
		parent::setup($form);
	}
	
	public function checkData( array $data, SK_FormResponse $response, SK_Form $form ) 
	{
		$lang_errors = SK_Language::section('forms.forum_ban_profile.messages.error');
		$lang_msg = SK_Language::section('forms.forum_ban_profile.messages.success');
		
		if ( !SK_HttpUser::isModerator() ) {
			$response->addError( $lang_errors->text('login_as_moderator') );
			return false;
		}
		elseif ( app_Profile::isProfileModerator($data['profile_id']) ) {
			$response->addError( $lang_errors->text('cannot_ban_moderator') );
			return false;
		}		
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$lang_errors = SK_Language::section('forms.forum_ban_profile.messages.error');
		$lang_msg = SK_Language::section('forms.forum_ban_profile.messages.success');
		
		$response->addMessage( $lang_msg->text('success') );
		return app_Forum::BanProfile($post_data['profile_id'], $post_data['period']);
	}
}
