<?php

require_once( DIR_ADMIN_INC.'class.admin_profile_field.php' );

function printProfileField( $params )
{
	global $language;

	$_field = $params['field'];

	if ( $_field['matching'] )
            $match_field_name = $_field['matching'];

	switch ( $_field['presentation'] )
	{
		case 'text':
		case 'password':
		case 'callto':
		case 'url':
			$_field['display_value'] = $_field['display_value'];//secure_html( $_field['display_value'] );//TODO

			if ( in_array( $_field['name'], array( 'country_id', 'state_id', 'city_id', 'zip' ) ) )
			{
				break;
			}

			$_class = ( $params['class'] ) ? $params['class'] : 'input_text';
			$_item = '<input name="'.$_field['name'].'" id="'.$_field['id'].'" type="text" class="'.$_class.'" value="'.$_field['display_value'].'" />';
			break;

		case 'textarea':

			$_field['display_value'] = $_field['display_value'];//secure_html( $_field['display_value'] );
			$_item = "<textarea name=\"".$_field['name']."\" id=\"".$_field['id']."\" class=\"profile_textarea_field\">".$_field['display_value']."</textarea>";
			break;

		case 'checkbox':

			$_class = ( $params['class'] ) ? $params['class'] : 'input_'.$_field['presentation'];
			$_checked = ( $_field['display_value'] ) ? 'checked' : '';
			$_item = '<input name="'.$_field['name'].'" id="'.$_field['id'].'" type="checkbox" class="'.$_class.'" value="1" '.$_checked.' />';
			break;

		case 'multicheckbox':

			$_class = ( $params['class'] ) ? $params['class'] : 'input_checkbox';
			foreach ( $_field['all_values'] as $key => $value )
			{
				$_checked	= ( ( int )$value&( int )$_field['display_value'] ) ? 'checked' : '';
				$_field_id	= ( $_field['matching'] ) ? $match_field_name.'_'.$value : $_field['name'].'_'.$value;
				$_label_field_id = $_field['name'].'_'.$value;
				$_label	= SK_Language::section("profile_fields.value")->text($_field_id);

				$_item.= '<input name="'.$_field['name'].'[]" id="'.$_label_field_id.'" type="checkbox" class="'.$_class.'" value="'.$value.'" '.$_checked.' /><label for="'.$_label_field_id.'">'.$_label.'</label><br />';
			}
			break;

		case 'fradio':
		case 'radio':

			$_class = ( $params['class'] ) ? $params['class'] : 'input_'.$_field['presentation'];
			foreach ( $_field['all_values'] as $key => $value )
			{
				if ( $_field['presentation'] == 'radio' )
					$_checked	= ( ( int )$value&( int )$_field['display_value'] ) ? 'checked' : '';
				else
					$_checked	= ( $value == $_field['display_value'] ) ? 'checked' : '';

				$_field_id	= ( $_field['matching'] ) ? $match_field_name.'_'.$value : $_field['name'].'_'.$value;
				$_label_field_id = $_field['name'].'_'.$value;
				$_label	= SK_Language::section("profile_fields.value")->text($_field_id);

				$_item.= '<input name="'.$_field['name'].'" id="'.$_label_field_id.'" type="radio" class="'.$_class.'" value="'.$value.'" '.$_checked.' /><label for="'.$_label_field_id.'">'.$_label.'</label><br />';
			}

			break;

		case 'fselect':
		case 'select':
			try {
				$invite_msg = SK_Language::section("profile_fields.select_invite_msg")->text( $_field['profile_field_id']);
			} catch (SK_LanguageException $e) {
				$invite_msg= "";
			}

			$_item .= '<select name="'.$_field['name'].'" id="'.$_field['id'].'" class="'.$params['class'].'">';
			$_item .= ( $invite_msg == 'field_select_invite_msg_'.$_field['profile_field_id'] ) ? '' : '<option value="0">'.$invite_msg.'</option>';

			foreach( $_field['all_values'] as $key => $value )
			{
				if ( $_field['presentation'] == 'select' )
					$_selected	= ( ( int )$value&( int )$_field['display_value'] ) ? 'selected' : '';
				else
					$_selected	= ( $value == $_field['display_value'] ) ? 'selected' : '';

				$_field_id	= ( $_field['matching'] ) ? $match_field_name.'_'.$value : $_field['name'].'_'.$value;

				$_label	= SK_Language::section("profile_fields.value")->text($_field_id);

				$_item .= '<option value="'.$value.'" '.$_selected.'>'.$_label.'</option>';
			}
			$_item .= '</select>';
			break;

		case 'multiselect':

			$_item .= '<select name="'.$_field['name'].'[]" id="'.$_field['id'].'" class="'.$params['class'].'" multiple >';
			foreach( $_field['all_values'] as $key => $value )
			{
				$_selected	= ( (int)$value&(int)$_field['display_value'] ) ? 'selected' : '';
				$_field_id	= ( $_field['matching'] ) ? $match_field_name.'_'.$value : $_field['name'].'_'.$value;
				$_label	= SK_Language::section("profile_fields.value")->text($_field_id);

				$_item .= '<option value="'.$value.'" '.$_selected.' >'.$_label.'</option>';
			}
			$_item .= '</select>';
			break;

		case 'age_range':

			$_item = '<label>'.SK_Language::text("%forms._fields.age_range.from").' <select name="'.$_field['name'].'_start" id="'.$_field['id'].'_start" >';

			if ( !$match_field_name )
			{
				$range = explode( '-', SK_ProfileFields::get($_field['name'])->custom);
			}
			else
			{
				$match_field_custom_info = explode( '-', SK_ProfileFields::get($match_field_name)->custom );
				$date_info	= getdate();
				$range[0]	= $date_info['year'] - $match_field_custom_info[1];
				$range[1]	= $date_info['year'] - $match_field_custom_info[0];
			}
			$values_range	= explode( '-', $_field['display_value'] );

			for( $i = $range[0]; $i <= $range[1]; $i++ )
			{
				$_selected	= ( $i == $values_range[0] ) ? 'selected' : '';
				$_item.= '<option value="'.$i.'" '.$_selected.'>'.$i.'</option>';
			}
			$_item.= '</select></label>
			<label>'.SK_Language::text("%forms._fields.age_range.to").' <select name="'.$_field['name'].'_end" id="'.$_field['id'].'_end"  >';

			for( $i = $range[1]; $i >= $range[0]; $i-- )
			{
				$_selected	= ( $i == $values_range[1] ) ? 'selected' : '';
				$_item .= '<option value="'.$i.'" '.$_selected.' >'.$i.'</option>';
			}

			$_item.= '</select></label>
			<input type="hidden" name="'.$_field['name'].'" />';

			break;


		case 'date':
		case 'birthdate':

			// Year
			try {
				$invite_year_msg = SK_Language::section("profile_fields.select_invite_msg")->text($_field['profile_field_id'].'_year');
			} catch (SK_LanguageException $e) {
				$invite_year_msg= "";
			}

			$_item = '<label><select name="'.$_field['name'].'_year" id="'.$_field['id'].'_year" >';
			$_item .= ( $invite_year_msg == 'field_select_invite_msg_'.$_field['profile_field_id'].'_year' ) ? '' : '<option value="0">'.$invite_year_msg.'</option>';

			$values_range	= explode( '-', $_field['display_value'] );

			// get year range from custom of field
			$date_year_range = explode( '-', SK_ProfileFields::get($_field['name'])->custom);

			for( $i = $date_year_range[0]; $i <= $date_year_range[1]; $i++ )
			{
				$_selected	= ( $i == $values_range[0] ) ? 'selected' : '';
				$_item.= '<option value="'.$i.'" '.$_selected.'>'.$i.'</option>';
			}

			// Month
			try {
				$invite_month_msg = SK_Language::section("profile_fields.select_invite_msg")->text($_field['profile_field_id'].'_month');
			} catch (SK_LanguageException $e) {
				$invite_month_msg= "";
			}
			$_item.= '</select></label>
			<label><select name="'.$_field['name'].'_month" id="'.$_field['id'].'_month" >';

			$_item .= ( $invite_month_msg == 'field_select_invite_msg_'.$_field['profile_field_id'].'_month' )
			? ''
			: '<option value="0">'.$invite_month_msg.'</option>';

			for( $i = 1; $i <= 12; $i++ )
			{
				$_selected	= ( $i == $values_range[1] ) ? 'selected' : '';
				$_item .= '<option value="'.$i.'" '.$_selected.' >' . SK_Language::section("i18n.date")->text('month_short_'.$i) . '</option>';
			}

			// Day
			try {
				$invite_day_msg = SK_Language::section("profile_fields.select_invite_msg")->text($_field['profile_field_id'].'_day');
			} catch (SK_LanguageException $e) {
				$invite_day_msg= "";
			}
			$_item.= '</select></label>';
			$_item.= '<label><select name="'.$_field['name'].'_day" id="'.$_field['id'].'_day" >';

			$_item .= ( $invite_day_msg == 'field_select_invite_msg_'.$_field['profile_field_id'].'_day' )
				? ''
				: '<option value="0">'.$invite_day_msg.'</option>';

			for( $i = 1; $i <= 31; $i++ )
			{
				$selected	= ( $i == $values_range[2] ) ? 'selected' : '';
				$_item .= '<option value="'.$i.'" '.$selected.' >'.$i.'</option>';
			}
			$_item.= '</select></label>
			<input type="hidden" name="'.$_field['name'].'" />';

			break;
	}

	return $_item;
}

