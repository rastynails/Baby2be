<?php
$file_key = 'profile_message';
$active_tab = 'messages';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'fnc.profile_message.php' );

$frontend = new AdminFrontend( );

adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);

if( $_POST )
{
	if( $_POST['command'] == 'delete_spam' && is_array($_POST['messages']) )
	{
		$result = true;
		foreach ( $_POST['messages'] as $message )
		{
			$_sender_and_hash = explode( ',', $message );
			
			if( !deleteSpamMessagesBySenderAndHash( $_sender_and_hash[0], $_sender_and_hash[1] ) )
				$result = false;
		}
		if( $result )
			$frontend->registerMessage( 'Deleted' );
		else
			$frontend->registerMessage( 'Failed', 'error' );
	}
	elseif ( $_POST['command'] == 'approve_spam' && is_array($_POST['messages']) )
	{
		$result = true;
		foreach ( $_POST['messages'] as $message )
		{
			$_sender_and_hash = explode( ',', $message );
			
			if( !approveSpamMessageBySenderAndHash( $_sender_and_hash[0], $_sender_and_hash[1] ) )
				$result = false;
		}
		if( $result )
			$frontend->registerMessage( 'Approved' );
		else
			$frontend->registerMessage( 'Failed', 'error' );
	}
	
	redirect( URL_ADMIN.'profile_message.php' );
}

$keywords = getSpamKeywords();
$search_arr = array();
$replace_arr = array();
foreach ( $keywords as $value )
{
	$search_arr[] = '~'.preg_quote($value['keyword'], '~').'~is';
	$replace_arr[] = "<span style='color: red'><b>{$value['keyword']}</b></span>";
}

$spam_messages = getSpamMessages();
$i = 0; $previous_hash = '';
foreach ( $spam_messages as $key => $value )
{    
	if( $previous_hash != $value['hash'] || $previous_sender_id != $value['sender_id'] )
	{
		$i++;
		$ordered_spam[$i] = array(
			'id' => $value['sender_id'].','.$value['hash'],
			'sender_id' => $value['sender_id'],
            'text' => preg_replace($search_arr, $replace_arr, htmlspecialchars($value['text'])),
			'subject' => preg_replace($search_arr, $replace_arr, htmlspecialchars($value['subject'])),
			'time_stamp' => $value['time_stamp'],
			'sender_name' => $value['sender_name'],
			'count' => 0
		);
		
		$previous_sender_id = $value['sender_id'];
		$previous_hash = $value['hash'];
	}
	
	$ordered_spam[$i]['recipients'][] = array(
		'id' => $value['recipient_id'],
		'name' => $value['recipient_name'],
	);
	$ordered_spam[$i]['count']++;
}

$frontend->assign_by_ref( 'messages', $ordered_spam );
$frontend->register_function( 'profile_url', array( 'adminProfile', 'frontendGetProfileURL') );

require_once( 'inc.admin_menu.php' );

$_page['title'] = 'Messages Filter';

$frontend->display( 'profile_message.html' );
?>
