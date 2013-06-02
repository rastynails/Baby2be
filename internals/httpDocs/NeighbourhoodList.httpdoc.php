<?php

class httpdoc_NeighbourhoodList extends SK_HttpDocument
{
	public $mode = 'country';
	
	private $available_locations = array();
	
	private $distance = 0;
	
	public function __construct( array $params = null )
	{
		parent::__construct('neighbourhood_list');
		
		$this->cache_lifetime = 5;
		
		$this->available_locations = app_NeighbourhoodList::getAvailableLocationLists( SK_HttpUser::profile_id() );
		
		$mode = @SK_HttpRequest::$GET['mode'];
		
		$this->distance = @SK_HttpRequest::$GET['distance'];
		
		$this->mode = $mode ? $mode : 'country';
		
		app_NeighbourhoodList::setDefaultNeighLocation($this->mode, SK_HttpUser::profile_id(), $this->distance ? intval($this->distance) : false );
		
							
	}
	
	public function prepare(SK_Layout $Layout, SK_Frontend $display_params)
	{
		
		$params = SK_HttpRequest::$GET;
		unset($params['distance']);
		$Layout->assign('current_location_url',sk_make_url(SK_HttpRequest::getDocument()->url,$params));
		parent::prepare($Layout, $display_params);
	}
	
	public function render( SK_Layout $Layout )
	{
		
		$Layout->assign('mode',$this->mode);
		$Layout->assign('neighbourhood_tabs', $this->get_tabs());
		$Layout->assign('distance', intval($this->distance));
		
		$search_unit = SK_Config::section('site')->Section('additional')->Section('profile_list')->search_distance_unit;
		$Layout->assign('search_unit', $search_unit);
		
		return parent::render($Layout);
	}
	
	
	public function get_tabs()
	{
		$tabs = array();
		foreach ( $this->available_locations as $value )
			$tabs[] = array( 
			'label' => SK_Language::section('label')->text('location_list_tab_'.$value), 
			'href' => sk_make_url(SK_HttpRequest::getDocument()->url,( $value == 'country' ? '' : 'mode='.$value )), 
			'active' => ( $this->mode == $value ) 
			);
		return $tabs;
	}
}
