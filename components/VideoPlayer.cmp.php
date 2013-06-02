<?php

class component_VideoPlayer extends SK_Component
{
	private $video_mode;
	
	private $hash;
	
	private $code;
	
	private $player = array();
	
	public function __construct( array $params = null )
	{
		parent::__construct('video_player');

		$this->video_mode = SK_Config::section('video')->get('media_mode');
		$this->player['video_src'] = $params['video_file_url'];
		switch ($this->video_mode)
		{
			case 'flash_video':
				$this->player['src'] = URL_FLASH_MEDIA_PLAYER.'mediaplayer.swf';
				
				$pattern = preg_quote('/pr_media_', '~').'([^&]+).flv';
				preg_match("~$pattern~", $this->player['video_src'], $hash);
				$this->player['preview_img'] = app_ProfileVideo::getVideoFrameURL($hash[1]);
				break;
				
			case 'windows_media':
				break;
		}
						
		$this->player['width'] = isset($params['width']) ? $params['width'] : SK_Config::section('video')->get('video_width');
		$this->player['height'] = isset($params['height']) ? $params['height'] : SK_Config::section('video')->get('video_height');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$id = $this->getTagAutoId('obj');
		switch ( $this->video_mode )
		{
			case 'flash_video':
				// watermark config
				$watermark_img = SK_Config::section('video')->Section('watermark')->get('enable_video_watermark') == 1 ? 
					URL_USERFILES.'video_watermark_img_'.SK_Config::section('video')->Section('watermark')->get('watermark_img').'.jpg' : '';

				$preview_img = isset($this->player['preview_img']) ? $this->player['preview_img'] : '';
						
				$handler = new SK_ComponentFrontendHandler('VideoPlayer');
				$this->frontend_handler = $handler;
				
				$Frontend->include_js_file(URL_STATIC.'swfobject.js');
				$handler->construct($this->player['video_src'], $this->player['src'], $this->player['width'], $this->player['height'], $watermark_img, $preview_img, $id);

				$handler->loadVideoPlayer();
				
				$output = '<p id="video_player_'.$id.'">'.SK_Language::section('components.video_player')->text('get_flashplayer_msg').'</p>';

				break;
								
			case 'windows_media':
				$output = 
				'<p id="video_player_'.$id.'">
				<object width="'.$this->player['width'].'"
					height="'.$this->player['height'].'"
  				 	type="application/x-oleobject"
  				 	classid="clsid:22d6f312-b0f6-11d0-94ab-0080c74c7e95"
  					codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#version=5,1,52,701">

					<param name="filename" value="'.$this->player['video_src'].'">
					<param name="transparentatstart" value="false">
					<param name="autostart" value="false">
					<param name="showcontrols" value="true">
					<param name="showtracker" value="false">
					<param name="showaudiocontrols" value="true">
					<param name="showstatusbar" value="true">
					  
					<embed type="application/x-mplayer2"
						width="'.$this->player['width'].'"
						height="'.$this->player['height'].'"
						src="'.$this->player['video_src'].'"
						autostart="0"
						showstatusbar="1"
						showdisplay="0"
						showcontrols="1"
						controltype="1"
						showtracker="1"
						pluginspage="http://www.microsoft.com/windows/downloads/contents/products/mediaplayer/">
					</embed>
				</object></p>';				
		}
		$Layout->assign('player_code',$output);	
	}
	
	
	public function render( SK_Layout $Layout )
	{

		return parent::render($Layout);
	}
	
}
