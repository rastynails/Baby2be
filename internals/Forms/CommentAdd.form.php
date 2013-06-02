<?php
require_once DIR_APPS . 'appAux/Comment.php';


/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 04, 2008
 * 
 */

class form_CommentAdd extends SK_Form
{
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('comment_add');
    }

    public function setup()
    {
        $text_field = new fieldType_textarea('comment_text');
        $text_field->maxlength = 1000;
        $this->registerField($text_field);
        $this->registerField(new fieldType_hidden('entity_id'));
        $this->registerField(new fieldType_hidden('entityType'));
        $this->registerField(new fieldType_hidden('mode'));
        $this->registerField(new fieldType_hidden('feature'));
        $this->registerAction(new CommentAddFormAction());
    }

    /**
     * @see SK_Form::renderStart()
     *
     * @param array $params
     * @return string
     */
//	public function renderStart ( $params )
//	{
//		$this->getField('entity_id')->setValue( $params['entity_id'] );
//		$this->getField('feature')->setValue( $params['feature'] );
//		return parent::renderStart();
//	}

}

class CommentAddFormAction extends SK_FormAction
{
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct('add_comment');
    }

    /**
     * @see SK_FormAction::process()
     *
     * @param array $data
     * @param SK_FormResponse $response
     * @param SK_Form $form
     */
    public function process( array $data, SK_FormResponse $response, SK_Form $form )
    {
        if ( !SK_HttpUser::is_authenticated() )
        {
            $response->addError("You should sign in to add comments!");
            return;
        }

        $service = app_CommentService::newInstance($data['feature']);

        if ( $service === null || (int) $data['entity_id'] <= 0 || app_Profile::suspended() )
        {
            $response->addError('error');
            return;
        }

        $configs = $service->getConfigs();
        $entity_id = $data['entity_id'];
        $comment = new Comment(SK_HttpUser::profile_id(), $data['comment_text'], $entity_id, time());
        $comment->setEntityType($data['entityType']);

        $service->saveOrUpdate($comment);

        $actionData = '';

        $feature = $data['feature'];
        $entityType = $data['entityType'];
        switch ( $data['feature'] )
        {
            case FEATURE_BLOG:

                $type = 'blog_post_comment';
                $entityType = 'blog_post_add';
                break;

            case FEATURE_NEWS:

                $type = 'news_post_comment';
                $entityType = 'blog_post_add';
                break;

            case FEATURE_PHOTO:

                $type = 'photo_comment';
                $entityType = 'photo_upload';
                break;

            case FEATURE_VIDEO:

                $type = 'video_comment';
                $entityType = 'media_upload';
                break;

            case FEATURE_EVENT:

                $type = 'event_comment';
                $entityType = 'event_add';
                break;

            case FEATURE_PROFILE:

                $type = 'profile_comment';
                $entityType = 'profile_comment';
                break;

            case FEATURE_GROUP:

                $type = 'group_comment';
                $entityType = 'group_add';
                break;

            case FEATURE_MUSIC:

                $type = 'music_comment';
                $entityType = 'music_upload';
                break;
            
            case FEATURE_CLASSIFIEDS:
                
                $type = 'classifieds_comment';
                $entityType = 'post_classifieds_item';

            case FEATURE_NEWSFEED:
                break;
        }

        if ( SK_HttpUser::profile_id() )
        {
            if ( !empty($type) )
            {
                $action = new SK_UserAction($type, SK_HttpUser::profile_id());
                $action->item = (int) $data['entity_id'];
                $action->commentId = (int) $comment->getId();
                $action->status = 'active';
                $action->unique = (int) $comment->getId();
                $action->feature = $data['feature'];

                app_UserActivities::trace_action($action);
            }

            // Trace newsfeed action
            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) && SK_Config::section('newsfeed')->allow_comments )
            {
                $replace = ( $entityType == 'profile_comment' ) ? true : false;
                $newsfeedDataParams = array(
                    'params' => array(
                        'feature' => $feature,
                        'entityType' => $entityType,
                        'entityId' => (int) $data['entity_id'],
                        'userId' => SK_HttpUser::profile_id(),
                        'replace' => $replace,
                        'time' => time()
                    ),
                    'data' => array(
                        'commentId' => $comment->getId()
                    )
                );

                app_Newsfeed::newInstance()->action($newsfeedDataParams);
            }

            //
        }

        $response->addMessage(SK_Language::text('comment.msg_comment_add'));

        // Service tracking
        $service_to_track = new SK_Service($configs['add_service'], SK_httpUser::profile_id());
        $service_to_track->checkPermissions();
        $service_to_track->trackServiceUse();

        return array('entity_id' => $data['entity_id'], 'entityType' => $entityType, 'feature' => $data['feature'], 'mode' => $data['mode'], 'entityType' => $data['entityType']);
    }

    /**
     * @see SK_FormAction::setup()
     *
     * @param SK_Form $form
     */
    public function setup( SK_Form $form )
    {
        $this->required_fields = array('comment_text');
        parent::setup($form);
    }
}