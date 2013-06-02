<?php

// requiring Smarty class
require_once( DIR_SMARTY.'Smarty.class.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );


class AdminFrontend extends Smarty
{
	/**
	 * @var array
	 */
	var $_required_js_files = array();

	/**
	 * @var array
	 */
	var $_required_css_files = array();

	/**
	 *
	 * @var string
	 */
	var $page_title;

	/**
	 * @var array
	 */
	var $_registered_meta_tags = array();


	/**
	 * Constructor.
	 */
	function __construct()
	{
		global $file_key, $active_tab;

		// calling base class constructor
		parent::Smarty();

		$this->template_dir = DIR_ADMIN . 'templates' . DIRECTORY_SEPARATOR;

		$this->trusted_dir = DIR_ADMIN_INC;

		$this->compile_dir	= DIR_COMPONENTS_C;

		$this->plugins_dir[] = DIR_INTERNALS.'Smarty_adds';

		$this->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
		$this->IncludeJsFile( URL_ADMIN_JS.'language.js' );
		$this->registerJSCode(
			"var URL_ADMIN = ".json_encode(URL_ADMIN).", URL_ADMIN_IMG = ".json_encode(URL_ADMIN_IMG)."
			URL_ADMIN_RSP = " . json_encode(URL_ADMIN . "rsp/") . ";"
		);

		// force compile!
		$this->force_compile = true;

		$this->use_sub_dirs = true;

		$this->caching = false;

		$this->compile_check = true;

		global $_page;

		// Assigning necessary data by reference
		$this->assign_by_ref( '_page', $_page );

		$this->assign_by_ref( '_required_js_files', $this->_required_js_files );
		$this->assign_by_ref( '_required_css_files', $this->_required_css_files );

		$this->assign_by_ref( '_meta_tags', $this->_registered_meta_tags );

		$this->assign( '_header_site_name', SK_Config::section('site')->Section('official')->get('site_name'));

		// Register template functions
		$this->register_function( 'print_arr', array( &$this, 'tpl_printArr' ) );

		$this->register_function( 'highlight', array($this, 'FrontendHighlight') );

		// !
		$this->register_function( 'page_messages', array( &$this, 'FrontendPrintMessages' ) );

		$this->register_function( 'text', array( &$this, 'tpl_Text' ) );

		$this->register_function( 'edit_lang_values_btn', array( &$this, 'tpl_EditLangKey' ) );

		$this->register_function( 'paging', array( &$this, 'tpl_paging' ) );

		$this->register_function( 'add_lang_values_input', array( &$this, 'tpl_AddLangKeyInput' ) );

		$this->register_function( 'lang_values_input', array( &$this, 'tpl_LangKeyInput' ) );

		$this->register_function( 'print_assign_vars', array( &$this, 'tpl_PrintAssignVars' ) );

		$this->register_function( 'print_configs', array( 'adminConfig', 'FrontendPrintConfig' ) );
		$this->register_block( 'block_popup', array( &$this, 'tpl_BlockPopup' ) );
		$this->register_block( 'block_page', array( &$this, 'tpl_BlockPage' ) );



		//$this->IncludeJsFile( URL_SKADATE_HOME.'_service/admin_tips_listener.js' );
		//$this->IncludeJsFile( URL_SKADATE_HOME.'_service/admin_tips_init.js?file_key='.$file_key.'&active_tab='.$active_tab );

		//$this->trusted_dir = DIR_ADMIN_INC;
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
    function _get_auto_filename($auto_base, $auto_source = null, $auto_id = null)
    {
        $_compile_dir_sep =  $this->use_sub_dirs ? DIRECTORY_SEPARATOR : '^';
        $_return = $auto_base . 'admin' . DIRECTORY_SEPARATOR;

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
	 * Adds javascript file for further inclusion in html header section.
	 * Performs check that the file exists
	 *
	 * @access public
	 * @param string $file_url
	 * @return boolean
	 */
	function IncludeJsFile( $file_url )
	{
		if( !in_array( $file_url, $this->_required_js_files ) )
			$this->_required_js_files[] = $file_url;
	}

	function includeCSSFile( $file_url )
	{
		if( !in_array( $file_url, $this->_required_css_files ) )
			$this->_required_css_files[] = $file_url;
	}

	function registerJSCode( $js_code ) {
		@$this->_tpl_vars['header_js_code'] .= "$js_code\r\n";
	}


	/**
	 * Register javascript code wich must be
	 * executed on document load.
	 * If second optional parametr $hight_priority = true
	 * $js_code will have more hight priority to executes first
	 *
	 * @access public
	 *
	 * @param string $js_code
	 * @param boolean $hight_priority
	 */
	function registerOnloadJS( $js_code, $hight_priority = false )
	{
		if( $hight_priority )
			$this->_tpl_vars['_js_onload'] = $js_code . @$this->_tpl_vars['_js_onload'];
		else
			@$this->_tpl_vars['_js_onload'] .= $js_code;
	}

	function tpl_printArr( $params )
	{
		$output = printArr( $params['var'], true );
		return 	$output;
	}

	function tpl_BlockPopup( $params, $content )
	{
		if ( !isset($content) ) {
			return;
		}

		if ( isset($params['title']) ) {
			$cap = '<div class="popup_cap"><div class="popup_title">'.$params['title'].'</div><div class="popup_close" onclick="hide_node(\''.$params['node'].'\')"></div></div>';
		}
		else
			$cap = '<div class="popup_cap">&nbsp;</div>';

		if ( empty($params['bottom']) )
		{
		    $params['top'] = '-14';
		}

		$style = array();
		foreach ( array('top', 'bottom', 'left', 'right', 'width') as $a )
		{
		    $style[$a] = isset($params[$a]) ? $a . ': ' . $params[$a] . 'px' : null;
		}
		$style = array_filter($style);

		$cont = '<div class="popup_body">'.$content.'</div>';

		return '<div class="popup_wrap"><div id="'.$params['node'].'" class="popup_cont" style="'.implode('; ', $style).'">'.$cap.$cont.'</div></div>';
	}

	public function tpl_BlockPage($params, $content)
	{
		$output_start = '
<table class="content_profiles" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td class="corner_left_t"></td>
<td class="side">&nbsp;</td>
<td class="corner_right_t"></td>
</tr>
<tr>
<td class="side"></td>
<td class="side">';

		$output_end ='</td>
<td class="side"></td>
</tr>
<tr>
<td class="corner_left_b"></td>
<td class="side">&nbsp;</td>
<td class="corner_right_b"></td>
</tr>
</table>
<br clear="all" />';
		return 	$output_start . $content . $output_end;
	}

	public function tpl_PrintAssignVars($params)
	{
		$output ='';
		$area = $params['textarea'];
		if (isset($params['vars']))
		{
			foreach ($params['vars'] as $var)
			{
				$output .= '<a href="javascript://" onclick=$jq("#'.$area.'").val($jq("#'.$area.'").val()+$jq("#var_'.$var.'").text()) id="var_'.$var.'" >{$'.$var.'}</a><br />';
			}
		}

		return $output;
	}


	/**
	 * Register message in session messages array
	 * which must be shown in template header.<br>
	 * Parameter $type must be:<br>
	 * <ul>
	 *  <li>message (default)</li>
	 *  <li>notice</li>
	 *  <li>error</li>
	 * </ul>
	 *
	 * @access public
	 * @param string $text
	 * @param string $type
	 *
	 */
	function registerMessage( $text, $type = 'message' )
	{
		$_SESSION['messages'][] = array
		(
			'type' => $type,
			'text' => $text
		);
	}


	/**
	 * Print and unset session registered messages
	 *
	 * @return string
	 */
	function frontendPrintMessages()
	{
		if( $_SESSION['messages'] )
			foreach( $_SESSION['messages'] as $_msg )
				$_out.= '<div class="page_'.$_msg['type'].'">'.$_msg['text'].'<br clear="all" /></div>';

		// Clear session messages
		$_SESSION['messages'] = array();

		return $_out;
	}


	/**
	 * @return string
	 */
	function FrontendHighlight()
	{
		return 'onmouseover="hightlight(this,true)" onmouseout="hightlight(this,false)"';
	}


	function tpl_Text($params)
	{
		if( !trim($params['section']) || !trim($params['key']) ) return '';
		if(isset($params['lang_id'])) {
			$lang_id = (int)$params['lang_id'];
			$language = SK_Language::instance($lang_id);
			$section = SK_Language::section($params['section'], $language);
		} else {
			$section = SK_Language::section($params['section']);
		}

		return $section->key_exists($params['key']) ? $section->cdata($params['key']) : '';

	}

	/**
	 * An implementation for {lang_values_input ...} template function.
	 *
	 * @param string $section
	 * @return string
	 */
	function tpl_EditLangKey( $params )
	{
		$lang_section = trim($params['section']);
		if ( !strlen($lang_section) ) {
			$this->trigger_error('{lang_values_input ...} empty value for attribute "section"');
		}

		$lang_key = trim($params['key']);
		if ( !strlen($lang_key) ) {
			$this->trigger_error('{lang_values_input ...} empty value for attribute "key"');
		}
		$lang_id = SK_Language::current_lang_id();

		$js_action = "SK_AdminLangEdit.requestValuesEdition(this, '$lang_section', '$lang_key', {lang_id: $lang_id});";

		if ( SK_Language::section($lang_section)->key_exists($lang_key))
		{
			if (strlen($lang_value = SK_Language::section($lang_section)->cdata($lang_key))) {
				return
				'<a href="#" onclick="'.$js_action.' return false">' .
					nl2br(SK_Language::htmlspecialchars($lang_value, ENT_NOQUOTES)) .
				'</a>';
			}
		}

		return
			'<a href="#" onclick="' . $js_action . ' return false" style="color: red">%' .
				$lang_section . '.' . $lang_key .
			'</a>';
	}


	function tpl_AddLangKeyInput( array $params )
	{
		if(!$params['name'] = trim( $params['name'] )) trigger_error('Parametr `name` is empty in '.__FILE__.', line '.__LINE__,E_USER_WARNING);

		$params['width'] = trim($params['width']) ? trim($params['width']) : '97%';
		$params['height'] = trim($params['height']) ? trim($params['height']) : '150px';
		$params['type'] = strlen(trim($params['type'])) ? trim($params['type']) : 'text';

		$params['languages'] = SK_LanguageEdit::getLanguages();
		$params['single_language'] = !(count($params['languages'])>1);
		$this->assign('params',$params);
		$out = $this->fetch('inc/_lang_add_key_value.html');
		$this->clear_assign('params');
		return $out;


	}


	/**
	 * Render a editable language key values node.
	 *
	 * @param array $params
	 * @return string html
	 */
	function tpl_LangKeyInput( array $params )
	{
		if ( SK_Language::section($params['section'])->key_exists($params['key']) && trim(SK_Language::section($params['section'])->cdata($params['key'])))
			return $this->tpl_EditLangKey($params);
		else
			return $this->tpl_AddLangKeyInput($params);
	}

	public function tpl_paging(array $params)
	{
		$total = (int)$params['total'];
		$in_page = (int)$params['on_page'];
		$pages_count = (int)$params['pages'];
		$var = isset($params['var']) ? (string)$params['var'] : 'page';

		$request_uri = $_SERVER['REQUEST_URI'];

		if (isset($params["exclude"])) {
			$excludes = explode(',', $params["exclude"]);

			if (count($excludes)) {
				$_exl = array();
				foreach ($excludes as $item) {
					$_exl[$item] = null;
				}
				$excludes = array_combine($excludes, $_exl);

				$request_uri = sk_make_url(null, $excludes);
			}
		}

		if( !$total || !$in_page || !$pages_count) return '';

		if(($total_pages = ceil($total / $in_page))<=1)
			return '';

		$current = $_GET[$var];
		$current = intval($current) ? $current : 1;

		$offset = ceil($pages_count / 2)-1;
		$offset_inc = ($total_pages - $offset)-$current;
		$offset+= ($offset_inc <= 0) ? abs($offset_inc)+(($pages_count%2)?0:1) : 0;

		$page = ($current - $offset)>1 ? ($current - $offset) : 1 ;

		$paging = '';

		for ($counter=1; $counter <= $pages_count && $page <= $total_pages; $counter++)
		{
			$active = ($page == $current) ? 'class="active"' : '';
			$url = sk_make_url($request_uri,array($var=>$page));
			$paging.= "<a href='{$url}' {$active}>{$page}</a>";
			$page++;
		}



		switch ($current)
		{
			case 1:
				$paging.="<a href='".sk_make_url($request_uri,array( $var=>$current+1))."'>".SK_Language::section('navigation.paging')->text('next_page')."</a>";
				$paging.="<a href='".sk_make_url($request_uri,array( $var=>$total_pages))."'>".SK_Language::section('navigation.paging')->text('last_page')."</a>";
				break;

			case $total_pages:
				$paging= "<a href='".sk_make_url($request_uri,array( $var=>$current-1))."'>".SK_Language::section('navigation.paging')->text('prev_page')."</a>".$paging;
				$paging= "<a href='".sk_make_url($request_uri,array( $var=>1))."'>".SK_Language::section('navigation.paging')->text('first_page')."</a>".$paging;
				break;

			default:
				$paging= "<a href='".sk_make_url($request_uri,array( $var=>$current-1))."'>".SK_Language::section('navigation.paging')->text('prev_page')."</a>".$paging;
				$paging= "<a href='".sk_make_url($request_uri,array( $var=>1))."'>".SK_Language::section('navigation.paging')->text('first_page')."</a>".$paging;

				$paging.="<a href='".sk_make_url($request_uri,array( $var=>$current+1))."'>".SK_Language::section('navigation.paging')->text('next_page')."</a>";
				$paging.="<a href='".sk_make_url($request_uri,array( $var=>$total_pages))."'>".SK_Language::section('navigation.paging')->text('last_page')."</a>";
				break;
		}

		$out = '<div class="paging">';
		$out.= '<span>'.SK_Language::section('navigation.paging')->text('pages').': </span>';
		$out.=$paging.'</div>';

		return $out;
	}

        public function tpl_CustomPageEditorLinks( $params )
        {
            if ( SK_Language::section($params['section'])->key_exists($params['key']) && trim(SK_Language::section($params['section'])->cdata($params['key'])) )
            {
                $lang_section = trim( $params['section'] );

                if ( !strlen($lang_section) )
                {
                    $this->trigger_error( '{lang_values_input ...} empty value for attribute "section"' );
                }

                $lang_key = trim( $params['key'] );

                if ( !strlen($lang_key) )
                {
                    $this->trigger_error( '{lang_values_input ...} empty value for attribute "key"' );
                }

                $lang_id = (int)SK_Language::current_lang_id();

                $js_action = "SK_CustomPageEditor.getEditInstance().getLinks(this, '$lang_section', '$lang_key', $lang_id);";

                if ( SK_Language::section($lang_section)->key_exists($lang_key) )
                {
                    if ( strlen($lang_value = SK_Language::section($lang_section)->cdata($lang_key)) )
                    {
                        return
                            '<a href="javascript://" onclick="'.$js_action.' return false">' .
                                nl2br( SK_Language::htmlspecialchars($lang_value, ENT_NOQUOTES) ) .
                            '</a>';
                    }
                }

                return
                    '<a href="#" onclick="' . $js_action . ' return false" style="color: red">%' .
                        $lang_section . '.' . $lang_key .
                    '</a>';
            }
            else
            {
                if ( !$params['name'] = trim($params['name']) )
                {
                    trigger_error( 'Parametr `name` is empty in '.__FILE__.', line '.__LINE__,E_USER_WARNING );
                }

		$params['languages'] = SK_LanguageEdit::getLanguages();
		$params['single'] = !( count($params['languages']) > 1 );
		$this->assign( 'params', $params );
		$out = $this->fetch( 'inc/custom_page_editor.html' );
		$this->clear_assign( 'params' );
		return $out;
            }
        }

        function RegisterMetaTags( $params )
	{
		$_name		= ( $params['name'] ) ? 'NAME="'.$params['name'].'" ' : '';
		$_content	= ( $params['content'] ) ? 'CONTENT="'.$params['content'].'"' : '';
		$_http_equiv = ( $params['http_equiv'] ) ? 'HTTP-EQUIV="'.$params['http_equiv'].'"' : '';

		$this->_registered_meta_tags[] = "<META $_name $_http_equiv $_content >";

	}

	function display( $resource_name, $var1 = null, $var2 = null )
	{
		if( function_exists( 'compileDemoHeader' ) )
			$this->assign( 'demo_header', compileDemoHeader( $this, true ) );

		//$this->registerOnloadJS( 'initAdminTips();' );


		parent::display( $resource_name );
	}
}

/**
 * Instance getter.
 *
 * @return AdminFrontend
 */
function sk_admin_frontend()
{
	global $frontend;

	if ( !$frontend ) {
		$frontend = new AdminFrontend;
	}

	return $frontend;
}
