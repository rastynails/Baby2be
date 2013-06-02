<?php

class form_ForumNewTopic extends SK_Form
{	
	public function __construct()
	{
		parent::__construct('forum_new_topic');	
	}
	
	public function setup()
	{
		$title = new fieldType_text('title');
		$first_post = new fieldType_textarea('first_post');
		$forum_id = new field_forums_select('forum_id');
		$notify_me = new fieldType_checkbox('notify_me');
		$group_forum_id = new fieldType_hidden('group_forum_id');
		$group_id = new fieldType_hidden('group_id');
		$attachment = new field_attachment('attachment');
        
        parent::registerField($attachment);	
		parent::registerField($title);
		parent::registerField($first_post);
		parent::registerField($forum_id);
		parent::registerField($notify_me);
		parent::registerField($group_forum_id);
		parent::registerField($group_id);
						
		$title->maxlength = 100;
		$first_post->maxlength = 65535;		
		
		parent::registerAction('form_ForumNewTopic_Process');
	}
	
}

class form_ForumNewTopic_Process extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct('post');
	}
	
	public function setup( SK_Form $form )
	{
		$this->required_fields = array('title', 'first_post');
					
		parent::setup($form);
	}
	
	public function checkData( array $data, SK_FormResponse $response, SK_Form $form ) 
	{
		$lang_errors = SK_Language::section( 'forms.forum_new_topic.messages.error'  );
		$service = new SK_Service('forum_write', SK_HttpUser::profile_id());
		
		if( $service->checkPermissions()!= SK_Service::SERVICE_FULL ){
			$response->addError( $service->permission_message['message'] );
		}
		
		if( !SK_HttpUser::profile_id() )
			$response->addError( $lang_errors->text('guest_cannot_add_topic') );		
	}

	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{
	    $forum_id = $post_data['group_id'] ? $post_data['group_forum_id'] : $post_data['forum_id'];
		$topic_id = app_Forum::AddTopic( SK_HttpUser::profile_id(), $forum_id, $post_data['title'], $post_data['first_post'] );

		if( $post_data['notify_me'] )
			app_Forum::subscribeProfile( SK_HttpUser::profile_id(), $topic_id );
		
		if (is_array($post_data['attachment']))
		{
		    $attachments = array_filter($post_data['attachment']);
		    
		    foreach ($attachments as $att)
		    {
		        $file = new SK_TemporaryFile($att);
		        app_Attachment::add('forum_topic', $topic_id, $file);
		    }
		}
        
        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_NEWSFEED,
                    'entityType' => 'forum_add_topic',
                    'entityId' => $topic_id,
                    'userId' => SK_HttpUser::profile_id(),
                )
            );
            app_Newsfeed::newInstance()->action($newsfeedDataParams);
        }
			
		$response->redirect( SK_Navigation::href( 'topic', array('topic_id'=>$topic_id) ) );
	}
}
