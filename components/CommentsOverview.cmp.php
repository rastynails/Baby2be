<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Jan 08, 2008
 * 
 */


class component_CommentsOverview extends SK_Component
{
    /**
     *
     * @var app_CommentService 
     */
    private $comment_service = array();
    
    private $comments_array;
    //private $features = array(FEATURE_BLOG, FEATURE_CLASSIFIEDS, FEATURE_EVENT, FEATURE_GROUP, FEATURE_MUSIC, FEATURE_PHOTO, FEATURE_PROFILE, FEATURE_VIDEO);
    private $features = array();
    private $mode;

	/**
	 * Class constructor
	 *
	 */
	public function __construct( $namespace = 'comments_overview' )
	{
		parent::__construct($namespace);

        if ( app_Features::isAvailable(23) )
        {
            $this->features[] = FEATURE_BLOG;
        }

        if ( app_Features::isAvailable(31) && app_Features::isAvailable(45) )
        {
            $this->features[] = FEATURE_CLASSIFIEDS;
        }

        if ( app_Features::isAvailable(6) )
        {
            $this->features[] = FEATURE_EVENT;
        }

        if ( app_Features::isAvailable(38) )
        {
            $this->features[] = FEATURE_GROUP;
        }

        if ( app_Features::isAvailable(58) && app_Features::isAvailable(40) )
        {
            $this->features[] = FEATURE_MUSIC;
        }

        if ( app_Features::isAvailable(30) )
        {
            $this->features[] = FEATURE_PHOTO;
        }

        if ( app_Features::isAvailable(25) )
        {
            $this->features[] = FEATURE_PROFILE;
        }

        if ( app_Features::isAvailable(4) && app_Features::isAvailable(57) )
        {
            $this->features[] = FEATURE_VIDEO;
        }

        $mode = @SK_HttpRequest::$GET['mode'];
        $this->mode = $mode ? $mode : 'all';

        if (  !in_array($this->mode, $this->features) && $this->mode != 'all' )
        {
            SK_HttpRequest::showFalsePage();
        }

        $this->getLastCommentsList();
        
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )
	{
        $Layout->assign('comments', $this->comments_array);
        $Layout->assign('feature_tabs', $this->get_tabs());
	
		return parent::render( $Layout );
	}

