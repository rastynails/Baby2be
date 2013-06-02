<?php

class component_ClsIndexItems extends SK_Component
{

	/**
	 * @var string
	 */
	private $active_type;
	
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
        
		parent::__construct('cls_index_items');
		
		$this->active_type = $params['active'];
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{	
		$langs  = SK_Language::section('components.cls_index_items');
		
		$this->menu_items =  array( array(
									'href'	=> 'javascript://',
									'label'	=> $langs->text('wanted'),
									'active'=> ($this->active_type=='wanted'),
									'type'  => 'wanted'
									 ),
							  array(
									'href'	=> 'javascript://',
									'label'	=> $langs->text('offer'),
									'active'=> ($this->active_type=='offer'),
							  		'type'	=> 'offer'		
							  ));
		$view_all_url = array( 
			'wanted' => SK_Navigation::href( 'classifieds_item_list', array('item'=>'wanted') ),
			'offer' => SK_Navigation::href( 'classifieds_item_list', array('item'=>'offer') )
		);
		
		$handler = new SK_ComponentFrontendHandler('ClsIndexItems');
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
		$items_list['wanted'] = app_ClassifiedsItemService::stGetLatestItems( 'wanted' );
		$items_list['offer'] = app_ClassifiedsItemService::stGetLatestItems( 'offer' );

        $profileIdList = array();

        foreach ( $items_list['wanted'] as $item )
        {
            $profileIdList[$item['profile_id']] = true;
        }

        foreach ( $items_list['offer'] as $item )
        {
            $profileIdList[$item['profile_id']] = true;
        }

        $profileIdList = array_keys($profileIdList);

        app_Profile::getUsernamesForUsers($profileIdList);
        app_ProfilePhoto::getThumbUrlList($profileIdList);
        app_Profile::getOnlineStatusForUsers($profileIdList);

		$Layout->assign_by_ref('items_list', $items_list);
		$Layout->assign('menu_items', $this->menu_items);		

		return parent::render($Layout);
	}

}
