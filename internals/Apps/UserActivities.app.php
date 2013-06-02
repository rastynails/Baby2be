<?php

class app_UserActivities
{
	/**
	 * Get last activity list.
	 *
	 * @param integer $viewer_profile_id
	 * @return array
	 */
	public static function get_list($counter = 1, $viewer_profile_id = null )
	{
		if ( !isset($viewer_profile_id) ) {
			if ( !SK_HttpUser::is_authenticated() ) {
				throw new Exception('failed to get $viewer_profile_id automatically, user not authenticated', 0);
			}
			$viewer_profile_id = SK_HttpUser::profile_id();
		}
		elseif ( !is_numeric($viewer_profile_id)
			|| !($viewer_profile_id = (int)$viewer_profile_id)
		) {
			throw new Exception('invalid argument $viewer_profile_id', 0);
		}
		
		
		$activity_list = array();
		
		$today_date_key = date('dmY');
		$yesterday_date_key = date('dmY', $row->timestamp - (3600*24));

        // getting friend ids index
		$friends = SK_FriendList::friend_id_index($viewer_profile_id);
		
        if( $friends )
        {
        	$count = SK_Config::Section('user_activity')->items_count * $counter;

            $query = SK_MySQL::placeholder(
                'SELECT * FROM `'.TBL_USER_ACTIVITY."`
                    WHERE `status`='active' AND `actor_id` IN (?@)
                    ORDER BY `timestamp` DESC LIMIT {$count}"
                , $friends
            );

            $result = SK_MySQL::query($query);

            while ( $row = $result->fetch_object() )
            {
                $date_key = date('dmY', $row->timestamp);

                if ( !isset($activity_list[$date_key]) )
                {
                    /*if ( $date_key == $today_date_key ) {
                        $date_label = SK_Language::text('i18n.date.today');
                    }
                    elseif ( $date_key == $yesterday_date_key ) {
                        $date_label = SK_Language::text('i18n.date.yesterday');
                    }
                    else {
                        $date_label = date('M j, Y', $row->timestamp);
                    }*/

                    $activity_list[$date_key] =
                        Object(array(
                            'date'	=> SK_I18n::getSpecFormattedDate( $row->timestamp ),
                            'feeds'	=> array()
                        ));
                }

                $feed = &$activity_list[$date_key]->feeds["$row->type%$row->actor_id%$row->properties"];
                $props = json_decode($row->properties, true);
                $props = !is_array($props) ? array() : $props;
                
                if ( !isset($feed) ) {
                    $feed = Object(
                        array_merge(array(
                            'actor_id'	=>	$row->actor_id,
                            'type'		=>	$row->type,
                            'time'		=>	SK_I18n::getSpecFormattedDate( $row->timestamp ),//date('g:ia', $row->timestamp),
                            'items'		=>	array(),
                            'items_c'	=>	0
                        ), $props)
                    );
                }

                $feed->items[] = json_decode($row->item);
                $feed->items_c++;
            }
        }

		return $activity_list;
	}
	
	static function getUserActivityList($counter, $userId){
		$activity_list = array();
		
		$today_date_key = date('dmY');
		$yesterday_date_key = date('dmY', $row->timestamp - (3600*24));

        // getting friend ids index
        $a = array();
        $a[$userId] = $userId;
		$friends = $a; 
		
        if( $friends )
        {
			$count = SK_Config::Section('user_activity')->items_count * $counter;

            $query = SK_MySQL::placeholder(
                'SELECT * FROM `'.TBL_USER_ACTIVITY."`
                    WHERE `status`='active' AND `actor_id` IN (?@)
                    ORDER BY `timestamp` DESC LIMIT {$count}"
                , $friends
            );

            $result = SK_MySQL::query($query);
            while ( $row = $result->fetch_object() )
            {
                $date_key = date('dmY', $row->timestamp);

                if ( !isset($activity_list[$date_key]) )
                {
                    /*if ( $date_key == $today_date_key ) {
                        $date_label = SK_Language::text('i18n.date.today');
                    }
                    elseif ( $date_key == $yesterday_date_key ) {
                        $date_label = SK_Language::text('i18n.date.yesterday');
                    }
                    else {
                        $date_label = date('M j, Y', $row->timestamp);
                    }*/

                    $activity_list[$date_key] =
                        Object(array(
                            'date'	=> SK_I18n::getSpecFormattedDate( $row->timestamp ),
                            'feeds'	=> array()
                        ));
                }

                $feed = &$activity_list[$date_key]->feeds["$row->type%$row->actor_id%$row->properties"];
                $props = json_decode($row->properties, true);
                $props = !is_array($props) ? array() : $props;
                
                if ( !isset($feed) ) {
                    $feed = Object(
                        array_merge(array(
                            'actor_id'	=>	$row->actor_id,
                            'type'		=>	$row->type,
                            'time'		=>	SK_I18n::getSpecFormattedDate( $row->timestamp ),//date('g:ia', $row->timestamp),
                            'items'		=>	array(),
                            'items_c'	=>	0
                        ), $props)
                    );
                }

                $feed->items[] = json_decode($row->item);
                $feed->items_c++;
            }
        }

		return $activity_list;		
	}
	
	/**
	 * Traces an action.
	 *
	 * @param integer $actor_profile_id
	 * @param string $action_type
	 * @param array $params
	 */
	public static function trace_action( SK_UserAction $action )
	{
		SK_MySQL::query(
			'INSERT INTO `'.TBL_USER_ACTIVITY.'` SET '.$action->toSQL()
		);
	}
	
	public static function deleteActivities($itemId, $type)
	{
		$_query = sql_placeholder("DELETE FROM `?#TBL_USER_ACTIVITY` WHERE `type`=? AND `item`=?",
			$type, $itemId);

		return MySQL::affectedRows( $_query );
	}

	public static function getWhere($where)
	{
		$query = sql_placeholder( "select * from `?#TBL_USER_ACTIVITY` where {$where}" );
		
		return MySQL::fetchArray($query);
	}
	
	public static function setStatus($id, $status){
        $sql = sql_placeholder(
			"UPDATE `?#TBL_USER_ACTIVITY` SET `status`=? WHERE `skadate_user_activity_id`=?",
				$status, $id
		);
		return MySQL::affectedRows($sql);
	}
	
	public static function cronJob()
	{
	    $time = time();

	    $q = sql_placeholder("DELETE FROM `?#TBL_USER_ACTIVITY` WHERE ({$time} - `timestamp`) > 60*60*24*30");

        MySQL::fetchResource($q);
	}
}
