<?php

$file_key = 'profiles';
$active_tab = 'statistic';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile_field.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

require_once( 'inc/fnc.profile_list.php' );
require_once( DIR_ADMIN_INC.'class.admin_profile.php' );

// requiring applications
require_once( DIR_APPS.'Profile.app.php' );
require_once( DIR_APPS.'ProfileField.app.php' );

$frontend = new AdminFrontend( );

$_page['title'] = 'Profile List';

require_once( 'inc.admin_menu.php' );

if ( isset($_POST['action']) )
{
    if ( !isset($_POST['profiles_arr']) )
    {
        $frontend->registerMessage('Select profiles', 'notice');
        redirect($_SERVER['REQUEST_URI']);
    }

    switch ( $_POST['action'] )
    {
        case 'send_msg':            
            $msg_subject = trim( $_POST['msg_subject'] );
            $msg_txt = trim( $_POST['msg_txt'] );
            
            if ( !strlen( $msg_subject ) || !strlen( $msg_txt ) )
            {
                $frontend->registerMessage( 'Missing message text or subject', 'error' );
                redirect( $_SERVER['REQUEST_URI'] );
            }
            else 
            {
                foreach ( $_POST['profiles_arr'] as $_profile_id )
                {
                    if( !app_Unsubscribe::isProfileUnsubscribed($_profile_id) || $_POST['ignore_unsubscribe'] == 'on' )     
                    {
                        // sending email
                        $msg = app_Mail::createMessage(app_Mail::FAST_MESSAGE)
                                ->setRecipientProfileId($_profile_id)
                                ->assignVar('unsubscribe_url', app_Unsubscribe::getUnsubscribeLink($_profile_id))
                                ->setSubject($msg_subject)
                                ->setContent($msg_txt);
                        if (app_Mail::send($msg)) {
                            $_counter++;
                        }
                    }
                }
                
                $frontend->registerMessage( 'Message was sent to ('.$_counter.') profiles' );
            }
            break;
            
        case 'set_membership':
            $_counter = 0;
            foreach ( $_POST['profiles_arr'] as $_profile_id )
            {        
                if ( AdminMembership::GiveMembershipToProfile($_profile_id, 
                    array(
                        'membership_type_id' => $_POST['membership_type'], 
                        'amount' => $_POST['numbers'][$_POST['membership_type']], 
                        'period' => $_POST['numbers'][$_POST['membership_type']], 
                        'units' => $_POST['units'][$_POST['membership_type']])
                    )
                )
                    $_counter++;
            }
            
            $frontend->registerMessage( 'Membership has been given to '.$_counter.' profiles ' );
            redirect( $_SERVER['REQUEST_URI'] );
            break;
            
        case 'send_verify_email':
            foreach ( $_POST['profiles_arr'] as $_profile_id )
            {
                $_profile_info = app_Profile::getFieldValues( $_profile_id, array( 'email' ) );
                app_EmailVerification::addRequestEmailVerification( $_profile_id, $_profile_info['email'] );
                $_counter ++;
            }
                
            $frontend->registerMessage( 'Verification email was sent to ('.$_counter.') profiles' );
            redirect($_SERVER['REQUEST_URI']);
            break;
            
        case 'set_status':
            foreach ( $_POST['profiles_arr'] as $_profile_id )
            {
                adminProfile::setProfileStatus( $_profile_id, $_POST['profilelist_status'] );
                if ( $_POST['set_reviewed'] )
                    adminProfile::setReviewed( $_profile_id, 'y' );
            }
            
            $frontend->registerMessage( 'Status was set for selected profiles' );
            redirect( $_SERVER['REQUEST_URI'] );
            break;
            
        case 'delete_profiles':
            $deleted = 0;
            foreach ( $_POST['profiles_arr'] as $_profile_id ) 
            {   
                // custom check for group owners
                $groups = app_Groups::getGroupsProfileCreated($_profile_id);
                if ($groups['total'] > 0)
                {
                    $str = "";
                    foreach ($groups['list'] as $gr)
                    {
                        $str .= "'" . $gr['title'] . "', ";     
                    }
                    $str = substr($str, 0, strlen($str) - 2);
                    
                    $msg = app_Profile::username($_profile_id) . " is a creator of the following groups: ". $str 
                    . ". You need to remove the group before deleting profile";
                    $frontend->registerMessage( $msg, "notice" );
                }
                else
                {
                    app_Profile::unregisterProfile( $_profile_id, '', empty($_POST['delete_with_content']) ? 0 : 1 );
                    $deleted++;
                }
            }
            if ( $deleted )
            {
                $frontend->registerMessage( $deleted . ' of selected profiles were deleted' );
            }
            redirect( $_SERVER['REQUEST_URI'] );
            
            break;
    }
    
}

if ( isset($_POST['update_fields']) )
{
	if ( !isset( $_POST['fields_arr'] ) )
	{
		$frontend->registerMessage( 'Select fields', 'notice' );
		redirect( $_SERVER['REQUEST_URI'] );
	}

	updateProfileListFieldSettings( $_POST['fields_arr'] );
	$frontend->registerMessage( 'Fields changed' );
	redirect( $_SERVER['REQUEST_URI'] );	
}

