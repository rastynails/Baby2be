<?php
$file_key = 'ads';
$active_tab = 'template_set';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

require_once( DIR_ADMIN_INC.'class.admin_ads.php' );
require_once( DIR_ADMIN_INC.'fnc.design.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( @$_POST['delete'] )
{
	if ( adminAds::deleteTemplateSet( @$_POST['template_set_id'] ) )
		$frontend->RegisterMessage( 'Template set has been deleted' );
	else 
		$frontend->RegisterMessage( 'Template set has not been deleted', 'notice' );
}

if ( @$_POST )
	redirect( $_SERVER['REQUEST_URI'] );

$all_countries = app_Location::Countries();
$all_templates = adminAds::getAllTemplates();
$all_template_sets = adminAds::getAllTemplateSets();

$frontend->assign_by_ref( 'all_countries', $all_countries);
$frontend->assign_by_ref( 'all_template_sets', $all_template_sets);

$template = 'ads_template_set.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'form.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'ads_template_set.js' );

$_page['title'] = "Ads Template Sets";

// display template
$frontend->display( $template );
?>
