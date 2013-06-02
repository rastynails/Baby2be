<?php

class SK_Config
{
	public static $sections = array();
    public static $sectionList = array();

    private static $generateCache = true;

    public static $configs = null;
    
    public static function init()
    {
        self::$configs = @new SK_Inner_Config_Section('configs');
        
        $clearCache = true;
        try
        {
            $clearCache = self::$configs->clear_config_cache == 1;
        }
        catch ( Exception $e ) {}
        
        if ( $clearCache || !file_exists(DIR_LANGS_C."configs.php") || defined('IS_CRON') )
        {
            if ( !defined('IS_CRON') )
            {
                self::$configs->set('clear_config_cache', 0);
            }

            self::$generateCache = true;
            self::generateCache();
        }
        else
        {
            self::$generateCache = false;
            require_once DIR_LANGS_C."configs.php";
        }
    }

    public static function isConfigCached()
    {
        return !self::$generateCache;
    }

	/**
	 * Retutns a config section object.
	 *
	 * @param string $path
	 * @return SK_Inner_Config_Section
	 */
	public static function section($path, $parent_id = 0) {

		$chain = explode('.', $path);
		$root_sec = array_shift($chain);

		$sect_obj = self::$sections[$root_sec] =
			isset(self::$sections[$root_sec]) ? self::$sections[$root_sec] : new SK_Inner_Config_Section($root_sec, $parent_id);

		foreach ($chain as $name) {
			$sect_obj = $sect_obj->Section($name);
		}

		return $sect_obj;
	}

	public static function saveCache()
	{
        if ( defined('IS_CRON') && IS_CRON )
        {
            return;
        }

        $cache_contents = "<?php\nSK_Config::\$sections = ".var_export(self::$sections, 1).";\n?>";
        file_put_contents(DIR_LANGS_C."configs.php", $cache_contents);
	}

    public static function removeCache()
	{
        self::$configs->set('clear_config_cache', 1);
    }

    public static function generateCache()
    {
        $sectionList = self::getSectionsList();
        
        if ( empty($sectionList) )
        {
            return false;
        }

        foreach ( $sectionList as $section )
        {
            if( $section['parent_section_id'] == 0 )
            {
                self::createSectionTree($section['config_section_id']);
            }
        }

        self::saveCache();

        return true;
    }

    public static function getSectionsList()
	{
        if( empty( self::$sectionList ) )
        {
            $query = 'SELECT `section`,`label`,`parent_section_id`,`config_section_id`
                    FROM `'.TBL_CONFIG_SECTION.'`
                    WHERE 1 ';

            $mysqlResult = SK_MySQL::query($query);

            $result = array();

            while ( $section = $mysqlResult->fetch_array() )
            {
                $result[$section['config_section_id']] = $section;
            }

            self::$sectionList = $result;
        }
        return self::$sectionList;
    }

    public static function createSectionTree( $sectionId, $sectionTree = null )
	{
        if ( (int)$sectionId === 0 )
        {
            return;
        }

        $sectionList = self::getSectionsList();
        $sectionName = $sectionList[$sectionId]['section'];
        //print_r($sectionList[$sectionId]);
        if ( !empty( $sectionTree ) )
        {
            $sectionObject = $sectionTree->Section($sectionName);
        }
        else
        {
            $sectionObject = self::section($sectionName);
        }

        foreach( $sectionList as $section )
        {
            if( $section['parent_section_id'] === (string)$sectionId )
            {
                self::createSectionTree($section['config_section_id'], $sectionObject);
            }
        }
    }
}

class SK_Inner_Config_Section
{
	public $section_id;

    public $section_info;

	public $sub_sections = array();

	public $configs;

    public $config_values;

    public static function __set_state( $data )
	{
		$instance = new self(null, null, false);

		foreach ( $data as $prop => $value ) {
			$instance->$prop = $value;
		}

		return $instance;
	}

