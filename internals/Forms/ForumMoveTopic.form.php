<?php

class form_ForumMoveTopic extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('forum_move_topic');
	}
	
	public function setup()
	{
		$topic_id = new fieldType_hidden('topic_id');		
		$forums_list = new field_forums_select('to_forum_id');
		
		parent::registerField($topic_id);
		parent::registerField($forums_list);

		parent::registerAction('form_ForumMoveTopic_Process');
	}
	
}

class form_ForumMoveTopic_Process extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('move');
	}
	
	public function setup( SK_Form $form )
	{			
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$lang_msg = SK_Language::section('forms.forum_move_topic.messages.success');
		$profile_id = SK_HttpUser::profile_id();
		
		$result = app_Forum::MoveTopic( $post_data['topic_id'], $post_data['to_forum_id'], $profile_id );

		if ($result)
		{
			app_Forum::ReplaceTopic( $post_data['topic_id'], $profile_id );
			$response->addMessage($lang_msg->text('success'));
			return true;
		}
		return false;
	}
}