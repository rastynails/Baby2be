<?php

class component_ClsItemList extends SK_Component
{		
	/**
	 * @var string
	 */
	private $entity;
	
	/**
	 * @var string
	 */
	private $type;	
	
	/**
	 * @var int
	 */
	private $page;	
	
	
	public function __construct( array $params = null )
	{
	    if ( !app_Features::isAvailable(45) )
        {
            SK_HttpRequest::showFalsePage();
        }
        
		parent::__construct('cls_item_list');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->entity = (SK_HttpRequest::$GET['item']) ? SK_HttpRequest::$GET['item'] : 'wanted';
		
		$this->type = ( SK_HttpRequest::$GET['type'] ) ? SK_HttpRequest::$GET['type'] : 'latest';
		
		$this->page = ( SK_HttpRequest::$GET['page'] ) ? SK_HttpRequest::$GET['page'] : 1;

	}

	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render( SK_Layout $Layout )
	{		
		SK_Language::defineGlobal( array('entity'=>SK_Language::text('components.cls.'.$this->entity) ) );
		
		$item_list = app_ClassifiedsItemService::stGetItemList( $this->entity, $this->type, $this->page );	
		
		$Layout->assign( 'item_list', $item_list );
		
		$label_langs  = SK_Language::section('components.cls');
		
		$Layout->assign( 'menu_items', array(
			array(
					'href'	=> SK_Navigation::href( 'classifieds_item_list', array( 'item'=>$this->entity, 'type'=>'latest') ),
					'label'	=> $label_langs->text('label_latest'),
					'active'=> ( $this->type == 'latest' )
				 ),
			array(
					'href'	=> SK_Navigation::href( 'classifieds_item_list', array( 'item'=>$this->entity, 'type'=>'ending_soon') ),
					'label'	=> $label_langs->text('label_ending_soon'),
					'active'=> ( $this->type == 'ending_soon' )			
			)) );
		
		$configs = new SK_Config_Section('classifieds');
			
		$Layout->assign( 'paging', array(
			'total'=> app_ClassifiedsItemService::stGetItemsCount( $this->entity, $this->type ),
			'on_page'=> $configs->items_count_on_page,
			'pages'=> $configs->show_pages_count
		));
		
		return parent::render($Layout);
	}
	
}

