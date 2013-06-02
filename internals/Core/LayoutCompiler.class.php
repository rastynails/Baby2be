<?php

require_once DIR_SMARTY . 'Smarty_Compiler.class.php';
require_once DIR_CORE . 'CSSCompiler.class.php';

class Core_LayoutCompiler extends Smarty_Compiler
{
	/**
	 * Parses tag attrs into an array like: $attr => $php_expression.
	 * Additionally this function catches a first "unnamed" attribute entry (SK template API).
	 * If the optional $parse_attrs is given as false only an "unnamed" attribute
	 * will be returned as a single item of an array.
	 *
	 * @param string $tag_attrs
	 * @param Core_LayoutCompiler $compiler
	 * @param boolean $parse_attrs
	 * @return array
	 */
	public static function parseTagAttrs( &$tag_attrs, Core_LayoutCompiler $compiler, $parse_attrs = true )
	{
		if ( preg_match('~^([^=\s]+)[\s$]*(?:\s|$)~i', $tag_attrs, $match) )
		{
			$primary_attr = $match[1];

			// cutting off a primary attribute declaration from
			$tag_attrs = substr($tag_attrs, strlen($match[0]));
		}
		else {
			$primary_attr = null;
		}

		$attrs = ($parse_attrs && strlen($tag_attrs)) ? $compiler->_parse_attrs($tag_attrs) : array();

		array_unshift($attrs, $primary_attr);

		return $attrs;
	}

	/**
	 * Compile parsed attributes into a PHP.
	 *
	 * @param array $attrs
	 */
	public static function compileParams( array $attrs )
	{
		$arg_list = array();
		foreach ( $attrs as $arg_name => $arg_val ) {
			if ( is_bool($arg_val) ) {
				$arg_val = $arg_val ? 'true' : 'false';
			}
			$arg_list[] = "'$arg_name' => $arg_val";
		}

		return 'array('.implode(',', $arg_list).')';
	}



	/* --- Layout Compiling --- */

	private static $canvas_info;

	/**
	 * Get canvas template dir.
	 *
	 * @param string $canvas_name
	 * @return string
	 */
	private static function canvas_tpl_dir( $canvas_name )
	{
		$canvas_tpl_dir = "canvas/$canvas_name/";

		$theme_dir = SK_Layout::theme_dir();

		// theme template override
		if ( SK_Layout::getInstance()->template_exists(
			$theme_dir . $canvas_tpl_dir . 'header.tpl'
		) ) {
			$canvas_tpl_dir = $theme_dir . $canvas_tpl_dir;
		}

		return $canvas_tpl_dir;
	}

	/**
	 * Compiles canvas tag open.
	 *
	 * @param string $tag_attrs
	 * @param Core_LayoutCompiler $compiler
	 */
	public static function CanvasStart( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		// catching canvas name
		$attrs = self::parseTagAttrs($tag_attrs, $compiler, false);
		$canvas_name = array_shift($attrs);

		// compiling interface styles inclusion
		$css_include = Core_CSSCompiler::interface_include($compiler);

		// no canvas page
		if ( $canvas_name == 'blank' ) {
			// keeping canvas info for footer
			self::$canvas_info = array($canvas_name);

			// code for blank page canvas start
			$php_output =
<<<EOT
global \$ajax_response;
if ( SK_Layout::current_component()->auto_id === 'httpdoc' && !isset(\$ajax_response) ) {
	$css_include
}\n
EOT;
			return $php_output;
		}


		// templated canvas compiling
		if ( !$canvas_name ) {
			$canvas_name = 'default';
		}

		$canvas_tpl_dir = self::canvas_tpl_dir($canvas_name);

		// keeping canvas info for footer
		self::$canvas_info = array($canvas_name, $canvas_tpl_dir, $tag_attrs);

		// compiling smarty include
		$header_tpl_include = substr(
			$compiler->_compile_include_tag("file='{$canvas_tpl_dir}header.tpl' $tag_attrs"), 5, -2
		);

		// demo mode case
		if ( defined('SK_DEMO_MODE') && SK_DEMO_MODE ) {
			$auto_url = "'".URL_STATIC."demo_tools.js.php?theme='.\$this->theme()";
			$header_tpl_include .= "\$this->frontend_handler()->include_js_file($auto_url);";
		}

		return <<<EOT
global \$ajax_response;
if ( SK_Layout::current_component()->auto_id === 'httpdoc' && !isset(\$ajax_response) ) {
	$css_include
	$header_tpl_include
}\n
EOT;
	}

