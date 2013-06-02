<?php

class SKM_Menu {
	
	private $menu = array();
	
	private $type;
	
	private $active;
	
	private $page_head;
	
	public function __construct( $type, $active = null, $head = null )
	{
		$this->type = $type;
		$this->active = $active;
		$this->page_head = $head;
				
		$this->set_menu();
	}
	
	public function get()
	{
		return $this->menu;
	}
	
	public function get_submenu($item = null)
	{
		if (isset($item))
			return $this->menu[$item]['sub'];
			
		else {
			$active = $this->get_active();
			if (strlen($active))
				return isset($this->menu[$active]['sub']) ? $this->menu[$active]['sub'] : array();
		}
	}
	
	public function get_view()
	{
		$menu_vars = array (
			'main_menu' => $this->get(), 
			'page_head' => $this->page_head, 
			'sub_menu' => $this->get_submenu(), 
			'mset' => Kohana::config('config.mset')
		);
		
		if ( SKM_User::is_authenticated() )
		{
			$IM_invitations = IM_Model::getNewInvitations();
			if ($IM_invitations) {
				$menu_vars['im_inv'] = $IM_invitations;
			}
		}
		
		return new View('menu', $menu_vars);		
	}
	
	private function set_menu()
	{
        $s1 = URI_Core::instance()->segment(1);
        $s2 = URI_Core::instance()->segment(2);
		
		switch ($this->type)
		{
			case 'main':
				if (SKM_User::is_authenticated())
					$this->menu['home'] = $this->add_item('nav_menu_item.index', url::base().'home/', $s1 == 'home' && !strlen($s2));
				else
					$this->menu['sign_in'] = $this->add_item('nav_doc_item.sign_in', url::base().'sign_in/', $s1 == 'sign_in' || $s1 == '');

				$this->menu['mailbox'] = $this->add_item('nav_menu_item.mailbox', url::base().'mailbox/inbox/', $s1 == 'mailbox');
					$this->menu['mailbox']['sub']['inbox'] = $this->add_item('components.mailbox_conversations_list.submenu_item_inbox', url::base().'mailbox/inbox/', $s2 == 'inbox');
					$this->menu['mailbox']['sub']['sent'] = $this->add_item('components.mailbox_conversations_list.submenu_item_sentbox', url::base().'mailbox/sent/', $s2 == 'sent');
					$this->menu['mailbox']['sub']['newmsg'] = $this->add_item('mobile.sm_new_message', url::base().'mailbox/new/', $s2 == 'new');
				
				$match_doc = SK_Navigation::getDocument('match_list');
			    $sub_matches = $match_doc->status && $match_doc->access_member && app_Features::isAvailable(51);
			    
			    $online_doc = SK_Navigation::getDocument('online_list');
                $sub_online = $online_doc->status && $online_doc->access_member;
			    
                $featured_doc = SK_Navigation::getDocument('featured_list');
                $sub_featured = $featured_doc->status && $featured_doc->access_member;
                
                $new_doc = SK_Navigation::getDocument('profile_new_members_list');
                $sub_new = $new_doc->status && $new_doc->access_member;
                
                if ( $sub_matches || $sub_online || $sub_featured || $sub_new )
                {
				    $this->menu['members'] = $this->add_item('nav_menu_item.members', url::base().'members/', $s1 == 'members');
                }
				    if ( $sub_online )
				    {
					   $this->menu['members']['sub']['online'] = $this->add_item('nav_menu_item.online_list', url::base().'members/online/', $s2 == 'online');
				    }
				    if ( $sub_new )
				    {
					   $this->menu['members']['sub']['new'] = $this->add_item('nav_menu_item.new_members', url::base().'members/new/', $s2 == 'new');
				    }
					if ( $sub_matches )
					{
					   $this->menu['members']['sub']['matches'] = $this->add_item('nav_doc_item.match_list', url::base().'members/matches/', $s2 == 'matches');
					}
					if ( $sub_featured )
					{
					   $this->menu['members']['sub']['featured'] = $this->add_item('nav_menu_item.featured_list', url::base().'members/featured/', $s2 == 'featured' || !strlen($s2));
					}
					
                $search_doc = SK_Navigation::getDocument('profile_search');
                if ( $search_doc->status && $search_doc->access_member && app_Features::isAvailable(2) )
                {
        			$this->menu['search'] = $this->add_item('nav_menu_item.main_searc', url::base().'search/', $s1 == 'search');
        				$this->menu['search']['sub']['newsearch'] = $this->add_item('nav_doc_item.titles.profile_search', url::base().'search', $s1 == 'search' && !strlen($s2));
        				$this->menu['search']['sub']['searches'] = $this->add_item('components.saved_lists.my_searches', url::base().'search/saved', $s2 == 'saved');				
                }
				$this->menu['profile'] = $this->add_item('mobile.mitem_profile', url::base().'profile/', $s1 == 'profile');
				break;

			case 'bottom':
				$this->menu['about'] = $this->add_item('nav_menu_item.about_us', url::base().'about/', $s1 == 'about');
				$this->menu['terms'] = $this->add_item('nav_menu_item.terms_of_use', url::base().'terms/', $s1 == 'terms');
				$this->menu['privacy'] = $this->add_item('nav_menu_item.privacy_policy', url::base().'privacy/', $s1 == 'privacy');
                $this->menu['main'] = $this->add_item('mobile.full_site', SITE_URL . 'index.php?from=mobile', false);
				break;
		}
	}
	
	public function get_active()
	{	
		foreach ($this->menu as $name => $item)
		{
			if ($this->menu[$name]['active'] === true) {
				return $name;
			}
		}
	}
	
	private function add_item($title, $link, $active = false)
	{
		return array('title' => SKM_Language::text($title), 'link' => $link, 'active' => $active);
	}
	
	
}
