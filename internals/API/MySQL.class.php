<?php

class SK_MySQL
{
	protected static $link_id;

    protected static $logger;

    protected static $resourcelogger;
	
	/**
	 * Open a connection to a MySQL server.
	 */
	public static function connect()
	{
        self::$logger = SK_QueryLogger::getInstance('db');
        self::$resourcelogger = SK_QueryLogger::getInstance('db-resource');

		self::$link_id = @mysql_connect(DB_HOST, DB_USER, DB_PASS);
		
		if ( !self::$link_id
			|| !mysql_selectdb(DB_NAME)
		) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() database connection failed', E_USER_WARNING);
			return false;
		}
		
		if ( !mysql_set_charset('utf8', self::$link_id) ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() cannot use utf-8 connection', E_USER_WARNING);
		}
		
		return true;
	}
	
	/**
	 * Escapes special characters in a string for use in a SQL statement.
	 *
	 * @param string $unescaped_string
	 * @return string
	 */
	public static function realEscapeString( $unescaped_string ) {
		return mysql_real_escape_string($unescaped_string, self::$link_id);
	}
	
	/**
	 * Compile query placeholder.
	 *
	 * @param array $query_tpl
	 * @return string
	 */
	public static function compile_placeholder( $query_tpl )
	{
		$compiled = array();
		$i = 0;	// placeholders counter
		$p = 0; // current position
		$prev_p = 0; // previous position
		
		while ( false !== ($p = strpos($query_tpl, '?', $p)) )
		{
			$compiled[] = substr($query_tpl, $prev_p, $p-$prev_p);
			
			$type_char = $char = $query_tpl{$p-1};
			
			switch ( $type_char ) {
				case '"': case "'": case '`':
					$type = $type_char;	// string
					break;
				default:
					$type = '';		// integer
					break;
			}
			
			$next_char = isset($query_tpl{$p+1}) ? $query_tpl{$p+1} : null;
			if ( $next_char === '@' ) {	// array list
				$compiled[] = array($i++, $type, '@');
				$prev_p = ($p=$p+2);
			}
			else {
				$compiled[] = array($i++, $type);
				$prev_p = ++$p;
			}
		}
		
		$tail_length = (strlen($query_tpl) - $prev_p);
		if ( $tail_length ) {
			$compiled[] = substr($query_tpl, -$tail_length);
		}
		
		return $compiled;
	}
	
	/**
	 * Generates a query string for execution.
	 *
	 * @param string $query_tpl
	 * @param mixed $arg1
	 * @param mixed $arg2
	 * @param mixed $arg3...
	 * @return string
	 */
	public static function placeholder()
	{
		$arguments = func_get_args();
		$c_query = array_shift($arguments);
		
		if ( !is_array($c_query) ) {
			$c_query = self::compile_placeholder($c_query);
		}
		
		$query = '';
		
		foreach ( $c_query as $piece )
		{
			if ( !is_array($piece) ) {
				$query .= $piece;
				continue;
			}
			
			list( $index, $type ) = $piece;
			
			if ( isset($piece[2]) ) // array value
			{
				$array = $arguments[$index];
				
				switch ( $type ) {
					case '"': case "'": case '`':
						$query .= implode("$type,$type", array_map(array(__CLASS__, 'realEscapeString'), $array));
						break;
					default:
						$query .= implode(",", array_map('intval', $array));
						break;
				}
			}
			else // scalar value
			{
				$var = $arguments[$index];
				
				switch ( $type ) {
					case '"': case "'": case '`':
						$query .= self::realEscapeString($var);
						break;
					default:
						$query .= (int)$var;
						break;
				}
			}
		}
		
		return $query;
	}
	
	/**
	 * Sends query to a database server.
	 *
	 * @param string $query
	 * @throws SK_MySQL_Exception
	 * @return SK_MySQL_Result
	 */
	public static function query( $query )
	{
        
        self::$logger->loggerStart($query);
        
		$result = mysql_query($query, self::$link_id);

        self::$logger->loggerStop();

		if ( $result === false ) {
			throw new SK_MySQL_Exception(
				mysql_error(self::$link_id),
				mysql_errno(self::$link_id),
				$query);
		}
		elseif ( is_bool($result) ) {
			return true;
		}
		else {
			return new SK_MySQL_Result($result, $query);
		}
	}
	
	/**
	 * Get number of affected rows in previous MySQL operation.
	 *
	 * @return integer the number of affected rows on success, and -1 if the last query failed.
	 */
	public static function affected_rows() {
		return mysql_affected_rows(self::$link_id);
	}
	
	/**
	 * Get the last insert query autoincrement id.
	 *
	 * @return string
	 */
	public static function insert_id() {
		return mysql_insert_id(self::$link_id);
	}
	
	/**
	 * Returns column info object
	 *
	 * @param string $tbl_name
	 * @param string $col_name
	 * @return SK_MySQL_Column
	 */
	public static function describe( $tbl_name, $col_name = null )
	{
		$column_name = isset($col_name) ? "`".$col_name."`" : '';
		$result = self::query("DESCRIBE `$tbl_name` $column_name");
		
		if ($result->num_rows()==1) {
			return $result->fetch_object("SK_MySQL_Column");
		}
		
		$out = array();
		while ( $item = $result->fetch_object("SK_MySQL_Column") ) {
			$out[$item->Name()] = $item;
		}
		return $out;
	}

    public static function queryForList( $query )
    {
        self::$logger->loggerStart($query);

		$result = mysql_query($query, self::$link_id);

        self::$logger->loggerStop();

        $resultToReturn = array();

        if( $result )
        {
            while( $row = mysql_fetch_assoc($result) )
            {
                $resultToReturn[] = $row;
            }
        }

        if( is_resource($result) )
        {
            mysql_free_result($result);
        }

        return $resultToReturn;
    }

