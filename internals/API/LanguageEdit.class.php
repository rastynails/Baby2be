<?php

register_shutdown_function(array('SK_LanguageEdit', 'destruct'));

class SK_LanguageEdit
{
	private static $changes_affected = false;
	
	/**
	 * Get section id using section path.
	 *
	 * @param string $section_path dot separated
	 * @return integer section_id
	 */
	private static function parseSectionPath( $section_path )
	{
		$query_c = SK_MySQL::compile_placeholder(
			'SELECT `lang_section_id` FROM `'.TBL_LANG_SECTION.'`
				WHERE `parent_section_id`=? AND `section`="?"'
		);
		
		$_section_id = 0;
		$_path = explode('.', $section_path);
		foreach ( $_path as $section )
		{
			$result = SK_MySQL::query(
				SK_MySQL::placeholder($query_c, $_section_id, $section)
			);
			
			if ( !$result->num_rows() ) {
				throw new SK_LanguageEditException(
					'Unknown section "'.htmlspecialchars($section).'" in path "'.htmlspecialchars($section_path).'"',
					SK_LanguageEditException::SECTION_NOT_EXISTS
				);
			}
			
			$_section_id = $result->fetch_cell();
			
			$result->free();
		}
		
		return $_section_id;
	}
	
	/**
	 * Get section id using section path.
	 *
	 * @param string $section_path
	 * @return integer
	 */
	public static function section_id($section_path) {
		return self::parseSectionPath($section_path);
	}
	
	/**
	 * Get all available languages data.
	 *
	 * @return array id-indexed objects list
	 */
	public static function getLanguages()
	{
		static $languages;
		
		if ( isset($languages) ) {
			return $languages;
		}
		
		$default_lang_id = SK_Config::Section('languages')->default_lang_id;
		
		$result = SK_MySQL::query('SELECT * FROM `'.TBL_LANG.'`');
		
		$languages = array();
		while ( $row = $result->fetch_object() )
		{
			if ( $row->lang_id == $default_lang_id ) {
				$row->default = true;
			}
			
			$languages[$row->lang_id] = $row;
		}
		
		$result->free();
		
		return $languages;
	}
	
	/**
	 * Get sections info list.
	 *
	 * @param integer $parent_section_id
	 * @return array id-indexed objects list
	 */
	public static function getSections( $parent_section_id )
	{
		if ( $parent_section_id != 0 )
		{
			// checking for argued section existence
			$query = SK_MySQL::placeholder(
				'SELECT `section` FROM `'.TBL_LANG_SECTION.'`
					WHERE `lang_section_id`=?', $parent_section_id
			);
			
			if ( !SK_MySQL::query($query)->num_rows() ) {
				throw new SK_LanguageEditException(
					'Section with argued `$parent_section_id` does not exist',
					SK_LanguageEditException::SECTION_NOT_EXISTS
				);
			}
		}
		
		
		// getting sections
		$query = SK_MySQL::placeholder(
			'SELECT * FROM `'.TBL_LANG_SECTION.'`
				WHERE `parent_section_id`=?', $parent_section_id
		);
		
		$result = SK_MySQL::query($query);
		
		if ( !$result->num_rows() ) {
			return array();
		}
		
		$sections = array();
		
		while ( $_sect = $result->fetch_object() ) {
			$_sect->has_children = false;
			$sections[$_sect->lang_section_id] = $_sect;
		}
		
		$result->free();
		
		
		// find out a child sections existence
		$query = SK_MySQL::placeholder(
			'SELECT DISTINCT `parent_section_id`
				FROM `'.TBL_LANG_SECTION.'`
				WHERE `parent_section_id` IN(?@)'
			, array_keys($sections)
		);
		
		$result = SK_MySQL::query($query);
		
		while ( $section_id = $result->fetch_cell() ) {
			$sections[$section_id]->has_children = true;
		}
		
		$result->free();
		
		return $sections;
	}
	
