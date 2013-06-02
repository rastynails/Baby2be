<?php

function getReports( $type, $profile_id = 0 )
{
	if ( strlen($type) == 0 )
		return array();
		
	switch ( $type )
	{
		case 'profile':
			$profile_query = ($profile_id == 0) ? "" : " AND `p2`.`profile_id`=" . $profile_id;

			$reports = MySQL::fetchArray( "SELECT `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `p2`.`profile_id` AS `owner_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`p2`.`profile_id` = `r`.`entity_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query );
			break;
			
		case 'photo':
			$profile_query = ($profile_id == 0) ? "" : " AND `photo`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `photo`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_PROFILE_PHOTO."` AS `photo` ON(`photo`.`photo_id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`photo`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query );
			break;
			
		case 'video':
			$profile_query = ($profile_id == 0) ? "" : " AND `m`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `m`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_PROFILE_VIDEO."` AS `m` ON(`m`.`video_id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`m`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query );
			break;
			
		case 'forum':
			$profile_query = ($profile_id == 0) ? "" : " AND `f`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `f`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_FORUM_POST."` AS `f` ON(`f`.`forum_post_id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`f`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query );
			break;
			
		case 'blog':
			$profile_query = ($profile_id == 0) ? "" : " AND `b`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `b`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_BLOG_POST."` AS `b` ON(`b`.`id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`b`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query );
			break;
			
		case 'classifieds':
			$profile_query = ($profile_id == 0) ? "" : " AND `b`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `b`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_CLASSIFIEDS_ITEM."` AS `b` ON(`b`.`id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`b`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query );
			break;
			
		case 'message':
			$profile_query = ($profile_id == 0) ? "" : " AND `p2`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `p2`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_MAILBOX_MESSAGE."` AS `m` ON(`m`.`message_id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`m`.`sender_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query );
			break;
			
	}
	

	return $reports;
}

function getReportsGroupedByEntity( $type, $profile_id = 0 )
{
	if ( strlen($type) == 0 )
		return array();
		
	switch ( $type )
	{
		case 'profile':
			$profile_query = ($profile_id == 0) ? "" : " AND `p2`.`profile_id`=" . $profile_id;

			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`r`.`reporter_id`) as `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `p2`.`profile_id` AS `owner_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`p2`.`profile_id` = `r`.`entity_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query ." GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
		case 'photo':
			$profile_query = ($profile_id == 0) ? "" : " AND `photo`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`photo`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `photo`.`profile_id` as `owner_id`, `photo`.`index` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_PROFILE_PHOTO."` AS `photo` ON(`photo`.`photo_id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`photo`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query ." GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
		case 'video':
			$profile_query = ($profile_id == 0) ? "" : " AND `m`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`m`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `m`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_PROFILE_VIDEO."` AS `m` ON(`m`.`video_id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`m`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query ." GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
		case 'forum':
			$profile_query = ($profile_id == 0) ? "" : " AND `f`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`f`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `f`.`profile_id` as `owner_id`, `f`.`text` as `content`, `t`.`forum_topic_id` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_FORUM_POST."` AS `f` ON(`f`.`forum_post_id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_FORUM_TOPIC."` AS `t` ON(`t`.`forum_topic_id` = `f`.`forum_topic_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`f`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query ." GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			break;
			
		case 'blog':
			$profile_query = ($profile_id == 0) ? "" : " AND `b`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`b`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `b`.`profile_id` as `owner_id`, `b`.`text` AS `content` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_BLOG_POST."` AS `b` ON(`b`.`id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`b`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query ." GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
		case 'classifieds':
			$profile_query = ($profile_id == 0) ? "" : " AND `b`.`profile_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`b`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `b`.`profile_id` as `owner_id`, `b`.`description` AS `content` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_CLASSIFIEDS_ITEM."` AS `b` ON(`b`.`id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`b`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query ." GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
		case 'message':
			$profile_query = ($profile_id == 0) ? "" : " AND `m`.`sender_id`=" . $profile_id;
			
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`m`.`sender_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `p2`.`profile_id` as `owner_id`, `m`.`text` AS `content` FROM `".TBL_REPORT."` AS `r`
				LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				LEFT JOIN `".TBL_MAILBOX_MESSAGE."` AS `m` ON(`m`.`message_id` = `r`.`entity_id`)
				LEFT JOIN `".TBL_PROFILE."` AS `p2` ON(`m`.`sender_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active'" . $profile_query ." GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;		
	}
	

	return $reports;
}

