<?php

require_once DIR_INTERNALS . 'utf8.php';

/**
 * Forum class.
 *
 * @package SkaDate
 * @subpackage SkaDate7
 * @link http://www.skadate.com
 * @version 7.0
 */
class app_Forum
{

    private static $postInfoCache;

    private static $postUrlCache;

    private static $topickInfoCache;
	/**
	 * Returns forum list with following info: forum name, description; count of the topics, posts; last post info
	 *
	 * @return array
	 */
	public static function getGroupForumList()
	{
		$query_ins = "`f`.*,`g`.`forum_group_id`,`g`.`order` AS `forum_group_order`,`g`.`name` AS `group_name`,`t`.`count_topic`,`p`.`count_post`";
		$join_tbl = "INNER JOIN `".TBL_FORUM."`AS `f` USING(`forum_group_id`) ";
		$join_tbl .= "LEFT JOIN (SELECT `forum_id`,COUNT(`forum_topic_id`) AS `count_topic` FROM `".TBL_FORUM_TOPIC."`
								  GROUP BY `forum_id`) AS `t` ON(`f`.`forum_id`=`t`.`forum_id`) ";
		$join_tbl .= "LEFT JOIN (SELECT `forum_id`,COUNT(`forum_post_id`) AS `count_post` FROM `".TBL_FORUM_POST."`
								  INNER JOIN `".TBL_FORUM_TOPIC."` USING(`forum_topic_id`) GROUP BY `forum_id`) AS `p` ON(`p`.`forum_id`=`f`.`forum_id`) ";
		$query_cond = "WHERE 1 GROUP BY `forum_id` ORDER BY `g`.`order`,`f`.`order`";

		$query_result = SK_MySQL::query( "SELECT $query_ins FROM `".TBL_FORUM_GROUP."` AS `g` $join_tbl $query_cond" );

		$query_groups_result = SK_MySQL::query( "SELECT `g`.* FROM `".TBL_FORUM_GROUP."` AS `g`
				   LEFT JOIN `".TBL_FORUM."` AS `f` USING(`forum_group_id`) WHERE `f`.`forum_group_id` is NULL" );

		$return = array();

        $list = array();
        $profileIdList = array();
		while ( $value = $query_result->fetch_assoc() )
		{
            $profileIdList[] = $value['profile_id'];
            if ( $value['forum_id'] )
			{
				$last_topic = self::getLastTopicByForumId($value['forum_id']);
				$value['count_topic'] = intval($value['count_topic']);
				$value['count_post'] = intval($value['count_post']);
				$value['last_topic_id'] = $last_topic['forum_topic_id'];
				$value['last_topic_stamp'] = $last_topic['create_stamp'];
				$value['title'] = htmlspecialchars(app_TextService::stOutputFormatter($last_topic['title'], 'forum', true ));
				$value['profile_id'] = $last_topic['profile_id'];
        }

            $profileIdList[] = $value['profile_id'];

            $list[] = $value;
        }

        app_Profile::getUsernamesForUsers($profileIdList, false);
        app_Profile::checkProfileDeletedForUsers($profileIdList);

		foreach( $list as $value )
		{
			if ( !$return[$value['forum_group_id']]['name'] )
			{
				$return[$value['forum_group_id']]['name'] = $value['group_name'];
				$return[$value['forum_group_id']]['forum_group_id'] = $value['forum_group_id'];
				$return[$value['forum_group_id']]['forum_group_order'] = $value['forum_group_order'];
			}

			if ( $value['forum_id'] )
			{
				$value['is_deleted'] = app_Profile::isProfileDeleted( $value['profile_id'] );
				$value['username'] = app_Profile::username( $value['profile_id'] );

				$return[$value['forum_group_id']]['forums'][] = $value;
			}
		}

		while ( $_group = $query_groups_result->fetch_assoc() )
		{
			$return[$_group['forum_group_id']] = $_group;
			$return[$_group['forum_group_id']]['forums'] = array();
		}

		return $return;
	}

	public static function getForumsList( $for_search=false )
	{
		$query = "SELECT `g`.`forum_group_id`, `g`.`name` as `group_name`, `f`.`forum_id`, `f`.`name`
				  FROM `".TBL_FORUM_GROUP."` as `g` LEFT JOIN `".TBL_FORUM."` as `f` ON `g`.`forum_group_id`=`f`.`forum_group_id`";
		$query_result = SK_MySQL::query( $query );

		if ( $for_search )
		{
			$search_all = SK_Language::section('components.forum_search')->text('search_in_all_forums');
			$result[$search_all] = 'all';
		}

		$cur_group_id = 0;
		while ( $value = $query_result->fetch_assoc() )
		{
			if( $cur_group_id!=$value['forum_group_id'] ){
				$result[$value['group_name']] = ( $for_search ) ? 'group_'.$value['forum_group_id'] :'';
			}
			$label = ' - '.$value['name'];
			$result[$label] = $value['forum_id'];

			$cur_group_id = $value['forum_group_id'];
		}

		return $result;
	}



	/**
	 * Returns forum info.
	 *
	 * @param integer $forum_id
	 * @return array
	 */
	public static function getForumInfo( $forum_id )
	{
		$forum_id = intval( $forum_id );
		if ( !$forum_id )
			return array();
		$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_FORUM."` WHERE `forum_id`=? ", $forum_id );

		return SK_MySQL::query( $query )->fetch_assoc();
	}


	/**
	 * Returns forum and last topic info.
	 *
	 * @param integer $forum_id
	 * @return array
	 */
	public static function getLastTopicByForumId( $forum_id )
	{
		$forum_id = intval( $forum_id );
		if ( !$forum_id )
			return array();

		if ( !empty(self::$topickInfoCache[$forum_id]) )
        {
            return self::$topickInfoCache[$forum_id];
        }
        else
        {

            $query = SK_MySQL::placeholder( "SELECT `t`.*,`f`.*
                FROM `".TBL_FORUM."` AS `f`
                LEFT JOIN `".TBL_FORUM_TOPIC."` AS `t` USING(`forum_id`)
                LEFT JOIN `".TBL_FORUM_POST."` AS `p` USING(`forum_topic_id`)
                WHERE `f`.`forum_id`=? ORDER BY `p`.`create_stamp` DESC LIMIT 1", $forum_id );

            $return = SK_MySQL::query($query)->fetch_assoc();
            self::$topickInfoCache[$forum_id] = $return;
            return $return;
        }
	}

	/**
	 * Returns last post info.
	 *
	 * @param integer $forum_id
	 * @return array
	 */
	public static function getLastPostByForumId( $forum_id )
	{
		$forum_id = intval( $forum_id );
		if ( !$forum_id )
			return array();

		$query = SK_MySQL::placeholder( "SELECT `p`.*,`t`.`title`
			FROM `".TBL_FORUM."` AS `f`
			INNER JOIN `".TBL_FORUM_TOPIC."` AS `t` USING(`forum_id`)
			INNER JOIN `".TBL_FORUM_POST."` AS `p` USING(`forum_topic_id`)
			WHERE `f`.`forum_id`=? ORDER BY `p`.`create_stamp` DESC LIMIT 1", $forum_id );

		$return =  SK_MySQL::query($query)->fetch_assoc();

		return $return;
	}


	/**
	 * Returns last post info.
	 *
	 * @param integer $forum_id
	 * @return array
	 */
	public static function getLastPostByTopicId( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !$topic_id )
			return array();

		$query = SK_MySQL::placeholder( "SELECT `p`.*
			FROM `".TBL_FORUM_TOPIC."` AS `t`
			INNER JOIN `".TBL_FORUM_POST."` AS `p` USING(`forum_topic_id`)
			WHERE `t`.`forum_topic_id`=? ORDER BY `p`.`create_stamp` DESC LIMIT 1", $topic_id );
		$return = SK_MySQL::query($query)->fetch_assoc();

		return $return;
	}


	/**
	 * Returns topic list with following info: topic title,username of the creator, create_stamp; count of the posts; last post info
	 *
	 * @param integer $forum_id
	 * @param integer $num_on_page
	 * @param integer $page
	 * @return array
	 */
	public static function getTopicListByForumId( $forum_id, $num_on_page = 10, $page = 1 )
	{
		$forum_id = intval( $forum_id );
		$page = ( intval( $page ) < 1 )? 1 : intval( $page );

		if ( !$forum_id )
			return array();

		$query = SK_MySQL::placeholder( "SELECT `f`.`name`,`t`.*,COUNT(`forum_post_id`) AS `count_post`,MAX(`p`.`create_stamp`) AS `cr`
			FROM `".TBL_FORUM."` AS `f`
			INNER JOIN `".TBL_FORUM_TOPIC."` AS `t` USING(`forum_id`)
			INNER JOIN `".TBL_FORUM_POST."` AS `p` USING(`forum_topic_id`)
			WHERE `f`.`forum_id`=$forum_id
			GROUP BY `forum_topic_id` ORDER BY `t`.`is_sticky` DESC, `cr` DESC LIMIT ".( ($page - 1)*$num_on_page ).",$num_on_page" );

		$query_result = SK_MySQL::query($query);

        $valueList = array();
        $profileIdList = array();
        $lastPostProfileId = array();

		while ( $value = $query_result->fetch_assoc() )
		{
            $last_post = self::getLastPostByTopicId( $value['forum_topic_id'] );
            $lastPostProfileId[] = $last_post['profile_id'];
            $profileIdList[] = $value['profile_id'];

			$value['last_post_profile_id'] = $last_post['profile_id'];
			$value['last_post_stamp'] = $last_post['create_stamp'];

            $valueList[] = $value;
        }

        app_Profile::checkProfileDeletedForUsers($profileIdList);
        app_Profile::checkProfileDeletedForUsers($lastPostProfileId);
        app_Profile::getUsernamesForUsers($profileIdList);
        app_Profile::getUsernamesForUsers($lastPostProfileId);

		foreach( $valueList as $value )
		{
			$value['is_deleted'] = app_Profile::isProfileDeleted( $value['profile_id'] );
			$value['username'] = app_Profile::username( $value['profile_id'] );
			$value['title'] = app_TextService::stCensor( $value['title'], 'forum', true );
			$value['last_post_is_deleted'] = app_Profile::isProfileDeleted($value['last_post_profile_id']);
            $value['last_post_username'] = app_Profile::username($value['last_post_profile_id']);
			$return[] = $value;
		}

		return $return;
	}

	/**
	 * Returns topic's all info.
	 *
	 * @param integer $topic_id
	 * @return integer
	 */
	public static function getTopic( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !$topic_id )
			return array();

		$query = SK_MySQL::placeholder( "SELECT `t`.*,`t`.`create_stamp` AS `topic_create_stamp`,`p`.*
			FROM `".TBL_FORUM_TOPIC."` AS `t`
			INNER JOIN `".TBL_FORUM_POST."` AS `p` USING(`forum_topic_id`)
			WHERE `t`.`forum_topic_id`=?
			ORDER BY `p`.`create_stamp` ASC LIMIT 1", $topic_id );

		$return = SK_MySQL::query($query)->fetch_assoc();

		if ( !$return ) {
			return array();
		}

		$return['title'] = app_TextService::stCensor( $return['title'], 'forum', true );
		$return['edited_by_is_deleted'] = app_Profile::isProfileDeleted($return['edited_by_profile_id']);
		$return['edited_by_username'] = app_Profile::username($return['edited_by_profile_id']);

		return $return;
	}

	public static function getTopicHtml( $topic_id )
	{
		$topic_info = self::getTopic($topic_id);
		$topic_info['text'] = nl2br( SK_Language::htmlspecialchars( $topic_info['text'] ) );
		$topic_info['text'] = self::forumTagsToHtmlChars($topic_info['text']);

		return $topic_info;
	}


	/**
	 * Returns post count of a topic.
	 *
	 * @param integer $topic_id
	 * @return integer
	 */
	public static function getTopicCount( $forum_id )
	{
		$forum_id = intval( $forum_id );
		if ( !$forum_id )
			return 0;

		$query = SK_MySQL::placeholder( "SELECT COUNT(`t`.`forum_topic_id`)
			FROM `".TBL_FORUM."` AS `f`
			INNER JOIN `".TBL_FORUM_TOPIC."` AS `t` USING(`forum_id`)
			WHERE `f`.`forum_id`=?", $forum_id );

		return SK_MySQL::query( $query )->fetch_cell();
	}


	public static function getTopicIdByPost($post_id)
	{
		$query = SK_MySQL::placeholder( "SELECT `forum_topic_id` FROM `".TBL_FORUM_POST."`
			WHERE `forum_post_id`=?", $post_id );
		return SK_MySQL::query( $query )->fetch_cell();
	}

	/**
	 * Updates topic: update topic and its first post info.
	 *
	 * @param integer $profile_id
	 * @param integer $topic_id
	 * @param string $title
	 * @param string $text
	 * @return boolean
	 */
	public static function UpdateTopic( $profile_id, $topic_id, $title, $text )
	{
		$topic_id = intval( $topic_id );
		$profile_id = intval( $profile_id );
		$title = trim( $title );
		$text = trim( $text );
		if ( !$profile_id || !$topic_id || !strlen($title) || !strlen($text) )
			return false;

		//update topic title
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_TOPIC."` SET `title`='?' WHERE `forum_topic_id`=?", $title, $topic_id );
		SK_MySQL::query($query);

		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_POST."` SET `text`='?',`edited_by_profile_id`=?,`edit_stamp`=?
			WHERE `forum_topic_id`=? ORDER BY `create_stamp` ASC LIMIT 1", $text, $profile_id, time(), $topic_id );
		SK_MySQL::query($query);

		return true;
	}


	/**
	 * Deletes a topic.
	 *
	 * @param integer $topic_id
	 * @return boolean
	 */
	public static function DeleteTopic( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !$topic_id )
			return false;

		$query_topic = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM_TOPIC."` WHERE `forum_topic_id`=?", $topic_id );
		$query_post = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM_POST."` WHERE `forum_topic_id`=?", $topic_id );
		$query_notifies = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM_PROFILE_NOTIFY."` WHERE `topic_id`=?", $topic_id);
		SK_MySQL::query($query_notifies);

		return ( SK_MySQL::query($query_topic) && SK_MySQL::query($query_post) )? true : false;
	}


	/**
	 * Returns post list.
	 *
	 * @param integer $topic_id
	 * @param integer $num_on_page
	 * @param integer $page
	 * @param string $order
	 * @return array
	 */
	public static function getPostListByTopicId( $topic_id, $num_on_page = 10, $page = 1, $order = 'ASC' )
	{
		$topic_id = intval( $topic_id );
		$page = ( intval( $page ) < 1 )? 1 : intval( $page );
		$order = ( $order == 'ASC' )? $order : 'DESC';
		if ( !$topic_id )
			return array();

		$query = SK_MySQL::placeholder( "SELECT `t`.`forum_id`,`t`.`title`,`t`.`profile_id` AS `topic_profile_id`,`t`.`create_stamp` AS `topic_create_stamp`,`p`.*
			FROM `".TBL_FORUM_TOPIC."` AS `t`
			INNER JOIN `".TBL_FORUM_POST."` AS `p` USING(`forum_topic_id`)
			WHERE `t`.`forum_topic_id`=?
			ORDER BY `p`.`create_stamp` $order LIMIT ?, ?", $topic_id, ( ($page - 1)*$num_on_page ), $num_on_page );
		$query_result = SK_MySQL::query( $query );

        $valueList = array();
        $profileIdList = array();
        $editedByIdList = array();

		while ( $value = $query_result->fetch_assoc() )
		{
            $profileIdList[] = $value['profile_id'];

            if ( !empty($value['edited_by_profile_id']) )
            {
                $editedByIdList[] = $value['edited_by_profile_id'];
			}

            $valueList[] = $value;
        }

        app_Profile::getUsernamesForUsers($profileIdList);
        app_Profile::checkProfileDeletedForUsers($profileIdList);

        if ( !empty($editedByIdList) )
        {
            app_Profile::getUsernamesForUsers($editedByIdList);
            app_Profile::checkProfileDeletedForUsers($editedByIdList);
        }

		foreach ( $valueList as $value )
		{
			$value['is_deleted'] = app_Profile::isProfileDeleted( $value['profile_id'] );
			$value['username'] = app_Profile::username( $value['profile_id'] );
			$value['profile_sex'] = app_Profile::getFieldValues( $value['profile_id'], 'sex');
			$pbirthdate = app_Profile::getFieldValues( $value['profile_id'], 'birthdate');
			if (empty($pbirthdate) || $pbirthdate == '0000-00-00')
			{
			    $value['profile_age'] = false;
			}
			else
			{
			    $value['profile_age'] = app_Profile::getAge($pbirthdate);
			}

			$value['text'] =  nl2br( SK_Language::htmlspecialchars( $value['text'] ) );
			$value['text'] = self::forumTagsToHtmlChars($value['text']);

			if ( $value['edited_by_profile_id'] ) {
				$value['edited_by_is_deleted'] = app_Profile::isProfileDeleted( $value['edited_by_profile_id'] );
				$value['edited_by_username'] = app_Profile::username( $value['edited_by_profile_id'] );
			}
			$return[] = $value;
		}

		return $return;
	}


	/**
	 * Returns post count of a topic.
	 *
	 * @param integer $topic_id
	 * @return integer
	 */
	public static function getPostCount( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !$topic_id )
			return 0;
		$query = SK_MySQL::placeholder( "SELECT COUNT(`p`.`forum_post_id`) FROM `".TBL_FORUM_TOPIC."` AS `t`
			INNER JOIN `".TBL_FORUM_POST."` AS `p` USING(`forum_topic_id`)
			WHERE `t`.`forum_topic_id`=?", $topic_id );

		return SK_MySQL::query($query)->fetch_cell();
	}


	/**
	 * Adds a new post and returns post id.
	 *
	 * @param integer $profile_id
	 * @param integer $topic_id
	 * @param string $text
	 * @return integer
	 */
	public static function AddPost( $profile_id, $topic_id, $text )
	{
		$profile_id = intval( $profile_id );
		$topic_id = intval( $topic_id );
		$text = trim( $text );
		if ( $profile_id!=intval( $profile_id ) || !isset($profile_id) || !$topic_id || !strlen($text) || app_Profile::suspended() )
			return 0;
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_FORUM_POST."` (`forum_topic_id`,`profile_id`,`create_stamp`,`text`)
		VALUES(?, ?, ?, '?')", $topic_id, $profile_id, time(), $text );
		SK_MySQL::query($query);

		return SK_MySQL::insert_id();
	}


	/**
	 * Updates a post.
	 *
	 * @param integer $profile_id
	 * @param integer $post_id
	 * @param string $text
	 * @return boolean
	 */
	public static function UpdatePost( $profile_id, $post_id, $text )
	{
		$profile_id = intval( $profile_id );
		$post_id = intval( $post_id );
		$text = trim( $text );
		if ( !$profile_id || !$post_id || !strlen($text ) )
			return false;

		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_POST."` SET `edited_by_profile_id`=?, `edit_stamp`=?, `text`='?'
			WHERE `forum_post_id`=?", $profile_id, time(), $text, $post_id );

		return ( SK_MySQL::query( $query ) );
	}


	/**
	 * Deletes a post.
	 *
	 * @param integer $post_id
	 * @return boolean
	 */
	public static function DeletePost( $post_id )
	{
		$post_id = intval( $post_id );
		if ( !$post_id )
			return false;

		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM_POST."` WHERE `forum_post_id`=?", $post_id );

		return ( SK_MySQL::query($query) )? true : false;
	}


	/**
	 * Returns topic's main(first) post info.
	 *
	 * @param integer $topic_id
	 * @return array
	 */
	public static function getTopicMainPost( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !$topic_id )
			return array();

		$query = SK_MySQL::placeholder( "SELECT `t`.`forum_topic_id`,`t`.`title`,`p`.* FROM `".TBL_FORUM_TOPIC."` AS `t`
			INNER JOIN `".TBL_FORUM_POST."` AS `p` USING(`forum_topic_id`)
			WHERE `t`.`forum_topic_id`=?
			ORDER BY `p`.`create_stamp` ASC LIMIT 1", $topic_id );

		return SK_MySQL::query( $query )->fetch_assoc();
	}


	/**
	 * Checks if the post can be deleted - if it is main post or not.
	 *
	 * @param integer $post_id
	 * @return boolean
	 */
	public static function isDeleteAblePost( $post_id )
	{
		$post_id = intval( $post_id );
		if ( !$post_id )
			return false;

		$query = SK_MySQL::placeholder( "SELECT `p`.`forum_post_id` FROM `".TBL_FORUM_TOPIC."` AS `t`
			INNER JOIN `".TBL_FORUM_POST."` AS `p` USING(`forum_topic_id`)
			WHERE `t`.`forum_topic_id`=(SELECT `forum_topic_id` FROM `".TBL_FORUM_POST."` WHERE `forum_post_id`=?)
			ORDER BY `p`.`create_stamp` ASC LIMIT 1", $post_id );

		$post_info = SK_MySQL::query( $query )->fetch_assoc();

		return ( $post_id != $post_info['forum_post_id'] && $post_info )? true : false;
	}


	/**
	 * Returns page num of the last post (considers order).
	 *
	 * @param integer $topic_id
	 * @param integer $num_on_page
	 * @param string $order
	 * @return integer
	 */
	public static function getLastPostPage( $topic_id, $num_on_page, $order )
	{
		$topic_id = intval( $topic_id );
		$num_on_page = intval( $num_on_page );
		if ( !$topic_id || !$num_on_page )
			return 0;

		return ( $order == 'ASC' )? ceil( self::getPostCount($topic_id)/$num_on_page ) : 1;
	}


	/**
	 * Adds a new topic and returns topic id.
	 *
	 * @param integer $profile_id
	 * @param integer $forum_id
	 * @param string $title
	 * @param string $text
	 * @return integer
	 */
	public static function AddTopic( $profile_id, $forum_id, $title, $text )
	{
		$profile_id = intval( $profile_id );
		$forum_id = intval( $forum_id );
		$title = trim( $title );
		$text = trim( $text );
		if ( !$profile_id || !strlen($forum_id) || !strlen($title) || !strlen($text) )
			return 0;

		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_FORUM_TOPIC."` (`forum_id`,`profile_id`,`create_stamp`,`title`)
			VALUES(?, ?, ?, '?')",$forum_id, $profile_id, time(), $title );
		SK_MySQL::query( $query );
		$_topic_id = SK_MySQL::insert_id();
		//add post
		self::AddPost( $profile_id, $_topic_id, $text );

		return $_topic_id;
	}


	/**
	 * Returns group, forum, topic & post info.
	 *
	 * @param integer $id
	 * @param string $type
	 * @return array
	 */
	public static function  getGroupForumTopicPostInfo( $id, $type = 'group', $refresh = false )
	{
		static $info;

		$id = intval( $id );
		$type = trim( $type );
		if ( !$id || !strlen($type) )
			return array();

		if ( !$refresh )
			switch ( $type )
			{
				case 'topic':
					if ( $info['forum_topic_id'] == $id )
						return $info;
					break;
				case 'post':
					if ( $info['forum_post_id'] == $id )
						return $info;
					break;
				case 'forum':
					if ( $info['forum_id'] == $id )
						return $info;
					break;
				case 'group':
					if ( $info['forum_group_id'] == $id )
						return $info;
			}

		$info = self::getCurrentGroupForumTopicPostInfo( $id, $type );


		return $info ? $info : self::getPostInfo($id);
	}


    public static function  getGroupForumTopicPostInfoList( $list, $type = 'group', $refresh = false )
	{
		$info = self::getCurrentGroupForumTopicPostInfoList( $list, $type );
		return $info;
	}


	/**
	 * Returns group, forum, topic & post info.
	 *
	 * @param integer $id
	 * @param string $type array( 'topic', 'forum', 'group' )
	 * @return array
	 */
	public static function  getCurrentGroupForumTopicPostInfo( $id, $type = 'group' )
	{
		$id = intval( $id );
		if ( !$id || !$type )
			return array();

        if ( isset(self::$postInfoCache[$type][$id]) )
        {
            return self::$postInfoCache[$type][$id];
        }

		switch ( $type )
		{
			case 'post':
				$query = SK_MySQL::placeholder( "SELECT `g`.`name` AS `forum_group_name`,`g`.`forum_group_id`,`f`.`forum_id`,`f`.`name` AS `forum_name`,
					`f`.`description` AS `forum_description`,`f`.`is_closed` AS `forum_is_closed`,`t`.`forum_topic_id`,`t`.`title`,
					`t`.`profile_id` AS `forum_topic_profile_id`,`t`.`create_stamp` AS `forum_topic_create_stamp`,
					`t`.`is_closed` AS `forum_topic_is_closed`,`p`.*
					FROM `".TBL_FORUM_POST."` AS `p`
					INNER JOIN `".TBL_FORUM_TOPIC."` AS `t` USING(`forum_topic_id`)
					INNER JOIN `".TBL_FORUM."` AS `f` USING(`forum_id`)
					LEFT JOIN `".TBL_FORUM_GROUP."` AS `g` USING(`forum_group_id`)
					WHERE `p`.`forum_post_id`=?", $id );
				break;
			case 'topic':
				$query = SK_MySQL::placeholder( "SELECT `g`.`name` AS `forum_group_name`,`g`.`forum_group_id`,`f`.`forum_id`,`f`.`name` AS `forum_name`,
					`f`.`description` AS `forum_description`,`f`.`is_closed` AS `forum_is_closed`,`t`.`forum_topic_id`,`t`.`title`,
					`t`.`profile_id` AS `forum_topic_profile_id`,`t`.`create_stamp` AS `forum_topic_create_stamp`,
					`t`.`is_closed` AS `forum_topic_is_closed`
					FROM `".TBL_FORUM_TOPIC."` AS `t`
					INNER JOIN `".TBL_FORUM."` AS `f` USING(`forum_id`)
					INNER JOIN `".TBL_FORUM_GROUP."` AS `g` USING(`forum_group_id`)
					WHERE `t`.`forum_topic_id`=?", $id );
				break;
			case 'forum':
				$query = SK_MySQL::placeholder( "SELECT `g`.`name` AS `forum_group_name`,`g`.`forum_group_id`,`f`.`forum_id`,`f`.`name` AS `forum_name`,
					`f`.`description` AS `forum_description`,`f`.`is_closed` AS `forum_is_closed`
					FROM `".TBL_FORUM."` AS `f`
					INNER JOIN `".TBL_FORUM_GROUP."` AS `g` USING(`forum_group_id`)
					WHERE `f`.`forum_id`=?", $id );
				break;
			default:
				$query = SK_MySQL::placeholder( "SELECT `g`.`name` AS `forum_group_name`,`g`.`forum_group_id`
					FROM `".TBL_FORUM_GROUP."` AS `g`
					WHERE `g`.`forum_group_id`=?", $id );
		}

		return SK_MySQL::query( $query )->fetch_assoc();
	}

	public static function  getCurrentGroupForumTopicPostInfoList( $idList, $type = 'group' )
	{
		if ( empty($idList) )
			return array();

        $field = '';

		switch ( $type )
		{
			case 'post':
				$query = SK_MySQL::placeholder( "SELECT `g`.`name` AS `forum_group_name`,`g`.`forum_group_id`,`f`.`forum_id`,`f`.`name` AS `forum_name`,
					`f`.`description` AS `forum_description`,`f`.`is_closed` AS `forum_is_closed`,`t`.`forum_topic_id`,`t`.`title`,
					`t`.`profile_id` AS `forum_topic_profile_id`,`t`.`create_stamp` AS `forum_topic_create_stamp`,
					`t`.`is_closed` AS `forum_topic_is_closed`,`p`.*
					FROM `".TBL_FORUM_POST."` AS `p`
					INNER JOIN `".TBL_FORUM_TOPIC."` AS `t` USING(`forum_topic_id`)
					INNER JOIN `".TBL_FORUM."` AS `f` USING(`forum_id`)
					LEFT JOIN `".TBL_FORUM_GROUP."` AS `g` USING(`forum_group_id`)
					WHERE `p`.`forum_post_id` IN (?@ )", $idList );

                $field = 'forum_post_id';
				break;
			case 'topic':
				$query = SK_MySQL::placeholder( "SELECT `g`.`name` AS `forum_group_name`,`g`.`forum_group_id`,`f`.`forum_id`,`f`.`name` AS `forum_name`,
					`f`.`description` AS `forum_description`,`f`.`is_closed` AS `forum_is_closed`,`t`.`forum_topic_id`,`t`.`title`,
					`t`.`profile_id` AS `forum_topic_profile_id`,`t`.`create_stamp` AS `forum_topic_create_stamp`,
					`t`.`is_closed` AS `forum_topic_is_closed`
					FROM `".TBL_FORUM_TOPIC."` AS `t`
					INNER JOIN `".TBL_FORUM."` AS `f` USING(`forum_id`)
					INNER JOIN `".TBL_FORUM_GROUP."` AS `g` USING(`forum_group_id`)
					WHERE `t`.`forum_topic_id` IN (?@ )", $idList );

                $field = 'forum_topic_id';
				break;
			case 'forum':
				$query = SK_MySQL::placeholder( "SELECT `g`.`name` AS `forum_group_name`,`g`.`forum_group_id`,`f`.`forum_id`,`f`.`name` AS `forum_name`,
					`f`.`description` AS `forum_description`,`f`.`is_closed` AS `forum_is_closed`
					FROM `".TBL_FORUM."` AS `f`
					INNER JOIN `".TBL_FORUM_GROUP."` AS `g` USING(`forum_group_id`)
					WHERE `f`.`forum_id` IN (?@ )", $idList );

                $field = 'forum_id';
				break;
			default:
				$query = SK_MySQL::placeholder( "SELECT `g`.`name` AS `forum_group_name`,`g`.`forum_group_id`
					FROM `".TBL_FORUM_GROUP."` AS `g`
					WHERE `g`.`forum_group_id` IN (?@ )", $idList );

                $field = 'forum_group_id';
		}

        $valueList = array();
        $profileIdList = array();

        $result = SK_MySQL::query( $query );

        while( $row =  $result->fetch_assoc())
        {
            $valueList[$row[$field]] = $row;
            self::$postInfoCache[$type][$row[$field]] = $row;
        }

		return $valueList;
	}

	/**
	 * Bans a profile.
	 *
	 * @param integer $profile_id
	 * @param integer $for_time - expiration stamp of the ban
	 * @return boolean
	 */
	public static function  BanProfile( $profile_id, $for_time=null  )
	{
		$profile_id = intval( $profile_id );
		if ( !$for_time )
			$for_time = SK_Config::section('forum')->get('ban_period');
		$for_time = intval( $for_time );
		if ( !$profile_id )
			return false;
		$query = SK_MySQL::placeholder( "SELECT `expiration_stamp` FROM `".TBL_FORUM_BANNED_PROFILE."` WHERE `profile_id`=?", $profile_id );

		if ( SK_MySQL::query( $query )->fetch_cell() )
		{
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_BANNED_PROFILE."` SET `expiration_stamp`=`expiration_stamp`+?
				WHERE `profile_id`=?", $for_time, $profile_id );
			SK_MySQL::query( $query );

			return ( SK_MySQL::affected_rows() )? true : false;
		}
		$for_time+= time();
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_FORUM_BANNED_PROFILE."` (`profile_id`,`expiration_stamp`) VALUES(?, ?)", $profile_id, $for_time );

		return ( !SK_MySQL::query( $query ) )? false : true;
	}


	/**
	 * Checks if a profile was banned.
	 *
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function isProfileBanned( $profile_id, $refresh = false )
	{
		static $profiles;

		$profile_id = intval( $profile_id );

		if ( !$profile_id ) //guests
			return false;

		if ( !$profiles[$profile_id] || $refresh )
		{
			$query = SK_MySQL::placeholder( "SELECT `expiration_stamp` FROM `".TBL_FORUM_BANNED_PROFILE."` WHERE `profile_id`=?", $profile_id );
			$expiration_stamp = SK_MySQL::query( $query )->fetch_assoc();

			$profiles[$profile_id] = ( $expiration_stamp > time() )? $expiration_stamp : 'no';
		}

		return ( $profiles[$profile_id] == 'no' )? false : $profiles[$profile_id];
	}


	/**
	 * Checks if a profile was banned.
	 *
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function  getBannedProfiles( $num_on_page = 10, $page = 1, $sort_by = 'username' )
	{
		$num_on_page = intval( $num_on_page )? intval( $num_on_page ) : 10;
		$page = intval( $page )? intval( $page ) : 1;

		$sort_by_q = ( $sort_by == 'username' )? 'username' : 'expiration_stamp';

		$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_FORUM_BANNED_PROFILE."`
			WHERE `expiration_stamp`>? LIMIT ?, ?", time(), ($num_on_page*$page - $num_on_page), $num_on_page );
		$query_result = SK_MySQL::query( $query );

        $valueList = array();
        $profileIdList = array();
		while ( $row = $query_result->fetch_assoc() )
		{
            $valueList[] = $row;
            $profileIdList[] = $row['profile_id'];
        }

        app_Profile::checkProfileDeletedForUsers($profileIdList);
        app_Profile::getUsernamesForUsers($profileIdList);

        foreach( $valueList as $row )
        {
			$row['is_deleted'] = app_Profile::isProfileDeleted($row['profile_id']);
			$row['username'] = app_Profile::username($row['profile_id']);
			$profiles[] = $row;
		}
		//get total count
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM `".TBL_FORUM_BANNED_PROFILE."` WHERE `expiration_stamp`>?", time() );
		$total = SK_MySQL::query( $query )->fetch_cell();

		return array( 'profiles' => $profiles, 'total' => $total );
	}


	/**
	 * Approve a profile.
	 *
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function  RemoveProfileBan( $profile_id )
	{
		$profile_id = intval($profile_id);
		if( !$profile_id )
			return false;
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM_BANNED_PROFILE."` WHERE `profile_id`=?", $profile_id );
		SK_MySQL::query($query);

		return SK_MySQL::affected_rows()? true : false;
	}


	public static function getBanPeriods()
	{
		$section = new SK_Config_Section('forum');
		$periods = $section->getConfigValues('ban_period');
		foreach ( $periods as $period ){
			$langs = SK_Language::section('forms.forum_ban_profile.fields.period');
			$period_arr[$langs->text($period['value'])] = $period['value'];
		}

		return $period_arr;
	}

	/**
	 * Checks if a topic was closed.
	 *
	 * @param integer $topic_id
	 * @return boolean
	 */
	public static function isTopicClosed( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !$topic_id )
			return false;

		$topic_info = self::getGroupForumTopicPostInfo( $topic_id, 'topic' );

		return $topic_info['forum_topic_is_closed'] == 'y' ? true : false;
	}


	/**
	 * Checks if a forum was closed.
	 *
	 * @param integer $forum_id
	 * @return boolean
	 */
	public static function isForumClosed( $forum_id )
	{
		$forum_id = intval( $forum_id );
		if ( !$forum_id )
			return false;

		$forum_info = self::getGroupForumTopicPostInfo( $forum_id, 'forum' );

		return $forum_info['forum_is_closed'] == 'y' ? true : false;
	}


	/**
	 * Closes a topic.
	 *
	 * @param integer $topic_id
	 * @return boolean
	 */
	public static function CloseTopic( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !intval($topic_id) )
			return false;
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_TOPIC."` SET `is_closed`='y' WHERE `forum_topic_id`=?", $topic_id );
		SK_MySQL::query($query);

		return SK_MySQL::affected_rows()? true : false;
	}


	/**
	 * Closes a forum.
	 *
	 * @param integer $topic_id
	 * @return boolean
	 */
	public static function OpenTopic( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !intval($topic_id) )
			return false;
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_TOPIC."` SET `is_closed`='n' WHERE `forum_topic_id`=?", $topic_id );
		SK_MySQL::query($query);

		return SK_MySQL::affected_rows()? true : false;
	}

	/**
	 * Set sticky a topic.
	 *
	 * @param integer $topic_id
	 * @return boolean
	 */
	public static function stickyTopic( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !intval($topic_id) )
			return false;
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_TOPIC."` SET `is_sticky`='y' WHERE `forum_topic_id`=?", $topic_id );
		SK_MySQL::query($query);

		return SK_MySQL::affected_rows()? true : false;
	}

	/**
	 * Set unsticky a topic.
	 *
	 * @param integer $topic_id
	 * @return boolean
	 */
	public static function unstickyTopic( $topic_id )
	{
		$topic_id = intval( $topic_id );
		if ( !intval($topic_id) )
			return false;
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_TOPIC."` SET `is_sticky`='n' WHERE `forum_topic_id`=?", $topic_id );
		SK_MySQL::query($query);

		return SK_MySQL::affected_rows()? true : false;
	}

	/**
	 * Returns a profile forum stat.
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public static function  getProfileStat( $profile_id )
	{
		$profile_id = intval( $profile_id );
		if ( !$profile_id )
			return array();

		//get profile's count of topics
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM `".TBL_FORUM_TOPIC."` WHERE `profile_id`=?", $profile_id );
		$return['topic_count'] = SK_MySQL::query($query)->fetch_cell();
		//get the profile's count of posts
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM `".TBL_FORUM_POST."` WHERE `profile_id`=?", $profile_id );
		$return['post_count'] = SK_MySQL::query($query)->fetch_cell();
		//get the profile's count of edited posts
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM `".TBL_FORUM_POST."` WHERE `edited_by_profile_id`=? AND `profile_id`!=?", $profile_id, $profile_id );
		$return['edited_post_count'] = SK_MySQL::query($query)->fetch_cell();
		//check if the profile is banned
		$return['is_banned'] = (int)self::isProfileBanned($profile_id);

		return $return;
	}

	/**
	 * Returns a profile forum stat.
	 *
	 * @param integer $profile_id
	 * @return array
	 */
	public static function  getProfilePosts( $profile_id )
	{
		$profile_id = intval( $profile_id );
		if ( !$profile_id )
			return array();

		$configs = new SK_Config_Section('forum');

		$query = SK_MySQL::placeholder( "SELECT `t`.`title`, `p`.`text`, `p`.`forum_post_id`, `p`.`create_stamp`, `p`.`profile_id`
			FROM `".TBL_FORUM_TOPIC."` AS `t`
			LEFT JOIN `".TBL_FORUM."` as `f` ON(`f`.`forum_id`=`t`.`forum_id`)
			LEFT JOIN
			(
				SELECT `text`, `forum_post_id`, `create_stamp`, `profile_id`, `forum_topic_id`
			    FROM `".TBL_FORUM_POST."`
			 	WHERE `profile_id`=?  ORDER BY `create_stamp` DESC
			) AS `p` ON(`p`.`forum_topic_id`=`t`.`forum_topic_id`)
			WHERE `p`.`profile_id` IS NOT NULL
			AND `f`.`group_id` IS NULL
			GROUP BY `p`.`forum_topic_id` ORDER BY `p`.`create_stamp` DESC
			LIMIT ?", $profile_id, $configs->profile_post_count );
		$query_result = SK_MySQL::query($query);

		while ($row = $query_result->fetch_assoc()) {

			$row['text'] = nl2br( SK_Language::htmlspecialchars( $row['text'] ) );
			$row['text'] = self::forumTagsToHtmlChars($row['text']);
			$row['text'] = strip_tags($row['text']);

			$row['text'] = ( SK_UTF8::strlen($row['text'])>($configs->topic_text_truncate + 1) ) ? SK_UTF8::substr($row['text'], 0, $configs->topic_text_truncate).'...' : $row['text'];
			$row['title'] = ( SK_UTF8::strlen($row['title'])>($configs->topic_title_truncate + 1) ) ? SK_UTF8::substr($row['title'], 0, $configs->topic_title_truncate).'...' : $row['title'];
			$row['title'] = app_TextService::stCensor( $row['title'], 'forum', true );
			$row['post_url'] = self::getPostURL($row['forum_post_id']);
			$return[] = $row;
		}

		return $return;
	}

	public static function getProfilePostCount( $profile_id )
	{
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM `".TBL_FORUM_POST."` WHERE `profile_id`=?", $profile_id );
		$query_result = SK_MySQL::query($query);

		return $query_result->fetch_cell();
	}


	/**
	 * Moves topic.
	 *
	 * @param integer $topic_id
	 * @param integer $to_forum_id
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function MoveTopic( $topic_id, $to_forum_id, $profile_id )
	{
		$topic_id = intval( $topic_id );
		$to_forum_id = intval( $to_forum_id );
		$profile_id = intval( $profile_id );
		if ( !$topic_id || !$to_forum_id || !$profile_id )
			return false;

		$topic_info = self::getTopic( $topic_id );
		if ( !$topic_info )
			return false;

		if ( $topic_info['forum_id'] == $to_forum_id )
			return false;
		$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM_TOPIC."` SET `forum_id`=?,`moved_by_profile_id`=?,`moved_from_forum_id`=?,`moved_time_stamp`=?
			WHERE `forum_topic_id`=?", $to_forum_id, $profile_id, $topic_info['forum_id'], time(), $topic_id );
		SK_MySQL::query( $query );

		return SK_MySQL::affected_rows();
	}

	public static function ReplaceTopic( $topic_id, $profile_id )
	{
		$topic_info = self::getTopic( $topic_id );

		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_FORUM_TOPIC."` (`forum_id`,`profile_id`,`create_stamp`,`title`, `status`, `is_closed`)
			VALUES(?, ?, ?, '?', 'y', 'y') ", $topic_info['moved_from_forum_id'], $profile_id, time(), $topic_info['title'] );
		SK_MySQL::query( $query );

		$lang_msg = SK_Language::section('components.forum_topic.messages');

		$old_topic = SK_Navigation::href('topic', array('topic_id'=>$topic_id));
		$old_topic = str_replace( SITE_URL, '/', $old_topic );
		$old_topic_text = $lang_msg->text('topic_moved_to_text');

		$old_topic_text = str_replace( '{$link}', $old_topic, $old_topic_text );

		self::AddPost( $profile_id, SK_MySQL::insert_id(), $old_topic_text );

		$old_forum_link = SK_Navigation::href('forum', array('forum_id'=>$topic_info['moved_from_forum_id']));
		$old_forum_link = str_replace( SITE_URL, '/', $old_forum_link );
		$new_topic_text = $lang_msg->text('topic_moved_from_text');
		$forum = self::getForumInfo($topic_info['moved_from_forum_id']);

		$new_topic_text = str_replace( '{$link}', $old_forum_link, $new_topic_text );
		$new_topic_text = str_replace( '{$forum_name}', $forum['name'], $new_topic_text );

		self::AddPost( $profile_id, $topic_id, $new_topic_text );

	}

	public static function deleteReplaceTopics()
	{
		$configs = new SK_Config_Section('forum');
		$expiration_time = time() - intval($configs->replace_topic_expr_time) * 3600;

		$query = SK_MySQL::placeholder( "SELECT `forum_topic_id` FROM `".TBL_FORUM_TOPIC."`
			WHERE `status`='y' AND (`create_stamp`) < ?", $expiration_time );

		$query_result = SK_MySQL::query( $query );

		while ( $value = $query_result->fetch_assoc() ) {
			self::DeleteTopic( $value['forum_topic_id'] );
		}
	}

	public static function subscribeProfile($profile_id, $topic_id)
	{
		$query = SK_MySQL::placeholder( "INSERT INTO `".TBL_FORUM_PROFILE_NOTIFY."` (`profile_id`, `topic_id`)
			VALUES(?,?)", $profile_id, $topic_id );
		MySQL::query($query);
	}

	public static function unSubscribeProfile($profile_id, $topic_id)
	{
		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM_PROFILE_NOTIFY."` WHERE `profile_id`=?
			AND `topic_id`=?", $profile_id, $topic_id );
		MySQL::query($query);
	}

	public static function sendProfileNotifies($profile_id, $topic_id, $post_url)
	{
		$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_FORUM_PROFILE_NOTIFY."` WHERE `profile_id`!=? AND `topic_id`=?",
			$profile_id, $topic_id );
		$query_result = SK_MySQL::query( $query );

		$topic_info = self::getTopic( $topic_id );

		$assigned_vars = array(
								'poster'	  => app_Profile::username( $profile_id ),
								'post_url'	  => $post_url,
								'topic_title' => $topic_info['title']
							   );

		while( $row = $query_result->fetch_assoc() )
		{
            if ( !app_Unsubscribe::isProfileUnsubscribed($row['profile_id']) )
            {
                $msg = app_Mail::createMessage(app_Mail::NOTIFICATION)
                        ->setRecipientProfileId($row['profile_id'])
                        ->setTpl('forum_update_notify')
                        ->assignVarRange($assigned_vars);
                app_Mail::send($msg);
            }
		}
	}

	public static function isProfileSubscribed($profile_id, $topic_id)
	{
		$query = SK_MySQL::placeholder( "SELECT `profile_id` FROM `".TBL_FORUM_PROFILE_NOTIFY."` WHERE `profile_id`=? AND `topic_id`=?",
			$profile_id, $topic_id );
		$query_result = SK_MySQL::query($query);
		return ( $query_result->fetch_cell() ) ? true : false ;
	}

	/**
	 * Returns Forum statistics
	 *
	 * @return array
	 */
	public static function  getForumStatistics()
	{
		//get count of groups
		$query_result = SK_MySQL::query( "SELECT COUNT(*) FROM `".TBL_FORUM_GROUP."` WHERE 1" );
		$return['group_count'] = $query_result->fetch_cell();
		//get count of forums
		$query_result = SK_MySQL::query( "SELECT COUNT(*) FROM `".TBL_FORUM."` WHERE 1" );
		$return['forum_count'] = $query_result->fetch_cell();
		//get count of closed forums
		$query_result = SK_MySQL::query( "SELECT COUNT(*) FROM `".TBL_FORUM."` WHERE `is_closed`='y'" );
		$return['forum_closed_count'] = $query_result->fetch_cell();
		//get count of topics
		$query_result = SK_MySQL::query( "SELECT COUNT(*) FROM `".TBL_FORUM_TOPIC."` WHERE 1" );
		$return['topic_count'] = $query_result->fetch_cell();
		//get count of closed topics
		$query_result = SK_MySQL::query( "SELECT COUNT(*) FROM `".TBL_FORUM_TOPIC."` WHERE `is_closed`='y'" );
		$return['topic_closed_count'] = $query_result->fetch_cell();
		//get count of posts
		$query_result = SK_MySQL::query( "SELECT COUNT(*) FROM `".TBL_FORUM_POST."` WHERE 1" );
		$return['post_count'] = $query_result->fetch_cell();
		//get count of edited posts
		$query_result = SK_MySQL::query( "SELECT COUNT(*) FROM `".TBL_FORUM_POST."` WHERE `edited_by_profile_id`!=''" );
		$return['post_edited_count'] = $query_result->fetch_ceil();
		//get count of banned profiles
		$query = SK_MySQL::placeholder( "SELECT COUNT(*) FROM `".TBL_FORUM_BANNED_PROFILE."` WHERE `expiration_stamp`>?", time() );
		$return['banned_profile_count'] = SK_MySQL::query($query)->fetch_cell();

		return $return;
	}


	/**
	 * Returns Forum html conts' id
	 *
	 * @param integer $id
	 * @param string $type
	 * @return string
	 */
	public static function  getForumObjectId( $id, $type='forum' )
	{
		if ( !$id )
			return false;

		switch ( $type )
		{
			case 'forum':
				return 'forum_'.$id;
			case 'topic':
				return 'forum_topic_'.$id;
			case 'topic_list':
				return 'topic_list_'.$id;
			case 'move_topic':
				return 'forum_topic_move_cont_'.$id;
			case 'post_list':
				return 'forum_post_list_'.$id;
			case 'post':
				return 'forum_post_'.$id;
			case 'post_replace':
				return 'forum_post_replace_cont_'.$id;
			default:
				return false;
		}
	}


	/**
	 * Returns search result
	 *
	 * @param array $keywords
	 * @param array|string $forums
	 * @param integer $num_on_page
	 * @param integer $page
	 * @return string
	 */
	public static function  getSearchResult( $keywords, $forums='all', $num_on_page=10, $page=1 )
	{
		if ( !is_array($keywords) || (!is_array($forums) && $forums!='all') || !$keywords || !$forums )
			return array();
		$page = (!$page) ? 1 : $page;

		foreach ( $keywords as $key=>$keyword ) {
			if ( strlen($keyword) < 2 ) {
				unset( $keywords[$key] );
				continue;
			}

			if ( substr_count('url', $keyword) ) {
				$keyword_q .= " AND (`text` LIKE 'url%' OR `text` LIKE '% url%' OR `text` LIKE 'url %')";
				unset( $keywords[$key] );
			}
			elseif ( substr_count('quote', $keyword) ) {
				$keyword_q .= SK_MySQL::placeholder( "AND (`text` LIKE '?' OR `text` LIKE '?' OR `text` LIKE '?')",
					trim($keyword).'%', '% '.trim($keyword).'%', trim($keyword).' %' );
				unset( $keywords[$key] );
			}
			elseif ( substr_count('name', $keyword) ) {
				$keyword_q .= SK_MySQL::placeholder( "AND (`text` LIKE '?' OR `text` LIKE '?')",
					trim($keyword).'%', '% '.trim($keyword).' %' );
				unset( $keywords[$key] );
			}
			elseif ( substr_count('date', $keyword) ) {
				$keyword_q .= SK_MySQL::placeholder( "AND (`text` LIKE '?' OR `text` LIKE '?')",
					trim($keyword).'%', '% '.trim($keyword).' %' );
			}
			else {
				$keyword_q.= SK_MySQL::placeholder( " AND `text` LIKE '?'",
					'%'.trim($keyword).'%' );
			}
		}

		if ( !$keyword_q ) {
			return array();
		}


		if ( $forums!='all' )
		{
			foreach ( $forums as $_forum ) {
				if ( !$_forum ) {
					continue;
				}
				$forum_q.= "$_forum,";
			}
			$forum_q = "`f`.`forum_id` IN (".substr( $forum_q, 0, -1 ).")";
		}
		else
			$forum_q = '1';

		$query_result = SK_MySQL::query( "SELECT COUNT(*)
			FROM `".TBL_FORUM_POST."` AS `p`
			INNER JOIN `".TBL_FORUM_TOPIC."` AS `t` USING(`forum_topic_id`)
			INNER JOIN `".TBL_FORUM."` AS `f` USING(`forum_id`)
			INNER JOIN `".TBL_FORUM_GROUP."` AS `g` USING(`forum_group_id`)
			WHERE $forum_q $keyword_q" );
		$result['total'] = $query_result->fetch_cell();

		$query = SK_MySQL::placeholder( "SELECT `g`.`name` AS `forum_group_name`,`g`.`forum_group_id`,`f`.`forum_id`,
			`f`.`name` AS `forum_name`,`f`.`description` AS `forum_description`,`f`.`is_closed` AS `forum_is_closed`,
			`t`.`forum_topic_id`,`t`.`title`,`t`.`profile_id` AS `forum_topic_profile_id`,
			`t`.`create_stamp` AS `forum_topic_create_stamp`,`t`.`is_closed` AS `forum_topic_is_closed`,`p`.*
			FROM `".TBL_FORUM_POST."` AS `p`
			INNER JOIN `".TBL_FORUM_TOPIC."` AS `t` USING(`forum_topic_id`)
			INNER JOIN `".TBL_FORUM."` AS `f` USING(`forum_id`)
			INNER JOIN `".TBL_FORUM_GROUP."` AS `g` USING(`forum_group_id`)
			WHERE $forum_q $keyword_q ORDER BY `p`.`create_stamp` DESC LIMIT ?, ?", ( ($page - 1)*$num_on_page ), $num_on_page );
		$query_result = SK_MySQL::query($query );

        $valueList = array();
        $profileIdList = array();
        $postIdList = array();
		while ( $row = $query_result->fetch_assoc() )
		{
            $valueList[] = $row;
            $profileIdList[] = $row['profile_id'];
            $postIdList[] = $row['forum_post_id'];
        }

        app_Profile::getUsernamesForUsers($profileIdList);
        app_Profile::checkProfileDeletedForUsers($profileIdList);
        self::getPostUrlList($postIdList);

        foreach( $valueList as $row )
        {
			$row['title'] = app_TextService::stCensor( $row['title'], 'forum', true );
			$row['text'] = nl2br( SK_Language::htmlspecialchars( $row['text'] ) );
			$row['text'] = self::forumTagsToHtmlChars( $row['text']);

			//highlight keywords
			foreach ( $keywords as $keyword ) {
				$row['text'] = str_replace($keyword, '<span style="color: green; font-weight: bold;">'.$keyword.'</span>', $row['text']);
			}
			$row['is_deleted'] = app_Profile::isProfileDeleted( $row['profile_id'] );
			$row['username'] = app_Profile::username( $row['profile_id'] );
			$row['post_url'] = self::getPostURL( $row['forum_post_id'] );

			$result['posts'][] = $row;
		}

		return $result;
	}


	/**
	 * Returns Forums of a Group
	 *
	 * @param integer $group_id
	 * @return array
	 */
	public static function  getForumsOfGroup( $group_id )
	{
		$group_id = intval( $group_id );
		if ( !$group_id )
			return array();
		$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_FORUM."` WHERE `forum_group_id`=?", $group_id );
		$query_result = SK_MySQL::query($query);
		while ( $row = $query_result->fetch_assoc() )
			$return[] = $row;

		return $return;
	}


	/**
	 * Deletes profile from Forum
	 *
	 * @param integer $profile_id
	 * @return boolean
	 */
	public static function  deleteProfile( $profile_id )
	{
		$profile_id = intval( $profile_id );
		if ( !$profile_id )
			return false;

		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM_BANNED_PROFILE."` WHERE `profile_id`=?", $profile_id );
		SK_MySQL::query( $query );
		$profile_result = SK_MySQL::affected_rows();

		$query = SK_MySQL::placeholder( "DELETE FROM `".TBL_FORUM_PROFILE_NOTIFY."` WHERE `profile_id`=?", $profile_id );
		SK_MySQL::query( $query );
		$profile_result += SK_MySQL::affected_rows();

		return $profile_result;
	}


	/**
	 * Returns post's url
	 *
	 * @param integer $post_id
	 * @return string
	 */
	public static function  getPostURL( $post_id )
	{
		$post_id = intval( $post_id );

        if (  isset(self::$postUrlCache[$post_id]) )
        {
            return self::$postUrlCache[$post_id];
        }

		$post_info = self::getGroupForumTopicPostInfo( $post_id, 'post' );

		if ( !$post_info )
			return '';
		$query = SK_MySQL::placeholder( "SELECT `forum_post_id` FROM `".TBL_FORUM_POST."`
			WHERE `forum_topic_id`=? ORDER BY `create_stamp` ASC", $post_info['forum_topic_id'] );
		$query_result = SK_MySQL::query( $query );
		while ( $row = $query_result->fetch_assoc() )
			$topic_posts[] = $row['forum_post_id'];

		$key = array_search( $post_info['forum_post_id'], $topic_posts ) + 1;
		$configs = new SK_Config_Section('forum');

		$_page = ceil( $key / $configs->post_count_on_page );

		return SK_Navigation::href( 'topic', array( 'topic_id'=>$post_info['forum_topic_id'], 'page'=>$_page) );
	}

    public static function  getPostUrlList( $post_id_list )
	{
        if ( empty($post_id_list) )
        {
            return array();
        }

        $post_info_list = self::getGroupForumTopicPostInfoList( $post_id_list, 'post' );
        $topicIdList = array();

        $postList = array();
        foreach( $post_info_list as $post_info )
        {
            $topicIdList[$post_info['forum_topic_id']] = $post_info['forum_topic_id'];
            $postList[$post_info['forum_post_id']] = $post_info;
        }

        if ( empty($topicIdList) )
        {
            return array();
        }

        $query = SK_MySQL::placeholder( "SELECT `forum_post_id`, `forum_topic_id` FROM `".TBL_FORUM_POST."`
			WHERE `forum_topic_id` IN ( ?@ ) ORDER BY `create_stamp` ASC", $topicIdList );

		$query_result = SK_MySQL::query( $query );
		while ( $row = $query_result->fetch_assoc() )
        {
			$topic_posts[$row['forum_topic_id']][] = $row['forum_post_id'];
        }

        $result = array();
        foreach ( $post_id_list as $post_id )
        {
            $post_info = $postList[$post_id];

            if ( !$post_info )
            {
                $result[$post_info['forum_post_id']] = '';
                self::$postUrlCache[$post_info['forum_post_id']] = $result[$post_info['forum_post_id']];
                continue;
            }

            $key = array_search( $post_info['forum_post_id'], $topic_posts[$post_info['forum_topic_id']] ) + 1;
            $configs = new SK_Config_Section('forum');

            $_page = ceil( $key / $configs->post_count_on_page );

            $result[$post_info['forum_post_id']] = SK_Navigation::href( 'topic', array( 'topic_id'=>$post_info['forum_topic_id'], 'page'=>$_page) );

            self::$postUrlCache[$post_info['forum_post_id']] = $result[$post_info['forum_post_id']];
        }

		return $result;
	}


	public static function  getLastTopicList($count = null)
	{
		$configs = new SK_Config_Section('forum');
	    $count = empty($count) ? $configs->last_topic_count : $count;

		$query = SK_MySQL::placeholder( "SELECT `p`.`text`, `p`.`forum_post_id`, `p`.`profile_id`, `p`.`create_stamp`, `t`.`forum_topic_id`,
			`t`.`title` FROM `".TBL_FORUM_POST."` as `p` LEFT JOIN `".TBL_FORUM_TOPIC."` as `t` USING(`forum_topic_id`)
			LEFT JOIN `".TBL_FORUM."` as `f` ON(`f`.`forum_id`=`t`.`forum_id`) WHERE `f`.`group_id` IS NULL
			ORDER BY `p`.`create_stamp` DESC LIMIT ?", $count );
		$query_result = SK_MySQL::query( $query );
		while ( $row = $query_result->fetch_assoc() ) {
			$row['text'] = ( SK_UTF8::strlen($row['text'])>($configs->topic_text_truncate + 1) ) ? SK_UTF8::substr($row['text'], 0, $configs->topic_text_truncate).'...' : $row['text'];
			$row['text'] = nl2br( SK_Language::htmlspecialchars( $row['text'] ) );
			$row['text'] = self::forumTagsToHtmlChars($row['text']);
			$row['title'] = ( SK_UTF8::strlen($row['title'])>($configs->topic_title_truncate + 1) ) ? SK_UTF8::substr($row['title'], 0, $configs->topic_title_truncate).'...' : $row['title'];
			$row['title'] = app_TextService::stCensor( $row['title'], 'forum', true );
			$row['is_deleted'] = app_Profile::isProfileDeleted( $row['profile_id'] );
			$row['username'] = 	app_Profile::username( $row['profile_id'] );
			$result[] = $row;
		}
		return $result;
	}

	public static function getAdminForumGroupList()
	{
		$query = "SELECT `g`.`forum_group_id`, `g`.`name` AS `group_name`, `g`.`order` AS `group_order`,
			`f`.`forum_id`, `f`.`name`, `f`.`description`, `f`.`order`
			FROM `".TBL_FORUM_GROUP."` AS `g` LEFT JOIN `".TBL_FORUM."` as `f` USING(`forum_group_id`)
			ORDER BY `g`.`order`, `f`.`order`;";
		$query_result = SK_MySQL::query($query);
		while ($row = $query_result->fetch_assoc())
		{
			if ( !$group['forum_group_id'] || $group['forum_group_id']!=$row['forum_group_id'])
			{
				if (isset($index)) {
					$index++;
				}
				else {
					$index = 0;
				}
				$group['forum_group_id'] = $row['forum_group_id'];
				$group['name'] 			 = $row['group_name'];
				$group['order'] 		 = $row['group_order'];
				$return[$index]          = $group;
			}

			$forum['forum_id']    = $row['forum_id'];
			$forum['name']     	  = $row['name'];
			$forum['description'] = $row['description'];
			$forum['order'] 	  = $row['order'];

			if ($forum['forum_id']) {
				$return[$index]['forums'][] = $forum;
			}
		}
        if (empty($return))
            return array();
		return $return;
	}

	public static function  SortArray( $array, $field, $order = 'ASC' )
	{
		if ( !is_array($array) )
			return array();

		if ( $order == 'ASC' )
		{
			for ( $i=0; $i<count($array)-1; $i++)
				for( $j=0; $j<count($array)-$i-1; $j++)
				{
					if ( $array[$j+1][$field] < $array[$j][$field] )
					{
						$_tmp = $array[$j+1];
						$array[$j+1] = $array[$j];
						$array[$j] = $_tmp;
					}
				}
		}
		else
		{
			for ( $i=0; $i<count($array)-1; $i++)
				for( $j=0; $j<count($array)-$i-1; $j++)
				{
					if ( $array[$j+1][$field] > $array[$j][$field] )
					{
						$_tmp = $array[$j+1];
						$array[$j+1] = $array[$j];
						$array[$j] = $_tmp;
					}
				}
		}

		return $array;
	}

	public static function getQuoteDate( $timestamp = false )
	{
		if ( $timestamp ) {
			return date( "M j, Y G:i", $timestamp );
		}
		return date( "M j, Y G:i" );
	}

	public static function getPostInfo( $post_id )
	{
		if ( !$post_id ) {
			return false;
		}

		$query = SK_MySQL::placeholder( "SELECT * FROM `".TBL_FORUM_POST."`
			WHERE `forum_post_id`=?", $post_id );
		$result = SK_MySQL::query($query);

		return $result->fetch_assoc();
	}

	public static function forumTagsToHtmlChars( $text )
	{
        $url_reg = '#(\[url=[\'"]?([^\s]*?)[\'"]?\])(.*?)(\[/url\])#i';
		$image_reg = '#\[img[\s]*(title="(.*?)")?[\s]*\](.*?)\[/img\]#i';
		$quote_reg = "#\[quote\sname=.?(.*?).?\sdate=.?(.*?).?\]#i";
		//badwords censor
		$text = app_TextService::stCensor($text, 'forum');

		//replace url tag
		$text = str_replace( '&quot;', '"', $text );
		if ( preg_match_all( $url_reg, $text, $text_arr ) )
		{
			foreach ( $text_arr[0] as $key=>$value )
			{
                if ( !strncmp($text_arr[2][$key], '/forum/', strlen('/forum/')) )
                {
                    $text_arr[2][$key] = rtrim(SITE_URL, "/") . $text_arr[2][$key];
                }
				$url = '<a href="'.$text_arr[2][$key].'" target="_blank">'.$text_arr[3][$key].'</a>';
				$text = str_replace( $value, $url, $text );
			}
		}

	   if ( preg_match_all( $image_reg, $text, $text_arr ) )
        {
            foreach ( $text_arr[0] as $key=>$value )
            {
                $img = '<img src="'.$text_arr[3][$key].'" title="'.$text_arr[2][$key].'" />';
                $text = str_replace( $value, $img, $text );
            }
        }

		//replace quote tag
		if ( preg_match_all( $quote_reg, $text, $text_arr ) );
		{
			$key = 0;
			foreach ( $text_arr[0] as $key=>$value )
			{
				$quote = '<div class="quote"><div class="quotetop">'.
				    SK_Language::section('components.forum_add_post')->text('quote').
				    ' ('.$text_arr[1][$key].' * '.$text_arr[2][$key].')</div><div class="quotemain">';
				$text = str_replace( $value, $quote, $text );
			}

			$is_closed = $key - substr_count( $text, '[/quote]' ) - 1;
			$text = str_replace( '[/quote]', '</div></div>', $text );

			if( $is_closed && $is_closed>0 )
			{
				for ($i=0; $is_closed>$i; $i++)
					$text .= "</div></div>";
			}

		}

		//replace bbtags
		$bb_tags = array('b', 'i', 'u');
		foreach ( $bb_tags as $tag )
		{
			$text = str_replace( "[$tag]", "<$tag>", $text );
			$text = str_replace( "[/$tag]", "</$tag>", $text );

			$is_closed = substr_count( $text, "<$tag>" ) - substr_count( $text, "</$tag>" );
			if( $is_closed && $is_closed>0 )
			{
				for ($i=0; $is_closed>$i; $i++)
					$text .= "</$tag>";
			}
		}

		return $text;
	}
}
?>