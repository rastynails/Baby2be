<?php

$file_key = 'forum';
$active_tab = 'settings';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

$frontend = new AdminFrontend();

adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);

// require file with specific functions
require_once( 'inc.admin_menu.php' );

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

$sections = adminConfig::getChildSections('forum');
array_unshift( $sections, 'forum');
$frontend->assign_by_ref('sections',$sections);

$_page['title'] = "Forum Settings";
$template = 'forum_settings.html';

// display template
$frontend->display( $template );

?>
