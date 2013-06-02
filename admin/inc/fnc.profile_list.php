<?php

/**
 * @param integer $res_per_page
 * @param integer $page
 * @return array( sql, display )
 */
function navigationDBLimit( $res_per_page, $page )
{
	$_from = $res_per_page*($page-1);
	
	return array
	(
		'begin'	=>	$_from+1,
		'sql'	=>	"LIMIT $_from, $res_per_page"
	);
}

function getProfileListFieldSettings( $field, $default )
{
	$set_fields = SK_Config::section('admin_system')->profile_list_columns; 

	return isset($set_fields->$field->checked) ? $set_fields->$field->checked : $default;  
}

function updateProfileListFieldSettings( $fields_arr )
{
	if (count($fields_arr) == 0)
	return -1;
	
	$arr = array();
	foreach ( $fields_arr as $field )
	{
		$arr[$field]['checked'] = 'yes';
	}

	SK_Config::section('admin_system')->set('profile_list_columns', $arr);
}

function profileList_getSQLDefinition( $sort_by = '', $order = '' )
{
	$_db_fields = array();
	$_where = "";
	$_join = "";
	 
	$_base_struct = mysql_list_fields( DB_NAME, TBL_PROFILE );
	
	$_base_fields_num = mysql_num_fields( $_base_struct );

	$base_arr = array
	(
		array('name' => 'profile_id',	'checked' => 'always'),
		array('name' => 'username',		'checked' => 'always'),
		array('name' => 'sex', 			'checked' => 'always'),
		array('name' => 'email_verified',	'checked' => getProfileListFieldSettings('email_verified', 'no') ),
		array('name' => 'has_photo', 		'checked' => getProfileListFieldSettings('has_photo', 'no') ),
		array('name' => 'has_media', 		'checked' => getProfileListFieldSettings('has_media', 'no') ),
        array('name' => 'has_music', 		'checked' => getProfileListFieldSettings('has_music', 'no') ),
		array('name' => 'activity_stamp', 	'checked' => getProfileListFieldSettings('activity_stamp', 'no') ),
		array('name' => 'reviewed', 		'checked' => getProfileListFieldSettings('reviewed', 'no') ),
		array('name' => 'status', 			'checked' => getProfileListFieldSettings('status', 'no') ),		
		array('name' => 'membership_type_id', 'checked' => getProfileListFieldSettings('membership_type_id', 'no') ),
		array('name' => 'birthdate', 		'checked' => getProfileListFieldSettings('birthdate', 'no') ),				
		array('name' => 'join_stamp', 		'checked' => getProfileListFieldSettings('join_stamp', 'no') ),
		array('name' => 'featured', 		'checked' => getProfileListFieldSettings('featured', 'no') ),
		array('name' => 'language_id', 		'checked' => getProfileListFieldSettings('language_id', 'no') ),						
	);

	$presentation_arr = "'select','checkbox','date', 'birthdate','radio'";
	$_query = "SELECT `profile_field_id`, `name` FROM `".TBL_PROF_FIELD."` WHERE `base_field`='0' AND `presentation` IN (".$presentation_arr.")";
	
	$extended_fields = MySQL::fetchArray( $_query );
	
	foreach ( $extended_fields as $field )
	{
		$item['name'] = $field['name'];
		$item['checked'] =  getProfileListFieldSettings( $field['name'], 'no');
		$extend_arr[] = $item; 
		unset($item);
	}
	
	// get checked fields list 
	$_checked_list = array();
	
	foreach ( $base_arr as $field )
	{
		if ( $field['checked'] == 'always' || $field['checked'] == 'yes' )
			$_checked_list[] = $field['name']; 
	}
	
	if ( $extend_arr )
	{
    	foreach ( $extend_arr as $field )
    	{
    		if ( $field['checked'] == 'yes' )
    			$_checked_list[] = $field['name']; 
    	}
	}
	
	for( $_i = 0; $_i < $_base_fields_num; $_i++ )
	{
		$_db_fields[] = mysql_field_name( $_base_struct, $_i );
	}
	
	foreach( $_GET as $_key => $_value )
	{
		if( in_array( $_key, $_db_fields ) )
			@$_where .= "`pr`.`$_key`='$_value' AND";
	}
	
	if( @$_GET['photo_status'] )
	{
		$_join .= " LEFT JOIN `".TBL_PROFILE_PHOTO."` as `p` ON(`pr`.`profile_id` = `p`.`profile_id`)";
		$_where .= " `p`.`status`='".mysql_real_escape_string($_GET['photo_status'])."' AND";
		$_select = " , `p`.`status` ";
	}
	
	if ( @$_GET['media_status'] )
	{
		$_join .= " LEFT JOIN `".TBL_PROFILE_VIDEO."` AS `v` ON(`pr`.`profile_id` = `v`.`profile_id`)";
		$_where .= " `v`.`status`='".mysql_real_escape_string($_GET['media_status'])."' AND ";
		$_select = " , `v`.`status` ";
	}
	if( @$_GET['music_status'] )
        {
                $_join .= " LEFT JOIN `".TBL_PROFILE_MUSIC."` AS `v` ON(`pr`.`profile_id` = `v`.`profile_id`)";
		$_where .= " `v`.`status`='".mysql_real_escape_string($_GET['music_status'])."' AND ";
		$_select = " , `v`.`status` ";
        }
	if ( @$_GET['mails'] )
	{
		switch (@$_GET['mails']) {
			case 'y':
				$_join .= "INNER JOIN ( SELECT DISTINCT `initiator_id` AS `ID` FROM `".TBL_MAILBOX_CONVERSATION."` 
			 		UNION SELECT DISTINCT `interlocutor_id` AS `ID` FROM `".TBL_MAILBOX_CONVERSATION."`) AS `m` 
			 		ON( `pr`.`profile_id` = `m`.`ID`)";
				break;
			case 'n':
				$_where .= "`pr`.`profile_id` NOT IN ( SELECT DISTINCT `initiator_id` AS `ID` FROM `".TBL_MAILBOX_CONVERSATION."` 
			 		UNION SELECT DISTINCT `interlocutor_id` AS `ID` FROM `".TBL_MAILBOX_CONVERSATION."`) AND ";
				break;
		}
	}
	
	if( @$_GET['online'] )
	{
		$_join .= " LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `o` ON(`pr`.`profile_id` = `o`.`profile_id`)";
		$_where .= " `o`.`hash` IS NOT NULL AND";
	}
	
    if( @$_GET['moderators'] )
    {
        $_join .= " INNER JOIN `".TBL_SITE_MODERATORS."` AS `m` ON(`pr`.`profile_id` = `m`.`profile_id`)";
    }
	
	if( @$_GET['claim_membership'] )
	{
		$_join .= " LEFT JOIN `".TBL_MEMBERSHIP_CLAIM."` AS `c` ON(`pr`.`profile_id` = `c`.`profile_id`)";
		$_where .= " `c`.`claim_result`='claim' AND";
	}
	
	if( @$_GET['search_username'] )
	{
		$username = trim($_GET['search_username']);
		$_where .= " `pr`.`username` LIKE '%".mysql_real_escape_string($username)."%' AND";
	}
	
	if( @$_GET['search_email'] )
	{
		$email = trim($_GET['search_email']);
		$_where .= " `pr`.`email` LIKE '%".mysql_real_escape_string($email)."%' AND";
	}
	
	if( @$_GET['search_keyword'] )
	{
		$_search_fields = MySQL::fetchArray( "SELECT `name` FROM `".TBL_PROFILE_FIELD."` WHERE `presentation`='text' OR `presentation`='textarea'", 0);
		foreach( $_search_fields as $_field )
		{
			$tbl = ( !in_array($_field, $_db_fields ) ) ?  "`ex`." :  "`pr`.";  
			$_where .= $tbl."`".$_field."` LIKE '%".mysql_real_escape_string($_GET['search_keyword'])."%' OR ";
		}
		
		$_where = substr( $_where, 0, -3 ).' AND';
	}
	
	if ( @$_GET['search_profile_id'] ) 
	{
		if ( $_GET['search_profile_id'] == intval( $_GET['search_profile_id'] ) )
			$_where .= " `pr`.`profile_id` = '". ((int)$_GET['search_profile_id']) ."'  AND";
	}
	
	if( strlen($_where) == 0 )
		$_where = '1';
	else
		$_where = substr( $_where, 0, -4 );
	
	if (!isset( $sort_by )) $sort_by = 'activity_stamp';
			
	if( @$_GET['reviewed'] )
		$_sort_order = '`join_stamp`'; 

		
	for( $_i = 0; $_i < $_base_fields_num; $_i++ )
	{
		$_def[$_i] =  mysql_field_name( $_base_struct, $_i );
	}

	if ( in_array($sort_by, $_def) )
		$table = "`pr`.";
	else 
		$table = "`ex`.";
	
	if( $sort_by == 'activity_stamp' )
	{
	    $_join .= " LEFT JOIN `".TBL_PROFILE_ONLINE."` AS `online` ON (`pr`.`profile_id` = `online`.`profile_id`) ";
        $sort_by = " IF( `online`.`profile_id` IS NULL, 1, 0 ) $order, " . time() . " - activity_stamp ";
	}
	else
	{
		$sort_by = $table.'`'.$sort_by.'`';
	}
	
	return array
	(
		'SELECT' => @$_select,
		'JOIN'	=>	@$_join,
		'WHERE'	=>	'WHERE '. @$_where,
		'ORDER' =>  'ORDER BY '.$sort_by.' '. $order,
		'default_fields' => $base_arr,
		'extend_fields' => $extend_arr,
		'checked_fields' => $_checked_list
	);
}


