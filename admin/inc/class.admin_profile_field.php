<?php
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );


/**
 * Project:    SkaDate reloaded
 * File:       class.admin_profile_field.php
 *
 * @link http://www.skadate.com/
 * @package SkaDate
 * @version 4.0
 */


/**
 * This class is intended for working with profile fields in admin area
 *
 * @package SkaDate
 * @since 4.0
 * @author Denis J
 * @link http://www.skalinks.com/ca/forum/ Technical Support forum
 */
class AdminProfileField
{
	/**#@+
	 *
	 * @access public
	 */
	/**
	 * Constructor
	 * Initialize <var>$lang_prefix_id</var> array
	 */
	function AdminProfileField()
	{
		$this->lang_prefix_id = array
		(
			'section'			=> 5,
			'field_page_join'	=> 3,
			'field_page_edit'	=> 13,
			'field_page_view'	=> 14,
			'field_value'		=> 4,
			'field_label'		=> 15,
			'field_label_join'	=> 2,
			'field_label_edit'	=> 16,
			'field_label_view'	=> 17,
			'field_label_search'=> 24,
			'field_confirm'		=> 19,
			'field_error_msg'	=> 21,
			'field_confirm_error_msg'	=> 22,
			'field_select_invite_msg'	=> 23,
		);
	}

	/**
	 * Return array with all fields
	 * or with fields on current page type
	 *
	 * @param string	$page_type type of current page
	 * @param integer	$page_num
	 * @return array
	 */
	function getAllFields( &$page_type, &$page_num, &$current_reliant_value )
	{
		// check input data
		if ( !in_array( $page_type, array( 'edit', 'join', 'view', 'search' ) ) && $page_type )
			return array();

		$page_num = ( ( int )$page_num ) ? $page_num: 1;
		$current_reliant_value = intval( $current_reliant_value );

		$hided_fields = ( $current_reliant_value ) ? app_ProfileField::getHidedDependedFields( 'sex', $current_reliant_value ) : array();

		if( !$page_type )
		{
			$query = "SELECT `field_section`.`order`,`field`.`profile_field_section_id` FROM `".TBL_PROF_FIELD."` AS `field`
				 LEFT JOIN `".TBL_PROF_FIELD_PAGE_LINK."` AS `field_page_link` USING( `profile_field_id` )
				 LEFT JOIN `".TBL_PROF_FIELD_SECTION."` AS `field_section` ON `field`.`profile_field_section_id`=`field_section`.`profile_field_section_id`
				 WHERE `field`.`presentation` IN ( '".implode( "', '", $this->getDisplayFieldPresentations() )."' )
				 AND `field`.`name` NOT IN ( 'state_id', 'city_id', 'zip','country_id' )
				 AND `field`.`name` NOT IN ( '".implode( "','", $hided_fields )."' )
				 GROUP BY `field`.`profile_field_section_id`
				 ORDER BY `field_section`.`order`";

			$_all_sections	= MySQL::FetchArray( $query );

			if ( $_all_sections )
				foreach ( $_all_sections as $key => $value )
				{
					$query = "SELECT * FROM `".TBL_PROF_FIELD."`
						 WHERE `profile_field_section_id`='{$_all_sections[$key]['profile_field_section_id']}'
						 AND `presentation` IN ( '".implode( "', '", $this->getDisplayFieldPresentations() )."' )
						 AND `name` NOT IN ( 'state_id', 'city_id', 'zip','country_id' )
						 AND `name` NOT IN ( '".implode( "','", $hided_fields )."' )
						 ORDER BY `order`";
					$_all_sections[$key]['fields'] = MySQL::fetchArray( $query );
				}
		}
		else
		{
			$query = "SELECT `field_section`.`order`,`field`.`profile_field_section_id` FROM `".TBL_PROF_FIELD."` AS `field`
				 LEFT JOIN `".TBL_PROF_FIELD_PAGE_LINK."` AS `field_page_link` USING( `profile_field_id` )
				 LEFT JOIN `".TBL_PROF_FIELD_SECTION."` AS `field_section` ON `field`.`profile_field_section_id`=`field_section`.`profile_field_section_id`
				 WHERE `field_page_link`.`profile_field_page_type`='$page_type' AND `field_page_link`.`number`='$page_num'
				 AND `field`.`presentation` IN ( '".implode( "', '", $this->getDisplayFieldPresentations() )."' )
				 AND `field`.`name` NOT IN ( 'state_id', 'city_id', 'zip','country_id' )
				 AND `field`.`name` NOT IN ( '".implode( "','", $hided_fields )."' )
				 GROUP BY `field`.`profile_field_section_id`
				 ORDER BY `field_section`.`order`";
			$_all_sections	= MySQL::fetchArray( $query );

			if ( $_all_sections )
			{
				foreach( $_all_sections as $key => $value )
				{
					$query = "SELECT * FROM `".TBL_PROF_FIELD."` `field`
						LEFT JOIN `".TBL_PROF_FIELD_PAGE_LINK."` AS `field_page_link`
						USING( `profile_field_id` )
						WHERE `field`.`profile_field_section_id`='{$_all_sections[$key]['profile_field_section_id']}'
						AND `field_page_link`.`profile_field_page_type`='$page_type' AND `field_page_link`.`number`='$page_num'
						AND `field`.`presentation` IN ( '".implode( "', '", $this->getDisplayFieldPresentations() )."' )
						AND `name` NOT IN ( 'state_id', 'city_id', 'zip','country_id' )
						AND `name` NOT IN ( '".implode( "','", $hided_fields )."' )
						ORDER BY `field`.`order`";
					$_all_sections[$key]['fields'] = MySQL::fetchArray( $query );
				}
			}
		}

		return $_all_sections;
	}

	/**
	 * Get all types of pages
	 *
	 * for example: join, edit and view
	 *
	 * @return array	array with page's types
	 */
	function getPageTypes()
	{
		$query = "SELECT `profile_field_page_type` FROM `".TBL_PROF_FIELD_PAGE."`
			GROUP BY `profile_field_page_type`";

		return MySQL::FetchArray( $query );
	}

