<?php

class form_ForumEditTopic extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('forum_edit_topic');
	}
	
	public function setup()
	{
		$title = new fieldType_text('title');
		$first_post = new fieldType_textarea('first_post');
		$topic_id = new fieldType_hidden('topic_id');
			
		parent::registerField($title);
		parent::registerField($first_post);
		parent::registerField($topic_id);

		$title->maxlength = 100;
		$first_post->maxlength = 65535;	
				
		parent::registerAction('form_ForumEditTopic_Process');
	}
	
}

class form_ForumEditTopic_Process extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('save');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('title', 'first_post');
			
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$lang_msg = SK_Language::section('forms.forum_edit_topic.messages.success');
		$service = new SK_Service('forum_write', SK_HttpUser::profile_id());
		
		if( $service->checkPermissions()!= SK_Service::SERVICE_FULL ){
			return array( 'error'=>$service->permission_message['message'] );
		}
		
		//insert data and name into quote 
		$replace_str = "[quote name='" . SK_HttpUser::username() . "' date='" . app_Forum::getQuoteDate() . "']";
		$post_data['first_post'] = str_replace( '[quote]', $replace_str, $post_data['first_post'] );		
		
		$result = app_Forum::UpdateTopic( SK_HttpUser::profile_id(), $post_data['topic_id'], $post_data['title'], $post_data['first_post'] );

		if ($result) 
		{
			$title = app_TextService::stCensor( $post_data['title'], 'forum', true );
			
			$text = nl2br( SK_Language::htmlspecialchars( $post_data['first_post'] ) );
			$text = app_TextService::stHandleSmiles( $text );
		
			$response->addMessage($lang_msg->text('success'));
			
			return array( 'title'=>$title, 'text'=>app_Forum::forumTagsToHtmlChars($text) );
		}		
	}
	
}
