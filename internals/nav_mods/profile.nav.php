<?php

class nav_profile
{
	
	public function __construct()
	{
		SK_Navigation::register_node('profile', array(&$this, 'profile'));
		SK_Navigation::register_node('profile_view', array(&$this, 'profile'));
	}
	
	public function parseUrl($url) {
		$url_info = parse_url($url);
		$path = $url_info["path"];
		
		if (strpos($path, "/member/profile")!==0) {
			return null;
		}
		
		list($f1, $profile_url ) = explode("/", substr($path, 1));

                $queryString = $url_info['query'];

		$tmp = substr($profile_url, 0, strlen($profile_url)-5);
		
		list($f2, $username) = explode("_", $tmp, 2);
		
		if (!isset($username)) {
			$result = SK_Navigation::href("profile", array(), true);
			return $result;
		}
		
		$profile_id = app_Profile::getProfileIdByUsername(urldecode($username));
		
		if (!$profile_id) {
			return SK_Navigation::href("false_page");
		}

                $params = array();
                
                if ( !empty($queryString) )
                {
                    parse_str($queryString, $params);
                }
                $params['profile_id'] = $profile_id;

		$result =  SK_Navigation::href("profile", $params, true);
		
		return $result;	
	}
	

	public function profile( array $params )
	{
        $url = SITE_URL."member/profile.html";

		if (isset($params["profile_id"])) {
			$username = app_Profile::username((int)$params["profile_id"], true);
			$url = SITE_URL."member/profile_$username.html";
                        unset($params["profile_id"]);
		}

                $queryString = '';

                foreach ( $params as $key => $value )
                {
                    $queryString .= '&' . $key . '=' . $value;
                }

                if ( !empty($queryString) )
                {
                    $queryString = '?' . substr($queryString, 1);
                }

		return $url . $queryString;
	}
	
}

