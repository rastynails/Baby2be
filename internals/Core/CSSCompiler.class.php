<?php

class Core_CSSCompiler
{
	/**
	 * Reference to a current instance of layout compiler.
	 *
	 * @var Core_LayoutCompiler
	 */
	private static $LayoutCompiler;

	/**
	 * Interface styles memory.
	 *
	 * @var array
	 */
	private static $interface = array();

	/**
	 * Name of a currently parsed file.
	 *
	 * @var string
	 */
	private static $current_file;

	private static $selectors_regex = '[*+\$\.\#\w<>:/\~]+[*+\$\.\#\w\-<>\:\[=\],\s/\~]+';
	private static $property_regex = '[a-z\-]+';
	private static $value_regex = '[^;]+';

	/**
	 * Read interface styles and memorize it to self::$interface var.
	 */
	private static function load_interface()
	{
		static $interface_css;

		if ( isset($interface_css) ) { // interface is already loaded
			return $interface_css;
		}

		//$interface_css = array();

		self::parse_file('interface.style', $interface_css);

		$theme_dir = SK_Layout::theme_dir();
		if ( isset($theme_dir) ) {
			try {
				self::parse_file($theme_dir.'interface.style', $interface_css);
			}
			catch ( Core_CSSCompilerException $e ) {
				// no `interface.style` file in theme
			}
		}

		self::parse_file('layout.style', $interface_css);

		return $interface_css;
	}


	/**
	 * Compiles a "header" CSS files inclusion.
	 *
	 * @param Core_LayoutCompiler $compiler
	 * @return string php code
	 */
	public static function interface_include( Core_LayoutCompiler $compiler )
	{
		// linking to a current instance of compiler
		self::$LayoutCompiler = $compiler;

		$interface_css = self::load_interface();

		$interface_file_url =
			self::compile_resource('interface.style', $interface_css);

		return "\$this->includeCSSFile('$interface_file_url');\n";
	}

	/**
	 * Compiles a CSS file and returns a string of PHP code for compiled resource inclusion.
	 *
	 * @param string $params['filename'] relative from DIR_LAYOUT
	 * @param string $params['ns_class'] optional namespace selector
	 * @param Core_LayoutCompiler $compiler
	 * @return string php code
	 */
	public static function css_include( array $params, Core_LayoutCompiler $compiler )
	{
		if ( !isset($params['filename']) ) {
			$compiler->_syntax_error('missing attribute "filename"', E_USER_ERROR, __FILE__, __LINE__);
		}

		// linking to a current instance of compiler
		self::$LayoutCompiler = $compiler;

		if ( !self::$interface ) {
			// loading interfaces if need
			self::load_interface();
		}

		// copying interface memory
		$_interface = self::$interface;

		try {
			self::parse_file($params['filename'], $css_data);

			$theme_dir = SK_Layout::theme_dir();
			if ( isset($theme_dir) )
			{
				$theme_css_file = $theme_dir.$params['filename'];
				try {
					self::parse_file($theme_css_file, $css_data);
				}
				catch ( Core_CSSCompilerException $e ) {
					if ( $e->getCode() != Core_CSSCompilerException::FILE_READ_FAILURE ) {
						$compiler->_syntax_error($e->getMessage(), E_USER_WARNING, $e->getFile(), $e->getLine());
					}
				}
			}

			$css_file_url = self::compile_resource(
				$params['filename'], $css_data, $params['ns_class']
			);
		}
		catch ( Core_CSSCompilerException $e ) {
			$compiler->_syntax_error($e->getMessage(), E_USER_WARNING, $e->getFile(), $e->getLine());
		}

		// rolling back interface memory
		self::$interface = $_interface;

		$css_inclusion = $css_file_url ? "\$this->includeCSSFile('$css_file_url');\n" : null;

		return $css_inclusion;
	}