    /**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
    public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
    {
		$handler = new SK_ComponentFrontendHandler('CommentsOverview');
		$this->frontend_handler = $handler;

		$js_comm_array = array( 'items' => array() );
        $profileIdList = array();

		foreach ( $this->comments_array as $value )
		{
            $profileIdList[] = $value['author_id'];
			if($value['delete'])
			{
				$js_comm_array['items'][] = array( 'feature'=>$value['feature'], 'id'=> $value['id'], 'entity_id'=>$value['entity_id'], 'delete_link_id' => $value['delete_link_id'] );
			}
		}

        app_Profile::getUsernamesForUsers($profileIdList);
        app_ProfilePhoto::getThumbUrlList($profileIdList);
        app_Profile::getOnlineStatusForUsers($profileIdList);

		$handler->construct( $js_comm_array, $this->mode );
    }


    public function getLastCommentsList( $count = 20 )
    {
        
        if ($this->mode == 'all')
        {
            $this->comment_service = app_CommentService::newInstance(FEATURE_BLOG);
            $this->comments_array = $this->comment_service->findProfilesLastCommentList(SK_HttpUser::profile_id(), $count);
        }
        else
        {
            $this->comment_service = app_CommentService::newInstance($this->mode);
            $this->comments_array = $this->comment_service->findProfilesCommentListByFeature(SK_HttpUser::profile_id(), $this->mode);
        }

        foreach ($this->comments_array as $key=>$comment_item)
        {
            $this->comments_array[$key]['is_deleted'] = app_Profile::isProfileDeleted($comment_item['author_id']);                
            $this->comments_array[$key]['username'] = app_Profile::username($comment_item['author_id']);
            $this->comments_array[$key]['profile_url'] = app_Profile::getUrl($comment_item['author_id']);

            if( $comment_item['author_id'] == SK_HttpUser::profile_id() ||
                $this->comment_service->findEntityOwnerId( $comment_item['entity_id'], $comment_item['feature'] ) == SK_HttpUser::profile_id() ||
                SK_HttpUser::isModerator() )
            {
                $this->comments_array[$key]['delete'] = true;
                $this->comments_array[$key]['delete_link_id'] = 'delete_'.$comment_item['id'];
            }

            $entityInfo = $this->getEntityInfo($comment_item['entity_id'], $this->comments_array[$key]['feature']);

            if (isset($entityInfo))
            {
                $this->comments_array[$key]['entity_url'] = $entityInfo['url'];
                $this->comments_array[$key]['entity_title'] = $entityInfo['title'];
            }

        }
    }

    public function getEntityInfo($entity_id, $feature)
    {
        $result = array();
        switch( $feature )
        {
            case FEATURE_BLOG:
            case FEATURE_NEWS:
                $result['url'] = SK_Navigation::href('blog_post_view', array( 'postId'=> $entity_id ));
                $blog_post = app_BlogService::newInstance()->findBlogPostById($entity_id);
                $result['title'] = $blog_post->getTitle();
                break;

            case FEATURE_CLASSIFIEDS:
                $result['url'] = SK_Navigation::href('classifieds_item', array( 'item_id'=> $entity_id ) );
                $cls_item = app_ClassifiedsItemService::stGetItemInfo($entity_id);
                $result['title'] = $cls_item['title'];
                break;

            case FEATURE_EVENT:
                $result['url'] = SK_Navigation::href('event', array('eventId'=> $entity_id));
                $event = app_EventService::stFindEventInfo($entity_id);
                $result['title'] = $event['dto']->getTitle();
                break;

            case FEATURE_GROUP:
                $result['url'] = SK_Navigation::href('group', array('group_id'=> $entity_id));
                $group = app_Groups::getGroupById($entity_id);
                $result['title'] = $group['title'];
                break;

            case FEATURE_MUSIC:
                $result['url'] = nav_music::profile_music( array('music_id'=>$entity_id) );
                $hash = app_ProfileMusic::getMusicHash($entity_id);
                $music = app_ProfileMusic::getMusicInfo(SK_HttpUser::profile_id(), $hash);
                $result['title'] = $music['title'];
                break;

            case FEATURE_PHOTO:
                $result['url'] = app_ProfilePhoto::getPermalink($entity_id);
                $photo = app_ProfilePhoto::getPhoto($entity_id);
                $result['title'] = SK_Language::section('components.index_photo_list')->text('title');
                break;
            case FEATURE_PROFILE:
                $result['url'] = app_Profile::getUrl(SK_HttpUser::profile_id());
                $result['title'] = SK_Language::section('nav_menu_item')->text('my_profile');
                break;
            case FEATURE_VIDEO:
                $hash = app_ProfileVideo::getVideoHash($entity_id);
                $video = app_ProfileVideo::getVideoInfo(SK_HttpUser::profile_id(), $hash);
                $result['url'] = app_ProfileVideo::getVideoViewURL($hash);
                $result['title'] = $video['title'];
                break;
            default:
                return null;
                break;
        }

        return $result;
    }

    public function get_tabs()
	{
		$tabs = array();
        $features = array_merge( array('all'), $this->features );
        
		foreach ( $features as $value )
			$tabs[] = array(
			'label' => SK_Language::section('comment')->text('feature_list_tab_'.$value),
			'href' => sk_make_url(SK_HttpRequest::getDocument()->url,( $value == 'all' ? '' : 'mode='.$value )),
			'active' => ( $this->mode == $value )
			);
		return $tabs;
	}

    /**
	 * Ajax method for comment deleting
	 *
	 * @param $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_deleteComment( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_CommentService::newInstance( $params->feature );

        if( $service === null )
		{
			$handler->error( "Some error occured with comment service" );
			return;
		}

		$comment_item = $service->findCommentById( $params->id_to_delete );

		if( $comment_item === null )
		{
			$handler->error( "Comment item is not defined: ".$params->id_to_delete);
			return;
		}

		if( $comment_item !== null &&
			(
				$comment_item->getAuthor_id() == SK_HttpUser::profile_id() ||
				$service->findEntityOwnerId( $params->entity_id, $params->feature ) == SK_HttpUser::profile_id() ||
				SK_HttpUser::isModerator()
			)
		)
		{
			$service->deleteComment( $comment_item->getId() );
            $handler->message( SK_Language::text("%comment.msg_comment_delete") );
		}
		
        $handler->remove($params->feature, $params->id_to_delete);
	}

}