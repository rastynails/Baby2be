<?php

$file_key = 'membership_types_list';
$active_tab = 'membership_types_list';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );

// send header for clearing any image chache
header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', FALSE );
header( 'Pragma: no-cache' );

// alert if gd not installed
if ( !app_Image::getGdVersion() )
	$frontend->RegisterMessage( "Your server doesn't have GD Library installed", 'error' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );


$membership_type_id = intval( @$_GET['membership_type_id'] );
if ( !$membership_type_id )
	redirect( URL_ADMIN.'membership_type_list.php' );

// Update membership type's permissions
if ( isset( $_POST['save_permissions_settings'] ) )
{
	$result = AdminMembership::updateMembershipPermission( @$_POST['service_key'], $membership_type_id );
	if ( AdminMembership::updateServiceLimitForMembershipType( @$_POST['service_limit'], $membership_type_id ) || $result )
		$frontend->RegisterMessage( 'Settings saved' );
	else
		$frontend->RegisterMessage( 'Update failed. Try again.', 'error' );
	
	component_PaymentSelection::clearCompile();
	
	redirect( $_SERVER['REQUEST_URI'] );
}

// Delete membership type
if ( isset( $_POST['delete_membership_type'] ) )
{
	switch ( AdminMembership::DelMembershipType( $membership_type_id ) )
	{
		case -2:
			$frontend->RegisterMessage( 'Can\'t delete membership type with existing members', 'notice' );
			break;
		case -3:
			$frontend->RegisterMessage( 'Can\'t delete default membership type', 'notice' );
			break;
		case -5:
			$frontend->registerMessage( 'You cannot delete System Guest membership type', 'notice' );
			break;
		case 1:
			$frontend->RegisterMessage( 'Membership type has been deleted' );
			break;
		default:
			$frontend->RegisterMessage( 'Removal failed', 'error' );
	}
		component_PaymentSelection::clearCompile();
		redirect( 'membership_type_list.php' );	
}

// Add a new plan
if ( @$_POST['command'] == 'add_plan' )
{
	$plan_arr['price'] = @$_POST['price'];
	$plan_arr['period'] = @$_POST['period'];
	$plan_arr['unit'] = @$_POST['unit'];
	$plan_arr['is_recurring'] = @$_POST['is_recurring'];

	$result = AdminMembership::AddMembershipTypePlan( $membership_type_id, $plan_arr );
	switch ( $result )
	{
		case 1:
			$frontend->RegisterMessage( 'Membership plan has been added' );
			break;
		case 0:
			$frontend->RegisterMessage( 'Plan not added', 'error' );
			break;
		case -1:
			$frontend->RegisterMessage( 'Plan not added - incorrect data' );
			break;
		case -2:
			$frontend->RegisterMessage( 'Plan not added - trial membership can have only one plan', 'notice' );
			break;
	}
	component_PaymentSelection::clearCompile();
	redirect( $_SERVER['REQUEST_URI'] );
}

// Delete membership type's plans
if ( isset( $_REQUEST['delete_plans'] ) && isset( $_REQUEST['plan_id_arr'] ) )
{
	if ( count( $_REQUEST['plan_id_arr'] ) > 0 )
	{
		switch( AdminMembership::DeleteMembershipPlans( $_REQUEST['plan_id_arr'] ) )
		{
			case 1:
				$frontend->RegisterMessage( 'Membership plans have been deleted ' );
				break;
			case 0:
				$frontend->RegisterMessage( 'Plan not deleted. Try again.' );
				break;
			case -1:
				$frontend->RegisterMessage( 'Plan not deleted. Incorrect input data.' );
				break;
		}
	}
	component_PaymentSelection::clearCompile();
	redirect( 'membership_type.php?membership_type_id='.$membership_type_id );
}

