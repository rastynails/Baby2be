 <?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 29, 2008
 * 
 */

class component_BlogPostEdit extends SK_Component
{
	/**
	 * Event item to edit
	 *
	 * @var BlogPost
	 */
	private $post;
	
	
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
		parent::__construct( 'blog_post_edit' );
		
		$this->blog_service = app_BlogService::newInstance();
		
		$this->post = $this->blog_service->findBlogPostById( (int)SK_HttpRequest::$GET['postId'] );
		
		if( $this->post === null || ( $this->post->getProfile_id() != SK_HttpUser::profile_id() && !SK_HttpUser::isModerator() ) )
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
		$Layout->assign('menu_array',httpdoc_BlogWorkshop::getBlockMenuArray('edit'));
		$Layout->assign( 'tags_edit', new component_TagEdit( array( 'entity_id' => $this->post->getId(), 'feature' => 'blog' ) ) );
		
		$Layout->assign('images', new component_BlogPostImage($this->post->getId()));
		
		if( SK_HttpUser::isModerator())
		{
			$Layout->assign( 'mdr', true );
		}
			
		/*  TEEEEEEEEEEEEEEEEEEEEEEEEEEEEMP */
//		$Layout->assign( 'tag_navigator', new component_TagNavigator( 'blog', 'http://google.com' ) );
//		$Layout->assign( 'en_tag_navigator', new component_EntityItemTagNavigator( 'blog', 'http://google.com', 18 ) );
//		$Layout->assign( 'rates', new component_Rate( 18, 'blog' ) );
//		$Layout->assign( 'index_calendar', new component_BlogIndexList() );
		
		return parent::render( $Layout );
	}
	
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{	
		$form->getField('blog_title')->setValue( $this->post->getTitle() );
		$form->getField('blog_text')->setValue( $this->post->getText() );
		$form->getField( 'blog_post_id' )->setValue( $this->post->getId() );
		
		$form->getField( 'is_news' )->setValue( $this->post->getIs_news() ? true : false );
		
		if( $this->post->getProfile_status() === 0 )
		{
			$form->active_action = 'blog_post_edit_publish'; 
		}
		else
		{ 
			$form->active_action = 'blog_post_edit_draft';
		}
	}

}