	/**
	 * Parse a stylesheet file.
	 *
	 * @param string $filename relative to DIR_LAYOUT
	 * @param array $output_data css data to merge
	 */
	private static function parse_file( $filename, array &$output_data = null )
	{
		self::$current_file = DIR_LAYOUT . $filename;

		$output_data = isset($output_data) ? $output_data : array();

		// reading source file
		if ( false === (
			$tpl_source = self::$LayoutCompiler->_read_file(self::$current_file)
		) ) {
			throw new Core_CSSCompilerException(
				'failed to open css template file "'.self::$current_file.'"',
				Core_CSSCompilerException::FILE_READ_FAILURE
			);
		}

		// erasing commented code
		$tpl_source = preg_replace_callback(
			'~/\*' . '.+' . '\*/~sU' // [^(?:\*/)]
			, 'css_comment_eraser', $tpl_source
		);

		// regex parsing
		if ( !preg_match_all(
				'~(\S'.self::$selectors_regex.')\s*\{([^\}]+)\}~isU'
				, $tpl_source
				, $match, PREG_SET_ORDER
		) ) {
			return array();
		}

		// processing blocks
		$clear_selector_memory = 0;
		foreach ( $match as $match_decl )
		{
			if ( DEV_MODE ) {
				$curr_line = self::getLineNum($tpl_source, $match_decl[0]);
			}

			$selectors_set = self::parse_selectors($match_decl[1], $prototype);

			if ( $prototype == '~' ) { // clearing selectors output memory
				$clear_selector_memory = 1;
				$prototype = null;
			}

			// parsing rules
			$rules = self::parse_rules($match_decl[2], $prototype, $curr_line);

			// merge process
			foreach ( $selectors_set as $i => $selector )
			{
				// memorizing to interface
				if ( !isset(self::$interface[$selector]) ) {
					self::$interface[$selector] = $rules;
				}
				else {
					foreach ( $rules as $property => $value ) {
						self::$interface[$selector][$property] = $value;
					}
				}

				if ( $selector{0} == '$' ) { // hidden (interface) selector
					unset($selectors_set[$i]);
				}
			}

			// merging output
			if ( count($selectors_set) )
			{
				$selectors_decl = implode(', ', $selectors_set);

				if ( $clear_selector_memory ) { // clearing selectors output memory
					unset($output_data[$selectors_decl], $clear_selector_memory);
				}

				if ( !isset($output_data[$selectors_decl]) ) {
					$output_data[$selectors_decl] = $rules;
				}
				else {
					$output_data[$selectors_decl] =
						array_merge($output_data[$selectors_decl], $rules);
				}
			}
		}
	}

	/**
	 * Parse selectors declaration string into an array list.
	 * If there are prototype declaration found - prototype selector
	 * will be added to a result array with 'prototype' index.
	 *
	 * @param string $decl_str
	 * @return array
	 */
	private static function parse_selectors( $decl_str, &$prototype = null )
	{
		preg_match(
			'/^(.+)\s*(?:(?-U)\<\<\s?\s*(.+))?$/sU',
			$decl_str, $match
		);

		$prototype = @$match[2];

		return preg_split('~\s*,\s*~', $match[1]);
	}

	/**
	 * Parse rules declaration into an array.
	 *
	 * @param string $decl_str
	 * @param string|array $prototype
	 * @param integer $curr_line
	 */
	private static function parse_rules( $decl_str, $prototype = null, $curr_line = 0 )
	{
		if ( $prototype && !is_array($prototype) )
		{
			if ( !isset(self::$interface[$prototype]) ) {
				$err_line = $curr_line + self::getLineNum($decl_str, $match[0]);
				trigger_error(
					'CSS compile-time error: undefined prototype selector "'.$prototype.'" '.
						'in ['.self::$current_file.' line '.$err_line.']'
					, E_USER_WARNING
				);
			}
			else {
				$prototype = self::$interface[$prototype];
			}
		}

		if ( !preg_match_all(
			'~\s*('.self::$property_regex.')\s*:\s*('.self::$value_regex.');?\s*~',
				$decl_str, $matches, PREG_SET_ORDER
		) ) {
			return ($prototype && is_array($prototype)) ? $prototype : array();
		}

		$properties = $prototype ? $prototype : array();

		foreach ( $matches as $rule )
		{
			$property = $rule[1];
			$value = $rule[2];

			if ( strpos($value, '<<') === 0 ) {
				$value = self::get_property($value, $property, $curr_line);
			}

			// working with images
			if ( $property == 'background' || $property == 'background-image' )
			{
				$filename_regex = '([^\)\[\s]+)';
				$crop_dims_regex = '\[([\d,\s]+)\]';
				if ( preg_match(
					'~(?:image|img)\('.$filename_regex.'\s?(?:'.$crop_dims_regex.')?\)~'
						, $value , $match
				) ) {
					@list($img_match, $filename, $crop_dims) = $match;

					$themed_imagefile = DIR_LAYOUT.SK_Layout::theme_dir().'img'.DIRECTORY_SEPARATOR.$filename;

					if ( file_exists($themed_imagefile) ) {
						$imagefile = $themed_imagefile;
						$image_url = URL_LAYOUT.SK_Layout::theme_dir(true).'img/'.$filename;
					}
					else {
						$imagefile = DIR_LAYOUT.'img'.DIRECTORY_SEPARATOR.$filename;
						$image_url = URL_LAYOUT.'img/'.$filename;
					}

					if ( isset($crop_dims) ) {
						$image_url =
							self::compile_image(array(
								'filename'	=> $imagefile,
								'crop_dims'	=> $crop_dims
							));
					}

					$value = str_replace($img_match, "url($image_url)", $value);
				}
				else if( preg_match(
                    '~(?:src)\('.$filename_regex.'\)~'
                        , $value , $match
                ) )
				{
				    @list($img_match, $filename) = $match;
				    $image_url = SITE_URL . $filename;
				    $value = str_replace($img_match, "url($image_url)", $value);
				}
			}

			$properties[$property] = $value;
		}

		return $properties;
	}

