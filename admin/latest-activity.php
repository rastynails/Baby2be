<?php

$file_key = 'latest-activity';
$active_tab = 'settings';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

$frontend = new AdminFrontend();
require_once( 'inc.admin_menu.php' );

adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

// register meta tags
$frontend->RegisterMetaTags( array( 'http_equiv' => 'pragma', 'content' => 'no-cache' ) );

$template = 'latest-activity.html';

$_page['title'] = "Latest Activity";

// display template
$frontend->display( $template );
?>
