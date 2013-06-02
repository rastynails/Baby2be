<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 05, 2008
 * 
 */

class component_TagEdit extends SK_Component
{
	/**
	 * @var integer
	 */
	private $entity_id;
	
	/**
	 * @var app_TagService
	 */
	private $tag_service;
	
	/**
	 * @var array
	 */
	private $feature;
	
	/**
	 * Class constructor
	 *
	 */
	public function __construct( $params )
	{
		if( empty( $params ) || !isset( $params['entity_id'] ) || !isset( $params['feature'] ) || (int)$params['entity_id'] <= 0 || !app_Features::isAvailable(17))
		{
			$this->annul();
			return;
		}
		
		parent::__construct( 'tag_edit' );
		
		$this->tag_service = app_TagService::newInstance( $params['feature'] );
		
		if( $this->tag_service === null || !SK_HttpUser::is_authenticated() )
		{
			$this->annul();
			return;
		}
		
		$this->entity_id = (int)$params['entity_id'];
		$this->feature = $params['feature'];
	}
	
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_FrontendHandler $Frontend
	 */
    public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('TagEdit');
		$this->frontend_handler->construct();

	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$Layout->assign( 'tags_cmp', new component_TagManageList( array( 'entity_id' => $this->entity_id, 'feature' => $this->feature ) ) );
		 
		 return parent::render( $Layout );	
	}
	
	
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{
		$form->getField( 'entity_id' )->setValue( $this->entity_id );
		$form->getField( 'feature' )->setValue( $this->feature );
		
		$component_var = $this->frontend_handler->auto_var();
		
		$form->frontend_handler->bind("success", "function(data) {
			this.\$form[0].reset();
			var children = this.ownerComponent.children;
			
			for (var i = 0; i < children.length; i++) {
				var child = children[i];
				if (child instanceof component_TagManageList) {
					child.reload({entity_id:data.entity_id, feature:data.feature, page:1});	
				}
			}
		}");
		
		
	}

}