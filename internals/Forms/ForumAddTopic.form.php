<?php

class form_ForumAddTopic extends SK_Form
{	
	public function __construct()
	{
		parent::__construct('forum_add_topic');
	}
	
	public function setup()
	{
		$title = new fieldType_text('title');
		$first_post = new fieldType_textarea('first_post');
		$forum_id = new fieldType_hidden('forum_id');
		$notify_me = new fieldType_checkbox('notify_me');
		$attachment = new field_attachment('attachment');
        
        parent::registerField($attachment);	
		parent::registerField($title);
		parent::registerField($first_post);
		parent::registerField($forum_id);
		parent::registerField($notify_me);
		
				
		$title->maxlength = 100;
		$first_post->maxlength = 65535;		
		
		parent::registerAction('form_ForumAddTopic_Process');
	}
	
}

class form_ForumAddTopic_Process extends SK_FormAction
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
	
	public function process( array $post_data, SK_FormResponse $response, SK_Form $from )
	{		
		$service = new SK_Service('forum_write', SK_HttpUser::profile_id());

        if ( app_Profile::suspended() )
        {
            return false;
        }

		if( $service->checkPermissions()!= SK_Service::SERVICE_FULL ){
			$response->addError( $service->permission_message['message'] );
			return false;
		}
		
		//insert data and name into quote 
		$replace_str = "[quote name='" . SK_HttpUser::username() . "' date='" . app_Forum::getQuoteDate() . "']";
		$post_data['first_post'] = str_replace( '[quote]', $replace_str, $post_data['first_post'] );
		
		$topic_id = app_Forum::AddTopic( SK_HttpUser::profile_id(), $post_data['forum_id'], $post_data['title'], $post_data['first_post'] );
		
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

		if( $post_data['notify_me'] )
			app_Forum::subscribeProfile( SK_HttpUser::profile_id(), $topic_id );
		
		$response->redirect( SK_Navigation::href( 'topic', array('topic_id'=>$topic_id) ) );
	}
}
