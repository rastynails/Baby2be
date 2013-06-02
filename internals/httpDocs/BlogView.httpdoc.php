<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 05, 2008
 *
 */


class httpdoc_BlogView extends SK_HttpDocument
{
	/**
	 * @var app_BlogService
	 */
	private $blog_service;

	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct('blog_view');
		$this->blog_service = app_BlogService::newInstance();

		if( !app_Features::isAvailable( 23 ) )
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
		$profile_id = (isset(SK_HttpRequest::$GET['profile_id']) && (int)SK_HttpRequest::$GET['profile_id'] > 0) ? (int)SK_HttpRequest::$GET['profile_id'] : false;

		$page = (isset(SK_HttpRequest::$GET['page']) && (int)SK_HttpRequest::$GET['page'] > 0) ? (int)SK_HttpRequest::$GET['page'] : 1;

		if( !$profile_id )
		{
			SK_HttpRequest::showFalsePage();
		}

		SK_Navigation::removeBreadCrumbItem();

		SK_Navigation::addBreadCrumbItem( app_Profile::getFieldValues( $profile_id, 'username' ). SK_Language::text( 'blogs.blog_view_bc_label_postfix' ) );

		$blog_list = $this->blog_service->findUserBlogPostsForBlog( $profile_id, $page );

		$ids = array();

		foreach ( $blog_list as $value )
		{
			$ids[] = $value->getId();
		}

		$comments_array = app_CommentService::stFindCommentsCountForEntities( $ids, 'blog', ENTITY_TYPE_BLOG_POST_ADD );

		$rates_array = app_RateService::stFindRatesForEntityIds( $ids, 'blog' );

		foreach ( $blog_list as $key => $value )
		{
			$blog_list[$key] = array(
				'dto' => $value,
				'url' => component_BlogPostView::getBlogPostUrl( $value->getId() ),
				'comments_count' => $comments_array[$value->getId()],
				'rate_info' => $rates_array[$value->getId()],
				'text' => $this->blog_service->processBlogPostForView( $value->getMored_text() ),
				'cap_label' => app_TextService::stCensor( SK_Language::htmlspecialchars( $value->getTitle() ), FEATURE_BLOG, true )
			);
		}

		$Layout->assign( 'bp_list', $blog_list );

		$Layout->assign( 'bp_count', $this->blog_service->findUserBlogPostsCount( $profile_id ) );

		$coinfigs = $this->blog_service->getConfigs();
		$Layout->assign( 'on_page', $coinfigs['blog_view_posts_count'] );

		$Layout->assign( 'tags', new component_TagNavigator( 'blog', SK_Navigation::href( 'blogs' ) ) );

		SK_Language::defineGlobal( 'username', app_Profile::getFieldValues( $profile_id, 'username' ) );

		return parent::render( $Layout );
	}

	/* static */

	public static function getBlogViewURL( $profile_id )
	{
		return SK_Navigation::href('view_blog',array('profile_id'=> $profile_id ));
	}

}