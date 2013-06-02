<?php

class component_MusicList extends SK_Component
{
	private $list_type;
	
	/**
	 * Component MusicList constructor.
	 *
	 * @return component_MusicList
	 */
	public function __construct( array $params = null )
	{
		parent::__construct('music_list');
		
		$available_types = array('latest', 'toprated', 'most_viewed', 'discussed', 'tags', 'profile');
		
		if (isset(SK_HttpRequest::$GET['profile_id']))
		{
			$this->list_type = 'profile';
		}
		elseif (!isset($params['list_type']) || !in_array($params['list_type'], $available_types))
			$this->list_type = 'latest';
		else 
			$this->list_type = $params['list_type'];
		}
	
	public function render( SK_Layout $Layout )
	{
		
		$tag_limit = 50; 

		switch ($this->list_type)
		{
			case 'latest':
				$music = app_MusicList::getMusicList( 'latest', SK_HttpRequest::$GET['page']);
				$Layout->assign('MusicTagNavigator', new component_TagNavigator('music', SK_Navigation::href('music_list'), $tag_limit ));
				break;
			case 'toprated':
				$music = app_MusicList::getMusicList( 'toprated', SK_HttpRequest::$GET['page']);
				$Layout->assign('MusicTagNavigator', new component_TagNavigator('music', SK_Navigation::href('music_list'), $tag_limit ));
				break;
					
			case 'discussed':
				$music = app_MusicList::getMusicList( 'discussed', SK_HttpRequest::$GET['page']);
				$Layout->assign('MusicTagNavigator', new component_TagNavigator('music', SK_Navigation::href('music_list'), $tag_limit ));
				break;
				
			case 'profile':
				if (SK_HttpRequest::$GET['profile_id']) {
					$username = app_Profile::username(SK_HttpRequest::$GET['profile_id']);				
					$Layout->assign('username', $username);
					$bc_item = SK_Language::section('components.music_list')->text('music_by').' '.$username;
					SK_Navigation::addBreadCrumbItem($bc_item);
					$page = isset(SK_HttpRequest::$GET['page']) ? SK_HttpRequest::$GET['page'] : 1; 
					$music = app_ProfileMusic::getProfileMusic(SK_HttpRequest::$GET['profile_id'], 'active', false, $page);
					$Layout->assign('MusicTagNavigator', new component_TagNavigator('music', SK_Navigation::href('music_list'), $tag_limit ));
				}
				else SK_HttpRequest::showFalsePage();
				break;
		}

		$Layout->assign('list_type', $this->list_type);	

		SK_Language::defineGlobal( 'username', $username );
		
		if (isset($music))
		{
			$Layout->assign('list', $music['list']);
			$Layout->assign('total', $music['total']);
			
			$Layout->assign('paging',array(
				'total'=> $music['total'],
				'on_page'=> SK_Config::Section('music')->display_music_list_limit,
				'pages'=> SK_Config::Section('site')->Section('additional')->Section('profile_list')->nav_per_page,
			));
		}
						
		return parent::render($Layout);
	}
	
}

