<?php

class IM_Model
{
	/**
	 * Get the timestamp to compare with session last ping.
	 *
	 * @return integer
	 */
	public static function dead_session_timestamp() {
		return time()-5*60;
	}
	
	/**
	 * Get the timestamp to compare with user last ping.
	 *
	 * @return integer
	 */
	public static function offline_user_timestamp() {
		return time()-6;
	}
	
	/**
	 * Get a private chat session.
	 *
	 * @param integer $opponent_id
	 * @return SKM_IMSession
	 */
	public static function getSession( $opponent_id ) {
		return new SKM_IMSession($opponent_id);
	}
	
	/**
	 * Get a private chat session.
	 *
	 * @param integer $opponent_id
	 * @return SKM_IMSession
	 */
	public static function getSessionID( $opponent_id ) {
		$sess = new SKM_IMSession($opponent_id);
		return $sess->im_session_id();
	}
	
	
	/**
	 * Add a message entries to a database.
	 */
	public static function addMessages(
		array $msg_entries, $im_session_id, $sender_id = null, $recipient_id = null
	) {
		$query = 'INSERT INTO `'.TBL_IM_MESSAGE.'`
					(`im_session_id`, `sender_id`, `recipient_id`, `text`, `timestamp`, `color`)
						VALUES ';
		
		$sql_values = array();
		foreach ( $msg_entries as $msg ) {
			$sql_values[] = '('.
				$im_session_id.', '.
				(isset($sender_id) ? $sender_id : 'NULL').', '.
				(isset($recipient_id) ? $recipient_id : 'NULL').', "'.
				SK_MySQL::realEscapeString($msg['text']).'", '.
				time().', "'.
				SK_MySQL::realEscapeString($msg['color']).'")';
		}

		if ( $sql_values ) {
			SK_MySQL::query($query . implode(', ', $sql_values));
		}
	}
	
	/**
	 * Get new messages.
	 */
	public static function getNewMessages( $im_session_id, $last_message_id = null )
	{
		if ( !is_numeric($im_session_id) || !(
			$im_session_id = (int)$im_session_id
		) ) {
			throw new Exception('invalid argument "$im_session_id"');
		}
		
		$profile_id = SKM_User::profile_id();
		
		if ( !isset($last_message_id) ) { // when the chatarea is blank
			$sql_condition = '`im_session_id`='.$im_session_id;
		}
		elseif ( is_numeric($last_message_id)
			&& ($last_message_id = (int)$last_message_id)
		) {
			$sql_condition =
				'`im_message_id`>'.$last_message_id.' AND `im_session_id`='.$im_session_id;
		}
		else {
			throw new Exception('invalid argument "$last_message_id"');
		}
		
		$result = SK_MySQL::query(
			'SELECT * FROM `'.TBL_IM_MESSAGE.'`
				WHERE '.$sql_condition.'
				ORDER BY `im_message_id`'
		);
		
		$messages = array();
		$read_msg_ids = array();
		while ( $row = $result->fetch_object() )
		{
			$time_format = (date('Ymd', $row->timestamp) == date('Ymd')) ? 'H:i:s' : 'F j, Y H:i:s';
			
			$messages[] =
				array(
					'im_message_id'	=> $row->im_message_id,
					'sender_id'		=> $row->sender_id,
                    'href'          => app_Profile::href($row->sender_id),
					'sender_name'	=> app_Profile::username($row->sender_id),
					'recipient_id'	=> $row->recipient_id,
					'text'			=> self::truncate_text( app_TextService::stHandleSmiles(SKM_Language::htmlspecialchars($row->text)) ),
					'format_time'	=> date($time_format, $row->timestamp),
					'color'			=> $row->color
				);
			
			if ( $row->recipient_id == $profile_id ) {
				$read_msg_ids[] = $row->im_message_id;
			}
		}
		
		if ( $read_msg_ids ) {
			SK_MySQL::query(
				'UPDATE `'.TBL_IM_MESSAGE.'` SET `read`=TRUE
					WHERE `im_message_id` IN ('.implode(', ', $read_msg_ids).')'
			);
		}
		
		return $messages;
	}
	
	
	public static function getNewInvitations( )
	{
		$profile_id = SKM_User::profile_id();
		
		$dead_sess_stamp = self::dead_session_timestamp();
		
		$get_sessions_query =
			'SELECT `im_session_id` FROM `'.TBL_IM_SESSION.'`
				WHERE (`opponent_id`='.$profile_id.' OR `opener_id`='.$profile_id.')
					AND (`opener_activity`>'.$dead_sess_stamp.'
						AND (`opponent_activity` IS NULL OR `opponent_activity`>'.$dead_sess_stamp.')
					)';
		
		$query =
			'SELECT * FROM `'.TBL_IM_MESSAGE.'`
				WHERE `im_session_id` IN ('.$get_sessions_query.')
					AND (`recipient_id`='.$profile_id.'
						AND `read`=FALSE
						AND `timestamp`<'.self::offline_user_timestamp().'
					)
				GROUP BY `im_session_id`';
		
		$result = SK_MySQL::query($query);
		
		$invitations = array();
		while ( $row = $result->fetch_object() )
		{
			$username = app_Profile::username($row->sender_id);
			$invitations[] = array(
				'im_session_id'	=> $row->im_session_id,
				'sender_id'		=> $row->sender_id,
				'link'			=> url::base() . 'im/'. app_Profile::username($row->sender_id),
				'title'			=> SKM_Language::text(
									'%components.im_listener.invitation_title'
									, array('username' => $username)),
				'message'		=> app_TextService::stHandleSmiles(SKM_Language::htmlspecialchars($row->text))
			);
		}
		return $invitations;
	}
	
