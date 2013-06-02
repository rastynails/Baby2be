<?php

class app_FieldForm
{
	const FORM_JOIN = "join";
	const FORM_EDIT = "edit";
	const FORM_SEARCH = "search";

	public static function formFields($form)
	{

		$query	= SK_MySQL::placeholder("
				SELECT `fields`.`name`, `fields`.`profile_field_id`  FROM `" . TBL_PROF_FIELD_PAGE_LINK . "` AS `form`
				JOIN `" . TBL_PROF_FIELD . "` AS `fields` USING(`profile_field_id`)
				WHERE `form`.`profile_field_page_type`='?'
				ORDER BY `fields`.`order`
			", $form );


		$result = SK_MySQL::query($query);

		$out = array();
		while ($item = $result->fetch_assoc()) {
			$out[$item["profile_field_id"]] = $item["name"];
		}

		return $out;
	}

	public static function getRequredFields( $profileId = null )
	{
            $sex = null;
            if ( !empty($profileId) )
            {
                $sex = app_Profile::getFieldValues($profileId, 'sex');
            }

	    $fields = self::formFields(self::FORM_EDIT);
	    $out = array();
	    foreach ( $fields as $f )
	    {
	        $prf = SK_ProfileFields::get($f);

	        if ( $prf->required_field && ( !empty($sex) && $prf->getPermission() & intval($sex) ) )
	        {
	            $out[] = $f;
	        }
	    }

	    return $out;
	}

	public static function formStepFields($form, $reliant_field_value = null)
	{
		$add_where = '';

		if (isset($reliant_field_value)) {
			$reliant_field_value = $reliant_field_value === false ? null : $reliant_field_value;
			$hided_field_list = self::hidedDependedFields( "sex", $reliant_field_value );
			if (count($hided_field_list)) {
				$add_where = "AND `fields`.`name` NOT IN ( '".implode( "','", $hided_field_list )."' )";
			}
		}

		$query	= SK_MySQL::placeholder("
			SELECT `form`.`number`, `fields`.`profile_field_id` AS `id`, `fields`.`name` FROM `".TBL_PROF_FIELD_PAGE_LINK."` AS `form`
			JOIN `" . TBL_PROF_FIELD . "` AS `fields` USING(`profile_field_id`)
			LEFT JOIN `".TBL_PROF_FIELD_SECTION."` AS `sections` USING( `profile_field_section_id` )
			WHERE `form`.`profile_field_page_type`='?' $add_where
			ORDER BY `sections`.`order`, `fields`.`order`", $form );
		$result = SK_MySQL::query($query);
		$out = array();
		while ($item = $result->fetch_object()) {
			$out[$item->number][$item->id] = $item->name;
		}

		$out = array_filter($out, "count");
		ksort($out);
		return $out;
	}

	public static function formSteps($form, $reliant_field_value=null) {
		$steps = self::formStepFields($form, $reliant_field_value);
		$steps = array_filter($steps, "count");
		$out = array_keys($steps);
		asort($out);
		return $out;
	}


	public static function hidedDependedFields( $field_name, $field_value )
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

}