<?php
require_once DIR_APPS.'appAux/BlogPost.php';
/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 29, 2008
 * 
 */

final class form_BlogEditForm extends SK_Form 
{
	
	/**
	 * @var string
	 */
	public $active_action;
	
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( )
	{
		parent::__construct( 'blog_edit' );
	}
	
	/**
	 * @see SK_Form::setup()
	 *
	 */
	public function setup() 
	{		
		$this->registerField( new fieldType_text( 'blog_title' ) );
		
		$text_field = new field_wysiwyg( 'blog_text' );
		
		$this->registerField( $text_field );
		
		$text_field->maxlength = 1000000;
		
		$this->registerField( new fieldType_hidden( 'blog_post_id' ) );
		
		$this->registerField( new fieldType_checkbox( 'is_news' ) );
		
		$this->registerAction( new BlogPublishAction() );
		$this->registerAction( new BlogAddToDraftAction() );
		$this->registerAction( new BlogSaveAction() );
	}
	
	
	/*
	 * @var app_BlogService
	 */
	private function getBlogService()
	{
		return app_BlogService::newInstance();
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
	 * Class constructor
	 *
	 * @param string $key
	 */
	public function __construct( $key )
	{
		parent::__construct( $key );
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
        if( !SK_HttpUser::is_authenticated() )
        {
            $response->addError( 'Error' );
			return;
        }

		$blog_post_to_edit = $this->getBlogService()->findBlogPostById( $data['blog_post_id'] );
		
		if( $blog_post_to_edit === null || ( $blog_post_to_edit->getProfile_id() != SK_HttpUser::profile_id() && !SK_HttpUser::isModerator()))
		{
			$response->addError( 'Error' );
			return;
		}
		
		$blog_post_to_edit->setTitle( $data['blog_title'] );
		$blog_post_to_edit->setText( $data['blog_text'] );
		
		if( $data['is_news'] )
			$blog_post_to_edit->setIs_news(1);
		else 
			$blog_post_to_edit->setIs_news(0);
		
		$this->getBlogService()->updateBlogPost( $this->setPostStatus( $blog_post_to_edit ) );
		
		$response->addMessage( SK_Language::text( 'msg.edit_blog_success' ) );
		
		if( $blog_post_to_edit->getProfile_id() != SK_HttpUser::profile_id() && SK_HttpUser::isModerator() )
		{
			$response->redirect( SK_Navigation::href('blogs') );
			return;
		}
		$response->redirect( $this->getRedirectURL( $blog_post_to_edit ) );
		
	}
	
	/**
	 * @see SK_FormAction::setup()
	 *
	 * @param SK_Form $form
	 */
	public function setup( SK_Form $form)
	{
		$this->process_fields = array( 'blog_title', 'blog_text', 'blog_post_id', 'is_news' );
		$this->required_fields = array( 'blog_title', 'blog_text', 'blog_post_id' );
		parent::setup($form);
	}
	
	/**
	 * @param array $data
	 * @return BlogPost
	 */
	protected abstract function setPostStatus( BlogPost $post );
	
	/**
	 *@return string
	 */
	protected abstract function getRedirectURL( BlogPost $post );
}

final class BlogPublishAction extends BlogAddFormAction 
{
	public function __construct()
	{
		parent::__construct( 'blog_post_edit_publish' );
	}
	
	
	/**
	 * @see BlogAddFormAction::setPostStatus()
	 *
	 * @param BlogPost $post
	 * @return BlogPost
	 */
	protected function setPostStatus ( BlogPost $post )
	{
		$post->setProfile_status( 1 );
		return $post;
	}

	
	/**
	 * @see BlogAddFormAction::getRedirectURL()
	 *
	 * @return string
	 */
	protected function getRedirectURL ( BlogPost $post )
	{
		return SK_Navigation::href( 'manage_blog', array( 'tab' => 'manage' ) );
	}

}

final class BlogAddToDraftAction extends BlogAddFormAction 
{
	public function __construct()
	{
		parent::__construct( 'blog_post_edit_draft');
	}
	
	
	/**
	 * @see BlogAddFormAction::setPostStatus()
	 *
	 * @param BlogPost $post
	 * @return BlogPost
	 */
	protected function setPostStatus ( BlogPost $post )
	{
		$post->setProfile_status( 0 );
		return $post;
	}

	
	/**
	 * @see BlogAddFormAction::getRedirectURL()
	 *
	 * @return string
	 */
	protected function getRedirectURL ( BlogPost $post )
	{
		return SK_Navigation::href( 'manage_blog', array( 'tab' => 'draft' ) );
	}
}

final class BlogSaveAction extends BlogAddFormAction 
{
	public function __construct()
	{
		parent::__construct( 'blog_post_edit_save' );
	}
	
	
	/**
	 * @see BlogAddFormAction::setPostStatus()
	 *
	 * @param BlogPost $post
	 * @return BlogPost
	 */
	protected function setPostStatus ( BlogPost $post )
	{
		return $post;
	}

	
	/**
	 * @see BlogAddFormAction::getRedirectURL()
	 *
	 * @return string
	 */
	protected function getRedirectURL ( BlogPost $post )
	{
		return SK_Navigation::href( 'manage_blog', array( 'tab' => ( $post->getProfile_status() === 1 ? 'manage' : 'draft' ) ) );
	}
}



