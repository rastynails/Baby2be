<?php

class component_ClsCategory extends SK_Component
{		
	/**
	 * @var int
	 */
	private $category_id;
	
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
        
		parent::__construct('cls_category');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->category_id = intval( SK_HttpRequest::$GET['cat_id'] );
		
		$this->type = ( SK_HttpRequest::$GET['type'] ) ? SK_HttpRequest::$GET['type'] : 'latest';
		
		$this->page = ( SK_HttpRequest::$GET['page'] ) ? SK_HttpRequest::$GET['page'] : 1;
		
		if ( !$this->category_id ) {
			SK_HttpRequest::redirect( 'classifieds' );
		}
	}

	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render( SK_Layout $Layout )
	{		
		$category_info = app_ClassifiedsGroupService::stGetCategoryInfo( $this->category_id );
		
		$item_list = app_ClassifiedsItemService::stGetCategoryItems( $this->category_id, $this->type, $this->page ); 		
		
		$entity_title = SK_Language::text('components.cls.'.$category_info['entity']);
		
		SK_Language::defineGlobal( array( 'entity'=>$entity_title, 'catname'=>$category_info['name']) );
		
		
		$label_langs  = SK_Language::section('components.cls');
		
		$Layout->assign('category', $category_info);
		
		$Layout->assign( 'menu_items', array(
			array(
					'href'	=> SK_Navigation::href( 'classifieds_category', array( 'cat_id'=>$this->category_id, 'type'=>'latest') ),
					'label'	=> $label_langs->text('label_latest'),
					'active'=> ( $this->type == 'latest' )
				 ),
			array(
					'href'	=> SK_Navigation::href( 'classifieds_category', array( 'cat_id'=>$this->category_id, 'type'=>'ending_soon') ),
					'label'	=> $label_langs->text('label_ending_soon'),
					'active'=> ( $this->type == 'ending_soon' )			
			)) );

		$Layout->assign( 'item_list', $item_list );
			
		$configs = new SK_Config_Section('classifieds');
		
		$Layout->assign( 'paging',array(
			'total'=> app_ClassifiedsItemService::stGetCategoryItemsCount( $this->category_id, $this->type ),
			'on_page'=> $configs->items_count_on_page,
			'pages'=> $configs->show_pages_count
		));
		
		return parent::render($Layout);
	}

}

