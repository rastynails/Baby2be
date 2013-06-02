<?php

class SK_Language
{
	/**
	 * An instance of auto singleton object.
	 *
	 * @var SK_Language
	 */
	private static $auto_instance;

	/**
	 * Defined constants.
	 *
	 * @var array
	 */
	private static $globals = array();


	public static $generate_cache_mode = false;

	/**
	 * Use this factory method for create cachable language instances.
	 *
	 * @param integer $lang_id
	 * @return SK_Language
	 */
	public static function instance( $lang_id )
	{
		static $instances = array();

		if ( !($lang_id = (int)$lang_id) ) {
			trigger_error('invalid argument $lang_id', E_USER_ERROR);
		}

		if ( isset($instances[$lang_id]) ) {
			return $instances[$lang_id];
		}

		if ( SK_Config::section('languages')->caching
			&& !SK_Language::$generate_cache_mode
		) {
			require_once DIR_LANGS_C."lang-$lang_id.php";
			$instances[$lang_id] = $language;
		}
		else {
			$instances[$lang_id] = new self($lang_id);
		}

		return $instances[$lang_id];
	}

	/**
	 * Construct a default language object.
	 * Default means that user used.
	 */
	private static function constructAutoInstance() {
		self::$auto_instance = self::instance(SK_HttpUser::language_id());
	}


	public static function __set_state( $data )
	{
		$instance = new self($data['lang_id']);
		unset($data['lang_id']);

		foreach ( $data as $prop => $value ) {
			$instance->$prop = $value;
		}

		return $instance;
	}

	/**
	 * Check whether a language data is cached.
	 *
	 * @return boolean
	 */
	public static function cached()
	{
		static $lang_caching;

		if ( !isset($lang_caching) ) {
			$lang_caching = SK_Config::section('languages')->caching;
		}

		return ($lang_caching && !self::$generate_cache_mode);
	}

	/**
	 * Returns current language id.
	 *
	 * @return SK_Language
	 */
	public static function current_lang_id()
	{
		if ( !isset(self::$auto_instance) ) {
			self::constructAutoInstance();
		}

		return self::$auto_instance->lang_id;
	}

	/**
	 * Define value for a single constant or a list of key=>value pairs for globally using in text.
	 *
	 * @param array|string $const
	 * @param mixed $value
	 */
	public static function defineGlobal( $const, $value = null )
	{
		if ( is_array($const) ) {
			foreach ( $const as $key => $value ) {
				self::$globals[$key] = $value;
			}
		}
		else {
			self::$globals[$const] = $value;
		}
	}

	/**
	 * Get a reference to functional object of a language section.
	 *
	 * @param string $section
	 * @return Core_LanguageTreeNode
	 */
	public static function section( $section, SK_Language $language = null )
	{
		if ( isset($language) ) {
			$instance = $language;
		}
		else {
			if ( !isset(self::$auto_instance) ) {
				self::constructAutoInstance();
			}
			$instance = self::$auto_instance;
		}

		$path_steps = explode('.', $section);
		$root_sec = array_shift($path_steps);

		if ( !isset($instance->sections[$root_sec]) ) {
			$instance->sections[$root_sec] = new Core_LanguageTreeNode($instance->lang_id, $root_sec);
		}

		$sect_node = $instance->sections[$root_sec];

		foreach ( $path_steps as $step )
			$sect_node = $sect_node->section($step);

		return $sect_node;
	}

	/**
	 * Get a text by full address without throwing an exceptions.
	 *
	 * @param string $cdata_addr
	 * @param array $text_vars
	 * @return string
	 */
	public static function text( $cdata_addr, array $text_vars = null )
	{
		if ( $cdata_addr{0} == '%' ) {
			$cdata_addr = substr($cdata_addr, 1);
		}

		$rdot_position = strrpos($cdata_addr, '.');
		$section = substr($cdata_addr, 0, $rdot_position);
		$value_key = substr($cdata_addr, $rdot_position + 1);

		try {
			return SK_Language::section($section)->text($value_key, $text_vars);
		}
		catch ( SK_LanguageException $e ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() '.$e->getMessage(), E_USER_NOTICE);
			return '%'.$cdata_addr;
		}
	}

