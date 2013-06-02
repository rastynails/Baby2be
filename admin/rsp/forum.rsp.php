<?php

require_once( '../../internals/Header.inc.php' );

require_once( DIR_ADMIN_INC.'fnc.auth.php' );
require_once( DIR_ADMIN_INC.'fnc.blocked_ip.php' );



rsp_Forum::construct();

class rsp_Forum
{
	private static $response = array();
	
	public static function construct()
	{
		$apply_function = (string)$_GET["apply_func"];
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
	
	public static function addNewGroup($params = null) 
	{		
		if ( !trim($params['group_name']) )
		{
			self::alert('Empty name field.');
			return ;			
		}

		//add a new forum group
		#get max order
		$query_result = SK_MySQL::query( "SELECT MAX(`order`) FROM `".TBL_FORUM_GROUP."` WHERE 1" );
		$order = $query_result->fetch_cell() + 1;
		#insert a new forum group
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_FORUM_GROUP."` (`name`,`order`) 
			VALUES('?', ?)", $params['group_name'], $order);
		SK_MySQL::query($query);
		$new_group_id = SK_MySQL::insert_id();
		if ( !$new_group_id )
		{
			self::alert( 'Failed.' );
			return ;
		}
		return array('group_id'=>$new_group_id, 'order'=>$order);				
	}
	
	public static function addNewForum($params=null)
	{
		if ( !trim($params['group_id']) ) {
			self::alert('Please, select forum group.');
			return ;			
		}
		if ( !trim($params['name']) ) {
			self::alert('Empty name field.');
			return ;			
		}
		if ( !trim($params['description']) ) {
			self::alert('Empty description field.');
			return ;			
		}
		
		//add a new forum
		#get max order
		$query = SK_MySQL::placeholder( "SELECT MAX(`order`) FROM `".TBL_FORUM."` 
			WHERE `forum_group_id`=?", $params['group_id'] );
		$query_result = SK_MySQL::query( $query );
		$order = $query_result->fetch_cell() + 1;
		#insert a new forum
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_FORUM."` (`forum_group_id`,`name`,`description`,`order`) 
			VALUES(?,'?','?',?)", $params['group_id'], $params['name'], $params['description'], $order );
		SK_MySQL::query($query);
		$new_forum_id = SK_MySQL::insert_id();
				
		if ( !$new_forum_id )
		{
			self::alert( 'Failed.' );
			return ;
		}
		
		return array('forum_id'=>$new_forum_id, 'order'=>$order);			
	}
	
	public static function editForumGroup($params=null)
	{
		if ( !trim($params['name']) ) {
			self::alert('Empty name field.');
			return ;			
		}
		
		//edit forum group
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_GROUP."` SET `name`='?' WHERE `forum_group_id`=?", 
			$params['name'], $params['group_id'] );
		SK_MySQL::query($query);
		
		return SK_MySQL::affected_rows();			
	}	
	
	public static function deleteForumGroup($params=null)
	{	
		//delete forum group
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM_GROUP."` WHERE `forum_group_id`=?", $params['group_id'] );
		SK_MySQL::query($query);
		
		//get all forums
		$query = SK_MySQL::placeholder( "SELECT `forum_id`, `order` FROM `".TBL_FORUM."` WHERE `forum_group_id`=?", $params['group_id'] );
		$query_result = SK_MySQL::query( $query );
		while ( $row = $query_result->fetch_assoc() ) {
			$return_arr[] = self::deleteForum($row);
		}
		return $return_arr;		
	}	
	
	public static function editForum($params=null)
	{
		if ( !trim($params['name']) ) {
			self::alert('Empty name field.');
			return ;			
		}
		if ( !trim($params['desc']) ) {
			self::alert('Empty description field.');
			return ;			
		}
		//edit forum
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `name`='?', `description`='?' WHERE `forum_id`=?", 
			$params['name'], $params['desc'], $params['forum_id'] );
		SK_MySQL::query($query);
		
