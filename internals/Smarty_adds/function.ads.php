<?php

function smarty_function_ads(  array $params, SK_Layout $Layout )
{
	if (!app_Features::isAvailable(16)) {
		return '';
	}
	
	static $serviceAvaliable = false;
	
	// check service availability
	if ( !$serviceAvaliable )
	{
	   $service = new SK_Service("hide_ads", SK_HttpUser::profile_id());
	   
	   if ( $service->checkPermissions() == SK_Service::SERVICE_FULL )
	   {
	       $service->trackServiceUse();
	       $serviceAvaliable = true;
	   }
	}
	
	if ($serviceAvaliable) {
		return '';
	}

	$pos = trim($params['pos']);
	$doc_key = SK_HttpRequest::getDocument()->document_key;
	
	switch ( $pos )
		{
			case 'top':
				$pos_id = 1;
				break;
			case 'bottom':
				$pos_id = 2;
				break;
			case 'middle':
				$pos_id = 3;
				break;
			case 'sidebar':
				$pos_id = 4;
				break;
			case 'profile_list':
				$pos_id = 5;
				break;
			default:
				trigger_error( __FILE__.' function '.__FUNCTION__.' : undefined position name <code>'.$pos.'</code>' );
		}
		
	$ads_code = app_Advertisement::DocPositionTpl( $pos_id );

		
	return trim($ads_code) ? "<div class='ads center clr'>" . $ads_code . "</div>" : '';
	
}

