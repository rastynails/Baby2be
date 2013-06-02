<?php

class app_Chat
{
	/**
	 * Get the offline timestamp threshold.
	 *
	 * @return integer
	 */
	private static function getOfflineStamp() {
		return time() - 6;
	}


	private static function getRoomsIDIndex( $initial_value = null, $unite_inactive = false )
	{
		$query =
			"SELECT `chat_room_id` FROM `".TBL_CHAT_ROOM."`".
				(!$unite_inactive ? 'WHERE `active`=TRUE' : '');

		$result = SK_MySQL::query($query);

		$rooms = array();
		while ( $room_id = $result->fetch_cell() ) {
			$rooms[$room_id] = $initial_value;
		}

		return $rooms;
	}

	/**
	 * Get chat rooms list.
	 *
	 * @param boolean $unite_inactive
	 * @return array object list of SK_ChatRoom
	 */
	public static function getRooms( $unite_inactive = false )
	{
		$query = 'SELECT * FROM `'.TBL_CHAT_ROOM.'`';

		if ( !$unite_inactive ) {
			$query .= ' WHERE `active`=TRUE';
		}

		$result = SK_MySQL::query($query);

		$rooms = array();
		while ( $room = $result->fetch_object('SK_ChatRoom') ) {
			$room->construct();
			$rooms[] = $room;
		}

		return $rooms;
	}


	public static function getCountUsersInRooms()
	{
		$offline_stamp = self::getOfflineStamp();

		$result = SK_MySQL::query(
			"SELECT `chat_room_id`, COUNT(`profile_id`)
				FROM `".TBL_CHAT_USER."`
				WHERE `ping_stamp`>$offline_stamp
				GROUP BY `chat_room_id`"
		);

		$count_index = self::getRoomsIDIndex(0);
		while ( $row = $result->fetch_array(MYSQL_NUM) ) {
			$count_index[$row[0]] = $row[1];
		}

		return $count_index;
	}

	/**
	 * Get a chat room users.
	 *
	 * @param integer $room_id
	 */
	public static function getRoomUsers( $room_id )
	{
		$room_id = (int)$room_id;

		$offline_stamp = self::getOfflineStamp();

		$result = SK_MySQL::query(
			"SELECT * FROM `".TBL_CHAT_USER."`
				WHERE `chat_room_id`=$room_id
					AND `ping_stamp`>$offline_stamp"
		);

		$users = array();
		while ( $user = $result->fetch_object('SK_ChatUser') ) {
			$user->construct();
			$users[] = $user;
		}

		return $users;
	}

	/**
	 * Get a chat room users hash code.
	 *
	 * @param integer $room_id
	 */
	public static function getRoomUsersHash( $room_id )
	{
		$room_id = (int)$room_id;

		$offline_stamp = self::getOfflineStamp();

		$result = SK_MySQL::query(
			"SELECT `profile_id` FROM `".TBL_CHAT_USER."`
				WHERE `chat_room_id`=$room_id
					AND `ping_stamp`>$offline_stamp"
		);

		$user_id_slug = '';
		while ( $user_id = $result->fetch_cell() ) {
			$user_id_slug .= "$user_id|";
		}

		return md5($user_id_slug);
	}

	/**
	 * Returns a list of room messages which autoincrement `id` is bigger than argued $last_msg_id.
	 *
	 * @param integer $room_id
	 * @param integer $last_msg_id
	 * @return array
	 */
	public static function getNewMessages( $room_id, $last_message_id = 0 )
	{
		$room_id = (int)$room_id;
		$last_message_id = (int)$last_message_id;

		if ( !$last_message_id )
		{
			$configs = SK_Config::section('chat');

			if ( $configs->history_type == 'recent_msg_num' ) {
				$query =
					"SELECT * FROM (
						SELECT * FROM `".TBL_CHAT_MESSAGE."`
							WHERE `chat_room_id`=$room_id
							ORDER BY `chat_message_id` DESC
							LIMIT $configs->history_recent_msgs_num
						) AS `messages`
						ORDER BY `chat_message_id` ASC";
			}
			elseif ( $configs->history_type == 'by_time' ) {
				switch ( $configs->history_time->unit ) {
					case 'seconds':
						$factor = 1;
						break;
					case 'minutes':
						$factor = 60;
						break;
					case 'hours':
						$factor = 60*60;
						break;
				}

				$from_timestamp = time() - ($factor * $configs->history_time->digit);

				$query =
					"SELECT * FROM `".TBL_CHAT_MESSAGE."`
						WHERE `chat_room_id`=$room_id
							AND `timestamp`>$from_timestamp";
			}
		}
		else {
			$query = "SELECT * FROM `".TBL_CHAT_MESSAGE."`
				WHERE `chat_message_id`>$last_message_id
					AND `chat_room_id`=$room_id";
		}

		$result = SK_MySQL::query($query);

		$messages = array();
		while ( $row = $result->fetch_object('SK_ChatRoomMessage') ) {
			$row->construct();
			$messages[] = $row;
		}

