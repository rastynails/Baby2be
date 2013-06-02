<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Jan 08, 2008
 * 
 */


class httpdoc_Moderate extends SK_HttpDocument
{
	/**
	 * @var app_BlogService
	 */
	private $blog_service;

	/**
	 * @var app_EventService
	 */
	private $event_service;
	
	/**
	 * @var app_ClassifiedsItemService
	 */
	private $cls_services;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct('moderator');
		$this->blog_service = app_BlogService::newInstance();
		$this->event_service = app_EventService::newInstance();
		$this->cls_services = app_ClassifiedsItemService::newInstance();
		
		if( !SK_HttpUser::isModerator() )
		{
			SK_HttpRequest::showFalsePage();
		}
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$b_page = (isset(SK_HttpRequest::$GET['bPage']) && (int)SK_HttpRequest::$GET['bPage'] > 0) ? (int)SK_HttpRequest::$GET['bPage'] : 1;

		$blog_array = $this->blog_service->findPostsForModeration( $b_page );
		
		$posts_count = $this->blog_service->findPostsForModerationCount();
	
		foreach ( $blog_array as $key => $value )
		{
			$blog_array[$key]['temp']['profile_url'] = SK_Navigation::href( 'profile', array( 'profile_id' => $value['dto']->getProfile_id() ) );
			$blog_array[$key]['temp']['blog_post_url'] = component_BlogPostView::getBlogPostUrl( $value['dto']->getId() );
			$blog_array[$key]['temp']['title'] = app_TextService::stCensor( $value['dto']->getTitle(), FEATURE_BLOG );	
		}

		if( $posts_count == 0 )
		{
			$Layout->assign( 'no_posts', true );
		}
		
		$Layout->assign( 'bp_list', $blog_array );
		$Layout->assign( 'bp_count', $posts_count );
		
		/** --------- Event --------- **/

		$e_page = (isset(SK_HttpRequest::$GET['ePage']) && (int)SK_HttpRequest::$GET['ePage'] > 0) ? (int)SK_HttpRequest::$GET['ePage'] : 1;
		
		$event_array = $this->event_service->findEventsToModerate( $e_page );
		
		foreach ( $event_array as $key => $value )
		{
			if( $value['dto']->getImage() != null )
				$event_array[$key]['image_url'] = $this->event_service->getEventImageURL( 'event_icon_'.$value['dto']->getId().'.jpg' );
			else 
				$event_array[$key]['image_url'] = $this->event_service->getEventDefaultImageURL();
			
			$event_array[$key]['event_url'] = component_Event::getEventUrl( $value['dto']->getId() );
			$event_array[$key]['profile_url'] = SK_Navigation::href( 'profile', array( 'profile_id' => $value['dto']->getProfile_id() ) );
		}
		
		$events_count = $this->event_service->findEventsToModerateCount();
		
		if( $events_count == 0 )
		{
			$Layout->assign( 'no_events', true );
		}
		
		$Layout->assign( 'event_list', $event_array );
		$Layout->assign( 'event_count', $events_count );
		
		/* -----  Classifieds approving ---------------------- */

        $to_approve = !$this->cls_services->getIsAutoApprove();

		if ($to_approve)
		{
			$cls_file_service = app_ClassifiedsFileService::newInstance();
			$c_page = (isset(SK_HttpRequest::$GET['cPage']) && (int)SK_HttpRequest::$GET['cPage'] > 0) ? (int)SK_HttpRequest::$GET['cPage'] : 1;
			
			$cls_array = $this->cls_services->findItemsToModerate( $c_page );
			$cls_list = array();
			
			foreach ( $cls_array as $key => $value )
			{			
				$cls_array[$key]['title'] =  app_TextService::stOutputFormatter($value['dto']->getTitle());
				$cls_array[$key]['description'] = app_TextService::stOutputFormatter($value['dto']->getDescription() );
                                $cls_array[$key]['title'] =  app_TextService::stCensor( $cls_array[$key]['title'], FEATURE_CLASSIFIEDS );
                                $cls_array[$key]['description'] =  app_TextService::stCensor( $cls_array[$key]['description'], FEATURE_CLASSIFIEDS );
				$cls_array[$key]['item_url'] = SK_Navigation::href('classifieds_item', array('item_id'=>$value['dto']->getId()));
				$cls_array[$key]['profile_url'] = SK_Navigation::href( 'profile', array( 'profile_id' => $value['dto']->getProfile_id() ) );
				
				//get item thumb
				$item_files = $cls_file_service->getItemFiles( $value['dto']->getId() );
				$cls_array[$key]['item_thumb'] = ( $item_files[0] ) ? $item_files[0]['file_url'] : false;				
				
			}
				
			
			$cls_count = $this->cls_services->findItemsToModerateCount();

			if( $cls_count == 0 )
			{
				$Layout->assign( 'no_cls', true );
			}
			
			$Layout->assign( 'cls_list', $cls_array );
			$Layout->assign( 'cls_count', $cls_count );
			
		}

		$Layout->assign_by_ref('to_approve', $to_approve);
		
		return parent::render( $Layout );
	}
}