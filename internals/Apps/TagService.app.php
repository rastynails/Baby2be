<?php

require_once DIR_APPS.'appAux/TagDao.php';
require_once DIR_APPS.'appAux/TagEntityDao.php';
require_once DIR_APPS.'appAux/Tag.php';
require_once DIR_APPS.'appAux/TagEntity.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: SD
 * Date: Nov 04, 2008
 * 
 * Desc: Tag Feature Service Class
 */

final class app_TagService
{
	/**
	 * @var array
	 */
	private static $classInstance;
	
	/**
	 * @var TagDao
	 */
	private $tag_dao;
	
	/**
	 * @var TagEntityDao
	 */
	private $tag_entity_dao;
	
	/**
	 * @var array
	 */
	private $configs;
	
	/**
	 * Class constructor
	 *
	 * @param string $table_name
	 */
	private function __construct( $table_name, $feature )
	{
		$this->tag_dao = TagDao::newInstance();
		$this->tag_entity_dao = new TagEntityDao( $table_name, $feature );	
		
		
		$conf = new SK_Config_Section('site');

		
		
		$this->configs['tag_limit'] = $conf->Section('additional')->Section('tags')->get('navigator_tags_count');
	}
	
	/**
	 * Returns the only (for feature) instance of the class
	 *
	 * @return app_TagService
	 */
	public static function newInstance( $feature )
	{
		switch ($feature)
		{
			case 'blog':
			case 'news':
				$table_name = TBL_BLOG_POST_TAG;
				break;
				
			case 'photo':
				$table_name = TBL_PHOTO_TAG; // TODO remove
				break;
				
			case 'video':
				$table_name = TBL_VIDEO_TAG;
				break;
				
			case 'profile':
				$table_name = TBL_PROFILE_TAG; // TODO remove
				break;

			case 'event':
				$table_name = TBL_EVENT_TAG;
				break;	
			
			case 'photo':
                $table_name = TBL_PHOTO_TAG;
                break;
				
			default:
				return null;
		}
		
		
		if ( !isset(self::$classInstance[$feature]) && self::$classInstance[$feature] === null )
			self::$classInstance[$feature] = new self( $table_name, $feature );
			
		return self::$classInstance[$feature];	
	}
	
	/**
	 * Adds and links added tags with entity
	 *
	 * @param integer $entity_id
	 * @param string $tags
	 * 
	 * @return void
	 */
	public function addTagsToEntity( $entity_id, $tags )
	{
        $addedTagEntities = array();
		$tags_array = app_TextService::stCensorArray(explode(',',$tags));

        $tags_array = array_map('strtolower', $tags_array);
        $tags_array = array_map('htmlspecialchars', $tags_array);

		foreach ( $tags_array as $key => $value )
		{
			if( trim($value) === '' ) // TODO add bad words check
			{
				unset( $tags_array[$key] );
				continue;
			}	
			$tags_array[$key] = trim(strip_tags($value));// TODO add proc (bad words etc)
		}
			
		$tags_array = array_unique( $tags_array );
		
		$tags_in_db = $this->tag_dao->findTagsByLabel( $tags_array );
		
		if( sizeof($tags_array) !== sizeof($tags_in_db) )
		{
			$tags_in_db_string_array = array();
			
			foreach( $tags_in_db as $value )
				$tags_in_db_string_array[] = $value->getLabel();	
			
			foreach( $tags_array as $value )
			{	
				if( ! in_array( $value, $tags_in_db_string_array ) )
				{
					$new_tag = new Tag( $value );
					$this->tag_dao->saveOrUpdate( $new_tag );
					$tags_in_db[] = $new_tag;		
				}
			}
		}
		
		$entity_tags = $this->tag_entity_dao->findEntityTags( $entity_id );
		
		$entity_tag_ids = array();
		
		foreach ( $entity_tags as $value )
		{
			$entity_tag_ids[] = $value['dto']->getTag_id();
		}
		
		foreach ( $tags_in_db as $value )
		{
			if( in_array( $value->getId(), $entity_tag_ids ) )
			{
				continue;
			}
			$new_tag_entity = new TagEntity( $value->getId(), $entity_id );
			$this->tag_entity_dao->saveOrUpdate( $new_tag_entity );
            $addedTagEntities[] = $new_tag_entity;
		}

        return $addedTagEntities;
	}
	
