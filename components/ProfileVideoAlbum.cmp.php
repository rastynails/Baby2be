<?php

class component_ProfileVideoAlbum extends SK_Component
{
	private $video_list; 
	
	private $profile_id; 
	
	public function __construct( array $params = null )
	{
		parent::__construct('profile_video_album');
		
		if ( !app_Features::isAvailable(4))
			$this->annul();
		else {
			$valid_lists = array('latest', 'toprated');
			$param_list = isset($params['active']) ? $params['active'] : 'latest';
			
			$this->video_list = in_array($param_list, $valid_lists) ? $param_list : 'latest';
			$this->profile_id = $params['profile_id'];
		}
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$this->frontend_handler = new SK_ComponentFrontendHandler('ProfileVideoAlbum');
		
		parent::prepare($Layout, $Frontend);
	}

	public function render( SK_Layout $Layout)
	{
		$viewer = SK_HttpUser::profile_id();
		$service = new SK_Service('view_video', $viewer);
		
		if ($service->checkPermissions() == SK_Service::SERVICE_FULL) {
			
			$Layout->assign('video_menu_items', array (
				array (
					'href'	 =>	'latest',
					'label'	 =>	SK_Language::section('components.profile_video_album')->text('submenu_item_latest'),
					'active' => ($this->video_list == 'latest') ? true : false,
				    'class' => 'latest'
				),
				array (
					'href'	 =>	'toprated',
					'label'	 =>	SK_Language::section('components.profile_video_album')->text('submenu_item_toprated'),
					'active' => ($this->video_list == 'toprated') ? true : false,
				    'class' => 'toprated'
				),
			));
			
			$list_latest = app_ProfileVideo::getProfileAlbumVideo($this->profile_id, 'latest');
			$list_toprated = app_ProfileVideo::getProfileAlbumVideo($this->profile_id, 'toprated');
	
			$width = SK_Config::section('video')->get('small_video_width');
			$height = SK_Config::section('video')->get('small_video_height');
			
			$Layout->assign('player_width', $width);
			$Layout->assign('player_height', $height);
			
			$Layout->assign('thumb_width', SK_Config::section('video')->get('video_thumb_width'));
			
			//latest video
			$video_latest = $list_latest['for_player'];
			if ($video_latest['video_source'] != 'file')
				$video_latest['code'] = app_ProfileVideo::formatEmbedCode($video_latest['code'], $width, $height);
				
			if ( $video_latest['privacy_status'] == 'friends_only' && !app_FriendNetwork::isProfileFriend($video_latest['profile_id'], $viewer) && $viewer != $video_latest['profile_id'] )
				$Layout->assign('latest_friends_only', SK_Language::section('components.profile_video_album')->text('friends_only', array('username'=>app_Profile::getFieldValues($video_latest['profile_id'], 'username'))));

                        if ( $viewer != $video_latest['profile_id'] && $video_latest['privacy_status'] == 'password_protected' && !app_ProfileVideo::isUnlocked($video_latest['hash']) )
                        {
                            $Layout->assign( 'latest_locked', true );
                        }

			$Layout->assign('first', $video_latest);
			$Layout->assign('others', $list_latest['for_thumbs']);
			
			//toprated video
			$video_top = $list_toprated['for_player'];
			if ($video_top['video_source'] != 'file')
				$video_top['code'] = app_ProfileVideo::formatEmbedCode($video_top['code'], $width, $height);	

			if ( $video_top['privacy_status'] == 'friends_only' && !app_FriendNetwork::isProfileFriend($video_top['profile_id'], $viewer) && $viewer != $video_top['profile_id'] )
				$Layout->assign('top_friends_only', SK_Language::section('components.profile_video_album')->text('friends_only', array('username'=>app_Profile::getFieldValues($video_top['profile_id'], 'username'))));

                        if ( $viewer != $video_top['profile_id'] && $video_top['privacy_status'] == 'password_protected' && !app_ProfileVideo::isUnlocked($video_top['hash']) )
                        {
                            $Layout->assign( 'top_locked', true );
                        }

			$Layout->assign('first_top', $video_top);
			$Layout->assign('others_top', $list_toprated['for_thumbs']);

                        $this->frontend_handler->construct( array('latestHash' => $video_latest['hash'], 'topHash' => $video_top['hash'], 'profile_id' => $this->profile_id) );
		}
		else {
			$Layout->assign('service_msg', $service->permission_message['message']);					
		}
		
		return parent::render($Layout);	
	}

        public static function ajaxUnlock( $params, SK_ComponentFrontendHandler $handler )
        {
            $hash = $params->hash;
            $profile_id = $params->profile_id;
            $info = app_ProfileVideo::getVideoInfo($profile_id, $hash);

            if ( $params->password != $info['password'] )
            {
                $handler->error(SK_Language::text("%components.photo_view.incorrect_password"));
                return false;
            }

            app_ProfileVideo::unlockVideo($hash);
            $handler->refresh();
        }
}
