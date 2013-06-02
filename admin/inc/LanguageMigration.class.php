<?php

define("DIR_LANG_DUMP", DIR_USERFILES . "lang_dumps" . DIRECTORY_SEPARATOR);

class SK_LanguageMigration
{
	public static function createDumpDir()
	{
		if ( !file_exists( DIR_LANG_DUMP ) )
			mkdir( DIR_LANG_DUMP, 0777 );
	}

	public static function scanDumpDir()
	{
		$files = array();

		$handle = @opendir( DIR_LANG_DUMP );

		if ( !$handle )
			return array();

		while ( false !== ( $dump_file = readdir( $handle ) ) )
		{
			if ( $dump_file == '.' || $dump_file == '..' )
				continue;

			$files[] = $dump_file;
		}

		closedir( $handle );

		return $files;
	}

	public static function downloadFile($file) {
		$file = trim( $file );

		self::sendDownloadFileHeaders( $file );
                exit;
	}

	private static function sendDownloadFileHeaders( $file_name )
	{
		$file_name = trim( $file_name );
                $path = DIR_LANG_DUMP . $file_name;

		header( 'Content-Type: application/x-gzip' );
		header( 'Content-Disposition: inline; filename="'.$file_name.'"' );
		header( 'Expires: '.gmdate('D, d M Y H:i:s').' GMT' );
		header( 'Content-Disposition: inline; filename="'.$file_name.'"' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );

		readfile($path);
	}
}


class SK_LanguageExport
{
	public static function exportLanguage($lang_id)
	{
		$dump = new LanguageDump($lang_id);

		self::fill_structure($dump, $dump->language_info()->lang_id);

		$dump->toFile(DIR_LANG_DUMP . $dump->language_info()->abbrev . ".xml");

	}

	private static function fill_structure(LanguageDump $dump, $lang_id, LanguageDump_Section $parent_section = null, $section_id = null) {
		if (!isset($section_id)) {
			$section_id = 0;
		}

		if (!isset($parent_section)) {
			$parent_section = false;
		}

		$sections = SK_LanguageEdit::getSections($section_id);

		if (!$sections) {
			return false;
		}

		$out = array();

		foreach ($sections as $section)
		{
			if ($parent_section) {
				$_section = $parent_section->addSection($section->section);
			} else {
				$_section = $dump->creatSection($section->section);
			}

			if ($_section) {
				$keys = self::keys($section->lang_section_id, $lang_id);

				foreach ($keys as $key => $value) {
					$_section->addKey($key, $value);
				}

				self::fill_structure($dump, $lang_id, $_section, $section->lang_section_id);
			}
		}
		return true;
	}

	private static function keys($section_id, $lang_id) {
		$keys = SK_LanguageEdit::getKeyNodes($section_id);
		$out = array();
		foreach ($keys as $key) {
			$out[$key->key] = $key->values[$lang_id];
		}
		return $out;
	}

}

function checkUploadedFile( $form_name, $max_file_size, $file_dist = '' )
{
	if ( $_FILES[$form_name]['error'] )
	{
		@unlink( $_FILES[$form_name]['tmp_name'] );
		return -1;
	}

	if ( $_FILES[$form_name]['size'] > $max_file_size ) {
		return -1;
	}


	if ( !is_uploaded_file( $_FILES[$form_name]['tmp_name'] ) )
	{
		@unlink( $_FILES[$form_name]['tmp_name'] );
		return -2;
	}

	if ( function_exists("is_executable") && is_executable( $_FILES[$form_name]['tmp_name'] ) )
	{
		@unlink( $_FILES[$form_name]['tmp_name'] );
		return -3;
	}

	if ( !@filesize( $_FILES[$form_name]['tmp_name'] ) )
	{
		@unlink( $_FILES[$form_name]['tmp_name'] );
		return -4;
	}


	if ( $file_dist )
		@move_uploaded_file( $_FILES[$form_name]['tmp_name'], $file_dist );

	return true;
}


class SK_LanguageImport
{
	/**
	 * DOMDocument object
	 *
	 * @var DOMDocument
	 */
	private static $xml;

	private static $lang_id;

	/**
	 * DOMXPath object
	 *
	 * @var DOMXPath
	 */
	private static $xpath;

	public static function construct() {
		self::$xml = new DOMDocument('1.0');
	}

