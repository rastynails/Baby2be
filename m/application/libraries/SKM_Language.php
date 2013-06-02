<?php


class SKM_Language
{
	/**
	 * An instance of auto singleton object.
	 *
	 * @var SKM_Language
	 */
	private static $auto_instance;
	
	/**
	 * Defined constants.
	 *
	 * @var array
	 */
	private static $globals = array();
		
	/**
	 * Use this factory method to create language instances.
	 *
	 * @param integer $lang_id
	 * @return SKM_Language
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
		
		$instances[$lang_id] = new self($lang_id);
		
		return $instances[$lang_id];
	}
	
	/**
	 * Construct a default language object.
	 * Default means that user used.
	 */
	private static function constructAutoInstance() {
		self::$auto_instance = self::instance(SKM_User::language_id());
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
	 * Returns current language id.
	 *
	 * @return SKM_Language
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
	 * @return SKM_LanguageTreeNode
	 */
	public static function section( $section, SKM_Language $language = null )
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
			$instance->sections[$root_sec] = new SKM_LanguageTreeNode($instance->lang_id, $root_sec);
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
			return SKM_Language::section($section)->text($value_key, $text_vars);
		}
		catch ( SKM_LanguageException $e ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() '.$e->getMessage(), E_USER_NOTICE);
			return '%'.$cdata_addr;
		}
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
	 * @return SKM_Language
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

class SKM_LanguageTreeNode
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
		
		$result = SK_MySQL::query(
			"SELECT `lang_section_id` FROM `".TBL_LANG_SECTION."`
				WHERE `parent_section_id`=$this->parent_section_id
					AND `section`='$this->section_name'"
		);
		
		$this->section_id = $result->fetch_cell();
		
		if ( !$this->section_id ) {
			throw new SKM_LanguageException(
				'section "'.$section_name.'" does not exist',
				SKM_LanguageException::SECTION_NOT_EXISTS
			);
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
	 * @return SKM_LanguageTreeNode
	 */
	public function section( $section_name )
	{
		if ( !isset($this->sections[$section_name]) )
		{
			$this->sections[$section_name] = new self($this->lang_id, $section_name, $this->section_id);
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
			throw new SKM_LanguageException(
				'section "'.$this->section_name.'" does not exist',
				SKM_LanguageException::SECTION_NOT_EXISTS);
		}
		
		$result = SK_MySQL::query(
			"SELECT `key`, `value`
				FROM `".TBL_LANG_KEY."` LEFT JOIN `".TBL_LANG_VALUE."` USING(`lang_key_id`)
				WHERE `lang_section_id`=$this->section_id AND `lang_id`=$this->lang_id"
		);
		
		while ( $row = $result->fetch_object() ) {
			$this->lang_data[$row->key] = $row->value;
		}
		
		$result->free();
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
			/* we additionally check for a $lang_key existense because
				a previous query may not fetch a `key` row without `lang_id` matching */
			$sql_lang_key = SK_MySQL::realEscapeString($lang_key);
			$key_check_query =
				"SELECT `lang_key_id` FROM `".TBL_LANG_KEY."`
					WHERE `lang_section_id`=$this->section_id AND `key`='$sql_lang_key'";
			
			$result = SK_MySQL::query($key_check_query);
			if ( !$result->num_rows() ) {
				throw new SKM_LanguageException(
					'language key "'.$lang_key.'" does not exist in section "'.$this->section_name.'"',
					SKM_LanguageException::KEY_NOT_EXISTS
				);
			}
			$result->free();
			
			return null;
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
			$text = SKM_Language::exec($text, $args);
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
	 * @return SKM_LanguageTreeNode
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


class SKM_LanguageException extends Exception
{
	const	EMPTY_ARGUMENT_SECTION	= 1,
			EMPTY_ARGUMENT_KEY		= 2,
			SECTION_NOT_EXISTS		= 3,
			KEY_NOT_EXISTS			= 4;
	
}

?>
