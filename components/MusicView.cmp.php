<?php

class component_MusicView extends SK_Component
{
	/**
	 * info about music owner
	 *
	 * @var array
	 */
	private $profile_info = array();

	/**
	 * info about music
	 * music_id, profile_id, hash
	 *
	 * @var array
	 */
	private $music_info = array();

	/**
	 * music source: 'file' || 'embed_code'
	 *
	 * @var string
	 */
	private $music_source;

	/**
	 * music player settings
	 *
	 * @var array
	 */
	private $player_settings = array();

	private $hash;

	private $profile_id;

	public function __construct( array $params = null )
	{
           	parent::__construct('music_view');

		$this->hash = SK_HttpRequest::$GET['musickey'];

		// detect music owner profile id
		$this->profile_id = app_ProfileMusic::getMusicOwner($this->hash);

		if (!$this->profile_id)
			SK_HttpRequest::showFalsePage();
	}

	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$Frontend->onload_js(
			'$("#'.$this->getTagAutoId('video_code').'").click(
				function() {
					this.select();
				});

			$("#'.$this->getTagAutoId('permalink').'").click(
				function() {
					this.select();
				});'
		);

		parent::prepare($Layout, $Frontend);
	}

	private function formatEmbedCode()
	{
		$output = '<embed src="'.$this->player_settings['player_src'].'" width="'.$this->player_settings['width'].'"
height="'.$this->player_settings['height'].'"
allowfullscreen="false"
flashvars="&file='.$this->music_info['music_url'].'&image='.$this->music_info['preview_url'].'&bufferlength=10'.$videowatermark_code.'" />';

		return SK_Language::htmlspecialchars($output);
	}

	public function render( SK_Layout $Layout )
	{
		$viewer_profile_id = SK_HttpUser::profile_id();


		$service = new SK_Service('view_music', $viewer_profile_id);

		$this->music_info = app_ProfileMusic::getMusicInfo($this->profile_id, $this->hash);

        $title = app_TextService::stCensor($this->music_info['title'], FEATURE_MUSIC, true);
		$title = app_TextService::stOutputFormatter($title, FEATURE_MUSIC, false);

		SK_Language::defineGlobal('musictitle', $title);

                if ( !empty($this->music_info['description']) )
                {
                    $this->getDocumentMeta()->description = $this->music_info['description'];
                }

		if (!app_FriendNetwork::isProfileFriend($viewer_profile_id, $this->profile_id) && $this->music_info['privacy_status'] == 'friends_only' && $viewer_profile_id != $this->profile_id)
			$Layout->assign('service_msg', SK_Language::section('components.music_view')->text('is_not_friend', array('username' => app_Profile::username($this->profile_id) )));

		elseif ($service->checkPermissions() == SK_Service::SERVICE_FULL || $viewer_profile_id == $this->profile_id)
		{
            $username = app_Profile::username($this->profile_id);
			$username = isset($username) ? $username : SK_Language::section('label')->text('deleted_member');
			$bc_item_1 = SK_Language::section('components.music_list')->text('music_by') . " " . $username;
			$bc_item_2 = $title;
			SK_Navigation::removeBreadCrumbItem();
			SK_Navigation::addBreadCrumbItem($bc_item_1, SK_Navigation::href('profile_music', array('profile_id' => $this->profile_id)));
			SK_Navigation::addBreadCrumbItem($bc_item_2);

			app_ProfileMusic::updateViews($this->music_info['music_id'], $viewer_profile_id);

			$this->music_info['music_url'] = app_ProfileMusic::getMusicURL($this->hash, $this->music_info['extension']);

			$this->player_settings['player_src'] = URL_FLASH_MEDIA_PLAYER.'mediaplayer.swf';

			$this->music_info['owner_name'] = $username;

			if ($this->music_info['music_source'] == 'file')
				$this->music_info['share_code'] = $this->formatEmbedCode();
			else
			{
				$this->music_info['share_code'] = SK_Language::htmlspecialchars($this->music_info['code']);
				$this->music_info['code'] = app_ProfileMusic::formatEmbedCode($this->music_info['code']);
			}

			$this->music_info['permalink'] = sk_make_url(null);

			$this->music_info['hash'] = $this->hash;

			$Layout->assign('music_info', $this->music_info);

            $Layout->assign('MusicRate', new component_Rate(array('entity_id' => $this->music_info['music_id'], 'feature' => 'music')));

			$Layout->assign('viewer_id', $viewer_profile_id);

            $Layout->assign('show_details', SK_Config::section('music')->get('show_share_details'));

		}
		else {
			$Layout->assign('service_msg', $service->permission_message['message']);
		}

		$comments = app_Features::isAvailable(58) ? new component_AddComment($this->music_info['music_id'], 'music', 'music_upload') : null;
		$Layout->assign('music_comments', $comments);


		return parent::render($Layout);
	}

}
