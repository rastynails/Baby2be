<?php

class component_ClsMoveItem extends SK_Component
{	

	/**
	 * @var int
	 */
	private $item_id;
	
	/**
	 * @var int $group_id
	 */
	private $group_id;
	
	/**
	 * @var string $entity
	 */
	private $entity;
	
	public function __construct( array $params = null )
	{
		parent::__construct('cls_move_item');
		
		$this->item_id = $params['item_id'];
		$this->group_id = $params['group_id'];
		$this->entity = $params['entity'];
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{		
		$item_id = SK_HttpRequest::$GET['item_id'];

		$handler = new SK_ComponentFrontendHandler('ClsMoveItem');
		$handler->construct( $this->entity );
		
		$this->frontend_handler = $handler;
		
		return parent::prepare( $Layout, $Frontend );
	}
		
	/**
	 * @see SK_Component::handleForm()
	 *
	 * @param SK_Form $form
	 */
	public function handleForm( SK_Form $form )
	{			
		$form->getField('item_id')->setValue( $this->item_id );
		$form->getField('category')->setValues( app_ClassifiedsGroupService::stGetItemCategories() );
		
		$form->getField('category')->setValue( $this->group_id );
		
			$form->frontend_handler->bind('success', 'function( data ) {
			if (data) {
				this.ownerComponent.hideMovebox();
			}
		}');
	}

}
