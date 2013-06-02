<?php

class form_BlogPostImage extends SK_Form 
{
	public function __construct()
	{
		parent::__construct( 'blog_post_image' );
	}
	
	/**
	 * @see SK_Form::setup()
	 *
	 */
	public function setup() 
	{		
		$this->registerField( new BlogPostImageField() );
		
		$this->registerField(new fieldType_text('label'));
		
		$this->registerField(new fieldType_hidden('post_id'));
		
		$this->registerAction( new BlogPostImageUploadAction() );
	}

}

class BlogPostImageField extends fieldType_file
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * @see fieldType_file::setup()
	 *
	 * @param SK_Form $form
	 */
	public function setup ( SK_Form $form )
	{
		$this->allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');
		//$this->allowed_mime_types = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png', 'image/x-png');
		$this->max_file_size = 2048*1024;
		
		parent::setup( $form );
	}

	
	/**
	 * @see fieldType_file::preview()
	 *
	 * @param SK_TemporaryFile $tmp_file
	 */
	public function preview ( SK_TemporaryFile $tmp_file )
	{
		$file_url = $tmp_file->getURL();
		return '<div><img src="'.$file_url.'" width="100" />&nbsp;&nbsp;&nbsp;<a class="delete_file_btn" style="cursor:pointer;">Delete</a></div>';
		
	}
}

class BLogPostImageUploadAction extends SK_FormAction
{
	public function __construct()
	{
		parent::__construct( 'blog_post_image' );
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
		$service = app_BlogService::newInstance();
		
		if( isset( $data['file'] ) )
		{
			$tmp_file = new SK_TemporaryFile( $data['file'] );
			
			$conf = new SK_Config_Section( 'blogs' );
			
			$conf_section = $conf->Section( 'image' );
			
			$max_width = $conf_section->get('blog_post_image_max_width');
			
			$max_height = $conf_section->get('blog_post_image_max_height');
			
			$max_size = $conf_section->get('blog_post_image_max_size');
			
			if( $tmp_file->getSize() > (int)$max_size * 1024 )
			{
				$response->addError(SK_Language::text('blogs.error_max_image_size', array('size' => $max_size.'KB')));
				return;
			}
			
			$properties = GetImageSize( $tmp_file->getPath() );
				
			if( !$properties || $properties[0] > $max_width )
			{
				$response->addError(SK_Language::text('blogs.error_max_image_width', array('width' => $max_width.'px')));
				return;	
			}
				
			if( !$properties || $properties[1] > $max_height )
			{
				$response->addError(SK_Language::text('blogs.error_max_image_height', array('height' => $max_height.'px')));
				return;	
			}	
			
			$post_image = new BlogPostImage($data['post_id'], ( !$data['label'] ? 'image_'.rand(1,100): $data['label']) , null, SK_HttpUser::profile_id());
			
			$service->saveBlogPostImage($post_image);
			
			$file_name = 'blog_post_image_'.$post_image->getId().'.'.$tmp_file->getExtension(); 
			
			$tmp_file->move(DIR_USERFILES.$file_name);
			
			$post_image->setFilename($file_name);
			
			if( isset($data['post_id']) && intval($data['post_id']) > 0 )
				$post_image->setPost_id(intval($data['post_id']));
			
			$service->saveBlogPostImage($post_image);
		}
		
	}
}

