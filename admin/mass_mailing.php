<?php

$file_key = 'mass_mailing';
$active_tab = 'mass_mailing';


require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

//require_once( DIR_ADMIN_INC.'fnc.design.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'fnc.mass_mailing.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'fnc.mail_templates.php' );

$frontend = new AdminFrontend();

$_page['title'] = 'Mass Mailing';

require_once( 'inc.admin_menu.php' );

if ( $_POST['save_template'] )
{
    if ( !$_POST['msg_subject'] || !$_POST['msg_text'] )
	{
		$frontend->registerMessage( 'Missing message subject or text', 'error' );
		SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
	}

    if( saveMailTemplate( $_POST['msg_subject'], $_POST['msg_text'] ) )
        $frontend->registerMessage('Template were save');
    else
		$frontend->registerMessage('Template were not saved','error');

    SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}
if ( $_POST['edit_template'] )
{
    if ( !$_POST['msg_subject'] || !$_POST['msg_text'] )
	{
		$frontend->registerMessage( 'Missing message subject or text', 'error' );
		SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
	}

    if( updateTemplate( $_POST['mail_template_id'], $_POST['msg_subject'], $_POST['msg_text'] ) )
        $frontend->registerMessage('Template were save');
    else
		$frontend->registerMessage('Template were not saved','error');

    SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}

if ( $_POST['send'] )
{
	$sand_as = $_POST['as_html'] == 'on' ? 'html' : 'text';

	if ( !$_POST['msg_subject'] || !$_POST['msg_text'] )
	{
		$frontend->registerMessage( 'Missing message subject or text', 'error' );
		SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
	}

	$profile_arr = getProfilesForSending();

	if ( !$profile_arr )
	{
		$frontend->registerMessage( 'No profiles found. Message not sent', 'notice' );
		SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
	}

	$_counter = 0;

	switch ( $_POST['msg_type'] )
	{
	    case 'external':

        	foreach ( $profile_arr as $_profile_info )
            {
                $unsubscribed = app_Unsubscribe::isProfileUnsubscribed($_profile_info['profile_id']);
                if ( $_POST['ignore_unsubscribe'] != 'on' && $unsubscribed )
                {
                    continue;
                }

                try {
                    $msg = app_Mail::createMessage()
                        ->setSenderEmail(SK_Config::section('site.official')->site_email_main)
                        ->setRecipientEmail($_profile_info['email'])
                        ->setRecipientProfileId($_profile_info['profile_id'])
                        ->setRecipientLangId($_profile_info['language_id'])
                        ->assignVar('profile_username', $_profile_info['username'])
                        ->assignVar('profile_email', $_profile_info['email'])
                        ->assignVar('unsubscribe_url', app_Unsubscribe::getUnsubscribeLink( $_profile_info['profile_id'] ));
                } catch (SK_EmailException $e) {}

                switch ($sand_as)
                {
                    case 'html':
                        $msg->setContent('', $_POST['msg_text']);
                        break;

                    case 'text':
                        $msg->setContent($_POST['msg_text']);
                        break;
                }

                $msg->setSubject($_POST['msg_subject']);
                if (app_Mail::send($msg)) {
                    $_counter ++;
                }
            }

            break;

	    case 'internal':

	        $config = SK_Config::section('site')->Section('official');

	        foreach ( $profile_arr as $_profile_info )
	        {
	            $unsubscribed = app_Unsubscribe::isProfileUnsubscribed($_profile_info['profile_id']);
                if ( $_POST['ignore_unsubscribe'] != 'on' && $unsubscribed )
                {
                    continue;
                }

	            // assign vars
                $assign = array(
                    'profile_username'  => app_Profile::username($_profile_info['profile_id']),
                    'profile_email'     => app_Profile::getFieldValues($_profile_info['profile_id'], 'email'),
                    'site_name'         => $config->site_name,
                    'site_url'          => SITE_URL,
                    'unsubscribe_url'   => app_Unsubscribe::getUnsubscribeLink($_profile_info['profile_id'])
                );

                $subject = SK_Language::exec($_POST['msg_subject'], $assign);
                $message = SK_Language::exec($_POST['msg_text'], $assign);

                if ( app_MailBox::sendSystemMessage($_profile_info['profile_id'], $subject, $message) > 0 )
                    $_counter++;
	        }

	        break;
	}

	$frontend->registerMessage( 'Message sent to <code>'.$_counter.'</code> profiles' );
	SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}

