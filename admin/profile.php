<?php

$file_key = 'profiles';
$active_tab = 'statistic';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC . 'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_membership.php' );
require_once( DIR_ADMIN_INC . 'class.admin_profile.php' );
require_once( DIR_ADMIN_INC . 'class.admin_profile_field.php' );

// requiring applications

require_once( DIR_ADMIN_INC . 'fnc.blocked_ip.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );
require_once( DIR_ADMIN_INC . 'fnc.profile.php' );

$profile_id = intval($_GET['profile_id']);

if ( !is_numeric($_GET['profile_id']) || !intval($_GET['profile_id']) )
    throw new Exception('Undefined profile id');


if ( $_POST['set_membership'] )
{
    AdminMembership::GiveMembershipToProfile($profile_id, array('membership_type_id' => $_POST['membership_type'], 'amount' => $_POST['numbers'][$_POST['membership_type']], 'period' => $_POST['numbers'][$_POST['membership_type']], 'units' => $_POST['units'][$_POST['membership_type']]));

    $frontend->registerMessage('Membership has been given to the profile.');
    redirect($_SERVER['REQUEST_URI']);
}

if ( $_POST['edit_profile'] )
{
    $fields = checkEditProfileFields($_POST);

    if ( $fields["email"] != app_Profile::getFieldValues($profile_id, 'email') )    {
        $query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `" . TBL_PROFILE . "` WHERE `email`='?'", $fields["email"]);
        if ( (bool) SK_MySQL::query($query)->fetch_cell() )        {
            unset($fields["email"]);
            $frontend->registerMessage('Email not changed. User with this email already exists', 'error');
        }
    }

    if ( $fields["username"] != app_Profile::getFieldValues($profile_id, 'username') )    {
        $query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `" . TBL_PROFILE . "` WHERE `username`='?'", $fields["username"]);
        if ( (bool) SK_MySQL::query($query)->fetch_cell() )        {
            unset($fields["username"]);
            $frontend->registerMessage('Username was not changed. User with same username already exists', 'error');
        }
    }


    $base = array();
    $extend = array();
    foreach ( $fields as $name => $value )
    {
        try        {
            $pr_field = SK_ProfileFields::get($name);
        }
        catch ( Exception $e )        {
            continue;
        }

        if ( $pr_field->base_field )
        {
            $base[] = "`$name`=" . (isset($value) ? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)");
        }
        else
        {
            $extend[] = "`$name`=" . (isset($value) ? SK_MySQL::placeholder("'?'", $value) : "DEFAULT(`$name`)");
        }
    }
    $records_affected = 0;

    if ( count($base) )    {
        $query = "UPDATE `" . TBL_PROFILE . "` SET " . implode(',', $base) . " WHERE `profile_id`=$profile_id";

        SK_MySQL::query($query);
    }
    $records_affected += SK_MySQL::affected_rows();
    if ( count($extend) )    {
        $query = "UPDATE `" . TBL_PROFILE_EXTEND . "` SET " . implode(',', $extend) . " WHERE `profile_id`=$profile_id";
        SK_MySQL::query($query);
    }
    $records_affected += SK_MySQL::affected_rows();

    if ( $records_affected )
        $frontend->registerMessage('Profile modified');
    else
        $frontend->registerMessage('Profile not modified', 'notice');

    if ( $_POST['edit_set_reviewed'] )
    {
        if ( adminProfile::setReviewed($profile_id, 'y') )
        {

            if ( app_Features::isAvailable(app_Newsfeed::FEATURE_ID) )
            {
                $newsfeedAction = app_Newsfeed::newInstance()->getAction('profile_join', $profile_id);
                if ( !empty($newsfeedAction) )
                {
                    $newsfeedAction->setStatus('active');
                    app_Newsfeed::newInstance()->saveAction($newsfeedAction);
                }

                $newsfeedAction = app_Newsfeed::newInstance()->getAction('profile_edit', $profile_id);
                if ( !empty($newsfeedAction) )
                {
                    $newsfeedAction->setStatus('active');
                    app_Newsfeed::newInstance()->saveAction($newsfeedAction);
                }
            }

            $frontend->registerMessage('Profile marked as reviewed');
        }
    }

    redirect($_SERVER['REQUEST_URI']);
}

if ( $_POST['profile_unregister'] )
{
    $with_profile_content = isset($_POST['with_profile_content']) ? 1 : 0;
    if ( $_POST['with_blocking_ip'] )
    {
        switch ( adminProfile::blockProfileIP($profile_id) )
        {
            case -2:
                $frontend->registerMessage('Profile IP is unavailable and can not be blocked', 'notice');
                break;
            case -3:
                $frontend->registerMessage('Profile IP was already blocked', 'notice');
                break;
            case 1:
                $frontend->registerMessage('Profile IP was successfully blocked for registration');
                break;
        }
    }

    $groups = app_Groups::getGroupsProfileCreated($profile_id);
    if ( $groups['total'] > 0 )
    {
        $str = "";
        foreach ( $groups['list'] as $gr )
        {
            $str .= "'" . $gr['title'] . "', ";
        }
        $str = substr($str, 0, strlen($str) - 2);

        $msg = app_Profile::username($_profile_id) . " is a creator of the following groups: " . $str
            . ". You need to remove profile groups before deleting profile";
        $frontend->registerMessage($msg, "notice");

        redirect(URL_ADMIN . 'profile.php?profile_id=' . $profile_id);
    }
    else
    {
        if ( app_Profile::unregisterProfile($profile_id, '', $with_profile_content) )
            $frontend->registerMessage('Profile deleted');
        else
            $frontend->registerMessage('Profile not deleted correctly', 'error');
        redirect(URL_ADMIN . 'profiles.php');
    }
}

if ( $_POST['give_credits'] )
{
    if ( ($amount = floatval($_POST['credit_amount'])) == 0 )
    {
        $frontend->registerMessage('Please specify correct number of credits', 'notice');
    }
    else if ( app_UserPoints::giveCreditsByAdmin($profile_id, $_POST['credit_amount'], $_POST['credit_comment']) )
    {
        $frontend->registerMessage('Profile has been given ' . $amount . ' credits.');
    }
    redirect(URL_ADMIN . 'profile.php?profile_id=' . $profile_id);
}

if ( $_GET['delete_membership'] )
{
    controlAdminGETActions();

    if ( AdminMembership::deleteProfileMembership($_GET['membership_id'], $profile_id) )
        $frontend->registerMessage('Profile active membership deleted');
    else
        $frontend->registerMessage('Membership not deleted', 'error');

    redirect(URL_ADMIN . 'profile.php?profile_id=' . $profile_id);
}

$full_profile_info = app_Profile::getFieldValues($profile_id);

if ( !$full_profile_info )
    exit('No info about profile');

app_Membership::checkSubscriptionTrialMembership($profile_id);

$full_profile_info['thumb_href'] = app_ProfilePhoto::getThumbURL($profile_id, false);

$full_profile_info['activity_info'] = app_Profile::ActivityInfo($full_profile_info['activity_stamp'], app_Profile::isOnline($profile_id), true);

$full_profile_info['age'] = app_Profile::getAge($full_profile_info['birthdate']);

$full_profile_info['photos_count'] = adminProfile::getCountPhotos($profile_id);

$full_profile_info['media_count'] = adminProfile::getCountMedia($profile_id);

$full_profile_info['music_count'] = adminProfile::getCountMusic($profile_id);

$full_profile_info['membership_info'] = app_Membership::profileCurrentMembershipInfo($profile_id);

$full_profile_info['membership_info']['history'] = getProfileTransactionHistory($profile_id);

$full_profile_info['membership_info']['current_memberships'] = app_Membership::GetProfileAllMemberships($profile_id);

$full_profile_info['is_ip_blocked'] = searchBlockedIp($full_profile_info['join_ip']);

$full_profile_info['profile_url'] = SK_Navigation::href("profile", array('profile_id' => $profile_id));

$full_profile_info['site_moderator'] = adminProfile::isSiteModerator($profile_id);

$full_profile_info['total_referals'] = app_Referral::getPersonalReferralsNum($profile_id);

$full_profile_info['total_mails'] = adminProfile::countMailboxConversations($profile_id);
$full_profile_info['is_unsubscribed_mail'] = app_Unsubscribe::isProfileUnsubscribed($profile_id);

$full_profile_info['is_hot'] = app_HotList::isHot( $profile_id );

$frontend->assign_by_ref('full_profile_info', $full_profile_info);

//$smiles = appSmile::getSmilesUnique();
$profile_info_for_review = app_ProfileField::getMarkedProfileFields($profile_id);

foreach ( $full_profile_info as $_field_name => $_field_value )
{
    try    {
        $field_info = SK_ProfileFields::get($_field_name);
    }    catch ( SK_ProfileFieldException $e )    {
        continue;
    }

    if ( in_array($field_info->presentation, AdminProfileField::getNoneDisplayFieldPresentations()) || in_array($_field_name, AdminProfileField::getLocationFields()) || !$_field_value && !in_array($_field_name, array('password', 'username', 'email', 'sex')) )
        continue;

    $_id = "profile_field_id_$_field_name";
    $smile_list = array();
    if ( ($field_info->presentation == 'text' || $field_info->presentation == 'textarea') && $_field_name != 'email' && $_field_name != 'username' )
    {
        /* foreach ( $smiles as $key => $smile )
          {
          $smile_list[$key]['href'] = "javascript:smile(\$('$_id'),'{$smile['code']}')";
          $smile_list[$key]['img'] = URL_UPL_IMG."smiles/{$smile['url']}";
          } */
    }

    $changed = in_array($field_info->profile_field_id, $profile_info_for_review) ? true : false;

    $main_profile_info[$field_info->profile_field_section_id][$_field_name] = array
        (
        'id' => $_id,
        'name' => $_field_name,
        'display_value' => $_field_value,
        'profile_field_id' => $field_info->profile_field_id,
        'presentation' => $field_info->presentation,
        'all_values' => $field_info->values,
        'matching' => $field_info->matching,
        'smile' => $smile_list,
        'changed' => $changed
    );
}


$frontend->assign_by_ref('main_profile_info', $main_profile_info);

$show_credits = app_Features::isAvailable(44);
$frontend->assign('show_credits', $show_credits);
if ( $show_credits )
{
    $frontend->assign('balance', app_UserPoints::getProfilePointsBalance($profile_id));
}

// assign config vars
$frontend->assign('profile_photo_width', SK_Config::section('photo')->Section('general')->preview_width);
$frontend->assign('profile_photo_height', SK_Config::section('photo')->Section('general')->preview_height);

$frontend->register_function('print_field_value', 'printProfileField');

$frontend->register_function('print_membership_expiration', 'frontendGetMembershipExpirationCountDate');

//give membership
$given_membership_types = AdminMembership::getGivenMembershipTypes($full_profile_info['sex']);
$frontend->assign_by_ref('membership_types', $given_membership_types);


// Register Ajax functions
/* $ajax->registerSessionFunction( 'setProfileStatus', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'setProfileEmailVerified', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'setProfileReviewedStatus', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'updatePhotosInfo', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'updateMediaInfo', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'sendMessageToProfile', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'markProfileAsFeatured', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'blockProfileIP', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'addNote', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'deleteAdminNote', DIR_ADMIN_INC.'ajax.profile.php' );
  $ajax->registerSessionFunction( 'setProfileUnsubscribed', DIR_ADMIN_INC.'ajax.profile.php' ); */

$frontend->IncludeJsFile(URL_ADMIN_JS . 'opacity.js');
$frontend->IncludeJsFile(URL_ADMIN_JS . 'profile.js');
//$frontend->IncludeJsFile( URL_MAIN_JS.'smile.js' );
$frontend->IncludeJsFile(URL_ADMIN_JS . 'profile_list.js');

$frontend->registerOnloadJS("adminProfile.profile_notes = " . json_encode(adminProfile::getAdminNotes($profile_id)) . ";");
$frontend->registerOnloadJS("adminProfile.construct(" . $profile_id . ");");

$frontend->assign('photo_ver_avaliable', app_Features::isAvailable(42));

$_page['title'] = 'Profile: ' . $full_profile_info['username'];

$frontend->display( 'profile.html' );

?>
