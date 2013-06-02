<?php
$file_key = 'ads';
$active_tab = 'template';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

require_once( DIR_ADMIN_INC.'class.admin_ads.php' );
require_once( DIR_ADMIN_INC.'fnc.design.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( @$_POST['create'] )
{
	switch ( adminAds::createTemplate( @$_POST['ads_name'], @$_POST['ads_code'] ) )
	{
		case -1:
			$frontend->RegisterMessage( 'Ads name must be entered', 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Ads code must be entered', 'error' );
			break;
		case -3:
			$frontend->RegisterMessage( 'Advertisement with this name already exists', 'error' );
			break;
		default:
			$frontend->RegisterMessage( 'Advertisement has been added' );
			break;
	}		
}

if ( @$_POST['delete'] )
{
	if ( adminAds::deleteTemplate( @$_POST['template_id'] ) )
		$frontend->RegisterMessage( 'Template has been deleted' );
	else 
		$frontend->RegisterMessage( 'Template has not been deleted', 'notice' );
}

if ( @$_POST['save'] )
{
	switch ( adminAds::saveTemplate( @$_POST['template_id'], @$_POST['ads_name'], @$_POST['ads_code'] ) )
	{
		case -1:
			$frontend->RegisterMessage( 'Undefined template', 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Ads name must be entered', 'error' );
			break;
		case -3:
			$frontend->RegisterMessage( 'Ads code must be entered', 'error' );
			break;
		case -4:
			$frontend->RegisterMessage( 'Advertisement with this name already exists', 'notice' );
			break;
		case 0:
			$frontend->RegisterMessage( 'Template has not been changed', 'notice' );
			break;
		default:
			$frontend->RegisterMessage( 'Template has been changed' );
			break;
	}
}

if ( @$_POST )
	redirect( $_SERVER['REQUEST_URI'] );

$all_templates = adminAds::getAllTemplates(); 
$frontend->assign_by_ref( 'all_templates', $all_templates);

$template = 'ads_template.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'form.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'ads_template.js' );

$_page['title'] = "Ads Templates";

// display template
$frontend->display( $template );
?>
