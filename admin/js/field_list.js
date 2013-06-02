/* Profile's fields admin interface JS file */


function show_field_values( value )
{
	switch( value )
	{
		case 'text':
		case 'password':
			$( 'field_value' ).className = "field_value_h";
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_s';
			$( 'field_age_range_value' ).className = 'field_value_h';
			$( 'first_select' ).className = "field_value_h";
			break;
		case 'age_range':
			$( 'field_value' ).className = "field_value_h";
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_h';
			$( 'field_age_range_value' ).className = 'field_value_s';
			$( 'first_select' ).className = "field_value_h";
			break;
		case 'select':
			$( 'field_value' ).className = "field_value_s";
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_h';
			$( 'field_age_range_value' ).className = 'field_value_h';
			$( 'first_select' ).className = "field_value_s";
		case 'multicheckbox':
		case 'multiselect':
		case 'radio':
			$( 'field_value' ).className = "field_value_s";
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_h';
			$( 'field_age_range_value' ).className = 'field_value_h';
			$( 'first_select' ).className = "field_value_h";
			break;

		case 'date':
		case 'birthdate':
		case 'checkbox':
		case 'textarea':
			$( 'field_value' ).className = "field_value_h";
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_h';
			$( 'field_age_range_value' ).className = 'field_value_h';
			$( 'first_select' ).className = "field_value_h";
			break;
	}
}

function manage_field_attr( value )
{
		if ( value == 'text' || value == 'password' || value == 'textarea' )
		{
			$( 'field_confirm' ).value = 0;
			$( 'manage_confirm_href' ).className = 'confirm_href_s';
			$( 'confirm_error_msg' ).className = "confirm_error_h";
			$( 'first_select' ).className = "first_select_h";
			$( 'first_select_date' ).className = "first_select_date_h";
			$( 'first_select_date' ).className = "first_select_date_h";
			$( 'date_field_match' ).className = "field_value_h";
			$( 'multi_field_match' ).className = "field_value_h";
			$( 'age_range_field_match' ).className = "field_value_h";
			$( 'field_search' ).disabled = false;
			$( 'cols_count' ).className = 'cols_count_h';
		}
		else if ( value == 'select' || value == 'fselect' )
		{
			$( 'field_confirm' ).value = 0;
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_h';
			$( 'confirm_error_msg' ).className = "confirm_error_h";
			$( 'first_select' ).className = "first_select_s";
			$( 'first_select_date' ).className = "first_select_date_h";
			$( 'date_field_match' ).className = "field_value_h";

			if ( value == 'select' )
				$( 'multi_field_match' ).className = "field_value_s";
			else
				$( 'multi_field_match' ).className = "field_value_h";

			$( 'age_range_field_match' ).className = "field_value_h";
			$( 'field_search' ).disabled = false;
			$( 'cols_count' ).className = 'cols_count_s';
		}
		else if ( value == 'date' || value == 'birthdate')
		{
			$( 'field_confirm' ).value = 0;
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_h';
			$( 'confirm_error_msg' ).className = "confirm_error_h";
			$( 'first_select' ).className = "first_select_h";
			$( 'first_select_date' ).className = "first_select_date_s";
			$( 'date_field_match' ).className = "field_value_s";
			$( 'multi_field_match' ).className = "field_value_h";
			$( 'age_range_field_match' ).className = "field_value_h";
			$( 'field_search' ).disabled = false;
			$( 'cols_count' ).className = 'cols_count_h';
		}
		else if ( value == 'multiselect' || value == 'multicheckbox' || value == 'radio' || value == 'fradio' )
		{
			$( 'field_confirm' ).value = 0;
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_h';
			$( 'confirm_error_msg' ).className = "confirm_error_h";
			$( 'first_select' ).className = "first_select_h";
			$( 'first_select_date' ).className = "first_select_date_h";
			$( 'date_field_match' ).className = "field_value_h";
			$( 'multi_field_match' ).className = "field_value_h";
			$( 'age_range_field_match' ).className = "field_value_h";
			$( 'field_search' ).disabled = false;
			$( 'cols_count' ).className = 'cols_count_s';
		}
		else if ( value == 'age_range' )
		{
			$( 'field_confirm' ).value = 0;
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_h';
			$( 'confirm_error_msg' ).className = "confirm_error_h";
			$( 'first_select' ).className = "first_select_h";
			$( 'first_select_date' ).className = "first_select_date_h";
			$( 'date_field_match' ).className = "field_value_h";
			$( 'multi_field_match' ).className = "field_value_h";
			$( 'age_range_field_match' ).className = "field_value_s";
			$( 'field_search' ).checked = false;
			$( 'field_search' ).disabled = true;
			$( 'search_input' ).className	= "field_value_h";
			$( 'cols_count' ).className = 'cols_count_h';
		}
		else
		{
			$( 'field_confirm' ).value = 0;
			$( 'confirm_tr' ).className = 'confirm_tr_h';
			$( 'manage_confirm_href' ).className = 'confirm_href_h';
			$( 'confirm_error_msg' ).className = "confirm_error_h";
			$( 'first_select' ).className = "first_select_h";
			$( 'first_select_date' ).className = "first_select_date_h";
			$( 'date_field_match' ).className = "field_value_h";
			$( 'multi_field_match' ).className = "field_value_h";
			$( 'age_range_field_match' ).className = "field_value_h";
			$( 'field_search' ).disabled = false;
			$( 'cols_count' ).className = 'cols_count_h';

		}

        if( value != 'birthdate' )
        {
            $( 'profile_list_label' ).className = "field_value_h";

            if( $( 'field_profile_list_member' ).checked )
                $( 'profile_list_input' ).className = "field_value_h";
        }
        else
        {
           $( 'profile_list_label' ).className = "field_value_s";

           if( $( 'field_profile_list_member' ).checked )
                $( 'profile_list_input' ).className = "field_value_s";
        }
}

