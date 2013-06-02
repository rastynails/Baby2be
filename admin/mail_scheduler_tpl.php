<?php

$file_key = 'mail_scheduler';
$active_tab = 'mail_template';

require_once( '../internals/Header.inc.php' );
require_once( DIR_ADMIN_INC.'fnc.auth.php' );
require_once( DIR_ADMIN_INC.'fnc.admin.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

$display_mode = ( $_GET['create'] ) ? 'create_tpl' : 'list_tpl';

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

if ( $_POST['create_mail'] )
{
	switch ( app_ActivityScheduler::createMailTemplate($_POST['mail_name'], $_POST['mail_subject'], $_POST['mail_body_txt'], $_POST['mail_body_html']) )
	{
		case -1:
			$frontend->registerMessage( 'Incorrect mail label', 'error' );
			break;
		case -2:
			$frontend->registerMessage( 'Empty mail subject', 'error' );
			break;
		case -3:
			$frontend->registerMessage( 'Empty mail body txt', 'error' );
			break;
		case -4:
			$frontend->registerMessage( 'Empty mail body html', 'error' );
			break;
		case -5:
			$frontend->registerMessage( 'Mail with same label already exists', 'error' );
			break;
		default:
			$frontend->registerMessage( 'Mail template was successfully created' );
			break;
	}
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( $_POST['delete_mail'] )
{
	if ( app_ActivityScheduler::deleteMailTemplate($_POST['mail_name']) )
		$frontend->registerMessage( 'Mail template was deleted successfully' );
	else
		$frontend->registerMessage( 'Mail template can not be delete while it used by Scheduler', 'notice' );

	redirect( $_SERVER['REQUEST_URI'] );
}

if ( $_POST['send_test_mail'] )
{
	$mail_name = trim( $_POST['mail_name'] );
	$profile_name = trim( $_POST['profile_name'] );

	$_email = trim( $_POST['test_email'] );
	if( !preg_match("/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_-]+?.[a-zA-Z0-9_]{2,}(\.\w{2})?$/i",$_email) )
	{
		$frontend->registerMessage('Incorrect Email address','error');
		redirect( $_SERVER['REQUEST_URI'] );
	}

	$tpl_name = 'scheduler_'.$mail_name;
	if( $profile_name ) {

		$_profile_id = intval( app_Profile::getProfileIdByUsername( $profile_name ) );

		if( !$_profile_id ) {
			$frontend->registerMessage("Profile $profile_name is not registered",'error');
			redirect( $_SERVER['REQUEST_URI'] );
		}

		if ($mail_name == 'matches' || $mail_name == 'send_matches') {
			$new_matches_only = SK_Config::section('scheduler')->Section('match_list')->get('new_matches') ? true : false;
			$match_count = app_ProfileList::CountProfileMatch( $_profile_id , $new_matches_only );

			if( intval($match_count)) {
				$match_list_info = app_ProfileList::ProfileMatcheList(
					$_profile_id ,
					SK_Config::section('scheduler')->Section('match_list')->get('scheduler_mlist_profile_count'),
					SK_Config::section('scheduler')->Section('match_list')->get('scheduler_mlist_is_random'),
					$new_matches_only
				);
				$_assign_vars = array();
				$_assign_vars['profile_match_list_html'] = app_ActivityScheduler::generateMatchlistTemplate($match_list_info['profiles']);
				$_assign_vars['profile_match_list_txt'] = strip_tags(app_ActivityScheduler::generateMatchlistTemplate($match_list_info['profiles'], 'txt'));

				$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
						->setTpl($tpl_name)
						->setRecipientProfileId($_profile_id)
						->setRecipientEmail($_email)
						->assignVarRange($_assign_vars)
						->assignVar('unsubscribe_url', app_Unsubscribe::getUnsubscribeLink( $_profile_id ));
				$_result = app_Mail::send($msg);

				if( $_result )
					$frontend->registerMessage('Message was sent');
				else
					$frontend->registerMessage('Message was not sent','error');
			} else
				$frontend->registerMessage('Profile has no matches. Message was not sent', 'notice');
		}
		else {

			$msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
						->setTpl($tpl_name)
						->setRecipientProfileId($_profile_id)
						->setRecipientEmail($_email)
						->assignVar('unsubscribe_url', app_Unsubscribe::getUnsubscribeLink( $_profile_id ));
			$_result = app_Mail::send($msg);

			if( $_result )
				$frontend->registerMessage('Message was sent');
			else
				$frontend->registerMessage('Message was not sent','error');
		}
	}
	else
		$frontend->registerMessage('Specify the profile name whose info will be assigned to template vars','error');


	redirect( $_SERVER['REQUEST_URI'] );
}

if ( $display_mode == 'list_tpl' )
{
	$frontend->assign( 'mail_list', app_ActivityScheduler::getMailTemplates() );
}

$template = ( $display_mode == 'create_tpl' ) ? 'mail_scheduler_tpl_create.html' : 'mail_scheduler_tpl_list.html';

$_page['title'] = "Scheduler Templates";

// display template
$frontend->display( $template );
