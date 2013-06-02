<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Jan 06, 2009
 * 
 */
final class component_IndexNews extends SK_Component
{
    /**
     * @var app_BlogService
     */
    private $blog_service;
    /**
     * @var integer
     */
    private $posts_count;
    /**
     * @var string
     */
    private $view_mode = 'short';

    /**
     * Class constructor
     *
     * @param array $params
     */
    public function __construct( array $params = null )
    {
        parent::__construct('index_news');

        if ( isset($params['count']) && intval($params['count']) > 0 )
            $this->posts_count = intval($params['count']);

        if ( isset($params['view_mode']) && in_array(trim($params['view_mode']), array('mored', 'short')) )
            $this->view_mode = trim($params['view_mode']);

        $this->blog_service = app_BlogService::newInstance();

        if ( !app_Features::isAvailable(37) )
            $this->annul();
    }

    /**
     * @see SK_Component::render()
     *
     * @param SK_Layout $Layout
     */
    public function render( SK_Layout $Layout )
    {
        $blog_list = $this->blog_service->findLastNews($this->posts_count);

        if ( empty($blog_list) )
        {
            $Layout->assign('no_posts', true);
        }
        else
        {
            $profileIdList = array();

            foreach ( $blog_list as $item )
            {
                $profileIdList[] = $item->getProfile_id();
            }

            app_Profile::getUsernamesForUsers($profileIdList);
            app_ProfilePhoto::getThumbUrlList($profileIdList);
            app_Profile::getOnlineStatusForUsers($profileIdList);


            $assign_list = array();

            foreach ( $blog_list as $value )
            {
                $assign_list[] = array(
                    'title' => app_TextService::stCensor($value->getTitle(), FEATURE_BLOG),
                    'desc' => app_TextService::stCensor(( $this->view_mode == 'mored' ? $value->getMored_text() : $value->getPreview_text()), FEATURE_BLOG),
                    'blog_post_url' => component_BlogPostView::getBlogPostUrl($value->getId(), true),
                    'profile_url' => SK_Navigation::href('profile', array('profile_id' => $value->getProfile_id())),
                    'username' => app_Profile::username($value->getProfile_id()),
                    'dto' => $value
                );
            }

            $Layout->assign('bp_list', $assign_list);
        }

        $Layout->assign('is_moderator', app_Profile::isProfileModerator(SK_HttpUser::profile_id()));

        $Layout->assign('news_url', SK_Navigation::href('news'));

        return parent::render($Layout);
    }
}