<?php

class app_FieldView
{
	public static function steps()
	{
		$result	= SK_MySQL::query("SELECT `number` FROM `".TBL_PROF_FIELD_PAGE."`
			WHERE `profile_field_page_type`='view' 
			GROUP BY `number`" );
		$out = array();
		while ($item = $result->fetch_cell()) {
			$out[] = $item;
		}
		return $out;
	}
	
	public static function fields( $step = null, $sections=false )
	{
		$step_cond = '1';
		
		if (isset($step)) {
			$step_cond = SK_MySQL::placeholder("`page_link`.`number`=?", $step);
		}
		$query	= SK_MySQL::placeholder( "SELECT `fields`.`name`,`fields`.`profile_field_id`,`fields`.`profile_field_section_id`, `page_link`.`number` FROM `".TBL_PROF_FIELD_PAGE_LINK."` AS `page_link` 
			INNER JOIN `".TBL_PROF_FIELD."` AS `fields` USING( `profile_field_id` ) 
			INNER JOIN `".TBL_PROF_FIELD_SECTION."` AS `sections` USING( `profile_field_section_id` )
			WHERE `page_link`.`profile_field_page_type`='view' AND $step_cond
			ORDER BY `sections`.`order`, `fields`.`order`", $step );
		
		$result = SK_MySQL::query($query);
		$fields = array();
		while ($item = $result->fetch_object()) {
			if ($sections) {
				if (!isset($step)) {
					$fields[$item->number][$item->profile_field_section_id][$item->profile_field_id]=$item->name;
				} else {
					$fields[$item->profile_field_section_id][$item->profile_field_id]=$item->name;
				}
			}
			else {
				if (!isset($step)) {
					$fields[$item->number][$item->profile_field_id]=$item->name;
				} else {
					$fields[$item->profile_field_id]=$item->name;
				}
			}
		}
		
		return $fields;
	}
	
}