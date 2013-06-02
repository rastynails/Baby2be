<?php

class SKM_Template {

	private static $tpl_vars;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public static function header() 
	{
		$vars = array (
			'page_title' => SK_Config::section('site.official')->get('site_name'),
			'css_include' => html::stylesheet('application/media/css/layout.css', 'screen'),
			'home_url' => html::anchor('', SK_Config::section('site.official')->get('site_name'))
		);
		
		if ( SKM_User::is_authenticated() )
		{
			// Username
			$vars['username'] = SKM_User::username();
			$vars['logout_url'] = url::base() . 'logout';
			
			// Mailbox new messages
			$msgs = app_MailBox::newMessages(SKM_User::profile_id());
			if ($msgs)
				$vars['notifications'][] = array('title' => SKM_Language::text('%mobile.new_messages', array('count' => $msgs)), 'url' => url::base().'mailbox/inbox');
		}
		
		$header = new View('header', $vars);
		
		return $header;
	}
	
	public static function footer() 
	{
		$bottom_menu = new SKM_Menu('bottom');
		$footer = new View('footer', array('menu' => $bottom_menu->get()));
		
		return $footer;
	}
	
	public static function addMessage($msg, $type = 'message')
	{
		if ( strlen($msg)) {
			$stack = self::getMessages();
			
			//if (count($stack)) {
				$stack[] = array('msg' => $msg, 'type' => $type);
				Session::instance()->set('messages', $stack);
			//}
		}
	}
	
	public static function getMessages()
	{
		return Session::instance()->get_once('messages');
	}
	
	public static function unsetMessages()
	{
		Session::instance()->delete('messages');
	}
	
	public static function addTplVar( $index, $obj )
	{
		if (strlen($index) && isset($obj))
		{
			self::$tpl_vars[$index] = $obj;
		}
	}
	
	public static function getTplVars()
	{
		return self::$tpl_vars;
	}
}
