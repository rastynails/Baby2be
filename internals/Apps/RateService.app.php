<?php
require_once DIR_APPS.'appAux/RateDao.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 04, 2008
 * 
 * Desc: Rate Feature Service Class
 */

final class app_RateService
{
	/**
	 * @var RateDao
	 */
	private $rateDao;
	
	/**
	 * @var array
	 */
	private $configs;
	
	/**
	 * @var array
	 */
	private static $classInstance;
	
	/**
	 * Class constructor
	 * 
	 * @param string $feature
	 */
	private function __construct( $table_name, $rate_service )
	{
		$this->rateDao = new RateDao( $table_name );
		$this->configs['rate_service'] = $rate_service;
	}
	
	/**
	 * @return array
	 */
	public function getConfigs()
	{
		return $this->configs;
	}
	
	/**
	 * Returns the only instance of the class
	 *
	 * @param string $feature ( blog | event | photo | video | profile )
	 * 
	 * @return app_RateService
	 */
	public static function newInstance ( $feature )
	{
		switch ( $feature )
		{
			case 'news':
			case 'blog':
				$table_name = TBL_BLOG_POST_RATE;
				$rate_service = 'rate_blog_post';
				break;
				
			case 'photo':
				$table_name = TBL_PHOTO_RATE;
				$rate_service = 'rate_photo';
				break;
				
			case 'video':
				$table_name = TBL_VIDEO_RATE;
				$rate_service = 'rate_media';
				break;
                        case 'music':
				$table_name = TBL_MUSIC_RATE;
				$rate_service = 'rate_music';
				break;
				
			case 'profile':
				$table_name = TBL_PROFILE_RATE;
				$rate_service = 'rate_profiles';
				break;
				
			case 'news':
				$table_name = TBL_BLOG_POST_RATE;
				$rate_service = 'rate_news';
				break;		
				
			default:
				return null;
		}
		
		if ( !isset(self::$classInstance[$feature]) && self::$classInstance[$feature] === null )
			self::$classInstance[$feature] = new self( $table_name, $rate_service );
			
		return self::$classInstance[$feature];
	}
	
	
	/**
	 * Returns entity_ids with rate info
	 *
	 * @param integer $page
	 * 
	 * @return array
	 */
	public function findRateSortedEntityIds( $info = false )
	{
	
		$return_array = array();
		
		$result_array = $this->rateDao->findMostRatedEntityIds();
		
		foreach ( $result_array as $value )
		{
			$return_array[$value['dto']->getEntity_id()] = array( 'entity_id' => $value['dto']->getEntity_id(), 'avg_rate' => $value['avg_score'], 'rate_count' => $value['items_count'] );
		}
		
		if( $info )
		{
			return $return_array;
		}
		else 
		{
			return array_keys( $return_array );
		}
		
	}
	
	
	/**
	 * Deletes all rate entries for entity
	 *
	 * @param integer $entity_id
	 */
	public function deleteEntityItemScores( $entity_id )
	{
		$this->rateDao->deleteEntityItemScores( $entity_id );
	}
	
	
	/**
	 * Saves and Updates Rate items
	 *
	 * @param Rate $rate_item
	 */
	public function saveOrUpdateRate( Rate $rate_item )
	{
		$this->rateDao->saveOrUpdate( $rate_item );
	}
	
	
	/**
	 * Returns Rate Item by entity_id and profile_id
	 *
	 * @param integer $entity_id
	 * @param integer $profile_id
	 * 
	 * @return Rate
	 */
	public function findRateByEntityIdAndProfileId( $entity_id, $profile_id )
	{
		return $this->rateDao->getRateByEntityIdAndProfileId( $entity_id, $profile_id );
	}
	
	
	/**
	 * Saves and updates Rate items
	 *
	 * @param Rate $rate_item
	 */
	public function saveOrUpdate( Rate $rate_item )
	{
		$this->rateDao->saveOrUpdate( $rate_item );
	}
	
	
	/**
	 * Returns rate info for entity item
	 *
	 * @param integer $entity_id
	 * @return array
	 */
	public function findEntityItemRateInfo( $entity_id )
	{
		return $this->rateDao->findEntityItemRateInfo( $entity_id );
	}
	
	
	/**
	 * Returns avg rate and rates count for entities
	 *
	 * @param array $entities
	 * @return array
	 */
	public function findRatesForEntityIds( array $entities )
	{
		if( empty( $entities ) )
			return false;
			
		$result_array = $this->rateDao->findRatesForEntityIds( $entities );
		
		$return_array = array();
		
		foreach ( $result_array as $value )
		{
			$return_array[$value['dto']->getEntity_id()] = array( 'avg_rate' => $value['avg_score'], 'rate_count' => $value['items_count'] );
		}
		
		return $return_array;
	}
	
	
	/**
	 * Hardcoded method, returns entity owner Id
	 *
	 * @param integer $entity_id
	 * @param string $feature
	 */
	public function findEntityOwnerId( $entity_id, $feature )
	{
		switch ( $feature )
		{
			case 'blog':
			case 'news':
				$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `". TBL_BLOG_POST. "` WHERE `id`=? ", $entity_id );
				break;
			
			case 'event':
				$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `". TBL_EVENT. "` WHERE `id`=? ", $entity_id );
				break;
				
			case 'photo':
				$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `". TBL_PROFILE_PHOTO. "` WHERE `photo_id`=? ", $entity_id );
				break;
				
			case 'video':
				$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `". TBL_PROFILE_VIDEO. "` WHERE `video_id`=? ", $entity_id );
				
                                break;

                        case 'music':
                                $query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `". TBL_PROFILE_MUSIC. "` WHERE `music_id`=? ", $entity_id );
				
				break;
				
			default:
				return -1;
		}
		
		return (int)SK_MySQL::query( $query )->fetch_cell();
	}
	
	
	/**
	 * Deletes profile rates
	 *
	 * @param integer $profile_id
	 */
	public function deleteProfileRates( $profile_id )
	{	
		$this->rateDao->deleteProfileRates( $profile_id );
	}
	
