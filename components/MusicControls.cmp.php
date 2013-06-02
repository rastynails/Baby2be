<?php

class component_MusicControls extends SK_Component
{
	private $music_id;
	
	public function __construct( array $params = null )
	{
		$this->music_id = $params['music_id'];
				
		parent::__construct('music_controls');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('MusicControls');
		$handler->construct($this->music_id);
		$this->frontend_handler = $handler;
				
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{		
		$Layout->assign('music_id', $this->music_id);

		return parent::render($Layout);
	}
	
	public static function ajax_DeleteMusic($params, SK_ComponentFrontendHandler $handler)
	{
		$music_id = (int)$params->music_id;

		$error_ns = SK_Language::section('components.music_controls.error_msg');
		$message_ns = SK_Language::section('components.music_controls.msg');
		
		if (app_ProfileMusic::deleteMusic($music_id))
		{
			$handler->message($message_ns->text('delete_success'));
			$handler->reloadList();
		}
		else 
			$handler->error($error_ns->text('delete_error'));
	}
}
