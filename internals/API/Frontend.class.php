<?php

class SK_Frontend
{
	private $inc_js_files = array();

	protected $js_inc_decl = '';

	protected $header_js = '';

	protected $onload_js = '';

	protected $forms_counter = 0;

	/**
	 * Says whether the component js handler headers is defined.
	 *
	 * @var boolean
	 */
	protected $component_headers_defined = false;

	/**
	 * Concatinate a javascript code $line with $var by reference.
	 *
	 * @param string $line
	 * @param string $var reference
	 */
	public static function concat_js( $line, &$var )
	{
		$line = trim($line);
		$strlen = strlen($line);

		if ( $strlen ) {
			if ( $line{$strlen-1} != ';' ) {
				$line .= ';';
			}

			$var .= "$line\n";
		}
	}

	/**
	 * Constructor.
	 */
	public function __construct() {

                $this->include_js_file(URL_STATIC.'jquery.js');
                //$this->include_js_file('http://code.jquery.com/jquery-migrate-1.0.0.js'); // Jquery migration checker
                $this->include_js_file(URL_STATIC . 'jquery.browser.js');

		$this->include_js_file(URL_STATIC.'interface.js');

		if ( app_Cometchat::isActive() )
		{
                    $this->include_js_file(SITE_URL.'cometchat/cometchatjs.php');
		}

		$this->header_js(
			"var SITE_URL = '".SITE_URL."';\n".
			"var URL_MEMBER = '".URL_MEMBER."';"
		);

		$this->registerLanguageValue('%interface.ok');
		$this->registerLanguageValue('%interface.cancel');
		$this->registerLanguageValue('%interface.confirmation_title');
		$this->registerLanguageValue('%interface.alert_title');

        if ( !defined('IS_MOBILE_VERSION') && !SK_HttpRequest::isXMLHttpRequest() && SK_Config::section("site")->Section("splash_screen")->enable && empty($_COOKIE['splash_screen_viewed']) )
        {
            if (  sk_request_uri() != '/member/splash_screen.php' )
            {
                if ( empty($_COOKIE['splash_screen_back_uri']) )
                {
                    SK_HttpUser::set_cookie('splash_screen_back_uri', sk_request_uri());
                }

                $this->header_js( " window.location.href=" . json_encode(SITE_URL . 'member/splash_screen.php') );
            }
        }
	}

	/**
	 * Include a javascript file.
	 *
	 * @param string $file_src
	 */
	public function include_js_file( $file_src )
	{
		if ( !isset($this->inc_js_files[$file_src]) ) {
			$this->js_inc_decl .=
<<<EOT
<script type="text/javascript" src="$file_src"></script>\n
EOT;
			$this->inc_js_files[$file_src] = true;
		}
	}

	/**
	 * Add a javascript code to header section.
	 *
	 * @param string $js_code
	 */
	public function header_js( $js_code ) {
		self::concat_js($js_code, $this->header_js);
	}

	/**
	 * Register a language value entry to use in javascript.
	 *
	 * @param string $lang_addr
	 */
	public function registerLanguageValue( $lang_addr, $frontend_lang_addr = null )
	{
		$value = SK_Language::text($lang_addr);

		if ( !isset($frontend_lang_addr) ) {
			$frontend_lang_addr = $lang_addr;
		}

		$this->header_js("SK_Language.data['$frontend_lang_addr'] = ".json_encode($value));
	}


	public function assignLanguageValue( $value, $frontend_lang_addr ) {
		$this->header_js("SK_Language.data['$frontend_lang_addr'] = ".json_encode($value));
	}

	/**
	 * Binds a javascript code to be executed whenever the DOM is ready.
	 *
	 * @param string $js_code
	 */
	public function onload_js( $js_code ) {
		self::concat_js($js_code, $this->onload_js);
	}

	/**
	 * Register a form js handler entry.
	 *
	 * @param SK_Form $form
	 */
	public function registerForm( SK_Form $form )
	{
		if ( !$this->forms_counter ) {
			// defining form handler headers
			$this->include_js_file(URL_STATIC.'jquery.form.js');
			$this->include_js_file(URL_STATIC.'form_handler.js');
			$this->header_js('var URL_FORM_PROCESSOR = '.json_encode(SITE_URL.'form_processor.php'));
			$this->header_js('window.sk_forms = {}');
		}

		if ( $this->forms_counter === -1 ) { // ajax reload
			$auto_index = rand(1000, 9999);
		}
		else {
			$this->include_js_file($form->frontend_data['js_file']);
			$auto_index = $this->forms_counter++;
		}

		$class = $form->frontend_data['js_class'];
		$auto_var = "window.sk_forms[$auto_index]";
		$auto_id = "form_$auto_index";

		$form->frontend_data['js_handler'] = $auto_var;
		$form->frontend_data['auto_id'] = $auto_id;

		$component = SK_Layout::current_component();

		$component->forms[] = $form;

		$form->frontend_data['init_js'] =
			"$auto_var = new $class('$auto_id');\n".
			$form->frontend_handler->compile_js($auto_var).
			"window.sk_components['$component->auto_id'].forms.push($auto_var);\n".
			"$auto_var.ownerComponent = window.sk_components['$component->auto_id'];\n";
	}

	/**
	 * Register a component js handler entry.
	 *
	 * @param SK_Component $component
	 */
	public function registerComponent( SK_Component $component )
	{
		if ( !$this->component_headers_defined ) {
			// defining component handler headers
			$this->include_js_file(URL_STATIC.'json2.js');
			$this->include_js_file(URL_STATIC.'component_handler.js');
			$this->header_js('var URL_RESPONDER = '.json_encode(SITE_URL.'xml_http_responder.php').';');
			$this->header_js('window.sk_components = {};');
			$this->component_headers_defined = true;
		}

		$cmp_init_js = '';

		$auto_var = "window.sk_components['$component->auto_id']";

		if ( isset($component->frontend_handler) )
		{
			$handler = $component->frontend_handler;

			$this->include_js_file(URL_COMPONENTS.$handler->constructor.'.cmp.js');

			$cmp_init_js .= "$auto_var = new component_$handler->constructor('$component->auto_id');\n";

			// instantiating forms
			foreach ( $component->forms as $form ) {
				self::concat_js($form->frontend_data['init_js'], $cmp_init_js);
			}

			$cmp_init_js .= $handler->compile_js($auto_var);
		}
		else {
			$_class = substr(get_class($component), strlen('component_'));

			$cmp_init_js .= "$auto_var = new SK_ComponentHandler({});\n";

			// instantiating forms
			foreach ( $component->forms as $form ) {
				self::concat_js($form->frontend_data['init_js'], $cmp_init_js);
			}

			$cmp_init_js .= "$auto_var.DOMConstruct('$_class', '$component->auto_id');\n";
		}

		if ( $component->auto_id !== 'httpdoc' )
		{
			$parent = SK_Layout::current_component_parent();
			$parent_var = "window.sk_components['$parent->auto_id']";
			$cmp_init_js .=
				"$parent_var.children.push($auto_var);\n".
				"$auto_var.parent = $parent_var;\n";
		}

		$this->onload_js = "$cmp_init_js\n$this->onload_js";
	}

	/**
	 * Render a script tags with compiled js code.
	 *
	 * @return string
	 */
	public function renderScripts()
	{
		$onload_js = str_replace("\n", "\n\t", $this->onload_js);

		return
<<<EOT
$this->js_inc_decl
<script type="text/javascript">
$this->header_js
window.console;

jQuery(function() {
	$onload_js
});
</script>
EOT;
	}

}