	/**
	 * Compiles canvas tag close.
	 *
	 * @param string $tag_attrs
	 * @param Core_LayoutCompiler $compiler
	 */
	public static function CanvasEnd( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$canvas_name = array_shift(self::$canvas_info);

		if ( $canvas_name == 'blank' ) {
			// no canvas page
			return;
		}

		list($canvas_tpl_dir, $tag_attrs) = self::$canvas_info;

		$php_output = substr(
			$compiler->_compile_include_tag("file='{$canvas_tpl_dir}footer.tpl' $tag_attrs"), 5, -2
		);

		return
<<<EOT
global \$ajax_response;
if ( SK_Layout::current_component()->auto_id === 'httpdoc' && !isset(\$ajax_response) ) {
	$php_output
}\n
EOT;
	}

	/**
	 * Compiles a component call.
	 *
	 * @param string $tag_attrs
	 * @param Core_LayoutCompiler $compiler
	 * @return string
	 */
	public static function ComponentPlaceholder( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$attrs = self::parseTagAttrs($tag_attrs, $compiler);

		$component_name = array_shift($attrs);

		if ( !isset($component_name) ) {
			$compiler->_syntax_error('missing component name declaration', E_USER_ERROR, __FILE__, __LINE__);
		}

		$arg_list = array();
		foreach ( $attrs as $arg_name => $arg_value )
		{
			if ( is_bool($arg_value) ) {
				$arg_value = $arg_value ? 'true' : 'false';
			}

			$arg_list[] = "'$arg_name' => $arg_value";
		}

		$_params = count($arg_list) ? ', array('.implode(',', $arg_list).')' : '';


		// instantiating straight way
		if ( preg_match('~^[a-z]+\w*$~i', $component_name) )
		{
			$php_code =
<<<EOT
\n\$component = SK_Component('$component_name'$_params);\n
EOT;
		}

		// hooking registered component
		elseif ( preg_match('~^\$([a-z]+.*)$~i', $component_name, $match) )
		{
			$_tpl_var = $compiler->_parse_var($component_name);

			$php_code =
<<<EOT
\n\$component = $_tpl_var;\n
EOT;
			if ( $_params ) {
				$compiler->_syntax_error(
					'dynamycaly registered components can not take template params'
					, E_USER_NOTICE, __FILE__, __LINE__
				);
			}
		}

		$php_code .=
<<<EOT
\necho \$this->renderComponent(\$component);\n
EOT;

		return $php_code;
	}


	private static $container_stack = array();

	/**
	 * Component template start handler.
	 *
	 * @param string $tag_attrs
	 * @param Core_LayoutCompiler $compiler
	 * @return string
	 */
	public static function ContainerStart( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$attrs = $compiler->_parse_attrs($tag_attrs);

		$container_tag = isset($attrs['tag']) ? $attrs['tag'] : 'div';

		array_unshift(self::$container_stack, array(
			'tag' => $container_tag
		));

		if ( isset($attrs['text_ns']) ) {
			self::$container_stack[0]['text_ns'] = $attrs['text_ns'];
		}

		$component_ns = SK_Layout::current_component()->getNamespace();

		if ( isset($attrs['class']) ) {
			if ( DEV_MODE && !preg_match("~$compiler->_qstr_regexp~", $attrs['class']) ) {
				$compiler->_syntax_error(
					'component {container...} attribute "class" can take only a quoted string literal value (hardcoded)',
					E_USER_WARNING, __FILE__, __LINE__
				);
			}
			$container_class = substr($attrs['class'], 1, -1);
		}
		else {
			$container_class = $component_ns;
		}

		if ( isset($attrs['stylesheet']) )
		{
			if ( DEV_MODE && !preg_match("~$compiler->_qstr_regexp~", $attrs['stylesheet']) ) {
				$compiler->_syntax_error(
					'container attribute `stylesheet` can take only a quoted string literal value (hardcoded)'
					, E_USER_WARNING, __FILE__, __LINE__
				);
			}

			// including component stylesheet
			$_css_inclusion =
				Core_CSSCompiler::css_include(array(
					'filename' => "components/$component_ns/" . substr($attrs['stylesheet'], 1, -1),
					'ns_class' => $container_class
				), $compiler);
		}
		else {
			$_css_inclusion = '';
		}

		return
<<<EOT
$_css_inclusion
?>
<$container_tag id="<?php echo SK_Layout::current_component()->auto_id; ?>" class="$container_class">
<?php\n
EOT;
	}