$res_per_page = profileList_getResultsPerPageValue();

if( !(int)@$_GET['_page'] )
	$_GET['_page'] = 1;

$limit = navigationDBLimit( $res_per_page, $_GET['_page'] );

$_result = MySQL::fetchResource( "SELECT `profile_id` FROM `".TBL_PROFILE_ONLINE."`" );
while( $_row = mysql_fetch_array( $_result, MYSQL_NUM ) )
	$online_profiles[$_row[0]] = true;

$definition = profileList_getSQLDefinition( @$_GET['_sf'], @$_GET['_so']);

$total_results_num = MySQL::fetchField( "SELECT COUNT(DISTINCT `pr`.`profile_id`) 
	FROM `".TBL_PROFILE."` AS `pr`
	LEFT JOIN `".TBL_PROFILE_EXTEND."` AS `ex` ON(`pr`.`profile_id` = `ex`.`profile_id`)
	{$definition['JOIN']}
	{$definition['WHERE']}" );

	
$query = "SELECT `pr`.*, `ex`.* {$definition['SELECT']}
	FROM `".TBL_PROFILE."` AS `pr`
	LEFT JOIN `".TBL_PROFILE_EXTEND."` AS `ex` ON(`pr`.`profile_id` = `ex`.`profile_id`)
	{$definition['JOIN']} {$definition['WHERE']} {$definition['ORDER']} {$limit['sql']}";

$result = SK_MySQL::query($query);

$profile = array();
while( $_row = $result->fetch_assoc() )
{
	foreach ( $definition['checked_fields'] as $field )
	{
		switch ( $field )
		{
			case 'sex':
				$profile['sex'] = SK_Language::section('profile_fields.value')->text('sex_'.$_row['sex']);
				break;
			
			case 'profile_id':
			case 'username':
			case 'email_verified':
			case 'has_photo':
			case 'has_media':		
            case 'has_music':
			case 'reviewed':
			case 'status':
			case 'birthdate':
			case 'join_stamp':
			case 'featured':
			case 'i_am_at_least_18_years_old':
			case 'i_agree_with_tos':
			case 'language_id':
				$profile[$field] = @$_row[$field];
				break;
						
			case 'membership_type_id':
				$profile['membership_type_id'] = SK_Language::text('%membership.types.' . $_row['membership_type_id']);
				break;
				
			case 'activity_stamp':
				$profile['activity_stamp'] = app_Profile::ActivityInfo( $_row['activity_stamp'], @$online_profiles[$_row['profile_id']], true );
				break;
					
			default:
				if ( $_row[$field] && SK_Language::section('profile_fields.value')->key_exists($field.'_'.$_row[$field]) )
					$profile[$field] = SK_Language::section('profile_fields.value')->text($field.'_'.$_row[$field]);
				else 
				    $profile[$field] = @$_row[$field];
				break;
				
		}
		if ( isset($profile) )
		{
			$profile_f[] = $profile;
			unset($profile);
		}
	}
	$profiles[$_row['profile_id']]['fields'] = $profile_f;
	$profiles[$_row['profile_id']]['href'] = adminProfile::getProfileURL( $_row['profile_id'] );

	unset($profile_f);
}

function getURL($params, $query)
{
	return sk_make_url(null, $query);
}

$results_num = $result->num_rows();

$frontend->assign( 'db_results', $limit['begin'].'-'.($limit['begin']+$results_num-1) );

$frontend->assign_by_ref( 'total_results_num', $total_results_num );

$frontend->assign_by_ref( 'profiles', $profiles );

$frontend->assign_by_ref( 'default_fields', $definition['default_fields'] );
$frontend->assign_by_ref( 'extend_fields', $definition['extend_fields'] );
$frontend->assign_by_ref( 'checked_fields', $definition['checked_fields'] );

$field_count = count($definition['checked_fields']) + 1;
$frontend->assign_by_ref( 'field_count', $field_count );

$sort_order = @$_GET['_so'];
	$frontend->assign_by_ref( 'sort_order', $sort_order );

$sort_field = @$_GET['_sf'];
	$frontend->assign_by_ref( 'sort_field', $sort_field );

$frontend->assign( 'rpp_select', ResPerPageSelect( array(10,30,50,100), $res_per_page ) );

$frontend->assign( 'navigation_pages', navigationPages( ceil( $total_results_num/$res_per_page ) ) );

$given_membership_types = AdminMembership::getGivenMembershipTypes();

$frontend->assign_by_ref( 'membership_types', $given_membership_types );

$frontend->register_function('type_membership_fields','frontendType_membership_fields');
$frontend->register_block('make_url', 'getURL');

$frontend->IncludeJsFile( URL_ADMIN_JS.'opacity.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'form.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'profile_list.js' );

$frontend->display( 'profile_list.html' );



?>
