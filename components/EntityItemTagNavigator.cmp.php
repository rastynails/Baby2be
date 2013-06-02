<?php

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 25, 2008
 *
 */

final class component_EntityItemTagNavigator extends SK_Component
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
	 * @var integer
	 */
	private $entity_id;

	/**
	 * Class
	 *
	 * @param string $feature ( blog | event | photo | video | profile )
	 * @param string $url
	 * @param integer $tag_limit
	 */
	public function __construct( $feature, $url, $entity_id, $tag_limit = null )
	{
		parent::__construct( 'entity_item_tag_navigator' );

		$this->tag_service = app_TagService::newInstance( $feature );

		if( $this->tag_service === null || !app_Features::isAvailable(17))
		{
			$this->annul();
		}

		$this->feature = $feature;
		$this->url = $url;
		$this->tag_limit = $tag_limit;
		$this->entity_id = $entity_id;
	}


	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$tags =  $this->tag_service->findEntityTags( $this->entity_id );

		if( empty( $tags ) )
		{
            $Layout->assign( 'no_tags', SK_Language::text('components.entity_item_tag_navigator.no_tags') );
			return parent::render( $Layout );
		}

		foreach ( $tags as $key => $value )
		{
			$tags[$key]['url'] = $this->url. ( strstr( $this->url, '?' ) ? '&' : '?' ).'tag='.urlencode($value['label']);
		}

		$Layout->assign( 'tags', $tags );

		return parent::render( $Layout );
	}
}