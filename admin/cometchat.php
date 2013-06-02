<?php

$file_key = 'services';
$active_tab = 'cometchat';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );
require_once( DIR_ADMIN_INC.'fnc.classifieds.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );


$adminUrl = SITE_URL . 'cometchat/admin/';
$frontend->assign('adminUrl', $adminUrl);

$_page['title'] = "Comet Chat";

$frontend->display( 'cometchat.html' );
