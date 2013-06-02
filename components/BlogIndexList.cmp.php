<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 20, 2008
 * 
 */

final class component_BlogIndexList extends SK_Component 
{
    private $postsCount = null;

	/**
	 * @var app_BlogService
	 */
	private $blog_service;
	
	public function __construct( $params = array() )
	{
		parent::__construct( 'index_blog_list' );

		$this->blog_service = app_BlogService::newInstance();

        if( isset ($params['count']) )
        {
            $this->postsCount = (int)$params['count'];
        }
		
		if(!app_Features::isAvailable(23))
			$this->annul();
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend)
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('BlogIndexList');
		
		$this->frontend_handler->construct();
	}
		
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		$blog_list = $this->blog_service->findIndexPageBlogs($this->postsCount);
        $pop_blog_list = $this->blog_service->findIndexMostPopularPosts($this->postsCount);
        
		if( empty( $blog_list ) )
			$Layout->assign( 'no_posts', true );
		
		$assign_list = array();

        $userList = array();

        foreach ( $blog_list as $item )
        {
            $userList[] = $item->getProfile_id();
        }
        
        foreach ( $pop_blog_list as $item )
        {
            $userList[] = $item['dto']->getProfile_id();
        }

        app_Profile::getUsernamesForUsers($userList);
        app_ProfilePhoto::getThumbUrlList($userList);
        app_Profile::getOnlineStatusForUsers($userList);

		foreach ( $blog_list as $value )
		{
			$assign_list[] = array( 'title' => app_TextService::stCensor( $value->getTitle(), FEATURE_BLOG, true ),
				'desc' => app_TextService::stCensor( $value->getPreview_text(), FEATURE_BLOG ), 
				'blog_post_url' => component_BlogPostView::getBlogPostUrl( $value->getId() ),
				'profile_url' => SK_Navigation::href( 'profile', array( 'profile_id' => $value->getProfile_id() ) ),
				'username' => app_Profile::username($value->getProfile_id()),
				'dto' => $value
			 );
		}

		$Layout->assign( 'blogs_url', SK_Navigation::href( 'blogs' ) );
		
		$Layout->assign('bp_list',$assign_list);

		$pop_assign_list = array();
		
		foreach ( $pop_blog_list as $value )
		{
			$pop_assign_list[] = array( 'title' => app_TextService::stCensor( $value['dto']->getTitle(), FEATURE_BLOG, true ),
				'desc' => app_TextService::stCensor( $value['dto']->getPreview_text(), FEATURE_BLOG ),
				'blog_post_url' => component_BlogPostView::getBlogPostUrl( $value['dto']->getId() ),
				'profile_url' => SK_Navigation::href( 'profile', array( 'profile_id' => $value['dto']->getProfile_id() ) ),
				'username' => app_Profile::username($value['dto']->getProfile_id()),
				'dto' => $value['dto']
			 );
		}        

		$Layout->assign('pop_bp_list', $pop_assign_list);

        $Layout->assign('menu_array',
                array(
                    array( 'label' => SK_Language::text('txt.index_latest'), 'active' => 1, 'href' => $this->auto_id.'latest', 'class' => 'latest' ),
                    array( 'label' => SK_Language::text('txt.index_top_rated'), 'active' => 0, 'href' => $this->auto_id.'popular', 'class' => 'popular' )
                )
        );

		return parent::render( $Layout );
	}
}