	/**
	 * Get a key name using $lang_key_id.
	 *
	 * @param integer $lang_key_id
	 * @return string key name or FALSE if there are no key with such id
	 */
	public static function getKey( $lang_key_id )
	{
		$query = SK_MySQL::placeholder(
			'SELECT `key` FROM `'.TBL_LANG_KEY.'`
				WHERE `lang_key_id`=?', $lang_key_id
		);
		
		return SK_MySQL::query($query)->fetch_cell();
	}
	
	/**
	 * Format a language value text escaping it html enrties.
	 *
	 * @param string $text
	 * @return string html formated text
	 */
	private static function formatValues( $text )
	{
		$text = SK_Language::htmlspecialchars($text);
		
		$text = preg_replace(
			'~\{\$\w+\}~',
			'<span class="text_var">\\0</span>',
			$text
		);
		
		$text = nl2br($text);
		
		return $text;
	}
	
	/**
	 * Get a key-indexed language key nodes list.
	 *
	 * @param integer $section_id
	 * @param boolean $format_values
	 * @return array lang_key_id-indexed objects list of SK_LanguageKeyNode
	 */
	public static function getKeyNodes( $section_id, $format_values = false )
	{
		$query = SK_MySQL::placeholder(
			'SELECT `tbl_key`.`lang_key_id`,`key`,`lang_id`,`value`
				FROM `'.TBL_LANG_KEY.'` `tbl_key`
					LEFT JOIN `'.TBL_LANG_VALUE.'` `tbl_val` USING(`lang_key_id`)
				WHERE `lang_section_id`=? ORDER BY `key`'
			, $section_id
		);
		
		$result = SK_MySQL::query($query);
		
		$key_nodes = array();
		while ( $row = $result->fetch_object() )
		{
			if ( !isset($key_nodes[$row->lang_key_id]) )
			{
				$node = new SK_LanguageKeyNode();
				$node->lang_key_id = $row->lang_key_id;
				$node->key = $row->key;
				$node->values = array();
				
				$key_nodes[$row->lang_key_id] = $node;
			}
			
			$key_nodes[$row->lang_key_id]->values[$row->lang_id] =
				!$format_values ? $row->value : self::formatValues($row->value);
		}
		
		return $key_nodes;
	}
	
	/**
	 * Get a single key node object.
	 *
	 * @param integer $lang_key_id
	 * @param boolean $format_values
	 * @return SK_LanguageKeyNode
	 */
	public static function getKeyNode( $lang_key_id, $format_values = false )
	{
		// getting key
		$key = self::getKey($lang_key_id);
		
		if ( !$key ) {
			throw new SK_LanguageEditException(
				'a key with $lang_key_id does not exist',
				SK_LanguageException::KEY_NOT_EXISTS
			);
		}
		
		// getting values
		$query = SK_MySQL::placeholder(
			'SELECT `lang_id`, `value`
				FROM `'.TBL_LANG_VALUE.'`
				WHERE `lang_key_id`=?'
			, $lang_key_id
		);
		
		$result = SK_MySQL::query($query);
		
		$values = array();
		while ( $row = $result->fetch_object() ) {
			$values[$row->lang_id] =
				!$format_values ? $row->value : self::formatValues($row->value);
		}
		
		$key_node = new SK_LanguageKeyNode();
		$key_node->lang_key_id = $lang_key_id;
		$key_node->key = $key;
		$key_node->values = $values;
		
		return $key_node;
	}
	
