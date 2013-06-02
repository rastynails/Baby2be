<?php defined('SYSPATH') OR die('No direct access allowed.');

class Memberhome_Controller extends Controller
{
	private $content;
	
	function __construct()
	{
		parent::__construct();

		if ( !SKM_User::is_authenticated() ) {
			Session::instance()->set("requested_url", "/".url::current()); 
			url::redirect('sign_in');
		}
	}
	
	private static function documentEnabled( $doc_key )
	{
	    $document = SK_Navigation::getDocument($doc_key);
	    
        return $document->status && $document->access_member;
	}
	
	public function index( )
	{
		$view = new View('default');

		$view->header = SKM_Template::header(); 
		
		// menu
		$m = new SKM_Menu('main', 'home', SKM_Language::text('nav_doc_item.headers.home'));
		$view->menu = $m->get_view();

		$profile_id = SKM_User::profile_id();
		
	    $lists = new Members_Model;
	    SKM_Template::addTplVar('online', $lists->get_members('online', 1, 3));
	    SKM_Template::addTplVar('online_enabled', self::documentEnabled('online_list'));
	    SKM_Template::addTplVar('latest', $lists->get_members('new', 1, 3));
	    SKM_Template::addTplVar('latest_enabled', self::documentEnabled('profile_new_members_list'));
	    SKM_Template::addTplVar('matches', $lists->get_members('matches', 1, 3, $profile_id));
	    SKM_Template::addTplVar('matches_enabled', self::documentEnabled('match_list'));
	    SKM_Template::addTplVar('bookmarks', $lists->get_members('bookmarks', 1, 3, $profile_id));
	    SKM_Template::addTplVar('bookmarks_enabled', self::documentEnabled('bookmark_list'));
	    
	    SKM_Template::addTplVar('profile_url', url::base() . 'profile/');
	    SKM_Template::addTplVar('members_url', url::base() . 'members/');
	    SKM_Template::addTplVar('welcome', SKM_Language::text('%memberhome.label_welcome', array('username' => app_Profile::username($profile_id))));

		$mh = new View('memberhome', SKM_Template::getTplVars());

		$view->content = $mh;
		$view->footer = SKM_Template::footer();

		$view->render(TRUE);
	}
}
