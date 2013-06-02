<?php

function searchLangKeys($phrase, $search_key, $search_value) {
	
	$_search = array( '%', '_', '*' );
	$_replace = array( '\\%', '\\_', '%' );
	
	$_phrase = str_replace( $_search, $_replace, ' '.mysql_real_escape_string( trim( $phrase ) ).' ' );
	
	$_phrase = str_replace( ' ', '%', $_phrase );

	$_where_definition = array();
	if( $search_key )
		$_where[] = '`key` LIKE ?phrase';
	if( $search_value )
		$_where[] = '`value` LIKE ?phrase';
	
	$_where_definition = empty( $_where ) ? '1' : implode( ' OR ', $_where );
	
	$def_lang = SK_Config::section('languages')->get('default_lang_id');

    $query = sql_placeholder( "SELECT `ls`.`section` , `ls`.`parent_section_id`, `ls`.`lang_section_id`, `lk`.`lang_key_id`, `lk`.`key`, `lv`.`value`
        FROM `".TBL_LANG_SECTION."` AS `ls` 
        LEFT JOIN `".TBL_LANG_KEY."` AS `lk` ON (`ls`.`lang_section_id`=`lk`.`lang_section_id`)
        LEFT JOIN `".TBL_LANG_VALUE."` AS `lv` ON (`lk`.`lang_key_id`=`lv`.`lang_key_id`) 
        INNER JOIN `".TBL_LANG."` AS `l`ON (`l`.`lang_id` = `lv`.`lang_id`)
        WHERE (".$_where_definition.")",
        array( 'phrase' => $_phrase )  );

	$keys = array();
	
	$res = SK_MySQL::query($query);
	
	while ($row = $res->fetch_assoc()) {
		$path = '';
		if ($row['parent_section_id'] != 0) {
			$path = $row['section'];
			$parent_sect = $row['parent_section_id'];
			do {
                $query = sql_placeholder( "SELECT `ls`.`parent_section_id`, `ls`.`section`, `ls`.`lang_section_id`, 
                    `lk`.`lang_key_id`, `lk`.`key`
                    FROM `".TBL_LANG_SECTION."` AS `ls`
                    LEFT JOIN `".TBL_LANG_KEY."` AS `lk` ON (`ls`.`lang_section_id`=`lk`.`lang_section_id`) 
                    WHERE `ls`.`lang_section_id` = ?", $parent_sect );

				$parent = SK_MySQL::query($query)->fetch_assoc();
				$parent_sect = $parent['parent_section_id'];

				$end = strlen($path) == 0 ? "" : "." . $path;
				$path = $parent['section'] . $end;
			}
			while ( $parent_sect != 0);
			
			$row['path'] = $path;
		}
		else $row['path'] = $row['section'];
		
		$keys[] = $row;
	}
	
	return $keys;
}


function getMissingLabels($lang_id)
{
	$languages_num = count(SK_LanguageEdit::getLanguages());
	
	$missing_labels = MySQL::FetchArray( "SELECT `lang_key_id`
		FROM `".TBL_LANG_KEY."`
		WHERE `lang_key_id` NOT IN 
		(SELECT `lang_key_id` from `".TBL_LANG_VALUE."` WHERE `lang_id`=".$lang_id." AND `value` != '')");

	if (is_array($missing_labels)) {
		
		foreach( $missing_labels as $id => $num )
		{
            $query = "SELECT `section` , `parent_section_id`, `ls`.`lang_section_id`, 
                `lk`.`lang_key_id`, `key`, `value`
                FROM `".TBL_LANG_SECTION."` AS `ls` 
                LEFT JOIN `".TBL_LANG_KEY."` AS `lk` ON(`ls`.`lang_section_id`=`lk`.`lang_section_id`)
                LEFT JOIN `".TBL_LANG_VALUE."` AS `lv` ON(`lk`.`lang_key_id`=`lv`.`lang_key_id`) 
                WHERE `lk`.`lang_key_id`=".$num['lang_key_id']." GROUP BY `lang_key_id`";

			$res = SK_MySQL::query($query);

			while ($row = $res->fetch_assoc())
			{
				$path = '';
				if ($row['parent_section_id'] != 0) {
					$path = $row['section'];
					$parent_sect = $row['parent_section_id'];
					do {
						$query = "SELECT `parent_section_id`, `section`, `ls`.`lang_section_id`, 
							`lk`.`lang_key_id`, `key`
							FROM `".TBL_LANG_SECTION."` AS `ls`
							LEFT JOIN `".TBL_LANG_KEY."` AS `lk` ON(`ls`.`lang_section_id`=`lk`.`lang_section_id`) 
							WHERE `ls`.`lang_section_id` =" . $parent_sect ;
		
						$parent = SK_MySQL::query($query)->fetch_assoc();
						$parent_sect = $parent['parent_section_id'];
		
						$end = strlen($path) == 0 ? "" : "." . $path;
						$path = $parent['section'] . $end;
					}
					while ( $parent_sect != 0);
					
					$row['path'] = $path;
				}
				else $row['path'] = $row['section'];
				
				$keys['labels'][] = $row;
			}
		}
		$keys['total'] = count($keys['labels']);
	}
	else { 
		$keys = array();
		$keys['total'] = 0;
	}
		
	return $keys;
}
