<?php

class app_Shoutbox
{
	/**
	 * Add a message entries to a database.
	 */
	public static function addMessage( $profile_id, $username, $text, $color )
    {
        $text = SK_MySQL::realEscapeString($text);
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_SHOUTBOX."` ( `profile_id`, `username`, `timestamp`, `text`, `color` )
						VALUES (?, '?', ?, '?', '?')", $profile_id, $username, time(), $text, $color);

        SK_MySQL::query($query);

	}

    public static function deleteMessage($id)
    {
        $query = SK_MySQL::placeholder( "DELETE FROM `".TBL_SHOUTBOX."` WHERE `id`=? ", $id);

        SK_MySQL::query($query);
    }

	/**
	 * Get new messages.
	 */
	public static function getNewMessages($lastMessageId)
	{
        $count = SK_Config::section('site')->Section('additional')->Section('shoutbox')->post_count;

        $query = SK_MySQL::placeholder("SELECT * FROM (SELECT * FROM `".TBL_SHOUTBOX."` WHERE `id`>? ORDER BY `id` DESC LIMIT 0, ?) AS `sh` ORDER BY `id` ASC", $lastMessageId, $count);
		$result = SK_MySQL::query($query);

		$messages = array();
		while ( $row = $result->fetch_object() )
		{
            if ($row->profile_id != 0)
			{
				if  (app_Profile::isProfileDeleted($row->profile_id))
				{
					$profile_thumb_url = app_ProfilePhoto::deleted_url();
				}
				else
				{
					$profile_thumb_url = app_ProfilePhoto::getThumbUrl($row->profile_id);
				}
			}
			else
			{
				$profile_thumb_url = app_ProfilePhoto::defaultPhotoUrl(0, app_ProfilePhoto::PHOTOTYPE_THUMB);
			}
			$messages[] =
				array(
                    'id' => $row->id,
					'profile_id'    => $row->profile_id,
                    'profile_thumb_url'    => $profile_thumb_url,
                    'href'      => ($row->profile_id != 0) ? app_Profile::href($row->profile_id) : '#',
					'username'	=> $row->username,
					'text'      => app_TextService::stHandleSmiles( app_TextService::stCensor(self::truncate_text( SK_Language::htmlspecialchars( stripslashes( $row->text ) ) ), FEATURE_SHOUTBOX, false) ),
					'time'      => SK_I18n::getSpecFormattedDate( $row->timestamp ),
					'color'		=> $row->color
				);

		}

		return $messages;
	}

    public static function getMessagesCount()
    {
        $query = SK_MySQL::placeholder("SELECT COUNT(*) FROM  `".TBL_SHOUTBOX."` ");
		return SK_MySQL::query($query)->fetch_cell();
    }


    public static function truncate_text( $string, $max_chars = 60 )
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

    public static function clearPosts()
    {
        $count = SK_Config::section('site')->Section('additional')->Section('shoutbox')->post_count;
        $query = "SELECT `id` FROM `".TBL_SHOUTBOX."` WHERE 1 ORDER BY `timestamp` DESC LIMIT ".$count;
        $result = MySQL::query($query);
        $array = array();
        while ( $row = $result->fetch_array() )
        {
            $array[] = $row['id'];
        }
        if (!empty($array))
        {
            $result_str = implode(',', $array);
            $query = "DELETE FROM `".TBL_SHOUTBOX."` WHERE `id` NOT IN ( {$result_str} )";

            $result = SK_MySQL::query($query);
        }
    }

}



