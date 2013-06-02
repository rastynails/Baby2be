<?php

class component_VideoControls extends SK_Component
{
	private $video_id;
	
	public function __construct( array $params = null )
	{
		$this->video_id = $params['video_id'];
				
		parent::__construct('video_controls');
	}
	
	public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
	{
		$handler = new SK_ComponentFrontendHandler('VideoControls');
		$handler->construct($this->video_id);			
		$this->frontend_handler = $handler;
				
		parent::prepare($Layout, $Frontend);
	}
	
	public function render( SK_Layout $Layout )
	{		
		$Layout->assign('video_id', $this->video_id);

		return parent::render($Layout);
	}
	
	public static function ajax_DeleteVideo($params, SK_ComponentFrontendHandler $handler)
	{
		$video_id = (int)$params->video_id;

		$error_ns = SK_Language::section('components.video_controls.error_msg');
		$message_ns = SK_Language::section('components.video_controls.msg');
		
		if (app_ProfileVideo::deleteVideo($video_id))
		{
			$handler->message($message_ns->text('delete_success'));
			$handler->reloadList();
		}
		else 
			$handler->error($error_ns->text('delete_error'));
	}
}
