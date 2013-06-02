<?php
$file_key = 'ads';
$active_tab = 'template_set';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_ads.php' );
require_once( DIR_ADMIN_INC.'fnc.design.php' );
require_once( DIR_ADMIN_INC.'fnc.ads.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( @$_POST['create'] )
{
	//print_arr($_POST);	
	switch ( adminAds::createTemplateSet( @$_POST['set_name'], @$_POST['template'], @$_POST['country']) )
	{
		case -1:
			$frontend->RegisterMessage( 'Ads set name must be entered', 'error' );
			redirect(URL_ADMIN."ads_template_set_create.php" );
			break;
		case -2:
			$frontend->RegisterMessage( 'Ads set can not be empty', 'error' );
			redirect(URL_ADMIN."ads_template_set_create.php" );
			break;			
		case -3:
			$frontend->RegisterMessage( 'Advertisement set with this name already exists', 'error' );
			redirect(URL_ADMIN."ads_template_set_create.php" );
			break;
		default:
			$frontend->RegisterMessage( 'Advertisement set has been added' );
			redirect(URL_ADMIN."ads_template_set.php" );
			break;
	}		
}


if ( @$_POST['save'] )
{
	switch ( adminAds::saveTemplateSet( @$_POST['template_set_id'], @$_POST['set_name'], @$_POST['template'], @$_POST['country'] ) )
	{
		case -1:
			$frontend->RegisterMessage( 'Undefined template set', 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Ads set name must be entered', 'error' );
			redirect(URL_ADMIN."ads_template_set_create.php?set_id=".@$_POST['template_set_id'] );
			break;
		case -3:
			$frontend->RegisterMessage( 'Ads set can not be empty', 'error' );
			redirect(URL_ADMIN."ads_template_set_create.php?set_id=".@$_POST['template_set_id'] );
			break;			
		case -4:
			$frontend->RegisterMessage( 'Advertisement set with this name already exists', 'notice' );
			redirect(URL_ADMIN."ads_template_set_create.php?set_id=".@$_POST['template_set_id'] );
			break;
		case 0:
			$frontend->RegisterMessage( 'Template set has not been changed', 'notice' );
			redirect(URL_ADMIN."ads_template_set.php" );
			break;
		default:
			$frontend->RegisterMessage( 'Template set has been changed' );
			redirect(URL_ADMIN."ads_template_set.php" );
			break;
	}
}
/*
if ( @$_POST )
	redirect( $_SERVER['REQUEST_URI'] );*/
	
	
if ( ( int )@$_GET['set_id'] )
{
	$template_set = adminAds::getTemplateSet( @$_GET['set_id'] );
	if (!$template_set) 
	{
		$frontend->RegisterMessage( 'Undefined template set', 'error' );
		redirect(URL_ADMIN."ads_template_set_create.php" );
	}
	$frontend->assign_by_ref( 'template_set', $template_set );

}	

$all_countries = adminAds::getAllCountries();
$all_templates = adminAds::getAllTemplates();
$all_template_sets = adminAds::getAllTemplateSets();


$frontend->assign_by_ref( 'all_countries', $all_countries);
$frontend->assign_by_ref( 'all_templates', $all_templates);
$frontend->assign_by_ref( 'all_template_sets', $all_template_sets);



$template = 'ads_template_set_create.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'form.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'ads_template_set.js' );


$frontend->register_function("print_tmpls", 'frontendPrintTemplates');

$_page['title'] = "Ads Template Sets";

// display template
$frontend->display( $template );
?>
