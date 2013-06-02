<?php

$file_key = 'membership_types_list';
$active_tab = 'coupon_codes';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_membership.php' );

$frontend = new AdminFrontend();
$membership = new AdminMembership();

adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);

require_once( 'inc.admin_menu.php' );

if ( isset($_POST['action']) )
{
    switch ( $_POST['action'] )
    {
        case 'add':
            if ( $_POST['percent'] > 100 )
            {
                $frontend->RegisterMessage('Discount cannot be greater that 100%', 'notice');
                redirect($_SERVER['REQUEST_URI']);
            }
            
            $res = app_CouponCodes::addCode(
                $_POST['code'], 
                $_POST['start_stamp'], 
                $_POST['expire_stamp'], 
                $_POST['membership_id'], 
                $_POST['percent']
            );
            if ( $res )
            {
                $frontend->RegisterMessage('Coupon code has been added');
            }
            else 
            {
                $frontend->RegisterMessage('Coupon code has not been added', 'notice');
            }
            break;
            
        case 'update':
            $res = app_CouponCodes::updateCodes(
                $_POST['code'], 
                $_POST['start_ts'], 
                $_POST['expire_ts'], 
                $_POST['membership_id'], 
                $_POST['discount']
            );
            if ( $res )
            {
                $frontend->RegisterMessage('Coupon codes have been updated');
            }
            else 
            {
                $frontend->RegisterMessage('Coupon codes have not been updated', 'notice');
            }
            break;
    }
    
    redirect($_SERVER['REQUEST_URI']);
}


if ( isset($_GET['remove']) && (int)$_GET['remove'] )
{
    if ( app_CouponCodes::removeCode($_GET['remove']) )
    {
        $frontend->RegisterMessage('Coupon code has been deleted');
    }
    
    redirect(URL_ADMIN . 'coupon_codes.php');
}

$codes = app_CouponCodes::getList();
$frontend->assign('codes', $codes);

$frontend->assign('date_start', date('m/d/Y'));
$frontend->assign('date_expire', date('m/d/Y', time() + 30*24*60*60));

$ms_list = $membership->GetAllMembershipTypes();
$ms = array();
foreach ( $ms_list as $k => $m )
{
    if ( $m['membership_type_id'] != 1 && $m['limit'] == 'limited' && !$m['paid_by_sms'] )
    {
        $ms[$k] = $m;
        $ms[$k]['label'] = SK_Language::text('%membership.types.'.$m['membership_type_id']) . ' ('.$m['type'].')';
    }
}
$frontend->assign('ms', $ms);

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

$_page['title'] = "Coupon Codes";

$frontend->display('coupon_codes.html');
