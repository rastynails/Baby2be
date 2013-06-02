<?php

require_once DIR_SMARTY.'Smarty.class.php';

class SK_Layout extends Smarty
{
    const XML_BLOCK_TOOLBAR_POSITION = 'blockToolbarPosition';

	/**
	 * Active theme directory where template and plugin files are located.
	 *
	 * @var string
	 */
	private $theme_dir;

	/**
	 * Displaying components counter.
	 * Used to generate auto ids.
	 *
	 * @var integer
	 */
	private static $component_counter = 0;

	/**
	 * Currently rendering components stack.
	 *
	 * @var array
	 */
	private $component_stack = array();

	/**
	 * Currently displaying component work name.
	 * Used to define compile and cache directories.
	 *
	 * @var string
	 */
	protected $current_component_name;

	/**
	 * An instance of frontend handler.
	 *
	 * @var SK_Frontend
	 */
	protected $frontend_handler;

	/**
	 * This params sets up by a compiled httpDocument.
	 * You must define this params using a {canvas} wrapper in your httpDocument templates.
	 *
	 * @var array
	 */
	public $canvas_params;

	/**
	 * CSS registration data.
	 */
	private $inc_css_files = array(),
			$css_inc_decl = '';

	/**
	 * A singleton object.
	 *
	 * @var SK_Layout
	 */
	private static $instance;

	/**
	 * Currently opened blocks stack.
	 *
	 * @var array
	 */
	private $block_stack = array();

	/**
	 * Layout configs.
	 *
	 * @var SK_ConfigSection
	 */
	protected $configs;

	private $hiddenComponents = array();

	private $themeData = array();

    /**
	 * Constructor.
	 */
	private function __construct()
	{
		if ( isset(self::$instance) ) {
			trigger_error(__CLASS__.'::'.__FUNCTION__.'() singleton object is already instantiated, use '.__CLASS__.'::getInstance() instead', E_USER_ERROR);
		}

		parent::Smarty();

		// disabling php in templates
		$this->security = true;

		$this->security_settings  = array(
                                    'PHP_HANDLING'    => false,
                                    'IF_FUNCS'        => array('array', 'list',
                                                               'isset', 'empty',
                                                               'count', 'sizeof',
                                                               'in_array', 'is_array',
                                                               'true', 'false', 'null'),
                                    'INCLUDE_ANY'     => true,
                                    'PHP_TAGS'        => false,
                                    'MODIFIER_FUNCS'  => array('count'),
                                    'ALLOW_CONSTANTS'  => true
                                   );


		$this->configs = SK_Config::section('layout');

		$this->theme_dir = self::theme_dir();

		$this->template_dir = DIR_LAYOUT;

		$this->compile_dir = DIR_COMPONENTS_C;
		$this->cache_dir = DIR_SMARTY_CACHE;

		$this->use_sub_dirs = true;

		$this->compiler_class = 'Core_LayoutCompiler';
		$this->compiler_file = DIR_CORE . 'LayoutCompiler.class.php';

		if ( DEV_MODE ) {
			if ( isset($_GET['force_compile']) ) {
				$this->force_compile = (bool)$_GET['force_compile'];
			}
			elseif ( defined('DEV_FORCE_COMPILE') ) {
				$this->force_compile = DEV_FORCE_COMPILE;
			}
		}

		$this->compile_check = DEV_MODE;

		$this->caching = $this->configs->caching ? 2 : 0;
		$this->cache_lifetime = 0;
		$this->cache_modified_check = true;

		$this->php_handling = SMARTY_PHP_REMOVE;

		if ( defined('SK_ERROR_REPORTING') ) {
			$this->error_reporting = SK_ERROR_REPORTING;
		}

		$this->plugins_dir[] = DIR_INTERNALS.'Smarty_adds';

		parent::register_prefilter(array($this->compiler_class, 'tpl_prefilter'));

		parent::register_compiler_function('canvas', array($this->compiler_class, 'CanvasStart'), false);
		parent::register_compiler_function('/canvas', array($this->compiler_class, 'CanvasEnd'));

		parent::register_compiler_function('component', array($this->compiler_class, 'ComponentPlaceholder'), false);

		parent::register_compiler_function('container', array($this->compiler_class, 'ContainerStart'), false);
		parent::register_compiler_function('/container', array($this->compiler_class, 'ContainerEnd'), false);
		parent::register_compiler_function('tag_auto_id', array($this->compiler_class, 'TagAutoId'), false);

		parent::register_compiler_function('form', array($this->compiler_class, 'FormStart'), false);
		parent::register_compiler_function('label', array($this->compiler_class, 'FormFieldLabel'), true);
		parent::register_compiler_function('input', array($this->compiler_class, 'FormField'), false);
		parent::register_compiler_function('input_item', array($this->compiler_class, 'FormInputItem'), false);
		parent::register_compiler_function('button', array($this->compiler_class, 'FormButton'), false);
		parent::register_compiler_function('/form', array($this->compiler_class, 'FormEnd'), false);

		parent::register_compiler_function('include_style', array($this->compiler_class, 'includeStyle'), false);

		parent::register_compiler_function('text', array($this->compiler_class, 'Text'), true);

		parent::register_block('block', array($this, 'tpl_block'));
		parent::register_block('block_cap', array($this, 'tpl_block_cap'));

		parent::register_function('print_arr', array( &$this, 'tpl_printArr' ));

		//Language global vars
		$official_info_configs = SK_Config::section('site')->Section('official')->getConfigsList();

		$globals = array();
		foreach ($official_info_configs as $item) {
			$globals[$item->name] = $item->value;
		}
		SK_Language::defineGlobal($globals);

		$profile = !SK_HttpUser::is_authenticated()
		  ? false
		  : array(
		      'id' => SK_HttpUser::profile_id(),
		      'isModerator' => SK_HttpUser::isModerator()
		  );

                $langId = SK_Language::current_lang_id();

		$smartyGlobals = array(
		  'profile' => $profile,
                  'language' => $langId
		);

		$this->assign('SK', $smartyGlobals);

	    if ( app_Cometchat::isActive() )
        {
            $this->includeCSSFile(SITE_URL.'cometchat/cometchatcss.php');
        }

        //$this->themeData = $this->readThemeXml();
	}

