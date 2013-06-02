<?php

function smarty_function_cls_thumb( array $params, SK_Layout $Layout )
{
	if ( !key_exists('thumb', $params) ) {
		trigger_error('{cls_thumb...} missing attribute "thumb"', E_USER_WARNING);
		return;
	}
    
	$params['size'] = isset($params['size']) ? $params['size'] : 100;
	
	if ( isset($params['size']) ) {
		$size = 'width="' . $params['size'] . '"';
	}
	else {
		$size = '';
	}

	$src = !empty($params['thumb']) ? $params['thumb'] : app_ClassifiedsItemService::newInstance()->getItemNoPhoto();
	$title = ( isset($params["title"]) && $params["title"] ) ? " title='".$params["title"]."' " : '';
	
	
	$imgEmbed = "<img $title class='profile_thumb'  $size  src='$src' />";

	
	if (@$params["href"] === false) 
	{
		$out = $imgEmbed;
	} 
	else 
	{
		$href = (isset($params["href"]) && $params["href"]) ? $params["href"] : '';
		
		$out = <<<EOT
<a href="$href">
	$imgEmbed 
</a>
EOT;
	}
	
	return $out;
}