//    public function queryForRow( $query )
//    {
//
//    }
	
}

class SK_MySQL_Column
{
	private $column_info = array();
	
	public function __construct() {}
	
	public function __set( $var, $value ) {
		if ($var=='Type') {
			$matches = array();
			preg_match_all('/^(\w+)(\(\S+\))?\s?(\w+)?$/',$value,$matches);
			$this->column_info['type'] = $matches[1][0];
			$this->column_info['size'] = str_replace("'","",trim($matches[2][0],'()'));
			$this->column_info['unsigned'] = ($matches[3][0] == 'unsigned');
		}
		else {
			$this->column_info[$var] = $value;
		}
	}
	
	public function size(){
		return $this->column_info['size'];
	}
	
	public function type(){
		return $this->column_info['type'];
	}
	
	public function unsigned(){
		return (bool)$this->column_info['unsigned'];
	}
	
	public function allowNull(){
		return (bool)($this->column_info['Null'] == "NO");
	}
	
	public function defaultValue(){
		return (bool)$this->column_info['Default'];
	}
	
	public function name(){
		return $this->column_info['Field'];
	}
	
	public function is_string() {
		switch ($this->column_info['type'])
		{
			case "varchar":
			case "char":
			case "text":
			case "enum":
			case "date":
				return true;
			default:
				return false;
		}
	}
	
}


class SK_MySQL_Result
{
	private $result;

    private $query = null;

    private $logger;
	
	/**
	 * Constructor.
	 *
	 * @param resource $result
	 * @return SK_MySQL_Result
	 */
	public function __construct( $result, $query = null ) {
		$this->result = $result;
        $this->query = $query;
        $this->logger = SK_QueryLogger::getInstance('db-resource');
	}
	
	/**
	 * Destructor frees result used memory.
	 */
	public function __destruct() {
		$this->free();
	}
	
	/**
	 * Get number of rows in result.
	 *
	 * @return integer
	 */
	public function num_rows() {
		return mysql_num_rows($this->result);
	}
	
	/**
	 * Fetch a result row as an object.
	 *
	 * @param string $class_name
	 * @param array $params
	 * @return object an object with string properties that correspond to the fetched row, or FALSE if there are no more rows.
	 */
	public function fetch_object( $class_name = null, array $params = null )
	{
        $this->logger->loggerStart($this->query);

		if ( !isset($class_name) ) {
			$result_obj = mysql_fetch_object($this->result);
		}
		elseif ( !isset($params) ) {
			$result_obj = mysql_fetch_object($this->result, $class_name);
		}
		else {
			$result_obj = mysql_fetch_object($this->result, $class_name, $params);
		}
		
        $this->logger->loggerStop();

		return $result_obj;
	}
	
