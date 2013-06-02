<?php defined('SYSPATH') OR die('No direct access allowed.');

class Mailbox_Model extends Model {

	private $sender_id;
	
	private $recipient_id;
	
	const INITIATOR_FLAG = 1;
	const INTERLOCUTOR_FLAG = 2;
		
	public function __construct( $sender, $recipient )
	{
		parent::__construct();
		
		if ( !(int)$sender || !(int)$recipient )
			trigger_error('Mailbox Model: invalid "sender_id" or "recipient_id"', E_USER_WARNING);
		else 
		{		
			$this->sender_id = $sender;
			$this->recipient_id = $recipient;
		}
	}
	
	
	/**
	 * Start new conversation
	 * 
	 * @param string $subject
	 * 
	 * @return integer - new added conversation identificator in database
	 * 
	 */
	private function startConversation( $subject )
	{
		if ( !$this->sender_id || !$this->recipient_id || !strlen( trim( $subject ) ) )
			return -1;
		
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_MAILBOX_CONVERSATION."` 
			(`initiator_id`, `interlocutor_id`, `subject`, `bm_read`, `conversation_ts`) 
			VALUES ( ?, ?, '?', '1', ? )", $this->sender_id, $this->recipient_id, $subject, time() );
		
		$res = SK_MySQL::query( $query );
		$conversation_id = SK_MySQL::insert_id();
		
		return isset($conversation_id) ? $conversation_id : null;
	}
	