	/**
	 * Unlinks tag from entity
	 *
	 * @param integer $entityId
	 * @param integer $tag_id
	 */
	public function unlinkTag( $entityId, $tag_id )
	{
		$this->tag_entity_dao->deleteTagEntryByEntityIdAndTagId( $entityId, $tag_id );	
	}
	
	
	/**
	 * Returns entity tags with label
	 *
	 * @param integer $entity_id
	 * 
	 * @return array
	 */
	public function findEntityTags( $entity_id )
	{
		return $this->tag_entity_dao->findEntityTags( $entity_id );
	}
	
	
	/**
	 * Unlinks all tags from entity
	 *
	 * @param integer $entity_id
	 */
	public function unlinkAllTags( $entity_id )
	{
		$this->tag_entity_dao->deleteTagEntriesForEntity( $entity_id );
	}
	
	
	/**
	 * Returns all entityIds linked to tag
	 *
	 * @param string $label
	 * 
	 * @return array
	 */
	public function findEntityIdsForTag( $label )
	{
        $label = strtolower($label);

		$page = ( !isset($page ) || (int)$page <= 0  ) ? 1 : (int)$page;
		
		$first = ( $page - 1 ) * (int)$this->configs['entity_items_on_page'];
		$count = (int)$this->configs['entity_items_on_page'];
		
		$tag = $this->tag_dao->findTagByLabel( trim( $label ) );

		if( $tag === null )
			return array();
			
		$dto_array = $this->tag_entity_dao->findEntriesByTagId( $tag->getId(), $first, $count );
		
		if( empty( $dto_array ) )
			return array();
		
		$return_array = array();
		
		foreach ( $dto_array as $value ) 
		{
			$return_array[] = $value->getEntity_id();
		}
		
		return $return_array;	
	}

	
	/**
	 * Returns most popular tags for entity
	 *
	 * @param integer $limit
	 * @return array
	 */
	public function findMostPopularTags( $limit = null )
	{
		return $this->tag_entity_dao->findMostPopularTags($this->configs['tag_limit']);
	}
	
	
	/**
	 * Returns tag_id by tag_label
	 *
	 * @param string $tag
	 * @return Tag
	 */
	public function findTagByLabel( $tag )
	{
        $tag = strtolower($tag);

		return $this->tag_dao->findTagByLabel( $tag );
	}
	
	/* Static Interface for third part users */
	
	
	/**
	 * Adds and links added tags with entity 
	 *
	 * @param integer $entity_id
	 * @param integer $tags
	 * @param integer $feature
	 * @return boolean
	 */
	public static function stAddEntityTags( $entity_id, $tags, $feature )
	{
		$service = self::newInstance( $feature );
		
		if( $service === null )
			return false;

		$service->addTagsToEntity( $entity_id, $tags );
		
		return true;
	}
	
	
	/**
	 * Unlinks all tags from entity item
	 *
	 * @param string $feature ( blog | photo | video | profile )
	 * 
	 * @return boolean
	 */
	public static function stUnlinkAllTags( $feature, $entity_id )
	{
		$service = self::newInstance( $feature );
		
		if( $service === null )
			return false;
			
		$service->unlinkAllTags( $entity_id );
		
		return true;
	}
	
	
	/**
	 * Returns entity Ids for tag label
	 *
	 * @param string $feature ( blog | photo | video | profile )
	 * 
	 * @return array
	 */
	public static function stFindEntityIdsForTag( $feature, $label )
	{
		$service = self::newInstance( $feature );
		
		if( $service === null )
			return false;
			
		return $service->findEntityIdsForTag( $label );
	}
	
	
	/**
	 * Adds tags to entity item
	 *
	 * @param string $feature
	 * @param integer $entity_id
	 * @param integer $tags
	 * @return boolean
	 */
	public static function addEntityTags( $feature, $entity_id, $tags )
	{
		$service = self::newInstance( $feature );
		
		if( $service === null )
			return false;
			
		$service->addTagsToEntity( $entity_id, $tags );
		
		return true;
	}
	
}