	/**
	 * Instantiates and returns a reference to the Layout singleton.
	 *
	 * @return SK_Layout
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	public static function theme()
	{
		static $theme;

		if ( !isset($theme) )
		{
			if ( defined('SK_DEMO_MODE') && SK_DEMO_MODE )
			{
				if ( isset($_GET['layout']) ) {
					$theme = $_GET['layout'];
					$_SESSION['demo_tools.active_theme'] = $theme;
				}
				elseif ( isset($_SESSION['demo_tools.active_theme']) ) {
					$theme = $_SESSION['demo_tools.active_theme'];
				}
			}

			if ( !isset($theme) || !self::isThemeAvailable($theme) ) {
				$theme = (DEV_MODE && defined('DEV_LAYOUT_THEME')) ? DEV_LAYOUT_THEME : SK_Config::section('layout')->theme;
			}
		}

		return $theme;
	}

    public static function getThemeByName( $theme )
    {
        $query = SK_MySQL::placeholder("
			SELECT * FROM `" . TBL_THEME . "` WHERE `theme_name`='?'", $theme);

        return SK_MySQL::query($query)->fetch_assoc();
    }

	private static function isThemeAvailable($theme)
	{
		$query = SK_MySQL::placeholder("
			SELECT 1 FROM `" . TBL_THEME . "`
				WHERE `theme_name`='?'
		", $theme);

		return (bool) SK_MySQL::query($query)->fetch_cell();
	}

	/**
	 * Returns an active theme directory name.
	 *
	 * @return string
	 */
	public static function theme_dir( $get_url_path = false ) {

		if (isset(self::$instance)) {
			$theme_dir = self::$instance->theme_dir;
		} else {
			$theme_dir = self::theme() ? 'themes/' . self::theme() . '/' : null;
		}

		return !$get_url_path ? $theme_dir : str_replace('\\', '/', $theme_dir);
	}