	/**
	 * Setup a language key with values.
	 * Language key will automatically created if it's need.
	 *
	 * @param integer|string $section id or path
	 * @param string $key
	 * @param array $values lang_id=>value pairs
	 */
	public static function setKey( $section, $key, array $values )
	{
		// checking params
		if ( !$section ) {
			throw new SK_LanguageEditException(
				'argument $section value is empty or equals zero',
				SK_LanguageEditException::EMPTY_ARGUMENT_SECTION
			);
		}
		
		if ( !strlen($key) ) {
			throw new SK_LanguageEditException(
				'empty argument $key',
				SK_LanguageEditException::EMPTY_ARGUMENT_KEY
			);
		}
		
		// getting section_id
		if ( !is_numeric($section) ) {
			$section_id = self::parseSectionPath($section);
		}
		else { // setting by section_id
			$section_id = (int)$section;
			
			$query = SK_MySQL::placeholder(
				'SELECT `section` FROM `'.TBL_LANG_SECTION.'`
					WHERE `lang_section_id`=?', $section_id
			);
			
			if ( !SK_MySQL::query($query)->num_rows() ) {
				throw new SK_LanguageEditException(
					'Section with argued `$section_id` does not exist',
					SK_LanguageEditException::SECTION_NOT_EXISTS
				);
			}
		}
		
		
		// checking lang_key for existense
		$query = SK_MySQL::placeholder(
			'SELECT `lang_key_id` FROM `'.TBL_LANG_KEY.'`
				WHERE `lang_section_id`=? AND `key`="?"'
			, $section_id, $key
		);
		
		$result = SK_MySQL::query($query);
		
		if ( $result->num_rows() ) {
			$lang_key_id = $result->fetch_cell();
			$result->free();
		}
		else {
			$query = SK_MySQL::placeholder(
				'INSERT INTO `'.TBL_LANG_KEY.'`
					SET `lang_section_id`=?, `key`="?"'
				, $section_id, $key
			);
			
			try {
				// inserting key
				SK_MySQL::query($query);
			}
			catch ( SK_MySQL_Exception $e ) { // TODO: handling duplicate entry...
				printArr($e);
				throw $e;
			}
			
			$lang_key_id = SK_MySQL::insert_id();
		}
		
		// setting values
		self::updateKeyValues($lang_key_id, $values);
	}
	
	/**
	 * Update an existent key values using $lang_key_id.
	 *
	 * @param integer $lang_key_id
	 * @throws SK_LanguageEditException
	 */
	public static function updateKeyValues( $lang_key_id, array $values )
	{
		if ( !($lang_key_id = (int)$lang_key_id) ) {
			throw new SK_LanguageEditException(
				'empty argument $lang_key_id',
				SK_LanguageEditException::EMPTY_ARGUMENT_KEY_ID
			);
		}
		
		// getting existent values
		$existent_values = array();
		
		$query = SK_MySQL::placeholder(
			'SELECT `lang_id`, `value`
				FROM `'.TBL_LANG_VALUE.'` WHERE `lang_key_id`=?',
			$lang_key_id
		);
		
		$result = SK_MySQL::query($query);
		if ( $result->num_rows() )
		{
			while ( $row = $result->fetch_object() ) {
				$existent_values[$row->lang_id] = $row->value;
			}
		}
		else {
			// checking for key existence
			if ( !SK_LanguageEdit::getKey($lang_key_id) ) {
				throw new SK_LanguageEditException(
					'a key with id '.$lang_key_id.' does not exist',
					SK_LanguageEditException::KEY_NOT_EXISTS
				);
			}
		}
		$result->free();
		
		
		$insert_query = SK_MySQL::compile_placeholder(
			"INSERT INTO `".TBL_LANG_VALUE."`(`lang_key_id`,`value`,`lang_id`) VALUES($lang_key_id, '?', ?)"
		);
		
		$update_query = SK_MySQL::compile_placeholder(
			"UPDATE `".TBL_LANG_VALUE."` SET `value`='?' WHERE `lang_key_id`=$lang_key_id AND `lang_id`=?"
		);
		
		$delete_query = SK_MySQL::compile_placeholder(
			"DELETE FROM `".TBL_LANG_VALUE."` WHERE `lang_key_id`=$lang_key_id AND `lang_id`=?"
		);
		
		
		$lang_ids = array_keys(self::getLanguages());
		foreach ( $lang_ids as $lang_id )
		{
			if ( !isset($values[$lang_id]) )
			{
				if ( !isset($existent_values[$lang_id]) ) {
					continue;
				}
				else {
					$aff_query = SK_MySQL::placeholder($delete_query, $lang_id);
				}
			}
			elseif ( isset($existent_values[$lang_id]) )
			{
				if ( $values[$lang_id] == $existent_values[$lang_id] ) {
					continue;
				}
				
				$aff_query = SK_MySQL::placeholder($update_query, $values[$lang_id], $lang_id);
			}
			else /*( isset($values[$lang_id])
				&& !isset($existent_values[$lang_id]) )*/ {
				$aff_query = SK_MySQL::placeholder($insert_query, $values[$lang_id], $lang_id);
			}
			
			SK_MySQL::query($aff_query);
		}
		
		self::$changes_affected = true;
		
		return isset($aff_query);
	}
	
