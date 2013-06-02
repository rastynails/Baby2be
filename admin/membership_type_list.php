<?php

$file_key = 'membership_types_list';
$active_tab = 'membership_types_list';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

$frontend = new AdminFrontend();

$template = 'membership_type_list.html';


require_once( 'inc.admin_menu.php' );

$_admin_config = new adminConfig();


//Get and adapt input data
if ( $_POST || $_GET )
{
	$_admin_config->SaveConfigs($_POST);
	$_admin_config->getResult($frontend);
	
	
	// Creat a new membership type
	if ( isset( $_POST['creat_membership'] ) )
	{
		switch ( AdminMembership::AddNewMembershipType( @$_POST['membership_name'], @$_POST['membership_description'], @$_POST['membership_type'], @$_POST['membership_limit'], @$_POST['available_for'] ) )
		{
			case -1:
				$frontend->RegisterMessage( 'Incorrect data posted', 'error' );
				break;
			case -2:
				$frontend->RegisterMessage( 'Membership type created but language entried not created', 'error' );
				break;
			case 1:
				$frontend->RegisterMessage( 'Membership type created' );
				break;
		}
		// reread lang cache after updates
		redirect( $_SERVER['REQUEST_URI'] );
	}
	
	// change order membership type
	if ( isset( $_GET['move_order'] ) && isset( $_GET['membership_type_id'] ) )
	{
		controlAdminGETActions();
		
		if ( AdminMembership::MoveOrderMembershipType( $_GET['membership_type_id'], $_GET['move_order'] ) )
			$frontend->RegisterMessage( 'Membership type order changed' );
		else
			$frontend->RegisterMessage( 'Order change failed', 'notice' );
		
		redirect( $_SERVER['PHP_SELF'] );
	}
	
	// Change default membership types
	if ( @$_POST['command'] == 'default_membership_type' )
	{
		switch ( AdminMembership::ChangeDefaultMembershipType( @$_POST['default_membership_type_id'] ) )
		{
			case 1:
				$frontend->RegisterMessage( 'Default membership has been changed' );
				break;
			case -1:
				$frontend->RegisterMessage( 'Default membership has not been changed. Incorrect data.', 'error' );
				break;
			case -2:
				$frontend->RegisterMessage( 'Only unlimited subscription membership can be default', 'notice' );
				break;
		}
		
		redirect( $_SERVER['REQUEST_URI'] );
	}
	
	if ( @$_POST['command'] == 'default_reg_membership_type' )
	{
		if ( !is_array( @$_POST['given_on_reg'] ) )
			$frontend->RegisterMessage( 'Incorrect data posted', 'error' );
		else
		{
			$_array = array();
			foreach ( @$_POST['given_on_reg'] as $_sex => $_membership_type_id )
				@$_array[$_membership_type_id]+= $_sex;
			
			if ( AdminMembership::changeDefaultRegMembershipType( $_array ) )
				$frontend->RegisterMessage( 'On-registration membership has been changed' );
			else
				$frontend->RegisterMessage( 'On-registration membership has not been changed. Please, try again.' );
		}
		
		redirect( $_SERVER['REQUEST_URI'] );
	}
}

adminConfig::SaveConfigs($_POST);

adminConfig::getResult($frontend, false);

#pass id of default membership type
$def_mem_type = SK_Config::Section('membership')->default_membership_type_id;
$frontend->assign( 'def_mem_type', $def_mem_type );

#get list of memberships
$mship_type_list = AdminMembership::GetAllMembershipTypes();
foreach ( $mship_type_list as $_key => $_value )
	for( $_i = 1; $_i <= $_value['available_for']; $_i = $_i * 2 )
		if ( $_value['available_for'] & $_i )
			$mship_type_list[$_key]['available_for_sex'][] = $_i;
$frontend->assign( 'mship_type_list', $mship_type_list );

# get list of plans
$_sample_plan = array(
	'single' => app_Membership::getFormatedPlan( 3, 'months', 36 ),
	'recurring' => app_Membership::getFormatedPlan( 3, 'months', 12, 'recurring' ),
	'free_trial' => app_Membership::getFormatedPlan( 3, 'months', 0, 'free_trial' )
);
$frontend->assign_by_ref( 'sample_plan', $_sample_plan );
$sexTempVar = SK_ProfileFields::get('sex');
$frontend->assign_by_ref( 'field_sex',  $sexTempVar);

	
foreach ( SK_ProfileFields::get('sex')->values as $_sex )
{
	foreach ( $mship_type_list as $_membership_type )
	{
		// check if it can be given
		if ( $_membership_type['type'] == 'subscription' && $_membership_type['limit'] == 'limited' )
			continue;
		if (intval($_membership_type['paid_by_sms']))
			continue;
		if ( $_membership_type['type'] == 'trial' )
			if ( !AdminMembership::GetMembershipTypePlan( $_membership_type['membership_type_id'] ) )
				continue;
		// check if it is available for the sex
		if ( !(intval($_membership_type['available_for']) & intval($_sex) ))
			continue;
		
		// selected
		if ( intval($_membership_type['given_on_registration']) & intval($_sex) )
			$given_on_reg[$_sex]['default'] = $_membership_type['membership_type_id'];
		// possible
		$given_on_reg[$_sex]['membership_types'][] = $_membership_type['membership_type_id'];
	}
	
	if ( isset($given_on_reg[$_sex]['default']) && !$given_on_reg[$_sex]['default']  )
		$given_on_reg[$_sex]['default'] = $def_mem_type;
}

$frontend->assign_by_ref( 'given_on_reg', $given_on_reg );


$_page['title'] = "Membership Types";

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'opacity.js' );
$frontend->display( $template );