	/**
	 * Get a propery value for a placeholder declaration.
	 *
	 * @param string $decl_str
	 * @param string $def_property default property
	 * @param integer $curr_line
	 * @return string
	 */
	private static function get_property( $decl_str, $def_property, $curr_line = 0 )
	{
		preg_match(
			'~^(?:<<)?\s*([^>]+)\s*(?:\->\s*('.self::$property_regex.'))?$~s',
			$decl_str, $match
		);

		$selector = &$match[1];

		if ( isset($match[2]) ) {
			$property_decl = &$match[2];
		}

		if ( !isset(self::$interface[$selector]) ) {
			$err_line = $curr_line + self::getLineNum($decl_str, $match[0]);
			trigger_error(
				'CSS compile-time error: undefined prototype selector "'.$selector.'" '.
					'in ['.self::$current_file.' line '.$err_line.']'
				, E_USER_WARNING
			);
			return null;
		}

		if ( !isset($property_decl) )
		{
			if ( !isset(self::$interface[$selector][$def_property]) )
			{
				$err_line = $curr_line + self::getLineNum($decl_str, $match[0]);
				trigger_error(
					'CSS compile-time error: undefined prototype property "'.$selector.'->'.$def_property.'" '.
						'in ['.self::$current_file.' line '.$err_line.']'
					, E_USER_WARNING
				);
				return null;
			}

			return self::$interface[$selector][$def_property];
		}

		else // property defined declaration
		{
			if ( !isset(self::$interface[$selector][$property_decl]) ) {
				$err_line = $curr_line + self::getLineNum($decl_str, $match[0]);
				trigger_error(
					'CSS compile-time error: undefined prototype property "'.$selector.'->'.$property_decl.'" '.
						'in ['.self::$current_file.' line '.$err_line.']'
					, E_USER_WARNING
				);
				return null;
			}

			return self::$interface[$selector][$property_decl];
		}
	}

	/**
	 * Merges parsed CSS files data.
	 *
	 * @param array $css_data1
	 * @param array $css_data2
	 */
	private static function css_merge( array $css_data1, array $css_data2 )
	{
		foreach ( $css_data2 as $selectors => $properties )
		{
			if ( !isset($css_data1[$selectors]) ) {
				$css_data1[$selectors] = $properties;
			}
			else {
				$css_data1[$selectors] = array_merge($css_data1[$selectors], $properties);
			}
		}

		return $css_data1;
	}


	private static function fetch_current_decl_block()
	{
		$_selectors = '';
		$_rules = array();

		foreach ( self::$current_selectors as $selector => $rules )
		{
			if ( strpos($selector, '$') !== 0 ) {
				$_selectors .= strlen($_selectors) ? ", $selector" : $selector;
			}
			else {
				continue; // abstract selector (hidden)
			}

			foreach ( $rules as $property => $value ) {
				if ( !isset($_rules[$property]) ) {
					$_rules[$property] = "\n\t$property: $value;";
				}
			}
		}

		if ( !strlen($_selectors) ) {
			return '';
		}

		return "$_selectors {" . implode('', $_rules) . "\n}\n";
	}

