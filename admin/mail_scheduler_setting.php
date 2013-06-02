<?php

$file_key = 'mail_scheduler';
$active_tab = 'mail_setting';

require_once( '../internals/Header.inc.php' );
require_once( DIR_ADMIN_INC.'fnc.auth.php' );
require_once( DIR_ADMIN_INC.'fnc.admin.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

require_once( DIR_ADMIN_INC.'fnc.scheduler.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

$admin_config = new adminConfig();

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( $_POST['save_config'] )
{
	$cron_config_value = app_ActivityScheduler::implodeTimestampConfig($_POST);
	if ( !$cron_config_value )
	{
		$frontend->registerMessage( 'Inserted values are incorrect!', 'error' );
		redirect( $_SERVER['REQUEST_URI'] );
	}
	
	adminConfig::SaveConfig('scheduler', 'mail_scheduler_time', $cron_config_value);
    $next = app_ActivityScheduler::calculateNextRunTimestamp();
    app_ActivityScheduler::setNextRunTimestamp($next);
	$frontend->registerMessage( 'Scheduler run time was changed' );
		
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( $_POST['save_configs'] )
{
	if ( adminConfig::SaveConfigs( $_POST ) )
		$frontend->registerMessage( 'Settings were changed' );
	else
		$frontend->registerMessage( 'Settings were not changed', 'notice' );
		
	redirect( $_SERVER['REQUEST_URI'] );
}

$frontend->assign( 'time_config', app_ActivityScheduler::explodeTimestampConfig(SK_Config::section('scheduler')->get('mail_scheduler_time')) );

$frontend->assign( 'match_list_config', adminConfig::ConfigList('scheduler.match_list') );

$frontend->assign('current_time', date("D M j, Y, G:i", time()));
$frontend->assign('run_time', date("D M j, Y, G:i", SK_Config::section('scheduler')->get('next_run_time')));

$template = 'mail_scheduler_setting.html';

$_page['title'] = "Scheduler Settings";

// display template
$frontend->display( $template );