	/**
	 * Component template end handler.
	 *
	 * @param string $tag_attrs
	 * @param Core_LayoutCompiler $compiler
	 * @return string
	 */
	public static function ContainerEnd( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		// unshifting current component.
		$container_info = array_shift(self::$container_stack);

		return
<<<EOT

?>
</{$container_info['tag']}>
<?php

EOT;
	}


	public static function TagAutoId( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$attrs = $compiler->_parse_attrs($tag_attrs);

		if ( !@$attrs['id'] ) {
			$compiler->_syntax_error('missing tag id value', E_USER_ERROR, __FILE__, __LINE__);
		}

		return <<<EOT
echo 'id="'.SK_Layout::current_component()->getTagAutoId({$attrs['id']}).'"';
EOT;
	}


	public static function tpl_prefilter( $tpl_resource, Core_LayoutCompiler $compiler )
	{
		$Layout = SK_Layout::getInstance();

		$curr_file = $compiler->_current_file;

		if ( $curr_file == 'Layout.tpl' ) {
			return $tpl_resource;
		}

		$themed_tpl = SK_Layout::theme_dir() . $curr_file;

		if ( $Layout->template_exists($themed_tpl) )
		{
			$_params = array(
				'resource_name'			=>	$themed_tpl,
				'source_content'		=>	&$source_content,
				'resource_timestamp'	=>	&$resource_timestamp,
				'get_source'			=>	true
			);

			$Layout->_fetch_resource_info($_params);

			/* TODO: Set $resource_timestamp to some smarty variable to make $smarty->compile_check working correctly.
				$_params['resource_timestamp'] = $filetime;
			*/

			$tpl_resource = $_params['source_content'];
		}

		// handling special tag attributes syntax (smarty hack)
		$tpl_resource = str_replace('{id=', '{tag_auto_id id=', $tpl_resource);

		return $tpl_resource;
	}



	/* -- Languages -- */

	/**
	 * Language pointers handler.
	 */
	public function _parse_var_props( $val )
	{
		if ( strpos($val, '%') == 1
			&& preg_match("~^(?:\"|')%([\w\.\$`]+)(?:\"|')$~i", $val, $match)
		) {
			return self::compileLanguageTextCall($match[1]);
		}

		return parent::_parse_var_props($val);
	}

	/**
	 * Compile a language text call.
	 *
	 * @param string $data_addr
	 * @param array $text_vars
	 */
	private function compileLanguageTextCall( $data_addr, array $text_vars = null )
	{
		if ( $data_addr{0} == '%' ) {
			$data_addr = substr($data_addr, 1);
		}

		if ( $data_addr{0} != '.' ) {
			if ( isset(self::$container_stack[0]['text_ns']) ) {
				$params_slug =
					self::$container_stack[0]['text_ns'] . ".'.'." .
						parent::_parse_var_props('"'.$data_addr.'"');
			}
			else {
				$params_slug = parent::_parse_var_props(
					'"components.' . SK_Layout::current_component()->getNamespace() . '.' . $data_addr . '"'
				);
			}
		}
		else {
			$params_slug = parent::_parse_var_props('"'.substr($data_addr, 1).'"');
		}

		if ( $text_vars ) {
			$params_slug .= ', '.self::compileParams($text_vars);
		}

		return "SK_Language::text($params_slug)";
	}


