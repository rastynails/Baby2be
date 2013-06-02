<?php

/************class for push notification********/

class Push
{
	public function sendPush($id)
	{
		$essence	=	new Essentials();
		$message_tbl			=	$essence->tblPrefix().'mailbox_message';
		$conversation_tbl		=	$essence->tblPrefix().'mailbox_conversation';
		$push_token_tbl			=	$essence->tblPrefix().'push_device_token';
		$im_message_tbl			=	$essence->tblPrefix().'im_message';
		$db 		= 	new MySQL(true, $essence->getDbName(), $essence->getDbHost(), $essence->getDbUser(), $essence->getDbPass());

		//checking for device token
                
                $sql0 = "SELECT device_token FROM $push_token_tbl WHERE profile_id='$id'";
		$sql0Exe = $db->Query($sql0);
		$sql0ExeRes = mysql_fetch_array($sql0Exe);
		$sql0ExeResult = $sql0ExeRes['device_token'];
		//check mail count
		$sqlMail="SELECT count(*) FROM $message_tbl msg 
                       LEFT JOIN $conversation_tbl con ON (con.conversation_id=msg.conversation_id and msg.time_stamp=con.conversation_ts) WHERE 			       recipient_id='$id'";
		$sqlMailExe =$db->Query($sqlMail);
		$sqlMailRes =mysql_fetch_array($sqlMailExe);
		$sqlMailCount = $sqlMailRes['count(*)'];
               
		//check chat count
		$sqlChat ="SELECT count(*) FROM $im_message_tbl WHERE recipient_id='$id' and read=0";
		$sqlChatExe = $db->Query($sqlChat);
		$sqlChatRes = mysql_fetch_array($sqlChatExe);
		$sqlChatCount = $sqlChatRes['count(*)'];

//sending push notification
$deviceToken = $sql0ExeResult;

// Put your private key's passphrase here:
$passphrase = 'sodtech123#';

		
	
        }	

	
}
?>
