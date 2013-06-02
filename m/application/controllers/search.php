<?php


class Search_Controller extends Controller {
	
	private $session;
	
	function __construct()
	{
		parent::__construct();

		$this->session = Session::instance(); 
		
		if (!SKM_User::is_authenticated()) {
			$this->session->set("requested_url", "/".url::current());
			url::redirect('sign_in');
		}
	}
	
	public function index() 
	{
	    $search_doc = SK_Navigation::getDocument('profile_search');
	    if ( !$search_doc->status || !app_Features::isAvailable(2) ) { url::redirect('not_found'); }
        if ( !$search_doc->access_member ) { url::redirect('home'); }
	    
		$search_fields = app_ProfileSearch::SearchTypeFields('quick_search');
		$fields = array();
		foreach ($search_fields as $field)
		{
			if ( in_array( $field, array( 'country_id', 'state_id', 'city_id', 'zip', 'custom_location' ) ) )
				continue;

			$fobj = SK_ProfileFields::get($field);

			$fields[$field]['id'] = $fobj->profile_field_id;
			
			if (in_array($field, array( 'sex', 'match_sex'))) {
				$fields[$field]['values'] = $fobj->values;
				$fields[$field]['prefix'] = $fobj->matching ? $fobj->matching : $fobj->name;;				
			}
			if ($field == 'birthdate') {
				$range = explode('-', $fobj->custom);			
				$date	= getdate();
				
				$min = $date['year'] - $range[1];
				$max = $date['year'] - $range[0];
				
				$from = array();
				$to = array();
				for ( $a = $min; $a <= $max; $a++ ) 
					$from[] = $a;
					
				for ( $a = $max; $a >= $min; $a-- )
					$to[] = $a;
					
				$fields[$field]['from'] = $from;
				$fields[$field]['to'] = $to;
			}
				
		}
		
		$view = new View('default');

		$view->header = SKM_Template::header(); 
		
		// menu
		$m = new SKM_Menu('main', 'search', SKM_Language::text('%nav_doc_item.headers.profile_search'));
		$view->menu = $m->get_view();
		
		// members list
		
		$db_search_unit = SK_Config::section('site')->Section('additional')->Section('profile_list')->search_distance_unit;
		$search_unit = SKM_Language::text('%i18n.location.' . $db_search_unit);
		SKM_Template::addTplVar('units', SKM_Language::text('%forms._fields.mileage.miles_from', array('unit' => $search_unit)));
		SKM_Template::addTplVar('form_action', url::base().'search/result');

		SKM_Template::addTplVar('fields', $fields);
		
		$service = new SK_Service('search', SKM_User::profile_id());
		if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
		{
            SKM_Template::addTplVar('service_msg', strip_tags($service->permission_message['message']));
		}
		
		$search = new View('search', SKM_Template::getTplVars());
				
		$view->content = $search;

		$view->footer = SKM_Template::footer();
		
		$view->render(TRUE);
	}
	
	public function saved( )
	{
        $search_doc = SK_Navigation::getDocument('profile_search');
        if ( !$search_doc->status || !app_Features::isAvailable(2) ) { url::redirect('not_found'); }
        if ( !$search_doc->access_member ) { url::redirect('home'); }
	    
		$view = new View('default');

		$view->header = SKM_Template::header();
		
		// menu
		$m = new SKM_Menu('main', 'search', SKM_Language::text('%nav_doc_item.headers.profile_search'));
		$view->menu = $m->get_view();
		
		$lists = app_SearchCriterion::getCriterionList(SKM_User::profile_id());
		foreach ($lists as $key => $list)
			$lists[$key]->href = url::base() . 'members/search_list/'. $list->criterion_id;
	    
		SKM_Template::addTplVar('lists', $lists);
		
		$view->content = new View('searches', SKM_Template::getTplVars());
		$view->footer = SKM_Template::footer();

		$view->render(TRUE);
	}
	
	public function result( $page = 1 )
	{
        $search_doc = SK_Navigation::getDocument('profile_search');
        if ( !$search_doc->status || !app_Features::isAvailable(2) ) { url::redirect('not_found'); }
        if ( !$search_doc->access_member ) { url::redirect('home'); }
	    
		$view = new View('default');

		$view->header = SKM_Template::header();
		
		// menu
		$m = new SKM_Menu('main', 'search', SKM_Language::text('%nav_doc_item.headers.profile_search'));
		$view->menu = $m->get_view();
		
		$members = new Members_Model;
		$on_page = SK_Config::Section('site.additional.profile_list')->result_per_page;
		$profiles = $members->quickSearch($_GET, $page, $on_page);
		
		if (isset($profiles['total']))
		{
			$pagination = new Pagination(array(
		        'base_url'    => 'search/result',
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
			
	    $service = new SK_Service('search', SKM_User::profile_id());
        if ( $service->checkPermissions() != SK_Service::SERVICE_FULL )
        {
            SKM_Template::addTplVar('service_msg', strip_tags($service->permission_message['message']));
        }
			
		$members = new View('members/featured', SKM_Template::getTplVars());
				
		$view->content = $members;
		$view->footer = SKM_Template::footer();

		$view->render(TRUE);
	}
}
