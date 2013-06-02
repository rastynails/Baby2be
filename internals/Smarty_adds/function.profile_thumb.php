<?php

function smarty_function_profile_thumb( array $params, SK_Layout $Layout )
{
	if ( !key_exists('profile_id', $params) ) {
		trigger_error('{profile_thumb...} missing attribute "profile_id"', E_USER_WARNING);
		return;
	}
    $deleted = !($profile_id = (int)$params['profile_id']);

	$info = $deleted ? array() : app_Profile::getFieldValues($profile_id, array('sex', 'username', 'profile_id'));
        if (!$deleted)
        {
            $info['username'] = app_Profile::username($profile_id);
        }

	if (empty($info['profile_id']))
	{
	    $deleted = true;
	}

	$params['size'] = isset($params['size']) ? $params['size'] : 100;

	if ( isset($params['size']) ) {
		$size = 'width="' . $params['size'] . '"';
	}
	else {
		$size = '';
	}

        $href_params['profile_id'] = $profile_id;

        if( isset($params['redirect_params']) && is_array($params['redirect_params']) )
        {
            foreach( $params['redirect_params'] as $key => $value )
            {
                if( in_array($key, array( 'list_name', 'search_type', 'page', 'sex', 'tab', 'mode', 'period', 'distance' ) ) )
                {
                    $href_params[$key] = $value;
                }
            }
        }

	$href = $deleted ? "#" : SK_Navigation::href('profile', $href_params);
	$username = $deleted ? SK_Language::text("label.deleted_member") : $info["username"];
	$src = $deleted ? app_ProfilePhoto::deleted_url() : app_ProfilePhoto::getThumbUrl($profile_id);

	$print_username = isset($params["username"]) && $params["username"];

	$imgSexClass = !isset($params['sexLine']) || !empty($params['sexLine']) ? 'sex_line_' . $info['sex'] : '';
	$imgEmbed = '<img ' . $size . ' title="' . $username . '" src="' . $src . '" class="profile_thumb ' . $imgSexClass . '" />';

	if (isset($params['onlineMark']) && empty($params['onlineMark']))
	{
	    $onlineMark = '';
	}
	else
	{
        $onlineMark = app_Profile::isOnline($profile_id) ? '<div class="profile_thumb_online"></div>' : '';
	}

	if (@$params["href"] === false)
	{
	    $label = $print_username ? '<div class="username">'.$username.'</div>' : '';

		$out=  '<div class="profile_thumb_wrapper">' . $imgEmbed . $label . $onlineMark .'<div class="img_frame img_size_'.$params['size'].'"></div></div>';

	}
	else
	{
		$href = (isset($params["href"]) && $params["href"]) ? $params["href"] : $href;

		$label = $print_username ? '<div class="username">'.$username.'</div>' : '';
		$out = <<<EOT

<a class="profile_thumb_wrapper" href="$href">
		{$imgEmbed}{$label}{$onlineMark}
        <div class="img_frame img_size_{$params['size']}"></div>
</a>
EOT;
	}

	return $out;
}
