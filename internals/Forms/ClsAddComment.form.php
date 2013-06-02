<?php
require_once DIR_APPS.'appAux/Comment.php';


/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Feb 11, 2009
 * 
 */

class form_ClsAddComment extends SK_Form
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'cls_add_comment' );
	}
	
	public function setup()
	{		
		$text_field = new fieldType_textarea( 'comment_text' );
		$text_field->maxlength = 1000;
		
		$bid_field = new fieldType_text( 'bid' );
		$bid_field->setRegExPatterns('/^[\d]{0,8}([.,]{1}[\d]{1,2})?$/');
		
		$this->registerField( $text_field );
		$this->registerField( $bid_field );
		$this->registerField( new fieldType_hidden('entity_id') );

		$this->registerAction( new CommentAddFormAction() );
	}
	

}

class CommentAddFormAction extends SK_FormAction
{	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'add_comment' );
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
		$lang_errors = SK_Language::section('components.cls_add_comment.error_msg');
		
		$item_info = app_ClassifiedsItemService::stGetItemInfo( $data['entity_id'] );
		
		if( !SK_HttpUser::is_authenticated() )
		{
			$response->addError( $lang_errors->text("guest_cannot_add_comment") );
			return;
		}
		
		if( (int)$data['entity_id'] <= 0 )
		{
			$response->addError( $lang_errors->text("incorrect_item_id") );
			return;
		}
		
		if ( !$item_info ) {
			$response->addError( $lang_errors->text("item_is_not_exist") );
			return ;
		}
		
		if ( !$item_info['allow_comments'] ) {
			$response->addError( $lang_errors->text("comment_not_allowed") );
			return ;
		}
		
		if ( isset( $data['bid']) && !$item_info['allow_bids'] ) {
			$response->addError( $lang_errors->text("bid_not_allowed") );
			return ;
		}
					
		if ( isset( $data['bid']) && $item_info['limited_bids'] ) {
			if ( $item_info['entity']=='wanted' ) {
				$incorrect_bid = ( floatval($item_info['budget_max']) < floatval($data['bid']) ) ? true : false;
			}
			else {
				$incorrect_bid = ( floatval($item_info['price']) > floatval($data['bid']) ) ? true : false;
			}
		}
		
		if ( $incorrect_bid ) {
			$response->addError( $lang_errors->text($item_info['entity']."_incorrect_bid"), "bid" );
			return ;
		}		
				
		$service = app_CommentService::newInstance( FEATURE_CLASSIFIEDS );
		$comment = new Comment( SK_HttpUser::profile_id(), $data['comment_text'], $data['entity_id'], time() );
		$service->saveOrUpdate( $comment );
		
		if ( isset( $data['bid'] ) ) {
			$data['bid'] = floatval( str_replace(',', '.', $data['bid']) );
			
			$service = app_ClassifiedsBidService::newInstance();
			$service->saveOrUpdate( new ClassifiedsBid( SK_HttpUser::profile_id(), $comment->getId(), $data['bid'], time() )  );
		}
		
		
		$response->addMessage( SK_Language::text( 'comment.msg_comment_add' ) );
		
		return array( 'entity_id' => $data['entity_id'] );
		
	}
	
	/**
	 * @see SK_FormAction::setup()
	 *
	 * @param SK_Form $form
	 */
	public function setup($form) 
	{
		$this->required_fields = array( 'comment_text' );
		parent::setup($form);
	}
}