<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 20, 2008
 * 
 */

final class component_BlogProfilePageList extends BlogAbstractList
{
	/**
	 * @var integer
	 */
	private $profile_id;
	
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( $profile_id )
	{
		parent::__construct( 'profile_page_blog_list' );
		$this->profile_id = $profile_id;		
	}
	
	/**
	 * @see AbstractBlogList::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		$Layout->assign('bp_list', $this->blog_service->findLastProfileBlogPosts( $this->profile_id, 5 ) );
	}

}