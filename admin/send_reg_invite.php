<?php

$file_key = 'config_reg_invite';
$active_tab = 'send_reg_invite';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );


$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );


if ( @$_POST['send'] )
{
	$email_list = explode( ',', $_POST['email_list'] );
	
	if ( !$email_list[0] )
	{
		$frontend->registerMessage( 'Not any email was specified! Invitation was not sent', 'error' );
	}
	else 
	{
		foreach ( $email_list as $email )
			app_Invitation::addAdminRequestRegister( $email );
			
		$frontend->registerMessage( 'Invitations were sent!' );
	}
	
	redirect( URL_ADMIN.'send_reg_invite.php' );
}

$template = 'send_reg_invite.html';

$_page['title'] = 'Send Invitation';

// display template
$frontend->display( $template );
?>
