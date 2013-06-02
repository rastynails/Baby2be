<?php

require_once DIR_APPS . 'appAux/CommentDao.php';
require_once DIR_APPS . 'appAux/BlogDao.php';
require_once DIR_APPS . 'appAux/BlogPostImageDao.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 17, 2008
 * 
 * Desc: Blog Feature Service Class
 */
final class app_BlogService
{
    /**
     * @var BlogDao
     */
    private $blogDao;

    /**
     * @var BlogPostImageDao
     */
    private $blogPostImageDao;

    /**
     * @var array
     */
    private $configs;

    /**
     * @var BlogService
     */
    private static $classInstance;

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->blogDao = new BlogDao();
        $this->blogPostImageDao = new BlogPostImageDao();


        $this->configs = array();
        $conf = new SK_Config_Section('blogs');
        $conf_section = $conf->Section('listing');

        $conf1 = new SK_Config_Section('site');

        $conf_section1 = $conf1->Section('automode');
        
        $this->configs['index_page_blog_post_count'] = $conf_section->get('index_page_blog_post_count');
        $this->configs['profile_page_blog_post_count'] = $conf_section->get('profile_page_blog_post_count');
        $this->configs['admin_approve'] = $conf_section1->get('set_active_blog_post_on_submit');
        $this->configs['short_posts_on_page_count'] = $conf_section->get('short_posts_on_page_count');
        $this->configs['blog_view_posts_count'] = $conf_section->get('blog_view_posts_count');
        $this->configs['more_divider'] = "<!--more-->";
        $this->configs['avail_tags'] = "<center><a><b><i><u><span><font><div><ul><ol><li><h1><h2><h3><h4><h5><input><img>";
        $this->configs['news_count'] = $conf_section->get('news_count');
        $this->configs['show_pages'] = 5;
    }

    /**
     * Returns the only instance of the class
     *
     * @return app_BlogService
     */
    public static function newInstance()
    {
        if ( self::$classInstance === null )
            self::$classInstance = new self;

        return self::$classInstance;
    }

    /**
     * Returns service configs
     *
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }
    /* !Class auxilary methods devider! */

    private static $postCache = array();

    /**
     * Returns BlogPost by id
     *
     * @param integer $id
     * @return BlogPost
     */
    public function findBlogPostById( $id )
    {
        return $this->blogDao->findById($id);
    }

    /**
     * Saves BlogPost item
     *
     * @param BlogPost $blogPost
     * @return void
     */
    public function saveBlogPost( BlogPost $blogPost )
    {
        if ( $this->configs['admin_approve'] || $blogPost->getIs_news() || SK_HttpUser::isModerator() )
        {
            $blogPost->setAdmin_status(1);
        }

        $blogPost->setCreate_time_stamp(time());
        $blogPost->setPreview_text($this->getPreviewText($blogPost->getText()));
        $blogPost->setMored_text($this->getMoredText($blogPost->getText()));
        $blogPost->setTitle(htmlspecialchars($blogPost->getTitle()));

        $this->blogDao->saveOrUpdate($blogPost);

        $type = ( $blogPost->getIs_news() == 1 ) ? 'news_post_add' : 'blog_post_add';

        $userAction = new SK_UserAction($type, $blogPost->getProfile_id());
        $userAction->item = $blogPost->getId();
        $userAction->status = ( $blogPost->getAdmin_status() == 1 && $blogPost->getProfile_status() == 1 ) ? 'active' : 'approval';
        $userAction->unique = $blogPost->getId();

        app_UserActivities::trace_action($userAction);

        if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
        {
            $newsfeedDataParams = array(
                'params' => array(
                    'feature' => FEATURE_BLOG,
                    'entityType' => $type,
                    'entityId' => $blogPost->getId(),
                    'userId' => $blogPost->getProfile_id(),
                    'status' => ( $blogPost->getAdmin_status() == 1 && $blogPost->getProfile_status() == 1 ) ? 'active' : 'approval'
                )
            );
            app_Newsfeed::newInstance()->action($newsfeedDataParams);
        }

        $this->blogPostImageDao->setImagePostId($blogPost->getId(), SK_HttpUser::profile_id());
    }

    /**
     * Updates BlogPost items
     *
     * @param BlogPost $post
     * @return void
     */
    public function updateBlogPost( BlogPost $post )
    {
        if ( $post->getId() === null )
            return;

        $post->setUpdate_time_stamp(time());
        $post->setPreview_text($this->getPreviewText($post->getText()));
        $post->setMored_text($this->getMoredText($post->getText()));
        $post->setTitle(htmlspecialchars($post->getTitle()));
        
        if ($post->getAdmin_status() == 1 && $post->getProfile_status() == 1)
        {
            $type = ( $post->getIs_news() == 1 ) ? 'news_post_add' : 'blog_post_add';
            $userAction = app_UserActivities::getWhere(" `type`='{$type}' AND `actor_id`=".$post->getProfile_id()." AND `item`=".$post->getId()." ");
            app_UserActivities::setStatus((int)$userAction[0]['skadate_user_activity_id'], 'active');
        }        
        
        $this->blogDao->saveOrUpdate($post);
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function findIndexPageBlogs( $count = null )
    {

        $count = ( $count === null ? $this->configs['index_page_blog_post_count'] : (int) $count );

        return $this->blogDao->findLastCommonMainBlogPosts($count);
    }

    /**
     * Enter description here...
     *
     * @param integer $profile_id
     * @return array
     */
    public function findLastProfileBlogPosts( $profile_id )
    {
        return $this->blogDao->findLastUserBlogPosts($profile_id, $this->configs['profile_page_blog_post_count']);
    }

    /**
     * Returns posts count for profile page
     *
     * @param integer $profile_id
     * @return integer
     */
    public function findLastProfileBlogPostsCount( $profile_id )
    {
        return $this->blogDao->findLastUserBlogPostsCount($profile_id);
    }

    /**
     * Returns user blog posts
     *
     * @param integer $profile_id
     * @param integer $first
     * @param integer $count
     * @param string $view_mode
     * @param string $order
     * @return array
     */
    public function findUserBlogPosts( $profile_id, $page, $view_mode = 'user', $order = 'date' )
    {
        $first = ( $page - 1 ) * (int) $this->configs['short_posts_on_page_count'];
        $count = (int) $this->configs['short_posts_on_page_count'];
        return $this->blogDao->findUserBlogPosts($profile_id, $first, $count, $view_mode, $order);
    }

    /**
     * Returns user blog posts for blog view
     *
     * @param integer $profile_id
     * @param integer $first
     * @param integer $count
     * @param string $view_mode
     * @param string $order
     * @return array
     */
    public function findUserBlogPostsForBlog( $profile_id, $page, $view_mode = 'user', $order = 'date' )
    {
        $first = ( $page - 1 ) * (int) $this->configs['blog_view_posts_count'];
        $count = (int) $this->configs['blog_view_posts_count'];
        return $this->blogDao->findUserBlogPosts($profile_id, $first, $count, $view_mode, $order);
    }

    /**
     * Returns user blog posts with comments count
     *
     * @param integer $profile_id
     * @param integer $first
     * @param integer $count
     * @param string $view_mode
     * @param string $order
     * @return array
     */
    public function findUserBlogPostsWithCC( $profile_id, $page, $view_mode = 'user', $order = 'date' )
    {
        $first = ( $page - 1 ) * (int) $this->configs['short_posts_on_page_count'];
        $count = (int) $this->configs['short_posts_on_page_count'];
        return $this->blogDao->findUserBlogPostsWithCC($profile_id, $first, $count, $view_mode, $order);
    }

    /**
     * Returns user blog posts count
     *
     * @param integer $profile_id
     * @param integer $view_mode
     * @return integer
     */
    public function findUserBlogPostsCount( $profile_id, $view_mode = 'user' )
    {
        return $this->blogDao->findUserBlogPostsCount($profile_id, $view_mode);
    }

    /**
     * Increments blog post view count
     *
     * @param BlogPost $post
     * @return void
     */
    public function incrementBlogPostViewCount( BlogPost $post )
    {
        if ( !isset($_SESSION['blog_post_increment']) )
            $_SESSION['blog_post_increment'] = array();

        elseif ( in_array($post->getId(), $_SESSION['blog_post_increment']) )
            return;

        $post->setView_count(( $post->getView_count() + 1));

        $this->updateBlogPost($post);

        $_SESSION['blog_post_increment'][] = $post->getId();
    }

    /**
     * Gets blog post abstract between <-more-> tags
     *
     * @param string $text
     * @param integer $page
     * @return string
     */
    public function getBlogPostPageText( $post_text, $page = 1 )
    {
        $text_array = explode($this->configs['more_divider'], $post_text);

        $page = (int) $page;

        if ( $page < 1 || sizeof($text_array) < 3 )
            return $this->processBlogPostForView($post_text);

        if ( $page === 1 )
            return $this->processBlogPostForView($text_array[0] . $text_array[1]);
        else
            return $this->processBlogPostForView($text_array[$page]);
    }

    /**
     * Gets blog post pages count
     *
     * @param string $post_text
     * @return integer
     */
    public function getBlogPostPagesCount( $post_text )
    {
        return ( sizeof(explode($this->configs['more_divider'], $post_text)) - 1 );
    }

    /**
     * @param string $text
     * @return string
     */
    public function processBlogPostForView( $text )
    {
        return app_ProfileVideo::addEmbedWmodeParam($text);
    }

    /**
     * @param string $text
     * @return string
     */
    private function getPreviewText( $text )
    {
        return strlen($text) <= 100 ? strip_tags(trim($text)) : substr(trim(strip_tags($text)), 0, 100) . '...';
    }

    /**
     * @param string $text
     * @return string
     */
    private function getMoredText( $text )
    {
        $text_array = explode($this->configs['more_divider'], $text);

        return $text_array[0];
    }

    /**
     * Deletes blog post by id 
     * 
     * @param integer $id
     */
    public function deleteBlogPostById( $id )
    {
        $post = $this->blogDao->findById($id);

        if ( $post === null )
            return false;

        $this->deleteBlogPost($post);
        return true;
    }

    /**
     * Deletes blog post
     *
     * @param BlogPost $post
     */
    public function deleteBlogPost( BlogPost $post )
    {        
        app_RateService::stDeleteEntityItemScores(FEATURE_BLOG, $post->getId());
        app_TagService::stUnlinkAllTags(FEATURE_BLOG, $post->getId());

        if ( $post->getIs_news() == 1 )
        {
            app_CommentService::stDeleteEntityComments(FEATURE_BLOG, $post->getId(), ENTITY_TYPE_NEWS_POST_ADD);
            
            app_UserActivities::deleteActivities($post->getId(), 'news_post_add');
            app_UserActivities::deleteActivities($post->getId(), 'news_post_comment');

            app_Newsfeed::newInstance()->removeAction('news_post_add', $post->getId());
        }
        else
        {
            app_CommentService::stDeleteEntityComments(FEATURE_BLOG, $post->getId(), ENTITY_TYPE_BLOG_POST_ADD);
            app_UserActivities::deleteActivities($post->getId(), 'blog_post_add');
            app_UserActivities::deleteActivities($post->getId(), 'blog_post_comment');

            app_Newsfeed::newInstance()->removeAction('blog_post_add', $post->getId());
        }

        $this->blogDao->deleteById($post->getId());
    }

    /**
     * Returns profile blogs count
     *
     * @return integer
     */
    public function getBlogsCount()
    {
        return $this->blogDao->findBlogsCount();
    }

    /**
     * Returns common active blog posts
     *
     * @param integer $page
     * @return array
     */
    public function getCommonActiveBlogPosts( $page )
    {
        $first = ( $page - 1 ) * (int) $this->configs['short_posts_on_page_count'];
        $count = (int) $this->configs['short_posts_on_page_count'];

        return $this->blogDao->findCommonBlogPosts($first, $count);
    }

    /**
     * Returns common active blog posts count
     *
     * @return unknown
     */
    public function getCommonActiveBlogPostsCount()
    {
        return (int) $this->blogDao->findCommonBlogPostsCount();
    }

    /**
     * Returns blog posts for tag_id
     *
     * @param integer $tag_id
     * @param integer $page
     * @return array
     */
    public function findBlogPostsByTagId( $tag_id, $page )
    {
        $first = ( $page - 1 ) * (int) $this->configs['short_posts_on_page_count'];
        $count = (int) $this->configs['short_posts_on_page_count'];

        return $this->blogDao->findBlogPostsByTagId($tag_id, $first, $count);
    }

    /**
     * Returns posts count for tag_id
     *
     * @param integer $tag_id
     * @return integer
     */
    public function findPostsCountForTagId( $tag_id )
    {
        return $this->blogDao->findBlogPostsByTagIdCount($tag_id);
    }

    /**
     * Returns posts for period
     *
     * @param integer $page
     * @param integer $year
     * @param integer $month
     * @return array
     */
    public function findPostsForPeriod( $page, $year, $month = null )
    {
        //TODO
        return array();
    }

    /**
     * Returns posts count for period
     *
     * @param integer $year
     * @param integer $month
     * @return integer
     */
    public function findPostsCountForPeriod( $year, $month = null )
    {
        //TODO
        return 0;
    }

    /**
     * Returns next profile post
     *
     * @param integer $profile_id
     * @param date $date
     * @return BlogPost|null
     */
    public function findNextProfilePost( $profile_id, $date )
    {
        return $this->blogDao->findNextPost($profile_id, $date);
    }

    /**
     * Returns prev profile post
     *
     * @param integer $profile_id
     * @param integer $date
     * @return BlogPost|null
     */
    public function findPrevProfilePost( $profile_id, $date )
    {
        return $this->blogDao->findPrevPost($profile_id, $date);
    }

    /**
     * Deletes profile posts
     *
     * @param integer $profile_id
     */
    public function deleteProfilePosts( $profile_id )
    {
        $posts = $this->blogDao->findAllProfilePosts($profile_id);

        foreach ( $posts as $value )
            $this->deleteBlogPost($value);
    }

    /**
     * Returns last news for index page
     *
     * @param integer $count
     * @return array
     */
    public function findLastNews( $count )
    {
        if ( is_null($count) )
            $count = $this->configs['news_count'];

        return $this->blogDao->findLastNews($count);
    }

    /**
     * Returns news posts
     *
     * @param integer $page
     * @return array
     */
    public function findNews( $page )
    {
        $first = ( $page - 1 ) * (int) $this->configs['blog_view_posts_count'];
        $count = (int) $this->configs['blog_view_posts_count'];

        return $this->blogDao->findNews($first, $count);
    }

    public function findNewsCount()
    {
        return $this->blogDao->findNewsCount();
    }

    public function findPostsForModeration( $page )
    {
        $first = ( $page - 1 ) * (int) $this->configs['blog_view_posts_count'];
        $count = (int) $this->configs['blog_view_posts_count'];

        return $this->blogDao->findPostsForModeration($first, $count);
    }

    public function findPostsForModerationCount()
    {
        return $this->blogDao->findModerationPostsCount();
    }

    /**
     * Returns blog posts for tag_id
     *
     * @param integer $tag_id
     * @param integer $page
     * @return array
     */
    public function findNewsByTagId( $tag_id, $page )
    {
        $first = ( $page - 1 ) * (int) $this->configs['short_posts_on_page_count'];
        $count = (int) $this->configs['short_posts_on_page_count'];
        return $this->blogDao->findBlogPostsByTagId($tag_id, $first, $count, 'date', 1);
    }

    /**
     * Returns posts count for tag_id
     *
     * @param integer $tag_id
     * @return integer
     */
    public function findNewsCountForTagId( $tag_id )
    {
        return $this->blogDao->findBlogPostsByTagIdCount($tag_id, 1);
    }

    public function findBlogPostImageById( $id )
    {
        return $this->blogPostImageDao->findById($id);
    }

    public function saveBlogPostImage( BlogPostImage $image )
    {
        $this->blogPostImageDao->saveOrUpdate($image);
    }

    public function findPostImages( $post_id, $profile_id )
    {
        return $this->blogPostImageDao->findPostImages($post_id, $profile_id);
    }

    /**
     * Enter description here...
     *
     * @param integer $id
     * @return BlogPostImage
     */
    public function findPostImageById( $id )
    {
        return $this->blogPostImageDao->findById($id);
    }

    public function deletePostImageById( $id )
    {
        $this->blogPostImageDao->deleteById($id);
    }

    public function findMostPopularPosts( $page, $news = 0 )
    {
        $first = ( $page - 1 ) * (int) $this->configs['short_posts_on_page_count'];
        $count = (int) $this->configs['short_posts_on_page_count'];

        return $this->blogDao->findMostPopularPosts($first, $count, $news);
    }

    public function findIndexMostPopularPosts( $count = null )
    {
        $count = ( $count === null ? $this->configs['index_page_blog_post_count'] : (int) $count );
        return $this->blogDao->findMostPopularPosts(0, $count);
    }

    public function findMostPopularPostsCount( $news = 0 )
    {
        return $this->blogDao->findCommonBlogPostsCount($news);
    }

    /** --------------------- static interface ------------------ * */
    public static function stFindLastProfileBlogPostsCount( $profile_id )
    {
        $service = self::newInstance();

        return $service->findLastProfileBlogPostsCount($profile_id);
    }

    /**
     * Deletes all profile posts
     *
     * @param integer $profile_id
     */
    public static function stDeleteProfilePosts( $profile_id )
    {
        $service = self::newInstance();

        $service->deleteProfilePosts($profile_id);
    }

    public static function stFindPostsForModerationCount()
    {
        $service = self::newInstance();

        return $service->findPostsForModerationCount();
    }

    public function findAllActivePostList()
    {
        return $this->blogDao->findAllActivePostList();
    }
}