<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 25, 2008
 * 
 */

final class component_TagNavigator extends SK_Component 
{
	/**
	 * @var string
	 */
	private $feature;
	
	/**
	 * @var app_TagService
	 */
	private $tag_service;
	
	/**
	 * @var string
	 */
	private $url;
	
	/**
	 * @var integer
	 */
	private $tag_limit;
	
	/**
	 * Class
	 *
	 * @param string $feature ( blog | event | photo | video | profile )
	 * @param string $url
	 * @param integer $tag_limit
	 */
	public function __construct( $feature, $url, $tag_limit = null )
	{
		parent::__construct( 'tag_navigator' );
		
		$this->tag_service = app_TagService::newInstance( $feature );
		
		if( $this->tag_service === null || !app_Features::isAvailable(17))
		{
			$this->annul();
		}

		$this->feature = $feature;
		$this->url = $url;
		$this->tag_limit = $tag_limit;
	}
	
	
	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout )  
	{		
		$tags =  $this->tag_service->findMostPopularTags($this->tag_limit);
		
		if( empty( $tags ) )
		{
			$Layout->assign( 'no_tags', true );
			return parent::render( $Layout );
		}
		
		$min_font_size = 10;
		$max_font_size = 30;
		
		foreach ( $tags as $value )
		{
			if( !isset( $max_value ) )
			{
				$min_value = $value['items_count'];
				$max_value = $value['items_count'];
				continue;
			}
			
			if( $min_value > $value['items_count'] )
				$min_value = $value['items_count'];
				
			if( $max_value < $value['items_count'] )
				$max_value = $value['items_count'];
		}
		
		foreach ( $tags as $key => $value )
		{                
			$tags[$key]['url'] = $this->url. ( strstr( $this->url, '?' ) ? '&' : '?' ).'tag='.urlencode($value['tag_label']);
			$font_size = ( $max_value == $min_value ? 11 : 
			floor( ($value['items_count'] - $min_value)/( $max_value - $min_value ) * ( $max_font_size - $min_font_size ) + $min_font_size) );
			$tags[$key]['size'] = $font_size;
			$tags[$key]['line_height'] = $font_size + 4;
		}
		
		$Layout->assign( 'tags', $tags );
		
		return parent::render( $Layout );
	}
}