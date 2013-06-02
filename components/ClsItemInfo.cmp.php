<?php

class component_ClsItemInfo extends SK_Component
{	
	/**
	 * @var int
	 */
	private $item_info;
	
	public function __construct( array $params = null )
	{
		$this->item_info = $params['item_info'];

		parent::__construct('cls_item_info');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{		
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
		$Layout->assign( 'item_info', $this->item_info );
		
		$Layout->assign('allow_payment', SK_Config::section('classifieds')->get('allow_payment'));
		
		return parent::render($Layout);
	}
}