function manage_confirm_field()
{
	if ( $( 'confirm_tr' ).className == 'confirm_tr_h' )
	{
		$( 'field_confirm' ).value = 1;
		$( 'confirm_tr' ).className = 'confirm_tr_s';
		$( 'confirm_error_msg' ).className = 'confirm_error_s';
	}
	else
	{
		$( 'field_confirm' ).value = 0;
		$( 'confirm_tr' ).className = 'confirm_tr_h';
		$( 'confirm_error_msg' ).className = 'confirm_error_h';
	}
}


function show_field_attr_name( checked_val, input_id )
{
	if ( checked_val )
		$( input_id ).className	= "field_value_s";
	else
		$( input_id ).className	= "field_value_h";
}

function onload_field_attr_name( field_edit_member, field_view_member, field_join )
{
	if ( field_edit_member || $( "edit_member" ).checked )
		$( "edit_input" ).className = "field_value_s";

	if ( field_view_member || $( "view_member" ).checked )
		$( "view_input" ).className = "field_value_s";

	if ( field_join || $( "join_member" ).checked )
		$( "join_input" ).className = "field_value_s";
}

function convert_field_name( value )
{
	value = str2lower( value );
	if (/^\d/.exec(value)) {
		value = '_' + value;
	}
	$( "field_db_name" ).value = value;
}

function CheckAddFieldValueForm()
{
	if ( !$( 'new_field_val' ).value.trim() )
		return fail_alert( $( 'new_field_val' ), 'Insert value' );
	else
		return true;
}

function ConfirmDelFieldValue()
{
	return confirm("Are you sure you want to delete field value?" );
}