		return SK_MySQL::affected_rows();			
	}

	public static function deleteForum($params=null)
	{		
		//get forum group_id
		$query = SK_MySQL::placeholder( "SELECT `forum_group_id` FROM `".TBL_FORUM."` WHERE `forum_id`=?", $params['forum_id'] );
		$group_id = SK_MySQL::query( $query )->fetch_cell();
		
		//delete forum
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM."` WHERE `forum_id`=?", $params['forum_id'] );
		SK_MySQL::query($query);		
		
		//sort by order
		$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_FORUM."` WHERE `forum_group_id`=?
			ORDER BY `order`", $group_id ); 
		$order = 1; 
		$query_result = SK_MySQL::query($query);
		while ($row = $query_result->fetch_assoc())
		{
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `order`=? 
				WHERE `forum_id`=?", $order++, $row['forum_id'] );
			SK_MySQL::query($query);
		}
		
		$query = SK_MySQL::placeholder( "SELECT `forum_topic_id` FROM `".TBL_FORUM_TOPIC."` 
			WHERE `forum_id`=?", $params['forum_id'] );
		$query_result = SK_MySQL::query($query);
		while ($row = $query_result->fetch_assoc()) {
			app_Forum::DeleteTopic($row['forum_topic_id']);
		}

		return $params['forum_id'];		
	}	
	
	public static function upForumGroup($params=null)
	{
		//get upper forum group
		$query = SK_MySQL::placeholder( "SELECT `forum_group_id` as `group_id`, `order` FROM `".TBL_FORUM_GROUP."` 
			WHERE `order`<? ORDER BY `order` DESC LIMIT 1", $params['order'] );
		$query_result = SK_MySQL::query($query);
		$old_group = $query_result->fetch_assoc();

		if ( !$old_group ){
			return ;
		}
		
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_GROUP."` SET `order`=? 
			WHERE `forum_group_id`=?", $params['order'], $old_group['group_id'] );
		SK_MySQL::query($query);
		
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_GROUP."` SET `order`=? 
			WHERE `forum_group_id`=?", $old_group['order'], $params['group_id'] );
		SK_MySQL::query($query);
		
		return array('group'=>$old_group, 'forum_list'=>$result_arr);	
	}
	
	public static function downForumGroup($params=null)
	{
		//get lower forum group
		$query = SK_MySQL::placeholder( "SELECT `forum_group_id` as `group_id`, `order` FROM `".TBL_FORUM_GROUP."` 
			WHERE `order`>? ORDER BY `order` LIMIT 1", $params['order'] );
		$query_result = SK_MySQL::query($query);
		$old_group = $query_result->fetch_assoc();
		
		if ( !$old_group ){
			return ;
		}
				
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_GROUP."` SET `order`=? 
			WHERE `forum_group_id`=?", $params['order'], $old_group['group_id'] );
		SK_MySQL::query($query);
		
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_GROUP."` SET `order`=? 
			WHERE `forum_group_id`=?", $old_group['order'], $params['group_id'] );
		SK_MySQL::query($query);

		return array( 'group'=>$old_group, 'forum_list'=>$result_arr);	
	}
	
	public static function upForum($params=null)
	{	
		//get forum info
		$query = SK_MySQL::placeholder( "SELECT `f`.`forum_id`, `f`.`forum_group_id`, `f`.`order`, 
			`g`.`order` as `group_order` FROM `".TBL_FORUM."` as `f` LEFT JOIN `".TBL_FORUM_GROUP."` as `g` 
			USING(`forum_group_id`) WHERE `forum_id`=?", $params['forum_id'] );
		$query_result = SK_MySQL::query($query);
		$new_forum = $query_result->fetch_assoc();
		
		//get previous forum
		$query = SK_MySQL::placeholder( "SELECT `forum_id`, `forum_group_id`, `order` FROM `".TBL_FORUM."`
			WHERE `order`<? AND `forum_group_id`=? ORDER BY `order` DESC", $new_forum['order'], $new_forum['forum_group_id'] );
		$query_result = SK_MySQL::query($query);
		$old_forum = $query_result->fetch_assoc();
		
		if ( $old_forum ) {
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `order`=? 
				WHERE `forum_id`=?", $new_forum['order'], $old_forum['forum_id'] );
			SK_MySQL::query($query);
			
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `order`=? 
				WHERE `forum_id`=?", $old_forum['order'], $new_forum['forum_id'] );
			SK_MySQL::query($query);
			
			return array('forum_id'=>$old_forum['forum_id'], 'order'=>$old_forum['order']);			
		}
		
		//get previous group
		$query = SK_MySQL::placeholder( "SELECT `forum_group_id` FROM `".TBL_FORUM_GROUP."` 
			WHERE `order`<? ORDER BY `order` DESC", $new_forum['group_order'] );
		$query_result = SK_MySQL::query($query);
		$prev_group_id = $query_result->fetch_cell();
		
		//get new order
		$query = SK_MySQL::placeholder( "SELECT MAX(`order`) FROM `".TBL_FORUM."` 
			WHERE `forum_group_id`=?", $prev_group_id );
		$new_order = SK_MySQL::query($query)->fetch_cell()+1;

		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `forum_group_id`=?, `order`=?
			WHERE `forum_id`=?", $prev_group_id, $new_order, $new_forum['forum_id'] );
		SK_MySQL::query($query);

		//update group forums order
		$order = 1;
		$forum_list = array();
		
		$forum_list[$new_forum['forum_id']] = $new_order;

		$query = SK_MySQL::placeholder( "SELECT `forum_id` FROM `".TBL_FORUM."` 
			WHERE `forum_group_id`=? ORDER BY `order`", $new_forum['forum_group_id'] );
		$result = SK_MySQL::query($query);		
		while ($forum_id = $result->fetch_cell()) {
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `order`=? 
				WHERE `forum_id`=?", $order, $forum_id );
			SK_MySQL::query($query);	

			$forum_list[$forum_id] = $order++;
		}
		
		return array('forum_group_id'=>$prev_group_id, 'forum_list'=>$forum_list);
	}
	
	public static function downForum($params=null)
	{
		//get forum info
		$query = SK_MySQL::placeholder( "SELECT `f`.`forum_id`, `f`.`forum_group_id`, `f`.`order`, 
			`g`.`order` as `group_order` FROM `".TBL_FORUM."` as `f` LEFT JOIN `".TBL_FORUM_GROUP."` as `g` 
			USING(`forum_group_id`) WHERE `forum_id`=?", $params['forum_id'] );
		$query_result = SK_MySQL::query($query);
		$new_forum = $query_result->fetch_assoc();
		
		//get next forum
		$query = SK_MySQL::placeholder( "SELECT `forum_id`, `forum_group_id`, `order` FROM `".TBL_FORUM."`
			WHERE `order`>? AND `forum_group_id`=? ORDER BY `order`", $new_forum['order'], $new_forum['forum_group_id'] );
		$query_result = SK_MySQL::query($query);
		$old_forum = $query_result->fetch_assoc();
		
		if ( $old_forum ) {
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `order`=? 
				WHERE `forum_id`=?", $new_forum['order'], $old_forum['forum_id'] );
			SK_MySQL::query($query);
			
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `order`=? 
				WHERE `forum_id`=?", $old_forum['order'], $new_forum['forum_id'] );
			SK_MySQL::query($query);
			
			return array('forum_id'=>$old_forum['forum_id'], 'order'=>$old_forum['order']);
		}
		
		//get next group
		$query = SK_MySQL::placeholder( "SELECT `forum_group_id` FROM `".TBL_FORUM_GROUP."` 
			WHERE `order`>? ORDER BY `order`", $new_forum['group_order'] );
		$query_result = SK_MySQL::query($query);
		$next_group_id = $query_result->fetch_cell();
		
		//update next group forums order
		$order = 2;
		$forum_list = array();
		
		$query = SK_MySQL::placeholder( "SELECT `forum_id` FROM `".TBL_FORUM."` 
			WHERE `forum_group_id`=? ORDER BY `order`", $next_group_id );
		$result = SK_MySQL::query($query);		
		while ($forum_id = $result->fetch_cell()) {
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `order`=? 
				WHERE `forum_id`=?", $order, $forum_id );
			SK_MySQL::query($query);
			
			$forum_list[$forum_id] = $order++;
		}
		
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `forum_group_id`=?, `order`=1
				WHERE `forum_id`=?", $next_group_id, $new_forum['forum_id'] );
			SK_MySQL::query($query);
			
		$forum_list[$new_forum['forum_id']] = 1;
			
		return array('forum_group_id'=>$next_group_id, 'forum_list'=>$forum_list);
	}
	
}