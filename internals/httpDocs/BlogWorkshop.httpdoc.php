<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 27, 2008
 * 
 */

class httpdoc_BlogWorkshop extends SK_HttpDocument
{
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct( 'blog_workshop' );
	}
	
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		switch ( trim(SK_HttpRequest::$GET['tab']) )
		{
			case 'add':
				$Layout->assign('cmp',new component_BlogAdd());
				SK_Navigation::addBreadCrumbItem( SK_Language::text( 'blogs.bc_item_add_post' ) );
				break;
				
			case 'draft':
				$Layout->assign('cmp', new component_BlogDraftList());
				SK_Navigation::addBreadCrumbItem( SK_Language::text( 'blogs.bc_item_drafts' ) );
				break;
							
			case 'edit':
				$Layout->assign('cmp', new component_BlogPostEdit());
				SK_Navigation::addBreadCrumbItem( SK_Language::text( 'blogs.bc_item_edit' ) );
				break;
				
			default:
				$Layout->assign('cmp', new component_BlogManageList());
				break;
		}
		
		$Layout->assign( 'blog_url', httpdoc_BlogView::getBlogViewURL( SK_HttpUser::profile_id() ) );
		
		return parent::render( $Layout );
	}
	
	
	/**
	 * Returns array for page components tab menu
	 *
	 * @param string $active
	 * @return array
	 */
	public static function getBlockMenuArray( $active )
	{	
		return array(
			array( 'href' => SK_Navigation::href('manage_blog',array('tab'=>'manage')), 'label' => SK_Language::text( 'blogs.menu_item_manage_blog' ), 'active' => ( $active === 'manage' ? true : false ) ),	
			array( 'href' => SK_Navigation::href('manage_blog',array('tab'=>'draft')), 'label' => SK_Language::text( 'blogs.menu_item_drafts' ), 'active' => ( $active === 'draft' ? true : false ) ),
			array( 'href' => SK_Navigation::href('manage_blog',array('tab'=>'add')), 'label' => SK_Language::text( 'blogs.menu_item_new_post' ), 'active' => ( $active === 'add' ? true : false ) )	
		);
	}
	
	/**
	 * Generates and returns Blog Post edit Link
	 *
	 * @param integer $id
	 * @return string
	 */
	public static function getPostEditLink( $id )
	{
		return SK_Navigation::href('manage_blog', array( 'tab' => 'edit', 'postId' => (int)$id ));	
	}
}




