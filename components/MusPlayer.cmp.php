<?php

class component_MusPlayer extends SK_Component
{
	private $music_mode;
	
	private $hash;
	
	private $code;
	
	private $player = array();
	
	public function __construct( array $params = null )
	{
		parent::__construct('mus_player');

		$this->music_mode = 'flash_video';
		$this->player['music_src'] = $params['music_file_url'];
		$this->player['src'] = URL_FLASH_MEDIA_PLAYER.'mediaplayer.swf';
				
		$pattern = preg_quote('/pr_music_', '~').'([^&]+).flv';
		preg_match("~$pattern~", $this->player['music_src'], $hash);
						
		$this->player['width'] = '100%';
		$this->player['height'] = '20';
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$id = $this->getTagAutoId('obj');
		$handler = new SK_ComponentFrontendHandler('MusPlayer');
		$this->frontend_handler = $handler;
				
		$Frontend->include_js_file(URL_STATIC.'swfobject.js');

		$handler->construct($this->player['music_src'], $this->player['src'], $this->player['width'], $this->player['height'], $watermark_img, $preview_img, $id);
		$handler->loadMusPlayer();
				
		$output = '<p id="video_player_'.$id.'">'.SK_Language::section('components.mus_player')->text('get_flashplayer_msg').'</p>';
		
		$Layout->assign('player_code',$output);	
	}
	
	
	public function render( SK_Layout $Layout )
	{
		return parent::render($Layout);
	}	
}
