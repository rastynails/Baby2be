<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 24, 2008
 * 
 */

final class component_TagManageList extends SK_Component 
{
	/**
	 * @var app_TagService
	 */
	private $tag_service;
	
	/**
	 * @var integer
	 */
	private $entity_id;
	
	/**
	 * @var string
	 */
	private $feature;
	
	/**
	 * @var array
	 */
	private $tags;
	
	/**
	 * Class
	 *
	 * @param integer $entityId
	 * @param string $feature
	 */
	public function __construct( $params )
	{
		parent::__construct( 'tag_manage_list' );
		
		$this->entity_id = $params['entity_id'];
		$this->feature = $params['feature'];
		
		$this->tag_service = app_TagService::newInstance( $this->feature );
		
		if( $this->tag_service === null )
		{
			$this->annul();
			return;
		}
		
		$this->tags = $this->tag_service->findEntityTags( $this->entity_id );
		
		foreach ( $this->tags as $key => $value )
		{
			$this->tags[$key]['link_id'] = 'tag_delete_'. $value['dto']->getTag_id();
		}
	}
	
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{	
		if( empty( $this->tags ) )
		 {
		 	$Layout->assign( 'no_tags', 'No Tags to manage' );
		 	return parent::render( $Layout );
		 }
		 
		 $Layout->assign( 'tags', $this->tags );
		 
		 return parent::render( $Layout );
	}
	
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_FrontendHandler $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{	
		$this->frontend_handler = new SK_ComponentFrontendHandler('TagManageList');
		
		$js_array = array( 'entity_id' => $this->entity_id, 'feature' => $this->feature, 'items' => array() );
		
		foreach ( $this->tags as $value )
		{
				$js_array['items'][] = array( 'id'=> $value['dto']->getTag_id(), 'link_id' => $value['link_id'] );
		}
		
		$this->frontend_handler->construct( $js_array );
	}

	
	/**
	 * Ajax method for tag deleting
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_deleteTag( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_TagService::newInstance( $params->feature );
		
		if( $service === null )
		{
			$handler->error( "Error" );
			return;
		}
		
		$service->unlinkTag( $params->entity_id, $params->tag_id );

		$handler->message( SK_Language::text( 'components.tag_edit.msg_tag_delete' ) );
		
		self::reload( array( 'entity_id' => $params->entity_id, 'feature' => $params->feature ), $handler, $response );
	} 
}


