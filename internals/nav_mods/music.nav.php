<?php

class nav_music
{
	
	public function __construct()
	{
		SK_Navigation::register_node('profile_music_view', array(&$this, 'profile_music'));
		SK_Navigation::register_node('music_view', array(&$this, 'profile_music'));
	}
	
	
	public function parseUrl($url) {
		$url_info = parse_url($url);
		$path = $url_info["path"];
		
		if (strpos($path, "/member/music")!==0) {
			return null;
		}
		
		list($f1, $f2, $hash) = explode("/", substr($path, 1));
		return SK_Navigation::href("music_view", array("musickey" => $hash), true);
			
	}
	
	public static function profile_music( array $params ) {
		
		if (isset($params["musickey"])) {
			$out = SITE_URL . "member/music/".$params['musickey'];
		} 
		elseif( $params['music_id'] ){
			$out = SITE_URL . "member/music/".app_ProfileMusic::getMusicHash($params['music_id']);
		}
		else {
			$out = SITE_URL . "member/music/";
		}
                return $out;
	}
	
	
}

