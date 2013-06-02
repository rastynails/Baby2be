<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 05, 2008
 * 
 */


class httpdoc_Blogs extends SK_HttpDocument
{
	/**
	 * @var app_BlogService
	 */
	private $blog_service;

	/**
	 * @var app_TagService
	 */
	private $tag_service;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct('blogs');
		$this->blog_service = app_BlogService::newInstance();
		$this->tag_service = app_TagService::newInstance( 'blog' );
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$page = ( isset(SK_HttpRequest::$GET['page']) && (int)SK_HttpRequest::$GET['page'] > 0) ? (int)SK_HttpRequest::$GET['page'] : 1;		
		
		$tag = ( isset( SK_HttpRequest::$GET['tag'] ) && strlen(trim( SK_HttpRequest::$GET['tag'] ) ) > 0 ) ? htmlspecialchars(trim( SK_HttpRequest::$GET['tag'] )) : false;
		
		$year = ( isset( SK_HttpRequest::$GET['year'] ) && (int)SK_HttpRequest::$GET['year'] > 2007 && (int)SK_HttpRequest::$GET['year'] < 2100 ) ? (int)SK_HttpRequest::$GET['year'] : false;
		
		$popular = ( isset( SK_HttpRequest::$GET['mode'] ) && SK_HttpRequest::$GET['mode'] == 'popular' ? true : false );
		
		if( $tag )
		{
			SK_Navigation::addBreadCrumbItem( '"'.$tag.'" tagged posts' );
			
			$tag = $this->tag_service->findTagByLabel( $tag );
			
			if( !is_null($tag) )
			{
				$blog_array = $this->blog_service->findBlogPostsByTagId( $tag->getId(), $page );
				$posts_count = $this->blog_service->findPostsCountForTagId( $tag_id );
			}
			else 
			{
				$blog_array = array();
				$posts_count = 0;
			}
		}
		elseif( $year )
		{
			$month = ( isset( SK_HttpRequest::$GET['month'] ) && in_array( (int)SK_HttpRequest::$GET['month'], array( 1,2,3,4,5,6,7,8,9,10,11,12 ) ) ) ? (int)SK_HttpRequest::$GET['month'] : null;
			
			$blog_array = $this->blog_service->findPostsForPeriod( $page, $year, $month );
			
			$posts_count = $this->blog_service->findPostsCountForPeriod( $year, $month );
		}
		elseif( $popular )
		{
			$blog_array = $this->blog_service->findMostPopularPosts($page);
			$posts_count = $this->blog_service->findMostPopularPostsCount();
		}
		else
		{
			$blog_array = $this->blog_service->getCommonActiveBlogPosts( $page );
			$posts_count = $this->blog_service->getCommonActiveBlogPostsCount();
		}

		$configs = $this->blog_service->getConfigs();

                if( empty($blog_array) )
		{
			$Layout->assign( 'no_posts', 'No posts' );
		}
                else
                {
                    $profileIdList = array();

                    foreach ( $blog_array as $item )
                    {
                        $profileIdList[] = $item['dto']->getProfile_id();
                    }

                    app_Profile::getUsernamesForUsers($profileIdList);
                    app_ProfilePhoto::getThumbUrlList($profileIdList);
                    app_Profile::getOnlineStatusForUsers($profileIdList);
                    app_Profile::checkProfileDeletedForUsers($profileIdList);

                    foreach ( $blog_array as $key => $value )
                    {
                        if ( app_Profile::isProfileDeleted($value['dto']->getProfile_id()) )
                        {
                            $blog_array[$key]['username'] = SK_Language::text("label.deleted_member");
                            $blog_array[$key]['temp']['profile_url'] = 'javascript://';
                        }
                        else
                        {
                            $blog_array[$key]['username'] = app_Profile::username($value['dto']->getProfile_id());
                            $blog_array[$key]['temp']['profile_url'] = SK_Navigation::href( 'profile', array( 'profile_id' => $value['dto']->getProfile_id() ) );
                        }

                        $blog_array[$key]['temp']['blog_post_url'] = component_BlogPostView::getBlogPostUrl( $value['dto']->getId() );
                        $blog_array[$key]['temp']['title'] = app_TextService::stCensor( $value['dto']->getTitle(), FEATURE_BLOG, true );
                        $blog_array[$key]['temp']['preview_text'] = app_TextService::stCensor( $value['dto']->getPreview_text(), FEATURE_BLOG );
                    }
                }
		
		$Layout->assign( 'bp_list', $blog_array );
		$Layout->assign( 'bp_count', $posts_count );
		$Layout->assign( 'b_count', $this->blog_service->getBlogsCount() );
		$Layout->assign( 'on_page', $configs['short_posts_on_page_count'] );
		
		$Layout->assign( 'tag_navigator', new component_TagNavigator( 'blog', SK_Navigation::href( 'blogs' ) ) );
		
		return parent::render( $Layout );
	}

}