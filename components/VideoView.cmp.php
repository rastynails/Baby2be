<?php

class component_VideoView extends SK_Component
{
	/**
	 * info about video owner
	 *
	 * @var array
	 */
	private $profile_info = array();

	/**
	 * info about video
	 * video_id, profile_id, hash
	 *
	 * @var array
	 */
	private $video_info = array();

	/**
	 * global video mode: 'windows_media' || 'flash_video'
	 *
	 * @var string
	 */
	private $video_mode;

	/**
	 * video source: 'file' || 'embed_code'
	 *
	 * @var string
	 */
	private $video_source;

	/**
	 * video player settings
	 *
	 * @var array
	 */
	private $player_settings = array();

	private $hash;

	private $profile_id;

	public function __construct( array $params = null )
	{
		parent::__construct('video_view');

		$this->hash = SK_HttpRequest::$GET['videokey'];

		// detect video owner profile id
		$this->profile_id = app_ProfileVideo::getVideoOwner($this->hash);

		if (!$this->profile_id)
			SK_HttpRequest::showFalsePage();
			
        $this->video_info = app_ProfileVideo::getVideoInfo($this->profile_id, $this->hash);
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
		
		if ( $this->video_info['video_source'] != 'file' )
		{
            $Frontend->onload_js('
                var $player = $("iframe", "#video_player_cont");
                var url = $player.attr("src");
                if ( url != undefined )
                {
                    var wmode = "wmode=transparent";
                    if ( url.indexOf("?") != -1) 
                        $player.attr("src", url + "&" + wmode);
                    else 
                        $player.attr("src", url + "?" + wmode);
                }
            ');
		}

		$handler = new SK_ComponentFrontendHandler("VideoView");
		$handler->construct($this->hash, $this->profile_id);

		$this->frontend_handler = $handler;

		parent::prepare($Layout, $Frontend);
	}

	private function formatEmbedCode()
	{
		switch ( $this->video_mode )
		{
			case 'flash_video':
				if ( $watermark_img = SK_Config::section('video')->Section('watermark')->get('enable_video_watermark') )
					$videowatermark_code = '&logo='.URL_USERFILES.'video_watermark_img_'.SK_Config::section('video')->Section('watermark')->get('watermark_img').'.jpg';

				$output = '<embed src="'.$this->player_settings['player_src'].'" width="'.$this->player_settings['width'].'"
height="'.$this->player_settings['height'].'"
allowfullscreen="true"
flashvars="&file='.$this->video_info['video_url'].'&image='.$this->video_info['preview_url'].'&bufferlength=10'.$videowatermark_code.'" />';
				break;

			case 'windows_media':

				$output = '<object width="'.$this->player_settings['width'].'"
height="'.$this->player_settings['height'].'"
type="application/x-oleobject" classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95"
codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#version=5,1,52,701">

<param name="filename" value="'.$this->video_info['video_url'].'">
<param name="transparentatstart" value="false">
<param name="autostart" value="false">
<param name="showcontrols" value="true">
<param name="showtracker" value="false">
<param name="showaudiocontrols" value="true">
<param name="showstatusbar" value="true">

<embed type="application/x-mplayer2"
width="'.$this->player_settings['width'].'"
height="'.$this->player_settings['height'].'"
src="'.$this->video_info['video_url'].'"
autostart="0"
showstatusbar="1"
showdisplay="0"
showcontrols="1"
controltype="1"
showtracker="1"
pluginspage="http://www.microsoft.com/windows/downloads/contents/products/mediaplayer/">
</embed>
</object>';
		}

		return SK_Language::htmlspecialchars($output);
	}

	public function render( SK_Layout $Layout )
	{
		$viewer_profile_id = SK_HttpUser::profile_id();

		$service = new SK_Service('view_video', $viewer_profile_id);

		$title = app_TextService::stCensor($this->video_info['title'], FEATURE_VIDEO, true);
		$title = app_TextService::stOutputFormatter($title, FEATURE_VIDEO, false);

                if ( !empty($this->video_info['description']) )
                {
                    $this->getDocumentMeta()->description = strip_tags($this->video_info['description']);
                }

                SK_Language::defineGlobal('videotitle', $title);

		$username = app_Profile::getFieldValues($this->profile_id, 'username');
        $username = isset($username) ? $username : SK_Language::section('label')->text('deleted_member');
        $bc_item_1 = SK_Language::section('components.video_list')->text('video_by') . " " . $username;
        $bc_item_2 = $title;

        SK_Navigation::addBreadCrumbItem($bc_item_1, SK_Navigation::href('profile_video', array('profile_id' => $this->profile_id)));
        SK_Navigation::addBreadCrumbItem($bc_item_2);

		$pass_protection = $this->video_info['privacy_status'] == 'password_protected' && $viewer_profile_id != $this->profile_id && !app_ProfileVideo::isUnlocked($this->hash);
		$Layout->assign('pass_protection', $pass_protection);

		$private = $this->video_info['privacy_status'] == 'friends_only' && $viewer_profile_id != $this->profile_id && !app_FriendNetwork::isProfileFriend($viewer_profile_id, $this->profile_id);

		if ( $private )
		{
			$Layout->assign('service_msg', SK_Language::section('components.video_view')->text('is_not_friend', array('username' => $username)));
		}
		elseif ( $service->checkPermissions() == SK_Service::SERVICE_FULL || $viewer_profile_id == $this->profile_id )
		{
			if ( app_ProfileVideo::updateViews($this->video_info['video_id'], $viewer_profile_id) )
			{
                $service->trackServiceUse();
			}

			$this->video_info['video_url'] = app_ProfileVideo::getVideoURL($this->hash, $this->video_info['extension']);

			$this->video_mode = SK_Config::section('video')->get('media_mode');

			if ( $this->video_mode == 'flash_video' )
			{
				$this->player_settings['player_src'] = URL_FLASH_MEDIA_PLAYER.'mediaplayer.swf';
				$this->video_info['preview_url'] = app_ProfileVideo::getVideoThumbnailURL($this->hash);
			}

			$this->player_settings['width'] = SK_Config::section('video')->get('video_width');
			$this->player_settings['height'] = SK_Config::section('video')->get('video_height');

			$this->video_info['owner_name'] = app_Profile::getFieldValues($this->profile_id, 'username');

			if ($this->video_info['video_source'] == 'file')
				$this->video_info['share_code'] = $this->formatEmbedCode();
			else
			{
				$this->video_info['share_code'] = SK_Language::htmlspecialchars($this->video_info['code']);
				$this->video_info['code'] = app_ProfileVideo::formatEmbedCode($this->video_info['code'], $this->player_settings['width'], $this->player_settings['height']);
			}

			$this->video_info['permalink'] = sk_make_url(null);

			$this->video_info['hash'] = $this->hash;

			$Layout->assign('video_mode',$this->video_mode);

			$Layout->assign('VideoTags', new component_EntityItemTagNavigator('video', SK_Navigation::href('video_list'), $this->video_info['video_id']));
			$Layout->assign('VideoRate', new component_Rate(array('entity_id' => $this->video_info['video_id'], 'feature' => 'video')));

			$Layout->assign('viewer_id', $viewer_profile_id);
			$Layout->assign('show_details', SK_Config::section('video')->section('other_settings')->get('show_share_details'));
		}
		else {
			$Layout->assign('service_msg', $service->permission_message['message']);
		}

		$Layout->assign('video_info', $this->video_info);

		$comments = app_Features::isAvailable(57) ? new component_AddComment($this->video_info['video_id'], 'video', 'media_upload') : null;
		$Layout->assign('video_comments', $comments);
		$Layout->assign('enable_categories', SK_Config::section('video')->Section('other_settings')->get('enable_categories'));

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
