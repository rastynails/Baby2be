<?php

require_once( '../../internals/Header.inc.php' );

require_once( DIR_ADMIN_INC.'fnc.auth.php' );
require_once( DIR_ADMIN_INC.'fnc.blocked_ip.php' );
require_once( DIR_ADMIN_INC.'fnc.classifieds.php' );



rsp_Classifieds::construct();

class rsp_Classifieds
{
	private static $response = array();
	
	private static $entity;	
		
	public static function construct()
	{
		$apply_function = (string)$_GET["apply_func"];
		self::$entity = (string)$_GET["entity"];
		if (!strlen($apply_function)) {
			return;
		}
		if ( !isAdminAuthed( false ) ) {
			self::exec("alert('No changes made. Demo mode.'); return false");
		} else {
			$result = call_user_func(array(__CLASS__, $apply_function), $_POST);
		
			self::$response["result"] = $result;
		}
		
		echo json_encode(self::$response);
	}
	
	private static function exec($script) {
		self::$response["script"] .= $script .";\n";
	}
	
	private static function alert($msg) {
		self::exec("alert('" . $msg . "')");
	}
	
	public static function getGroups()
	{
		$groups = app_ClassifiedsGroupService::stGetItemGroups( self::$entity, 0 );
		
		return sortGroups( $groups );
	}
	
	public static function deleteGroup($params=null)
	{	
		//delete forum group
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_CLASSIFIEDS_GROUP."` WHERE `id`=?", $params['group_id'] );
		SK_MySQL::query($query);
			
		//update child groups
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_CLASSIFIEDS_GROUP."` SET `parent_id`=0 WHERE `parent_id`=?", $params['group_id'] );
		SK_MySQL::query( $query );
		
		//get all items
		$query = SK_MySQL::placeholder( "SELECT `id` FROM `".TBL_CLASSIFIEDS_ITEM."` WHERE `group_id`=?", $params['group_id'] );
		$query_result = SK_MySQL::query( $query );
		while ( $item_id = $query_result->fetch_cell() ) {
			app_ClassifiedsItemService::stDeleteItem( $item_id );
		}
		
		return self::getGroups();		
	}	
	
	public static function addNewGroup($params=null)
	{
		$order = SK_MySQL::query( "SELECT MAX(`order`) FROM `".TBL_CLASSIFIEDS_GROUP."` WHERE 1" )->fetch_cell();
				
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM `".TBL_CLASSIFIEDS_GROUP."` 
			WHERE `name`='?' AND `parent_id`=? AND `entity`='?'", $params['name'], $params['parent_id'], self::$entity );
		$is_uniq = ( SK_MySQL::query( $query )->fetch_cell() ) ? false : true;
		
		if ( $is_uniq ) 
		{
			$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_CLASSIFIEDS_GROUP."` ( `entity`, `parent_id`, `name`, `order` )
				VALUES( '?', ?, '?', ?)", self::$entity, $params['parent_id'], $params['name'], $order+1 );
			SK_MySQL::query( $query );
		}
		
		return self::getGroups();
	}
	
	public static function moveGroup($params=null)
	{
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_CLASSIFIEDS_GROUP."` SET `parent_id`=? 
			WHERE `id` IN (".substr($params['groups'], 0, strlen($params['groups'])-1).")", $params['parent_id'] );
		SK_MySQL::query( $query );
		
		return self::getGroups();
	}
	
	public static function editGroup($params=null)
	{
		if ( !trim($params['name']) ) {
			self::alert('Empty name field.');
			return ;			
		}

		//edit forum
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_CLASSIFIEDS_GROUP."` SET `name`='?' WHERE `id`=?", 
			$params['name'], $params['group_id'] );
		SK_MySQL::query($query);
		
		return SK_MySQL::affected_rows();			
	}
	
	public static function upGroup($params=null)
	{
		$query = SK_MySQL::placeholder( "SELECT `id`, `order` FROM `".TBL_CLASSIFIEDS_GROUP."` 
			WHERE `entity`='?' AND `parent_id`=? AND `order`<? ORDER BY `order` DESC LIMIT 1", 
			self::$entity, $params['parent_id'], $params['order'] );
		$upper_group = SK_MySQL::query( $query )->fetch_assoc();
		
		//update upper group
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_CLASSIFIEDS_GROUP."` SET `order`=? 
			WHERE `id`=?", $params['order'], $upper_group['id'] );
		SK_MySQL::query( $query );
		
		//update current group
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_CLASSIFIEDS_GROUP."` SET `order`=? 
			WHERE `id`=?", $upper_group['order'], $params['group_id'] );
		SK_MySQL::query( $query );
		
		return self::getGroups();
	}
	
	public static function downGroup($params=null)
	{
		$query = SK_MySQL::placeholder( "SELECT `id`, `order` FROM `".TBL_CLASSIFIEDS_GROUP."` 
			WHERE `entity`='?' AND `parent_id`=? AND `order`>? ORDER BY `order` LIMIT 1", 
			self::$entity, $params['parent_id'], $params['order'] );
		$lower_group = SK_MySQL::query( $query )->fetch_assoc();
		
		//update lower group
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_CLASSIFIEDS_GROUP."` SET `order`=? 
			WHERE `id`=?", $params['order'], $lower_group['id'] );
		SK_MySQL::query( $query );
		
		//update current group
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_CLASSIFIEDS_GROUP."` SET `order`=? 
			WHERE `id`=?", $lower_group['order'], $params['group_id'] );
		SK_MySQL::query( $query );
		
		return self::getGroups();
	}	
	
}