function checkEditProfileFields( $values_arr )
{
	$fields_arr = AdminProfileField::getAllProfileFIelds();

	if ( !is_array( $fields_arr ) )
		return array();

		foreach ( $fields_arr as $_field_info)
		{
			$_field_name = $_field_info['name'];
			$_field_value = $values_arr[$_field_name];

			if ( !SK_ProfileFields::get($_field_name)->profile_field_id || in_array( $_field_name, AdminProfileField::getLocationFields() ) )
				continue;

			// generate array with fields values
			switch ( SK_ProfileFields::get($_field_name)->presentation )
			{
				//case 'password':
				case 'textarea':
				case 'text':
				case 'url':
				case 'email':
				case 'callto':
				case 'url':
					$_field_value = trim( $_field_value );

					$_complete_arr[$_field_name] = $_field_value;

					break;
				case 'multiselect':
				case 'multicheckbox':
					$_field_value	= ( is_array( $_field_value ) ) ? array_sum( $_field_value ) : 0;
				case 'checkbox':
				case 'select':
                case 'fselect':
				case 'radio':

					$_complete_arr[$_field_name] = $_field_value;

					break;

				case 'date':
				case 'birthdate':

					if ( $_POST[$_field_name.'_year'] && $_POST[$_field_name.'_month'] && $_POST[$_field_name.'_day'] ) {
						$_complete_arr[$_field_name] = $_POST[$_field_name.'_year'].'-'.$_POST[$_field_name.'_month'].'-'.$_POST[$_field_name.'_day'];
					}

					break;

				case 'age_range':

					$_complete_arr[$_field_name] = $_POST[$_field_name.'_start'].'-'.$_POST[$_field_name.'_end'];

					break;
			}
		}
		return $_complete_arr;
}

function frontendGetMembershipExpirationCountDate( $params )
{
	$_expiration_stamp = $params['expiration_stamp'];

	$_time_interval = $_expiration_stamp - time();

	if ( ( $_time_interval / 86400 ) >= 1 )
	{
		$_info['item_num'] = floor( $_time_interval / 86400 );
		$_info['item'] = 'day(s)';
	}
	elseif ( ( $_time_interval / 3600 ) >= 1 )
	{
		$_info['item_num'] = floor( $_time_interval / 3600 );
		$_info['item'] = 'hour(s)';
	}
	elseif ( ( $_time_interval / 60 ) >= 1 )
	{
		$_info['item_num'] = floor( $_time_interval / 60 );
		$_info['item'] = 'minute(s)';
	}
	else
	{
		$_info['item_num'] = 1;
		$_info['item'] = 'minute(s)';
	}

	return $_info['item_num'].' '.$_info['item'];
}

?>