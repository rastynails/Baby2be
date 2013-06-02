<?php

$file_key = 'prof_field_list';

$active_tab = 'new';
if( isset($_GET['field_id']) )
    $active_tab = 'main';


require_once( '../internals/Header.inc.php' );
// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile_field.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );


$profile_field	= new AdminProfileField();


$frontend = new AdminFrontend();

$default_lang_id = SK_Config::Section('languages')->default_lang_id;

require_once( 'inc.admin_menu.php' );

// fatal error if field_id not exists
$fieldId = empty($_GET['field_id']) ? null : $_GET['field_id'];
$field_info	= $profile_field->GetFieldInfo( $fieldId );
$f_first_select = array();
$match_field = '';
// Get and adapt input data
if ( @$_POST['update_field'] )
{

	switch ( @$_POST['field_type'] )
	{
		case 'select':
		case 'fselect':
			$f_first_select['select']	= @$_POST['field_select_invite'];
		case 'multiselect':
		case 'multicheckbox':
		case 'radio':

			$match_field	= @$_POST['match_field_multi'];
			break;

		case 'birthdate':
		case 'date':

			$f_first_select['year']		= @$_POST['field_select_year_invite'];
			$f_first_select['month']	= @$_POST['field_select_month_invite'];
			$f_first_select['day']		= @$_POST['field_select_day_invite'];
			$match_field				= @$_POST['match_field_date'];
			break;
	}
	//printArr($_POST);
	switch ( $profile_field->updateField( array
		(
			'field_id'	=> @$_GET['field_id'],
			'f_type'	=> @$_POST['field_type'],
			'f_section'	=> @$_POST['field_section'],
			'f_regexp'	=> @$_POST['field_regexp'],
			'f_required'	=> @$_POST['field_required'],
			'f_edit_member'	=> @$_POST['field_edit_member'],
			'f_edit_name'	=> @$_POST['edit_page_name'],
			'f_view_member'	=> @$_POST['field_view_member'],
			'f_view_name'	=> @$_POST['view_page_name'],
			'f_join_member'	=> @$_POST['field_join_member'],
			'f_join_name'	=> @$_POST['join_page_name'],
			'f_search_member'	=> @$_POST['field_search'],
			'f_search_name'		=> @$_POST['search_name'],
			'f_confirm'			=> @$_POST['field_confirm'],
			'f_confirm_name'	=> @$_POST['confirm_name'],
                        'f_profile_list_member'	=> @$_POST['field_profile_list_member'],
                        'f_profile_list_value'	=> @$_POST['profile_list_value'],
			'f_error_msg'		=> @$_POST['field_error_msg'],
			'f_confirm_error_msg'	=> @$_POST['field_confirm_error_msg'],
			'f_first_select'		=> $f_first_select,
			'f_match'	=> $match_field,
			'f_column_size'	=> @$_POST['field_values_column_size'],
			'reliant_field_id' => @$_POST['reliant_field_id'],
			'depended_values' => @$_POST['depended_values'],

		) ) )
	{
		case -2:
			$frontend->RegisterMessage( "Field database name missing", 'notice' );
			break;
		case -3:
			$frontend->RegisterMessage( "Field not found in database", 'notice' );
			break;
		case -4:
			$frontend->RegisterMessage( "Value for Edit page name is missing for one of the active languages", 'notice' );
			break;
		case -5:
			$frontend->RegisterMessage( "Value for View page name is missing for one of the active languages", 'notice' );
			break;
		case -6:
			$frontend->RegisterMessage( "Value for Join page name is missing for one of the active languages", 'notice' );
			break;
		case -7:
			$frontend->RegisterMessage( "Undefined field type", 'error' );
			break;
		case -8:
			$frontend->RegisterMessage( "Undefined field", 'error');
			break;
		case -9;
			$frontend->RegisterMessage( "Confirmation field name missing", 'notice');
			break;
		case -10;
			$frontend->RegisterMessage( "Value for Search page name is missing for one of the active languages", 'notice');
			break;
        case -11;
			$frontend->RegisterMessage( "Value for profile view/list page missing name is missing for one of the active languages", 'notice');
			break;
		case 0:
			$frontend->RegisterMessage( "`<code>{$field_info['name']}</code>` field not updated", 'notice');
			break;
		default:
			$frontend->RegisterMessage( "`<code>{$field_info['name']}</code>` field updated" );

			break;
	}

	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_POST['create_field'] )
{
	if ( strlen( @$_POST[SK_Config::Section('languages')->default_lang_id]['new_field_name'] ) >= 64 )
	{
		$frontend->registerMessage( 'The name of field is more then 64 symbols!', 'error' );
		redirect( $_SERVER['REQUEST_URI'].'&field_id='.$create_field );
	}

	switch ( @$_POST['field_type'] )
	{
		case 'select':
		case 'fselect':
			$f_first_select['select']	= @$_POST['field_select_invite'];

		case 'multiselect':
		case 'multicheckbox':
		case 'radio':

			$match_field	= @$_POST['match_field_multi'];
			break;

		case 'birthdate':
		case 'date':

			$f_first_select['year']		= @$_POST['field_select_year_invite'];
			$f_first_select['month']	= @$_POST['field_select_month_invite'];
			$f_first_select['day']		= @$_POST['field_select_day_invite'];
			$match_field				= @$_POST['match_field_date'];
			break;
	}

	$create_field	= $profile_field->addNewField( array
		(
			'f_name'	=> @$_POST['new_field_name'],
			'f_type'	=> @$_POST['field_type'],
			'f_section'	=> @$_POST['field_section'],
			'f_regexp'	=> @$_POST['field_regexp'],
			'f_required'	=> @$_POST['field_required'],
			'f_edit_member'	=> @$_POST['field_edit_member'],
			'f_edit_name'	=> @$_POST['edit_page_name'],
			'f_view_member'	=> @$_POST['field_view_member'],
			'f_view_name'	=> @@$_POST['view_page_name'],
			'f_join_member'	=> @$_POST['field_join_member'],
			'f_join_name'	=> @$_POST['join_page_name'],
			'f_search_member'	=> @$_POST['field_search'],
			'f_search_name'		=> @$_POST['search_name'],
			'f_confirm'			=> @$_POST['field_confirm'],
			'f_confirm_name'	=> @$_POST['confirm_name'],
			'f_error_msg'		=> @$_POST['field_error_msg'],
			'f_confirm_error_msg'	=> @$_POST['field_confirm_error_msg'],
			'f_first_select'		=> $f_first_select,
			'f_match'	=> $match_field,
			'f_cols_count'	=> @$_POST['field_values_cols_count'],
			'reliant_field_id' => @$_POST['reliant_field_id'],
			'depended_values' => @$_POST['depended_values'],
                        'f_profile_list_member'	=> @$_POST['field_profile_list_member'],
                        'f_profile_list_value'	=> @$_POST['profile_list_value'],

		));

	switch ( $create_field )
	{
		case -1:
			$frontend->RegisterMessage( "Field name missing", 'notice' );
			break;
		case -2:
			$frontend->RegisterMessage( "Field requires values", 'notice' );
			break;
		case -3:
			$frontend->RegisterMessage( "Field with this name already exists", 'notice' );
			break;
		case -4:
			$frontend->RegisterMessage( "Value for Edit page name is missing for one of the active languages", 'notice' );
			break;
		case -5:
			$frontend->RegisterMessage( "Value for View page name is missing for one of the active languages", 'notice' );
			break;
		case -6:
			$frontend->RegisterMessage( "Value for Join page name is missing for one of the active languages", 'notice' );
			break;
		case -7:
			$frontend->RegisterMessage( "Incorrect field presentation type", 'notice' );
			break;
		case -8:
			$frontend->RegisterMessage( "Confirmation field name missing", 'notice' );
			break;
		case -9:

			$frontend->RegisterMessage( "Value for Incorrect value error message is missing for one of the active languages", 'notice' );
			break;

               case -10;
			$frontend->RegisterMessage( "Value for Search page name is missing for one of the active languages", 'notice');
			break;
                case -11;
			$frontend->RegisterMessage( "Value for profile view/list page missing name is missing for one of the active languages", 'notice');
			break;
		default:
			$frontend->RegisterMessage( "`<code>{$_POST['new_field_name'][SK_Config::Section('languages')->default_lang_id]}</code>` field has been added" );

			break;
	}
	redirect( $_SERVER['REQUEST_URI'].'&field_id='.$create_field );
}

