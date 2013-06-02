<?php

class form_ForumAddPost extends SK_Form
{	
	
	public function __construct()
	{
		parent::__construct('forum_add_post');
	}
	
	public function setup()
	{
		$post_text = new fieldType_textarea('post_text');
		$topic_id = new fieldType_hidden('topic_id');
		$attachment = new field_attachment('attachment');
		
		parent::registerField($attachment);
		parent::registerField($post_text);
		parent::registerField($topic_id);		
				
		$post_text->maxlength = 65535;
		parent::registerAction('form_ForumAddPost_Process');
	}	
	
}

class form_ForumAddPost_Process extends SK_FormAction
{
	
	public function __construct()
	{
		parent::__construct('post');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('post_text');
			
		parent::setup($form);
	}
	
	public function checkData( array $data, SK_FormResponse $response, SK_Form $form ) 
	{
		$lang_errors = SK_Language::section( 'forms.forum_add_post.messages.error'  );
		$service = new SK_Service('forum_write', SK_HttpUser::profile_id());
		
		if( !SK_HttpUser::profile_id() )
			$response->addError( $lang_errors->text('guest_cannot_add_post') );		
		if( app_Forum::isTopicClosed($data['topic_id']) )
			$response->addError( $lang_errors->text('cannot_add_post_topic_is_closed') );
		if( $service->checkPermissions()!= SK_Service::SERVICE_FULL )
		{
			$response->addError( $service->permission_message['message'] );
		}
		else
		{
            $service->trackServiceUse();
		}
	}

	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{		
		//insert data and name into quote 
		$replace_str = "[quote name='" . SK_HttpUser::username() . "' date='" . app_Forum::getQuoteDate() . "']";
		$post_data['post_text'] = str_replace( '[quote]', $replace_str, $post_data['post_text'] );
		
		$post_id = app_Forum::AddPost( SK_HttpUser::profile_id(), $post_data['topic_id'], $post_data['post_text'] );
		$post_url = app_Forum::getPostURL($post_id);
		
	    if (is_array($post_data['attachment']))
        {
            $attachments = array_filter($post_data['attachment']);
            
            foreach ($attachments as $att)
            {
                $file = new SK_TemporaryFile($att);
                app_Attachment::add('forum_post', $post_id, $file);
            }
        }

		app_Forum::sendProfileNotifies( SK_HttpUser::profile_id(), $post_data['topic_id'], $post_url);
		
		$response->redirect( $post_url );
	}
}
