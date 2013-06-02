<?php

require_once( DIR_ADMIN_INC.'class.admin_membership.php' );

function frontendPrintConditions( $params )
{
	$params = $params['data'];
	
	$activity_data = app_ActivityScheduler::getStatementTimestamp($params['activity_stamp']);
	$join_data = app_ActivityScheduler::getStatementTimestamp($params['join_stamp']);
	
	$have_photo_value = $params['has_photo'];
	$have_media_value = $params['has_media'];
	$reviewed_value = $params['reviewed'];
	$membership_value = $params['membership_type_id'];
	$email_verified_value = $params['email_verified'];
	$status_value = $params['status'];
	$sex_value = $params['sex'];
	$ignore_unsubscribe = $params['ignore_unsubscribe'];
	
	$timeunit_arr = array
	(
		'second' => 'seconds',
		'minute' => 'minutes',
		'hour'	=> 'hours',
		'day'	=> 'days',
		'month'	=> 'months',
	);
	
	$unit_arr = array
	(
		'0'	=> 'both',
		'y'	=> 'yes',
		'n'	=> 'no'
	);
	
	$verifiedunit_arr = array
	(
		'0'	=> 'all',
		'yes'	=> 'yes',
		'no'	=> 'no',
		'undefined'	=> 'undefined'
	);
	
	$statusunit_arr = array
	(
		'active'	=> 'active',
		'on_hold'	=>  'on hold',
		'suspended'	=> 'suspedned'
	);
	
	$membership_arr = AdminMembership::GetAllMembershipTypes();
	
	$_output = 
	'<table><tbody><tr><td><span class="nested_cat_sign" title="leave empty if needless" >Last Activity</span></td>
	<td>
		from <input type="text" name="activity_stamp_start" class="input_text" size="1" value="'.$activity_data['start_num'].'" />
		<select name="activity_stamp_start_unit" >';
	foreach ( $timeunit_arr as $_unit_name => $_unit_label )
	{
		$selected = ( $activity_data['start_unit'] == $_unit_name ) ? 'selected' : '';
		$_output .= '<option value="'.$_unit_name.'" '.$selected.' >'.$_unit_label.'</option>';
	}
	$_output .= '</select>
		to
		<input type="text" name="activity_stamp_end" class="input_text" size="1" value="'.$activity_data['end_num'].'" />
		<select name="activity_stamp_end_unit" >';
	foreach ( $timeunit_arr as $_unit_name => $_unit_label )
	{
		$selected = ( $activity_data['end_unit'] == $_unit_name ) ? 'selected' : '';
		$_output .= '<option value="'.$_unit_name.'" '.$selected.' >'.$_unit_label.'</option>';
	}
	$_output .= '</select> ago
		</td>
	</tr>
	<tr><td>Sex</td>
	<td>
		<select name="sex">
			<option value="0">all</option>';
			foreach ( SK_ProfileFields::get('sex')->values as $_value )
			{
				$selected = ( $_value == $sex_value ) ? 'selected' : '';
				$_output .= '<option value="'.$_value.'" '.$selected.' >'.SK_Language::section('profile_fields.value')->text('sex_'.$_value).'</option>';
			}
		$_output .= '</select>
	</td>
	<tr><td>Have Photos</td>
	<td>
		<select name="has_photo">';
	foreach ( $unit_arr as $_unit_value => $_unit_label )
	{
		$selected = ( $_unit_value == $have_photo_value ) ? 'selected' : '';
		$_output .= '<option value="'.$_unit_value.'" '.$selected.' >'.$_unit_label.'</option>';
	}
	$_output .= '
		</select>
	</td></tr>
	<tr><td>Have Media</td>
	<td>
		<select name="has_media">';
	foreach ( $unit_arr as $_unit_value => $_unit_label )
	{
		$selected = ( $_unit_value == $have_media_value ) ? 'selected' : '';
		$_output .= '<option value="'.$_unit_value.'" '.$selected.' >'.$_unit_label.'</option>';
	}
	$_output .= '
		</select>
	</td></tr>
	<tr><td>Reviewed</td>
	<td>
		<select name="reviewed">';
	foreach ( $unit_arr as $_unit_value => $_unit_label )
	{
		$selected = ( $_unit_value == $reviewed_value ) ? 'selected' : '';
		$_output .= '<option value="'.$_unit_value.'" '.$selected.' >'.$_unit_label.'</option>';
	}
	$_output .= '
		</select>
	</td></tr>
	<tr><td>Membership</td>
	<td>
		<select name="membership_type_id">
			<option value="0">all</option>';
	foreach ( $membership_arr as $_membership )
	{
		$selected = ( $_membership['membership_type_id'] == $membership_value ) ? 'selected' : '';
		$_output .= '<option value="'.$_membership['membership_type_id'].'" '.$selected.' >'.SK_Language::section('membership')->section('types')->text($_membership['membership_type_id']) .'</option>';
	}
	$_output .= '
		</select>
	</td></tr>
	<tr><td>Email verified</td>
	<td>
		<select name="email_verified">';
	foreach ( $verifiedunit_arr as $_unit_value => $_unit_label )
	{
		$selected = ( $_unit_value == $email_verified_value ) ? 'selected' : '';
		$_output .= '<option value="'.$_unit_value.'" '.$selected.' >'.$_unit_label.'</option>';
	}
	$_output .= '			
		</select>
	</td></tr>
	<tr><td>Status</td>
	<td>
		<select name="status">
			<option value="0">all</option>';
		foreach ( $statusunit_arr as $_unit_name => $_unit_label )
		{
			$selected = ( $_unit_name == $status_value ) ? 'selected' : '';
			$_output .= '<option value="'.$_unit_name.'" '.$selected.' >'.$_unit_label.'</option>';
		}
		$_output .= '
		</selected>
	</td></tr>
	<tr><td><span class="nested_cat_sign" title="leave empty if needless" >Join stamp</span></td>
	<td>
		from <input type="text" name="join_stamp_start" class="input_text" size="1" value="'.$join_data['start_num'].'" />
		<select name="join_stamp_start_unit" >';
	foreach ( $timeunit_arr as $_unit_name => $_unit_label )
	{
		$selected = ( $join_data['start_unit'] == $_unit_name ) ? 'selected' : '';
		$_output .= '<option value="'.$_unit_name.'" '.$selected.' >'.$_unit_label.'</option>';
	}
	$_output .= '</select>
		to
		<input type="text" name="join_stamp_end" class="input_text" size="1" value="'.$join_data['end_num'].'" />
		<select name="join_stamp_end_unit" >';
	foreach ( $timeunit_arr as $_unit_name => $_unit_label )
	{
		$selected = ( $join_data['end_unit'] == $_unit_name ) ? 'selected' : '';
		$_output .= '<option value="'.$_unit_name.'" '.$selected.' >'.$_unit_label.'</option>';
	}
	$_output .= '</select> ago
		</td>
	</tr>
	
	<tr class="tr_1"><td>Ignore "Unsubscribe"</td> <td><input type="checkbox" name="ignore_unsubscribe"'.( $ignore_unsubscribe ? 'checked' : '' ).'></td></tr>
	
	</tbody></table>
	';
	
	return $_output;
}