	/**
	 * Sends message to member's mailbox and starts conversation
	 *
	 * @param integer $sender_id
	 * @param integer $recipient_id
	 * @param string  $text
	 * @param string  $subject
	 * @param boolean $is_readable
	 * 
	 * @return integer:
	 * <ul>
	 * <li>  1 - successfully inserted</li>
	 * <li>  0 - was not inserted</li>
	 * <li>	-1 - sender or recipient not found</li>
	 * <li> -2 - attempt to send message to oneself</li>
	 * <li> -3 - could not start conversation</li>
	 * </ul>
	 */
	public function sendMessage( $text, $subject, $is_readable = false, $is_kiss = false )
	{
		if ( !isset($this->sender_id) || !isset($this->recipient_id))
			return -1;
			
		// check sending to oneself
		if ( $this->recipient_id == $this->sender_id )
			return -2;
		
		if ($this->sender_id != SKM_User::profile_id())
			return -1;

		$conversation_id = $this->startConversation( $subject );
		if ( $conversation_id == null )
			return -3;
		
		$readable = ( $is_readable )? 'yes' : 'no';
		
		$hash = $this->generateMessageHash( $this->sender_id, $subject, $text );
			
		#scan for scam keywords
		$status_q = ( SK_Config::section('mailbox')->section('spam_filter')->mailbox_message_filter && !$is_kiss && ($this->scanForScamKeyword( $text ) || $this->scanForSpam( $hash )) )? 'p' : 'a';
		$is_kiss = $is_kiss ? 'yes' : 'no';
		
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_MAILBOX_MESSAGE."` (`conversation_id`, `time_stamp`,`sender_id`,
			`recipient_id`,`text`, `is_kiss`, `is_readable`,`status`,`hash`) VALUES( ?, ?, ?, ?, '?', '?', '?', '?', '?' )", 
			$conversation_id,  time(), $this->sender_id, $this->recipient_id, $text, $is_kiss, $readable, $status_q, $hash );

		$res = SK_MySQL::query( $query );
					
		return SK_MySQL::affected_rows() ? ($status_q == 'a' ? 1 : 2) : 0;
	}
	
	/**
	 * Adds reply to the message in member's mailbox
	 *
	 * @param integer $conversation_id 
	 * @param integer $sender_id
	 * @param integer $recipient_id
	 * @param string  $text
	 * @param boolean $is_readable
	 * 
	 * @return integer:
	 * <ul>
	 * <li>  1 - successfully inserted</li>
	 * <li>  0 - was not inserted</li>
	 * <li>	-1 - sender or recipient not found</li>
	 * <li> -2 - attempt to send message to oneself</li>
	 * <li> -3 - conversation is alien</li>
	 * </ul>
	 */
	public function sendReplyMessage( $conversation_id, $text, $is_readable = false )
	{
		if ( !isset($this->sender_id) || !isset($this->recipient_id) || !isset($conversation_id))
			return -1;
			
		// check sending to oneself
		if ( $this->recipient_id == $this->sender_id )
			return -2;
		
		if ($this->sender_id != SKM_User::profile_id())
			return -1;
				
		//check owners of the conversation
		$owner_arr = implode(", ", array($this->sender_id, $this->recipient_id));
		$query = SK_MySQL::placeholder( "SELECT `conversation_id` FROM `".TBL_MAILBOX_CONVERSATION."` 
			WHERE `conversation_id`=? AND `initiator_id` IN ($owner_arr) 
			AND `interlocutor_id` IN ($owner_arr)", $conversation_id );
		$conv_check = SK_MySQL::query( $query )->fetch_cell();
		
		if (!isset($conv_check))
			return -3;
	
		$readable = ( $is_readable )? 'yes' : 'no';
		
		$hash = $this->generateMessageHash( $this->sender_id, '', $text );
			
		#scan for scam keywords
		$status_q = ( SK_Config::section('mailbox')->section('spam_filter')->mailbox_message_filter && ($this->scanForScamKeyword( $text ) || $this->scanForSpam( $hash )) )? 'p' : 'a';
		
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_MAILBOX_MESSAGE."` (`conversation_id`, `time_stamp`,`sender_id`,`recipient_id`,`text`,
			`is_readable`,`status`,`hash`) VALUES( ?, ?, ?, ?, '?', '?', '?', '?' )", 
			$conversation_id,  time(), $this->sender_id, $this->recipient_id, $text, $readable, $status_q, $hash );

		$res = SK_MySQL::query($query);
		$affected = SK_MySQL::affected_rows();
		$restored = $this->restoreConversation($conversation_id, $this->recipient_id);
		
		$this->markConversationsUnread(array($conversation_id), $this->recipient_id);

		return $affected ? ($status_q == 'a' ? 1 : 2) : 0;
	}
	
	/**
	 * Restore conversation (mark it undeleted for profile).
	 * 
	 * @param integer $conv_id
	 * @param integer $restore_for
	 * 
	 * @return boolean
	 */
	public function restoreConversation( $conv_id, $restore_for )
	{			
		$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MAILBOX_CONVERSATION."` 
			WHERE (`initiator_id`=? OR `interlocutor_id`=?) 
			AND `conversation_id`=?", $restore_for, $restore_for, $conv_id);

		$conv = MySQL::query($query)->fetch_assoc();
		if ( $conv )
		{
			if ( $conv['initiator_id'] == $restore_for )
				$mark_mask = self::INITIATOR_FLAG;
			elseif ( $conv['interlocutor_id'] == $restore_for )
				$mark_mask = self::INTERLOCUTOR_FLAG;
			
			$query_if = "(IF( `bm_deleted` IN (".$this->getBitMaskValues($mark_mask)."), ".$mark_mask.", 0))";
			$query = "UPDATE `".TBL_MAILBOX_CONVERSATION."` SET `bm_deleted`=`bm_deleted`-".$query_if." 
			WHERE `conversation_id`=".$conv_id;
			
			$res = SK_MySQL::query($query);
			if (SK_MySQL::affected_rows())
				return true;
		}
		return false;
	}
	
	/**
	 * Mark conversation(s) unread.
	 * 
	 * @param integer | array of integer $conversations_arr
	 * @param integer $mark_for
	 * 
	 * @return boolean:<br>
	 * <li> true : successfully marked read</li>
	 * <li> false : incorrect parameters passed</li>
	 */
	public function markConversationsUnread( $conversations_arr, $mark_for )
	{
		if ( !is_array( $conversations_arr ) || !count( $conversations_arr ) )
			return false;

		$marked = 0;
			
		foreach ( $conversations_arr as $conv_id )
		{
			$query = SK_MySQL::placeholder("SELECT * FROM `".TBL_MAILBOX_CONVERSATION."` 
				WHERE (`initiator_id`=? OR `interlocutor_id`=?) AND `conversation_id`=?", $mark_for, $mark_for, $conv_id);

			$conv = SK_MySQL::query($query)->fetch_assoc();
			if ( $conv )
			{
				if ( $conv['initiator_id'] == $mark_for )
					$mark_mask = self::INITIATOR_FLAG;
				elseif ( $conv['interlocutor_id'] == $mark_for )
					$mark_mask = self::INTERLOCUTOR_FLAG;
				
				$query_if = "(IF( `bm_read` IN (".$this->getBitMaskValues($mark_mask)."), ".$mark_mask.", 0))";
				$query = "UPDATE `".TBL_MAILBOX_CONVERSATION."` SET `bm_read`=`bm_read`-".$query_if." WHERE `conversation_id`=".$conv_id;

				$res = SK_MySQL::query($query);
				$marked += SK_MySQL::affected_rows(); 
			}
		}
		
		return $marked ? true : false;
	}
	
	/**
	 * Get array of values that make up mask.
	 * 
	 * @param integer $mask
	 * @param boolean $string_presentation
	 * @return string|array
	 */
	private function getBitMaskValues( $mask, $string_presentation = true )
	{
		$available_mask_arr = array(self::INITIATOR_FLAG, self::INTERLOCUTOR_FLAG);
		
		if ( in_array( $mask, $available_mask_arr ) )
		{
			$values_count = pow( 2, 2 );
			if ( $string_presentation )
			{
				$return_arr = "";
				for ( $i = 1; $i < $values_count; $i++ )
					if ( ($mask & $i) == $mask )
						$return_arr .= $i.", ";
						
				return substr( $return_arr, 0, -2 );
			}
			else 
			{
				$return_arr = array();
				for ( $i = 1; $i < $values_count; $i++ )
					if ( ($mask & $i) == $mask )
						$return_arr[$i] = $i;
				return $return_arr;
			}
		}
	}
	
	/**
	 * Returns message md5 hash.
	 *
	 * @param integer $profile_id
	 * @param string $message
	 * @return string
	 */
	private function generateMessageHash( $profile_id, $subject, $message )
	{
		if (!strlen($subject)) // means it is not the first message
			$subject = md5(time());
			
		$slug = preg_replace('~\s+~', '', "$profile_id-".strtolower(trim($subject).trim($message)));
		$charsonly = preg_replace('~[^\w]+~', '', $slug);
		
		$hash = md5($charsonly);

		return $hash;
	}
	
	/**
	 * Scans a message for scam keywords. Returns true if the message contains scam words.
	 *
	 * @param string $text
	 * @return boolean
	 */
	private function scanForScamKeyword( $text )
	{
		$scan_text = $text;
		$result = SK_MySQL::query( "SELECT `keyword` FROM `".TBL_MAILBOX_SCAM_KEYWORD."` WHERE 1" );
				 
		while ( $keyword = $result->fetch_assoc() )
			if ( stristr( $scan_text, $keyword['keyword'] ) !== false )
				return true;
		
		return false;
	}
	
	/**
	 * Scans a message using hash. Returns true if the message is a spam.
	 *
	 * @param string $hash
	 * @return boolean
	 */
	private function scanForSpam( $message_hash )
	{
		$query = SK_MySQL::placeholder( "SELECT `hash` FROM `".TBL_MAILBOX_MESSAGE."` 
			WHERE `hash`='?' LIMIT 1", $message_hash );
		return SK_MySQL::query( $query )->fetch_cell() ? true : false;
	}
		
}