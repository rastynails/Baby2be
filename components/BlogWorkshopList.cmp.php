<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 27, 2008
 *
 */
abstract class component_BlogWorkshopList extends SK_Component
{
    /**
     * @var app_BlogService
     */
    protected $blog_service;

    /**
     * Class constructor
     *
     */
    public function __construct( $name_space )
    {
        parent::__construct($name_space);
        $this->blog_service = app_BlogService::newInstance();
    }

    /**
     * @see SK_Component::render()
     *
     * @param SK_Layout $Layout
     */
    public function render ( SK_Layout $Layout ) 
    {
        $this->postActions();

        $blog_list = $this->getBlogPostList();

        foreach ( $blog_list as $key => $value )
        {
            $blog_list[$key]['temp']['post_url'] = component_BlogPostView::getBlogPostUrl($value['dto']->getId());
            $blog_list[$key]['temp']['delete_url'] = sk_make_url(null, array('deletePost' => $value['dto']->getId()));
            $blog_list[$key]['temp']['edit_url'] = httpdoc_BlogWorkshop::getPostEditLink($value['dto']->getId());
        }

        $feature_configs = $this->blog_service->getConfigs();

        $Layout->assign('on_page', $feature_configs['short_posts_on_page_count']);
        $Layout->assign('total', $this->getBlogPostCount());
        $Layout->assign('pages', $feature_configs['show_pages']);
        $Layout->assign('menu_array', $this->getMenuArray());
        $Layout->assign('blog_list', $blog_list);

        return parent::render($Layout);
    }

    private function postActions()
    {
        if ( isset(SK_HttpRequest::$GET['deletePost']) && (int) SK_HttpRequest::$GET['deletePost'] > 0 )
        {
            $blog_post = $this->blog_service->findBlogPostById((int) SK_HttpRequest::$GET['deletePost']);

            if ( $blog_post !== null && ( SK_HttpUser::isModerator() || $blog_post->getProfile_id() == SK_HttpUser::profile_id() ) )
            {
                $this->blog_service->deleteBlogPostById($blog_post->getId());
            }

            SK_HttpRequest::redirect(sk_make_url(null, array('deletePost' => null)));
        }
    }

    protected function getCurrentPage()
    {
        return ( isset(SK_HttpRequest::$GET['page']) && (int) SK_HttpRequest::$GET['page'] > 0) ? (int) SK_HttpRequest::$GET['page'] : 1;
    }

    abstract protected function getBlogPostList();

    abstract protected function getBlogPostCount();

    abstract protected function getMenuArray();
}