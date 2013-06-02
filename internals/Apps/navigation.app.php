<?php

class app_Navigation
{
/**
	 * Get full breadcrumb for document key
	 *
	 * @param string $document_key
	 *
	 * @return array
	 *
	 */ 
//	public static function getDocumentBreadCrumb()
//	{
//		$document_key = SK_HttpRequest::getDocument()->document_key;
//		
//		$_breadcrumb_arr = array();
//		$_breadcrumb_arr[] = $document_key;
//		$_compiled_query =SK_MySQL::compile_placeholder("SELECT `parent_document_key` FROM `".TBL_DOCUMENT."` WHERE `document_key`='?'" );
//		
//		while ( true )
//		{
//			$_query = SK_MySQL::placeholder( $_compiled_query, $document_key );
//			
//			$_parent_doc_key = SK_MySQL::query( $_query )->fetch_cell();
//			if ( !$_parent_doc_key )
//				break;
//			else 
//				$_breadcrumb_arr[] = $_parent_doc_key;
//				
//			$document_key = $_parent_doc_key;
//		}
//		
//		return $_breadcrumb_arr;	
//	}
//	
//	public static function getDocumentURL( $document_key, $query_string='' )
//	{
//		$query = SK_MySQL::placeholder("SELECT `url` FROM `".TBL_DOCUMENT."` WHERE `document_key`='?'",$document_key);
//		$_url = SITE_URL.SK_MySQL::query($query)->fetch_cell();
//		return sk_make_url($_url, $query_string );
//	}
//	
		
//	public static function getActiveMenuItem( $menu_name )
//	{
//		$document_key = SK_HttpRequest::getDocument()->document_key;
//		$_result = SK_MySQL::query(SK_MySQL::placeholder("SELECT * FROM `".TBL_MENU_ITEM."` AS `menu_item`
//															LEFT JOIN `".TBL_MENU."` AS `menu` USING(`menu_id`)
//															WHERE `document_key`='?' AND `menu`.`menu_name`='?'",$document_key,$menu_name));
//		if( $_result->num_rows() > 1 )
//			while ($_item = $_result->fetch_object())
//				if(intval($_item->parent_menu_item_id )) return  $_item;
//				
//		return	$_result->fetch_object();
//	}
//	
//	
//	public static function getActiveItemsBranch( $active_menu_item_id )
//	{
//		$branch = array();
//		
//		if( !$active_menu_item_id ) return array();
//		
//		$query = SK_MySQL::placeholder("SELECT `parent_menu_item_id` AS `parent_id`
//										FROM `".TBL_MENU_ITEM."` AS `menu_item`
//										WHERE `menu_item_id`=?", $active_menu_item_id);
//		
//		$result = SK_MySQL::query($query);
//		
//		if ( !$result->num_rows() ) {
//			return $branch;
//		}
//		
//		$parent_id = $result->fetch_cell();
//		
//		$branch[] = $active_menu_item_id;
//		
//		return array_merge(self::getActiveItemsBranch($parent_id), $branch);
//	}
//	
//	
//	
//	
//	public static function getMenu( $menu_name, $recursive = 0, $parent_menu_item_id = 0 )
//	{
//		static $query, $active_items_branch = array(), $menu_ids = array(), $membership_type_id = 1;
//		
//		if ( !isset($menu_ids[$menu_name]) ) {
//			
//			if($active_menu_item = self::getActiveMenuItem($menu_name))
//				$active_items_branch = self::getActiveItemsBranch($active_menu_item->menu_item_id);
//			
//			$menu_ids[$menu_name] = SK_MySQL::query(
//				SK_MySQL::placeholder(
//					"SELECT `menu_id` FROM `".TBL_MENU."` WHERE `menu_name`='?'", $menu_name
//				)
//			)->fetch_cell();
//		}
//		
//		if ( !isset($query) )
//		{
//			$query = SK_MySQL::compile_placeholder(
//					"SELECT `menu_item`.`name`,`menu_item_id`,`document`.`url`
//						FROM `".TBL_MENU_ITEM."` AS `menu_item`
//						LEFT JOIN `".TBL_LINK_MENU_ITEM_MEMBERSHIP."` AS `link` USING( `menu_item_id` )
//						LEFT JOIN `".TBL_DOCUMENT."` AS `document` USING(`document_key`)
//						WHERE `menu_id`=? AND `parent_menu_item_id`=? AND `link`.`membership_type_id`=? ORDER BY `menu_item`.`order`"
//			);
//					
//			$membership_type_id = app_Profile::MembershipTypeId();
//		}
//		
//		$menu_items = array();
//		$result = SK_MySQL::query(
//			SK_MySQL::placeholder($query, $menu_ids[$menu_name], $parent_menu_item_id, $membership_type_id)
//		);
//		
//		while ( $row = $result->fetch_assoc() )
//		{
//			$is_active = in_array($row['menu_item_id'], $active_items_branch);
//			
//			$menu_items[$row['menu_item_id']] = 
//				array(
//					'label'		=>	SK_Language::section('nav_menu_item')->text($row['name']),
//					'href'		=>	SITE_URL.$row['url'],
//					'active'	=>	$is_active,
//					'sub_menu'	=>	($recursive === 2 || ($recursive && $is_active))
//										? self::getMenu($menu_name, $recursive, $row['menu_item_id'])
//										: array()
//				);
//		}
//		
//		return $menu_items;
//	}
	
//	public static function getCustomDocs()
//	{
//		$query = 'SELECT `url` FROM `'.TBL_DOCUMENT.'` WHERE `custom=1';
//		$result = SK_MySQL::query($query);
//		while ($url = $result->fetch_cell())
//			$out[$doc->document_key] = $url;
//		return $out;
//	}
//	
//	public static function getCustomDocument( $url )
//	{
//		$url_info = parse_url($url);
//		$query = SK_MySQL::placeholder('SELECT `url` FROM `'.TBL_DOCUMENT.'` WHERE `custom=1 AND `url`="?"',$url_info['path']);
//		return SK_MySQL::query($query)->fetch_cell();
//	}
//	
//	public static function ProfileNavigation( array $document)
//	{
//		
//		if ( !$document['status'] )
//		{
//			header( 'HTTP/1.0 404 NOT FOUND' );	
//			exit();
//		}
//		
//		if(SK_HttpUser::is_authenticated())
//		{
//			if ( !$document['access_member'] ){
//				SK_HttpUser::redirect(self::getDocumentURL('home')); 	
//			}
//				
//		}
//		else 
//		{
//			if ( !$document['access_guest'] )		
//				return 'sign_in'; 	
//		}
//			
//	}
	
	
	
}