	/**
	 * Get all numbers of
	 * current page
	 *
	 * @param	string	$page_type type of current page
	 * @return array	array with numbers
	 */
	function getPageNums( &$page_type )
	{
		$page_type = trim( $page_type );
		if ( !strlen( $page_type ) )
			return array();

		$query = sql_placeholder( "SELECT * FROM `".TBL_PROF_FIELD_PAGE."`
			WHERE `profile_field_page_type`=? GROUP BY `number`", $page_type );

		return MySQL::FetchArray( $query );
	}

	/**
	 * Get all profiles's sections
	 *
	 * @return	array	array with sections
	 */
	function getAllSection()
	{
		$query = "SELECT * FROM `".TBL_PROF_FIELD_SECTION."` ORDER BY `order`";
		return MySQL::FetchArray( $query );
	}

	/**
	 * Delete profile field,
	 * field's values and field's names from langs
	 *
	 * @param	integer	$field_id field's id
	 * @return	integer	if field was delete 1 , else 0
	 *
	 */
	function delField( $field_id )
	{

		// check input data
		$field_id = ( int )$field_id;

		$_field_info	= $this->GetFieldInfo( $field_id );

		if ( !$_field_info['name'] || $_field_info['base_field'] )
			return false;

		// delete field's values langs
		$query	= sql_placeholder( "SELECT `value` FROM `".TBL_PROF_FIELD_VALUE."`
			WHERE `profile_field_id`=?", $_field_info['profile_field_id'] );

		$_f_val	= MySQL::fetchArray( $query );

		// delete langs from join, edit, search and view pages and general label
		SK_LanguageEdit::deleteKey('profile_fields.label',$_field_info['profile_field_id']);
		SK_LanguageEdit::deleteKey('profile_fields.label_edit',$_field_info['profile_field_id']);
		SK_LanguageEdit::deleteKey('profile_fields.label_view',$_field_info['profile_field_id']);
		SK_LanguageEdit::deleteKey('profile_fields.label_join',$_field_info['profile_field_id']);
		SK_LanguageEdit::deleteKey('profile_fields.label_search',$_field_info['profile_field_id']);
		SK_LanguageEdit::deleteKey('profile_fields.confirm',$_field_info['profile_field_id']);
		SK_LanguageEdit::deleteKey('profile_fields.error_msg',$_field_info['profile_field_id']);
		SK_LanguageEdit::deleteKey('profile_fields.select_invite_msg',$_field_info['profile_field_id']);
		SK_LanguageEdit::deleteKey('profile_fields.select_invite_msg',$_field_info['profile_field_id'].'_year');
		SK_LanguageEdit::deleteKey('profile_fields.select_invite_msg',$_field_info['profile_field_id'].'_month');
		SK_LanguageEdit::deleteKey('profile_fields.select_invite_msg',$_field_info['profile_field_id'].'_day');


		// delete field's values from table
		AdminProfileField::deleteAllFieldValues( $_field_info['profile_field_id'] );

		MySQL::fetchResource( $query );

		// delete field from profiles table
		$query	= "ALTER TABLE `".TBL_PROFILE_EXTEND."` DROP `{$_field_info['name']}`";
		MySQL::fetchResource( $query );

		// delete from field's table
		$query = sql_placeholder( "DELETE FROM `".TBL_PROF_FIELD."`
			WHERE `profile_field_id`=? AND `base_field`!='1'", $field_id );

		MySQL::fetchResource( $query );

		// delete from page link table
		$query	= sql_placeholder( "DELETE FROM `".TBL_PROF_FIELD_PAGE_LINK."`
			WHERE `profile_field_id`=?", $field_id );

		MySQL::fetchResource( $query );

		// delete field from field's match
		$query = sql_placeholder( "DELETE FROM `".TBL_PROF_FIELD_MATCH_LINK."`
			WHERE `profile_field_id`=? OR `match_profile_field_id`=?", $field_id, $field_id );

		MySQL::fetchResource( $query );

		// detect if text field and set as default username field, then unset config
		if ( $_field_info['presentation'] == 'text' && SK_Config::Section('profile_fields')->Section('advanced')->default_username_field_display == $_field_info['name'] )
			adminConfig::SaveOneConfig( 'default_username_field_display', 'username' );

		// move fields order
		$query = sql_placeholder( "SELECT `profile_field_id`, `order` FROM `".TBL_PROF_FIELD."`
			WHERE `order`>?", $_field_info['order'] );

		$_fields_order	= MySQL::fetchArray( $query );
		if ( $_fields_order )
			foreach ( $_fields_order as $key => $value )
			{
				$_fields_order[$key]['order']--;

				$query	= sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `order`=?
					WHERE `profile_field_id`=?", $_fields_order[$key]['order'], $_fields_order[$key]['profile_field_id'] );

				MySQL::fetchResource( $query );
			}

		// delete field dependence
		$_query = sql_placeholder( "DELETE FROM `".TBL_PROFILE_FIELD_DEPENDENCE."`
			WHERE `depended_profile_field_id`=?", $field_id );
		MySQL::fetchResource( $_query );

		self::clearCache();
		return true;
	}

	/**
	 * Get all info about field
	 *
	 * for exam: name, base, and etc
	 *
	 * @param	integer	$field_id	filed's id
	 * @return 	array array with field's info
	 */
	function getFieldInfo( &$field_id )
	{
		$field_id = intval( $field_id );

		if ( !$field_id )
			return array();

		$query = sql_placeholder( "SELECT `field`.*, `match_field1`.`match_profile_field_id` AS `match_field`, `match_field2`.`profile_field_id` AS `matching`, `match_field2`.`match_type` AS `matching_type`
			FROM `".TBL_PROF_FIELD."` AS `field`
			LEFT JOIN `".TBL_PROF_FIELD_MATCH_LINK."` AS `match_field1` USING( `profile_field_id` )
			LEFT JOIN `".TBL_PROF_FIELD_MATCH_LINK."` AS `match_field2` ON `field`.`profile_field_id`=`match_field2`.`match_profile_field_id`
			WHERE `field`.`profile_field_id`=?", $field_id );

		return MySQL::fetchRow( $query );
	}

	/**
	 * Moves field to other section
	 *
	 * @param	integer	$field_id	field's id
	 * @param	integer	$section_id	id of section where field move
	 *
	 * @return	integer
	 */
	function changeFieldSection( $field_id, $section_id )
	{
		// check input data
		$field_id	= ( int )$field_id;
		$section_id	= ( int )$section_id;

		if ( !$field_id || !$section_id)
			return 0;

		if ( $field_id == 112 )
		{
			$this->changeFieldSection( 111, $section_id );
			$this->changeFieldSection( 113, $section_id );
			$this->changeFieldSection( 114, $section_id );
			$this->changeFieldSection( 115, $section_id );
			$this->changeFieldSection( 116, $section_id );
			$this->changeFieldSection( 56, $section_id );
		}

		$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `profile_field_section_id`=?
			WHERE `profile_field_id`=?", $section_id, $field_id );

		self::clearCache();
		return MySQL::affectedRows( $query );
	}

	/**
	 * Moves field to other number of page's type
	 *
	 * for exam: move field from first join page to second.
	 * It is forbidden to move last field from none last number of page
	 *
	 * @param	integer	$field_id	field's id
	 * @param	string	$page_type	current type of page
	 * @param	integer	$page_number	number of page, where field move
	 * @return	string|integer	mysql result ot error code
	 */
	function changeFieldPage( $field_id, $page_type, $page_number )
	{
		// check input data
		$field_id		= ( int )$field_id;
		$page_number	= ( int )$page_number;

		if ( !$field_id )
			return 0;

		// check if this field not last on current page and if this current page is last
		$query = sql_placeholder( "SELECT `number` FROM `".TBL_PROF_FIELD_PAGE_LINK."`
			WHERE `profile_field_id`=? AND `profile_field_page_type`=?", $field_id, $page_type );

		$_old_number	= MySQL::fetchField( $query );

		// check if this field not last on current page and if this current page is last
		$query	= sql_placeholder( "SELECT COUNT(`profile_field_id`) FROM `".TBL_PROF_FIELD_PAGE_LINK."`
			WHERE `profile_field_page_type`=? AND `number`=?", $page_type, $_old_number );

		$_count_fields	= MySQL::fetchField( $query );

		$query	= sql_placeholder( "SELECT `number` FROM `".TBL_PROF_FIELD_PAGE."`
			WHERE `profile_field_page_type`=? AND `number`>?", $page_type, $_old_number );

		$_not_last	= MySQL::fetchField( $query );

		if ( $_count_fields == 1 && $_not_last )
		{
			return -2;
		}
		else
		{
			$query = sql_placeholder( "SELECT `number` FROM `".TBL_PROF_FIELD_PAGE."`
				WHERE `profile_field_page_type`=? AND `number`=?", $page_type, $page_number );

			$_num_exists		= MySQL::fetchField( $query );

			// if number exists, then update
			if ( $_num_exists )
			{
				// custom processing location field
				if ( $field_id == 112 )
				{
					$this->changeFieldPage( 111, $page_type, $page_number );
					$this->changeFieldPage( 113, $page_type, $page_number );
					$this->changeFieldPage( 114, $page_type, $page_number );
					$this->changeFieldPage( 115, $page_type, $page_number );
					$this->changeFieldPage( 116, $page_type, $page_number );
					$this->changeFieldPage( 56, $page_type, $page_number );
				}

				$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD_PAGE_LINK."` SET `number`=?
					WHERE `profile_field_page_type`=? AND `profile_field_id`=?", $_num_exists, $page_type, $field_id );

				self::clearCache();
				return MySQL::AffectedRows( $query );
			}
			else
				return -1;
		}
	}

	/**
	 * Change order of section with profiles
	 *
	 * @param	integer	$section_id	section's id
	 * @param	integer	$order	new number of order
	 * @return	string|integer	mysql result or error code
	 */
	function changeSectionOrder( &$section_id, &$action )
	{
		// check input data
		$section_id = intval( $section_id );

		if ( !$section_id )
			return 0;

		switch ( $action )
		{
			case 'up':

				$query_cond		= " `field_section`.`order` < ";
				$query_order	= "ORDER BY `field_section`.`order` DESC ";
				break;

			case 'down':

				$query_cond		= " `field_section`.`order` > ";
				$query_order	= "ORDER BY `field_section`.`order` ";
				break;

			default :
				return 	-1;
		}

		// get old order
		$query		= sql_placeholder( "SELECT `order` FROM `".TBL_PROF_FIELD_SECTION."`
			WHERE `profile_field_section_id`=?", $section_id );

		$_old_order	= MySQL::FetchField( $query );

		// get section id , where new order
		$query = sql_placeholder( "SELECT `field_section`.`profile_field_section_id`, `field_section`.`order`
			FROM `".TBL_PROF_FIELD_SECTION."` AS `field_section`
			LEFT JOIN `".TBL_PROF_FIELD."` AS `field` USING( `profile_field_section_id` )
			WHERE $query_cond ?
			AND `field`.`profile_field_id` IS NOT NULL
			".$query_order."
			LIMIT 0, 1", $_old_order );

		$_new_order_info	= MySQL::fetchRow( $query );

		if ( $_new_order_info['order'] )
		{
			$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD_SECTION."` SET `order`=?
				WHERE `profile_field_section_id`=?", $_new_order_info['order'], $section_id );

			$_affected_row = MySQL::affectedRows( $query );

			$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD_SECTION."` SET `order`=?
				WHERE `profile_field_section_id`=?", $_old_order, $_new_order_info['profile_field_section_id'] );

			$_affected_row += MySQL::affectedRows( $query );

			self::clearCache();
			return $_affected_row;
		}
		else
			return -2;
	}

	/**
	 * Change order in section of profile field
	 *
	 * @param	integer	$field_id	field's id
	 * @param	integer	$move	action with order ( UP or DOWN )
	 * @return	string|boolean	mysql result or error code
	 */
	function changeFieldOrder( &$field_id, &$move )
	{
		// check input data
		$field_id	= ( int )$field_id;

		if ( !$field_id )
			return false;

		// get old order
		$query = sql_placeholder( "SELECT `order`,`profile_field_section_id` FROM `".TBL_PROF_FIELD."`
			WHERE `profile_field_id`=?", $field_id );

		$_field_info	= MySQL::FetchRow( $query );

		// get field id, where order is current
		switch( $move )
		{
			case 'up':

				$query	= sql_placeholder( "SELECT `order`,`profile_field_id` FROM `".TBL_PROF_FIELD."`
					WHERE `profile_field_section_id`=? AND `order`<?
					ORDER BY `order` DESC LIMIT 0,1", $_field_info['profile_field_section_id'], $_field_info['order'] );
				break;

			case 'down':

				$query = sql_placeholder( "SELECT `order`,`profile_field_id` FROM `".TBL_PROF_FIELD."`
				WHERE `profile_field_section_id`=? AND `order`>?
				ORDER BY `order` LIMIT 0,1", $_field_info['profile_field_section_id'], $_field_info['order'] );
				break;

			default:
				return false;
		}

		$_new_order_info = MySQL::FetchRow( $query );

		if ( $_field_info && $_new_order_info )
		{
			$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `order`=?
				WHERE `profile_field_id`=?", $_new_order_info['order'], $field_id );

			$_affected_rows = MySQL::fetchResource( $query );

			$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `order`=?
				WHERE `profile_field_id`=?", $_field_info['order'], $_new_order_info['profile_field_id'] );

			$_affected_rows += MySQL::fetchResource( $query );
			self::clearCache();
			return $_affected_rows;
		}
		else
			return false;
	}

	/**
	 * Remove field from page type ( join, edit, view, search )
	 *
	 * It is forbidden to remove last fild from none last number of page type
	 *
	 * @param	integer	$field_id	field's id
	 * @param	string	$page_type	type of current page
	 * @param	$number	$number		number of page type where field is located
	 * @return	string|integer	mysql result or error code
	 */
	function deleteFieldFromPage( $field_id, $page_type, $number )
	{
		// check input data
		$field_id	= ( int )$field_id;

		if ( !$field_id )
			return 0;

		// check if this a base field
		if ( in_array( $page_type, array( 'join', 'edit' ) ) )
		{
			$query = sql_placeholder( "SELECT `base_field` FROM `".TBL_PROF_FIELD."`
				WHERE `profile_field_id`=? AND `name`!='username' AND `editable_by_admin`!=1", $field_id );

			if ( MySQL::fetchField( $query ) )
				return -2;
		}

		// check if this field not last on current page and if this current page is last
		$query	= sql_placeholder( "SELECT COUNT(`profile_field_id`) FROM `".TBL_PROF_FIELD_PAGE_LINK."`
			WHERE `profile_field_page_type`=? AND `number`=?", $page_type, $number );

		$_count_fields	= MySQL::FetchField( $query );

		$query	= sql_placeholder( "SELECT `number` FROM `".TBL_PROF_FIELD_PAGE."`
			WHERE `profile_field_page_type`=? AND `number`>?", $page_type, $number );

		$_not_last	= MySQL::fetchField( $query );

		if ( $_count_fields == 1 && !$_not_last )
		{
			return -1;
		}
		else
		{
			switch ( $page_type )
			{
				case 'edit':
					$query_ins = 'editable_by_member';
					break;
				case 'join':
					$query_ins = 'join_page';
					break;
				case 'view':
					$query_ins = 'viewable_by_member';
					break;
				case 'search':
					$query_ins = 'searchable';
					break;
			}

			$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `".$query_ins."`='0'
				WHERE `profile_field_id`=?", $field_id );

			MySQL::fetchResource( $query );

			// custom processing location field
			if ( $field_id == 112 )
			{
				$this->deleteFieldFromPage( 111, $page_type, $number );
				$this->deleteFieldFromPage( 113, $page_type, $number );
				$this->deleteFieldFromPage( 114, $page_type, $number );
				$this->deleteFieldFromPage( 115, $page_type, $number );
				$this->deleteFieldFromPage( 116, $page_type, $number );
				$this->deleteFieldFromPage( 56, $page_type, $number );
			}

			$query = sql_placeholder( "DELETE FROM `".TBL_PROF_FIELD_PAGE_LINK."`
		 		WHERE `profile_field_id`=? AND `profile_field_page_type`=?", $field_id, $page_type );

			self::clearCache();
			return MySQL::affectedRows( $query );
		}
	}

	/**
	 * Create new section for profile's fields
	 *
	 * @return string	mysql result
	 */
	function addSection( )
	{
		// get max order
		$query		= "SELECT MAX(`order`) FROM `".TBL_PROF_FIELD_SECTION."`";
		$max_order	= MySQL::fetchField( $query ) + 1;

		$query = sql_placeholder( "INSERT INTO `".TBL_PROF_FIELD_SECTION."`(`profile_field_section_id`,`order`)
			VALUES(NULL, ?)", $max_order );

		self::clearCache();
		return MySQL::insertId( $query );
	}

	/**
	 * Delete section from database
	 * Forbidden to delete section with id = 1
	 *
	 * @param	integer	$sec_id	section's id
	 * @return	string|integer	mysql result or error code
	 */
	function delSection( &$sec_id )
	{
		// check input data
		$sec_id = intval( $sec_id );

		if ( $sec_id == 1 )
			return -1;

			// get section info
			$query = sql_placeholder( "SELECT `order` FROM `".TBL_PROF_FIELD_SECTION."`
				WHERE `profile_field_section_id`=?", $sec_id );

			$_sec_order	= MySQL::fetchField( $query );

			// delete section from db
			$query = sql_placeholder( "DELETE FROM `".TBL_PROF_FIELD_SECTION."`
				WHERE `profile_field_section_id`=?", $sec_id );

			MySQL::affectedRows( $query );

			// set order for other section
			$query = sql_placeholder( "SELECT `order`, `profile_field_section_id` FROM `".TBL_PROF_FIELD_SECTION."`
				WHERE `order`>?", $_sec_order );

			$_sections	= MySQL::FetchArray( $query );
			if ( $_sections )
				foreach ( $_sections as $key => $value )
				{
					$_sections[$key]['order']--;

					$query	= sql_placeholder( "UPDATE `".TBL_PROF_FIELD_SECTION."` SET `order`=?
						WHERE `profile_field_section_id`=?", $_sections[$key]['order'], $_sections[$key]['profile_field_section_id'] );

					MySQL::affectedRows( $query );
				}

			// change to default section fields with deleted section
			$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `profile_field_section_id`='1'
				WHERE `profile_field_section_id`=?", $sec_id );

			self::clearCache();
			return MySQL::affectedRows( $query );
	}

	/**
	 * Create new number of page type.
	 *
	 * For example: creates "Edit page - Step 2" for edit page
	 *
	 * for exam: create page number "Join page - Step 2" for page of type "Join"
	 *
	 * @param	string	$page_type type of page, where new page is created
	 * @return	integer	number of created page or error code
	 */
	function addPage( &$page_type )
	{
		if ( !in_array( $page_type, array( 'join', 'edit', 'view' ) ) )
			return false;

		// get current max number of page type
		$query		= sql_placeholder( "SELECT MAX(`number`) FROM `".TBL_PROF_FIELD_PAGE."`
			WHERE `profile_field_page_type`=?", $page_type );

		$_next_num	= MySQL::fetchField( $query ) + 1;

		$query = "INSERT INTO `".TBL_PROF_FIELD_PAGE."`(`profile_field_page_type`,`number`) VALUES('$page_type','$_next_num')";
		MySQL::affectedRows( $query );

		self::clearCache();
		return $_next_num;
	}

	/**
	 * Delete page number from type of page
	 *
	 * Forbidden to delete first page
	 *
	 * @param	string	$page_type	type of page, where number of page is deleted
	 * @param 	integer	$number	number of deleting page
	 *
	 * @return string|integer	mysql result or error code
	 */
	function delPage( &$page_type, &$number )
	{
		// check input data
		$number		= intval( $number );

		if ( $number <= 1 )
			return -2;

		if ( !in_array( $page_type, array( 'join', 'edit', 'view' ) ) )
			return -1;

		// check if this last page
		$query = sql_placeholder( "SELECT `number` FROM `".TBL_PROF_FIELD_PAGE."`
			WHERE `profile_field_page_type`=? AND `number`>? LIMIT 0,1", $page_type, $number );

		$_not_last	= MySQL::fetchField( $query );
		if ( $_not_last )
			return -3;

		$query = sql_placeholder( "DELETE FROM `".TBL_PROF_FIELD_PAGE."`
			WHERE `profile_field_page_type`=? AND `number`=?", $page_type, $number );

		MySQL::affectedRows( $query );

		// move to first page fields on deleted page
		$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD_PAGE_LINK."` SET `number`='1'
			WHERE `profile_field_page_type`=? AND `number`=?", $page_type, $number );

		self::clearCache();
		return MySQL::affectedRows( $query );
	}

	/**
	 * Get all types of profile's field
	 *
	 * There are: 'text', 'password', 'date' and etc...
	 *
	 * @return array	array with types of profile field
	 */
	function getFieldTypes()
	{
		$query	= "SHOW COLUMNS FROM `".TBL_PROF_FIELD."` LIKE 'presentation'";
		$_columns = MySQL::fetchRow( $query );

		// parse Type of field
		preg_match( "/enum\((.*)\)/i", $_columns['Type'], $_matches );

		$_field_types	= explode( ',', str_replace( "'", "", $_matches[1]) );

		return $_field_types;
	}

	/**
	 * Create new field into the database
	 *
	 * Create all nesessary values for possible field's values into the langs.
	 * Makes ALTER TABLE for table with profiles. Adds field to join, edit, view and search pages if checked.
	 * Returns ID of created field
	 * Takes array as argument with following items:<br />
	 * <li>f_name - Name of field on latin language</li>
	 * <li>f_type - Field presentation</li>
	 * <li>f_section - Field section ID</li>
	 * <li>f_regexp - Regexp for field</li>
	 * <li>f_required - If field required</li>
	 * <li>f_edit_member - If field editable by member</li>
	 * <li>f_edit_name - Name of field on edit page</li>
	 * <li>f_view_member - If field viewable by member</li>
	 * <li>f_view_name	- Name of field on view page</li>
	 * <li>f_join_member - If field display on join page</li>
	 * <li>f_join_name - Name of field on join page</li>
	 * <li>f_search_member - If field display on search form</li>
	 * <li>f_search_name - Name of field on search form</li>
	 * <li>f_confirm - If field requires confirm</li>
	 * <li>f_confirm_name - Name of confirmation field</li>
	 * <li>f_error_msg - Error message for field</li>
	 * <li>f_confirm_error_msg - Error message for wrong field confirmation<li>
	 * <li>f_first_select - Text of label for choise from list ( if field presentation is select or date )</li>
	 * <li>f_match - ID of match field</li>
	 *
	 * @param array $params
	 * @return	integer
	 */
	function addNewField( $params )
	{
            $languages = SK_LanguageEdit::getLanguages();
            $languagesCount = count($languages);



		$f_name_label	= is_array($params['f_name']) ? array_filter( $params['f_name'],'strlen' ) : array();
		if(count($f_name_label))
			$f_name	= strtolower( preg_replace( "/[^\w]/i", "_", $f_name_label[SK_Config::Section('languages')->default_lang_id] ));
		else
			return -1;

		if ( !strlen( $f_name ) )
			return -1;

		// processing input data
		$f_match		= intval( $params['f_match'] );
		$f_section		= intval( $params['f_section'] ) ? intval( $params['f_section'] ) : 1;
		$f_required		= strlen( $params['f_required'] ) ? 1 : 0;
		$f_edit_member	= strlen( $params['f_edit_member'] ) ? 1 : 0;
		$f_view_member	= strlen( $params['f_view_member'] ) ? 1 : 0;
		$f_join_member	= strlen( $params['f_join_member'] ) ? 1 : 0;
		$f_search_member	= strlen( $params['f_search_member'] ) ? 1 : 0;

		$f_edit_name	= is_array($params['f_edit_name']) ? array_filter( $params['f_edit_name'],'strlen' ) : array();
		$f_view_name	= is_array($params['f_view_name']) ? array_filter( $params['f_view_name'],'strlen' ) : array();
		$f_join_name	= is_array($params['f_join_name']) ? array_filter( $params['f_join_name'],'strlen' ) : array();
		$f_search_name	= is_array($params['f_search_name']) ? array_filter( $params['f_search_name'],'strlen' ) : array();
		$f_confirm_error_msg	= is_array($params['f_confirm_error_msg']) ? array_filter( $params['f_confirm_error_msg'],'strlen' ) : array();
		$f_error_msg	= is_array($params['f_error_msg']) ? array_filter( $params['f_error_msg'],'strlen' ) : array();
		$f_confirm_name = is_array($params['f_confirm_name']) ? array_filter( $params['f_confirm_name'],'strlen' ) : array();


		$f_confirm		= intval( $params['f_confirm'] ) ? 1 : 0;
		$f_type		= trim( $params['f_type'] );
		$f_regexp	= trim( $params['f_regexp'] );

        $f_profile_list_member	= ( $params['f_profile_list_member'] ) ? 1 : 0;
        $f_profile_list_value = is_array($params['f_profile_list_value']) ? array_filter( $params['f_profile_list_value'],'strlen' ) : array();

		$f_first_select = $params['f_first_select'];
		$f_first_select['select'] = is_array(@$f_first_select['select'])? array_filter(@$f_first_select['select'],'strlen') : array();
		$f_first_select['year'] = is_array(@$f_first_select['year'])? array_filter(@$f_first_select['year'],'strlen') : array();
		$f_first_select['month'] = is_array(@$f_first_select['month'])? array_filter(@$f_first_select['month'],'strlen') : array();
		$f_first_select['day'] = is_array(@$f_first_select['day'])? array_filter(@$f_first_select['day'],'strlen') : array();

		$depended_values = $params['depended_values'];
		$reliant_field_id = intval( $params['reliant_field_id'] );

		$f_column_size = trim(@$params['f_column_size']);
		$f_column_size = strtolower($f_column_size);

		if (substr($f_column_size,-1)=='%'){

			$column_size =intval($f_column_size);
			$column_size_measure = '%';
		}
		else {
			$column_size = intval($f_column_size);
			$column_size_measure = 'px';
		}

		$f_column_size = (bool)$column_size ? $column_size.$column_size_measure : '100%';


		// check values of field name in page types
		if ( $f_edit_member && count( $f_edit_name ) != $languagesCount )
			return -4;

		if ( $f_view_member && count( $f_view_name ) != $languagesCount )
			return -5;

		if ( $f_join_member	&& count( $f_join_name ) != $languagesCount )
			return -6;

		if ( $f_search_member && count( $f_search_name ) != $languagesCount )
			return -10;

        if ( $f_type == 'birthdate' && $f_profile_list_member && count( $f_profile_list_value ) != $languagesCount )
			return -11;

		if ( count( $f_error_msg ) != $languagesCount )
			return -9;

		// check confirm field
		if ( $f_confirm && ( count( $f_confirm_name ) != $languagesCount || count( $f_confirm_error_msg ) != $languagesCount ) )
			return -8;

		// check field presentation
		if ( !in_array( $f_type, $this->getDisplayFieldPresentations() ) )
			return -7;


		// check if this field exists
		$query			= sql_placeholder( "SELECT `name` FROM `".TBL_PROF_FIELD."` WHERE `name`=?", $f_name );
		$field_exists	= MySQL::FetchField( $query );

		if ( $field_exists )
			return -3;

		// get new field order
		$query			= "SELECT MAX(`order`) FROM `".TBL_PROF_FIELD."`";
		$f_order	= MySQL::FetchField( $query ) + 1;

		// add field into field's table
		$query	= sql_placeholder( "INSERT INTO `".TBL_PROF_FIELD."`
			( `profile_field_id`, `profile_field_section_id`, `name`, `presentation`, `regexp`, `required_field`, `join_page`, `editable_by_member`, `viewable_by_member`, `searchable`, `confirm`, `order`, `column_size`, `profile_list_template` )
			VALUES( NULL, ?@ )", array( $f_section, $f_name, $f_type, $f_regexp, $f_required, $f_join_member, $f_edit_member, $f_view_member, $f_search_member, $f_confirm, $f_order, $f_column_size, $f_profile_list_member ) );

		$field_id	= MySQL::insertId( $query );

		// add field to profile table
		$query	= sql_placeholder( "ALTER TABLE `".TBL_PROFILE_EXTEND."` ADD `$f_name` " );

		switch ( $f_type )
		{
			case 'age_range':

				$query_ins	= "VARCHAR(5) DEFAULT NULL ";
				break;

			case 'fselect':
			case 'fradio':
				$query_ins = "INT(10) DEFAULT NULL";
				break;

			case 'select':
			case 'multiselect':
			case 'multicheckbox':
			case 'radio':

				$query_ins	= "BIGINT(20) DEFAULT NULL ";
				break;

			case 'textarea':

				$query_ins	= "TEXT DEFAULT NULL ";
				$query_ins	= "TEXT DEFAULT NULL ";
				break;
			case 'birthdate':
			case 'date':

				$query_ins	= "DATE DEFAULT NULL ";
				break;

			case 'checkbox':

				$query_ins	= "TINYINT(1) DEFAULT NULL ";
				break;

			case 'password':
			case 'text':
                        case 'email':
                        case 'url':
				$query_ins	= "VARCHAR(128) DEFAULT NULL ";
				break;
			case 'callto':

				$query_ins	= "VARCHAR(32) DEFAULT NULL ";
				break;
		}

		MySQL::fetchResource( $query.$query_ins );

		// set field match
		$this->SetFieldMatch( $field_id, $f_match, $f_type );

		$order = 1;

		// Add langs for join, edit, search and view pages
		if ( $f_edit_member )
		{
			// add field to edit page
			$this->AddFieldToPage( $field_id, 'edit', 1 );
			SK_LanguageEdit::setKey('profile_fields.label_edit',$field_id, $f_edit_name);
		}
		if ( $f_view_member )
		{
			// add field to view page
			$this->AddFieldToPage( $field_id, 'view', 1 );
			SK_LanguageEdit::setKey('profile_fields.label_view',$field_id, $f_view_name);
		}
		if ( $f_join_member )
		{
			// add field to join page
			$this->AddFieldToPage( $field_id, 'join', 1 );
			SK_LanguageEdit::setKey('profile_fields.label_join',$field_id, $f_join_name);
		}
		if ( $f_search_member )
		{
			// add field to join page
			$this->AddFieldToPage( $field_id, 'search', 1 );
			SK_LanguageEdit::setKey('profile_fields.label_search',$field_id, $f_search_name);
		}

        if ( count($f_profile_list_value) ){
            // add profile list label for field
			SK_LanguageEdit::setKey('profile_fields.label_profile_list', $field_id, $f_profile_list_value);
		}

		// add general label for field
		SK_LanguageEdit::setKey('profile_fields.label',$field_id, $f_name_label);

		// add error error message to langs
		SK_LanguageEdit::setKey('profile_fields.error_msg',$field_id, $f_error_msg);

		if ( $f_confirm )
		{
			SK_LanguageEdit::setKey('profile_fields.confirm',$field_id, $f_confirm_name);
			SK_LanguageEdit::setKey('profile_fields.error_msg','confirm_'.$field_id, $f_confirm_error_msg);
		}

		// define field dependence
		$this->updateFieldDependence( $reliant_field_id, $depended_values, $field_id );

		//add invite message if presentation select or date
		switch ( $f_type )
		{
			case 'select':
			case 'fselect':

				if ( count( $f_first_select['select'] ) )
					SK_LanguageEdit::setKey('profile_fields.select_invite_msg',$field_id, $f_first_select['select']);

				break;

			case 'birthdate':
			case 'date':

				if ( count( $f_first_select['year'] ) )
					SK_LanguageEdit::setKey('profile_fields.select_invite_msg',$field_id.'_year', $f_first_select['year']);


				if ( count( $f_first_select['month'] ) )
					SK_LanguageEdit::setKey('profile_fields.select_invite_msg',$field_id.'_month', $f_first_select['month']);

				if ( count( $f_first_select['day'] ) )
					SK_LanguageEdit::setKey('profile_fields.select_invite_msg',$field_id.'_day', $f_first_select['day']);

					try {
						list($default_start, $default_end) = explode('-', SK_ProfileFields::get('birthdate')->custom);
					} catch (SK_ProfileFieldException $e) {
						$default_start = 1910;
						$default_end = 1990;
					}

				self::ChangeFieldCustomValue($field_id, "$default_start-$default_end", $f_type);

				break;
		}
		self::clearCache();
		return $field_id;
	}

	/**
	 * Get fields, which are can be but not present on current type of page
	 *
	 * @param	string	$page_type	type of page
	 *
	 * @return	array	array with fields
	 */
	function getNotInPageFields( &$page_type )
	{
		$field_type_cond = '';
		// get cond of current page
		switch ( $page_type )
		{
			case 'edit':

				$query_cond	= 'editable_by_member';
				break;
			case 'view':

				$query_cond	= 'viewable_by_member';
				break;
			case 'join':

				$query_cond = 'join_page';
				break;
			case 'search':

				$query_cond = 'searchable';
				// not select age_range fields
				$field_type_cond = "AND `presentation`!='age_range'";
				break;
			default:

				return false;
		}


		$query	= "SELECT `profile`.`profile_field_id` FROM `".TBL_PROF_FIELD."` AS `profile`
			LEFT JOIN `".TBL_PROF_FIELD_PAGE_LINK."` AS `page_link` ON
			`profile`.`profile_field_id`=`page_link`.`profile_field_id` AND `page_link`.`profile_field_page_type`='$page_type'
			WHERE `page_link`.`profile_field_id` IS NULL AND `$query_cond` = '0' AND `presentation` IN ( '".implode( '\' ,\'', $this->getDisplayFieldPresentations() )."' ) $field_type_cond
			AND `name` NOT IN ( 'state_id', 'city_id', 'zip' )
			GROUP BY `profile`.`profile_field_id`";

		return MySQL::fetchArray( $query );
	}

	/**
	 * Add field to type of page
	 *
	 * @param	integer	$field_id	field's id
	 * @param	string	$page_type	type of page
	 * @param	integer	$page_type_number	number of page type
	 *
	 * @return string|integer	mysql result ot error code
	 */
	function addFieldToPage( $field_id, $page_type, $page_type_number )
	{
		// check input data
		$field_id			= intval( $field_id );
		$page_type_number	= intval( $page_type_number );

		if ( !$field_id )
			return -1;
		else if ( !$page_type_number )
			return -2;
		else if ( !in_array( $page_type, array( 'edit', 'join', 'view', 'search' ) ) )
			return -3;

		// make field joinable, editable, viewable or searchble
		switch ( $page_type )
		{
			case 'edit':

				$query_ins = 'editable_by_member';
				break;
			case 'join':

				$query_ins = 'join_page';
				break;
			case 'view':

				$query_ins = 'viewable_by_member';
				break;
			case 'search':

				$query_ins = 'searchable';
				break;
		}

		$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `".$query_ins."`='1'
			WHERE `profile_field_id`=?", $field_id );

		MySQL::fetchResource( $query );

		// custom processing location field
		if ( $field_id == 112 )
		{
			$this->addFieldToPage( 111, $page_type, $page_type_number );
			$this->addFieldToPage( 113, $page_type, $page_type_number );
			$this->addFieldToPage( 114, $page_type, $page_type_number );
			$this->addFieldToPage( 115, $page_type, $page_type_number );
			$this->addFieldToPage( 116, $page_type, $page_type_number );
			$this->addFieldToPage( 56, $page_type, $page_type_number );
		}

		// check if exists
		$query = sql_placeholder( "SELECT `profile_field_id` FROM `".TBL_PROF_FIELD_PAGE_LINK."`
			WHERE `profile_field_id`=? AND `profile_field_page_type`=?", $field_id, $page_type );

		if ( MySQL::fetchField( $query ) )
			return 0;

		$query	= sql_placeholder( "INSERT INTO `".TBL_PROF_FIELD_PAGE_LINK."`(`profile_field_id`, `profile_field_page_type`, `number`)
			VALUES( ?@ )", array( $field_id, $page_type, $page_type_number ) );

		self::clearCache();
		return MySQL::AffectedRows( $query );
	}

	/**
	 * Get all field's values
	 *
	 * @global 	object	$mysql	to use methods for working with mysql db
	 *
	 * @param	integer	$field_id filed's id
	 * @param 	string filed of order
	 * @return	array	array with field's values
	 */
	public static function getFieldValues( $field_id, $order_type = 'order' )
	{
		$field_id = intval( $field_id );
		if ( !$field_id )
			return array();

		$_order_cond = ( $order_type == 'order' ) ? "ORDER BY `order`" : "ORDER BY `value`";

		$query	= sql_placeholder( "SELECT * FROM `".TBL_PROF_FIELD_VALUE."`
			WHERE `profile_field_id`=? ".$_order_cond, $field_id );

		return MySQL::FetchArray( $query );
	}

	/**
	* Update profile field info. If lang key for field's label
	* is empty, function requires new label and creates lang key
	* for this label.
	*
	* Parametr $f_type must be:<br>
	* <ul>
	*  <li>text</li>
	*  <li>password</li>
	*  <li>select</li>
	*  <li>multiselect</li>
	*  <li>textarea</li>
	*  <li>date</li>
	*  <li>checkbox</li>
	*  <li>multicheckbox</li>
	* </ul>
	*
	* @param array $params
	*
	* @return integer|string mysql result or error code
	*/
	function updateField( $params )
	{
            $languages = SK_LanguageEdit::getLanguages();
            $languagesCount = count($languages);

		// check input data
		$field_id		= intval( $params['field_id'] );
		$f_match		= intval( $params['f_match'] );
		$f_type			= trim( $params['f_type'] );
		$f_section		= ( int )$params['f_section'] ? intval( $params['f_section'] ) : 1;
		$f_regexp		= trim( $params['f_regexp'] );
		$f_required		= ( $params['f_required'] ) ? 1 : 0;

        $f_edit_member	= ( $params['f_edit_member'] ) ? 1 : 0;
		$f_view_member	= ( $params['f_view_member'] ) ? 1 : 0;
		$f_join_member	= ( $params['f_join_member'] ) ? 1 : 0;
		$f_search_member = ( $params['f_search_member'] ) ? 1 : 0;
        $f_profile_list_member	= ( $params['f_profile_list_member'] ) ? 1 : 0;

		$f_confirm		= ( $params['f_confirm'] ) ? 1 : 0;
		$f_confirm_name	= is_array($params['f_confirm_name']) ? array_filter($params['f_confirm_name'],'strlen' ) : array();
                $f_confirm_error_msg = is_array($params['f_confirm_error_msg']) ? array_filter($params['f_confirm_error_msg'],'strlen' ) : array();
		$f_edit_name	= is_array($params['f_edit_name']) ? array_filter( $params['f_edit_name'],'strlen' ) : array();
		$f_view_name	= is_array($params['f_view_name']) ? array_filter( $params['f_view_name'],'strlen' ) : array();
		$f_join_name	= is_array($params['f_join_name']) ? array_filter( $params['f_join_name'],'strlen' ) : array();
		$f_search_name	= is_array($params['f_search_name']) ? array_filter( $params['f_search_name'],'strlen' ) : array();
		$f_error_msg	= is_array($params['f_error_msg']) ? array_filter( $params['f_error_msg'],'strlen' ) : array();
		$f_profile_list_value = is_array($params['f_profile_list_value']) ? array_filter( $params['f_profile_list_value'],'strlen' ) : array();

        $f_first_select = $params['f_first_select'];
		$f_first_select['select'] = is_array(@$f_first_select['select'])? array_filter(@$f_first_select['select'],'strlen') : array();
		$f_first_select['year'] = is_array(@$f_first_select['year'])? array_filter(@$f_first_select['year'],'strlen') : array();
		$f_first_select['month'] = is_array(@$f_first_select['month'])? array_filter(@$f_first_select['month'],'strlen') : array();
		$f_first_select['day'] = is_array(@$f_first_select['day'])? array_filter(@$f_first_select['day'],'strlen') : array();

		//$f_column_size = intval( $params['f_column_size'] ) ? intval( $params['f_column_size'] ) : 1;

		$f_column_size = trim(@$params['f_column_size']);
		$f_column_size = strtolower($f_column_size);

		if (substr($f_column_size,-1)=='%'){

			$column_size =intval($f_column_size);
			$column_size_measure = '%';
		}
		else {
			$column_size = intval($f_column_size);
			$column_size_measure = 'px';
		}

		$f_column_size = (bool)$column_size ? $column_size.$column_size_measure : '100%';


		$depended_values = $params['depended_values'];
		$reliant_field_id = intval( $params['reliant_field_id'] );

		// get if lang key exists
		$f_edit_lang_key = SK_Language::section('profile_fields')->section('label_edit')->key_exists($field_id);
		$f_view_lang_key = SK_Language::section('profile_fields')->section('label_view')->key_exists($field_id);
		$f_join_lang_key = SK_Language::section('profile_fields')->section('label_join')->key_exists($field_id);
		$f_search_lang_key = SK_Language::section('profile_fields')->section('label_search')->key_exists($field_id);
		$f_confirm_lang_exists = SK_Language::section('profile_fields')->section('confirm')->key_exists($field_id);
                $f_profile_list_key = SK_Language::section('profile_fields')->section('label_profile_list')->key_exists($field_id);


		// check values of field name in page types
		if ( $f_edit_member && ( !$f_edit_lang_key && count( $f_edit_name ) != $languagesCount) )
			return -4;
		if ( $f_view_member && ( !$f_view_lang_key && count( $f_view_name ) != $languagesCount ) )
			return -5;

        	if ( $f_join_member	&& ( !$f_join_lang_key && count( $f_join_name ) != $languagesCount ) )
			return -6;
		if ( $f_search_member && ( count( $f_search_name ) != $languagesCount && !$f_search_lang_key ) )
			return -10;
        if ( $f_profile_list_member && ( count( $f_profile_list_value ) != $languagesCount && !$f_profile_list_key ) )
			return -11;

		// check confirm field
		if ( $f_confirm && ( count( $f_confirm_name ) != $languagesCount && !$f_confirm_lang_exists) )
			return -9;

		// get filed info
		$field_info	= $this->GetFieldInfo( $field_id );


		// delete field values if presentation changed from select to fselect
		if ( ( in_array( $field_info['presentation'], array( 'fselect', 'fradio' ) ) && !in_array( $f_type, array( 'fselect', 'fradio' ) ) )
			|| ( !in_array( $field_info['presentation'], array( 'fselect', 'fradio' ) ) && in_array( $f_type, array( 'fselect', 'fradio' ) ) ) )
			{
				AdminProfileField::deleteAllFieldValues( $field_info['profile_field_id'] );
			}


		// check field presentation
		if ( !$field_info['base_field'] && !in_array( $f_type, AdminProfileField::getDisplayFieldPresentations() ) )
			return -7;

		if ( !$field_id )
			return -8;
		// check if this field exists
		if ( !$field_info )
			return -3;

		// update field's info in field's table
		if ( !$field_info['base_field'] || $field_info['editable_by_admin'])
		{


			$add_query = ($f_type == 'birthdate') ? ", `profile_list_template`='$f_profile_list_member'" : '';

			$query	= sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `presentation`=?presentation,
				`regexp`=?regexp, `required_field`='$f_required', `join_page`='$f_join_member',
				`editable_by_member`='$f_edit_member', `viewable_by_member`='$f_view_member', `confirm`='$f_confirm',
				`searchable`='$f_search_member', `column_size`='$f_column_size', `column_size`='$f_column_size' $add_query
				WHERE `profile_field_id`='$field_id'", array
													(
														'presentation' => $f_type,
														'regexp' => $f_regexp,
													) );
			$_affected_row = MySQL::affectedRows( $query );
			if (!$field_info['base_field'])
			{
				// add field to profile table
				$query	= "ALTER TABLE `".TBL_PROFILE_EXTEND."` CHANGE `{$field_info['name']}` `{$field_info['name']}` ";

				switch ( $f_type )
				{
					case 'age_range':
						$query_ins	= "VARCHAR(5) DEFAULT NULL ";
						break;

					case 'fselect':
					case 'fradio':
						$query_ins = "INT(10) DEFAULT NULL";
						break;

					case 'select':
					case 'multiselect':
					case 'multicheckbox':
					case 'radio':
						$query_ins	= "BIGINT(20) DEFAULT NULL ";
						break;
					case 'textarea':
						$query_ins	= "TEXT DEFAULT NULL ";
						break;

					case 'date':
					case 'birthdate':
						$query_ins	= "DATE DEFAULT NULL ";
						break;
					case 'checkbox':
						$query_ins	= "TINYINT(1) DEFAULT NULL ";
						break;
					case 'password':
					case 'text':
                                        case 'email':
                                        case 'url':
						$query_ins	= "VARCHAR(128) DEFAULT NULL ";
						break;
					case 'callto':

						$query_ins	= "VARCHAR(32) DEFAULT NULL ";
						break;
					default:
						$query = '';
						$query_ins = '';
						break;
				}
				$query = $query.$query_ins;
				if (strlen($query)) {
					$_affected_row += MySQL::affectedRows( $query );
				}
			}
		}
		else
		{
			$add_query = ($field_info['name']=='username') ? ",`editable_by_member`='$f_edit_member'" : '';
            $add_query .= ", `profile_list_template`='$f_profile_list_member'";

			$query	= sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `presentation`=?presentation,
				`regexp`=?regexp, `viewable_by_member`='$f_view_member', `confirm`='$f_confirm',
				`searchable`='$f_search_member', `column_size`='$f_column_size' $add_query
				WHERE `profile_field_id`='$field_id'", array
													(
														'presentation' => $f_type,
														'regexp' => $f_regexp,
													) );

			$_affected_row =	MySQL::affectedRows( $query );
		}

		// set section
		$_affected_row += $this->changeFieldSection( $field_id, $f_section );

		if ( !$field_info['matching'] )
		{
			// update field's match
			$_affected_row += $this->SetFieldMatch( $field_id, $f_match, $f_type );
		}
		else
		{
			switch ( $field_info['matching_type'] )
			{
				case 'exact':
					if ( !in_array( $f_type, array( 'select', 'multiselect', 'multicheckbox', 'radio' ) ) )
					{
						$query = "DELETE FROM `".TBL_PROF_FIELD_MATCH_LINK."` WHERE `match_profile_field_id`='$field_id'";
						$_affected_row += MySQL::affectedRows( $query );
					}
					break;
				case 'range':
					if ( $f_type != 'age_range' )
					{
						$query = "DELETE FROM `".TBL_PROF_FIELD_MATCH_LINK."` WHERE `match_profile_field_id`='$field_id'";
						$_affected_row += MySQL::affectedRows( $query );
					}
					break;
			}
		}

		$is_username = ($field_info['name']=='username');

		// add field to view, join and edit pages, if not exists or delete if exists
		if ( !$field_info['join_page'] && $f_join_member )
			$this->AddFieldToPage( $field_id, 'join', 1 );
		elseif ( $field_info['join_page'] && !$f_join_member && (!$field_info['base_field'] || $field_info['editable_by_admin']) )
			$this->DeleteFieldFromPage( $field_id, 'join', 1 );

		if ( !$field_info['editable_by_member'] && $f_edit_member )
			$this->AddFieldToPage( $field_id, 'edit', 1 );
		elseif ( $field_info['editable_by_member'] && !$f_edit_member && ((!$field_info['base_field'] || $field_info['editable_by_admin']) || $is_username) )
			$this->DeleteFieldFromPage( $field_id, 'edit', 1 );

		if ( !$field_info['viewable_by_member'] && $f_view_member )
			$this->AddFieldToPage( $field_id, 'view', 1 );
		elseif ( $field_info['viewable_by_member'] && !$f_view_member )
			$this->deleteFieldFromPage( $field_id, 'view', 1 );

		if ( !$field_info['searchable'] && $f_search_member )
			$this->AddFieldToPage( $field_id, 'search', 1 );
		elseif ( $field_info['searchable'] && !$f_search_member )
			$this->DeleteFieldFromPage( $field_id, 'search', 1 );

		// Add langs for join, edit and view pages
		if ( count($f_edit_name) ){
			SK_LanguageEdit::setKey('profile_fields.label_edit',$field_id,$f_edit_name);
			$_affected_row ++;
		}

		if ( count($f_view_name) ){
			SK_LanguageEdit::setKey('profile_fields.label_view',$field_id,$f_view_name);
			$_affected_row ++;
		}

		if ( count($f_join_name) ){
			SK_LanguageEdit::setKey('profile_fields.label_join',$field_id,$f_join_name);
			$_affected_row ++;
		}

		if ( count($f_search_name) ){
			SK_LanguageEdit::setKey('profile_fields.label_search',$field_id,$f_search_name);
			$_affected_row ++;
		}

                if ( count($f_profile_list_value) ){
			SK_LanguageEdit::setKey('profile_fields.label_profile_list', $field_id, $f_profile_list_value);
			$_affected_row ++;
		}

		if ( count($f_error_msg) ){
			SK_LanguageEdit::setKey('profile_fields.error_msg',$field_id,$f_error_msg);
			$_affected_row ++;
		}

		if ( count($f_confirm_name ) ){
			SK_LanguageEdit::setKey('profile_fields.confirm',$field_id,$f_confirm_name);
			$_affected_row ++;
		}

		if ( count($f_confirm_error_msg ) ){
			SK_LanguageEdit::setKey('profile_fields.error_msg','confirm_'.$field_id, $f_confirm_error_msg);
			$_affected_row ++;
		}

		// add invite message if type select or date
		switch ( $f_type )
		{
			case 'select':
			case 'fselect':
				if ( count( $f_first_select['select'] ) ){
					SK_LanguageEdit::setKey('profile_fields.select_invite_msg',$field_id, $f_first_select['select']);
					$_affected_row ++;
				}

				break;
			case 'date':
			case 'birthdate':
                if ( count( $f_first_select['year'] ) ){
                    SK_LanguageEdit::setKey('profile_fields.select_invite_msg',$field_id.'_year', $f_first_select['year']);
                    $_affected_row ++;
                }
                if ( count( $f_first_select['month'] ) ){
                    SK_LanguageEdit::setKey('profile_fields.select_invite_msg',$field_id.'_month', $f_first_select['month']);
                    $_affected_row ++;
                }
                if ( count( $f_first_select['day'] ) ){
                    SK_LanguageEdit::setKey('profile_fields.select_invite_msg',$field_id.'_day', $f_first_select['day']);
                    $_affected_row ++;
                }
                break;
		}

		// define field dependence
		$_affected_row += (int)$this->updateFieldDependence( $reliant_field_id, $depended_values, $field_id );

		if ( $field_info['name'] = 'photo_upload' )
			MySQL::affectedRows( sql_placeholder( "UPDATE ?#TBL_PROF_FIELD SET `presentation`='photo_upload' WHERE `name`='photo_upload'" ) );

		self::clearCache();
		return $_affected_row;
	}

	/**
	* Create new value for field
	*
	* Parametr $value_pos must be:<br>
	* <ul>
	*  <li> 0 - new value is added at the begin of value list</li>
	*  <li> -1 - new value is added at the end of value list</li>
	*  <li> integer number higher than zero - order of value, after which new value is created
	* </ul>
	*
	* @param integer $field_id field id
	* @param integer $value_pos value position
	* @param string $field_value
	*
	* @return integer|boolean
	*/
	function addFieldValue( $field_id, $value_pos, $field_value )
	{
		// check input data
		$field_id	= intval( $field_id );
		$value_pos	= intval( $value_pos );
		$value_order = 0;
		if ( !$field_id )
			return false;

		if ( !trim($field_value[SK_Config::Section('languages')->default_lang_id]) )
			return -1;

		// get field info
		$field_info	= $this->GetFieldInfo( $field_id );

		// select values before
		if ( $value_pos >= 0  )
		{
			// set cond if position at the begin or not
			if ( !$value_pos )
				$query_cond	= "AND `order`>='1'";
			else
				$query_cond	= sql_placeholder( "AND `order`>?", $value_pos );

			$query = sql_placeholder( "SELECT `value`, `order` FROM `".TBL_PROF_FIELD_VALUE."`
				WHERE `profile_field_id`=? ".$query_cond." ORDER BY `order`", $field_id );

			$mov_values	= MySQL::fetchArray( $query );

			if ( $mov_values )
				foreach ( $mov_values as $key => $value )
				{
					// change values order at before
					$new_order	= $mov_values[$key]['order']+1;

					$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD_VALUE."` SET `order`=?
						WHERE `profile_field_id`=? AND `value`=?", $new_order, $field_id, $mov_values[$key]['value'] );

					MySQL::affectedRows( $query );
				}

			$value_order	= $mov_values[0]['order'];
		}

		// get max order and max value
		$query	= sql_placeholder( "SELECT MAX(`order`) AS `order`, MAX(`value`) AS `value` FROM `".TBL_PROF_FIELD_VALUE."`
			WHERE `profile_field_id`=?", $field_id );

		$max = MySQL::fetchRow( $query );

		$_field_bin_value = '';
		if ( in_array( $field_info['presentation'], array( 'fselect', 'fradio' ) ) )
		{
			$value_key		= ( $max['value'] ) ? $max['value']+1 : 1;
		}
		else
		{
			$_all_values = AdminProfileField::getFieldValues( $field_id, 'value' );

			$_degree = 0;
			$_temp_number = intval( pow( 2, $_degree ) );

			if ( $_all_values )
				foreach ( $_all_values as $_key => $_value_info )
				{
					if ( $_temp_number != $_value_info['value'] )
					{
						$_field_bin_value = $_temp_number;
						break;
					}
					$_degree++;
					$_temp_number = intval( pow( 2, $_degree ) );
				}

			$value_key		= ( $_field_bin_value ) ? $_field_bin_value : pow( 2, $_degree );
		}


		$value_order	= ( $value_order ) ? $value_order : $max['order']+1;

		// insert new value
		$query	= sql_placeholder( "INSERT INTO `".TBL_PROF_FIELD_VALUE."`( `profile_field_id`, `value`, `order` )
			VALUES( ?@ )", array( $field_id, $value_key, $value_order ) );

		MySQL::affectedRows( $query );

		$new_val_id = mysql_insert_id();

		// insert field's value into the langs
		SK_LanguageEdit::setKey('profile_fields.value', $field_info['name'].'_'.$value_key, $field_value);

		if ( $field_info['name'] == 'sex' )
			AdminMembership::MakeNewSexAvailableForDeafultMembershipType( $value_key );

		self::clearCache();
		return true;
	}

	/**
	* Delete field's value and lang's key
	*
	* @param integer $field_id field id
	* @param integer $value deleting field value
	*
	* @return integer|boolean
	*/
	function deleteFieldValue( $field_id, $value )
	{
		// check input data
		$field_id = intval( $field_id );
		$value = intval( $value );

		if ( !$field_id || !$value )
			return false;

		/// get field info
		$field_info = $this->GetFieldInfo( $field_id );

		if ($field_info['name']=='sex') {
			$result = SK_MySQL::query(SK_MySQL::placeholder("
				SELECT COUNT(*) FROM `" . TBL_PROFILE . "` WHERE `sex`=?
			", $value))->fetch_cell();
			if ($result) {
				return -2;
			}
		}

		// get field's value order
		$query	= sql_placeholder( "SELECT `order` FROM `".TBL_PROF_FIELD_VALUE."`
			WHERE `profile_field_id`=? AND `value`=?", $field_id, $value );

		$value_info	= MySQL::fetchRow( $query );
		if ( !$value_info['order'] )
			return -1;

		// delete value
		$query	= sql_placeholder( "DELETE FROM `".TBL_PROF_FIELD_VALUE."`
			WHERE `profile_field_id`=? AND `value`=?", $field_id, $value );

		MySQL::AffectedRows( $query );

		// delete lang
		SK_LanguageEdit::deleteKey('profile_fields.value',$field_info['name'].'_'.$value);

		// move order of other fields
		$query	= sql_placeholder( "SELECT `order`,`value` FROM `".TBL_PROF_FIELD_VALUE."`
			WHERE `profile_field_id`=? AND `order`>?", $field_id, $value_info['order'] );

		$moved_values	= MySQL::FetchArray( $query );

		if ( $moved_values )
			foreach ( $moved_values as $key => $item )
			{
				$new_order	= $moved_values[$key]['order']-1;

				$query	= sql_placeholder( "UPDATE `".TBL_PROF_FIELD_VALUE."` SET `order`=?
					WHERE `profile_field_id`=? AND `value`=?", $new_order, $field_id, $moved_values[$key]['value'] );

				MySQL::AffectedRows( $query );
			}

		if ( $field_info['name'] == 'sex' )
		{
			if ($value) {
				$_query = sql_placeholder( "DELETE FROM `".TBL_PROFILE_FIELD_DEPENDENCE."`
					WHERE `value`=?", $value );
				MySQL::fetchResource( $_query );
			}

			AdminMembership::ClearMembershipTypeAvailabilityFromDeletedSex( $value );
		}

		self::clearCache();
		return true;
	}

	/**
	* Change field's value order in value's list
	* Parametr $action must be:<br>
	* <ul>
	*  <li>up</li>
	*  <li>down</li>
	* </ul>
	*
	* @param integer $field_id field id
	* @param integer $value field value
	* @param string $action
	*
	* @return integer error code or 1
	*/
	function moveFieldValueOrder( &$field_id, &$value, &$action )
	{
		// check input data
		$field_id	= ( int )$field_id;
		$value		= ( int )$value;

		if ( !$field_id || !$value )
			return false;

		if ( $action != 'up' && $action != 'down' )
			return -1;

		// get field's value order
		$query	= sql_placeholder( "SELECT `order` FROM `".TBL_PROF_FIELD_VALUE."`
			WHERE `profile_field_id`=? AND `value`=?", $field_id, $value );

		$value_order	= MySQL::fetchField( $query );
		if ( !$value_order )
			return -2;

		// get new order
		switch ( $action )
		{
			case 'up':

				$query = sql_placeholder( "SELECT * FROM `".TBL_PROF_FIELD_VALUE."`
					WHERE `profile_field_id`=? AND `order`<?
					ORDER BY `order` DESC LIMIT 0,1", $field_id, $value_order );
				break;
			case 'down':

				$query	 = sql_placeholder( "SELECT * FROM `".TBL_PROF_FIELD_VALUE."`
					WHERE `profile_field_id`=? AND `order`>?
					ORDER BY `order` LIMIT 0,1", $field_id, $value_order );
				break;
		}

		$result	= MySQL::fetchRow( $query );

		if ( !$result['order'] )
			return -3;

		// change orders
		$query	= sql_placeholder( "UPDATE `".TBL_PROF_FIELD_VALUE."` SET `order`=?
			WHERE `profile_field_id`=? AND `value`=?", $result['order'], $field_id , $value);

		MySQL::affectedRows( $query );

		$query	= sql_placeholder( "UPDATE `".TBL_PROF_FIELD_VALUE."` SET `order`=?
			WHERE `profile_field_id`=? AND `value`=?", $value_order, $result['profile_field_id'], $result['value'] );

		MySQL::affectedRows( $query );

		self::clearCache();
		return 1;
	}

	/**
	* Change custom value for field.
	* Fields with different presentations has different custom values
	*
	* @param integer $field_id field's id
	* @param mixed $custom_value custom value of field
	* @param string $f_type type of field
	*
	* @return integer mysql affected rows or error code
	*/
	function ChangeFieldCustomValue( $field_id, $custom_value, $f_type )
	{
		// check input data
		$field_id	= ( int )$field_id;

		if ( !$field_id )
			return false;

		switch ( $f_type )
		{
			case 'date':
			case 'birthdate':
			case 'age_range':

				$temp_expl = explode( '-', $custom_value );
				if ( !( int )$temp_expl[0] || !( int )$temp_expl[1] )
					return -1;

				break;
			default:
				return -2;
		}

		$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD."` SET `custom`=?
			WHERE `profile_field_id`=?", $custom_value, $field_id );

		self::clearCache();
		return MySQL::affectedRows( $query );
	}

	/**
	* Get
	*/
	function GetFieldPossibleMatches( $field_type, $field_id )
	{
		$field_id = ( int )$field_id;
		$query_total_ins = '';
		if ( !$field_id )
			return false;

		switch ( $field_type )
		{
			case 'multi':

				$query_ins = "WHERE ( `field`.`presentation`='select' OR `field`.`presentation`='multiselect'
					OR `field`.`presentation`='multicheckbox' OR `field`.`presentation`='radio' )";
				break;

			case 'birthdate':
			//case 'date':

				$query_ins = "WHERE ( `field`.`presentation`='age_range' )";
				break;
			default:
				return array();
		}

		$query_total_ins .= ( $field_id ) ? sql_placeholder( $query_ins." AND `field`.`profile_field_id`!=? ", $field_id ) : $query_ins;

		$query = sql_placeholder( "
			( SELECT `field`.`profile_field_id`,`field`.`order` FROM`".TBL_PROF_FIELD_MATCH_LINK."` AS `match_field1`
			RIGHT JOIN `".TBL_PROF_FIELD."` AS `field` USING( `profile_field_id` )
			LEFT JOIN `".TBL_PROF_FIELD_MATCH_LINK."` AS `match_field2` ON `match_field2`.`match_profile_field_id`=`field`.`profile_field_id` ".$query_total_ins."
			AND (`match_field1`.`profile_field_id` IS NULL AND `match_field2`.`match_profile_field_id` IS NULL) )
			UNION
			( SELECT `field`.`profile_field_id`,`field`.`order` FROM `".TBL_PROF_FIELD."` AS `field`
			LEFT JOIN `".TBL_PROF_FIELD_MATCH_LINK."` AS `match_field` ON `field`.`profile_field_id` = `match_field`.`match_profile_field_id`
			".$query_ins." AND `match_field`.`profile_field_id`=?)
			ORDER BY `order`", $field_id );

		return MySQL::fetchArray( $query );
	}

	function GetFieldMatch( $field_id )
	{
		$field_id = ( int )$field_id;

		if ( !$field_id )
			return false;

		$query = sql_placeholder( "SELECT * FROM `".TBL_PROF_FIELD_MATCH_LINK."` WHERE `profile_field_id`=?", $field_id );

		return MySQL::fetchRow( $query );
	}

	function SetFieldMatch( $field_id, $match_field_id, $field_presentation )
	{
		$field_id		= ( int )$field_id;
		$match_field_id	= ( int )$match_field_id;

		if ( !$field_id )
			return false;

		$match_field_info	= $this->GetFieldInfo( $match_field_id );

		switch ( $field_presentation )
		{
			case 'select':
			case 'multiselect':
			case 'multicheckbox':
			case 'radio':

				$match_type = 'exact';
				break;

			case 'birthdate':
				$match_type = 'range';
				break;
			default:

				$query	= "DELETE FROM `".TBL_PROF_FIELD_MATCH_LINK."` WHERE `profile_field_id`='$field_id' OR `match_profile_field_id`='$field_id'";
				MySQL::fetchResource( $query );
				return false;
		}
		// check if field match exists
		$query = sql_placeholder( "SELECT `profile_field_id` FROM `".TBL_PROF_FIELD_MATCH_LINK."`
			WHERE `profile_field_id`=?", $field_id );

		$match_exists = MySQL::fetchField( $query );

		if ( !$match_field_id )
		{
			$query = sql_placeholder( "DELETE FROM `".TBL_PROF_FIELD_MATCH_LINK."`
				WHERE `profile_field_id`=?", $field_id );

			MySQL::fetchResource( $query );
			return 1;
		}

		if ( $match_exists )
			$query = sql_placeholder( "UPDATE `".TBL_PROF_FIELD_MATCH_LINK."`
				SET `match_profile_field_id`=?, `match_type`=?
				WHERE `profile_field_id`=?", $match_field_id, $match_type, $field_id );
		else
			$query = sql_placeholder( "INSERT INTO `".TBL_PROF_FIELD_MATCH_LINK."`( `profile_field_id`, `match_profile_field_id`, `match_type` )
				VALUES( ?@ )", array( $field_id, $match_field_id, $match_type ) );

		self::clearCache();

		return MySQL::affectedRows( $query );
	}

	public static function getNoneDisplayFieldPresentations()
	{
		return array( 'read_only', 'system_radio', 'system_checkbox', 'system_text', 'radius', 'array', 'timestamp', 'password' );
	}

	public static function getLocationFields()
	{
		return array( 'country_id', 'state_id', 'city_id', 'zip', 'custom_location' );
	}

	function getDisplayFieldPresentations()
	{
		return array( 'text', 'password', 'select', 'multiselect', 'textarea', 'date', 'birthdate', 'checkbox', 'multicheckbox', 'radio', 'age_range', 'fselect', 'fradio', 'callto', 'photo_upload', 'url', 'location', 'email' );
	}

	public static function getAllProfileFIelds()
	{
		$_query = "SELECT * FROM `".TBL_PROF_FIELD."`
			WHERE `presentation` IN ( '".implode( "', '", self::getDisplayFieldPresentations() )."' )";

		return MySQL::fetchArray( $_query );
	}

	public static function deleteAllFieldValues( $field_id )
	{

		$field_id = intval( $field_id );
		if ( !$field_id )
			return false;

		$_field_info = AdminProfileField::getFieldInfo( $field_id );

		$_query = sql_placeholder( "SELECT `value` FROM `".TBL_PROF_FIELD_VALUE."`
			WHERE `profile_field_id`=?", $field_id );
		$_value_arr = MySQL::fetchArray( $_query, 0 );

		foreach ( $_value_arr as $_value )
			SK_LanguageEdit::deleteKey('profile_fields.value', $_field_info['name'].'_'.$_value);

		$_query = sql_placeholder( "DELETE FROM `".TBL_PROF_FIELD_VALUE."`
			WHERE `profile_field_id`=?", $field_id );

		self::clearCache();
		return MySQL::affectedRows( $_query );
	}

    public static function moveLocationOrder( $location_field, $move )
	{
		if ( !in_array( $move, array( 'up', 'down' ) ) )
			return false;

		if ( !in_array( $location_field, AdminProfileField::getLocationFields() ) )
			return false;

		$_location_fields = explode( ',', SK_Config::section('profile_fields')->Section('location')->order );

		$_field_key = array_search( $location_field, $_location_fields );

		switch ( $move )
		{
			case 'up':
				if ( !$_field_key )
					return false;

				$_buffer = $_location_fields[$_field_key-1];
				$_location_fields[$_field_key-1] = $location_field;
				$_location_fields[$_field_key] = $_buffer;
				break;
			case 'down':
				if ( !$_location_fields[$_field_key+1] )
					return false;

				$_buffer = $_location_fields[$_field_key+1];
				$_location_fields[$_field_key+1] = $location_field;
				$_location_fields[$_field_key] = $_buffer;
				break;
		}
		adminConfig::SaveConfig('profile_fields.location','order',implode( ',', $_location_fields ));

		self::clearCache();

		return true;
	}

	public static function getFieldsByPresentation( $presentation, $include_system = false )
	{
		$presentation = trim( $presentation );

		if ( !strlen( $presentation ) )
			return array();

		$_where_term = ( !$include_system ) ? "AND `presentation` NOT IN ( '".implode( "','", AdminProfileField::getNoneDisplayFieldPresentations() )."' )
			AND `name` NOT IN ( '".implode( "','", AdminProfileField::getLocationFields() )."' )" : '';

		$_query = sql_placeholder( "SELECT * FROM `".TBL_PROF_FIELD."`
			WHERE `presentation`=? $_where_term", $presentation );

		return MySQL::fetchArray( $_query );
	}

	public static function getDependedFieldValues( $field_id, $reliant_field_id )
	{
		$field_id = intval( $field_id );
		$reliant_field_id = intval( $reliant_field_id );

		if ( !$field_id || !$reliant_field_id )
			return array();

		$_query = sql_placeholder( "SELECT `value` FROM `".TBL_PROFILE_FIELD_DEPENDENCE."`
			WHERE `profile_field_id` = ? AND `depended_profile_field_id` = ? ", $reliant_field_id, $field_id );

		return MySQL::fetchArray( $_query, 0 );
	}

	function updateFieldDependence( $reliant_field_id, $depended_values_arr, $depended_field_id )
	{
		$reliant_field_id = intval( $reliant_field_id );
		$depended_field_id = intval( $depended_field_id );

		if ( !$reliant_field_id || !$depended_field_id )
			return false;


		$_query = sql_placeholder( "DELETE FROM `".TBL_PROFILE_FIELD_DEPENDENCE."`
			WHERE `depended_profile_field_id`=?", $depended_field_id );

		MySQL::fetchResource( $_query );
		if ( $depended_values_arr && $reliant_field_id )
			foreach ( $depended_values_arr as $_depended_value )
			{
				$_query = sql_placeholder( "INSERT INTO `".TBL_PROFILE_FIELD_DEPENDENCE."` ( `profile_field_id`, `value`, `depended_profile_field_id` )
					VALUES( ?@ )", array( $reliant_field_id, $_depended_value, $depended_field_id ) );
				MySQL::fetchResource( $_query );
			}
		self::clearCache();

		$on_join = (bool)SK_MySQL::query("
			SELECT COUNT(*) FROM `" . TBL_PROF_FIELD_PAGE_LINK . "` WHERE
				`profile_field_id`=$depended_field_id
				AND `profile_field_page_type`='join'
				AND `number`=1
		")->fetch_cell();

		if ($on_join && count($depended_values_arr)) {
			AdminFrontend::registerMessage("
				This field will not show up on the first step of the Join Page as far as it depends on member's sex.<br /> You should move this field to another step (step 2, step 3, etc.).
			", 'notice');
		}

		return true;
	}

	public static function clearCache()
	{
		$components = array(
			'Join', 'EditProfile', 'MainSearch', 'SignUp', 'QuickSearch'
		);

		foreach ($components as $item) {
			$cmp_name = "component_$item";
			$cmp = new $cmp_name;
			$cmp->clear_compiled_tpl();
		}
	}

	/**#@-*/
}

class SK_AdminProfileFieldException extends Exception {}
