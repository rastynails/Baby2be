<?php

class component_ClsSearchResultList extends SK_Component
{

	/**
	 * @var string
	 */
	private $keyword;
	
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
        
		parent::__construct('cls_search_result_list');
		
		$this->keyword = $params['keyword'];
	}

	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render( SK_Layout $Layout )
	{
        $page = SK_HttpRequest::$GET['page'] ? SK_HttpRequest::$GET['page'] : 1;
		$items = app_ClassifiedsItemService::stGetSearchResultItems($this->keyword, $page);
        $Layout->assign('item_list', $items);

        $configs = new SK_Config_Section('classifieds');

        $Layout->assign( 'paging', array(
            'total'=> app_ClassifiedsItemService::stGetSearchResultItemsCount( $this->keyword ),
            'on_page'=> $configs->items_count_on_page,
            'pages'=> $configs->show_pages_count
        ));

		return parent::render($Layout);
	}
}