	/**
	 * Compiles a CSS file for externally inclusion.
	 *
	 * @param string $filename path to file relatively to DIR_LAYOUT
	 * @param array $css_data parsed css data
	 * @param string $ns_class optional namespace class
	 * @throws Core_CSSCompilerException
	 * @return string url to a compiled file
	 */
	private static function compile_resource( $filename, array $css_data, $ns_class = null )
	{
		$compiled_content = '';

		foreach ( $css_data as $selectors => $properies )
		{
			$compiled_content .= "$selectors {\n";

			foreach ( $properies as $property => $value ) {
				$compiled_content .= "\t$property: $value;\n";
			}

			$compiled_content .= "}\n\n";
		}

		// writing compiled file
		$_params = array(
			'filename'	=>	self::$LayoutCompiler->get_external_auto_src($filename, $ns_class) . '.css',
			'contents'	=>	$compiled_content,
			'create_dirs' => true);

		require_once SMARTY_CORE_DIR . 'core.write_file.php';
        $layoutInS = SK_Layout::getInstance();
		smarty_core_write_file($_params, $layoutInS);

		return self::$LayoutCompiler->get_external_auto_src($filename, $ns_class, true) . uniqid('.css?');
	}

	/**
	 * Compiles an image and returns url to.
	 *
	 * @param string $filename
	 * @param string $crop_dims optional crop dimensions comma separated "$src_x, $src_y, $src_w, $src_h"
	 * @return string
	 */
	private static function compile_image( array $params )
	{
		$file_ext = substr(strrchr($params['filename'], '.'), 1);

		switch ( $file_ext )
		{
			case 'png':

				if ( isset($params['crop_dims']) )
				{
					list($src_x, $src_y, $src_w, $src_h) = preg_split('~\s*,\s*~', $params['crop_dims'], 4);

					$image = imagecreate($src_w, $src_h);
					$src_image = imagecreatefrompng($params['filename']);

					imagetruecolortopalette($image, true, 64);

					imagecopy($image, $src_image, 0, 0, $src_x, $src_y, $src_w, $src_h);
					imagedestroy($src_image);

					$auto_id = "$src_x-$src_y-$src_w-$src_h";
				}
				else {
					$image = imagecreatefrompng($params['filename']);
					$auto_id = null;
				}

				break;

			default:
				trigger_error(__CLASS__.'::'.__FUNCTION__.'() working with image extension "'.$file_ext.'" is not supported', E_USER_WARNING);
				break;
		}


		$auto_src = self::$LayoutCompiler->get_external_auto_src($params['filename'], $auto_id);

		$_params = array('dir' => dirname($auto_src));
		require_once(SMARTY_CORE_DIR . 'core.create_dir_structure.php');
		$smarty = SK_Layout::getInstance();
		smarty_core_create_dir_structure($_params, $smarty);


		switch ( $file_ext )
		{
			case 'png':
				imagepng($image, $auto_src);
				break;
		}

		imagedestroy($image);

		return self::$LayoutCompiler->get_external_auto_src($params['filename'], $auto_id, true);
	}

	/**
	 * Counts a line number of $code_needle in $code_haytack.
	 *
	 * @param string $code_haytack
	 * @param string $code_needle
	 * @return integer
	 */
	private static function getLineNum( $code_haytack, $code_needle ) {
		$carret_pos = strpos($code_haytack, $code_needle);
		$str_before = substr($code_haytack, 0, $carret_pos);
		return substr_count($str_before, "\n") + 1;
	}
}

/**
 * Erases a CSS comments leaving newlines unaffected.
 * Used as callback function in preg_replace_callback().
 *
 * @param array $regex_matches
 * @return string
 */
function css_comment_eraser( $regex_matches ) {
	return str_repeat("\n", substr_count($regex_matches[0], "\n"));
}


class Core_CSSCompilerException extends Exception
{
	const FILE_READ_FAILURE = 1;

	public function __construct($message, $code) {
		parent::__construct($message, $code);
	}
}