	/**
	 * A getter for an instance of frontend handler object.
	 *
	 * @return SK_Frontend
	 */
	public static function frontend_handler() {
		return self::$instance->frontend_handler;
	}

	/**
	 * Retuns a reference to a currently displayng component.
	 *
	 * @return SK_Component
	 */
	public static function current_component() {
		return self::$instance->component_stack[0];
	}

	/**
	 * Retuns a reference to a currently displayng component parent.
	 *
	 * @return SK_Component
	 */
	public static function current_component_parent() {
		return isset(self::$instance->component_stack[1])
			? self::$instance->component_stack[1]
			: Object(array('auto_id' => 'httpdoc')); // temporary hack for component SignIn hidden
	}

	private function readThemeXml()
	{
	    $themeName = self::theme();
	    $cacheKey = 'theme_data_' . $themeName;
	    $themeData = DEV_FORCE_COMPILE ? false : SK_Cache::get($cacheKey);

	    if ( $themeData )
	    {
            $jsonThemeData = @json_decode($themeData, true);

            return empty($jsonThemeData) ? array() : $jsonThemeData;
	    }

	    $themeDir = self::theme_dir();
        $xmlPath = DIR_LAYOUT . $themeDir . 'theme.xml';

        if ( !file_exists($xmlPath) )
        {
            return array();
        }

        $themeData = (array) @simplexml_load_file($xmlPath);

        $themeData = empty($themeData) ? array() : $themeData;

        SK_Cache::set($cacheKey, json_encode($themeData));

        return $themeData;
	}

	private static function clearXmlCache()
	{
	    $query = "SELECT `theme_name` FROM " . TBL_THEME;
	    $r = SK_MySQL::query($query);
	    while ( $themeName = $r->fetch_cell() )
	    {
            SK_Cache::clear('theme_data_' . $themeName);
	    }
	}

	public function renderHiddenComponent($key, SK_Component $cmp)
	{
	    $this->hiddenComponents[$key] = $this->renderComponent($cmp, $key);
	}

