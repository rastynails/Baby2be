<?php
require_once DIR_APPS.'appAux/Rate.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Oct 26, 2008
 *
 */

final class component_Rate extends SK_Component
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
	 * @var array
	 */
	private $rate_array;

	/**
	 * @var boolean
	 */
	private $block;

	/**
	 * @var boolean
	 */
	private $no_rate;

	/**
	 * Class
	 *
	 * @param integer $entityId
	 * @param string $featureId
	 */
	public function __construct( $params )
	{
		parent::__construct( 'rate' );

		if( empty( $params ) || !isset( $params['entity_id'] ) || !isset( $params['feature'] ) || (int)$params['entity_id'] <= 0 )
		{
			$this->annul();
			return;
		}

		//TODO overloaded constructor XXX

		// init part
		$this->entity_id = $params['entity_id'];
		$this->feature = $params['feature'];
		$this->rate_service = app_RateService::newInstance( $params['feature'] );

		if( $this->rate_service === null )
		{
			$this->annul();
			return;
		}

		if( isset($params['block']) && $params['block'] == false )
			$this->block = false;
		else
			$this->block = true;

		//

		$this->rate_array = array();

		if( SK_HttpUser::is_authenticated() )
		{
			$rate = $this->rate_service->findRateByEntityIdAndProfileId( $this->entity_id, SK_HttpUser::profile_id() );
			$this->rate_array['auth'] = true;
		}

		$this->rate_array['score'] = ( $rate === null ? 0 : $rate->getScore() );
		$this->rate_array['feature'] = $this->feature;
		$this->rate_array['entity_id'] = $this->entity_id;
		$this->rate_array['items'] = array();

		if( !SK_HttpUser::is_authenticated() )
		{
			$this->rate_array['no_rate'] = true;
		}

		for ( $i=1; $i<=5; $i++ )
		{
			$this->rate_array['items'][] = array( 'id' => 'rate_item_'.$i );
		}

        switch ( trim($params['feature']) )
        {
            case FEATURE_PHOTO:
                $featureId = 12;
                break;

            case FEATURE_BLOG:
                $featureId = 47;
                break;

            case FEATURE_VIDEO:
                $featureId = 48;
                break;

            case FEATURE_MUSIC:
                $featureId = 49;
                break;

            case 'news':
                $featureId = 60;
                break;
            
            default:
                $featureId = null;
        }

		if( !app_Features::isAvailable( $featureId ) && $featureId !== null )
			$this->annul();

	}


	/**
	 * @see SK_Component::render()
	 *
	 * @param SK_Layout $Layout
	 */
	public function render ( SK_Layout $Layout ) 
	{
		$Layout->assign( 'rate_array', $this->rate_array );

		$configs = $this->rate_service->getConfigs();

		$service = new SK_Service( $configs['rate_service'], SK_httpUser::profile_id() );

		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
		{
			if( $this->rate_service->findEntityOwnerId( $this->entity_id, $this->feature ) == SK_HttpUser::profile_id() )
			{
				$Layout->assign( 'no_rate', SK_Language::text( 'rate.rate_entity_owner' ) );
			}
		}
		else
		{
			$Layout->assign( 'no_rate', $service->permission_message['message'] );
		}

		$Layout->assign( 'total_rate', new component_RateTotal( array( 'entity_id' => $this->entity_id, 'feature' => $this->feature ) ) );

		$Layout->assign( 'block', $this->block );

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
		$configs = $this->rate_service->getConfigs();

		$service = new SK_Service( $configs['rate_service'], SK_httpUser::profile_id() );

		$mode = true;

		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
		{
			if( $this->rate_service->findEntityOwnerId( $this->entity_id, $this->feature ) == SK_HttpUser::profile_id() )
			{
				$mode = false;
			}
		}
		else
		{
			$mode = false;
		}

		$this->frontend_handler = new SK_ComponentFrontendHandler('Rate');

		$this->frontend_handler->construct( $this->rate_array, $mode );
	}


	/**
	 * Ajax method for updating rates
	 *
	 * @param stdObject $params
	 * @param SK_ComponentFrontendHandler $handler
	 */
	public static function ajax_updateRate( $params, SK_ComponentFrontendHandler $handler, SK_AjaxResponse $response )
	{
		$service = app_RateService::newInstance( $params->feature );

		if( $service === null || !SK_HttpUser::is_authenticated() )
		{
			$handler->error( "Error" );
			return;
		}

		$rate_item = $service->findRateByEntityIdAndProfileId( $params->entity_id, SK_HttpUser::profile_id() );

		if( $rate_item === null )
		{
			$rate_item = new Rate( $params->entity_id, SK_HttpUser::profile_id(), $params->score, time() );
		}

		$rate_item->setScore( $params->score );
		$rate_item->setDate( time() );

		$service->saveOrUpdate( $rate_item );

        $configs = $service->getConfigs();

        // Service tracking
		$service_to_track = new SK_Service( $configs['rate_service'], SK_httpUser::profile_id() );
		$service_to_track->checkPermissions();
		$service_to_track->trackServiceUse();
	}
}


