<?php
$file_key = 'ads';
$active_tab = 'binding';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_ads.php' );
require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );
require_once( DIR_ADMIN_INC.'fnc.design.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC.'fnc.ads.php' );

$_admin_config = new adminConfig();

if ( @$_POST['set_template'] )
{
	if ( is_array( @$_POST['pos_arr'] ) )
	{
		foreach ( @$_POST['pos_arr'] as $document_key => $pos_arr )
		{
			adminAds::setAdsTemplateSetToDoc( $document_key, $pos_arr, @$_POST['template_set_id'] );
		}
		$frontend->RegisterMessage( 'Templates have been set' );
	}
	else 
		$frontend->RegisterMessage( 'Templates have not been set', 'notice' );
}

if ( @$_POST['empty'] )
{
	if ( is_array( @$_POST['pos_arr'] ) )
	{
		foreach ( @$_POST['pos_arr'] as $document_key => $pos_arr )
		{
			adminAds::setAdsTemplateSetToDoc( $document_key, $pos_arr );
		}
		$frontend->RegisterMessage( 'Positions have been cleared' );
	}
	else 
		$frontend->RegisterMessage( 'Positions have not been cleared', 'notice' );
}

/*
adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);

if ( @$_POST['save_config'] )
{
	if ( $_admin_config->SaveOneConfig( 'profile_list_ads_num_display', @$_POST['profile_list_ads_num_display'] ) )
		$frontend->RegisterMessage( 'Configuration changed' );
	else 
		$frontend->RegisterMessage( 'Configuration not changed', 'notice' );
}
*/
if ( @$_POST )
	redirect( $_SERVER['REQUEST_URI'] );

//$doc_structure = adminAds::getAdsPosInfo();
//$frontend->assign_by_ref( 'doc_structure', $doc_structure );

$doc_structure = adminAds::getAdsSetPosInfo();
$frontend->assign_by_ref( 'doc_structure', $doc_structure );


$positions = adminAds::getAdsPositions();
$frontend->assign_by_ref( 'positions', $positions );

//$all_templates = adminAds::getAllTemplates(); 
//$frontend->assign_by_ref( 'all_templates', $all_templates);

$all_template_sets = adminAds::getAllTemplateSets();
$frontend->assign_by_ref( 'all_template_sets', $all_template_sets);

$template = 'ads_binding.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'form.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'ads_template.js' );

$frontend->register_function( 'print_doc', 'frontendPrintDocuments' );

$_page['title'] = "Advertisement";

// display template
$frontend->display( $template );
?>
