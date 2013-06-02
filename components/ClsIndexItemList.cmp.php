<?php

class component_ClsIndexItemList extends SK_Component
{

	/**
	 * @var string
	 */
	private $entity;
	
	/**
	 * @var array
	 */
	private $menu_items;
	
	public function __construct( array $params = null )
	{
	    if ( !app_Features::isAvailable(45) )
        {
            $this->annul();
        }
        
		parent::__construct('cls_index_item_list');
		
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
		$langs  = SK_Language::section('components.cls');
		
		$this->menu_items =  array( array(
									'href'	=> 'javascript://',
									'label'	=> $langs->text('label_latest'),
									'active'=> true ,
									'type'  => 'latest',
		                             'class' => 'latest'
									 ),
							  array(
									'href'	=> 'javascript://',
									'label'	=> $langs->text('label_ending_soon'),
									'active'=> false,
							  		'type'	=> 'ending_soon',
							  		'class' => 'ending_soon'		
							  ));
		$view_all_url = array( 
			'latest' => SK_Navigation::href( 'classifieds_item_list', array('item'=>$this->entity, 'type'=>'latest') ),
			'ending_soon' => SK_Navigation::href( 'classifieds_item_list', array('item'=>$this->entity, 'type'=>'ending_soon') )
		);
		
		$handler = new SK_ComponentFrontendHandler('ClsIndexItemList');
		$handler->construct( $this->menu_items, $view_all_url );
		
		$this->frontend_handler = $handler;
		
		return parent::prepare( $Layout, $Frontend );
	}
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render( SK_Layout $Layout )
	{
		$items_list['latest'] = app_ClassifiedsItemService::stGetLatestItems( $this->entity );
		$items_list['ending_soon'] = app_ClassifiedsItemService::stGetEndingSoonItems( $this->entity );
		
		$Layout->assign('entity', $this->entity);
		$Layout->assign_by_ref('items_list', $items_list);
		$Layout->assign('menu_items', $this->menu_items);		

		return parent::render($Layout);
	}

}
