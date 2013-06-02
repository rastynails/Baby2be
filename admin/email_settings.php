<?php

$file_key = 'email_settings';
$active_tab = 'email_settings';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

if ( isset($_GET['testConnection']) )
{
    try
    {
        @app_Mail::testSMTPConnect();
    }
    catch ( Exception $e )
    {
        exit ($e->getMessage());
    }

    exit ('SMTP is configured properly');
}

$frontend = new AdminFrontend();

adminConfig::SaveConfigs($_POST);

adminConfig::getResult($frontend);

// require file with specific functions
require_once( 'inc.admin_menu.php' );

$_page['title'] = "Email Settings";

$frontend->assign('showTestConnection', SK_Config::section('email.smtp')->enable);

// display template
$frontend->display( 'email_settings.html' );
