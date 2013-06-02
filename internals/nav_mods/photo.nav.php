<?php

class nav_photo
{
	
	public function __construct()
	{
		SK_Navigation::register_node('profile_photo', array(&$this, 'profile_photo'));
	}
	
	
	public function parseUrl($url) {
		$url_info = parse_url($url);
		$path = $url_info["path"];
		
		if (strpos($path, "/member/photos")!==0) {
			return null;
		}
		
		list($f1, $f2, $username, $photo ) = explode("/", substr($path, 1));
				
		$profile_id = app_Profile::getProfileIdByUsername(urldecode($username));
		
		if (!$profile_id) {
			return SK_Navigation::href("false_page");
		}
		
		if (!isset($photo)) {
			return SK_Navigation::href("profile_photo", array("profile_id" => $profile_id), true);
		}
		
		if($profile_id != app_ProfilePhoto::getPhoto(intval($photo))->profile_id) {
			return SK_Navigation::href("false_page");
		}
				
		$result = SK_Navigation::href("profile_photo", array("photo_id" => $photo), true);
		
		return $result;
			
	}
	
	public function profile_photo( array $params ) {
		
		if ( !isset($params['photo_id']) && $album_id = intval($params['album']) ) {
			$album = app_PhotoAlbums::getAlbum($album_id);
			$params["photo_id"] = $album->getFirst_photo_id(); 
		}
		
		if (isset($params["photo_id"])) {
			$photo_info = app_ProfilePhoto::getPhoto(intval($params["photo_id"]));
			$out = SITE_URL . "member/photos/" 
				. app_Profile::getFieldValues($photo_info->profile_id, 'username')
				. "/" . $photo_info->photo_id;
		} elseif($profile_id = intval(@$params["profile_id"])) {
			$out = SITE_URL . "member/photos/" . app_Profile::getFieldValues($profile_id, 'username');
		} else {
			$out = SITE_URL . "member/photos/";
		}
		return $out;
	}
	
}