function profileList_getResultsPerPageValue()
{
	if( isset($_GET['_rpp']) )
	{
		setcookie( 'admin_rpp', $_GET['_rpp'], time()+2592000 /* expires after 30 days */, '/' );
		$_rpp = $_GET['_rpp'];
	}
	elseif( isset($_COOKIE['admin_rpp']) )
		$_rpp = $_COOKIE['admin_rpp'];
	else
		$_rpp = 30;
	
	return $_rpp;
}

function getCleanRequestURL()
{
	$_url = $_SERVER['PHP_SELF'].'?';
	
	foreach( $_GET as $_key => $_value )
		if( $_key != '_rpp' && $_key != '_page' )
			$_url .= $_key.'='.$_value.'&';
	
	return $_url;
}

/**
 *
 * @param array $rpp_values
 * @param integer $selected
 * @return string
 */
function ResPerPageSelect( $rpp_values, $selected )
{
	$_url = getCleanRequestURL();
	
	$_out = '<select onchange="location.href=this.value">';
	
	foreach( $rpp_values as $_rpp )
	{
		$_selected = ( $_rpp == $selected ) ? 'selected="selected"' : '';
		$_out .= '<option value="'.$_url.'_rpp='.$_rpp.'" '.$_selected.'>'.$_rpp.'</option>';
	}
	
	$_out .= '</select>';
	
	return $_out;
}


