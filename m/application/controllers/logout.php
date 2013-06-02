<?php defined('SYSPATH') OR die('No direct access allowed.');

class Logout_Controller extends Controller {
		
	function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		if ( SKM_User::is_authenticated() ) {
			app_Profile::deleteCookiesLogin(SKM_User::username());
		}
		
		SKM_User::logoff();		
		url::redirect('');
	}
}