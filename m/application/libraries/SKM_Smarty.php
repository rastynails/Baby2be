<?php defined('SYSPATH') OR die('No direct access allowed.');

class SKM_Smarty_Core extends Smarty {

	function __construct()
	{
		// Check if we should use smarty or not
		if (Kohana::config('smarty.integration') == FALSE)
			return;

		// Okay, integration is enabled, so call the parent constructor
		parent::Smarty();

		$this->cache_dir      = Kohana::config('smarty.cache_path');
		$this->compile_dir    = Kohana::config('smarty.compile_path');
		$this->config_dir     = Kohana::config('smarty.configs_path');
		$this->plugins_dir[]  = Kohana::config('smarty.plugins_path');
		$this->debug_tpl      = Kohana::config('smarty.debug_tpl');
		$this->debugging_ctrl = Kohana::config('smarty.debugging_ctrl');
		$this->debugging      = Kohana::config('smarty.debugging');
		$this->caching        = Kohana::config('smarty.caching');
		$this->force_compile  = Kohana::config('smarty.force_compile');
		$this->security       = Kohana::config('smarty.security');

		//TODO:: UNCOMMENT THIS CHECKINGS IN PRODUCTION MODE!!!
		
		// check if cache directory is exists
		//$this->checkDirectory($this->cache_dir);

		// check if smarty_compile directory is exists
		//$this->checkDirectory($this->compile_dir);

		// check if smarty_cache directory is exists
		//$this->checkDirectory($this->cache_dir);

		if ($this->security)
		{
			$configSecureDirectories = Kohana::config('smarty.secure_dirs');
			$safeTemplates           = array(Kohana::config('smarty.global_templates_path'));

			$this->secure_dir                          = array_merge($configSecureDirectories, $safeTemplates);
			$this->security_settings['IF_FUNCS']       = Kohana::config('smarty.if_funcs');
			$this->security_settings['MODIFIER_FUNCS'] = Kohana::config('smarty.modifier_funcs');
		}

		// Autoload filters
		$this->autoload_filters = array('pre'    => Kohana::config('smarty.pre_filters'),
										'post'   => Kohana::config('smarty.post_filters'),
										'output' => Kohana::config('smarty.output_filters'));

		// Add all helpers to plugins_dir
		$helpers = glob(APPPATH . 'helpers/*', GLOB_ONLYDIR | GLOB_MARK);

		if ($helpers)
		{
			foreach ($helpers as $helper)
			{
				$this->plugins_dir[] = $helper;
			}
		}
		
		parent::register_function('profile_thumb', array( &$this, 'tpl_profileThumb' ));
		parent::register_block('block_cap', array( &$this, 'tpl_blockCap' ));
		parent::register_block('block', array( &$this, 'tpl_block' ));
		parent::register_function('text', array( &$this, 'tpl_text' ));
		
		parent::assign('messages', SKM_Template::getMessages());
	}

	public function checkDirectory($directory)
	{
		if ((! file_exists($directory) AND ! @mkdir($directory, 0755)) OR ! is_writable($directory) OR !is_executable($directory))
		{
			$error = 'Compile/Cache directory "%s" is not writeable/executable';
			$error = sprintf($error, $directory);

			throw new Kohana_User_Exception('Compile/Cache directory is not writeable/executable', $error);
		}

		return TRUE;
	}
	
	public function tpl_text($params)
	{
		if( !trim($params['section']) || !trim($params['key']) ) return '';
		if(isset($params['lang_id'])) {
			$lang_id = (int)$params['lang_id'];
			$language = SKM_Language::instance($lang_id);
			$section = SKM_Language::section($params['section'], $language);
		} else {
			$section = SKM_Language::section($params['section']);
		}
			
		return $section->key_exists($params['key']) ? $section->cdata($params['key']) : $params['section'].'_'.$params['key'];
		
	}
	
	public function tpl_blockCap($params, $content)
	{
		$output = '<div class="block_cap"><div class="section_title"><h3>'.$content.'</h3></div></div>';
		return 	$output;	
	}
	
	public function tpl_block($params, $content)
	{
		$output = '<div class="block">'.$content.'</div>';
		return 	$output;	
	}
	
	public function tpl_profileThumb( array $params )
	{
		if ( !key_exists('profile_id', $params) ) {
			trigger_error('{profile_thumb...} missing attribute "profile_id"', E_USER_WARNING);
			return;
		}
	
		if ( !($profile_id = (int)$params['profile_id'])) {
			trigger_error('{profile_thumb...} incorrect value for attribute "profile_id"', E_USER_WARNING);
			return;
		}
		
		$info = app_Profile::getFieldValues($profile_id, array('sex', 'username'));
	
		$deleted = !(bool)count($info);
	
		$params['size'] = isset($params['size']) ? $params['size'] : 100;
	
		$size = isset($params['size']) ? 'width="' . $params['size'] . '"' : '';
	
		$username = $deleted ? SKM_Language::text("%label.deleted_member") : $info["username"];
		$src = $deleted ? url::base() . 'application/media/img/deleted.jpg' : SKM_Profile::get_thumb_path($profile_id);
		
		$print_username = isset($params["username"]) && $params["username"];
	
		if (key_exists('href', $params)) {
			$href = (isset($params["href"]) && $params["href"]) ? $params["href"] : $href;
		
			$label = $print_username ? '<br /><span class="username">'.$username.'</span>' : '';
			$out = <<<EOT
<a href="$href">
	<img title="$username" class="profile_thumb" $size src="$src" />$label
</a>
EOT;
		} else {
			$href = $deleted ? "#" : url::base() . 'profile/' . $info['username'];
			$out= '<a href="'.$href.'"><img ' . $size . ' title="' . $username . '" src="' . $src . '" /></a>' .
			($print_username ? "<span>{$username}</span>" : '');
		
		}
	
		return $out;
	}
}
