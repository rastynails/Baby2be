<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 26, 2008
 * 
 */

final class component_RateTotal extends SK_Component 
{
	/**
	 * @var app_RateService
	 */
	private $rate_service;
	
	/**
	 * @var integer
	 */
	private $entity_id;
	
	/**
	 * @var string
	 */
	private $feature;
	
	/**
	 * Class
	 *
	 * @param integer $entityId
	 * @param string $feature
	 */
	public function __construct( $params )
	{
		parent::__construct( 'rate_total' );
		
		$this->entity_id = $params['entity_id'];
		$this->feature = $params['feature'];
		
		$this->rate_service = app_RateService::newInstance( $params['feature'] );
		
		if( $this->rate_service === null || !isset( $params['entity_id'] ) || !isset( $params['feature'] ) )
		{
			$this->annul();
			return;
		}
	}
	
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{	
		$rate_info = $this->rate_service->findEntityItemRateInfo( $this->entity_id );
		
		
		$rate_info['width'] = (int)floor( (float)$rate_info['avg_score'] / 5 * 100);
		$rate_info['widthp'] = (int)floor( (float)$rate_info['avg_score'] / 5 * 100);
		$rate_info['avg_score'] = round( $rate_info['avg_score'], 2 );
		$Layout->assign('rate_info', $rate_info);
		
		
		return parent::render( $Layout );
	}
	/**
	 * @see SK_Component::prepare()
	 *
	 * @param SK_Layout $Layout
	 * @param SK_Frontend $Frontend
	 */
	public function prepare ( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('RateTotal');
				
		$this->frontend_handler->construct();
	}

}