	public static function getLanguageTag( $lang_id )
	{
	    if ( !$lang_id )
	    {
	        return 'en';
	    }

	    $query = SK_MySQL::placeholder("SELECT `abbrev` FROM `".TBL_LANG."` WHERE `lang_id` = ? LIMIT 1", $lang_id);

	    $tag = SK_MySQL::query($query)->fetch_cell();

	    return $tag == 'eng' ? 'en' : strtolower($tag);
	}

	/**
	 * Executes a text inplacing variables with given kay=>value pair arguments.
	 *
	 * @param string $cdata
	 * @param array $args
	 * @return string
	 */
	public static function exec( $cdata, array $args = null )
	{
		$args = isset($args) ? array_merge(self::$globals, $args) : self::$globals;

		return preg_replace('~\{\$(\w+)\}~ie', "isset(\$args['\\1']) ? \$args['\\1'] : '\\0'", $cdata);
	}

	/**
	 * Convert special characters to html entities using the $charser encoding.
	 *
	 * @param string $string
	 * @param integer $quote_style
	 * @param string $charset
	 * @return string
	 */
	public static function htmlspecialchars( $string, $quote_style = ENT_COMPAT, $charset = 'UTF-8' ) {
		return htmlspecialchars($string, $quote_style, $charset);
	}


	/**
	 * Current language id.
	 *
	 * @var integer
	 */
	private $lang_id;

	/**
	 * Self-factored language sections objects list.
	 *
	 * @var array
	 */
	private $sections = array();

	/**
	 * Language tree constructor.
	 *
	 * @param integer $lang_id
	 * @return SK_Language
	 */
	private function __construct( $lang_id ) {
		$this->lang_id = $lang_id;
	}

	public function sections() {
		return $this->sections;
	}

	public function language_id() {
		return $this->lang_id;
	}
}


class Core_LanguageTreeNode
{
	/**
	 * Current language id.
	 *
	 * @var integer
	 */
	protected $lang_id;

	/**
	 * Section id.
	 *
	 * @var integer
	 */
	private $section_id;

	/**
	 * Name of a node section.
	 *
	 * @var string
	 */
	private $section_name;

	/**
	 * An id of a parent section.
	 *
	 * @var integer
	 */
	private $parent_section_id;

	/**
	 * Factored language sections objects list.
	 *
	 * @var array
	 */
	private $sections = array();

	/**
	 * Language `key`=>`value` data store.
	 *
	 * @var array
	 */
	protected $lang_data;

	/**
	 * Language section object constructor.
	 *
	 * @param integer $lang_id
	 * @param string $section_name
	 */
	public function __construct( $lang_id, $section_name, $parent_section_id = 0 )
	{
		$this->lang_id = $lang_id;
		$this->section_name = $section_name;
		$this->parent_section_id = $parent_section_id;

		if ( !SK_Language::cached() ) {
			$this->section_id = SK_LanguageEdit::findSectionId($this->section_name, $this->parent_section_id);

			if ( !$this->section_id ) {
				throw new SK_LanguageException(
					'section "'.$section_name.'" does not exist',
					SK_LanguageException::SECTION_NOT_EXISTS
				);
			}
		}
	}

	/**
	 * The getter for $this->section_id.
	 *
	 * @return integer
	 */
	public function section_id() {
		return $this->section_id;
	}

	/**
	 * Provides a child language section.
	 *
	 * @param string $section_name
	 * @return Core_LanguageTreeNode
	 */
	public function section( $section_name )
	{
		if ( !isset($this->sections[$section_name]) )
		{
			if ( !SK_Language::cached() ) {
				$this->sections[$section_name] = new self($this->lang_id, $section_name, $this->section_id);
			}
			else {
				throw new SK_LanguageException(
					'section "'.$section_name.'" does not exist',
					SK_LanguageException::SECTION_NOT_EXISTS
				);
			}
		}

		return $this->sections[$section_name];
	}

