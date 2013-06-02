<?php
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'class.admin_ads.php' );

/**
 * Project:    SkaDate reloaded
 * File:       class.admin_navigation.php
 *
 * @link http://www.skadate.com/
 * @package SkaDate
 * @version 4.0
 */


/**
 * Use methods of this class for controling menu items
 *
 * @package SkaDate
 * @since 4.0
 * @author Denis J
 * @link http://www.skalinks.com/ca/forum/ Technical Support forum
 */
class adminNavigation
{
	/**
	* Language prefix's ID 
	*
	* @var array
	*/
	var $lang_prefix_id;
	
	/** 
	 * Class constructor
	 *
	 * Define language prefixes
	 */
	function adminNavigation()
	{
		$this->lang_prefix_id = array
		(
			'menu_item' => 36,		
			'doc_key' => 37,
		);		
	}
	
	
	public static function GetMenuItems()
	{
		$menus = MySQL::fetchArray("SELECT * FROM `".TBL_MENU."` ORDER BY `menu_id`");
                
		$result = array();
                
		foreach ( $menus as $menu )
                {
                    $result[$menu['menu_name']]['menu'] = self::_menuItems($menu['menu_id']);
                    $result[$menu['menu_name']]['menu_id'] = $menu['menu_id'];
                }
                
		return $result;
	}
	