	/**
	 * Delete a language key with values which it refer.
	 *
	 * @param string|integer $section path or id
	 * @param string $key
	 */
	public static function deleteKey( $section, $key )
	{
		// checking params
		if ( !$section ) {
			throw new SK_LanguageEditException(
				'argument $section value is empty or equals zero',
				SK_LanguageEditException::EMPTY_ARGUMENT_SECTION
			);
		}
		elseif ( !strlen($key) ) {
			throw new SK_LanguageEditException(
				'empty argument $key',
				SK_LanguageEditException::EMPTY_ARGUMENT_KEY
			);
		}
		
		// getting section_id
		if ( !is_numeric($section) ) {
			$section_id = self::parseSectionPath($section);
		}
		else { // setting by section_id
			$section_id = (int)$section;
			
			$query = SK_MySQL::placeholder(
				'SELECT `section` FROM `'.TBL_LANG_SECTION.'`
					WHERE `lang_section_id`=?', $section_id
			);
			
			if ( !SK_MySQL::query($query)->num_rows() ) {
				throw new SK_LanguageEditException(
					'Section with argued `$section_id` does not exist',
					SK_LanguageEditException::SECTION_NOT_EXISTS
				);
			}
		}
		
		// getting $lang_key_id
		$query = SK_MySQL::placeholder(
			'SELECT `lang_key_id` FROM `'.TBL_LANG_KEY.'`
				WHERE `lang_section_id`=? AND `key`="?"'
			, $section_id, $key
		);
		
		$result = SK_MySQL::query($query);
		if ( !$result->num_rows() ) {
			return false;
		}
		
		$lang_key_id = $result->fetch_cell();
		
		$result->free();
		
		return self::deleteKeyById($lang_key_id);
	}
	
