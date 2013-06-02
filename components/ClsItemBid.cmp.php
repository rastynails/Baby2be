<?php

class component_ClsItemBid extends SK_Component
{	
	/**
	 * @var int
	 */
	private $item_id;
	
	/**
	 * @var string
	 */
	private $item_entity;	
	
	/**
	 * @var string
	 */
	private $currency;		
	
	public function __construct( array $params = null )
	{
		$this->item_id = $params['entity_id'];
		$this->item_entity = $params['entity'];
		$this->currency = $params['currency'];

		parent::__construct('cls_item_bid');
	}
	
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{	
		$handler = new SK_ComponentFrontendHandler('ClsItemBid');
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
		$Layout->assign( 'entity', $this->item_entity );
		$Layout->assign( 'bids_count', app_ClassifiedsBidService::stGetItemBidsCount($this->item_id) );
		$Layout->assign( 'bid_info', app_ClassifiedsBidService::stGetItemBid( $this->item_entity, $this->item_id ) );
		$Layout->assign( 'currency', $this->currency );
		
		return parent::render($Layout);
	}
}

