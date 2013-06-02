<?php

class nav_video
{
	
	public function __construct()
	{
		SK_Navigation::register_node('profile_video_view', array(&$this, 'profile_video'));
	}
	
	
	public function parseUrl($url) {
		$url_info = parse_url($url);
		$path = $url_info["path"];
		
		if (strpos($path, "/member/video")!==0) {
			return null;
		}
		
		list($f1, $f2, $hash) = explode("/", substr($path, 1));
		
		return SK_Navigation::href("profile_video_view", array("videokey" => $hash), true);
			
	}
	
	public function profile_video( array $params ) {
		
		if ( $params['videokey'] ) {
			$out = SITE_URL . "member/video/".$params['videokey'];			
		} 
		elseif( $params['video_id'] ){
			$out = SITE_URL . "member/video/".app_ProfileVideo::getVideoHash($params['video_id']);
		}
		else {
			$out = SITE_URL . "member/video/";
		}
		return $out;
	}
	
}