	/**
	 * Display an http document component.
	 *
	 * @param SK_Component $httpdoc
	 */
	public function display( $httpdoc, $cache_id = null, $compile_id = null )
	{
		$this->frontend_handler = new SK_Frontend();
SK_Profiler::getInstance('app')->mark('display -> frontend init');
		$this->assign('content_header', '{%$#CONTENT_HEADER#$%}');

                $this->renderHiddenComponent('ping', new component_Ping());
		$this->renderHiddenComponent('sign_in_hidden', new component_SignIn(array('hidden' => true)));
SK_Profiler::getInstance('app')->mark('display -> render hid cmp');
		// rendering httpdoc components
        $html_body = $this->renderComponent($httpdoc, 'httpdoc');

SK_Profiler::getInstance('app')->mark('display -> render httpdoc');
		if ( DEV_MODE && @$_GET['force_compile'] ) {
			// redirecting developer if force compile is requested
			$request_uri = preg_replace('~(?:\?|&)force_compile=[^&]+~', '',$_SERVER['REQUEST_URI']);
			SK_HttpRequest::redirect($request_uri);
		}

		if ( !isset($httpdoc->document_meta) ) {
			$httpdoc->document_meta = new SK_HttpDocumentMeta();
		}
SK_Profiler::getInstance('app')->mark('display -> dev actions');
		$meta_info = $httpdoc->document_meta->get();
SK_Profiler::getInstance('app')->mark('display -> meta get');
		$title = SK_Language::htmlspecialchars($meta_info->title);
		$description = SK_Language::htmlspecialchars($meta_info->description);
		$keywords = SK_Language::htmlspecialchars($meta_info->keywords);

		$seo_config_section = SK_Config::section("site")->Section("seo");
		$add_meta = $seo_config_section->Section("meta")->add_meta;
		$google_code = $seo_config_section->Section("google_analytics")->enabled
						? $seo_config_section->Section("google_analytics")->code
						: '';

		$this->assign("google_code", $google_code);

		if ( DEV_MODE ) {
			$this->frontend_handler->include_js_file(URL_STATIC.'dev_toolbar.js');
		}

		$head_scripts = $this->frontend_handler->renderScripts();

                if ( (bool)SK_Config::section('site.additional.profile')->enable_no_follow )
                {
                    $reg = '/(<a)((\s+(?=[^\s])(?!rel)(?!href=[\'"][^\s]*?(' . preg_quote(SITE_URL, '/') . '|javascript)[^\s]*?[\'"])[\w\-]+?=[\'"][^\'"]*?[\'"])*)?>/';
                    $html_body = preg_replace( $reg, '\1 rel="nofollow" \2 >', $html_body);
                }

		parent::assign(array(
			'html_head' =>
<<<EOT
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>$title</title>
	<meta name="description" content="$description" />
	<meta name="keywords" content="$keywords" />
	$this->css_inc_decl
	$head_scripts
	$add_meta
        $meta_info->meta
EOT
			, 'html_body' =>
				str_replace('{%$#CONTENT_HEADER#$%}', $meta_info->content_header, $html_body)
		));

		$this->caching = 0;
		$this->current_component_name = 'layout';

		$this->assign('hiddenComponents', $this->hiddenComponents);

		$this->assign('language_tag', SK_Language::getLanguageTag(SK_HttpUser::language_id()));

        $theme = self::getThemeByName(self::theme());
        if ( !empty($theme['direction']) )
        {
            $this->assign('themeDirection', $theme['direction']);
        }

		parent::display('Layout.tpl');

        if ( defined('DEV_PROFILER') && DEV_PROFILER  )
        {
			printVar(SK_Profiler::getInstance('app')->getResult());        
			SK_QueryLogger::getInstance('db')->printQueryLog();
			
			
			printVar(memory_get_peak_usage()/1024/1024);
			//SK_QueryLogger::getInstance('db-resource')->printQueryLog();
		}
	}

	/**
	 * Render a component.
	 *
	 * @param SK_Component $component
	 * @param string $cmp_id
	 * @return string html output or null if the component annuled
	 */
	public function renderComponent( SK_Component $component, $cmp_id = null )
	{

        
		global $ajax_response;

		if ( $component->annuled() ) {
			return null;
		}
SK_Profiler::getInstance('app')->mark('__'.$component->getNamespace().'__ cmp render start');
		if ( isset($ajax_response) )
		{
			if ( !isset($this->frontend_handler) ) {
				$this->frontend_handler = $ajax_response;
			}

			if ( !self::$instance->component_stack ) {
				self::$instance->component_stack[0] = new stdClass();
				self::$instance->component_stack[0]->auto_id = $ajax_response->COM_node()->parent()->auto_id;
			}

			if ( !isset($cmp_id) ) {
				$cmp_id = uniqid('cmp');
			}
		}

		// keeping properties workspace
		$_layout = array(
			$this->current_component_name,
			$this->caching,
			$this->cache_lifetime,
			$this->_plugins,
			$this->_reg_objects,
			$this->_tpl_vars
		);

		$this->current_component_name = $component->getNamespace();

		// adding component to stack
		array_unshift($this->component_stack, $component);

		// setting component id
		$component->auto_id = isset($cmp_id) ? $cmp_id : 'cmp'.self::$component_counter++;

		// catching component exceptions
		try {
			// preparing component
			if ( $component->prepare($this, $this->frontend_handler) === false ) {
				// annuling the component displyng if the $component->prepare() method returns false
				$component->annul();
			}

			// getting display params
			list($tpl_file, $cache_id, $compile_id, $cache_lifetime) = $component->getDisplayParams();

			if ( $tpl_file )
			{
				$tpl_file_src = $tpl_file;

				// turning on the caching to make able to fetch a component cache
				$this->caching = $this->configs->caching ? 2 : 0;

				if ( $this->is_cached($tpl_file_src, $cache_id, $compile_id) ) {
					$html_output = parent::fetch($tpl_file_src, $cache_id, $compile_id);
				}
				else {
					// in development mode we check the displaying templates for existence
					if ( DEV_MODE && !$this->template_exists($tpl_file_src) ) {
						$_error_msg = sprintf(
							'%s::%s() document template "%s" does not exist',
							__CLASS__, __FUNCTION__, $tpl_file_src
						);
						trigger_error($_error_msg, E_USER_WARNING);
						return;
					}

					// calling a render method to make fetch component data.
                    
					if ( $component->render($this, $this->frontend_handler) !== false )
					{
						if ( $cache_lifetime ) {
							$this->cache_lifetime = $cache_lifetime;
						}
						else {
							$this->caching = 0;
						}

						$this->assign('this', Object(array(
							'tpl_dir'	=> dirname($tpl_file_src).DIRECTORY_SEPARATOR
						)));

						// and fetch using $this->prepare() method refered $display_params.
						$html_output = parent::fetch($tpl_file_src, $cache_id, $compile_id);
					}
					else {
						$component->annul();
						$html_output = '';
					}
				}

				// frontend handling
				$this->frontend_handler->registerComponent($component);
			}
			else {
				$html_output = '';
			}
		}
		catch ( SK_ServiceUseException $e ) {
			$html_output = $e->getHtmlMessage();
		}
		/*catch ( Exception $e ) {
			$html_output = $this->handleComponentException($e);
		}*/

		// shifting displayed component
		array_shift($this->component_stack);

		list(
			$this->current_component_name,
			$this->caching,
			$this->cache_lifetime,
			$this->_plugins,
			$this->_reg_objects,
			$this->_tpl_vars
		) = $_layout;
SK_Profiler::getInstance('app')->mark('__'.$component->getNamespace().'__ cmp render end');
		return $html_output;
	}