	/**
	 * Delete a language key with values which it refer using $lang_key_id.
	 *
	 * @param integer $lang_key_id
	 */
	public static function deleteKeyById( $lang_key_id )
	{
		$lang_key_id = (int)$lang_key_id;
		
		// deleting values
		SK_MySQL::query("DELETE FROM `".TBL_LANG_VALUE."` WHERE `lang_key_id`=$lang_key_id");
		
		// deleting key
		SK_MySQL::query("DELETE FROM `".TBL_LANG_KEY."` WHERE `lang_key_id`=$lang_key_id");
		
		if ( SK_MySQL::affected_rows() ) {
			self::$changes_affected = true;
			return true;
		}
		else {
			return false;
		}
	}
	
	
	public static function createSection( $parent_section, $section, $description )
	{
		if ( !strlen($section) ) {
			throw new SK_LanguageEditException(
				'empty argument $section',
				SK_LanguageEditException::EMPTY_ARGUMENT_SECTION
			);
		}
		
		// getting section_id
		if ( !is_numeric($parent_section) ) {
			$parent_section_id = self::parseSectionPath($parent_section);
		}
		elseif ( $parent_section_id = (int)$parent_section ) { // setting by section_id
			$query = SK_MySQL::placeholder(
				'SELECT `section` FROM `'.TBL_LANG_SECTION.'`
					WHERE `lang_section_id`=?', $parent_section_id
			);
			
			if ( !SK_MySQL::query($query)->num_rows() ) {
				throw new SK_LanguageEditException(
					'Section with argued `$parent_section_id` does not exist',
					SK_LanguageEditException::SECTION_NOT_EXISTS
				);
			}
		}
		
		$query = SK_MySQL::placeholder(
			'INSERT INTO `'.TBL_LANG_SECTION.'`
				SET `parent_section_id`=?, `section`="?", `description`="?"'
			, $parent_section_id, $section, $description
		);
		
		
		// sending insert query
		SK_MySQL::query($query);
		
		
		$object = new stdClass();
		$object->lang_section_id = SK_MySQL::insert_id();
		$object->parent_section_id = "$parent_section_id";
		$object->section = $section;
		$object->description = $description;
		
		self::$changes_affected = true;
		
		return $object;
	}
	
	
	public static function getKeyId( $section, $key )
	{
		// checking params
		if ( !$section ) {
			throw new SK_LanguageEditException(
				'argument $section value is empty or equals zero',
				SK_LanguageEditException::EMPTY_ARGUMENT_SECTION
			);
		}
		elseif ( !strlen($key) ) {
			throw new SK_LanguageEditException(
				'empty argument $key',
				SK_LanguageEditException::EMPTY_ARGUMENT_KEY
			);
		}
		
		// getting section_id
		if ( !is_numeric($section) ) {
			$section_id = self::parseSectionPath($section);
		}
		else { // getting by section_id
			$section_id = (int)$section;
			
			$query = SK_MySQL::placeholder(
				'SELECT `section` FROM `'.TBL_LANG_SECTION.'`
					WHERE `lang_section_id`=?', $section_id
			);
			
			if ( !SK_MySQL::query($query)->num_rows() ) {
				throw new SK_LanguageEditException(
					'Section with argued `$section_id` does not exist',
					SK_LanguageEditException::SECTION_NOT_EXISTS
				);
			}
		}
		
		// getting $lang_key_id
		$query = SK_MySQL::placeholder(
			'SELECT `lang_key_id` FROM `'.TBL_LANG_KEY.'`
				WHERE `lang_section_id`=? AND `key`="?"'
			, $section_id, $key
		);
		
		$result = SK_MySQL::query($query);
		if ( !$result->num_rows() ) {
			return false;
		}
		
		$lang_key_id = $result->fetch_cell();
		
		$result->free();
		
		return $lang_key_id;
	}
	
	
	public static function renameKey( $lang_key_id, $new_name )
	{
		if ( !($lang_key_id = (int)$lang_key_id) ) {
			throw new SK_LanguageEditException(
				'empty or zero argument $lang_key_id',
				SK_LanguageEditException::EMPTY_ARGUMENT_KEY_ID
			);
		}
		
		if ( !strlen($new_name) ) {
			throw new SK_LanguageEditException(
				'empty argument $new_name',
				SK_LanguageEditException::EMPTY_ARGUMENT_KEY
			);
		}
		
		$query = SK_MySQL::placeholder(
			'UPDATE `'.TBL_LANG_KEY.'` SET `key`="?" WHERE `lang_key_id`=?'
			, $new_name, $lang_key_id
		);
		
		SK_MySQL::query($query);
		
		if ( SK_MySQL::affected_rows() ) {
			self::$changes_affected = true;
			return true;
		}
		else {
			return false;
		}
	}
	
	public static function destruct() 
	{
		if ( self::$changes_affected && SK_Config::section('languages')->caching ) {
			self::generateCache();
		}
	}
	