	/**
	 * Fetch a result row as an associative array, numeric array or both.
	 *
	 * @param integer $result_type
	 * @return array an associative array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
	 */
	public function fetch_array( $result_type = null )
	{
        $this->logger->loggerStart($this->query);
        $result = isset($result_type)
			? mysql_fetch_array($this->result, $result_type)
			: mysql_fetch_array($this->result);
        $this->logger->loggerStop();
        
		return $result;
	}
	
	/**
	 * Fetch a result row as an associative array.
	 *
	 * @return array an associative array of strings that corresponds to the fetched row, or FALSE if there are no more rows.
	 */
	public function fetch_assoc() {
        $this->logger->loggerStart($this->query);
        $result = mysql_fetch_assoc($this->result);
        $this->logger->loggerStop();

        return $result;
	}

	/**
	 * Fetch a row column.
	 *
	 * @param integer $col_index
	 * @return string a row column value or FALSE if there are no rows in result.
	 */
	public function fetch_cell( $col_index = 0 )
	{
        $this->logger->loggerStart($this->query);
        $row = mysql_fetch_row($this->result);
        $this->logger->loggerStop();
        
		if ( $row === false ) {
			return false;
		}

		return $row[$col_index];
	}
	
	/**
	 * Free result memory.
	 *
	 * @return bool
	 */
	public function free() {
		if ( !is_resource($this->result) ) {
			return false;
		}
		return mysql_free_result($this->result);
	}
	
	/**
	 * Maps result row in parameter Class object 
	 * 
	 * @param string $class
	 * @return object | array | null
	 */
	public function mapObject( $class )
	{	
        $this->logger->loggerStart($this->query);
        $row_array = mysql_fetch_assoc($this->result);
        $this->logger->loggerStop();
		
		if( self::num_rows()== 0 || $row_array === false || empty($row_array) )
			return null;
		
		$r_class = new ReflectionClass($class);
		
		$mapped_obj = $r_class->newInstance();

		if( ! $mapped_obj instanceof SK_Entity ) // !WARNING! method is valid only for classes extended from SK_Entity
			return null;
		
		foreach( $r_class->getDefaultProperties() as $key => $value )
		{	
			if(array_key_exists($key,$row_array))
			{
				$r_setter_method = $r_class->getMethod("set".ucfirst($key));
				
				if( $r_setter_method === false || $r_setter_method === null || !isset( $row_array[$key] ) )
				{
					unset( $row_array[$key] );
					continue;
				}
				$r_setter_method->invoke($mapped_obj,$row_array[$key]);
				
				unset($row_array[$key]);	
			}	
		}
		
		if( empty( $row_array ) )
			return $mapped_obj;
		
		$row_array['dto'] = $mapped_obj;
		
		return $row_array;
	}
	
	/**
	 * Maps result rows in array of parameter Class object
	 * 
	 * @param string $class
	 * 
	 * @return array
	 */
	public function mapObjectArray( $class )
	{
		if( self::num_rows() == 0 )
			return array();
		
		$result_array = array();
		
		$add_fields = null;
				
		while ( true )
		{
			$row_array = mysql_fetch_assoc($this->result);
			
			if( $row_array === false || empty($row_array) )
				break;
				
			$r_class = new ReflectionClass($class);
			
			$mapped_obj = $r_class->newInstance();
			
			if( ! $mapped_obj instanceof SK_Entity ) // method is valid for classes extended from SK_Entity
				return array();
			
			foreach( $r_class->getDefaultProperties() as $key => $value )
			{	
				if(array_key_exists( $key, $row_array ))
				{
					$r_setter_method = $r_class->getMethod("set".ucfirst($key));
					
					if( $r_setter_method === false || $r_setter_method === null )
						continue;
					
					$r_setter_method->invoke($mapped_obj,$row_array[$key]);
					
					unset($row_array[$key]);	
				}
			}
			
			if( $add_fields === null )
			{
				$add_fields = empty($row_array) ? true : false;
			}	

			if( $add_fields )
			{
				$result_array[] = $mapped_obj;
			}
			else 
			{
				$row_array['dto'] = $mapped_obj;
				$result_array[] = $row_array;
			}
		}
		
		return $result_array;
	}
}


class SK_MySQL_Exception extends Exception
{
	private $query;
	
	public function __construct( $message, $code, $query )
	{
		parent::__construct($message, $code);
		
		$this->query = $query;
	}
	
	
	public function __toString() {
		return $this->query;
	}
	
}
