<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 21, 2008
 *
 */

final class component_NewsfeedCommentList extends SK_Component
{
	/**
	 * @var string
	 */
    protected $entityType;

    /**
	 * @var app_CommentService
	 */
    protected $comment_service;

	/**
	 * @var integer
	 */
	protected $entity_id;

	/**
	 * @var integer
	 */
	protected $page;

	/**
	 * @var string
	 */
	protected $feature;

	/**
	 * @var array
	 */
	protected $js_pages_array;

	/**
	 * @var array
	 */
	protected $comments_array;

	/**
	 * @var boolean
	 */
	protected $mode;

	/**
	 * Class
	 *
	 * @param integer $entityId
	 * @param string $feature
	 * @param integer $page
	 */
	public function __construct( $params )
	{
		parent::__construct( 'newsfeed_comment_list' );

		$this->entityType = $params['entityType'];
		$this->entity_id = $params['entity_id'];
		$this->feature = $params['feature'];
		$this->page = $params['page'];
		$this->mode = (int)$params['mode'];

        $this->comment_service = app_CommentService::newInstance( $this->feature );

		if( $this->comment_service === null )
		{
			$this->annul();
			return;
		}

        $this->comments_array = $this->comment_service->findCommentList( $this->entity_id, $this->page, $this->mode, $this->entityType );

		foreach ( $this->comments_array as $key => $value )
		{
			if( $value['dto']->getAuthor_id() == SK_HttpUser::profile_id() ||
                $this->comment_service->findEntityOwnerId( $this->entity_id, $this->feature, $this->entityType ) == SK_HttpUser::profile_id() ||
				SK_HttpUser::isModerator() )
			{
				$this->comments_array[$key]['delete'] = true;
				$this->comments_array[$key]['delete_link_id'] = 'delete_'.$value['dto']->getId();
			}

			$this->comments_array[$key]['profile_url'] = SK_Navigation::href( 'profile', array( 'profile_id' => $value['dto']->getAuthor_id() ) );
            $this->comments_array[$key]['username'] = app_Profile::username($value['dto']->getAuthor_id());
		}

		if( !$this->mode )
			$this->commentPages();
	}

	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
    public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('NewsfeedCommentList');
		$this->frontend_handler = $handler;

		$js_comm_array = array( 'entityType'=>$this->entityType, 'entity_id' => $this->entity_id, 'feature' => $this->feature, 'page' => $this->page, 'items' => array() );

		foreach ( $this->comments_array as $value )
		{
			if($value['delete'])
			{
				$js_comm_array['items'][] = array( 'id'=> $value['dto']->getId(), 'delete_link_id' => $value['delete_link_id'] );
			}
		}

		$handler->construct( $this->js_pages_array, $js_comm_array, $this->mode );

        $Layout->assign('expanded_comments_count', SK_Config::section('newsfeed')->get('comments_count'));
        $Layout->assign('comments_count', count($this->comments_array) );
	}

    /**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$Layout->assign( 'pages', $this->js_pages_array );

		$Layout->assign( 'comments', ( empty($this->comments_array) ? false : $this->comments_array ) );

		return parent::render( $Layout );

	}


    /**
	 * Custom methods for pages generation
	 *
	 */
	private function commentPages()
	{

		$pages_count = $this->comment_service->findCommentPagesCount( $this->entity_id, $this->entityType );

		if( $pages_count < 2 )
		{
			$this->js_pages_array = array();
			return;
		}

		$pages_array = array();

		for( $i=1; $i<=$pages_count; $i++ )
		{
			if( $i == $this->page )
			{
				$pages_array[] = array(
					'label' => $i,
					'id' => 'paging_item_'.$i,
					'active' => true
				);
			}
			else
			{
				$pages_array[] = array(
					'label' => $i,
					'id' => 'paging_item_'.$i
				);
			}
		}

		$this->js_pages_array = $pages_array;
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
			$handler->error( "Error in CommentService" );
			return;
		}

		$comment_item = $service->findCommentById($params->id_to_delete );

		if( $comment_item === null )
		{
			$handler->error( "Error with comment item ".$params->id_to_delete);
			return;
		}

		if( $comment_item !== null &&
			(
				$comment_item->getAuthor_id() == SK_HttpUser::profile_id() ||
				$service->findEntityOwnerId( $params->entity_id, $params->feature, $params->entityType ) == SK_HttpUser::profile_id() ||
				SK_HttpUser::isModerator()
			)
		)
		{
			$service->deleteComment( $comment_item->getId() );
		}

		$handler->message( SK_Language::text("%comment.msg_comment_delete") );

		$pages_count = $service->findCommentPagesCount( $comment_item->getEntity_id(), $comment_item->getEntityType() );

		$page = ( (int)$params->entity_id > $pages_count ) ? $pages_count : $params->page;

		self::reload( array('entityType'=>$params->entityType, 'entity_id' => $params->entity_id, 'feature' => $params->feature, 'page' => $page, 'mode' => $params->mode ), $handler, $response );
	}

}


