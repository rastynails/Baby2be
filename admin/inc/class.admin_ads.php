<?php
require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );

/**
 * Project:    SkaDate reloaded
 * File:       class.admin_ads.php
 *
 * @link http://www.skadate.com/
 * @package SkaDate
 * @version 4.0
 */


/**
 * Class for config ads on site
 *
 * @package SkaDate
 * @since 5.0
 * @author Denis J
 * @link http://www.skalinks.com/ca/forum/ Technical Support forum
 */
class adminAds
{
    public static function getAllTemplates()
	{
		$query = "SELECT * FROM `".TBL_ADS_TEMPLATE."` ORDER BY `template_id` DESC";
		return MySQL::fetchArray( $query, 'template_id' );
	}
	
	
	public static function createTemplate( &$name, &$code )
	{
		if ( !strlen( trim( $name ) ) )
			return -1;
			
		if ( !strlen( trim( $code ) ) )
			return -2;
			
		// detetetc if tha same name exists
		$query = sql_placeholder( "SELECT `name` FROM `".TBL_ADS_TEMPLATE."` WHERE `name`=?", $name );
		if ( MySQL::fetchField( $query ) )
			return -3;
				
		$query = sql_placeholder( "INSERT INTO `".TBL_ADS_TEMPLATE."`( `name`, `code` ) 
		VALUES( ?@ )", array( $name, $code ) );
			
		return MySQL::insertId( $query );
	}
	
	public static function deleteTemplate( &$template_id )
	{
		if ( !( int )$template_id )
			return false;
			
		$query = "DELETE FROM `".TBL_ADS_TEMPLATE."` WHERE `template_id`='$template_id'";
		MySQL::fetchResource( $query );
		
		$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_TPL."` WHERE `template_id`='$template_id'";
		MySQL::fetchResource( $query );

		$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_COUNTRY."` WHERE `template_id`='$template_id'";
		MySQL::fetchResource( $query );		
		
		return true;
	}
	
	
	
	public static function saveTemplate( $template_id, $name, $code )
	{
		if ( !( int )$template_id )
			return -1;
			
		if ( !strlen( trim( $name ) ) )
			return -2;
			
		if ( !strlen( trim( $code ) ) )
			return -3;
			
		// check if the same name exists
		$query = sql_placeholder( "SELECT `name` FROM `".TBL_ADS_TEMPLATE."` WHERE `name`=? AND `template_id`!=?", $name, $template_id );
		if ( MySQL::fetchField( $query ) )
			return -4;
			
		// update row
		$query = sql_placeholder( "UPDATE `".TBL_ADS_TEMPLATE."` SET `name`=?, `code`=? WHERE `template_id`='$template_id'", $name, $code );
		
		return MySQL::affectedRows( $query );
	}
	
	public static function getAdsPosInfo( $parent_doc = '' )
	{
		$query = "SELECT * FROM `".TBL_DOCUMENT."` WHERE `parent_document_key`='$parent_doc'";
		$_docs = MySQL::fetchArray( $query );
				
		foreach ( $_docs as $_key => $_doc_info )
		{
			$query = "SELECT `name`, `bind`.`template_id`, `position_id`, `bind`.`link_id` FROM `".TBL_LINK_ADS_PAGE_POSITION."`
				LEFT JOIN `".TBL_LINK_ADS_BINDING_TPL."` AS `bind` USING( `link_id` ) 
				LEFT JOIN `".TBL_ADS_TEMPLATE."` USING( `template_id` ) 
				WHERE `document_key`='{$_doc_info['document_key']}'";
			
			$_docs[$_key]['ads_info'] = MySQL::fetchArray( $query, 'position_id' );
			
			// detect if child exists
			$query = "SELECT `document_key` FROM `".TBL_DOCUMENT."` 
				WHERE `parent_document_key`='{$_doc_info['document_key']}' 
				LIMIT 1";
			
			if ( MySQL::fetchRow( $query ) )
				$_docs[$_key]['sub_docs'] = adminAds::getAdsPosInfo( $_doc_info['document_key'] );
		}
		
		return $_docs;
	}
	
	public static function getAdsPositions()
	{
		$query = "SELECT * FROM `".TBL_ADS_POSITION."`";
		return MySQL::fetchArray( $query );		
	}
	
	public static function addAdsPosToDoc( $document_key )
	{
		for( $i = 1; $i <= 4 ; $i++ )
		{
			$query = "INSERT INTO `".TBL_LINK_ADS_PAGE_POSITION."` ( `document_key`, `position_id` ) 
				VALUES( '$document_key', '$i' )";
			
			SK_MySQL::query($query);
		}
	}
	
	public static function setAdsTemplateToDoc( $document_key, $pos_arr, $template_id = null )
	{
		if ( !is_array( $pos_arr ) )
			return false;
		
		foreach ( $pos_arr as $position_id )
		{
			// get link id
			$query = "SELECT `link_id` FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
				WHERE `document_key`='$document_key' AND `position_id`='$position_id'";
			
			$_link_id = MySQL::fetchField( $query );
			
			if ( !$_link_id )
				continue;
		
			$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_TPL."` WHERE `link_id`='$_link_id'";
			MySQL::fetchResource( $query );
			
			if ( !$template_id )
				continue;
		
			$query = "INSERT INTO `".TBL_LINK_ADS_BINDING_TPL."`( `link_id`, `template_id` ) 
				VALUES( '$_link_id', '$template_id' )";
		
			MySQL::fetchResource( $query );
		}
		
		return true;
	}
	
	public static function deleteAdsPositionFromDocument( $document_key, $position_id = null )
	{
		if ( !strlen( trim( $document_key ) ) )
			return false;
			
		if ( ( int )$position_id )
		{
			$query = "SELECT `link_id` FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
				WHERE `document_key`='$document_key' AND `position_id`='$position_id'";
			
			$_link_id = MySQL::fetchField( $query );
			
			$query = "DELETE FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
				WHERE `document_key`='$document_key' AND `position_id`='$position_id'";

			MySQL::fetchResource( $query );
			
			$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_TPL."` WHERE `link_id`='$_link_id'";
			MySQL::fetchResource( $query );
		}
		else 
		{
			$query = "SELECT `link_id`, `position_id` FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
				WHERE `document_key`='$document_key'";
			
			$_positions = MySQL::fetchArray( $query );
			foreach ( $_positions as $_key => $_pos_info )
			{
				$query = "DELETE FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
					WHERE `document_key`='$document_key' AND `position_id`='{$_pos_info['position_id']}'";
				
				MySQL::fetchResource( $query );
			
				$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_TPL."` WHERE `link_id`='{$_pos_info['link_id']}'";
				MySQL::fetchResource( $query );
			}
		}
	}
	
	/* ******** Processing ads template sets ****** */
	public static function getAllCountries()
	{
		$other = array('Country_str_code'=>'XX', 'Region_str_code'=>'Oth', 'Country_str_name'=>'Other');
		 
		$countries = array_merge(array($other), app_Location::Countries());		
		return $countries;
	}
	
	
	public static function getAllTemplateSets()
	{
		$query = "SELECT * FROM `".TBL_ADS_TEMPLATE_SET."` ORDER BY `template_set_id` DESC";
		$tmpl_sets = MySQL::fetchArray( $query, 'template_set_id' );
		
		foreach($tmpl_sets as $template_set)
		{
			$query = "SELECT `template_id`, `template`.`name`, `template`.`code`, `template_set_id`, `Country_str_code`, `country`.`Country_str_name` FROM `".TBL_LINK_ADS_BINDING_COUNTRY."` 
				LEFT JOIN	`".TBL_ADS_TEMPLATE."` AS `template` USING( `template_id` )
				LEFT JOIN	`".TBL_LOCATION_COUNTRY."` AS `country` USING( `Country_str_code` )		
				WHERE template_set_id='".$template_set['template_set_id']."' ORDER BY `Country_str_name`";
			$child_array = MySQL::fetchArray( $query, 'template_id' );			

			$tmpl_sets[$template_set['template_set_id']]['templates'] = $child_array; 	
		}

			
		return $tmpl_sets;
	}
	
	public static function createTemplateSet( &$name, &$templates, &$countries)
	{
		if ( !strlen( trim( $name ) ) )
			return -1;
		if ( !count($templates) )
			return -2;			
	
		// detect if the same set already exists
		$query = sql_placeholder( "SELECT `name` FROM `".TBL_ADS_TEMPLATE_SET."` WHERE `name`=?", $name );
		if ( MySQL::fetchField( $query ) )
			return -3;
				
		$query = sql_placeholder( "INSERT INTO `".TBL_ADS_TEMPLATE_SET."`( `name` ) 
		VALUES( ?@ )", array( $name ) );
		$template_set_id = MySQL::insertId( $query );
		
		
		foreach ($templates as $key=>$template)		
		{
			$query = sql_placeholder( "INSERT INTO `".TBL_LINK_ADS_BINDING_COUNTRY."`( `template_id`, `template_set_id`, `Country_str_code` ) 
			VALUES( ?@ )", array( $template, $template_set_id, $countries[$key] ) );
						
			MySQL::insertId( $query );			
		}
		

		
		return $template_set_id;
	}
	
	public static function deleteTemplateSet( &$template_set_id )
	{
		if ( !( int )$template_set_id )
			return false;
			
		$query = "DELETE FROM `".TBL_ADS_TEMPLATE_SET."` WHERE `template_set_id`='$template_set_id'";
		MySQL::fetchResource( $query );
		
		$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_TPL_SET."` WHERE `template_set_id`='$template_set_id'";
		MySQL::fetchResource( $query );
		
		$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_COUNTRY."` WHERE `template_set_id`='$template_set_id'";
		MySQL::fetchResource( $query );
		
		return true;
	}
	
	public static function saveTemplateSet($template_set_id, $name,  &$templates, &$countries)
	{
		if ( !( int )$template_set_id )
			return -1;
			
		if ( !strlen( trim( $name ) ) )
			return -2;
			
		if ( !count($templates) )
			return -3;
			
		// check if the same name exists
		$query = sql_placeholder( "SELECT `name` FROM `".TBL_ADS_TEMPLATE_SET."` WHERE `name`=? AND `template_set_id`!=?", $name, $template_set_id );
		if ( MySQL::fetchField( $query ) )
			return -4;

		$affRows = 0;
		// update name row
		$query = sql_placeholder( "UPDATE `".TBL_ADS_TEMPLATE_SET."` SET `name`=? WHERE `template_set_id`='$template_set_id'", $name );		
		$affRows += MySQL::affectedRows( $query );
	
		
		foreach($countries as $key=>$country)
		{			
			// if template was removed from set
			if (!isset($templates[$key])) 
			{
				$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_COUNTRY."` WHERE `template_set_id`='$template_set_id' AND `template_id`='".$key."' AND `Country_str_code`='".$country."'";

				$affRows +=	MySQL::affectedRows( $query );
			}
			else			
			{
				$query = "SELECT `Country_str_code` FROM `".TBL_LINK_ADS_BINDING_COUNTRY."` WHERE `template_id`='".$key."' AND `template_set_id`='".$template_set_id."' ";
				if (!MySQL::fetchField( $query ))
				{
					// if new template was added
					$query = sql_placeholder( "INSERT INTO `".TBL_LINK_ADS_BINDING_COUNTRY."`( `template_id`, `template_set_id`, `Country_str_code` ) 
						VALUES( ?@ )", array( $templates[$key], $template_set_id, $country ) );
					$affRows += MySQL::affectedRows( $query );				
				}
				else
				{
					// if country for template was modified 
					$query = sql_placeholder( "UPDATE `".TBL_LINK_ADS_BINDING_COUNTRY."` SET  `Country_str_code`=? WHERE `template_id`=? AND `template_set_id`='?'", $country, $templates[$key], $template_set_id );
					$affRows += MySQL::affectedRows( $query );
				}
			}
		}	
					
		return $affRows;
	}
	
	public static function getAdsSetPosInfo( $parent_doc = '' )
	{
		$query = "SELECT * FROM `".TBL_DOCUMENT."` WHERE `parent_document_key`='$parent_doc'";
		$_docs = MySQL::fetchArray( $query );
				
		foreach ( $_docs as $_key => $_doc_info )
		{
			$query = "SELECT `name`, `bind`.`template_set_id`, `position_id`, `bind`.`link_id` FROM `".TBL_LINK_ADS_PAGE_POSITION."`
				LEFT JOIN `".TBL_LINK_ADS_BINDING_TPL_SET."` AS `bind` USING( `link_id` ) 
				LEFT JOIN `".TBL_ADS_TEMPLATE_SET."` USING( `template_set_id` ) 
				WHERE `document_key`='{$_doc_info['document_key']}'";
			
			
			$_docs[$_key]['ads_info'] = MySQL::fetchArray( $query, 'position_id' );
			
			// detect if child exists
			$query = "SELECT `document_key` FROM `".TBL_DOCUMENT."` 
				WHERE `parent_document_key`='{$_doc_info['document_key']}' 
				LIMIT 1";
			
			if ( MySQL::fetchRow( $query ) )
				$_docs[$_key]['sub_docs'] = adminAds::getAdsSetPosInfo( $_doc_info['document_key'] );
		}
		
		return $_docs;
	}
	
	
	
	public static function setAdsTemplateSetToDoc( $document_key, $pos_arr, $template_set_id = null )
	{
		if ( !is_array( $pos_arr ) )
			return false;
		
		foreach ( $pos_arr as $position_id )
		{
			// get link id
			$query = "SELECT `link_id` FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
				WHERE `document_key`='$document_key' AND `position_id`='$position_id'";
			
			$_link_id = MySQL::fetchField( $query );
			
			if ( !$_link_id )
				continue;
		
			$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_TPL_SET."` WHERE `link_id`='$_link_id'";
			MySQL::fetchResource( $query );
			
			if ( !$template_set_id )
				continue;
		
			$query = "INSERT INTO `".TBL_LINK_ADS_BINDING_TPL_SET."`( `link_id`, `template_set_id` ) 
				VALUES( '$_link_id', '$template_set_id' )";
		
			MySQL::fetchResource( $query );
		}
		
		return true;
	}
	
	
	public static function deleteAdsSetPositionFromDocument( $document_key, $position_id = null )
	{
		if ( !strlen( trim( $document_key ) ) )
			return false;
			
		if ( ( int )$position_id )
		{
			$query = "SELECT `link_id` FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
				WHERE `document_key`='$document_key' AND `position_id`='$position_id'";
			
			$_link_id = MySQL::fetchField( $query );
			
			$query = "DELETE FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
				WHERE `document_key`='$document_key' AND `position_id`='$position_id'";

			MySQL::fetchResource( $query );
			
			$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_TPL_SET."` WHERE `link_id`='$_link_id'";
			MySQL::fetchResource( $query );
		}
		else 
		{
			$query = "SELECT `link_id`, `position_id` FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
				WHERE `document_key`='$document_key'";
			
			$_positions = MySQL::fetchArray( $query );
			foreach ( $_positions as $_key => $_pos_info )
			{
				$query = "DELETE FROM `".TBL_LINK_ADS_PAGE_POSITION."` 
					WHERE `document_key`='$document_key' AND `position_id`='{$_pos_info['position_id']}'";
				
				MySQL::fetchResource( $query );
			
				$query = "DELETE FROM `".TBL_LINK_ADS_BINDING_TPL_SET."` WHERE `link_id`='{$_pos_info['link_id']}'";
				MySQL::fetchResource( $query );
			}
		}
	}
	
	public static function getTemplateSet( $template_set_id )
	{

		$query = "SELECT `name` FROM `".TBL_ADS_TEMPLATE_SET."` WHERE `template_set_id`='".$template_set_id."' ";
		$template_set['name'] = SK_MySQL::query( $query )->fetch_cell();
		//check for existance
		if (!$template_set['name']) return false;
		
		$query = "SELECT `Country_str_code`, `template_id` FROM `".TBL_LINK_ADS_BINDING_COUNTRY."` 	
			WHERE `template_set_id`='".$template_set_id."' ";
		$template_set['templates'] = MySQL::fetchArray( $query, 'template_id' );
		
		return $template_set;
	}
	
}
?>
