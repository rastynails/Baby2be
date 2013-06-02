<?php defined('SYSPATH') OR die('No direct access allowed.');

class Custom_Doc_Controller extends Controller
{
	function __construct()
	{
		parent::__construct();
	}
		
	public function about( )
	{
		$view = new View('default');

		$view->header = SKM_Template::header();
		
		// menu
		$m = new SKM_Menu('main');
		$view->menu = $m->get_view();
	    
		SKM_Template::addTplVar('content', SKM_Language::text('%nav_doc_item.custom_code.about_us'));
		
		$view->content = new View('custom_doc', SKM_Template::getTplVars());
		
		$view->footer = SKM_Template::footer();

		$view->render(TRUE);
	}
	
	public function privacy( )
	{
		$view = new View('default');

		$view->header = SKM_Template::header();
		
		// menu
		$m = new SKM_Menu('main');
		$view->menu = $m->get_view();
	    
		SKM_Template::addTplVar('content', SKM_Language::text('%nav_doc_item.custom_code.privacy_policy'));
		
		$view->content = new View('custom_doc', SKM_Template::getTplVars());
		
		$view->footer = SKM_Template::footer();

		$view->render(TRUE);
	}
	
   public function terms( )
    {
        $view = new View('default');

        $view->header = SKM_Template::header();
        
        // menu
        $m = new SKM_Menu('main');
        $view->menu = $m->get_view();
        
        SKM_Template::addTplVar('content', SKM_Language::text('%nav_doc_item.custom_code.term_of_use'));
        
        $view->content = new View('custom_doc', SKM_Template::getTplVars());
        
        $view->footer = SKM_Template::footer();

        $view->render(TRUE);
    }
	
    public function suspended( )
    {
        if ( app_Site::active() )
        {
            url::redirect('index');
        }
        
        $view = new View('default');

        $view->header = SKM_Template::header();
        
        // menu
        $m = new SKM_Menu('main');
        $view->menu = $m->get_view();
               
        SKM_Template::addTplVar('msg', SKM_Language::text('%txt.site_suspended.msg'));
        $view->content = new View('not_found', SKM_Template::getTplVars());
        
        $view->footer = SKM_Template::footer();

        $view->render(TRUE);
    }
    
    public function review( )
    {
        if ( !SKM_User::is_authenticated() )
        {
            url::redirect('sign_in');
        }
        
        $profile_id = SKM_User::profile_id();
        
        if ( app_Profile::reviewed($profile_id) || $config = SK_Config::section('site')->Section('additional')->Section('profile')->not_reviewed_profile_access )
        {
            url::redirect('home');
        }
        
        $view = new View('default');

        $view->header = SKM_Template::header();
        
        // menu
        $m = new SKM_Menu('main', 'home', SKM_Language::text('%nav_doc_item.headers.not_reviewed'));
        $view->menu = $m->get_view();
        
        $site_name = SK_Config::section('site')->Section('official')->site_name;
        SKM_Template::addTplVar('content', SKM_Language::text('%components.profile_not_reviewed.msg', array('site_name' => $site_name)));
        
        $view->content = new View('custom_doc', SKM_Template::getTplVars());
        
        $view->footer = SKM_Template::footer();

        $view->render(TRUE);
    }
    
    public function profileSuspended( )
    {
        if ( !SKM_User::is_authenticated() )
        {
            url::redirect('sign_in');
        }
        
        $profile_id = SKM_User::profile_id();
        
        if ( !app_Profile::suspended($profile_id) )
        {
            url::redirect('home');
        }
        
        $view = new View('default');

        $view->header = SKM_Template::header();
        
        // menu
        $m = new SKM_Menu('main', 'home', SKM_Language::text('%nav_doc_item.headers.profile_suspended'));
        $view->menu = $m->get_view();
        
        SKM_Template::addTplVar('content', SKM_Language::text('%components.profile_suspended.msg'));
        
        $view->content = new View('custom_doc', SKM_Template::getTplVars());
        
        $view->footer = SKM_Template::footer();

        $view->render(TRUE);
    }
}