	public static function import($params) {

		$fileName = @trim($params["file_name"]);

		if ( empty($fileName) ) {
			throw new SK_LanguageMigrateException("Specify dump file", 1);
		}

		$file = DIR_LANG_DUMP . $fileName;

		if (!file_exists($file)) {
			throw new SK_LanguageMigrateException("Can not open specified dump file", 1);
		}

                $fileExt = strtolower(substr($fileName, (strrpos($fileName, '.') + 1)));
                $gzipped = $fileExt != 'xml';

		if (!self::load($file, $gzipped)) {
			throw new SK_LanguageMigrateException("Wrong dump file", 1);
		}

		if (isset($params["lang_id"])) {

			if ( !SK_MySQL::query(SK_MySQL::placeholder("
				SELECT COUNT(*) FROM `" . TBL_LANG . "` WHERE `lang_id`=?
			", $params["lang_id"]))->fetch_cell()) {
				throw new SK_LanguageMigrateException("Undefined Language", 1);
			}

			self::$lang_id = (int) $params["lang_id"];
		} else {
			if ((!isset($params['lang_label']) || !isset($params['lang_abr']))) {
				throw new SK_LanguageMigrateException("'Language with same lable ar abbreviation already exists! Try other'", 1);
			}

			self::$lang_id = self::newLanguage($params['lang_label'], $params['lang_abr']);
		}

		self::fill_sections(self::$lang_id );

		SK_LanguageEdit::generateCache();
	}

	private static function newLanguage($lang_label, $lang_abr) {
		$result = SK_MySQL::query(SK_MySQL::placeholder("
			SELECT COUNT(*) FROM `" . TBL_LANG . "` WHERE `abbrev`='?' OR `label`='?'
		", $lang_abr, $lang_label))->fetch_cell();

		if ($result) {
			throw new SK_LanguageMigrateException("Language with this name or abbreviation already exists", 1);
		}

		SK_MySQL::query(SK_MySQL::placeholder("
			INSERT INTO `" . TBL_LANG . "`(`abbrev`, `label`) VALUES('?', '?' )
		", $lang_abr, $lang_label));

		return SK_MySQL::insert_id();
	}

	private static function fill_sections($lang_id, $parent_section_name = null)
	{
		$sections = self::section_list($parent_section_name);

		if ($sections->length <= 0) {
			return false;
		}

		foreach ($sections as $section) {
			if (isset($parent_section_name)) {
				$_parent_section_name = $parent_section_name . "." . $section->getAttribute("name");
			} else {
				$_parent_section_name = $section->getAttribute("name");
			}

			try {
				$section_id = SK_LanguageEdit::section_id($_parent_section_name);
			} catch (SK_LanguageEditException  $e) {
				continue;
			}


			foreach (self::keys($_parent_section_name) as $key){
				$value = $key->textContent;
				try {
					self::setKey($section_id, $key->getAttribute("name"), $value, self::$lang_id);
				} catch (SK_LanguageMigrateException $e) {}
			}

			self::fill_sections($lang_id, $_parent_section_name);
		}
	}

	private static function setKey($section_id, $key, $value, $lang_id)
	{
		if (!($section_id = (int)$section_id)) {
			throw new SK_LanguageMigrateException("wrong section id", 2);
		}

		if (!($key = trim($key))) {
			throw new SK_LanguageMigrateException("wrong key", 2);
		}

		if (!($lang_id = (int)$lang_id)) {
			throw new SK_LanguageMigrateException("wrong lang id", 2);
		}

		$key_id = SK_MySQL::query(SK_MySQL::placeholder("
			SELECT `lang_key_id` FROM `" . TBL_LANG_KEY . "` WHERE `lang_section_id`=? AND `key`='?'
		", $section_id, $key))->fetch_cell();

		$existing_value = SK_MySQL::query(SK_MySQL::placeholder("
			SELECT `value` FROM `" . TBL_LANG_VALUE . "` WHERE `lang_id`=? AND `lang_key_id`=?
		", $lang_id, $key_id))->fetch_cell();

		if ( $existing_value !== false ) {
			if ($existing_value == $value) {
				return false;
			}

			SK_MySQL::query(SK_MySQL::placeholder("
				UPDATE `" . TBL_LANG_VALUE . "` SET `value`='?' WHERE `lang_id`=? AND `lang_key_id`=?
			", $value, $lang_id, $key_id));

		} else {
			SK_MySQL::query(SK_MySQL::placeholder("
				INSERT INTO `" . TBL_LANG_VALUE . "`(`value`, `lang_key_id`, `lang_id`) VALUES('?', ?, ?)
			", $value, $key_id, $lang_id));
		}
		return (bool) SK_MySQL::affected_rows();

	}


	/**
	 * Returns Section DOMElement
	 *
	 * @param string $section_path
	 * @return DOMElement
	 */
	public static function section($section_path) {
		$_section_path = "";
		$section_path_nodes = explode(".", $section_path);
		foreach ($section_path_nodes as $item) {
			$_section_path .= '/section[@name="' . $item . '"]';
		}

		$_section_path = ltrim($_section_path, "/");

		return self::query($_section_path)->item(0);
	}

	public static function keys($section_path) {
		return self::query("key", self::section($section_path));
	}

	public static function load($file_name, $gzipped = true)
	{
		if ($gzipped) {
			$file_name = self::gunzip($file_name);
		}

		if (!@self::$xml->load($file_name)) {
			@unlink($file_name);
			return false;
		}

		@unlink($file_name);

		self::$xml->preserveWhiteSpace = false;
		self::$xpath = new DOMXPath(self::$xml);
		return true;
	}

	private static function gunzip($file_name, $replace = false) {
		if (!($dump_arr = @gzfile($file_name))) {
			throw new SK_LanguageMigrateException("Language dump file damaged and can not be imported", 2);
		}
		$file_dump = implode("", $dump_arr);

		$file_name_arr = explode('.', $file_name);
		array_pop($file_name_arr);
		$_file_name = implode('.', $file_name_arr);

		$new_file_name = file_exists($_file_name . '.xml') ? $_file_name . '(2).xml' : $_file_name . '.xml';

		$fp = fopen($new_file_name, "w");
		fwrite($fp, $file_dump);
		fclose($fp);
		if ($replace) {
			@unlink($file_name);
		}

		return $new_file_name;
	}

	public static function section_list($parent_name = null) {

		if ($parent_name) {

			$parent = self::section($parent_name);
			if (!$parent) {
				return new DOMNodeList();
			}
			$entries = self::query("section", $parent);
		} else {
			$entries = self::query("section");
		}

		return $entries;
	}

	public static function query($query, $context = null) {
		if (isset($context)) {
			return self::$xpath->query($query, $context);
		}
		return self::$xpath->query($query);
	}
}
SK_LanguageImport::construct();

class LanguageDump
{
	private $language;

	/**
	 * DOMDocument object of Dump
	 *
	 * @var DOMDocument
	 */
	private $xml;

	/**
	 * Root element of xml dump
	 *
	 * @var DOMElement
	 */
	private $root_element;

	public function __construct($lang_id)
	{
		$this->xml = new DOMDocument('1.0');

		$this->language = $this->language($lang_id);

		if (!$this->language) {
			throw new SK_LanguageMigrateException("Undefined Language", 1);
		}

		$this->root_element = $this->xml->createElement("language");

		$this->xml->appendChild($this->root_element);
	}


	/**
	 * Add section to document and returns it
	 *
	 * @param string $name
	 * @return LanguageDump_Section
	 */
	public function creatSection($name) {
		$section = new LanguageDump_Section($name, $this);
		return $section;
	}

	private function language($lang_id) {

		if (isset($this->language)) {
			return $this->language;
		}

		return SK_MySQL::query(SK_MySQL::placeholder("
			SELECT * FROM `" . TBL_LANG . "` WHERE `lang_id`=?
		", $lang_id))->fetch_object();
	}

	public function language_info() {
		return $this->language;
	}

	/**
	 * Returns DOMDocument object of Dump
	 *
	 * @return DOMDocument
	 */
	public function xml_document() {
		return $this->xml;
	}

	/**
	 * Returns Root element of xml dump
	 *
	 * @return DOMElement
	 */
	public function xml_root_element() {
		return $this->root_element;
	}

	public function toFile($file_name, $gzipped = true) {
		$this->xml->save($file_name);
		if ($gzipped) {
			self::gzip($file_name, true);
		}
	}

	private static function gzip($file_name, $replace = false) {
		$data = implode("", file($file_name));
		$gzdata = gzencode($data);
		$fp = fopen("$file_name.gz", "w");
		fwrite($fp, $gzdata);
		fclose($fp);
		if ($replace) {
			@unlink($file_name);
		}
	}

}


class LanguageDump_Section
{
	/**
	 * Dump
	 *
	 * @var LanguageDump
	 */
	private $dump;

	/**
	 * Parent Section
	 *
	 * @var LanguageDump_Section
	 */
	private $parent_section;

	/**
	 * Current Node
	 *
	 * @var DOMElement
	 */
	private $node;

	private $name;

	public function __construct($section_name, LanguageDump $dump, LanguageDump_Section $parrent_section = null) {
		$this->name = $section_name;
		$this->dump = $dump;
		$this->parent_section = $parrent_section;

		$this->node = $dump->xml_document()->createElement("section");
		$this->node->setAttribute("name", $section_name);

		if (!isset($parrent_section)) {
			$dump->xml_root_element()->appendChild($this->node);
		} else {
			$parrent_section->node()->appendChild($this->node);
		}

	}

	public function name() {
		return $this->name;
	}

	/**
	 * Returns section node
	 *
	 * @return DOMElement
	 */
	public function node() {
		return $this->node;
	}

	/**
	 * Add key node to curent section and returns this section
	 *
	 * @param string $key
	 * @param string $value
	 * @return DOMElement
	 */
	public function addKey($key, $value) {

		$node = $this->dump->xml_document()->createElement("key");
		$content = $this->dump->xml_document()->createTextNode($value);
		$node->appendChild($content);
		$node->setAttribute("name", $key);
		$this->node->appendChild($node);
		return $this;
	}


	/**
	 * Add new child section and returns it
	 *
	 * @param string $name
	 * @return LanguageDump_Section
	 */
	public function addSection($name) {

		return new LanguageDump_Section($name, $this->dump, $this);
	}
}


class SK_LanguageMigrateException extends Exception {}