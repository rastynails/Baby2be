<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Feb 11, 2009
 * 
 */

final class component_ClsAddComment extends SK_Component 
{
	/**
	 * @var string
	 */
	private $feature;
	
	/**
	 * @var app_CommentService
	 */
	private $comment_service;

	/**
	 * @var string
	 */
	private $entity;

	/**
	 * @var integer
	 */
	private $entity_id;

	/**
	 * @var integer
	 */
	private $item_ended;
		
	/**
	 * @var integer
	 */
	private $allow_comments;
	
	/**
	 * @var integer
	 */
	private $allow_bids;
	
	/**
	 * @var string
	 */
	private $currency;	
		
	/**
	 * Class
	 *
	 * @param integer $entityId
	 * @param string $feature
	 */
	public function __construct( $entity, $entity_id, $item_ended, $allow_comments, $allow_bids, $currency )
	{
		parent::__construct( 'cls_add_comment' );
		
		$this->entity = $entity;
		$this->entity_id = $entity_id;
		$this->item_ended = $item_ended;
		$this->allow_comments = $allow_comments;
		$this->allow_bids = $allow_bids;
		$this->currency = $currency;
		$this->feature = FEATURE_CLASSIFIEDS;
		
		$this->comment_service = app_CommentService::newInstance( $this->feature );

		if( $this->comment_service === null )
		{
			$this->annul();
		}
	}
	
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('ClsAddComment');
		$handler->entity = $this->entity;
		$handler->currency = $this->currency;
		
		$this->frontend_handler = $handler;
		$handler->construct();
	}

	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		
		$add_comm_confs = $this->comment_service->getConfigs();
		
		$service = new SK_Service( $add_comm_confs['add_service'], SK_httpUser::profile_id() );
		
		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
		{
			//custom cond for profile cmts
			if( $this->feature == FEATURE_PROFILE && 
				app_ProfilePreferences::get('my_profile','friends_only_comments', $this->entity_id ) && 
				( $this->entity_id != SK_HttpUser::profile_id() && !app_FriendNetwork::isProfileFriend( $this->entity_id, SK_HttpUser::profile_id() ) ) )
			{
				$Layout->assign( 'err_message', SK_Language::text( 'comment.friends_only_comments_msg' ) );
			}
		}
		else 
		{
			$Layout->assign( 'comment_error_msg', $service->permission_message['message'] );
		}	
		
		if ( $this->entity=='wanted' ) {
			$configs = new SK_Config_Section('cls_wanted');
		}
		else {
			$configs = new SK_Config_Section('cls_offer');
		}
				
		if ( !$this->allow_comments || !$configs->allow_comments ) {
			$Layout->assign( 'comment_error_msg', SK_Language::text('components.cls_add_comment.error_msg.comment_not_allowed') );
		}		
		if ( $this->item_ended ) {
			$Layout->assign( 'bid_error_msg', SK_Language::text('components.cls_add_comment.error_msg.item_ended') );
		}
		if (!$this->allow_bids || !$configs->allow_bids) {
			$Layout->assign( 'bid_error_msg', SK_Language::text('components.cls_add_comment.error_msg.bid_not_allowed') );
		}
			
		$Layout->assign( 'comment_list', new component_ClsCommentList( array( 'entity_id' => $this->entity_id, 'currency'=>$this->currency ) ) );
		
		return parent::render( $Layout );
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		$form->getField( 'entity_id' )->setValue( $this->entity_id );;

		$component_var = $this->frontend_handler->auto_var();
		
		$form->frontend_handler->bind("success", "function(data) {
			 
			this.\$form[0].reset();
			
			var children = this.ownerComponent.children;
			
			for (var i = 0; i < children.length; i++) {
				var child = children[i];
				
				if (child instanceof component_ClsCommentList) {
					child.reload({entity_id:data.entity_id, currency: this.ownerComponent.currency});	
				}
			}
			
			var sibling = this.ownerComponent.parent.children;

			for (var i = 0; i < sibling.length; i++) {
				var sib = sibling[i];
				
				if (sib instanceof component_ClsItemBid) {
					sib.reload({entity_id:data.entity_id, entity: this.ownerComponent.entity, currency: this.ownerComponent.currency});	
				}
			}			
		}");
	}
}