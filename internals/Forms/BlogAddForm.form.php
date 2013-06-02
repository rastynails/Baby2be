<?php

require_once DIR_APPS . 'appAux/BlogPost.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 08, 2008
 * 
 */
class form_BlogAddForm extends SK_Form
{

    public function __construct()
    {
        parent::__construct('blog_add');
    }

    /**
     * @see SK_Form::setup()
     *
     */
    public function setup()
    {
        $this->registerField(new fieldType_text('blog_title'));
        $this->registerField(new fieldType_text('blog_tags'));

        $text_field = new field_wysiwyg('blog_text');
        $text_field->maxlength = 1000000;

        $this->registerField($text_field);

        $this->registerField(new fieldType_checkbox('is_news'));

        $this->registerAction(new BlogPublishAction());
        $this->registerAction(new BlogAddToDraftAction());
    }
}

abstract class BlogAddFormAction extends SK_FormAction
{

    /**
     * @return app_BlogService
     */
    private function getBlogService()
    {
        return app_BlogService::newInstance();
    }

    /**
     * @return app_TagService
     */
    private function getTagSevice()
    {
        return app_TagService::newInstance('blog');
    }

    public function __construct( $key )
    {
        parent::__construct($key);
    }

    /**
     * @see SK_FormAction::process()
     *
     * @param array $data
     * @param SK_FormResponse $response
     * @param SK_Form $form
     */
    public function process( array $data, SK_FormResponse $response, SK_Form $form )
    {
        if ( !SK_HttpUser::is_authenticated() || app_Profile::suspended() )
        {
            $response->addError('Error');
            return;
        }

        // Service tracking
        $service_to_track = new SK_Service('add_blog_post', SK_httpUser::profile_id());

        if (  $service_to_track->checkPermissions() == SK_Service::SERVICE_FULL || SK_HttpUser::isModerator() )
        {
            $new_blog_post = $this->getNewBlogPost($data);

            if ( $data['is_news'] )
            {
                $new_blog_post->setIs_news(1);
            }

            $this->getBlogService()->saveBlogPost($new_blog_post);
            $response->addMessage(SK_Language::text('msg.add_blog_success'));
            $this->getTagSevice()->addTagsToEntity($new_blog_post->getId(), $data['blog_tags']);
            $response->redirect(SK_Navigation::href('manage_blog', array('tab' => $this->getRedirectTab())));

            $service_to_track->trackServiceUse();
        }
    }

    /**
     * @see SK_FormAction::setup()
     *
     * @param SK_Form $form
     */
    public function setup( SK_Form $form )
    {
        $this->process_fields = array('blog_title', 'blog_text', 'blog_tags', 'is_news');
        $this->required_fields = array('blog_title', 'blog_text');
        parent::setup($form);
    }

    /**
     * @param array $data
     * @return BlogPost
     */
    protected abstract function getNewBlogPost( array $data );

    /**
     * @return string
     */
    protected abstract function getRedirectTab();
}

final class BlogPublishAction extends BlogAddFormAction
{

    public function __construct()
    {
        parent::__construct('blog_post_publish');
    }

    /**
     * @see BlogAddFormAction::getNewBlogPost()
     *
     * @param array $data
     * @return BlogPost
     */
    protected function getNewBlogPost( array $data )
    {
        $new_blog_post = new BlogPost(SK_HttpUser::profile_id(), $data['blog_title'], $data['blog_text']);
        $new_blog_post->setProfile_status(1);
        return $new_blog_post;
    }

    /**
     * @see BlogAddFormAction::getRedirectTab()
     *
     * @return string
     */
    protected function getRedirectTab()
    {
        return 'manage';
    }
}

final class BlogAddToDraftAction extends BlogAddFormAction
{

    public function __construct()
    {
        parent::__construct('blog_post_draft');
    }

    /**
     * @see BlogAddFormAction::getNewBlogPost()
     *
     * @param array $data
     * @return BlogPost
     */
    protected function getNewBlogPost( array $data )
    {
        return new BlogPost(SK_HttpUser::profile_id(), $data['blog_title'], $data['blog_text']);
    }

    /**
     * @see BlogAddFormAction::getRedirectTab()
     *
     * @return string
     */
    protected function getRedirectTab()
    {
        return 'draft';
    }
}