	/*-------- Static Interface for third part users --------*/
	
	
	/**
	 * Deletes all rate entries for entity
	 *
	 * @param string $feature ( blog | photo | video | profile )
	 * 
	 * @return boolean
	 */
	public static function stDeleteEntityItemScores( $feature, $entity_id )
	{
		$service = self::newInstance( $feature );
		
		if( $service === null )
			return false;
			
		$service->deleteEntityItemScores( $entity_id );
		
		return true;
	}
	
	
	/**
	 * Returns rate sorted entity ids
	 *
	 * @param string $feature ( blog | photo | video | profile )
	 * @param integer $page
	 *  
	 * @return integer
	 */
	public static function stFindRateSortedEntityIds( $feature, $info = false )
	{
		$service = self::newInstance( $feature );
		
		if( $service === null )
			return false;
			
		return $service->findRateSortedEntityIds( $info );
	}
	
	/**
	 * Returns rate sorted entity ids with rate info
	 *
	 * @param string $feature
	 * @param integer $page
	 * 
	 * @return array
	 */
//	public static function stFindRateSortedEntityIdsWithRateInfo( $feature )
//	{
//		$service = self::newInstance( $feature );
//		
//		if( $service === null )
//			return false;
//		
//		$service->findRateSortedEntityIdsWithRateInfo();
//	}
	
	
	/**
	 * Returns avg rate and rates count for entities
	 *
	 * @param array $entities
	 * @return array
	 */
	public static function stFindRatesForEntityIds( array $entities, $feature )
	{
		$service = self::newInstance( $feature );
		
		if( $service === null )
			return false;
			
		return $service->findRatesForEntityIds( $entities );
	}
	
	
	/**
	 * Deletes all profile rates
	 *
	 * @param integer $profile_id
	 */
	public static function stDeleteProfileRates( $profile_id )
	{
		$features = array( FEATURE_BLOG, FEATURE_PHOTO, FEATURE_VIDEO, FEATURE_MUSIC );

		foreach ( $features as $value )
		{
			$service = self::newInstance( $value );
			$service->deleteProfileRates( $profile_id );
		}
	}
}