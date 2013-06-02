<?php

class ChatAdmin
{
	public static function save_history_config( $post_data )
	{
		global $frontend, $time_units;

		$affected_vars = 0;

		if ( !in_array($post_data['chat_history_type'], array('recent_msg_num', 'by_time')) ) {
			exit('Error: "chat_history_type" missing or have an unallowable value');
		}

		if (
			SK_Config::section('chat')
				->set('history_type', $post_data['chat_history_type'])
		) {
			$affected_vars++;
		}

		if ( $post_data['chat_history_type'] == 'recent_msg_num' )
		{
			if ( is_numeric($post_data['recent_msgs_num']) && $post_data['recent_msgs_num'] > 0 )
			{
				if (
					SK_Config::section('chat')
						->set('history_recent_msgs_num', (int)$post_data['recent_msgs_num'])
				) {
					$affected_vars++;
				}
			}
			else {
                $frontend->registerMessage('"Number of recent Chat messages to display" missing or have a non-numeric value', 'error');
			}
		}
		elseif ( $post_data['chat_history_type'] == 'by_time' )
		{
			if ( !is_numeric($post_data['chat_history_time_digit']) ) {
                $frontend->registerMessage('"Keep chat messages displaying for" digit missing or have a non-numeric value', 'error');
			}
            else
            if ( !in_array($post_data['chat_history_time_unit'], $time_units) ) {
                $frontend->registerMessage('"Keep chat messages displaying for" unit missing or have a non-numeric value', 'error');

			}
			else
			if (
				SK_Config::section('chat')
					->set('history_time', array(
						'digit' => (int)$post_data['chat_history_time_digit'],
						'unit'	=> $post_data['chat_history_time_unit']
					))
			) {
				$affected_vars++;
			}
		}

		if ( $affected_vars ) {
			$frontend->registerMessage('Chat configs updated');
		}
	}


	public static function create_room( $post_data )
	{
		global $frontend;

                $languages = SK_LanguageEdit::getLanguages();
                $languagesCount = 0;

                foreach ( $languages as $lang )
                {
                    if ( $lang->enabled )
                    {
                        $languagesCount++;
                    }
                }

                $name = array_filter( $post_data['room_name'],'strlen' );

		if ( count($name) < $languagesCount ) {
			$frontend->registerMessage('Value for Room Name is missing for one of the active languages', 'notice');
                        return;
		}

		$query = SK_MySQL::placeholder(
			'INSERT INTO `'.TBL_CHAT_ROOM.'` SET `active`="1"'
		);

		SK_MySQL::query($query);

		$new_room_id = SK_MySQL::insert_id();

		SK_LanguageEdit::setKey('components.chat.rooms', $new_room_id, $name);

		$frontend->registerMessage('Chat room created');
	}


	public static function save_rooms_config( $post_data )
	{
		global $frontend;

		$chat_rooms = app_Chat::getRooms(true);

		$activate_room_ids = array();
		$deactivate_room_ids = array();

		foreach ( $chat_rooms as $room )
		{
			if ( !$room->active && isset($post_data['active_rooms'][$room->chat_room_id]) ) {
				$activate_room_ids[] = $room->chat_room_id;
			}
			elseif ( $room->active && !isset($post_data['active_rooms'][$room->chat_room_id]) ) {
				$deactivate_room_ids[] = $room->chat_room_id;
			}
		}

		$affected_rows = 0;

		if ( !empty($activate_room_ids) )
		{
			$query = SK_MySQL::placeholder(
				'UPDATE `'.TBL_CHAT_ROOM.'` SET `active`=1
					WHERE `chat_room_id` IN (?@)'
				, $activate_room_ids
			);

			SK_MySQL::query($query);

			$affected_rows += SK_MySQL::affected_rows();
		}

		if ( !empty($deactivate_room_ids) )
		{
			$query = SK_MySQL::placeholder(
				'UPDATE `'.TBL_CHAT_ROOM.'` SET `active`=0
					WHERE `chat_room_id` IN (?@)'
				, $deactivate_room_ids
			);

			SK_MySQL::query($query);

			$affected_rows += SK_MySQL::affected_rows();
		}

		if ( $affected_rows ) {
			$frontend->registerMessage('Chat rooms configuration updated');
		}
	}


	public static function delete_room( $post_data )
	{
		global $frontend;

		if ( !isset($post_data['room_id']) ) {
			exit('Error: missing param "room_id"');
		}
		elseif ( !(
			$room_id = (int)$post_data['room_id']
		) ) {
			exit('Error: invalid argument "room_id"');
		}

		SK_MySQL::query(
			'DELETE FROM `'.TBL_CHAT_ROOM.'`
				WHERE `chat_room_id`='.$room_id
		);

		SK_LanguageEdit::deleteKey('components.chat.rooms', $room_id);

		$frontend->registerMessage('Chat room deleted');
	}

}

if ( $action = @$_POST['action'] )
{
	unset($_POST['action']);

	$request_callback = array('ChatAdmin', $action);

	if ( method_exists($request_callback[0], $request_callback[1]) )
	{
		call_user_func($request_callback, $_POST);
		header('Location: '.$_SERVER['REQUEST_URI']);
	}
	else {
		trigger_error('call to undefined function '.implode('::', $request_callback), E_USER_ERROR);
	}

	exit();
}
