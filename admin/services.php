<?php

$file_key = 'services';
$active_tab = ( isset( $_GET['service'] ) && $_GET['service'] ) ? $_GET['service'] : 'autocrop';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile_field.php' );

// requiring applications
require_once( DIR_ADMIN_INC.'fnc.service.php' );
require_once( DIR_ADMIN_INC.'fnc.chat.php' );

$frontend = new AdminFrontend();

$_page['title'] = 'Services';

require_once( 'inc.admin_menu.php' );

switch ( $active_tab )
{
	case 'autocrop':
	case 'chuppo':
	case '123flashchat':
	case 'facebook':
		$template = $active_tab.'_service.html';
		break;
	default:
		$template = 'autcrop_service.html';
		break;		
}

if($active_tab)
{

	if(!empty($_POST['command']) && $_POST['command'] == 'fb_invite_settings')
	{	
		SK_Config::section('fb_invite')->set('appId', $_POST['invite']['appId']);
		SK_Config::section('fb_invite')->set('secret', $_POST['invite']['secret']);
		
		$frontend->registerMessage('Updated');
		
		redirect($_SERVER['REQUEST_URI']);	
	}
	
	$frontend->assign('fbi_appId', SK_Config::section('fb_invite')->appId);
	$frontend->assign('fbi_secret', SK_Config::section('fb_invite')->secret);
}

if ( $active_tab == 'autocrop' )
{
	$config_section = SK_Config::section("services")->Section("autocrop");
	if ( $_POST['save'] )
	{
		$_service_config_value = ( $_POST['enable_autocrop_service'] ) ? 1 : 0;
		
		if ( $_service_config_value && !isAutoCropAllow( $_POST['autocrop_username'], $_POST['autocrop_password'] ) )
		{
			$frontend->registerMessage( 'The username or password you entered is incorrect. Please enter valid details, or register an account.', 'notice' );
			$_service_config_value = 0;
		}
		
		$config_section->set("enabled", $_service_config_value);
		$config_section->set("username", $_POST['autocrop_username']);
		$config_section->set("password", $_POST['autocrop_password']);
				
		if ( $_service_config_value )
			$frontend->registerMessage( 'Autocrop Service has been activated. Please make sure you have paid to enable this add-on' );
		else 
			$frontend->registerMessage( 'Service Autocrop was disabled', 'notice' );
			
		redirect( $_SERVER['REQUEST_URI'] );
	}
	
	if ( $_POST['view_stat'] )
	{
		$_stamp_info = calculateAutocropStatTimeStamp( $_POST['start_date'], $_POST['end_date'] );
		$_stat_info = getAutoCropStat( $config_section->username, $config_section->password, $_stamp_info['start_stamp'], $_stamp_info['end_stamp'] );
		
		if ( $_stat_info )
		{
			$frontend->assign( '_stat_info', $_stat_info );
			$frontend->assign_by_ref( '_stat_info_start_stamp', $_stamp_info['start_stamp'] );
			$frontend->assign_by_ref( '_stat_info_end_stamp', $_stamp_info['end_stamp'] );
		}
		else 
		{
			$frontend->registerMessage( 'There is no stat information for specified period', 'notice' );
			redirect( $_SERVER['REQUEST_URI'] );
		}
	}
	
	$_config['enable_autocrop_service'] = $config_section->enabled;
	$_config['autocrop_username'] = $config_section->username;
	$_config['autocrop_password'] = $config_section->password;
}

$frontend->assign_by_ref('_config', $_config);

$frontend->IncludeJsFile( URL_ADMIN_JS.'services.js' );

// display template
$frontend->display( $template );

?>