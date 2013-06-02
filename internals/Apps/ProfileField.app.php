<?php

/**
 * Class for working with profile fields
 *
 * @package SkaDate
 * @subpackage SkaDate5
 * @link http://www.skadate.com
 * @version 5.0
 */
class app_ProfileField
{
    
        public static $birthdateFieldsCache = array();
	/**
	 * Get all steps of form type
	 *
	 * For exam: get steps on join form.
	 * On view page cut steps where profile have all empty fields on step
	 *
	 * @param string $form_type Type of form
	 *
	 * @return array
	 */
    
	public static function formSteps( $form )
	{
		$query	= SK_MySQL::placeholder("SELECT `number` FROM `".TBL_PROF_FIELD_PAGE."`
			WHERE `profile_field_page_type`='?'
			GROUP BY `number`", $page_type );

		$result = SK_MySQL::query($query);

		if (isset($profile_id)) {
			$reliant_value = app_Profile::getFieldValues($profile_id, 'sex');
		}

		$out = array();
		while ($item = $result->fetch_cell()) {
			if (isset($reliant_value)) {
				$fields = self::getPageFields($page_type, $item, $reliant_value);
				if (count($fields) < 1) {
					continue;
				}
			}
			$out[] = $item;
		}

		return $out;
	}




	/**
	 * Get all steps of page type
	 *
	 * For exam: get steps on join page.
	 * On view page cut steps where profile have all empty fields on step if profile id
	 * is specified
	 *
	 * @param string $page_type Type of page
	 *
	 * @return array
	 */
	public static function getPageSteps( $page_type, $profile_id = null )
	{
		$query	= SK_MySQL::placeholder("SELECT `number` FROM `".TBL_PROF_FIELD_PAGE."`
			WHERE `profile_field_page_type`='?'
			GROUP BY `number`", $page_type );

		$result = SK_MySQL::query($query);

		if (isset($profile_id)) {
			$reliant_value = app_Profile::getFieldValues($profile_id, 'sex');
		}

		$out = array();
		while ($item = $result->fetch_cell()) {
			if (isset($reliant_value)) {
				$fields = self::getPageFields($page_type, $item, $reliant_value);
				if (count($fields) < 1) {
					continue;
				}
			}
			$out[] = $item;
		}

		return $out;
	}


	/**
	 * Get all profile fields on specified step of page type
	 *
	 * @param string $page_type Type of page
	 * @param integer $number Page step
	 *
	 * @return array
	 */
	public static function getPageFields( $page_type, $number, $reliant_field_value=null, $sections=false )
	{
		$reliant_field = self::getReliantField();
		$add_where = '';
		if ($page_type!="search") {
			$hided_field_list = self::getHidedDependedFields( $reliant_field, $reliant_field_value );
			if (count($hided_field_list)) {
				$add_where = "AND `fields`.`name` NOT IN ( '".implode( "','", $hided_field_list )."' )";
			}
		}

		$query	= SK_MySQL::placeholder( "SELECT `fields`.`name`,`fields`.`profile_field_id`,`fields`.`profile_field_section_id` FROM `".TBL_PROF_FIELD_PAGE_LINK."` AS `page_link`
			LEFT JOIN `".TBL_PROF_FIELD."` AS `fields` USING( `profile_field_id` )
			LEFT JOIN `".TBL_PROF_FIELD_SECTION."` AS `sections` USING( `profile_field_section_id` )
			WHERE `page_link`.`profile_field_page_type`='?' AND `page_link`.`number`=? $add_where
			ORDER BY `sections`.`order`, `fields`.`order`", $page_type, $number );

		$result = SK_MySQL::query($query);
		$fields = array();
		while ($item = $result->fetch_object()) {
			if ($sections) {
				$fields[$item->profile_field_section_id][$item->profile_field_id]=$item->name;
			}
			else {
				$fields[$item->profile_field_id]=$item->name;
			}
		}

		return $fields;
	}

	public static function getReliantField()
	{
		static $field;

		if (isset($field)) {
			return $field;
		}
		$query = "SELECT `t2`.`name` FROM `".TBL_PROFILE_FIELD_DEPENDENCE."` AS `t1`
			LEFT JOIN `".TBL_PROFILE_FIELD."` AS `t2` USING( `profile_field_id` )
			GROUP BY `t1`.`profile_field_id`
			LIMIT 1";

		$field = SK_MySQL::query($query)->fetch_cell();
		return $field;
	}

	public static function getHidedDependedFields( $field_name, $field_value )
	{
		$field_name = trim( $field_name );
		$field_value = intval( $field_value );

		if ( !$field_name )
			return array();

		$_where_cond = ( $field_value ) ? "`t2`.`name` =? AND `t1`.`value` !=?" : "`t2`.`name` =?";

		$_query = sql_placeholder( "SELECT `t3`.`name` FROM `".TBL_PROFILE_FIELD_DEPENDENCE."` AS `t1`
			LEFT JOIN `".TBL_PROFILE_FIELD."` AS `t2` USING( `profile_field_id` )
			LEFT JOIN `".TBL_PROFILE_FIELD."` AS `t3` ON( `t1`.`depended_profile_field_id` = `t3`.`profile_field_id` )
			WHERE $_where_cond
			AND `depended_profile_field_id` NOT IN ( SELECT `depended_profile_field_id` FROM `".TBL_PROFILE_FIELD_DEPENDENCE."` AS `t1_in`
				LEFT JOIN `".TBL_PROFILE_FIELD."` AS `t2_in` USING( `profile_field_id` )
				WHERE `t2_in`.`name` =? AND `t1_in`.`value` = ? )
			GROUP BY `depended_profile_field_id`", $field_name, $field_value, $field_name, $field_value );

		return MySQL::fetchArray( $_query, 0 );
	}

	public static function getDependedFields($page_type, $number, $reliant_field_value=null)
	{
		if (!isset($reliant_field_value)) {
			return array();
		}
		$reliant_field = self::getReliantField();
		$pr_field_id = SK_ProfileFields::get($reliant_field)->profile_field_id;


		$query = MySQL::placeholder("SELECT `fields`.`name` AS `field_id` FROM `".TBL_PROFILE_FIELD_DEPENDENCE."` AS `dp_fields`
									LEFT JOIN `".TBL_PROF_FIELD_PAGE_LINK."` AS `page`
									ON( `dp_fields`.`depended_profile_field_id` = `page`.`profile_field_id`)
									LEFT JOIN `".TBL_PROF_FIELD."` AS `fields`
									ON( `dp_fields`.`depended_profile_field_id` = `fields`.`profile_field_id`)
									WHERE `page`.`profile_field_page_type` = '?' AND `page`.`number`=?
									AND `dp_fields`.`profile_field_id`=? AND `dp_fields`.`value` = ?",
									$page_type, $number, $pr_field_id, $reliant_field_value);

		$result = MySQL::query($query);
		$out = array();
		while ($item = $result->fetch_cell()){
			$out[]=$item;
		}
		return $out;
	}

	/**
	 * Return the number of pages on type of page
	 * For example: if join form has 4 steps, this function return 4
	 *
	 * @param string $page_type
	 *
	 * @return integer
	 *
	 */
	public static function getPageCountSteps( $page_type )
	{
		$_query = sql_placeholder( "SELECT COUNT( `number` ) FROM `?#TBL_PROF_FIELD_PAGE`
			WHERE `profile_field_page_type`=?", $page_type );

		return MySQL::FetchField( $_query );
	}


	public static function TextualFields()
	{
		$query = "SELECT `name` FROM `".TBL_PROF_FIELD."`
			WHERE `presentation`='text' OR `presentation`='textarea'
			AND `name` NOT IN ( 'country_id', 'state_id', 'city_id', 'zip' )";
		$result = SK_MySQL::query($query);
		$out = array();

		while ($item = $result->fetch_cell()){
			$out[] = $item;
		}

		return $out;
	}


	/**
	 * Return array with profile fields
	 * which are must be hided and handled customly
	 *
	 * @return array
	 */
	public static function getSearchHideFieldNames()
	{
		return array( 'state_id', 'city_id', 'zip', 'radius', 'country_id', 'mcheck_country', 'custom_location', 'search_online_only', 'search_with_photo_only' );
	}

	/**
	 * Order location fields
	 *
	 * @param array $fields_arr
	 */
	public static function orderLocationFields( &$fields_arr )
	{

		$_location_fields = explode( ',', SK_Config::section("profile_fields")->Section('location')->order );

		$_arr_length = ( count( $_location_fields ) - 1);

		for ( $_j = 0; $_j < $_arr_length; $_j++ )
		for ( $_i = 0; $_i < $_arr_length; $_i++ )
		{
			$_temp_key = array_search( $_location_fields[$_i], $fields_arr );

			if ( $_temp_key || $_temp_key === 0 )
			{
				$_next_field_index = array_search( $_location_fields[$_i+1], $fields_arr );

				if ( $_next_field_index < $_temp_key )
				{
					$_buffer = $fields_arr[$_next_field_index];
					$fields_arr[$_next_field_index] = $fields_arr[$_temp_key];
					$fields_arr[$_temp_key] = $_buffer;
				}
			}
		}
	}

	public static function getReliantFieldValueFromSource( $source_array, $field_name = 'sex', $fieldname_prefix = '' )
	{
		return intval( $source_array[$fieldname_prefix.$field_name] ) ? intval( $source_array[$fieldname_prefix.$field_name] ) : null;
	}

	public static function getDependedFieldsOnCurrentJoinPage( $reliant_value, $current_page_number )
	{
		$reliant_value = intval( $reliant_value );
		$current_page_number = intval( $current_page_number  );

		$reliant_field_info = app_ProfileField::getReliantField();
		$reliant_field = $reliant_field_info['name'];

		if  ( !$reliant_field || !$reliant_value || !$current_page_number )
			return array();

		$_query	= sql_placeholder( "SELECT `t1`.`depended_profile_field_id` FROM `".TBL_PROFILE_FIELD_DEPENDENCE."` AS `t1`
			LEFT JOIN `".TBL_PROF_FIELD_PAGE_LINK."` AS `t2` ON ( `t1`.`depended_profile_field_id` = `t2`.`profile_field_id` )
			LEFT JOIN `".TBL_PROFILE_FIELD."` AS `t3` ON ( `t1`.`profile_field_id`=`t3`.`profile_field_id` )
			WHERE `t2`.`profile_field_page_type`='join' AND `t2`.`number`=? AND `t3`.`name` = ? AND `t1`.`value` = ?
			", $current_page_number, $reliant_field, $reliant_value );

		return MySQL::fetchArray( $_query, 0 );
	}


	/**
	 * Returns Form Field Object by field name
	 *
	 * @param string $name
	 * @return SK_FormField
	 */
	public static function FormField( $name )
	{
		if(!($name = trim($name)))
			return false;

		$profile_field = SK_ProfileFields::get($name);

		if(!$profile_field)
			return false;

		$field = null;

		if ( $profile_field->base_field && file_exists(DIR_FORM_FIELDS.$name.'.field.php') )
		{
			$field_class = 'field_'.$name;
			$field = new $field_class;
			return $field;
		}

		switch ($profile_field->presentation)
		{
			case 'callto':
			case 'text':
				$field = new fieldType_text($profile_field->name);
				break;

			case 'select':
			case 'radio':
				$field = new fieldType_select($profile_field->name);

				$field->setType($profile_field->presentation);

				$field->setValues($profile_field->values);

				$field->setColumnSize($profile_field->column_size);

				$field->label_prefix = $profile_field->matching ? $profile_field->matching : $profile_field->name;
				break;

			case 'multicheckbox':
			case 'multiselect':

				$field = new fieldType_set($profile_field->name);

				$field->setType($profile_field->presentation);

				$field->setValues($profile_field->values);

				$field->setColumnSize($profile_field->column_size);

				$field->label_prefix = $profile_field->matching ? $profile_field->matching : $profile_field->name;
				break;

			case 'age_range':
				$field = new fieldType_age_range($profile_field->name);
				break;

			case 'system_checkbox':
			case 'checkbox':
				$field = new fieldType_checkbox($profile_field->name);
				break;
		}




		return $field;
	}


	/**
	 * Return unique Chuppo key for
	 * specified profile ID
	 *
	 * @param integer $profile_id
	 * @return string
	 */
	public static function getProfileUniqueId( $profile_id )
	{
		if ( !$profile_id )
			return false;

		$query = SK_MySQL::placeholder( "SELECT `profile_chuppo_key` FROM `".TBL_PROFILE_CHUPPO_ID."`
			WHERE `profile_id`=?", $profile_id );

		return SK_MySQL::query($query)->fetch_cell();
	}

    public static function getProfileListBirthdateFields()
	{
        if ( !empty(self::$birthdateFieldsCache) )
        {
            return self::$birthdateFieldsCache;
        }

		$query = "SELECT `name` FROM `".TBL_PROF_FIELD."`
			WHERE `presentation`='birthdate' AND `profile_list_template` = 1 ";
		$result = SK_MySQL::query($query);
		$out = array();

		while ($item = $result->fetch_cell()){
			$out[] = $item;
		}

        self::$birthdateFieldsCache = $out;

		return $out;
	}

    public static function profileFieldChanged($profile_id, $profile_field, $value)
    {
        if ($profile_field->base_field)
        {
            $query = SK_MySQL::placeholder("SELECT `".$profile_field->name."` FROM `".TBL_PROFILE."` WHERE `profile_id`=?", $profile_id);
        }
        else
        {
            $query = SK_MySQL::placeholder("SELECT `".$profile_field->name."` FROM `".TBL_PROFILE_EXTEND."` WHERE `profile_id`=?", $profile_id);
        }

        $prevValue = SK_MySQL::query($query)->fetch_cell();

        if ($value != $prevValue)
        {
            return true;
        }

        return false;

    }

    public static function markProfileFieldForReview($profile_id, $profile_field_id)
    {
        if (SK_Config::section('site')->Section('additional')->Section('profile')->profile_review_enabled)
        {
            $query = SK_MySQL::placeholder("INSERT INTO `".TBL_PROFILE_FIELD_REVIEW."` (`profile_id`, `profile_field_id`) VALUES (?, ?) ", $profile_id, $profile_field_id);
            SK_MySQL::query($query);

            return SK_MySQL::affected_rows();
        }
        
        return false;
    }

    public static function clearMarkedProfileFields($profile_id)
    {
        $query = SK_MySQL::placeholder("DELETE FROM `".TBL_PROFILE_FIELD_REVIEW."` WHERE `profile_id`=?", $profile_id);
        SK_MySQL::query($query);

        return SK_MySQL::affected_rows();
    }

    public static function getMarkedProfileFields($profile_id)
    {
        $query =  SK_Mysql::placeholder("SELECT `profile_field_id` FROM `".TBL_PROFILE_FIELD_REVIEW."` WHERE `profile_id`=?", $profile_id);
        $list = MySQL::fetchArray($query, 'profile_field_id');
        $info = array();
        foreach($list as $id=>$item)
        {
            $info[] = $id;
        }
        return $info;
    }
}
?>
