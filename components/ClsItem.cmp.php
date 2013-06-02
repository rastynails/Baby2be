<?php

class component_ClsItem extends SK_Component
{
	/**
	 * @var int
	 */
	private $item_id;

	/**
	 * @var int
	 */
	private $item_info;

	public function __construct( array $params = null )
	{
	    if ( !app_Features::isAvailable(45) )
        {
            SK_HttpRequest::showFalsePage();
        }

		parent::__construct('cls_item');
	}

	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->item_id = SK_HttpRequest::$GET['item_id'];
		$this->item_info = app_ClassifiedsItemService::stGetItemInfo( $this->item_id );

		if ( !$this->item_info ) {
			SK_HttpRequest::redirect( SK_Navigation::href( 'classifieds' ) );
		}

		$handler = new SK_ComponentFrontendHandler('ClsItem');
		$handler->construct( SK_Config::section('classifieds')->view_fullsize , $this->item_info);

		$this->frontend_handler = $handler;

		return parent::prepare( $Layout, $Frontend );
	}

	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render( SK_Layout $Layout )
	{


		$item_files = app_ClassifiedsFileService::stGetItemFiles( $this->item_id );

		SK_Language::defineGlobal('itemname', $this->item_info['title']);

                if ( !empty($this->item_info['description']) )
                {
                    $this->getDocumentMeta()->description = $this->item_info['description'];
                }

		$this->item_info['payment_dtls'] = app_ClassifiedsItemService::stFilterPaymentDetails($this->item_info['payment_dtls']);

    	$Layout->assign( 'item_info', $this->item_info );
		$Layout->assign( 'item_files', $item_files );
		$Layout->assign( 'profile_id', SK_HttpUser::profile_id() );
		$Layout->assign( 'moderator', app_Profile::isProfileModerator( SK_HttpUser::profile_id() ) );
		$Layout->assign( 'to_approve', !$this->item_info['is_approved'] );

		$comments_add = new component_ClsAddComment( $this->item_info['entity'], $this->item_id, $this->item_info['item_ended'],
			$this->item_info['allow_comments'], $this->item_info['allow_bids'], $this->item_info['currency'] );
		$Layout->assign( 'item_comments', $comments_add );

        if (SK_Config::section('cls_'.$this->item_info['entity'])->allow_bids)
        {
            $Layout->assign( 'allow_bids', $this->item_info['allow_bids'] );
        }

        if (SK_Config::section('cls_'.$this->item_info['entity'])->allow_comments)
        {
            $Layout->assign( 'allow_comments', $this->item_info['allow_comments'] );
        }

		return parent::render($Layout);
	}

	public static function ajax_DeleteItem ( $params , SK_ComponentFrontendHandler $handler)
	{
		$item_id = intval( $params->entity_id );
		$profile_id = SK_HttpUser::profile_id();

		if ( !$item_id ) {	return false;	}

		if ( $profile_id != app_ClassifiedsItemService::stGetItemOwnerId($item_id) && !app_Profile::isProfileModerator($profile_id) )
		{
			$handler->error( SK_Language::text('components.cls_item.error_msg.cannot_delete') );

			return false;
		}

		app_ClassifiedsItemService::stDeleteItem( $item_id );

		$handler->message( SK_Language::text('components.cls_item.messages.item_deleted') );

		$handler->redirect( SK_Navigation::href( 'classifieds' ) );
	}

	public static function ajax_ApproveItem ( $params, SK_ComponentFrontendHandler $handler)
	{
		$item_id = intval( $params->entity_id );
		$profile_id = SK_HttpUser::profile_id();

		if ( !$item_id ) {	return false;	}

		if ( !app_Profile::isProfileModerator($profile_id) )
		{
			$handler->error( SK_Language::text('components.cls_item.error_msg.cannot_approve') );

			return false;
		}

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_CLASSIFIEDS,
                    'entityType' => 'post_classifieds_item',
                    'entityId' => $params->entity_id,
                    'status' => 'active'
                )
            );
            app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);
        }

		app_ClassifiedsItemService::stApproveItem( $item_id );

		$handler->message( SK_Language::text('components.cls_item.messages.item_approved') );


	}
}

