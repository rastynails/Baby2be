<?php

$file_key = 'services';
$active_tab = 'chat_services';

$active_chat = ( isset( $_GET['service'] ) && $_GET['service'] ) ? $_GET['service'] : 'chuppo';

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

$_page['title'] = 'Chats';

require_once( 'inc.admin_menu.php' );

switch ( $active_chat )
{
	case '123_chat':
	case 'chuppo':
		$template = $active_chat.'_service.html';
		break;
	default:
		$template = 'chuppo_service.html';
		break;		
}

if ( $active_chat == 'chuppo' )
{

	if ( $_POST['save_configs'] )
	{
		if ( $_POST['enable_chuppo_chat'] ) {;
			adminConfig::SaveConfig('flash_123_chat', 'enable_123_chat', 0);
		}		
		
		adminConfig::SaveConfigs($_POST);
		adminConfig::getResult($frontend);
			
		redirect( $_SERVER['REQUEST_URI'] );
	}

	if ( $_POST['add_gender'] )
	{
		if ( addChuppoGender( $_POST['sex'], $_POST['chuppo_gender'] ) )
			$frontend->registerMessage( 'Gender was added' );
		else 
			$frontend->registerMessage( 'Gender name is not specified!', 'error' );
			
		redirect( $_SERVER['REQUEST_URI'] );
	}
	
	if ( $_GET['delete_gender'] )
	{
		controlAdminGETActions();
		
		if ( deleteChuppoGender( $_GET['delete_gender'] ) )
			$frontend->registerMessage( 'Gender was deleted' );
		else 
			$frontend->registerMessage( 'Incorrect gender!' );
			
		redirect( URL_ADMIN.'chat_services.php?service=chuppo' );
	}
	
	$sex_list = AdminProfileField::getFieldValues( 6 );
	$lang_labels = SK_Language::section('profile_fields.value');
	
	foreach ($sex_list as $sex_info)	{
		$sex_labels[$sex_info['value']] = $lang_labels->text('sex_'.$sex_info['value']);
	}

	$frontend->assign_by_ref('sex_labels', $sex_labels);
	
	$gender_list = getChuppoGenderList();
	$frontend->assign_by_ref( 'chuppo_gender_list', $gender_list );
	
	foreach ( $sex_list as $_sex_key => $sex_info )
	{
		if ($gender_list) 
		{
			foreach ( $gender_list as $_gender_key => $gender_info )
			{
					if ( $sex_info['value'] == $gender_info['sex'] )
						unset( $sex_list[$_sex_key] );
			}
		}
	}
	
	$frontend->assign_by_ref( 'sex_list', $sex_list );
	
	$_config = adminConfig::ConfigList('chuppo');
	
	$frontend->assign_by_ref('_config', $_config);
	
	$frontend->IncludeJsFile( URL_ADMIN_JS.'services.js' );	
}

if ( $active_chat == '123_chat' )
{
	if ( $_POST['save_configs'] )
	{
		if ( $_POST['enable_123_chat'] ) {;
			adminConfig::SaveConfig('chuppo', 'enable_chuppo_chat', 0);
		}
		
		adminConfig::SaveConfigs($_POST);
		adminConfig::getResult($frontend);
		
		redirect( $_SERVER['REQUEST_URI'] );
	}
	
	$frontend->assign('section', 'flash_123_chat');
	
	$flash_url = URL_123CHAT.'admin_123flashchat.swf';
	
	$frontend->IncludeJsFile( URL_123CHAT.'123flashchat.js' );
	$frontend->assign( 'flash_url', $flash_url );
	
	$frontend->assign( 'enable_chat', file_exists( DIR_123CHAT.'admin_123flashchat.swf' ) );

	$frontend->assign( 'enable_123wm', file_exists( DIR_123WM.'123webmessenger.swf' ) );	
}

// display template
$frontend->display( $template );

?>