// Update membership type's plans
if ( isset( $_POST['update_plans'] ) && isset( $_POST['plan_id_arr'] ) )
{
	$i = 0;
	foreach (@$_POST['plan_id_arr'] as $plan_id)
	{
		$plan_arr[$i]['id'] = $plan_id;
		$plan_arr[$i]['price'] = @$_POST['price_'.$plan_id];
		$plan_arr[$i]['period'] = @$_POST['period_'.$plan_id];
		$plan_arr[$i]['unit'] = @$_POST['unit_'.$plan_id];
		$plan_arr[$i]['type'] = isset($_POST['type_'.$plan_id]) ? $_POST['type_'.$plan_id] : 'n';
		$i++;
	}
	if ( $i > 0 )
	{
		$result = AdminMembership::UpdateMembershipPlans( $membership_type_id, $plan_arr );
		if ( $result )
			$frontend->RegisterMessage( 'Membership plans have been updated' );
		else
			$frontend->RegisterMessage( 'Plans not updated', 'notice' );
		
		showPaymentProviderNotices();
	}
	component_PaymentSelection::clearCompile();	
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( @$_POST['command'] == 'available_for' )
{
	if ( !is_array( $_POST['available_for'] ) )
		$_POST['available_for'] = array();
	$_availability = 0;
	foreach ( $_POST['available_for'] as $_value )
		$_availability = $_availability + $_value;
	
	if ( AdminMembership::UpdateMembershipTypeAvailability( $membership_type_id, $_availability ) )
		$frontend->RegisterMessage( 'Setting saved.' );
	else
		$frontend->RegisterMessage( 'Update failed. Try again.', 'error' );
	
	component_PaymentSelection::clearCompile();
	redirect( $_SERVER['REQUEST_URI'] );
}

if ( $_POST['command'] == 'add_membership_type_icon' || $_POST['command'] == 'delete_membership_type_icon')
{
	$img_path = DIR_USERFILES.'membership_type_icon_'.$membership_type_id.'.png';
	
	switch ($_POST['command'])
	{	
		case 'add_membership_type_icon':
		
			if ( $_FILES['membership_type_icon_upload']['name'] )
			{
				@unlink($img_path);
		
				try {
					app_Image::resize($_FILES['membership_type_icon_upload']['tmp_name'], 40, 40, true, $img_path);
					app_Image::convert($img_path, IMAGETYPE_PNG, $img_path);
				}
				catch (SK_ImageException $e) {
					$code = $e->getCode();

					switch ($code) {
						case app_Image::ERROR_WRONG_IMAGE_TYPE: 
							$frontend->RegisterMessage( 'Can not create undefined type image, only jpeg, gif and png allowed', 'notice' );
							break;
						case app_Image::ERROR_GD_LIB_NOT_INSTALLED: 
							$frontend->RegisterMessage( 'Your server does not support GD library', 'notice' );
							break;
						default:
							$frontend->RegisterMessage( 'System file upload error', 'notice' );
							break;
					}
					redirect( $_SERVER['REQUEST_URI'] );
				}
				
				@unlink($_FILES['membership_type_icon_upload']['tmp_name']);
				AdminMembership::setMembershipTypeHasIcon($membership_type_id);
				$frontend->registerMessage( 'Membership icon was uploaded' );
			}
			break;
			
		case 'delete_membership_type_icon':
			@unlink($img_path);
			AdminMembership::setMembershipTypeHasNotIcon($membership_type_id);
			break;
	}
	
	component_PaymentSelection::clearCompile();
	redirect( $_SERVER['REQUEST_URI'] );
}

$frontend->assign( 'membership_type_id', $membership_type_id );

// get the membership type's info
$mem_info = AdminMembership::GetMembershipTypeInfo( $membership_type_id );
if ( !$mem_info )
	redirect( URL_ADMIN.'membership_type_list.php' );

foreach ( SK_ProfileFields::get('sex')->values as $_value )
	$mem_info['available_for_sex'][$_value] = ( intval( $mem_info['available_for'] ) & intval( $_value ) )? 1 : 0;

$frontend->assign( 'membership_info', $mem_info );

// get list of services and permissions
$mem_permissions = AdminMembership::GetMembershipServiceListPermissions( $membership_type_id );

$frontend->assign( 'mem_permissions', $mem_permissions );

//get membership type's plans
$mem_plans = AdminMembership::GetMembershipTypePlan( $membership_type_id );
$frontend->assign( 'mem_plans', $mem_plans );
$frontend->assign( 'is_default_membership_type', ($membership_type_id == SK_Config::Section('membership')->default_membership_type_id) );
$frontend->register_function( 'getMembershipTypeIconUrl' , array( 'AdminMembership' , 'getMembershipTypeIconUrl' ) );	
$frontend->assign('membership_type_icon_uploded', AdminMembership::isMembershipTypeIconExist( $membership_type_id )) ;	

$_page['title'] = 'Membership Types';

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'form.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'membership_type.js' );

$template = 'membership_type.html';

$frontend->display( $template );

?>
