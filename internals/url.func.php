<?php

function sk_make_url( $url=null, $params = null, $hash = null )
{
	if (!isset($url)) {
		$url = sk_request_uri();
	}
	else{
		$url = sk_request_uri($url);
	}

	$url = SITE_URL.substr($url, 1);

	$url_info = parse_url($url);

	$url = strlen($_url = substr($url, 0, strpos($url, '?'))) ? $_url : $url ;

	if (isset($url_info['query'])) {
		parse_str($url_info['query'], $_params);
	}
	else {
		$_params = array();
	}

	$params = isset($params) ? $params : array();

	if(is_string($params) && strlen(trim($params)))
		parse_str($params, $params);
	elseif(!is_array($params))
		$params=array();

	$_params = array_merge($_params, $params);

	$hash_str = isset($hash) ? '#'.trim($hash) : (isset($url_info['fragment']) ? '#'.$url_info['fragment']:'');
	$query_str = count($_params) ? '?'.http_build_query($_params) : '';
	return $url.$query_str.$hash_str;
}

function sk_request_uri($url=null)
{
    if (isset($url))
    {
        $uri_info = @parse_url($url);

        if ( !$uri_info )
        {
            return null;
        }

        if (isset($uri_info["host"]))
        {
            $host = $uri_info["host"] . ( empty( $uri_info['port'] ) ? '' : ':' . $uri_info['port'] );
            $uri = substr(strstr($url, $host), strlen($host));
        }
        else
        {
            $uri = $url;
        }

    } else {
        $uri = $_SERVER['REQUEST_URI'];
    }

    $s_url_info =parse_url(SITE_URL);

    if ( strpos($uri, $s_url_info["path"]) === 0 )
    {
        $uri = '/' . substr($uri, strlen($s_url_info["path"]));
    }

    return $uri;
}