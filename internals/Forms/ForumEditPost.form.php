<?php

class form_ForumEditPost extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('forum_edit_post');
	}
	
	public function setup()
	{
		$post_text = new fieldType_textarea('edit_post_text');
		$post_id = new fieldType_hidden('post_id');
			
		parent::registerField($post_text);
		parent::registerField($post_id);
				
		$post_text->maxlength = 65535;
		
		parent::registerAction('form_ForumEditPost_Process');
	}	
	
}

class form_ForumEditPost_Process extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('edit');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('edit_post_text');
			
		parent::setup($form);
	}
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
		$lang_msg = SK_Language::section('forms.forum_edit_post.messages.success');
		$lang_errors = SK_Language::section('forms.forum_edit_post.messages.error');
		
		$service = new SK_Service('forum_write');
		
		$topic_id = app_Forum::getTopicIdByPost($post_data['post_id']);
		if ( app_Forum::isTopicClosed($topic_id) ) {
			return array( 'error'=>$lang_errors->text('cannot_edit_post_topic_is_closed'),
						  'post_id'=>$post_data['post_id'] );
		}
		if ( $service->checkPermissions() != SK_Service::SERVICE_FULL ) 
		{
			return array( 'error'=>$service->permission_message['message'],
						  'post_id'=>$post_data['post_id'] );
		}
		else 
		{
            $service->trackServiceUse();
		}
			
		//insert data and name into quote 
		$replace_str = "[quote name='" . SK_HttpUser::username() . "' date='" . app_Forum::getQuoteDate() . "']";
		$post_data['edit_post_text'] = str_replace( '[quote]', $replace_str, $post_data['edit_post_text'] );		
		
		$result = app_Forum::UpdatePost( SK_HttpUser::profile_id(), $post_data['post_id'], $post_data['edit_post_text'] );

		if ($result) 
		{					
			$text = nl2br( SK_Language::htmlspecialchars( $post_data['edit_post_text'] ) );
			$text = app_Forum::forumTagsToHtmlChars($text);
			$response->addMessage($lang_msg->text('success'));
			
			return array( 'id'=>$post_data['post_id'], 'text'=> app_TextService::stHandleSmiles( $text ) );
		}		
	}
	
}
