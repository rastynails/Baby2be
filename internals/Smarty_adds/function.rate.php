<?php

function smarty_function_rate(  array $params, SK_Layout $Layout )
{
    switch ( trim($params['feature']) )
        {
            case FEATURE_PHOTO:
                $featureId = 12;
                break;

            case FEATURE_BLOG:
                $featureId = 47;
                break;

            case FEATURE_VIDEO:
                $featureId = 48;
                break;

            case FEATURE_MUSIC:
                $featureId = 49;
                break;

            default:
                $featureId = null;
        }

		if( !app_Features::isAvailable( $featureId ) && $featureId !== null)
        {
            return '';
        }


	if( !isset( $params['rate'] ) || (float)$params['rate'] < 1 || (float)$params['rate'] > 5 )
	{
		//return '_INVALID_RATE_PARAM_';
		$width = 0;
	}

	$width = (int)floor( (float)$params['rate'] / 5 * 100);
	
	return '<div class="inactive_rate_list"><div class="active_rate_list" style="width:'.$width.'px;"></div></div>';
}

