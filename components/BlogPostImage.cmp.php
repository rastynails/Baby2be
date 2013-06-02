<?php

class component_BlogPostImage extends SK_Component
{
	private $post_id;
	
	public function __construct( $post_id = null )
	{
		
		$this->post_id = $post_id;
		
		parent::__construct( 'blog_post_image' );
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{
		$Layout->assign('image_list', new component_BlogPostImageList( array('post_id' => $this->post_id) ));
		
		return parent::render( $Layout );
	}
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('BlogPostImage');
		$this->frontend_handler = $handler;
		$handler->construct();
			
	}
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		if( !is_null($this->post_id) )
			$form->getField('post_id')->setValue($this->post_id);
		
		$form->frontend_handler->bind("success", "function(data){
			$('a.delete_file_btn').trigger('click');

			this.\$form[0].reset();
			
			var children = this.ownerComponent.children;
			
			for (var i = 0; i < children.length; i++) {
				var child = children[i];
				
				if (child instanceof component_BlogPostImageList) {
					child.reload({post_id:". ($this->post_id ? $this->post_id : 'null') ."});	
				}
			}
			
		}");
	}


}