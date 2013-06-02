<?php

class CacheAdmin
{

	public static function turn_on_output_caching()
	{
		if ( SK_Config::section('layout')->set('caching', true) ) {
            SK_Config::removeCache();
			sk_admin_frontend()->registerMessage('Output caching is turned ON');
		}
	}

	public static function turn_off_output_caching()
	{
		if ( SK_Config::section('layout')->set('caching', false) ) {
            SK_Config::removeCache();
			sk_admin_frontend()->registerMessage('Output caching is turned OFF');
		}
	}

	public static function turn_on_language_caching()
	{
		if ( SK_Config::section('languages')->set('caching', true) )
		{
            SK_Config::removeCache();
		    SK_LanguageEdit::generateCache();
			sk_admin_frontend()->registerMessage('Language caching is turned ON');
		}
	}

	public static function turn_off_language_caching()
	{
		if ( SK_Config::section('languages')->set('caching', false) )
		{
            SK_Config::removeCache();
			sk_admin_frontend()->registerMessage('Language caching is turned OFF');
		}
	}

	public static function clear_all_compiled()
	{
		// clear smarty compiled resources
		SK_Layout::clear_all_compiled();

		// regenerating language cache
		SK_LanguageEdit::generateCache();

        // clear configs cache
        SK_Config::removeCache();

		sk_admin_frontend()->registerMessage('All compiled resources and cache are cleared');
	}

}

if ( $action = @$_POST['action'] )
{
	unset($_POST['action']);

	$request_callback = array('CacheAdmin', $action);

	if ( method_exists($request_callback[0], $request_callback[1]) )
	{
		call_user_func($request_callback, $_POST);
		header('Location: '.$_SERVER['REQUEST_URI']);
	}
	else {
		trigger_error('call to undefined function '.implode('::', $request_callback), E_USER_ERROR);
	}

	exit();
}
