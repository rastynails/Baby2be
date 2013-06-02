<?php

$file_key = 'services';
$active_tab = 'mobile';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

$frontend = new AdminFrontend();

$_page['title'] = 'Services';

require_once( 'inc.admin_menu.php' );

$dir = DIR_SITE_ROOT . SK_Config::section('mobile')->get('mobile_directory') . DIRECTORY_SEPARATOR;
$conf_file = $dir . 'mconfig.php';

if (file_exists($conf_file) )
{
	require_once $conf_file;
	$frontend->assign('version', MOBILE_EDITION_VERSION);
}

if ( $_POST['save_configs'] )
{
	if ( adminConfig::SaveConfigs( $_POST ) )
		$frontend->registerMessage( 'Settings were changed' );
	else
		$frontend->registerMessage( 'Settings were not changed', 'notice' );
		
	redirect( $_SERVER['REQUEST_URI'] );
}


$frontend->assign('mobile_configs', adminConfig::ConfigList('mobile'));

$_page['title'] = "Mobile Edition";

$frontend->display( 'mobile.html');
