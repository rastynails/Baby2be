<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 15, 2008
 *
 * Desc: Blog Item View httpDoc
 */


class component_BlogPostView extends SK_Component
{
	/**
	 * @var app_BlogService
	 */
	private $blog_service;

	/**
	 * @var BlogPost
	 */
	private $post;

	/**
	 * Class constructor
	 *
	 */
	public function __construct()
	{
		parent::__construct('bpost_view');

		$this->blog_service = app_BlogService::newInstance();

		$this->post = $this->blog_service->findBlogPostById( (int)SK_HttpRequest::$GET['postId'] );

		if( $this->post === null || ( !SK_HttpUser::isModerator() && SK_HttpUser::profile_id() != $this->post->getProfile_id() && $this->post->getAdmin_status() === 0 ))
		{
			SK_HttpRequest::showFalsePage();
		}
	}

	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('BlogPostView');

		$js_array = array(
			'id' => $this->post->getId(),
			'status' => ( $this->post->getAdmin_status() ) ? 'none' : 'active',
		);

		$this->frontend_handler->construct( $js_array );
	}


	public function render( SK_Layout $Layout )
	{

		$blog_post = $this->post;

        $feature = $blog_post->getIs_news() ? FEATURE_NEWS : FEATURE_BLOG;

		$page = ( isset(SK_HttpRequest::$GET['page']) && (int)SK_HttpRequest::$GET['page'] > 0) ? (int)SK_HttpRequest::$GET['page'] : 1;

		$Layout->assign( 'blog_post', $blog_post );
		$Layout->assign( 'blog_post_text', $this->blog_service->getBlogPostPageText( $blog_post->getText(), $page ) );
		$Layout->assign( 'blog_post_pages_count', $this->blog_service->getBlogPostPagesCount( $blog_post->getText() ) );

		$Layout->assign( 'comments', new component_AddComment( $blog_post->getId(), $feature, 'blog_post_add' ) );

		$Layout->assign( 'rate', new component_Rate( array( 'entity_id' => $blog_post->getId(), 'feature' => $feature ) ) );
		$Layout->assign( 'en_tag_navigator', new component_EntityItemTagNavigator( 'blog', SK_Navigation::href( 'blogs' ), $blog_post->getId() ) );


		$prev_post = $this->blog_service->findPrevProfilePost( $blog_post->getProfile_id(), $blog_post->getCreate_time_stamp() );
		$next_post = $this->blog_service->findNextProfilePost( $blog_post->getProfile_id(), $blog_post->getCreate_time_stamp() );

		if( $prev_post !== null )
			$Layout->assign( 'prev_url', component_BlogPostView::getBlogPostUrl( $prev_post->getId() ) );

		if( $next_post !== null )
			$Layout->assign( 'next_url', component_BlogPostView::getBlogPostUrl( $next_post->getId() ) );

		$this->blog_service->incrementBlogPostViewCount( $blog_post );

		if( SK_HttpUser::isModerator() || SK_HttpUser::profile_id() == $blog_post->getProfile_id() )
		{
			$Layout->assign( 'edit_url', httpdoc_BlogWorkshop::getPostEditLink( $blog_post->getId() ) );

			if( SK_HttpUser::isModerator() && $blog_post->getAdmin_status() === 0 )
			{
				$Layout->assign( 'approve_button', true );
			}
			elseif( SK_HttpUser::isModerator() && $blog_post->getAdmin_status() === 1 )
			{
				$Layout->assign( 'block_button', true );
			}
		}

		$Layout->assign( 'reporter_id', SK_HttpUser::profile_id() );

		$service = $this->post->getIs_news() ? new SK_Service( 'view_news', SK_httpUser::profile_id() ) : new SK_Service( 'view_blog_post', SK_httpUser::profile_id() );

		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
		{
			$service->trackServiceUse();
		}
		else
		{
			$Layout->assign( 'err_message', $service->permission_message['message'] );
		}

        $Layout->assign('isNews', $blog_post->getIs_news());


		// BC

		$username = app_Profile::isProfileDeleted($blog_post->getProfile_id())
            ? SK_Language::text("label.deleted_member")
            : app_Profile::username($blog_post->getProfile_id());

		SK_Language::defineGlobal( array( 'username' => $username, 'posttitle' => app_TextService::stCensor( $blog_post->getTitle(), FEATURE_BLOG, true ) ) );

                $this->getDocumentMeta()->description = $blog_post->getPreview_text();

		if( $blog_post->getIs_news() )
		{
			SK_Navigation::removeBreadCrumbItem();
			SK_Navigation::removeBreadCrumbItem();
			SK_Navigation::removeBreadCrumbItem();

			SK_Navigation::addBreadCrumbItem( SK_Language::text( 'nav_doc_item.news' ), SK_Navigation::href( 'news' ) );
			SK_Navigation::addBreadCrumbItem( app_TextService::stCensor( $blog_post->getTitle(), FEATURE_BLOG, true ), SK_Navigation::href( 'news' ) );
		}
		else
		{
			SK_Navigation::removeBreadCrumbItem();
			SK_Navigation::removeBreadCrumbItem();

			SK_Navigation::addBreadCrumbItem( SK_Language::text( 'nav_doc_item.view_blog' ), httpdoc_BlogView::getBlogViewURL( $blog_post->getProfile_id() ) );
			SK_Navigation::addBreadCrumbItem( SK_Language::text( 'nav_doc_item.blog_post_view' ) );
		}

		return parent::render($Layout);
	}

	/**
	 * Ajax method for updating post status
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_updatePostStatus( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_BlogService::newInstance();

		$post = $service->findBlogPostById( $params->id );

		if( !SK_HttpUser::isModerator() || $post === null )
		{
			return;
		}

		$new_status = ( $params->status == 'active' ) ? 1 : 0;

		$list = app_UserActivities::getWhere("`type` IN ( 'blog_post_add', 'news_post_add', 'blog_post_comment', 'news_post_comment' ) AND item={$post->getId()}");

		foreach( $list as $a ){

			$s = ($new_status == 1)? 'active': 'approval';
			app_UserActivities::setStatus($a['skadate_user_activity_id'], $s);
		}


        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $entityType = $post->getIs_news() ? 'news_post_add' : 'blog_post_add';
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_BLOG,
                    'entityType' => $entityType,
                    'entityId' => $post->getId(),
                    'userId' => $post->getProfile_id(),
                    'status' => $params->status
                )
            );
            app_Newsfeed::newInstance()->updateStatus($newsfeedDataParams);
        }

		$post->setAdmin_status( $new_status );
		$service->updateBlogPost( $post );
	}

	public static function ajax_deletePost( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_BlogService::newInstance();

		$post = $service->findBlogPostById( $params->id );

		if( !SK_HttpUser::isModerator() || $post === null )
		{
			return;
		}

		$service->deleteBlogPost( $post );

		$handler->redirect(SK_Navigation::href('blogs'));
	}


	/**
	 * Ajax method for updating post status
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_postReport( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_BlogService::newInstance();

		$post = $service->findBlogPostById( $params->id );


	}


	/* static util methods */

	public static function getBlogPostUrl( $blog_post_id, $news = false )
	{
        if( $news )
        {
            return SK_Navigation::href( 'news_post_view', array( 'postId' => $blog_post_id ) );
        }
        
		return SK_Navigation::href( 'blog_post_view', array( 'postId' => $blog_post_id ) );
	}

}