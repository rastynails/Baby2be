<?php defined('SYSPATH') OR die('No direct access allowed.');

class Not_Found_Controller extends Controller
{
	private $content;
	
	function __construct()
	{
		parent::__construct();
	}
		
	public function index( )
	{
		$view = new View('default');

		$view->header = SKM_Template::header();
		
		// menu
		$m = new SKM_Menu('main');
		$view->menu = $m->get_view();
	    
		SKM_Template::addTplVar('msg', SKM_Language::text('%nav_doc_item.custom_code.not_found'));
		$view->content = new View('not_found', SKM_Template::getTplVars());
		$view->footer = SKM_Template::footer();

		$view->render(TRUE);
	}
}
