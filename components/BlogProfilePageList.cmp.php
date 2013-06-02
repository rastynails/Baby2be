<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 20, 2008
 * 
 */

final class component_BlogProfilePageList extends SK_Component
{
	/**
	 * @var integer
	 */
	private $profile_id;
	
	/**
	 * @var app_BlogService
	 */
	protected $blog_service;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( array $params )
	{
		parent::__construct( 'profile_page_blog_list' );
		
		$this->blog_service = app_BlogService::newInstance();	
		$this->profile_id = (int)$params['profile_id'];	

		if(!app_Features::isAvailable(23))
			$this->annul();
	}
	
	/**
	 * @see AbstractBlogList::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		$blog_posts = $this->blog_service->findLastProfileBlogPosts( $this->profile_id );
		
		$assign_array = array();
		
		foreach ( $blog_posts as $key => $value )
		{
			$assign_array[$key]['post_url'] = component_BlogPostView::getBlogPostUrl( $value->getId() );
			$assign_array[$key]['text'] = app_TextService::stCensor( $value->getPreview_text(), FEATURE_BLOG );
			$assign_array[$key]['title'] = app_TextService::stCensor( $value->getTitle(), FEATURE_BLOG );
			$assign_array[$key]['dto'] = $value;
		}
	
		$Layout->assign( 'blog_url', httpdoc_BlogView::getBlogViewURL( $this->profile_id ) );
		$Layout->assign('bp_list', $assign_array );
	}

}