<?php

class component_IndexVideoPlayer extends SK_Component
{
    private $listType;
    
    private $width = 310;
    
    private $height = 250;
    
	public function __construct( array $params = null )
	{
		parent::__construct('index_video_player');
		
		if ( !app_Features::isAvailable(4) )
		{
			$this->annul();
		}
		else 
		{	
			$this->listType = in_array($params['type'], array('latest', 'toprated')) ? $params['type'] : 'toprated';
			
			if ( isset($params['width']) )
			{
                $this->width = (int) $params['width'];
			}
			
            if ( isset($params['height']) )
            {
                $this->height = (int) $params['height'];
            }
		}
	}
		
	public function render( SK_Layout $Layout )
	{	
		$service = new SK_Service('view_video', SK_HttpUser::profile_id());
		
		if ( $service->checkPermissions() == SK_Service::SERVICE_FULL ) 
		{
			$video = app_VideoList::getIndexVideo($this->listType, $this->width, $this->height);
			if ( isset($video['for_player']['video_id']) ) 
			{
			    $Layout->assign('video', $video['for_player']);
				//$video_list[$list]['player'] = $video['for_player'];
			}
			else 
			{
			    $Layout->assign('service_msg', SK_Language::section('components.index_video')->text('no_video'));
			}

			$Layout->assign('player_width', $this->width);
			$Layout->assign('player_height', $this->height);
		}
		else 
		{
			$Layout->assign('service_msg', $service->permission_message['message']);					
		}
		
		return parent::render($Layout);	
	}
	
    public function prepare( SK_Layout $Layout, SK_Frontend $Frontend )
    {
        $Frontend->onload_js(
        'var $player = $("iframe", ".index_video_player");
        var url = $player.attr("src");
        if ( url != undefined )
        {
            var wmode = "wmode=transparent";
            if ( url.indexOf("?") != -1) 
                $player.attr("src", url + "&" + wmode);
            else 
                $player.attr("src", url + "?" + wmode);
        }');
                
        parent::prepare($Layout, $Frontend);
    }
}
