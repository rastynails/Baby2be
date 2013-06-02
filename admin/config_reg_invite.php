<?php

$file_key = 'config_reg_invite';
$active_tab = 'config_reg_invite';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );


$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );




if ( adminConfig::SaveConfigs($_POST) )
{
	$save_result = adminConfig::getResult(null, false);

	$frontend->registerMessage( 'Profile registration settings saved' );

	if(in_array('type', $save_result['validated']))
        {
            if ($_POST['type'] == 'invite')
                $frontend->registerMessage( 'Please remove Join button from site navigation', 'notice' );
        }

	redirect( $_SERVER['REQUEST_URI'] );
}

$template = 'config_reg_invite.html';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'form.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'config_reg_invite.js' );

$_page['title'] = "Registration / Invitation";

// display template
$frontend->display( $template );
?>