function getReportsGroupedByProfile( $type )
{
	if ( strlen($type) == 0 )
		return array();
		
	switch ( $type )
	{
		case 'profile':
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`r`.`reporter_id`) as `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `p2`.`profile_id` AS `owner_id` FROM `".TBL_REPORT."` AS `r`
				INNER JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				INNER JOIN `".TBL_PROFILE."` AS `p2` ON(`p2`.`profile_id` = `r`.`entity_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active' GROUP BY `p2`.`profile_id` ORDER BY `ts` DESC" );
			break;
			
		case 'photo':
			
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`photo`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `photo`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				INNER JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				INNER JOIN `".TBL_PROFILE_PHOTO."` AS `photo` ON(`photo`.`photo_id` = `r`.`entity_id`)
				INNER JOIN `".TBL_PROFILE."` AS `p2` ON(`photo`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active' GROUP BY `photo`.`profile_id` ORDER BY `ts` DESC" );
			break;
			
		case 'video':
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`m`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `m`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				INNER JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				INNER JOIN `".TBL_PROFILE_VIDEO."` AS `m` ON(`m`.`video_id` = `r`.`entity_id`)
				INNER JOIN `".TBL_PROFILE."` AS `p2` ON(`m`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active' GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
		case 'forum':
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`f`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `f`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				INNER JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				INNER JOIN `".TBL_FORUM_POST."` AS `f` ON(`f`.`forum_post_id` = `r`.`entity_id`)
				INNER JOIN `".TBL_PROFILE."` AS `p2` ON(`f`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active' GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
		case 'blog':
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`b`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `b`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				INNER JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				INNER JOIN `".TBL_BLOG_POST."` AS `b` ON(`b`.`id` = `r`.`entity_id`)
				INNER JOIN `".TBL_PROFILE."` AS `p2` ON(`b`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active' GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
		case 'classifieds':
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`b`.`profile_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `b`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				INNER JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				INNER JOIN `".TBL_CLASSIFIEDS_ITEM."` AS `b` ON(`b`.`id` = `r`.`entity_id`)
				INNER JOIN `".TBL_PROFILE."` AS `p2` ON(`b`.`profile_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active' GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
		case 'message':
			$reports = MySQL::fetchArray( "SELECT MAX(`r`.`time_stamp`) AS `ts`, COUNT(`m`.`sender_id`) AS `num`, `r`.*, `p`.`username` AS `reporter_name`, 
				`p2`.`username` AS `owner_name`, `p2`.`profile_id` as `owner_id` FROM `".TBL_REPORT."` AS `r`
				INNER JOIN `".TBL_PROFILE."` AS `p` ON(`p`.`profile_id` = `r`.`reporter_id`)
				INNER JOIN `".TBL_MAILBOX_MESSAGE."` AS `m` ON(`m`.`message_id` = `r`.`entity_id`)
				INNER JOIN `".TBL_PROFILE."` AS `p2` ON(`m`.`sender_id` = `p2`.`profile_id`)
				WHERE `r`.`type`='".$type."' AND `r`.`status`='active' GROUP BY `r`.`entity_id` ORDER BY `ts` DESC" );
			break;
			
	}
	

	return $reports;
}

function getReportTypes()
{
	$types = MySQL::fetchArray( "SELECT DISTINCT `type` FROM `".TBL_REPORT."` WHERE `status`='active' ORDER BY `type` ASC", 0);
	return $types;
}

function getProfileReportTypes( $profile_id )
{
	$report_types = getReportTypes(); 
	$type_arr = array();
	foreach ( $report_types as $type )
	{
		$rep_group = getReports( $type, $profile_id );
		if ( count($rep_group) > 0 )
		{
			foreach ( $rep_group as $report )
			{ 
				if ( !in_array($report['type'], $type_arr) )
				{
					$res[] = $report;
				}
				$type_arr[] = $report['type'];
			}
		} 
	}
	$return_str = "";
	foreach ( $res as $type )
	{
		$return_str .= $type['type'] .", ";
	}
	return substr( $return_str, 0, strlen($return_str)-2 );
}

function getProfilesReports( $page, $res_per_page )
{
	$report_types = getReportTypes();
	$owners_arr = array();
	foreach ( $report_types as $type )
	{
		$rep_group = getReportsGroupedByProfile( $type );
		if ( count($rep_group) > 0 )
		{
			foreach ( $rep_group as $report )
			{ 
				if ( !in_array($report['owner_id'], $owners_arr) )
				{
					$report['type'] = getProfileReportTypes( $report['owner_id'] );
					$res[] = $report;
				}
				$owners_arr[] = $report['owner_id'];
			}
		} 
	}
	
	$_from = $res_per_page * ($page - 1) + 1;
	$_end = $res_per_page * $page + 1;
	$i = 1;
	
	if ( !count($res))
		return array();
	foreach ( $res as $report )
	{
		if ( $i >= $_from && $i < $_end )
			$result['page'][] = $report;
		$i++;
	}
	$result['all'] = $res;
	return $result;
}
function getReasons($type, $entity_id)
{
	$_query = sql_placeholder( "SELECT `r`.*, `p`.`username` FROM `".TBL_REPORT."` AS `r`
		LEFT JOIN `".TBL_PROFILE."` AS `p` ON(`r`.`reporter_id` = `p`.`profile_id`)
		WHERE `type`=? AND `entity_id`=?", $type, $entity_id);
	return MySQL::fetchArray($_query);
}

function getProfileAllReports( $profile_id )
{
	$report_types = getReportTypes(); 

	foreach ( $report_types as $type )
	{
		$rep_group = getReportsGroupedByEntity( $type, $profile_id );
		
		if ( count($rep_group) > 0 )
		{
			foreach ($rep_group as $key => $entity)
			{
				$rep_group[$key]['reasons'] = getReasons($entity['type'], $entity['entity_id']);
			}
			
			$group['reports'] = $rep_group;
			$group['type'] = $type; 
			$reports[] = $group;
		} 
		unset($res);
		unset($group);
	}

	return $reports;
}

function getReportedVideo( $profile_id, $media_id )
{
	$_query = sql_placeholder( "SELECT * FROM `?#TBL_PROFILE_VIDEO` 
		WHERE `profile_id`=? AND `video_id`=?", $profile_id, $media_id );
	
	return MySQL::fetchRow( $_query );
}

function getMediaFileURL( $file_hash, $file_ext )
{
	return URL_USERFILES.'pr_media_'.$file_hash.'.'.$file_ext;
}

function dismissReports( $reports_arr, $type )
{
	if ( !isset( $reports_arr ) ) 
		return -1;
	
	$in_arr = implode(", ", $reports_arr );

	$query = "UPDATE `".TBL_REPORT."` SET `status`='dismissed' 
		WHERE `entity_id` IN(" . $in_arr .") AND `type`='".$type."'";

	return MySQL::affectedRows( $query );
}

function deleteReports( $reports_arr )
{
	if ( !isset( $reports_arr ) ) 
		return -1;
	
	$in_arr = implode(", ", $reports_arr );

	$query = "DELETE FROM `".TBL_REPORT."` WHERE `entity_id` IN(" . $in_arr .")";

	return MySQL::affectedRows( $query );
}