if ( @$_POST['add_value'] )
{
	switch ( @$_POST['where'] )
	{
		case 'begin':
			$value_pos	= 0;
			break;
		case 'after':
			$value_pos	= @$_POST['field'];
			break;
		default:
			$value_pos	= -1;
			break;
	}
	if ( $profile_field->addFieldValue( @$_GET['field_id'], $value_pos, @$_POST['value'] ) > 0)
		$frontend->RegisterMessage( "Value <code>{$_POST['value'][$default_lang_id]}</code> for field <code>{$field_info['name']}</code> added");
	else
		$frontend->RegisterMessage( "Please, enter new value for <code>{$field_info['name']}</code> field", 'notice' );

	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_POST['delete_value'] )
{
	$result = $profile_field->deleteFieldValue( @$_GET['field_id'], @$_POST['field_value'] );
	switch ((int)$result) {
		case -1:
			$frontend->RegisterMessage( "Field value not deleted. Incorrect field order.", 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'You need to change `sex` field for profiles having this sex ( <a href=" '. URL_ADMIN . 'profile_list.php?sex='. intval($_POST['field_value']) .' ">profile list</a> ) before deleting this sex!', 'error' );
			break;
		default:
			$frontend->RegisterMessage( "Field value deleted" );
	}

	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_GET['action'] )
{
	controlAdminGETActions();

	switch ( $profile_field->moveFieldValueOrder( @$_GET['field_id'], @$_GET['value'], @$_GET['action'] ) )
	{
		case -1:
			$frontend->RegisterMessage( 'Value not moved. Undefined action.', 'error' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Value not moved. Undefined value order.', 'error' );
			break;
		case -3:
			$frontend->RegisterMessage( 'Value not moved. New order can not be zero.', 'notice' );
			break;
		default:
			$frontend->RegisterMessage( "Field value moved" );
			break;
	}
	redirect( $_SERVER['HTTP_REFERER'] );
}

if ( @$_POST['change_range'] )
{
	$age_range_value = intval( @$_POST['age_range_val1'] ).'-'.intval( @$_POST['age_range_val2'] );

	switch ( $profile_field->changeFieldCustomValue( @$_GET['field_id'], $age_range_value, 'age_range' ) )
	{
		case -1:
			$frontend->RegisterMessage( 'Empty start or end value of range', 'notice' );
			break;
		case 0:
			$frontend->RegisterMessage( 'Field range interval not updated', 'notice' );
			break;
		default:
			$frontend->RegisterMessage( 'Range updated' );
			break;
	}
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_POST['change_date'] )
{
	$date_range_value = intval( @$_POST['start_date'] ).'-'.intval( @$_POST['end_date'] );
	switch ( $profile_field->changeFieldCustomValue( @$_GET['field_id'], $date_range_value, 'date' ) )
	{
		case -1:
			$frontend->RegisterMessage( 'Empty value', 'notice' );
			break;
		case 0:
			$frontend->RegisterMessage( 'Field values not updated', 'notice' );
			break;
		default:
			$frontend->RegisterMessage( 'Start year updated' );
			break;
	}
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_POST['save_date_config'] )
{
	if ( adminConfig::SaveOneConfig( 'date_display_config', @$_POST['date_display_config'] ) )
		$frontend->registerMessage( 'Date display format was changed' );
	else
		$frontend->registerMessage( 'DIsplay date format was not changed', 'notice' );
	AdminProfileField::clearCache();
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_GET['location_move'] )
{
	controlAdminGETActions();

	if ( AdminProfileField::moveLocationOrder( @$_GET['field'], @$_GET['location_move'] ) )
		$frontend->registerMessage( 'Order of location field on view page was changed' );
	else
		$frontend->registerMessage( 'Undefined move statement!', 'error' );

	redirect( $_SERVER['HTTP_REFERER'] );
}

// statements

if ( $field_info )
{
    $fieldId = empty($_GET['field_id']) ? null : $_GET['field_id'];
	// get field's info
	$field_info	= $profile_field->getFieldInfo( $fieldId );

	if ( !$field_info['matching'] )
	{
		switch ( $field_info['presentation'] )
		{
			case 'location':

				$field_info['field_config']['field_order'] = explode( ',', SK_Config::section('profile_fields')->Section('location')->order );

				break;

			case 'text':
				break;

			case 'age_range':
				// get custom field value
				if ( $field_info['custom'] )
				{
					$temp_expl = explode( '-', $field_info['custom'] );
					$field_info['age_start'] = $temp_expl[0];
					$field_info['age_end'] = $temp_expl[1];
				}
			case 'birthdate':
			case 'date':
				// get custom field value
				if ( $field_info['custom'] )
				{
					$temp_expl = explode( '-', $field_info['custom'] );
					$field_info['date_start'] = $temp_expl[0];
					$field_info['date_end'] = $temp_expl[1];
				}

				// get date configs
				$field_info['field_config']['date_display_config'] = SK_Config::section('profile_fields')->Section('advanced')->date_display_config;

				break;
		}
		// get field values
		$field_info['values']	= AdminProfileField::getFieldValues( @$_GET['field_id'] );
		$field_info['values_count'] = count( $field_info['values'] );
	}
	else
	{
		$field_info['matching_field_href'] = URL_ADMIN.'profile_field.php?field_id='.$field_info['matching'];
		if ( @$_GET['f_page'] )
			$field_info['matching_field_href'] .= '&f_page='.@$_GET['f_page'];
		if ( @$_GET['page_num'] )
			$field_info['matching_field_href'] .= '&page_num='.@$_GET['page_num'];
	}

}

if ( !@$field_info['base_field'] || @$field_info['editable_by_admin'])
{
	$field_info['reliant_field_info'] = array( 'name' => 'sex', 'values' => SK_ProfileFields::get('sex')->values );
	$field_info['field_depended_values'] = AdminProfileField::getDependedFieldValues( @$field_info['profile_field_id'], 6 );
}


//print_arr($field_info);

// get all posible field's matches
$multi_field_matches = $profile_field->getFieldPossibleMatches( 'multi', @$field_info['profile_field_id'] );
$date_field_matches = $profile_field->getFieldPossibleMatches( 'birthdate', @$field_info['profile_field_id'] );

$frontend->assign_by_ref( 'multi_field_match', $multi_field_matches );
$frontend->assign_by_ref( 'date_field_match', $date_field_matches );

if (@$field_info['profile_field_id'])
{
    $field_info['profile_field_label'] = SK_Language::text('%profile_fields.label.' . (int) $field_info['profile_field_id']);
}

$frontend->assign_by_ref( 'field_info', $field_info );
// get all sections
$pr_page_sections = $profile_field->getAllSection();
$frontend->assign_by_ref( 'pr_page_sections', $pr_page_sections );

// get all types of field
$field_types	= $profile_field->getFieldTypes();
$frontend->assign_by_ref( 'field_types', $field_types );


// include javascript modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'field_list.js' );

$template = 'prof_field.html';

$_page['title'] = "Profile Fields";

$frontend->assign('default_lang_id', SK_Config::Section('languages')->default_lang_id);

$frontend->display( $template );

?>
