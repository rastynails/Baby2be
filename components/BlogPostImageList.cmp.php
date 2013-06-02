<?php

class component_BlogPostImageList extends SK_Component 
{
	private $post_id;
	
	/**
	 * @var app_BlogService
	 */
	private $blogService;
	
	private $imageList;
	
	public function __construct( $params = null )
	{
		if( isset($params['post_id']) && $params['post_id'] )
			$this->post_id = (int)$params['post_id'];
			
		$this->blogService = app_BlogService::newInstance();

		$this->imageList = $this->blogService->findPostImages($this->post_id, SK_HttpUser::profile_id());
		
		parent::__construct('blog_post_image_list');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		
		$js_array = array();
		
		foreach( $this->imageList as $value )
		{
			$js_array[] = array( 'url' => URL_USERFILES.$value->getFilename(), 'id' => $value->getId(), 'label' => $value->getLabel() );
		}
		
		$handler = new SK_ComponentFrontendHandler('BlogPostImageList');
		$this->frontend_handler = $handler;
		$handler->construct( $js_array, $this->post_id );
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return boolean
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$assign_array = array();
		
		foreach ($this->imageList as $value)
		{
			$assign_array[] = array('label'=>$value->getLabel(), 'id' => $value->getId());
		}
		
		$Layout->assign('images', $assign_array);
	}
	
	public static function ajax_deleteImage( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		if( !SK_HttpUser::is_authenticated() )
			return;
		
		$service = app_BlogService::newInstance();
		
		$image = $service->findPostImageById($params->id);
		
		if( $image === null )
			return;
			
		if( file_exists(DIR_USERFILES.$image->getFilename()) )
			unlink( DIR_USERFILES.$image->getFilename() );
			
		$service->deletePostImageById($image->getId());
	} 

}