	public static function FormFieldLabel( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$attrs = self::parseTagAttrs($tag_attrs, $compiler);
		$primary_attr = array_shift($attrs);

		if ( !isset($attrs['for']) ) {
			$compiler->_syntax_error('missing attribute "for" in declaration of {label}', E_USER_WARNING, __FILE__, __LINE__);
		}

		if ( isset($primary_attr) && $primary_attr{0} == '%' ) {
			$attrs['text'] = $compiler->compileLanguageTextCall($primary_attr);
		}

		return 'echo $form->renderFieldLabel('.self::compileParams($attrs).');';
	}


	public static function Text( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$attrs = self::parseTagAttrs($tag_attrs, $compiler);
		$primary_attr = array_shift($attrs);

		if ( !isset($primary_attr) || $primary_attr{0} != '%' ) {
			$compiler->_syntax_error('missing primary attribute language text data address for {text}', E_USER_WARNING, __FILE__, __LINE__);
			return;
		}

		return 'echo '.$compiler->compileLanguageTextCall($primary_attr, $attrs).';';
	}



	/* --- Forms --- */

	/**
	 * Reference to currently opened form.
	 *
	 * @var SK_Form
	 */
	private static $current_form;


	public static function FormStart( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		if ( isset(self::$current_form) ) {
			$compiler->_syntax_error('form block is already opened in ...', E_USER_ERROR, __FILE__, __LINE__);
		}

		$attrs = self::parseTagAttrs($tag_attrs, $compiler);

		$form_name = array_shift($attrs);

		if ( DEV_MODE ) {
			if ( !$form_name ) {
				$compiler->_syntax_error('form name missing', E_USER_ERROR, __FILE__, __LINE__);
			}
			elseif ( !preg_match('~^\w+$~', $form_name) ) {
				$compiler->_syntax_error(
					'unrecognized form name identifier: ' .
						'"<code>'.htmlspecialchars($form_name).'</code>"'
					, E_USER_ERROR, __FILE__, __LINE__);
			}
		}

		// instantiating form
		$form_class = "form_$form_name";
		$form = new $form_class($form_name);


		$form->setup();
		require_once DIR_CORE . 'JSCompiler.class.php';
		Core_JSCompiler::compileFormJSHandler($form, $compiler);

		// compiling form php file
		$_compile_path = DIR_FORMS_C . $form->getName() . '.form.php';
		$_params = array(
			'compile_path' => $_compile_path,
			'compiled_content' => "<?php\n\$form = " . var_export($form, true) . ";\n?>"
		);
		require_once SMARTY_CORE_DIR . 'core.write_compiled_resource.php';
		smarty_core_write_compiled_resource($_params, $compiler);

		$output = "include '$_compile_path';\n";

		// parsing additional render params
		$_params = count($attrs) ? self::compileParams($attrs) : '';

		$output .=
<<<EOT
SK_Layout::current_component()->handleForm(\$form);
SK_Layout::frontend_handler()->registerForm(\$form);
echo \$form->renderStart($_params);
\$this->assign('form', \$form);\n
EOT;

		self::$current_form = $form;

		return $output;
	}


	public static function FormInputItem( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$attrs = $compiler->_parse_attrs($tag_attrs);

		if ( !isset($attrs['name']) ) {
			$compiler->_syntax_error('missing attribute "name" for function {field}', E_USER_ERROR, __FILE__, __LINE__);
		}

		$_params = self::compileParams($attrs);

		return "\necho \$form->renderInputItem($_params);\n";
	}


	public static function FormField( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$attrs = $compiler->_parse_attrs($tag_attrs);

		/*if ( !isset($attrs['name']) ) {
			$compiler->_syntax_error('missing attribute "name" for function {field}', E_USER_ERROR, __FILE__, __LINE__);
		}*/

		$_params = self::compileParams($attrs);

		return "\necho \$form->renderField($_params);\n";
	}


	public static function FormButton( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$attrs = $compiler->_parse_attrs($tag_attrs);

		if ( !isset($attrs['action']) ) {
			$compiler->_syntax_error('missing attribute "action" for function {button}', E_USER_ERROR, __FILE__, __LINE__);
		}

		$_params = self::compileParams($attrs);

		return "\necho \$form->renderButton($_params);\n";
	}


	public static function FormEnd( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		self::$current_form = null;

		return
<<<EOT
unset(\$this->_tpl_vars['form'], \$form);
echo '</form>';
EOT;
	}


