<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Feb 11, 2009
 * 
 */

final class component_ClsCommentList extends SK_Component 
{
	/**
	 * @var app_CommentService
	 */
	private $comment_service;
	
	/**
	 * @var integer
	 */
	private $entity_id;
	
	/**
	 * @var string
	 */
	private $currency;	
			
	/**
	 * @var integer
	 */
	private $feature;

	/**
	 * @var array
	 */
	private $comments_array;
		
	/**
	 * Class
	 *
	 * @param integer $entityId
	 */
	public function __construct( $params )
	{
		parent::__construct( 'cls_comment_list' );
		
		$this->entity_id = $params['entity_id'];
		$this->currency = $params['currency'];		
		$this->feature = 'classifieds';
		
		$this->comment_service = app_CommentService::newInstance( $this->feature );
		
		$this->comments_array = $this->comment_service->findClassifiedsCommentList( $this->entity_id );
		foreach ( $this->comments_array as $key => $value )
		{
			if( $value['dto']->getAuthor_id() == SK_HttpUser::profile_id() ||
				$this->comment_service->findEntityOwnerId( $this->entity_id, $this->feature ) == SK_HttpUser::profile_id() ||
				SK_HttpUser::isModerator() )
			{
				$this->comments_array[$key]['delete'] = true;
				$this->comments_array[$key]['delete_link_id'] = 'delete_'.$value['dto']->getId();
			}
		}

	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{			
		$Layout->assign( 'comments', ( empty($this->comments_array) ? false : $this->comments_array ) );
		$Layout->assign( 'currency', $this->currency );
		
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
		$handler = new SK_ComponentFrontendHandler('ClsCommentList');
		$this->frontend_handler = $handler;
		
		$js_comm_array = array( 'entity_id' => $this->entity_id, 'feature' => $this->feature, 'items' => array(), 'currency'=>$this->currency );
		
		foreach ( $this->comments_array as $value )
		{
			if($value['delete'])
			{
				$js_comm_array['items'][] = array( 'id'=> $value['dto']->getId(), 'delete_link_id' => $value['delete_link_id'] );
			}
		}
		
		$handler->construct( $js_comm_array );
	}

	
	/**
	 * Ajax method for comment deleting
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_deleteComment( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_CommentService::newInstance( $params->feature );
		
		if( $service === null )
		{
			$handler->error( "Error1" );
			return;
		}
		
		$comment_item = $service->findCommentById( $params->id_to_delete );

		if( $comment_item === null )
		{
			$handler->error( "Error2".$params->id_to_delete);
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
			app_ClassifiedsBidService::stDeleteEntityBid( $comment_item->getId() ); 
		}
		
		$handler->updateBidInfo();
		
		$handler->message( SK_Language::text('components.cls_comment_list.messages.comment_deleted') );
		
		self::reload( array( 'entity_id'=>$params->entity_id, 'feature'=>$params->feature, 'currency'=>$params->currency ), $handler, $response );
	} 
}