	public function truncate_text( $string, $max_chars = 70 )
	{		
		$words = explode(' ', $string);
		
		foreach ($words as $word) {
			if ( strlen($word) <= $max_chars ) {
				continue;
			}
			$new_word = '';
			for ( $i=1; strlen($word)/$max_chars>=$i-1; $i++ ) {
				$new_word .= substr($word, ($i-1)*$max_chars, $max_chars).' ';
			}
			
			$string = str_replace($word, $new_word, $string);
		}
		
		return $string;
	}
	
}


class SKM_IMSession
{
	private $im_session_id;
	
	private $opener_id;
	
	private $opponent_id;
	
	private $start_stamp;
	
	private $opener_activity;
	
	private $opponent_activity;
	
	/**
	 * A part of SQL query WHERE expression for matching users.
	 *
	 * @var string
	 */
	private $sql_users_match;
	
	/**
	 * Constructor.
	 *
	 * @param integer $opponent_id
	 */
	public function __construct( $opponent_id )
	{
		$profile_id = SKM_User::profile_id();
		$opponent_id = (int)$opponent_id;
		
		$this->sql_users_match =
			'(`opener_id`='.$profile_id.' AND `opponent_id`='.$opponent_id.')
				OR (`opener_id`='.$opponent_id.' AND `opponent_id`='.$profile_id.')';
		
		$record = $this->fetch($opponent_id, $profile_id);
		
		if ( !$record ) {
			$this->create($opponent_id);
			$record = $this->fetch($opponent_id, $profile_id);
		}
		
		foreach ( $record as $property => $value ) {
			$this->$property = $value;
		}
	}
	
	/**
	 * Start a new instant messenger session.
	 *
	 * @param integer $opponent_id
	 * @throws SKM_IMSessionException
	 */
	private function create( $opponent_id )
	{
		//$service = new SK_ServiceUse('initiate_im_session');
		
		SK_MySQL::query('DELETE FROM `'.TBL_IM_SESSION.'` WHERE '.$this->sql_users_match);
		
		$opener_id = SKM_User::profile_id();
		
		$result = SK_MySQL::query(
			'INSERT INTO `'.TBL_IM_SESSION.'`
				SET `opener_id`='.$opener_id.',
					`opponent_id`='.$opponent_id.',
					`start_stamp`='.time().',
					`opener_activity`='.time()
		);
		
		//$service->trackServiceUse();
	}
	
	/**
	 * Get a session record from database.
	 *
	 * @return Object session row or NULL if not exists
	 */
	private function fetch( $opponent_id, $profile_id )
	{
		$dead_sess_stamp = app_IM::dead_session_timestamp();
		
		$result = SK_MySQL::query(
			'SELECT * FROM `'.TBL_IM_SESSION.'`
				WHERE ('.$this->sql_users_match.')
				AND (`opener_activity`>'.$dead_sess_stamp.'
					AND (`opponent_activity` IS NULL OR `opponent_activity`>'.$dead_sess_stamp.')
				)'
		);
		
		return $result->fetch_object();
	}
	
	/**
	 * Trace user ping.
	 */
	public function ping()
	{
		$pinger = (SKM_User::profile_id() == $this->opener_id) ? 'opener' : 'opponent';
		
		try {
			SK_MySQL::query("UPDATE `".TBL_IM_SESSION."`
				SET `{$pinger}_activity`=".time()."
				WHERE `im_session_id`=$this->im_session_id"
			);
		} catch ( SK_MySQL_Exception $e ) {
			throw new DebugException($this);
		}
	}
	
	/**
	 * The getter for $this->im_session_id.
	 */
	public function im_session_id() {
		return $this->im_session_id;
	}
	
	/**
	 * Adds a user message entries to a database.
	 */
	public function processMsgEntries( array $msg_entries )
	{
		$sender_id = SKM_User::profile_id();
		
		$recipient_id = ($sender_id == $this->opener_id) ? $this->opponent_id : $this->opener_id;
		
		app_IM::addMessages($msg_entries, $this->im_session_id, $sender_id, $recipient_id);
	}
	
}