	/**
     * get a concrete filename for automagically created content
     *
     * @param string $auto_base
     * @param string $auto_source
     * @param string $auto_id
     * @return string
     * @staticvar string|null
     * @staticvar string|null
     */
    public function get_external_auto_src( $auto_source = null, $auto_id = null, $url = false )
    {
    	if ( $this->use_sub_dirs ) {
    		$_compile_dir_sep = $url ? '..-URL-DIR-SEP-..' : DIRECTORY_SEPARATOR;
    	}
    	else {
        	$_compile_dir_sep = '^';
    	}

    	$layout_theme = SK_Layout::getInstance()->theme();

    	$_return = $layout_theme ? $layout_theme . $_compile_dir_sep : '';

        if ( isset($auto_id) ) {
            // make auto_id safe for directory names
            $auto_id = str_replace('%7C',$_compile_dir_sep,(urlencode($auto_id)));
            // split into separate directories
            $_return .= $auto_id . $_compile_dir_sep;
        }

        if(isset($auto_source)) {
            // make source name safe for filename
            $_filename = urlencode(basename($auto_source));
            $_crc32 = sprintf('%08X', crc32($auto_source));
            // prepend %% to avoid name conflicts with
            // with $params['auto_id'] names
            $_crc32 = substr($_crc32, 0, 2) . $_compile_dir_sep .
                      substr($_crc32, 0, 3) . $_compile_dir_sep . $_crc32;
            $_return .= '%%' . $_crc32 . '%%' . $_filename;
        }

        if ( $url ) {
        	return URL_EXTERNAL_C . (
        		$this->use_sub_dirs ? str_replace('..-URL-DIR-SEP-..', '/', urlencode($_return)) : $_return
    		);
        }
        else {
        	return DIR_EXTERNAL_C . $_return;
        }
    }


	public static function includeStyle( $tag_attrs, Core_LayoutCompiler $compiler )
	{
		$attrs = self::parseTagAttrs($tag_attrs, $compiler);

		if ( !(
			$file = $attrs[0] ? $attrs[0] : @$attrs['file']
		) ) {
			$compiler->_syntax_error('missing `file` attribute in include_style tag', E_USER_WARNING, __FILE__, __LINE__);
			return;
		}

		// syntax checking
		if ( DEV_MODE && !preg_match("~$compiler->_qstr_regexp~", $file) ) {
			$compiler->_syntax_error('include_style tag attribute `stylesheet` can take only a quoted string literal value (hardcoded)', E_USER_WARNING, __FILE__, __LINE__);
			return;
		}

		$file = substr($file, 1, -1); // removing quotes

		if ( strpos($compiler->_current_file, 'db:') !== 0 )
		{
			$curr_tpl_dir = $compiler->template_dir . dirname($compiler->_current_file) . '/';

			$theme_dir = SK_Layout::theme_dir();

			if ( strpos($curr_tpl_dir, DIR_LAYOUT.$theme_dir) === 0 ) {
				$_file = substr($curr_tpl_dir, strlen(DIR_LAYOUT.$theme_dir)) . $file;
			}
			elseif ( strpos($curr_tpl_dir, DIR_LAYOUT) === 0 ) {
				$_file = substr($curr_tpl_dir, strlen(DIR_LAYOUT)) . $file;
			}
			else {
				trigger_error(
					__CLASS__.'::'.__FUNCTION__.'() the only trusted dir for stylesheet files is `DIR_LAYOUT`, "'.$curr_tpl_dir.'" given'
					, E_USER_ERROR);
			}

			$file = $_file;
		}

		return Core_CSSCompiler::css_include(array(
			'filename' => $file
		), $compiler);
	}



	/* --- Error handling --- */
	private function triggerCompilerError( $error_msg, $error_type = E_USER_ERROR, $file = null, $line = null )
	{
		if ( isset($file) && isset($line) ) {
			$info = ' ('.basename($file).", line $line)";
		} else {
			$info = '';
		}

		trigger_error('LayoutCompiler error: [in ' . $this->_current_file. ' line ' . $this->_current_line_no . "]: $error_msg$info", $error_type);
	}

}

