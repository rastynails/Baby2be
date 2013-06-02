<?php
$file_key = 'user_points';
$active_tab = 'earn_credits';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.user_points.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( isset( $_POST['active'] ) )
{
    $res = UserPoints::updateActions($_POST['active'], $_POST['credits'], $_POST['notify']);
    if ( $res )
        $frontend->RegisterMessage( 'Settings updated' );
    else
        $frontend->RegisterMessage( 'Update error', 'notice' );
        
    redirect( $_SERVER['REQUEST_URI'] );
}

$actions = UserPoints::getActionsList();
$frontend->assign('actions', $actions);

$template = 'earn_credits.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

$_page['title'] = "Earning Credits";

// display template
$frontend->display( $template );