	/**
	 * Config section constructor.
	 *
	 * @param string $section
	 * @param integer $parent_section_id
	 * @return SK_Inner_Config_Section
	 */
	public function __construct( $section, $parent_section_id = 0, $generate_cache = true )
	{
        $cacheExists = !$generate_cache;

        if ( $generate_cache && (int)$parent_section_id === 0 && !empty(SK_Config::$sections[$section]) )
        {
            $object = SK_Config::$sections[$section];
            $this->section_id = $object->section_id;
            $this->section_info = $object->section_info;
            $this->sub_sections = $object->sub_sections;
            $this->configs = $object->configs;
            $this->config_values = $object->config_values;

            $cacheExists = true;
        }

        if ( !$cacheExists || $section == 'site_status' )
        {
            $this->generateSectionCache( $section, $parent_section_id );
            $this->generatConfigCache();
            $this->generateConfigValuesCache();

            if ( !$this->section_id ) {
                trigger_error(__CLASS__.'::'.__FUNCTION__."() undefined config section `$section`", E_USER_WARNING);
            }
        }
	}

	/**
	 * Returns an information about the section.
	 *
	 * @return object of stdClass
	 */
	public function getSectionInfo()
	{
		return $this->section_info;
	}

	/**
	 * Retutns a config section object.
	 *
	 * @param string $name
	 * @return SK_Inner_Config_Section
	 */
	public function Section( $name )
	{
		$sect_obj =& $this->sub_sections[$name];

		if ( !isset($sect_obj) ) {
			$sect_obj = new SK_Inner_Config_Section($name, $this->section_id);
            $this->sub_sections[$name] =& $sect_obj;
		}

		return $sect_obj;
	}

	/**
	 * Config getter.
	 *
	 * @param string $var
	 * @return mixed
	 */
	public function get( $var ) {
		return $this->__get($var);
	}

	/**
	 * Config getter.
	 *
	 * @param string $var
	 * @return mixed
	 */
	public function __get( $var )
	{
		if ( !key_exists($var, $this->configs) )
        {
			throw new SK_ConfigException(
				'undefined config var `'.$var.'`',
				SK_ConfigException::CONFIG_NOT_EXISTS
			);
		}
		$object = $this->configs[$var];

		return $object->value;
	}

	/**
	 * Setter gag.
	 */
	public function __set( $var, $value )
    {
		throw new Exception(__CLASS__.'::'.__FUNCTION__.'() unable to set config var value directly, use $Section->set($var, $value) to change the config value');
	}

	/**
	 * Saves a config variable.
	 *
	 * @param string $var
	 * @param mixed $value
	 * @return boolean
	 */
	public function set( $var, $value )
	{
		$value = json_encode($value);

		$query = SK_MySQL::placeholder(
			'UPDATE `'.TBL_CONFIG.'` SET `value`="?"
				WHERE `config_section_id`=? AND `name`="?"'
			, $value, $this->section_id, $var
		);

		SK_MySQL::query($query);

		$result =  (bool)SK_MySQL::affected_rows();

		if ( $result )
		{
            $object = $this->configs[trim($var)];
            $value = json_decode($value, true);

            if ( is_array($value) )
            {
                $object->value = $this->arrayToObject($value);
            }
            else
            {
                $object->value = $value;
            }

            $this->configs[trim($var)] = $object;

            if ( $var != 'clear_config_cache' && isset(SK_Config::$configs->configs['clear_config_cache']) && SK_Config::$configs->clear_config_cache != 1 )
            {
                SK_Config::$configs->set('clear_config_cache', 1);
            }

		}

		return $result;
	}

	/**
	 * Returns section configs list as an assocciative array with config var names at index.
	 * Each item of the array is an object of stdClass.
	 *
	 * @return array
	 */
	public function getConfigsList()
	{
		return $this->configs;
	}

	public function getConfigValues( $config_name )
	{
        return isset($this->config_values[$config_name]) ? $this->config_values[$config_name] : array();
	}

