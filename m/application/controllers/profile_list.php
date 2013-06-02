<?php defined('SYSPATH') OR die('No direct access allowed.');

class Profile_List_Controller extends Controller
{
	private $members;
	
	private $content;
	
	function __construct()
	{
		parent::__construct();
		
		if (!SKM_User::is_authenticated()) {
			Session::instance()->set("requested_url", "/".url::current());
			url::redirect('sign_in');
		} else
			$this->members = new Members_Model;
	}
	
	private static function redirectToEnabled( $docKeys, $from )
	{
        foreach ( $docKeys as $feature => $key )
        {
            if ( $key == $from )
            {
                continue;
            }
            
            $doc = SK_Navigation::getDocument($key);
            
            if ( $doc->status && $doc->access_member )
            {
                url::redirect('members/' . $feature);
            }
        }
        
        url::redirect('not_found');
	}
	
	public function show( $listtype, $page = 1 )
	{
		$view = new View('default');

		$view->header = SKM_Template::header();
		
		$docKeys = array(
		  'new' => 'profile_new_members_list', 'featured' => 'featured_list', 
		  'online' => 'online_list', 'matches' => 'match_list', 'bookmarks' => 'bookmark_list'
		);
		
		$document = SK_Navigation::getDocument($docKeys[$listtype]);
        if ( !$document->status || !$document->access_member )
        {
            self::redirectToEnabled($docKeys, $docKeys[$listtype]);
        }

		switch ( $listtype )
		{
			case 'featured':
				$head = SKM_Language::text('%nav_doc_item.headers.featured_list', array('site_name' => SK_Config::section('site.official')->site_name));
				break;
				
			case 'online':
				$head = SKM_Language::text('%nav_doc_item.headers.online_list');
				break;
				
			case 'new':
				$head = SKM_Language::text('%nav_doc_item.headers.profile_new_members_list');
				break;
					
			case 'matches':
				$head = SKM_Language::text('%nav_doc_item.headers.match_list');
				break;
					
			case 'bookmarks':
				$head = SKM_Language::text('%nav_menu_item.bookmarks');
				break;	
		}
		
		// menu
		$m = new SKM_Menu('main', 'members', $head);
		$view->menu = $m->get_view();
		
		// members list
		$on_page = SK_Config::Section('site.additional.profile_list')->result_per_page;
		$profiles = $this->members->get_members($listtype, $page, $on_page, SKM_User::profile_id());
		
		if (isset($profiles['total']))
		{
			$pagination = new Pagination(array(
		        'base_url'    => 'members/' . $listtype,
		        'total_items' => $profiles['total'],
				'items_per_page' => $on_page,
		        'style' => 'ska'
		    ));
		    
		    SKM_Template::addTplVar('list', $profiles['list']);
		    SKM_Template::addTplVar('paging', $pagination);
		    SKM_Template::addTplVar('profile_url', url::base() . 'profile/');
		    SKM_Template::addTplVar('im_url', url::base() . 'im/');
		}
		else 
			SKM_Template::addTplVar('no_profiles', SKM_Language::text('%components.profile_list.no_items'));
			
		$members = new View('members/featured', SKM_Template::getTplVars());
				
		$view->content = $members;
		$view->footer = SKM_Template::footer();
		$view->render(TRUE);
	}
	
	public function search_list( $list_id, $page )
	{
		$view = new View('default');
		$view->header = SKM_Template::header();
		
		// menu
		$m = new SKM_Menu('main', 'members', SKM_Language::text('%nav_doc_item.headers.profile_list'));
		$view->menu = $m->get_view();

		// members list
		$on_page = SK_Config::Section('site.additional.profile_list')->result_per_page;
		$profiles = $this->members->get_saved_list($list_id, $page);
		
		if (isset($profiles['total']))
		{
			$pagination = new Pagination(array(
		        'base_url'    => '/members/search_list/' . $list_id,
				'uri_segment'	=> 4,
		        'total_items' => $profiles['total'],
				'items_per_page' => $on_page,
		        'style' => 'ska'
		    ));
		    
		    SKM_Template::addTplVar('list', $profiles['list']);
		    SKM_Template::addTplVar('paging', $pagination);
		    SKM_Template::addTplVar('profile_url', url::base() . 'profile/');
		    SKM_Template::addTplVar('im_url', url::base() . 'im/');	
		}
		else 
			SKM_Template::addTplVar('no_profiles', SKM_Language::text('%components.profile_list.no_items'));
		
		$members = new View('members/featured', SKM_Template::getTplVars());
				
		$view->content = $members;
		$view->footer = SKM_Template::footer();	
		$view->render(TRUE);
	}
}