if ( $_POST['send_test_mail'] )
{
	$sand_as = $_POST['as_html'] == 'on' ? 'html' : 'text';

	$profile_name = trim( $_POST['profile_name'] );
	$_email = trim( $_POST['test_email'] );


	if ( !$_POST['msg_subject'] || !$_POST['msg_text'] )
	{
		$frontend->registerMessage( 'Missing message subject or text', 'error' );
		SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
	}

	if ( $_POST['msg_type'] == 'external' && !preg_match("/^([\w\-\.\+\%]+)@((?:[A-Za-z0-9\-]+\.)+[A-Za-z]{2,})$/i", $_email) )
	{
		$frontend->registerMessage('Incorrect Email address','error');
		SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
	}

	if ( $profile_name )
	{
		$_profile_id = intval( app_Profile::getProfileIdByUsername( $profile_name ) );
		if ( !$_profile_id )
		{
			$frontend->registerMessage("Profile $profile_name is not registered",'error');
			SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
		}

        switch ( $_POST['msg_type'] )
        {
            case 'external':

                $msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
                    ->setSenderEmail(SK_Config::section('site.official')->site_email_main)
                    ->setRecipientEmail($_email)
                    ->setRecipientProfileId($_profile_id)
                    ->setSubject($_POST['msg_subject'])
                    ->assignVar('unsubscribe_url', app_Unsubscribe::getUnsubscribeLink($_profile_id));

                switch ( $sand_as )
                {
                    case 'html':
                        $msg->setContent('', $_POST['msg_text']);
                        break;

                    case 'text':
                        $msg->setContent($_POST['msg_text']);
                        break;
                }
                $msg_sent = app_Mail::send($msg);

                break;

            case 'internal':

                $config = SK_Config::section('site')->Section('official');

                // assign vars
                $assign = array(
                    'profile_username'  => app_Profile::username($_profile_id),
                    'profile_email'     => app_Profile::getFieldValues($_profile_id, 'email'),
                    'site_name'         => $config->site_name,
                    'site_url'          => SITE_URL,
                    'unsubscribe_url'   => app_Unsubscribe::getUnsubscribeLink($_profile_id)
                );

                $subject = SK_Language::exec($_POST['msg_subject'], $assign);
                $message = SK_Language::exec($_POST['msg_text'], $assign);

                if ( app_MailBox::sendSystemMessage($_profile_id, $subject, $message) > 0 )
                    $msg_sent = true;

                break;
        }
        if ( $msg_sent )
            $frontend->registerMessage('Message was sent');
        else
            $frontend->registerMessage('Message was not sent', 'error');

	}
	else
		$frontend->registerMessage('Specify the profile name whose info will be assigned to template vars','error');


	SK_HttpRequest::redirect( $_SERVER['REQUEST_URI'] );
}


// get profile fields info
$birthdate_interval = explode( '-', SK_ProfileFields::get('birthdate')->custom );

$date_info = getdate();

$pr_fields = SK_ProfileFields::field_list();

$int_from = array_fill( $date_info['year'] - $birthdate_interval[1], $birthdate_interval[1] - $birthdate_interval[0] + 1, 0 );
$int_to = array_reverse( $int_from, true );


$membership_types = AdminMembership::GetAllMembershipTypes();

$frontend->assign_by_ref( 'membership_types', $membership_types );

$frontend->assign_by_ref( '_pr_fields', $pr_fields );
$frontend->assign_by_ref( 'int_from', $int_from );
$frontend->assign_by_ref( 'int_to', $int_to );

$assign_vars = array('profile_username','profile_email','unsubscribe_url');

$frontend->assign_by_ref('assign_vars', $assign_vars);
$frontend->IncludeJsFile( URL_ADMIN_JS.'mass_mailing.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'mail_templates.js' );

$template = 'mass_mailing.html';

// display template
$frontend->display( $template );