    public function generatConfigCache()
	{
        $this->configs = array();

        $query = SK_MySQL::placeholder(
            'SELECT * FROM `'.TBL_CONFIG.'` WHERE `config_section_id`=?', $this->section_id
        );

        $result = SK_MySQL::query($query);

        while ( $_conf = $result->fetch_object('SK_ConfigDtoObject') )
        {
            $value = json_decode($_conf->value, true);

            if ( is_array($value) )
            {
                $_conf->value = $this->arrayToObject($value);
            }
            else
            {
                $_conf->value = $value;
            }

            $this->configs[$_conf->name] = $_conf;
        }
	}

    public function generateSectionCache( $section, $parent_section_id = 0 )
	{
        //print_r($section);
        $query = SK_MySQL::placeholder(
			'SELECT `section`,`label`,`parent_section_id`,`config_section_id`
				FROM `'.TBL_CONFIG_SECTION.'`
				WHERE `parent_section_id`=? AND `section`="?"'

			, $parent_section_id, $section
		);
        //print_r($query);
		$this->section_info = SK_MySQL::query($query)->fetch_object('SK_ConfigDtoObject');
        //print_r($this->section_info);
        $this->section_id = $this->section_info->config_section_id;
    }

    public function generateConfigValuesCache()
	{
		$query = SK_MySQL::placeholder(
			'SELECT `v`.*, `c`.`name` FROM `'.TBL_CONFIG_VALUE.'` AS `v` LEFT JOIN `'.TBL_CONFIG.'` AS `c` USING(`config_id`)
			 WHERE `c`.`config_section_id`=? ', $this->section_id
		);

		$result = SK_MySQL::query($query);

        $this->config_values = array();

		while ( $value = $result->fetch_assoc() )
        {   //print_r($value);
			$this->config_values[$value['name']][] = $value;
		}
    }

    private function arrayToObject( $array )
	{
        $result = array();

        foreach( $array as $key => $value )
        {
            if ( is_array($value) )
            {
                $result[$key] = $this->arrayToObject($value);
            }
            else
            {
                $result[$key] = $value;
            }
        }

        return SK_ConfigDtoObject::__set_state($result);
    }
}

class SK_Config_Section
{
    /**
     * @var SK_Inner_Config_Section
     */
    public $section;

	/**
	 * Config section constructor.
	 *
	 * @param string $section
	 * @param integer $parent_section_id
	 * @return SK_Config_Section
	 */
	public function __construct( $section, $parent_id = 0 )
	{
        $this->section = SK_Config::section($section, $parent_id);
	}

	/**
	 * Returns an information about the section.
	 *
	 * @return object of stdClass
	 */
	public function getSectionInfo()
	{
		return $this->section->section_info;
	}

	/**
	 * Retutns a config section object.
	 *
	 * @param string $name
	 * @return SK_Config_Section
	 */
	public function Section( $name )
	{
        return $this->section->Section($name);
	}

	/**
	 * Config getter.
	 *
	 * @param string $var
	 * @return mixed
	 */
	public function get( $var ) {
		return $this->section->get($var);
	}

	/**
	 * Saves a config variable.
	 *
	 * @param string $var
	 * @param mixed $value
	 * @return boolean
	 */
	public function set( $var, $value )
	{
        $this->section->set($var, $value);
	}

	/**
	 * Returns section configs list as an assocciative array with config var names at index.
	 * Each item of the array is an object of stdClass.
	 *
	 * @return array
	 */
	public function getConfigsList()
	{
		return $this->section->configs;
	}

	public function getConfigValues( $config_name )
	{
        return $this->section->getConfigValues($config_name);
	}

    public function __get( $var )
	{
        return $this->section->get($var);
	}
}

class SK_ConfigDtoObject
{
    public static function __set_state( array $params )
    {
		$instance = new self();

		foreach ( $params as $prop => $value ) {
			$instance->$prop = $value;
		}

		return $instance;
    }
}


class SK_ConfigException extends Exception
{
	const	EMPTY_ARGUMENT_SECTION	= 1,
			EMPTY_ARGUMENT_KEY		= 2,
			SECTION_NOT_EXISTS		= 3,
			CONFIG_NOT_EXISTS		= 4;
}


SK_Config::init();
