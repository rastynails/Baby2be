<?php
require_once(DIR_APPS.'MailBox.app.php');

function getSpamMessages()
{
	return MySQL::fetchArray( "SELECT `m`.`sender_id`,`m`.`recipient_id`, `m`.`conversation_id`, `m`.`time_stamp`,
		`m`.`text`,`m`.`hash`,`p1`.`username` AS `sender_name`,`p2`.`username` AS `recipient_name`, 
		`conv`.`conversation_id`, `conv`.`subject`
		FROM `".TBL_MAILBOX_MESSAGE."` AS `m`
		LEFT JOIN `".TBL_PROFILE."` AS `p1` ON(`sender_id`=`p1`.`profile_id`)
		LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`recipient_id`=`p2`.`profile_id`)
		LEFT JOIN `".TBL_MAILBOX_CONVERSATION."` AS `conv` ON(`m`.`conversation_id`=`conv`.`conversation_id`)
		WHERE `m`.`status`='p' AND `m`.`sender_id` !=0 ORDER BY `m`.`sender_id`,`m`.`hash`" );
}

function getSpamKeywords()
{
	return MySQL::fetchArray( "SELECT * FROM `".TBL_MAILBOX_SCAM_KEYWORD."` WHERE 1" );
}

function deleteSpamMessagesBySenderAndHash( $sender_id, $hash )
{
	if ( !intval($sender_id) || !strlen($hash) )
		return false;
	
	return MySQL::affectedRows( sql_placeholder("DELETE FROM `?#TBL_MAILBOX_MESSAGE` WHERE `sender_id`=? AND `hash`=? AND `status`='p'", $sender_id, $hash) ) ? true : false;
}

function approveSpamMessageBySenderAndHash( $sender_id, $hash )
{
	if ( !intval($sender_id) || !strlen($hash) )
		return false;
	
	$messages = MySQL::fetchArray(sql_placeholder("SELECT `conversation_id`, `recipient_id` FROM `?#TBL_MAILBOX_MESSAGE` 
		WHERE `sender_id`=? AND `hash`=? AND `status`='p'", $sender_id, $hash));
	
	foreach ( $messages as $message )
	{
		app_MailBox::sendEmailNotification($message['recipient_id'], $sender_id, 'message');
		app_MailBox::restoreConversation($message['conversation_id'], $message['recipient_id']);
		app_MailBox::markConversationsUnread(array($message['conversation_id']), $message['recipient_id']);
	}
    
	return MySQL::affectedRows( sql_placeholder("UPDATE `?#TBL_MAILBOX_MESSAGE` SET `status`='a' 
		WHERE `sender_id`=? AND `hash`=?", $sender_id, $hash) ) ? true : false;
}

function deleteMessageScamKeyword( $keyword_id )
{
	return MySQL::affectedRows( sql_placeholder("DELETE FROM `?#TBL_MAILBOX_SCAM_KEYWORD` WHERE `keyword_id`=?", $keyword_id) )? true : false;
}

function editMessageScamKeyword( $keyword_id, $keyword )
{
	if( !intval($keyword_id) || !strlen(trim($keyword)) )
		return false;
	
	return MySQL::affectedRows( sql_placeholder("UPDATE `?#TBL_MAILBOX_SCAM_KEYWORD` SET `keyword`=? WHERE `keyword_id`=?", $keyword, $keyword_id) ) ? true : false;
}

function addMessageScamKeyword( $keyword )
{
	if( !strlen(trim($keyword)) )
		return false;
	
	$query = SK_MySQL::placeholder("SELECT `keyword` FROM `".TBL_MAILBOX_SCAM_KEYWORD."` WHERE `keyword`='?'", $keyword);
	$res = SK_MySQL::query($query);
	
	if ( $res->num_rows() )
	{
	    return false;
	}
	
	return MySQL::affectedRows( sql_placeholder("INSERT INTO `?#TBL_MAILBOX_SCAM_KEYWORD` (`keyword`) VALUES(?)", $keyword) ) ? true : false;
}
?>