<?php

class SK_TemporaryFile
{
	private static $err_index = array(
		0 => 'Uploaded file moving failed.',
		UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
		UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
		UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
		UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
		UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
		UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
		UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.'
	);
	
	private $uniqid;
	
	private $type;
	
	private $filename;
	
	private $size;
	
	private $extension;
	
	private $tmp_name;
	
	private $captured = false;
	
	/**
	 * Catch a user uploaded file.
	 *
	 * @param array $file
	 * @return SK_TemporaryFile
	 */
	static public function catchFile( array $file, $field )
	{
		if ( $file['error'] !== UPLOAD_ERR_OK ) {
			throw new SK_TemporaryFileException(self::$err_index[$file['error']], $file['error']);
		}
		
		// creating an object without properties
		$_this = new SK_TemporaryFile;
		$_this->uniqid = md5(uniqid(rand(), true));
		$_this->tmp_name = $file['tmp_name'];
		$_this->type = $file['type'];
		$_this->filename = $file['name'];
		$_this->size = $file['size'];
		$_this->extension = strtolower(substr($file['name'], strrpos($file['name'], '.')+1));
		
		// validating userfile by field
		$field->validateUserFile($_this);
		
		$_this->captured = true;
		if ( // capturing tmp file
			!move_uploaded_file($_this->tmp_name, $_this->getPath())
		) {
			$_this->captured = false;
			throw new SK_TemporaryFileException(self::$err_index[0], 0);
		}
		
		$query = SK_MySQL::placeholder(
			'INSERT INTO `'.TBL_TMP_USERFILE.'`
				(`uniqid`, `type`, `filename`, `size`, `uploadstamp`)
					VALUES("?", "?", "?", ?, ?)'
			, $_this->uniqid, $_this->type, $_this->filename, $_this->size, time()
		);
		
		SK_MySQL::query($query);
		
		return $_this;
	}
	
	/**
	 * Constructor.
	 *
	 * @param string $uniqid
	 */
	public function __construct( $uniqid = null )
	{
		if ( isset($uniqid) ) {
			$this->fetchFromDB($uniqid);
			$this->captured = true;
		}
	}
	
	/**
	 * Sets the state of object getting file information from database.
	 *
	 * @param string $uniqid
	 */
	private function fetchFromDB( $uniqid )
	{
		$query = SK_MySQL::placeholder(
			'SELECT * FROM `'.TBL_TMP_USERFILE.'` WHERE `uniqid`="?"', $uniqid
		);
		
		$result = SK_MySQL::query($query);
		$file = $result->fetch_assoc();
		
		$this->uniqid = $file['uniqid'];
		$this->type = $file['type'];
		$this->filename = $file['filename'];
		$this->size = $file['size'];
		$this->extension = strtolower(substr($this->filename, strrpos($this->filename, '.')+1));
	}
	
	/**
	 * Get a full path to a temporary file.
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->captured ? DIR_TMP_USERFILES."$this->uniqid.$this->extension" : $this->tmp_name;
	}
	
	/**
	 * Get a file unique id.
	 *
	 * @return string
	 */
	public function getUniqid() {
		return $this->uniqid;
	}
	
	/**
	 * Get a mime-type of the file.
	 *
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Get a client filename.
	 *
	 * @return string
	 */
	public function getFileName() {
		return $this->filename;
	}
	
	/**
	 * Get the file size in bytes.
	 *
	 * @return integer
	 */
	public function getSize() {
		return $this->size;
	}
	
	/**
	 * Get the file extension.
	 *
	 * @return string
	 */
	public function getExtension() {
		return $this->extension;
	}
	
	/**
	 * Get a file URL.
	 *
	 * @return string
	 */
	public function getURL() {
		return URL_TMP_USERFILES . "$this->uniqid.$this->extension";
	}
	
	/**
	 * Move a temporary file to a specified $destination.
	 *
	 * @return string
	 */
	public function move( $destination )
	{
		if( file_exists( $this->getPath() ) && is_file( $this->getPath() ) )
		{
			if ( !rename($this->getPath(), $destination) ) {
				throw new SK_TemporaryFileException('file moving failed', 0);
			}
		}
		
		SK_MySQL::query('DELETE FROM `'.TBL_TMP_USERFILE.'` WHERE `uniqid`="'.$this->uniqid.'"');
	}
	
}


class SK_TemporaryFileException extends Exception
{
	
}