	/**
	 * Component exception handler.
	 *
	 * @param Exception $exception
	 * @return string
	 */
	private function handleComponentException( Exception $exception )
	{
		$output = '<div style="font-family:\'Courier New\';font-size:12px">';

		$output .= 'Uncaught exception with message: <b>' . $exception->getMessage() . '</b>';
		$output .= ' code: <b>' . $exception->getCode() . '</b><br />';

		$output .= 'Trace: <br />';
		$output .= '<div style="margin: 2px 6px">' . nl2br($exception->getTraceAsString()) . '</div>';

		$output .= 'thrown in: <b>' . $exception->getFile() . '</b>';
		$output .= ' on line <b>' . $exception->getLine() . '</b><br />';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Add a CSS file inclusion.
	 *
	 * @param string $file_src
	 */
	public function includeCSSFile( $file_src )
	{
		if ( !isset($this->inc_css_files[$file_src]) ) {
			$this->css_inc_decl .=
<<<EOT
<link rel="stylesheet" type="text/css" href="$file_src" />\n
EOT;
			$this->inc_css_files[$file_src] = true;
		}
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
    public function _get_auto_filename($auto_base, $auto_source = null, $auto_id = null)
    {
        $_compile_dir_sep = $this->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';
        $_return = $auto_base . $this->theme() . $_compile_dir_sep . $this->current_component_name . $_compile_dir_sep;

        if(isset($auto_id)) {
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

        return $_return;
    }

    /**
     * Clear specified component template.
     *
     * @param SK_Component $component
     * @return boolaen
     */
    public function clear_compiled_tpl1( SK_Component $component )
    {
    	list($tpl_file, $cache_id, $compile_id) = $component->getDisplayParams();

    	$this->current_component_name = $component->getNamespace();

    	return parent::clear_compiled_tpl($tpl_file, $compile_id);
    }

    /**
     * Clear all compiled resources.
     */
    public static function clear_all_compiled()
    {
    	// temporary locking site
    	app_Site::lock();

    	$_params = array('level' => 0);
		require_once SMARTY_CORE_DIR . 'core.rmdir.php';

		$empty_dirs = array(
			DIR_COMPONENTS_C,
			DIR_FORMS_C,
			DIR_EXTERNAL_C,
			DIR_SMARTY_CACHE
		);

		self::clearXmlCache();

		foreach ( $empty_dirs as $dirname ) {
			$_params['dirname'] = $dirname;
			smarty_core_rmdir($_params, self::getInstance());
		}

		// unlocking site
    	app_Site::unlock();
    }


    public function tpl_block( array $params, $content )
	{
		if ( !isset($content) ) {
			array_unshift($this->block_stack, array());
			return;
		}

		$inner_tags = array_shift($this->block_stack);

		$output =
			'<div'.(@$params['id'] ? ' id="'.self::current_component()->getTagAutoId($params['id']).'"' : '').
				' class="block'.(@$params['class'] ? ' '.$params['class'] : '').'">';

		unset($params['id'], $params['class']);

		$_params = $params;

		if ( isset($params['title'])
			|| @$params['toolbar']
			|| @$params['expandable']
			|| isset($inner_tags['block_cap'])
		) {
			// rendering block cap
			if ( isset($inner_tags['block_cap']) ) {
				$_params = array_merge($params, $inner_tags['block_cap']->params);
				$output .=	$this->tpl_block_cap($_params).
							$this->tpl_block_cap($_params, $inner_tags['block_cap']->content);
			}
			else {
				$output .=	$this->tpl_block_cap($params) . $this->tpl_block_cap($params, '');
			}
		}

		$output .=
				'<div class="block_body"'.((@$params['expandable'] && !@$params['expanded']) ? ' style="display: none"' : '').'>'.
					'<div class="block_body_r"><div class="block_body_c clearfix">'.
					$content.
				'</div></div>';

		if ( !empty($this->themeData[self::XML_BLOCK_TOOLBAR_POSITION])
            && $this->themeData[self::XML_BLOCK_TOOLBAR_POSITION] == 'bottom'
            && !empty($_params['toolbar']))
		{
		    $output .=
                '<div class="block_toolbar"><div class="block_toolbar_r"><div class="block_toolbar_c clearfix"><div class="block_toolbar_bg">'.$_params['toolbar'].'</div></div></div></div>';
		}

		$output .= '</div>';

		$output .= '<div class="block_bottom"><div class="block_bottom_r"><div class="block_bottom_c"></div></div></div>';

		$output .=
			'</div>';

		return $output;
	}

	public function tpl_printArr( array $params )
	{
		$output = printArr( $params['var'], true );
		return 	$output;
	}

	public function tpl_block_cap( array $params, $content = null, $smarty = null )
	{
		if ( !isset($content) ) {
			return;
		}

		if ( isset($smarty) && !empty($this->block_stack) )
		{
			$this->block_stack[0]['block_cap'] =
				Object(array(
					'params'	=> $params,
					'content'	=> $content
				));
			return;
		}

		$output =
			'<div'.(@$params['id'] ? ' id="'.self::current_component()->getTagAutoId($params['id']).'"' : '').
				' class="block_cap'.(@$params['class'] ? ' '.$params['class'] : '').'">'.
				'<div class="block_cap_r"><div class="block_cap_c clearfix">';

		if ( isset($params['title']) ) {
			$output .=
				'<h3 class="block_cap_title">'.SK_Language::htmlspecialchars($params['title'], ENT_NOQUOTES).'</h3>';
		}

		$output .= $content;

		if ( @$params['expandable'] ) {
			$_class = (!@$params['expanded']) ? 'block_expand' : 'block_collapse';
			$output .=
				'<a href="#" class="'.$_class.'"></a>';
		}

		$output .= '</div></div></div>';

		if ( @$params['toolbar'] )
		{
    		if ( empty($this->themeData[self::XML_BLOCK_TOOLBAR_POSITION]) || $this->themeData[self::XML_BLOCK_TOOLBAR_POSITION] != 'bottom' )
            {
                $output .=
                    '<div class="block_toolbar"><div class="block_toolbar_r"><div class="block_toolbar_c clearfix"><div class="block_toolbar_bg">'.$params['toolbar'].'</div></div></div></div>';
            }
		}

		return $output;
	}
}
