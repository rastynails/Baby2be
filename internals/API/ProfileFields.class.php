<?php

class SK_ProfileFields
{
    private static $fields = array();

	public static function construct()
	{
		$get_pr_fields_query = "SELECT `field`.*, `match_field_name`.`name` AS `matching`, `match_field`.`match_type` AS `matching_type`
								FROM `".TBL_PROF_FIELD."` AS `field`
								LEFT JOIN `".TBL_PROF_FIELD_MATCH_LINK."` AS `match_field` ON `field`.`profile_field_id`=`match_field`.`match_profile_field_id`
								LEFT JOIN `".TBL_PROF_FIELD."` AS `match_field_name` ON `match_field`.`profile_field_id`=`match_field_name`.`profile_field_id`";

		$result = SK_MySQL::query($get_pr_fields_query);

		$compiled_pr_fields_val_query = SK_MySQL::compile_placeholder( "SELECT `value` FROM `".TBL_PROF_FIELD_VALUE."` WHERE
																`profile_field_id`=? ORDER BY `order`" );

        $fields = array();
        $fieldValues = array();

		while ( $row = $result->fetch_object('ProfileField') )
        {
			self::$fields[$row->name] = $row;
            $fields[] = $row->profile_field_id;
        }

        if( !empty($fields) )
        {
            $result = SK_MySQL::queryForList(SK_MySQL::placeholder( "SELECT * FROM `".TBL_PROF_FIELD_VALUE."` WHERE `profile_field_id` IN (?@) ORDER BY `order`", $fields));

            foreach ( $result as $value )
            {
                if( !isset($fieldValues[$value['profile_field_id']]) )
                {
                    $fieldValues[$value['profile_field_id']] = array();
                }

                $fieldValues[$value['profile_field_id']][] = $value['value'];
            }            
        }

		foreach ( self::$fields as $field )
		{
			if( $field->matching )
			{
				$match_field = self::$fields[$field->matching];
				$profile_field_id = $match_field->profile_field_id;
				$field->match_field = $match_field;

				//$field->column_size = $match_field->column_size;
			}
			else
            {
				$profile_field_id = $field->profile_field_id;
            }

            if( isset($fieldValues[$profile_field_id]) )
            {
                $field->values = $fieldValues[$profile_field_id];
            }

//			$pr_fields_val_query = SK_MySQL::placeholder($compiled_pr_fields_val_query, $profile_field_id);
//			$_result = SK_MySQL::query($pr_fields_val_query);
//
//			while ($value = $_result->fetch_cell())
//				$field->values[]= $value;
		}
	}



	/**
	 * Get a profile field.
	 *
	 * @param string $field_name
	 * @return ProfileField
	 */
	public static function get( $field_name )
	{
		if(isset(self::$fields[$field_name]))
			return self::$fields[$field_name];
		throw new SK_ProfileFieldException('Undefined profile field `'.$field_name.'`');

	}

	public static function field_list()
	{
		return self::$fields;
	}

	private static $full_permission;
	public static function fullPermissions() {
		if (isset(self::$full_permission)) {
			return self::$full_permission;
		}
		$out = 0;
		foreach (self::get('sex')->values as $value) {
			$out += $value;
		}
		return (int)$out;
	}

	public static function permissions($field_name) {
		$pr_f = self::get($field_name);

		$query = SK_MySQL::placeholder(
			"SELECT `value` FROM `" . TBL_PROFILE_FIELD_DEPENDENCE . "` WHERE `depended_profile_field_id`=?"
		, $pr_f->profile_field_id);

		$result = SK_MySQL::query($query);
		if (!$result->num_rows()) {
			return self::fullPermissions();
		}
		$out = 0;
		while ($item = $result->fetch_cell()) {
			$out += (int) $item;
		}
		return (int) $out;
	}
}


class ProfileField
{
	public $name;
	public $values = array();
	public $profile_field_id;
	public $profile_field_section_id;
	public $regexp;
	public $required_field;
	public $join_page;
	public $editable_by_member;
	public $viewable_by_member;
	public $searchable;
	public $confirm;
	public $column_size;
	public $order;
	public $matching;
	public $matching_type;
	public $base_field;
	public $presentation;
	public $custom;

	/**
	 * Match field object
	 *
	 * @var ProfileField
	 */
	public $match_field;

	private $permitions;

        private static $sections = array();

	public function __construct(){}


	public function getPermission() {
		if (isset($this->permitions)) {
			return $this->permitions;
		}
		return $this->permitions = SK_ProfileFields::permissions($this->name);
	}

        public function getSection()
        {
            if ( empty( self::$sections[$this->profile_field_section_id] ) )
            {
                $query = SK_MySQL::placeholder("SELECT * FROM `" . TBL_PROF_FIELD_SECTION . "` WHERE profile_field_section_id=?", $this->profile_field_section_id);
                self::$sections[$this->profile_field_section_id] = SK_MySQL::query($query)->fetch_assoc();
            }

            return self::$sections[$this->profile_field_section_id];
        }
}

class SK_ProfileFieldException extends Exception {}


SK_ProfileFields::construct();