function CheckEditFieldForm()
{
	if ( $( 'join_member' ).checked && !lang_check_values('.lang_input_join_page_name', 'Value for Join page name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'view_member' ).checked && !lang_check_values('.lang_input_view_page_name', 'Value for View page name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'edit_member' ).checked && !lang_check_values('.lang_input_edit_page_name', 'Value for Edit page name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'field_search' ).checked && !lang_check_values('.lang_input_field_search', 'Value for Search page name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'confirm_tr' ).className == 'confirm_tr_s' && !lang_check_values('.lang_input_confirm_name', 'Value for Confirmation field name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'field_error_msg' ) && !lang_check_values('.lang_input_field_error_msg', 'Please add the values for the "Incorrect value error message" field') )
        {
            return false;
        }

	if ( $( 'confirm_tr' ).className == 'confirm_tr_s' && !lang_check_values('.lang_input_field_confirm_error_msg', 'Value for Confirmation field error message is missing for one of the active languages') )
            return false;

        var visible = ( jQuery('#field_select_invite:visible').length && !jQuery('#field_select_invite').parent('not(:visible)').length );
        if ( visible && !lang_check_values('.lang_input_field_select_invite', 'Missing field Selection invitation label') )
		return false;

        visible = ( jQuery('#field_select_year_invite:visible').length && !jQuery('#field_select_year_invite').parent('not(:visible)').length );
        if ( visible && !lang_check_values('.lang_input_field_select_year_invite', 'Missing field Date values selection invitation labels') )
            return false;

        visible = ( jQuery('#field_select_month_invite:visible').length && !jQuery('#field_select_month_invite').parent('not(:visible)').length );
        if ( visible && !lang_check_values('.lang_input_field_select_month_invite', 'Missing field Date values selection invitation labels') )
            return false;

        visible = ( jQuery('#field_select_day_invite:visible').length && !jQuery('#field_select_day_invite').parent('not(:visible)').length );
        if ( visible && !lang_check_values('.lang_input_field_select_day_invite', 'Missing field Date values selection invitation labels') )
            return false;

	return true;
}

function ConfirmFieldListForm()
{
	var checked_count = 0;
	var F = document.forms['field_list'];
	var length	= F.length;

	for( var i = 0; i<=length; i++ )
		if ( F.elements[i] && F.elements[i].name && F.elements[i].type == 'checkbox' && F.elements[i].checked == true)
			checked_count = checked_count+1;

	if ( !checked_count )
	{
		alert( 'Select field' );
		return false;
	}
	else
	{
		if ( $( 'form_submitter' ).value == 'del_field' )
			return confirm( 'Are you sure you want to delete profile field from database?' );
		else if ( $( 'form_submitter' ).value == 'del_field_from_page' )
			return confirm( 'Are you sure you want to delete profile field from page?' );
		else
			return true;
	}
}

function SetFormSubmitter( submit_id )
{
	$( 'form_submitter' ).value = submit_id;
}

function CheckAddSectionForm()
{
	if ( !lang_check_values('.lang_input_new_section_name', 'Value for Section name is missing for one of the active languages') )
		return false;
	else
		return true;
}

function CheckDeleteSection()
{
	if ( $( 'list_del_section' ).value == 0)
		return fail_alert( $( 'list_del_section' ), 'Select section' );

	return confirm( 'Are you sure you want to delete selected section(s)?' );
}

function CheckAddFieldForm()
{
	if ( !$( 'new_field_name' ).value.trim() )
		return fail_alert( $( 'new_field_name' ), 'Enter new field name' );

        if ( $( 'join_member' ).checked && !lang_check_values('.lang_input_join_page_name', 'Value for Join page name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'view_member' ).checked && !lang_check_values('.lang_input_view_page_name', 'Value for View page name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'edit_member' ).checked && !lang_check_values('.lang_input_edit_page_name', 'Value for Edit page name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'field_search' ).checked && !lang_check_values('.lang_input_field_search', 'Value for Search page name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'confirm_tr' ).className == 'confirm_tr_s' && !lang_check_values('.lang_input_confirm_name', 'Value for Confirmation field name is missing for one of the active languages') )
        {
            return false;
        }

        if ( $( 'field_error_msg' ) && !lang_check_values('.lang_input_field_error_msg', 'Please add the values for the "Incorrect value error message" field') )
        {
            return false;
        }

	if ( $( 'confirm_tr' ).className == 'confirm_tr_s' && !lang_check_values('.lang_input_field_confirm_error_msg', 'Value for Confirmation field error message is missing for one of the active languages') )
            return false;

        var visible = ( jQuery('#field_select_invite:visible').length && !jQuery('#field_select_invite').parent('not(:visible)').length );
        if ( visible && !lang_check_values('.lang_input_field_select_invite', 'Missing field Selection invitation label') )
		return false;

        visible = ( jQuery('#field_select_year_invite:visible').length && !jQuery('#field_select_year_invite').parent('not(:visible)').length );
        if ( visible && !lang_check_values('.lang_input_field_select_year_invite', 'Missing field Date values selection invitation labels') )
            return false;

        visible = ( jQuery('#field_select_month_invite:visible').length && !jQuery('#field_select_month_invite').parent('not(:visible)').length );
        if ( visible && !lang_check_values('.lang_input_field_select_month_invite', 'Missing field Date values selection invitation labels') )
            return false;

        visible = ( jQuery('#field_select_day_invite:visible').length && !jQuery('#field_select_day_invite').parent('not(:visible)').length );
        if ( visible && !lang_check_values('.lang_input_field_select_day_invite', 'Missing field Date values selection invitation labels') )
            return false;

	return true;
}

function CheckAddPageForm()
{
	if ( !lang_check_values('.lang_input_new_page_name', 'Value for Page name is missing for one of the active languages') )
            return false;
	else
            return true;
}

function CheckDeletePageForm()
{
	if ( $( 'list_del_page' ).value == 0 )
		return fail_alert( $( 'list_del_page' ), 'Select page' );
	else
		return confirm( 'Are you sure you want to delete selected page?' );
}

function CheckChangeRangeForm()
{
	if ( !$( 'date_start' ).value.trim() )
		return fail_alert( $( 'date_start' ), 'Enter range start date' );
	else if ( !$( 'date_end' ).value.trim() )
		return fail_alert( $( 'date_end' ), 'Enter range end date' );
}
