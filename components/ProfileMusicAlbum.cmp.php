<?php

class component_ProfileMusicAlbum extends SK_Component
{
	private $music_list;
	
	private $profile_id; 
	
	public function __construct( array $params = null )
	{
		parent::__construct('profile_music_album');
		
		if ( !app_Features::isAvailable(40))
			$this->annul();
		else {
			$valid_lists = array('latest', 'toprated');
			$param_list = isset($params['active']) ? $params['active'] : 'latest';
			
			$this->music_list = in_array($param_list, $valid_lists) ? $param_list : 'latest';
			$this->profile_id = $params['profile_id'];
		}
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('ProfileMusicAlbum');
		
		$this->frontend_handler->construct();
		
		parent::prepare($Layout, $Frontend);
	}

	public function render( SK_Layout $Layout)
	{
		$viewer = SK_HttpUser::profile_id();
		$service = new SK_Service('view_music', $viewer);
		
		if ($service->checkPermissions() == SK_Service::SERVICE_FULL) {
			
			$Layout->assign('music_menu_items', array (
				array (
					'href'	 =>	'latest',
					'label'	 =>	SK_Language::section('components.profile_music_album')->text('submenu_item_latest'),
					'active' => ($this->music_list == 'latest') ? true : false,
				    'class' => 'latest'
				),
				array (
					'href'	 =>	'toprated',
					'label'	 =>	SK_Language::section('components.profile_music_album')->text('submenu_item_toprated'),
					'active' => ($this->music_list == 'toprated') ? true : false,
				    'class' => 'toprated'
				),
			));
			
			$list_latest = app_ProfileMusic::getProfileAlbumMusic($this->profile_id, 'latest');
			$list_toprated = app_ProfileMusic::getProfileAlbumMusic($this->profile_id, 'toprated');
	
			$width = '100%';
			$height =20;
			
			$Layout->assign('player_width', $width);
			$Layout->assign('player_height', $height);
			
			//$Layout->assign('thumb_width', SK_Config::section('music')->get('music_thumb_width'));
			
			//latest music
			$music_latest = $list_latest['for_player'];
			if ($music_latest['music_source'] != 'file')
				$music_latest['code'] = app_ProfileMusic::formatEmbedCode($music_latest['code'], $width, $height);
				
			if ( $music_latest['privacy_status'] == 'friends_only' && !app_FriendNetwork::isProfileFriend($music_latest['profile_id'], $viewer) && $viewer != $music_latest['profile_id'] )
				$Layout->assign('latest_friends_only', SK_Language::section('components.profile_music_album')->text('friends_only', array('username'=>app_Profile::getFieldValues($music_latest['profile_id'], 'username'))));
					
			$Layout->assign('first', $music_latest);
			$Layout->assign('others', $list_latest['for_thumbs']);
			
			//toprated music
			$music_top = $list_toprated['for_player'];
			if ($music_top['music_source'] != 'file')
				$music_top['code'] = app_ProfileMusic::formatEmbedCode($music_top['code'], $width, $height);

			if ( $music_top['privacy_status'] == 'friends_only' && !app_FriendNetwork::isProfileFriend($music_top['profile_id'], $viewer) && $viewer != $music_top['profile_id'] )
				$Layout->assign('top_friends_only', SK_Language::section('components.profile_music_album')->text('friends_only', array('username'=>app_Profile::getFieldValues($music_top['profile_id'], 'username'))));

			$Layout->assign('first_top', $music_top);
			$Layout->assign('others_top', $list_toprated['for_thumbs']);			
		}
		else {
			$Layout->assign('service_msg', $service->permission_message['message']);					
		}
		
		return parent::render($Layout);	
	}
}