	public static function generateCache()
	{
		SK_Language::$generate_cache_mode = true;
		
		$smarty = SK_Layout::getInstance();
		require_once SMARTY_CORE_DIR.'core.write_file.php';
		
		$root_sections = self::getSections(0);
		
		$languages = self::getLanguages();
		foreach ( $languages as $lang_id => $lang )
		{
			$lang_instance = SK_Language::instance($lang_id);
			
			foreach ( $root_sections as $sect_id => $sect )
			{
				$sect_node = SK_Language::section($sect->section, $lang_instance);
				
				if ( $sect->has_children ) {
					self::fetchDataTree($sect_node, $lang_instance);
				}
                
				$sect_node->fetchKeysList();
			}

            file_put_contents(DIR_LANGS_C."lang-$lang_id.php", "<?php\n\$language = ".var_export($lang_instance, 1).";\n?>");
		}
		
		SK_Language::$generate_cache_mode = false;
	}
	
	
	private static function fetchDataTree(
		Core_LanguageTreeNode $lang_node,
		SK_Language $lang_instance
	) {
		$sections = self::getSections($lang_node->section_id());
		
		foreach ( $sections as $sect_id => $sect )
		{
			$child_node = $lang_node->section($sect->section);
			
			if ( $sect->has_children ) {
				self::fetchDataTree($child_node, $lang_instance);
			}
			
			$child_node->fetchKeysList();
		}
	}

    /* methods for cache generating */
    private static $sectionsArray = array();

    public static function findSectionId( $sectionName, $parentId )
    {
        if( empty(self::$sectionsArray) )
        {
            $result = SK_MySQL::queryForList("SELECT `lang_section_id`, `parent_section_id`, `section` FROM `".TBL_LANG_SECTION."`");

            foreach ( $result as $item )
            {
                if( empty(self::$sectionsArray[$item['parent_section_id']]) )
                {
                    self::$sectionsArray[$item['parent_section_id']] = array();
                }

                self::$sectionsArray[$item['parent_section_id']][$item['section']] = $item['lang_section_id'];
            }
        }

       return empty(self::$sectionsArray[$parentId][$sectionName]) ? null : self::$sectionsArray[$parentId][$sectionName];
    }

    private static $values = array();

    public static function findSectionValues( $langId, $sectionId )
    {
        if( empty(self::$values[$langId]) )
        {
            self::$values = array();
            self::$values[$langId] = array();

            $result = SK_MySQL::queryForList("SELECT `key`, `value`, `lang_section_id` FROM `".TBL_LANG_KEY."` LEFT JOIN `".TBL_LANG_VALUE."` USING(`lang_key_id`) WHERE `lang_id`=$langId");

            foreach ( $result as $item )
            {
                if( empty(self::$values[$langId][$item['lang_section_id']]) )
                {
                    self::$values[$langId][$item['lang_section_id']] = array();
                }

                self::$values[$langId][$item['lang_section_id']][$item['key']] = $item['value'];
            }
        }

        return empty(self::$values[$langId][$sectionId]) ? null : self::$values[$langId][$sectionId];
    }
}


/**
 * A result object of language key node.
 */
class SK_LanguageKeyNode
{
	/**
	 * Language key id.
	 *
	 * @var integer
	 */
	public $lang_key_id;
	
	/**
	 * The name of a key.
	 *
	 * @var string
	 */
	public $key;
	
	/**
	 * lang_id-indexed values list.
	 *
	 * @var array
	 */
	public $values;
	
}


class SK_LanguageEditException extends Exception
{
	const EMPTY_ARGUMENT_KEY		= 1;
	const EMPTY_ARGUMENT_KEY_ID		= 2;
	const EMPTY_ARGUMENT_SECTION	= 3;
	const KEY_NOT_EXISTS			= 4;
	const SECTION_NOT_EXISTS		= 5;
	const DUPLICATE_ENTRY			= 6;
}
