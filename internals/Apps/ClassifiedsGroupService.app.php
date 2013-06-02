<?php
require_once DIR_APPS.'appAux/ClassifiedsGroupDao.php';

/**
 * Created by Zend Studio for Eclipse
 * Project: Skadate 7
 * User: ER
 * Date: Jan 09, 2009
 * 
 * Desc: Classifieds Group Service Class
 */

final class app_ClassifiedsGroupService
{
	/**
	 * @var ClassifiedsGroupDao
	 */
	private $classifiedsGroupDao;
	
	/**
	 * @var array
	 */
	private $configs;
	
	/**
	 * @var array
	 */
	private $groups;

	/**
	 * @var array
	 */
	private static $classInstance;	
	
	/**
	 * Class constructor
	 */
	private function __construct()
	{
		$this->classifiedsGroupDao = new ClassifiedsGroupDao();
		
		$item_groups = $this->classifiedsGroupDao->findAllGroups();
		
		$groups = array();
		$group_childs = array();
		foreach ( $item_groups as $item_group )
		{
			$group = array();
			$group_id = $item_group->getId();

			$group['group_id'] = $group_id;
			$group['entity'] = $item_group->getEntity();
			$group['parent_id'] = $item_group->getParent_Id();
			$group['name'] = $item_group->getName();
			$group['order'] = $item_group->getOrder();
			
			if ( $group['parent_id'] ) {
				@$group_childs[$group['parent_id']] .= $group_id.' ';  
			}

			$groups[$group_id] = $group;
		}
		
		foreach ( $groups as $group_id=>$group ) {
			$groups[$group_id]['has_child'] = @$group_childs[$group_id];
		}

		$this->groups = $groups;		
	}
	
	/**
	 * Returns the only instance of the class
	 *
	 * @return app_ClassifiedsGroupService
	 */
	public static function newInstance ()
	{
		if ( self::$classInstance === null )
			self::$classInstance = new self();
		return self::$classInstance;
	}
	
	
	/**
	 * Returns Item Groups List
	 *
	 * @param string $entity
	 * @return array
	 */
	public function getItemGroups ( $entity, $is_approved = 1 )
	{
		$item_groups = $this->classifiedsGroupDao->findItemGroups( $entity, $is_approved );

		$groups = array();
		$group_childs = array();
		foreach ( $item_groups as $item_group )
		{
			$group = array();
			$group_id = $item_group['dto']->getId();

			$group['group_id'] = $group_id;
			$group['entity'] = $item_group['dto']->getEntity();
			$group['parent_id'] = $item_group['dto']->getParent_Id();
			$group['name'] = $item_group['dto']->getName();
			$group['order'] = $item_group['dto']->getOrder();
			$group['items_count'] = $item_group['items_count'];
			
			if ( $group['parent_id'] ) {
				@$group_childs[$group['parent_id']] .= $group_id.' ';  
			}

			$groups[$group_id] = $group;
		}
		
		foreach ( $groups as $group_id=>$group ) {
			$groups[$group_id]['has_child'] = @$group_childs[$group_id];
		}

		return $groups;
	}
	
	/**
	 * returns Classifieds groups
	 *
	 * @param int $group_id
	 * @return array
	 */
	public function getItemCategories ( $group_arr = array(), $group_id = false, $level = ' - ' )
	{
		if ( $group_id ) 
		{
			$value = $this->groups[$group_id]['group_id'];
			$option['class'] = $this->groups[$group_id]['entity'];
			$option['label'] = $level.$this->groups[$group_id]['name'];
			$group_arr[$value] = $option;
			
			$level = substr($level, 0, strlen($level)-1).'- ';
			$group_childs = explode( ' ', $this->groups[$group_id]['has_child'] );
			
			foreach ( $group_childs as $child_id )
			{
				if ( !$child_id ) {
					continue;
				}		
				if ( @$this->groups[$child_id]['has_child'] ) {
					$group_arr = $this->getItemCategories($group_arr, $child_id, $level); 
				}
				else {
					$value = $this->groups[$child_id]['group_id'];
					$option['class'] = $this->groups[$child_id]['entity'];
					$option['label'] = $level.$this->groups[$child_id]['name'];
					$group_arr[$value] = $option;
				}
			}
			
			return $group_arr;		
		}
		
		foreach ( $this->groups as $group )
		{
			if ( $group['parent_id'] ) {
				continue;		
			}
			if ( !isset($cur_entity) || $cur_entity != $group['entity'] ) {
				$cur_entity = $group['entity'];
				$option['class'] = $group['entity'];
				$option['label'] = SK_Language::text('components.cls.'.$cur_entity);
				$group_arr[$group['entity']] = $option;
			}
			
			$value = $group['group_id'];
			$option['class'] = $group['entity'];
			$option['label'] = $group['name'];
			$group_arr[$value] = $option;
			
			$group_childs = explode( ' ', @$group['has_child'] );
			foreach ( $group_childs as $child_id )
			{
				if ( !$child_id ) {
					continue;
				}				
				if ( @$this->groups[$child_id]['has_child'] ) {
					$group_arr = $this->getItemCategories($group_arr, $child_id); 
				}
				else {
					$value = $this->groups[$child_id]['group_id'];
					$option['class'] = $this->groups[$child_id]['entity'];
					$option['label'] = $level.$this->groups[$child_id]['name'];
					$group_arr[$value] = $option;
				}			
			}			
		}

		return $group_arr;
	}
	
	/**
	 * Returns category info
	 *
	 * @param int $category_id
	 */
	public function getCategoryInfo ( $category_id )
	{
		$category = $this->classifiedsGroupDao->findById( $category_id );
		$cat_info = array();
		
		if ( !$category ) {
			return $cat_info;
		}
		
		$cat_info['cat_id'] = $category->getId();
		$cat_info['entity'] = $category->getEntity();
		$cat_info['name'] = $category->getName();
		
		return $cat_info;
	}
	
	/**
	 * Returns group's entity
	 *
	 * @param int $group_id
	 * @return string
	 */
	public function getGroupEntity ( $group_id )
	{
		if ( !$group_id ) {
			return false;	
		}
		
		return $this->classifiedsGroupDao->findGroupEntity( $group_id );
	}
	
//------------------------ Static methods ---------------------//	
	
	/** Returns Item Groups List
	 *
	 * @param string $entity
	 * @return array
	 */	
	public static function stGetItemGroups ( $entity, $is_approved = 1)
	{
		$service = self::newInstance();
		
		return $service->getItemGroups( $entity, $is_approved );
	}
	
	/**
	 * returns Classifieds groups
	 *
	 * @param int $group_id
	 * @return array
	 */
	public static function stGetItemCategories()
	{
		$service = self::newInstance();
		
		return $service->getItemCategories();
	}
		
	/**
	 * Returns category info
	 *
	 * @param int $category_id
	 */
	public static function stGetCategoryInfo ( $category_id )
	{
		$service = self::newInstance();
		
		return $service->getCategoryInfo( $category_id );
	}
	
	/**
	 * Returns group's entity
	 *
	 * @param int $group_id
	 * @return string
	 */
	public static function stGetGroupEntity ( $group_id )	
	{
		$service = self::newInstance();
		
		return $service->getGroupEntity( $group_id );	
	}
	
	public static function findAllActiveCategories(){
		$service = self::newInstance();
		
		return $service->classifiedsGroupDao->findAll();
	}
}