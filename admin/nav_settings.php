<?php
$file_key	= 'navigation';
$active_tab	= 'nav_settings';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_navigation.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

$frontend = new AdminFrontend();

$_admin_config = new adminConfig();

// require file with specific functions
require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC.'fnc.navigation.php' );

$nav_doc = new AdminNavigation();
$_admin_config->SaveConfigs( @$_POST );
$_admin_config->getResult($frontend);
	

// Generate Output
$all_documents = $nav_doc->GetAllDocuments();

// get configs
$nav_configs = $_admin_config->ConfigList('navigation.settings');

foreach ( $all_documents as $_key => $_document )
{
	$nav_configs['signout_document_redirect']['values'][$_key]['value'] = $_document['document_key'];
	$nav_configs['signout_document_redirect']['values'][$_key]['label'] = $_document['url'];
	$nav_configs['join_document_redirect']['values'][$_key]['value'] = $_document['document_key'];
	$nav_configs['join_document_redirect']['values'][$_key]['label'] = $_document['url'];
        $nav_configs['signin_document_redirect']['values'][$_key]['value'] = $_document['document_key'];
        $nav_configs['signin_document_redirect']['values'][$_key]['label'] = $_document['url'];
}

$frontend->assign_by_ref( 'nav_configs', $nav_configs );

$_page['title'] = "Navigation Settings";

$template = 'nav_settings.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

// display template
$frontend->display( $template );
?>