		return $messages;
	}

	/**
	 * Trace an user ping.
	 *
	 * @param integer $room_id
	 */
	public static function traceUserPing( $room_id )
	{
		if ( !($room_id = (int)$room_id)
			|| !SK_HttpUser::is_authenticated()) {
			return false;
		}

		$profile_id = SK_HttpUser::profile_id();

		$result = SK_MySQL::query(
			'SELECT COUNT(`profile_id`) FROM `'.TBL_CHAT_USER.'`
				WHERE `chat_room_id`='.$room_id.' AND `profile_id`='.$profile_id
		);

		$user_in_room = (int)$result->fetch_cell();

		if ( $user_in_room ) {
			// updating user ping timestamp
			SK_MySQL::query(
				'UPDATE `'.TBL_CHAT_USER.'` SET `ping_stamp`='.time().'
					WHERE `chat_room_id`='.$room_id.' AND `profile_id`='.$profile_id
			);
		}
		else {
			$profile_sex = app_Profile::getFieldValues($profile_id, 'sex');
			SK_MySQL::query(
				'INSERT INTO `'.TBL_CHAT_USER.'`
					SET `profile_id`='.$profile_id.',
						`sex`='.$profile_sex.',
						`chat_room_id`='.$room_id.',
						`ping_stamp`='.time()
			);
		}

		return true;
	}

	/**
	 * Adds a user message entries to a database.
	 *
	 * @param integer $room_id
	 * @param array $msg_entries
	 */
	public static function processMsgEntries( $room_id, array $msg_entries )
	{
		$room_id = (int)$room_id;
		$profile_id = SK_HttpUser::profile_id();

		$query = 'INSERT INTO `'.TBL_CHAT_MESSAGE.'`
					(`chat_room_id`, `profile_id`, `text`, `timestamp`, `color`)
						VALUES ';

		$sql_values = array();
		foreach ( $msg_entries as $msg ) {
			$sql_values[] =
				'('.$room_id.', '.$profile_id.', "'.SK_MySQL::realEscapeString($msg['text']).'", '.time().', "'.SK_MySQL::realEscapeString($msg['color']).'")';
		}

		if ( $sql_values ) {
			SK_MySQL::query($query . implode(', ', $sql_values));
		}
	}

	/**
	 * Clear a room users garbage.
	 * Cron-called function.
	 */
	public static function clearGarbage()
	{
		SK_MySQL::query(
			'DELETE FROM `'.TBL_CHAT_USER.'` WHERE `ping_stamp`<'.self::getOfflineStamp()
		);
	}

}


class SK_ChatRoom
{
	/**
	 * Database autoincrement id.
	 *
	 * @var integer
	 */
	public $chat_room_id;

	/**
	 * Active room will set this value to TRUE;
	 *
	 * @var boolean
	 */
	public $active;

	/**
	 * The room name on user language.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Custom called constructor.
	 */
	public function construct() {
		$this->name = SK_Language::section('components.chat.rooms')->cdata($this->chat_room_id);
	}

}


class SK_ChatUser
{
	/**
	 * Chat room id.
	 *
	 * @var integer
	 */
	public $chat_room_id;

	/**
	 * An ID of a user profile.
	 *
	 * @var integer
	 */
	public $profile_id;

	/**
	 * Profile sex.
	 *
	 * @var integer
	 */
	public $sex;

	/**
	 * User last ping timestamp.
	 *
	 * @var integer
	 */
	public $ping_stamp;

	/**
	 * A name of a user.
	 *
	 * @var string
	 */
	public $username;

	/**
	 * Custom called constructor.
	 */
	public function construct() {
		$this->username = app_Profile::username($this->profile_id);
	}
}


class SK_ChatRoomMessage
{
	/**
	 * Chat message id.
	 *
	 * @var integer
	 */
	public $chat_message_id;

	/**
	 * Chat room id.
	 *
	 * @var integer
	 */
	public $chat_room_id;

	/**
	 * An ID of a user profile.
	 *
	 * @var integer
	 */
	public $profile_id;

	/**
	 * Profile username.
	 *
	 * @var string
	 */
	public $username;

	/**
	 * Message text.
	 *
	 * @var string
	 */
	public $text;

	/**
	 * Message entry timestamp.
	 *
	 * @var integer
	 */
	public $timestamp;

	/**
	 * Message entry formatted time.
	 *
	 * @var string
	 */
	public $format_time;

	/**
	 * Keeps an info cache about fetched user profiles.
	 *
	 * @var array
	 */

	static private $users_cache;

    /**
	 * Profile href.
	 *
	 * @var array
	 */

    public $href;

	/**
	 * Custom called constructor.
	 */
	public function construct()
	{
		if ( isset(self::$users_cache[$this->profile_id]) ) {
			$this->username = self::$users_cache[$this->profile_id];
		}
		else {
			$this->username = app_Profile::username($this->profile_id);
			self::$users_cache[$this->profile_id] = $this->username;
		}

                $military_time = SK_Config::section('site.official')->military_time;
                $this->format_time = $military_time ? strftime("%H:%M", $this->timestamp) : strftime("%I:%M%P", $this->timestamp);

        $this->text = app_TextService::stCensor( app_TextService::stHandleSmiles(SK_Language::htmlspecialchars($this->text)),  FEATURE_CHAT );

		$this->text = $this->truncate_text($this->text);

        $this->href = app_Profile::href($this->profile_id);
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