	/** 
	 * Returns all items and submenu of specified menu ID.
	 * If menu contains submenu, function calls by itself recursive.
	 *
	 * @param integer $menu_id
	 * @param boolean enable or disable recursive mode of array
	 * @return array
	 */
	private static function _menuItems( $menu_id ,$parent_menu_id=0, $recursive_mode = true, $curent_menu_item=0 )
	{		
		if ( $recursive_mode )
		{
			$_sql_cond = intval($curent_menu_item)?sql_placeholder('AND `menu_item_id`<>?',intval($curent_menu_item)):'';
			$query = sql_placeholder( "SELECT * FROM `".TBL_MENU_ITEM."` 
				WHERE `parent_menu_item_id`=? AND `menu_id`=? $_sql_cond ORDER BY `order`", $parent_menu_id, $menu_id);

			$_items = MySQL::fetchArray( $query );
		
			if ( !$_items )
				return array();
		}
		else
		{
			$_sql_cond = intval($curent_menu_item)?sql_placeholder('WHERE `menu_item_id`<>?',intval($curent_menu_item)):'';
			$query = sql_placeholder("SELECT * FROM `".TBL_MENU_ITEM."` 
				$_sql_cond  AND `menu_id`=? ORDER BY `order`",$menu_id);

			return MySQL::fetchArray( $query );
		}
		
			
		foreach ( $_items as $key => $_item_info )
		{
			// get memberships
			$query = sql_placeholder( "SELECT `membership_type_id` FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` 
				WHERE `menu_item_id`=?", $_item_info['menu_item_id'] );
			
			$_items[$key]['memberships'] = MySQL::fetchArray( $query, 0 );
			
			// check if submenu
			$query = sql_placeholder( "SELECT `menu_item_id` FROM `".TBL_MENU_ITEM."` 
				WHERE `parent_menu_item_id`=? ORDER BY `order`", $_item_info['menu_item_id'] );
			
			$_is_submenu = MySQL::fetchField( $query );
			
			if ( $_is_submenu )
				$_items[$key]['sub_items'] = self::_menuItems( $menu_id, $_item_info['menu_item_id'], true, $curent_menu_item );
		}
				
		return $_items;
	}

	/**
	 * Save permission for menu items for 
	 * different types of membership
	 *
	 * @param array $membersips_arr
	 *
	 * @return integer
	 */
	function SaveMenuPermissions( $membersips_arr )
	{
		
		// get all menu items
		$query = "SELECT `name`,`menu_item_id` FROM `".TBL_MENU_ITEM."`";
		$_items_arr = MySQL::fetchArray( $query );
		
		$_affected_rows = 0;
		
		foreach ( $_items_arr as $_item_key => $_item_info )
		{
			if ( !@$_POST[$_item_info['name']] )
			{
				$query = sql_placeholder( "DELETE FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` 
					WHERE `menu_item_id`=?", $_item_info['menu_item_id'] );
				
				$_affected_rows += MySQL::affectedRows( $query );
			}
			else 
			{
				foreach ( $membersips_arr as $_mem_key => $_mem_info )
				{
					if ( !@$_POST[$_item_info['name']][$_mem_info['membership_type_id']] )
					{
						$query = sql_placeholder( "DELETE FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` 
							WHERE `menu_item_id`=? AND `membership_type_id`=?", $_item_info['menu_item_id'], $_mem_info['membership_type_id'] );
						
						$_affected_rows += MySQL::affectedRows( $query );
					}					
					else 
					{
						// check if exists	
						$query = sql_placeholder( "SELECT `menu_item_id` FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` 
							WHERE `menu_item_id`=? 
							AND `membership_type_id`=?", $_item_info['menu_item_id'], $_mem_info['membership_type_id'] );
						
						$_if_exists = MySQL::fetchField( $query );
						if ( !$_if_exists )
						{
							$query = sql_placeholder( "INSERT INTO `".TBL_LINK_MENU_ITEM_MEMBERSHIP."`( `menu_item_id`, `membership_type_id` ) 
								VALUES( ?@ )", array( $_item_info['menu_item_id'], $_mem_info['membership_type_id'] )  );
							
							$_affected_rows += MySQL::affectedRows( $query );
						}
					}
				}				
			}
		}

		return $_affected_rows;
	}
	
	/**
	 * Change order for menu item
	 *
	 * @param integer $item_id
	 * @param string $move
	 *
	 * @return integer
	 */
	function changeMenuItemOrder( $item_id, $move )
	{
		$item_id = intval( $item_id );
		
		if ( !$item_id )
			return -3;			
		
		switch ( $move )
		{
			case 'up':
					// get menu item info
					$query = sql_placeholder( "SELECT * FROM `".TBL_MENU_ITEM."` WHERE `menu_item_id`=?", $item_id );
					$_item_info = MySQL::FetchRow( $query );
					
					// get next order
					$query = sql_placeholder( "SELECT `order`, `menu_item_id` FROM `".TBL_MENU_ITEM."` 
						WHERE `parent_menu_item_id`=? AND `menu_id`=? AND `order`<=? AND `menu_item_id`!=? 
						ORDER BY `order` DESC
						LIMIT 1", $_item_info['parent_menu_item_id'], $_item_info['menu_id'], $_item_info['order'], $item_id );
					
					$_next_order_info = MySQL::fetchRow( $query );
					
					if ( !$_next_order_info )
						return -2;
					
					// change order
					$query = sql_placeholder( "UPDATE `".TBL_MENU_ITEM."` SET `order`=? 
						WHERE `menu_item_id`=?", $_next_order_info['order'], $_item_info['menu_item_id'] );
					
					MySQL::fetchResource( $query );
					
					$query = sql_placeholder( "UPDATE `".TBL_MENU_ITEM."` SET `order`=? 
						WHERE `menu_item_id`=?", $_next_order_info['order'] + 1, $_next_order_info['menu_item_id'] );
					
					MySQL::fetchResource( $query );
						
				break;
			case 'down':
					
					// get menu item info
					$query = sql_placeholder( "SELECT * FROM `".TBL_MENU_ITEM."` WHERE `menu_item_id`=?", $item_id );
					
					$_item_info = MySQL::FetchRow( $query );
					
					// get next order
					$query = sql_placeholder( "SELECT `order`, `menu_item_id` FROM `".TBL_MENU_ITEM."` 
						WHERE `parent_menu_item_id`=? AND `menu_id`=? AND `order`>?
						ORDER BY `order` 
						LIMIT 1", $_item_info['parent_menu_item_id'], $_item_info['menu_id'], $_item_info['order'] );
					
					$_next_order_info = MySQL::fetchRow( $query );
					
					if ( !$_next_order_info )
						return -2;
					
					// change order
					$query = sql_placeholder( "UPDATE `".TBL_MENU_ITEM."` SET `order`=? 
						WHERE `menu_item_id`=?", $_next_order_info['order'], $_item_info['menu_item_id'] );
					
					MySQL::fetchResource( $query );
					
					$query = sql_placeholder( "UPDATE `".TBL_MENU_ITEM."` SET `order`=? 
						WHERE `menu_item_id`=?", $_item_info['order'], $_next_order_info['menu_item_id'] );
					
					MySQL::fetchResource( $query );
				break;
			default: 
				return -1;			
		}
		
		return 1;		
	}
	
	/**
	 * Returns all documents, registered in database
	 *
	 * @return array
	 */
	function GetAllDocuments()
	{
		$query = "SELECT * FROM `".TBL_DOCUMENT."` ORDER BY `url`";	
		
		return MySQL::fetchArray( $query, 'document_key' );
	}
	
	/**
	 * Create new menu item
	 *
	 * @param string $name
	 * @param string $label
	 * @param integer $parent_menu_id
	 * @param string $document_key
	 * @param array $memberships_arr
	 *
	 * @return integer
	 */
	function CreateMenuItem( $name, $label, $parent_menu_id, $document_key, $memberships_arr )
	{
			
		// check input data
		if ( !$parent_menu_id )
			return -1;
		
		$label = array_filter($label,"strlen");		

		$name = strtolower( $name );
            
        // check if this name exists
        $query = sql_placeholder( "SELECT `menu_item_id` FROM `".TBL_MENU_ITEM."` WHERE `name`=?", $name );
        if ( MySQL::fetchField( $query ) )
            return -3;
		
		if ( !count( $label ) || !strlen( trim( $name ) ) )
			return -2;
		$menu_id = ($parent_is_menu = !intval($parent_menu_id))?
											MySQL::fetchField(sql_placeholder("SELECT `menu_id` FROM `".TBL_MENU."` 
																				WHERE `menu_name`=?",$parent_menu_id))
											:
											MySQL::fetchField(sql_placeholder("SELECT `menu_id` FROM `".TBL_MENU_ITEM."` 
																				WHERE `menu_item_id`=?",$parent_menu_id));
					
				
		SK_LanguageEdit::setKey('nav_menu_item',$name,$label);
			
		// get max order
		$sql_cond = !$parent_is_menu ? sql_placeholder("AND `parent_menu_item_id`=?",$parent_menu_id):'';
		$query = sql_placeholder( "SELECT MAX(`order`) FROM `".TBL_MENU_ITEM."` 
			WHERE `menu_id`=? $sql_cond", $menu_id );
		$_max_order = MySQL::FetchField( $query ) + 1;
	
		$query = sql_placeholder( "INSERT INTO `".TBL_MENU_ITEM."`( `parent_menu_item_id`, `document_key`, `name`, `order`,`menu_id` ) 
			VALUES( ?@ )", array( ($parent_is_menu ? 0 :$parent_menu_id), $document_key, $name, $_max_order, $menu_id ) );
		
		MySQL::fetchResource( $query );
		
		$_insert_id = mysql_insert_id();
		
		if ( is_array( $memberships_arr ) )
			foreach ( $memberships_arr as $_membership_type_id => $_value )
			{
				$query = sql_placeholder( "INSERT INTO `".TBL_LINK_MENU_ITEM_MEMBERSHIP."`( `menu_item_id`, `membership_type_id` ) 
					VALUES( ?@ )", array( $_insert_id, $_membership_type_id ) );
				
				MySQL::fetchResource( $query );				
			} 
				
		return $_insert_id;
	}
	
	/**
	 * Returns all info about menu item
	 *
	 * @param integer $item_id
	 *
	 * @return array
	 */
	function GetMenuItemInfo( $item_id )
	{
		$item_id = intval( $item_id );
		
		$query = sql_placeholder( "SELECT * FROM `".TBL_MENU_ITEM."`
									LEFT JOIN `".TBL_MENU."` using(`menu_id`)
									WHERE `menu_item_id`=?", $item_id );
		
		$item_info = MySQL::FetchRow( $query );		
		
		// get memberships
		$query = sql_placeholder( "SELECT `membership_type_id` FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` 
			WHERE `menu_item_id`=?", $item_id );
		
		$item_info['memberships'] = MySQL::fetchArray( $query, 0 );
		
		return $item_info;
	}
	
	/**
	 * Saves info about menu item
	 *
	 * @param integer $item_id
	 * @param integer $parent_menu_id
	 * @param string $document_key
	 * @param array $memberships_arr
	 *
	 * @return integer
	 */
	function SaveMenuItem( $item_id, $parent_menu_id, $document_key, $memberships_arr )
	{
		$item_id = intval( $item_id );
		if ( !$item_id )
			return array();
		
		// check input data
		if ( !$memberships_arr )
			$memberships_arr = array();
		
		if( $item_id==$parent_menu_id )
			return -1;
			
		$menu_id = ($parent_is_menu = !intval($parent_menu_id))?
											MySQL::fetchField(sql_placeholder("SELECT `menu_id` FROM `".TBL_MENU."` 
																				WHERE `menu_name`=?",$parent_menu_id))
											:
											MySQL::fetchField(sql_placeholder("SELECT `menu_id` FROM `".TBL_MENU_ITEM."` 
																				WHERE `menu_item_id`=?",$parent_menu_id));
		$parent_menu_id = $parent_is_menu ? 0 : $parent_menu_id;
			
		$query = sql_placeholder( "UPDATE `".TBL_MENU_ITEM."` 
			SET `parent_menu_item_id`=?, `document_key`=?
			WHERE `menu_item_id`=?", $parent_menu_id, $document_key, $item_id );
		
		$_affected_rows = MySQL::affectedRows( $query );
		
		$_affected_rows = !$_affected_rows ? self::MenuItemChangeMenu($item_id,$menu_id) : $_affected_rows;
		
		$_all_memberships = AdminMembership::GetAllMembershipTypes();
		
		foreach ( $_all_memberships as $key => $mem_info )
		{
			if ( !array_key_exists( $mem_info['membership_type_id'], $memberships_arr ) )			
			{
				$query = sql_placeholder( "DELETE FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` 
				WHERE `menu_item_id`=?
				AND `membership_type_id`=?", $item_id, $mem_info['membership_type_id'] );
				
				$_affected_rows += MySQL::affectedRows( $query );				
			}
			else 
			{
				$query = sql_placeholder( "SELECT `menu_item_id` FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` 
					WHERE `menu_item_id`=? 
					AND `membership_type_id`=?", $item_id, $mem_info['membership_type_id'] );
				
				if ( !MySQL::fetchField( $query ) )
				{
					$query = sql_placeholder( "INSERT INTO `".TBL_LINK_MENU_ITEM_MEMBERSHIP."`( `menu_item_id`, `membership_type_id` ) 
					VALUES( ?@ )", array( $item_id, $mem_info['membership_type_id'] ) );
					
					$_affected_rows += MySQL::affectedRows( $query );					
				}
			}
		}
		
		return $_affected_rows;
	}
	
	private static function MenuItemChangeMenu($_menu_item_id, $menu_id )
	{
		
		$query = sql_placeholder("UPDATE `".TBL_MENU_ITEM."` SET `menu_id`=? WHERE `menu_item_id`=?",$menu_id, $_menu_item_id);
		$aff_rows = MySQL::affectedRows($query);
		if($aff_rows)
		{
			$query = sql_placeholder("SELECT `menu_item_id` FROM `".TBL_MENU_ITEM."` WHERE `parent_menu_item_id`=?", $_menu_item_id);
			$r = SK_MySQL::query($query);
			$subItems = array();
			
			while ( $item = $r->fetch_cell() )
			{
			    self::MenuItemChangeMenu($item, $menu_id);
			}
			
			return 1;
		}
		else
			return 0;
	}
	
	/**
	 * Deletes menu items
	 *
	 * @param array $item_arr
	 *
	 * @return integer|boolean
	 */
	function DeleteMenuItems( $item_arr )
	{
			
		$_affected_rows = 0;
		
		if ( !is_array( $item_arr ) )
			return false;

				
		foreach ( $item_arr as $item_id => $item_name)
		{
			$query = "SELECT COUNT(*) FROM " . TBL_MENU_ITEM . " WHERE  `parent_menu_item_id`=" . intval($item_id);
			
			if (SK_MySQL::query($query)->fetch_cell()) {
				$name = SK_Language::text('nav_menu_item.' . $item_name);
				throw new SK_AdminNavigationException("Menu item <i>$name</i> with sub items can not be deleted!");
			}	
			
			// delete from menu item table
			$query = sql_placeholder( "DELETE FROM `".TBL_MENU_ITEM."` 
				WHERE `menu_item_id`=?", $item_id );
			
			$_affected_rows += MySQL::affectedRows( $query );
			
			// delete mem permission tbl
			$query = sql_placeholder( "DELETE FROM `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` 
				WHERE `menu_item_id`=?", $item_id );
			
			$_affected_rows += MySQL::affectedRows( $query );
			
			SK_LanguageEdit::deleteKey('nav_menu_item',$item_name);
			
		}	
		
		return $_affected_rows;	
	}
	
	/** 
	 * Returns array with documents in tall structure.
	 * If parent document key not specified, returns tall structure
	 * of all documents.
	 *
	 * @param string $parent_document_key
	 *
	 * @return array
	 */
	function GetDocuments( $parent_document_key = '', $current_doc_key='' )
	{
		$_sql_cond = trim($current_doc_key)?sql_placeholder('AND `document_key`<>?',trim($current_doc_key)):'';
		
		$query = sql_placeholder( "SELECT * FROM `".TBL_DOCUMENT."` 
			WHERE `parent_document_key`=? $_sql_cond ORDER BY `order`", $parent_document_key );
		$_docs = MySQL::fetchArray( $query );

		if ( !$_docs )
			return array();
			
		foreach ( $_docs as $doc_key => $_document_info )
		{
			// check if subdocuments exists
			$query = sql_placeholder( "SELECT `document_key` FROM `".TBL_DOCUMENT."` 
				WHERE `parent_document_key`=?
				ORDER BY `order`
				LIMIT 1", $_document_info['document_key'] );
			
			if ( MySQL::fetchField( $query ) )
				$_docs[$doc_key]['sub_docs'] = AdminNavigation::GetDocuments( $_document_info['document_key'],  $current_doc_key );
		}	
		return $_docs;
	}
	
	/**
	 * Save permission for document
	 * Gets permission from $_POST arrray
	 * 
	 * @return integer
	 */
	function SaveDocumentsPermissions()
	{
		$_affected_rows = 0;
		
		// get all documents
		$_all_docs = $this->GetAllDocuments();
				
		foreach ( $_all_docs as $doc_info )
		{
			if ( $doc_info['status'] )
			{		
				// set permissions
				$_guest_access = ( @$_POST[$doc_info['document_key']]['guest'] ) ? 1 : 0;
				$_member_access = ( @$_POST[$doc_info['document_key']]['member'] ) ? 1 : 0;
				
				$query = "UPDATE `".TBL_DOCUMENT."` 
					SET `access_guest` = '$_guest_access', `access_member` = '$_member_access' 
					WHERE `document_key`='{$doc_info['document_key']}'";
				
				$_affected_rows += MySQL::affectedRows( $query );	
			}
			// set status
			$_status = ( $_POST['doc_access'][$doc_info['document_key']] ) ? 1: 0;
			$_affected_rows += $this->SetDocumentStatus( $doc_info['document_key'], $_status );
		}
		
		return $_affected_rows;
	}
	
	/**
	 * Sets document status. 
	 * Document can be active(1) or inactive(0)
	 * 
	 * @param string $document_key
	 * @param integer $status
	 *
	 * @return integer
	 */
	function SetDocumentStatus( $document_key, $status )
	{
		$query = sql_placeholder( "UPDATE `".TBL_DOCUMENT."` SET `status`='$status' 
			WHERE `document_key`=?", $document_key );
		
		$_affected_rows += MySQL::affectedRows( $query );
		
		return $_affected_rows;			
	}
	
	/**
	 * Deletes document from database
	 *
	 * @param array $document_arr
	 *
	 * @return integer
	 */
	function DeleteDocument( $document_arr )
	{
		

		// check input data
		if ( !is_array( $document_arr ) )
			return 0;
		
		$_affected_rows = 0;
		
		foreach ( $document_arr as $_document_key )
		{
			$query = sql_placeholder( "SELECT `base_document`,`custom` FROM `".TBL_DOCUMENT."` 
				WHERE `document_key`=?", $_document_key );
			$doc_info = MySQL::fetchField( $query );
			if ( !$doc_info['base_document'] )
			{
				$query = sql_placeholder( "DELETE FROM `".TBL_DOCUMENT."` 
					WHERE `document_key`=?", $_document_key );	
				
				$_affected_rows = MySQL::affectedRows( $query );
			
				// delete langs
				SK_LanguageEdit::deleteKey('nav_doc_item',$_document_key);
				SK_LanguageEdit::deleteKey('nav_doc_item.custom_code',$_document_key);
				SK_LanguageEdit::deleteKey('nav_doc_item.headers',$_document_key);
				SK_LanguageEdit::deleteKey('nav_doc_item.titles',$_document_key);
				SK_LanguageEdit::deleteKey('nav_doc_item.keywords',$_document_key);
				SK_LanguageEdit::deleteKey('nav_doc_item.descriptions',$_document_key);
				
				
				// delete ads from deleted document
				adminAds::deleteAdsPositionFromDocument( $_document_key );
			}
		}
		
		return $_affected_rows;
	}	
	 
	/**
	 * Change order of document
	 * Returns 1, if order changed, or error code:<br />
	 * <li>-1 undefined direct of order change ( can be only : up, down )</li>
	 * <li>-2 undefined new document order</li>
	 * <li>-3 undefined document key</li>
	 *
	 * @param string $document_key
	 * @param string $move
	 *
	 * @return integer
	 */
	function ChangeDocumentOrder( $document_key, $move )
	{
		if ( !strlen( trim( $document_key ) ) )
			return -3;			
				
		switch ( $move )
		{
			case 'up':
					// get document info
					$query = "SELECT * FROM `".TBL_DOCUMENT."` WHERE `document_key`='$document_key'";
					$doc_info = MySQL::FetchRow( $query );
					
					// get next order
					$query = "SELECT `order`, `document_key` FROM `".TBL_DOCUMENT."` 
						WHERE `parent_document_key`='{$doc_info['parent_document_key']}' AND `order`<'{$doc_info['order']}' 
						ORDER BY `order` DESC
						LIMIT 1";
					
					$next_order_info = MySQL::FetchRow( $query );
					if ( !$next_order_info )
						return -2;
					// change order
					$query = "UPDATE `".TBL_DOCUMENT."` SET `order`='{$next_order_info['order']}' 
						WHERE `document_key`='{$doc_info['document_key']}'";
					
					MySQL::fetchResource( $query );
					
					$query = "UPDATE `".TBL_DOCUMENT."` SET `order`='{$doc_info['order']}' 
						WHERE `document_key`='{$next_order_info['document_key']}'";
					
					MySQL::fetchResource( $query );
						
				break;
			case 'down':
					
					// get document info
					$query = "SELECT * FROM `".TBL_DOCUMENT."` WHERE `document_key`='$document_key'";
					$doc_info = MySQL::FetchRow( $query );
					
					// get next order
					$query = "SELECT `order`, `document_key` FROM `".TBL_DOCUMENT."` 
						WHERE `parent_document_key`='{$doc_info['parent_document_key']}' AND `order`>'{$doc_info['order']}' 
						ORDER BY `order`
						LIMIT 1";
					
					$next_order_info = MySQL::FetchRow( $query );
					if ( !$next_order_info )
						return -2;
					// change order
					$query = "UPDATE `".TBL_DOCUMENT."` SET `order`='{$next_order_info['order']}' 
						WHERE `document_key`='{$doc_info['document_key']}'";
					
					MySQL::fetchResource( $query );
					
					$query = "UPDATE `".TBL_DOCUMENT."` SET `order`='{$doc_info['order']}' 
						WHERE `document_key`='{$next_order_info['document_key']}'";
					
					MySQL::fetchResource( $query );
				break;
			default: 
				return -1;			
		}
		return 1;		
	}
	
	/**
	 * Set document status. Status can be:<br />
	 * <li> 1 - active</li>
	 * <li> 0 - inactive</li>
	 *
	 * @return integer
	 */
	function ChangeDocumentStatus( $document_key )
	{
		// check input data
		if ( !strlen( trim( $document_key ) ) )
			return -1;
		
		$query = "SELECT `status` FROM `".TBL_DOCUMENT."` WHERE `document_key`='$document_key'";
		$_current_status = MySQL::FetchField( $query );
		
		$_new_status = ( $_current_status ) ? 0 : 1;
		
		$query = "UPDATE `".TBL_DOCUMENT."` SET `status`='$_new_status' WHERE `document_key`='$document_key'";
		
		return MySQL::affectedRows( $query );
	}
	
    public static function disableDocument( $docKey )
    {
        $query = "UPDATE `".TBL_DOCUMENT."` SET `status`='0' WHERE `document_key`='$docKey'";
        
        SK_MySQL::query($query);
    } 
    
    public static function enableDocument( $docKey )
    {
        $query = "UPDATE `".TBL_DOCUMENT."` SET `status`='1' WHERE `document_key`='$docKey'";
        
        SK_MySQL::query($query);
    }
    
    public static function disableDocumentMenuItems( $docKey )
    {
        $query = "SELECT menu_item_id FROM `".TBL_MENU_ITEM."` WHERE `document_key`='$docKey'";
        $r = SK_MySQL::query($query);
        
        while ( $m = $r->fetch_cell() )
        {
            $query = "DELETE FROM " . TBL_LINK_MENU_ITEM_MEMBERSHIP . " WHERE `menu_item_id`=" . intval($m);
            SK_MySQL::query($query);            
        }
    }
    
    public static function enableDocumentMenuItems( $docKey )
    {
        $query = "SELECT menu_item_id FROM `".TBL_MENU_ITEM."` WHERE `document_key`='$docKey'";
        $r = SK_MySQL::query($query);
        
        $memberships = AdminMembership::GetAllMembershipTypes();
        
        while ( $m = $r->fetch_cell() )
        {
            foreach ($memberships as $mem)
            {
                $query = "REPLACE " . TBL_LINK_MENU_ITEM_MEMBERSHIP . " SET `menu_item_id`=" . intval($m) . ", membership_type_id=" . intval($mem['membership_type_id']);
                SK_MySQL::query($query);            
            }
        }
    }
	
	/**
	 * Get all info about document
	 *
	 * @param string $doc_key
	 *
	 * @return array
	 */
	function GetDocumentInfo( $doc_key )
	{
		if ( !strlen( trim( $doc_key ) ) )
			return array();
			
		$query = "SELECT * FROM `".TBL_DOCUMENT."` WHERE `document_key`='$doc_key'";
		
		return MySQL::fetchRow( $query );
	}
	
	/**
	 * Creates new document.
	 * Returns created document key or error code:<br />
	 * <li>-1 empty document key</li>
	 * <li>-2 empty document label</li>
	 * <li>-3 document file not found in file system</li>
	 *
	 * @param string $doc_key
	 * @param string $label
	 * @param string $parent_document_key
	 * @param string $doc_url
	 * @param integer $status
	 * @param integer $access_member
	 * @param integer $access_guest
	 *
	 * @return string|integer
	 */
	function CreateDocument( array $data )
	{ 
		$doc_key = @$data['doc_key'];
		$label = @$data['doc_label'];
		$parent_doc_key = @$data['doc_parent'];
		$doc_url = @$data['doc_url'];
		$status = @$data['status'] ? @$_POST['status'] : 0;
		$access_member = @$data['access_member'] ? @$_POST['access_member'] : 0;
		$access_guest = @$data['access_guest'] ? @$_POST['access_guest'] : 0;
		$document_type = @$data['document_type'] ? @$_POST['document_type'] : 'fixed';
		$custom_code = @$data['custom_code'] ? @$_POST['custom_code'] : '';
		$title = @$data['doc_title'];
		$header = @$data['doc_header'];
		$keywords = @$data['doc_keywords'];
		$description = @$data['doc_description'];
		
		// check input data
		if ( !strlen( trim( $doc_key ) ) )
			return -1;
			
		if ( !strlen( trim( $label[SK_Config::Section('languages')->default_lang_id] ) ) )
			return -2;
			
		if(!($is_custom = (int)($document_type == 'custom')))
		{	
			preg_match( '/^[^\?]*/i', $doc_url, $file_path );
					
			if ( !trim($file_path[0]) || !file_exists( DIR_SITE_ROOT.$file_path[0] ) )
				return -3;
		}
		else 
		{
			$query=sql_pholder('SELECT `document_key` FROM `'.TBL_DOCUMENT.'` WHERE `url`=?',$doc_url);
			if( MySQL::fetchField($query) )
				return -5;
		}
			
		if ( $status )
		{
			$status = 1;
			$access_guest = ( $access_guest ) ? 1 :0;
			$access_member = ( $access_member ) ? 1 : 0;
		}
		else 
		{
			$status = 0;			
			$access_guest = 0;
			$access_member = 0;
		}
		
		// check if document key exists
		$query = "SELECT `document_key` FROM `".TBL_DOCUMENT."` WHERE `document_key`='$doc_key'";
		if ( MySQL::fetchField( $query ) )
			return -4;
		
		// get max document order
		$query = "SELECT MAX( `order` ) FROM `".TBL_DOCUMENT."` 
			WHERE `parent_document_key`='$parent_doc_key'";	
		
		$max_order = MySQL::fetchField( $query ) + 1;
		
			
		$query = sql_placeholder( "INSERT INTO `".TBL_DOCUMENT."`
			( `document_key`, `parent_document_key`, `url`, `access_guest`, `access_member`, `status`, `order`,`custom`) 
			VALUES( ?, '$parent_doc_key', ?, '$access_guest', '$access_member', '$status', '$max_order', $is_custom )", $doc_key, $doc_url );
				
		MySQL::fetchResource( $query );
		
		if($is_custom)
			SK_LanguageEdit::setKey('nav_doc_item.custom_code',$doc_key,$custom_code);
		
		if( count($title = array_filter($label,'strlen')) )
			SK_LanguageEdit::setKey('nav_doc_item',$doc_key, $label);
			
		if( count($title = array_filter($title,'strlen')) )
			SK_LanguageEdit::setKey('nav_doc_item.titles',$doc_key, $title);
	
		if( count($title = array_filter($header,'strlen')) )	
			SK_LanguageEdit::setKey('nav_doc_item.headers',$doc_key, $header);
		
		if( count($title = array_filter($keywords,'strlen')) )
			SK_LanguageEdit::setKey('nav_doc_item.keywords',$doc_key, $keywords);
			
		if( count($title = array_filter($description,'strlen')) )
			SK_LanguageEdit::setKey('nav_doc_item.descriptions',$doc_key, $description);

		adminAds::addAdsPosToDoc($doc_key);
				
		return $doc_key;
	}
	
	/**
	 * Edits info about document.
	 * Returns mysql affected rows or error code:<br />
	 * <li>-1 empty document key</li>
	 * <li>-3 document file not found in file system</li>
	 *
	 * @param string $doc_key
	 * @param string $parent_document_key
	 * @param string $doc_url
	 * @param integer $status
	 * @param integer $access_member
	 * @param integer $access_guest
	 *
	 * @return integer
	 */
	function SaveDocument( $data )
	{
		
		$doc_key = @$data['doc_key'];
		$label = @$data['doc_label'];
		$parent_doc_key = @$data['doc_parent'];
		$doc_url = @$data['doc_url'];
		$status = @$data['status'] ? @$_POST['status'] : 0;
		$access_member = @$data['access_member'] ? @$_POST['access_member'] : 0;
		$access_guest = @$data['access_guest'] ? @$_POST['access_guest'] : 0;
		$document_type = @$data['document_type'] ? @$_POST['document_type'] : 'fixed';
		$custom_code = @$data['custom_code'] ? @$_POST['custom_code'] : array();
		$title = @$data['doc_title'];
		$header = @$data['doc_header'];
		$keywords = @$data['doc_keywords'];
		$description = @$data['doc_description'];
		// check input data
		if ( !strlen( trim( $doc_key ) ) )
			return -1;
				
		
		preg_match( '/^[^\?]*/i', $doc_url, $file_path );
				
		if(!($is_custom = (int)($document_type == 'custom')))
		{	
			preg_match( '/^[^\?]*/i', $doc_url, $file_path );
				
			if ( !trim($file_path[0]) || !file_exists( DIR_SITE_ROOT.$file_path[0] ) )
				return -3;
		}
		else 
		{
			$query=sql_pholder('SELECT `document_key` FROM `'.TBL_DOCUMENT.'` WHERE `url`=? AND `document_key`<>?',$doc_url,$doc_key);
			if( MySQL::fetchField($query) )
				return -5;
		}
			
		// get document info
		$query = "SELECT * FROM `".TBL_DOCUMENT."` WHERE `document_key`='$doc_key'";
		$old_doc_info = MySQL::FetchRow( $query );
		
		// check access
		if ( $status )
		{
			$status = 1;
			if ( $old_doc_info['status'] )
			{
				$access_guest = ( $access_guest ) ? 1 :0;
				$access_member = ( $access_member ) ? 1 : 0;
				$query_member_access = ",`access_member`='$access_member'";
				$query_guest_access = ",`access_guest`='$access_guest'";
			}		
		}
		else 
			$status = 0;			

		// main query
		$query = sql_placeholder( "UPDATE `".TBL_DOCUMENT."` SET `document_key`=?, `parent_document_key`='$parent_doc_key', 
			`url`=?, `status`='$status', `custom`=$is_custom ".$query_guest_access.$query_member_access." 
			WHERE `document_key`='$doc_key'", $doc_key, $doc_url );
			
		$result = MySQL::affectedRows( $query );	
		if($is_custom && (is_array($custom_code) && count($custom_code = array_filter($custom_code,'strlen'))) )
		{
			
			SK_LanguageEdit::setKey('nav_doc_item.custom_code',$doc_key,$custom_code);
			$result=1;
		}
		
		if( is_array($label) && count($title = array_filter($label,'strlen')) )
			SK_LanguageEdit::setKey('nav_doc_item',$doc_key, $label);
		
		if( is_array($title) && count($title = array_filter($title,'strlen')) ){
			SK_LanguageEdit::setKey('nav_doc_item.titles',$doc_key, $title);
			$result++;
		}
		if( is_array($header) && count($header = array_filter($header,'strlen')) ){	
			SK_LanguageEdit::setKey('nav_doc_item.headers',$doc_key, $header);
			$result++;
		}
		if( is_array($keywords) && count($keywords = array_filter($keywords,'strlen')) ){
			SK_LanguageEdit::setKey('nav_doc_item.keywords',$doc_key, $keywords);
			$result++;
		}
		if( is_array($description) && count($description = array_filter($description,'strlen')) ){
			SK_LanguageEdit::setKey('nav_doc_item.descriptions',$doc_key, $description);
			$result++;
		}
				
		return $result;	
	}
	
	/**
	 * Returns true if not base documents exists
	 * else false
	 *
	 * @return boolean
	 */
	function IsNotBaseDocExists()
	{
		$query = "SELECT COUNT(`document_key`) FROM `".TBL_DOCUMENT."` WHERE `base_document`='0'";

		return ( MySQL::fetchField( $query ) ) ? true : false;	
	}
	
	/**
	 * Returns true if not base menu items exists
	 * else false
	 *
	 * @return boolean
	 */
	function IsNotBaseItemExists()
	{
		$query = "SELECT COUNT( `menu_item_id` ) FROM `".TBL_MENU_ITEM."` WHERE `base_item`='0'";

		return ( MySQL::fetchField( $query ) ) ? true: false;	
	}
}

class SK_AdminNavigationException extends Exception {}

?>