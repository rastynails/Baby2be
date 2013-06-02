<?php
/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 29, 2008
 * 
 */

class component_ProfileComponentSelect extends SK_Component
{
	/**
	 * @var integer
	 */
	private $profile_id;
	
	/**
	 * @var app_ProfileComponentService
	 */
	private $pc_service;
	
	/**
	 * @var array
	 */
	private $cmp_list;
	
	/**
	 * Class constructor
	 *
	 * @param array $params
	 */
	public function __construct( array $params = null )
	{
		parent::__construct( 'profile_component_select' );
		$this->profile_id = $params['profile_id'];
		$this->pc_service = app_ProfileComponentService::newInstance();
		
		$this->cmp_list = $this->pc_service->findNotInViewListCMP( $this->profile_id );
	}
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('ProfileComponentSelect');
		$this->frontend_handler = $handler;
		
		$js_array = array();
		
		foreach ( $this->cmp_list as $value )
		{
			$js_array[] = array( 'id' => $value->getId() );
		}
		
		$handler->construct( array( 'cmps' => $js_array, 'url' => SK_Navigation::href( 'profile_view' ), 'is_available' => (bool)$this->cmp_list) );
	}
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 * @return unknown
	 */
	public function render ( SK_Layout $Layout ) 
	{
		
		$assign_array = array();
		
		foreach ( $this->cmp_list as $value )
		{
			$assign_array[] = array( 'dto' => $value, 'id' => $value->getId() );
		}
		
		$Layout->assign( 'cmps', ( empty($assign_array) ? false : $assign_array) );
				
		return parent::render( $Layout );
	}
	
		/**
	 * Ajax method for component adding
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_addCmp( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_ProfileComponentService::newInstance();
		
		foreach ( $params->cmps as $value )
		{
			if( (int)$value )
            {
				$service->insertFirstInSection( (int)$value, SK_HttpUser::profile_id(), 1 );
            }
		}
	} 

}