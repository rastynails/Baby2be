<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 29, 2008
 * 
 */

class component_BlogManageList extends component_BlogWorkshopList
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'blog_manage_list' );
	}
	
	/**
	 * @see component_BlogWorkshopList::getBlogPostCount()
	 *
	 */
	protected function getBlogPostCount() 
	{
		return $this->blog_service->findUserBlogPostsCount( SK_HttpUser::profile_id(), 'admin' );
	}
	
	/**
	 * @see component_BlogWorkshopList::getBlogPostList()
	 *
	 */
	protected function getBlogPostList() 
	{
		return $this->blog_service->findUserBlogPostsWithCC( SK_HttpUser::profile_id(), $this->getCurrentPage(), 'admin' );
	}
	
	/**
	 * @see component_BlogWorkshopList::getMenuArray()
	 *
	 */
	protected function getMenuArray() 
	{
		return httpdoc_BlogWorkshop::getBlockMenuArray('manage');
	}
	
	/**
	 * @see component_BlogWorkshopList::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		return parent::render( $Layout );
	}

}