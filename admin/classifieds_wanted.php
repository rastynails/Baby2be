<?php

$file_key = 'classifieds';
$active_tab = 'classifieds_wanted';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

require_once( DIR_ADMIN_INC.'fnc.classifieds.php' );


$frontend = new AdminFrontend();

adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);

// require file with specific functions
require_once( 'inc.admin_menu.php' );

$groups = app_ClassifiedsGroupService::stGetItemGroups( 'wanted', 0 );

$sorted_groups = sortGroups( $groups );

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'opacity.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'classifieds.js' );

$frontend->registerOnloadJS("SK_Language.data['%interface.ok'] = " . json_encode('Ok') . ";");
$frontend->registerOnloadJS("SK_Language.data['%interface.cancel'] = " . json_encode('Cancel') . ";");

$frontend->registerOnloadJS("adminClassifieds.construct( " . json_encode($sorted_groups) . ", " . json_encode('wanted') . " );");

$frontend->assign_by_ref('groups', $sorted_groups);
$frontend->assign('section', 'cls_wanted');

$_page['title'] = "Wanted Classifieds";
$template = 'classifieds_wanted.html';

// display template
$frontend->display( $template );

?>