	/**
	 * Fetch section keys from database.
	 */
	public function fetchKeysList()
	{
		// possible when cache is enabled
		if ( !$this->section_id ) {
			throw new SK_LanguageException(
				'section "'.$this->section_name.'" does not exist',
				SK_LanguageException::SECTION_NOT_EXISTS);
		}

        $this->lang_data = SK_LanguageEdit::findSectionValues($this->lang_id, $this->section_id);
	}

	/**
	 * Get a language character data for a key.
	 *
	 * @param string $lang_key
	 * @return string
	 */
	public function cdata( $lang_key )
	{
		if ( !isset($this->lang_data) ) {
			self::fetchKeysList();
		}

		if ( !isset($this->lang_data[$lang_key]) )
		{
                    if ( !$this->section_id )
                    {
			throw new SK_LanguageException(
                            'section "'.$this->section_name.'" does not exist',
                            SK_LanguageException::SECTION_NOT_EXISTS);
                    }

                    $result = SK_MySQL::query(
                        "SELECT `key`, `value`
                            FROM `".TBL_LANG_KEY."` LEFT JOIN `".TBL_LANG_VALUE."` USING(`lang_key_id`)
                            WHERE `lang_section_id`=$this->section_id AND `lang_id`=$this->lang_id"
                    );

                    while ( $row = $result->fetch_object() )
                        $this->lang_data[$row->key] = $row->value;

                    if ( (!isset($this->lang_data[$lang_key])) )
                    {
                        throw new SK_LanguageException(
                            'language key "'.$lang_key.'" does not exist in section "'.$this->section_name.'"',
                            SK_LanguageException::KEY_NOT_EXISTS
                        );
                    }
		}

		return $this->lang_data[$lang_key];
	}

	/**
	 * Get a text by $lang_key replacing variables.
	 *
	 * @param string $lang_key
	 * @param array $args list of `var_name` => $value
	 * @return string
	 */
	public function text( $lang_key, array $args = null )
	{
		$text = $this->cdata($lang_key);

		if ( !$text ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() undefined language value for key "'.$lang_key.'" in section "'.$this->section_name.'"', E_USER_NOTICE);
			return DEV_MODE ? "%$lang_key" : '';
		}

		if ( strpos($text, '{') !== false ) {
			$text = SK_Language::exec($text, $args);
		}

		return $text;
	}

	/**
	 * Checks wether a key is exists.
	 *
	 * @param string $lang_key
	 * @return boolean
	 */
	public function key_exists( $lang_key )
	{
		if ( !isset($this->lang_data) )
		{
			$query = SK_MySQL::placeholder("SELECT `value`
					FROM `".TBL_LANG_KEY."` LEFT JOIN `".TBL_LANG_VALUE."` USING(`lang_key_id`)
					WHERE `lang_section_id`=? AND `lang_id`=? AND `key`='?'",$this->section_id, $this->lang_id, $lang_key );
			return (SK_MySQL::query($query)->fetch_cell() !== false);
		}
		else return isset($this->lang_data[$lang_key]);
	}

	/**
	 * Cachable object.
	 *
	 * @param array $data
	 * @return Core_LanguageTreeNode
	 */
	public static function __set_state( $data )
	{
		$instance = new self(
			$data['lang_id'], $data['section_name'], $data['parent_section_id']
		);
		unset($data['lang_id'], $data['section_name'], $data['parent_section_id']);

		foreach ( $data as $prop => $value ) {
			$instance->$prop = $value;
		}

		return $instance;
	}

	public function sections() {
		return $this->sections;
	}

	public function lang_data() {
		return $this->lang_data;
	}
}


class SK_LanguageException extends Exception
{
	const	EMPTY_ARGUMENT_SECTION	= 1,
			EMPTY_ARGUMENT_KEY		= 2,
			SECTION_NOT_EXISTS		= 3,
			KEY_NOT_EXISTS			= 4;

}