function navigationPages( $total_pages )
{
	$_range = 5;
	
	$_url = getCleanRequestURL();
	$_out = "";  
	
	$_curr_page = isset($_GET['_page']) ? (int)$_GET['_page'] : 1;
		
	if( ($_curr_page - $_range) > 1 )
		$_out = '<a href="'.$_url.'_page=1" class="nav_page" title="First page">[<b>&laquo;</b>]</a>';
	
	if( $_curr_page > 1 )
		$_out .= '<a href="'.$_url.'_page='.($_curr_page-1).'" class="nav_page" title="Previous page">[<b>&lsaquo;</b>]</a> - ';
	
	
	for( $p = 1; $p < $total_pages+1; $p++ )
	{
		if( ($_curr_page - $_range) > $p )
		{
			continue;
		}
		
		if( ($_curr_page + $_range) < $p )
		{
			continue;
		}
		
		$_class = ( $p == $_curr_page ) ? 'nav_page_curr' : 'nav_page';
		$_out .= '<a href="'.$_url.'_page='.$p.'" class="'.$_class.'">['.$p.']</a>';
	}
	
	
	if( $_curr_page < $total_pages )
		$_out .= ' - <a href="'.$_url.'_page='.($_curr_page+1).'" class="nav_page" title="Next page">[<b>&rsaquo;</b>]</a>';
	
	if( ($_curr_page + $_range) < $total_pages )
		$_out .= '<a href="'.$_url.'_page='.$total_pages.'" class="nav_page" title="Last page">[<b>&raquo;</b>]</a>';
	
	return $_out;
}

?>