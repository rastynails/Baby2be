<?php

// requiring Smarty class
require_once( DIR_SMARTY.'Smarty.class.php' );

/**
 * Class AdminFrontend
 *
 * @package Skadate
 */
class AffiliateFrontend extends Smarty
{
	
	/**
	 * @var array
	 */
	var $_required_js_files = array();
	
	/**
	 * @var string
	 */
	var $page_title;
	
	/**
	 * @var array
	 */
	var $_registered_meta_tags = array();
	
	/**
	 * Class constructor
	 */
	function AffiliateFrontend()
	{
		parent::Smarty();
				
		$this->template_dir = DIR_AFFILIATE.'templates/';
		
		$this->compile_dir	= DIR_COMPONENTS_C;
				
		$this->force_compile = false;
		
		$this->use_sub_dirs = true;
		
		$this->caching = false;
		
		$this->compile_check = true;
		
		// Assigning necessary data by reference
		$this->assign_by_ref( '_page', $_page );
		
		$this->assign_by_ref( '_required_js_files', $this->_required_js_files );
		
		$this->assign_by_ref( '_meta_tags', $this->_registered_meta_tags );
						
		// Register template functions
		$this->register_function( 'print_arr', 'frontend_print_arr' );
		
		$this->register_function( 'page_messages', array( &$this, 'FrontendPrintMessages' ) );
		
		//$this->register_function( 'text', array( $language, 'FrontendText' ) );
		
		$this->trusted_dir = DIR_AFFILIATE_INC;

		$this->assign( '_header_site_name', SK_Config::section('site')->Section('official')->get('site_name') );
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
		$_filename = str_replace( URL_HOME, DIR_ROOT, $file_url );
		
		/*if( !file_exists( $_filename ) ) 
			throw new Exception('Javascript file "<code>'.$_filename.'</code>" not found');*/
		
		if( !in_array( $file_url, $this->_required_js_files ) )
			$this->_required_js_files[] = $file_url;
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
			$this->_tpl_vars['_js_onload'] = $js_code . $this->_tpl_vars['_js_onload'];
		else 
			$this->_tpl_vars['_js_onload'] .= $js_code;
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
	
	
	function display( $resource_name )
	{
		parent::display( $resource_name );
	}
}