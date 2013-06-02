<?php

$file_key = 'referrals';
$active_tab = 'referral_list';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

$frontend = new AdminFrontend();
require_once( 'inc.admin_menu.php' );

$referral_id = intval( $_GET['referral_id'] );
if ( !$referral_id )
{
	redirect( URL_ADMIN.'referral_list.php' );
	exit;
}

// update affiliate info
if ( is_array( $_POST['aff_info'] ) )
{
	$_referral = app_Referral::checkReferralFields( $_POST['aff_info'] );
	if ( !$_referral )
		$frontend->registerMessage( 'Update failed', 'error' );
	else 
	{
		
		if ( app_Referral::updateReferralInfo( $referral_id, $_referral ) )
			$frontend->registerMessage( 'Referral info updated' );
		else
			$frontend->registerMessage( 'Update failed', 'error' );
	}	
	redirect( $_SERVER['REQUEST_URI'] );
}

// update and insert affiliate payments
if ( isset( $_POST['pay_sum'] ) )
{
	if ( isset( $_POST['payment_id'] ) )
	{
		switch ( app_Referral::updateReferralPaymentInfo( $referral_id, $_POST['payment_id'], $_POST['pay_sum'] ) )
		{
			case 1:
				$frontend->registerMessage( 'Referral payment amount updated' );
				break;
			case -2:
				$frontend->registerMessage( 'You can update only last payment', 'notice' );
				break;
			default:
				$frontend->registerMessage( 'Update failed', 'error' );
		}
	}
	else
	{
		if ( app_Referral::addReferralPayment( $referral_id, $_POST['pay_sum'] ) && $_POST['pay_sum'] )
			$frontend->registerMessage( 'New payment record added' );
		else
			$frontend->registerMessage( 'Payment record add failed', 'error' );
	}

	redirect( $_SERVER['REQUEST_URI'] );
}

if( intval($_GET['month']) && ($_GET['month']<=12) )
{
	if($_GET['month'] == 1)
		$bounds[0] = 12;
	else 
		$bounds[0] = $_GET['month'] -1;
	$bounds[1] = $_GET['month'];
	$default_month = $_GET['month'];
	
	if($bounds[0] == 12)
		$start_stamp = mktime(0, 0, 0, ($bounds[0]+1), 1, (date('Y')-1) );
	else
		$start_stamp = mktime(0, 0, 0, ($bounds[0]+1), 1, (date('Y')) );			
	$end_stamp = mktime(0, 0, 0, ($bounds[1]+1), 1, date('Y'));
}
else 
	$default_month = date('n');
	
$current_month = date('n', mktime(0, 0, 0, ($bounds[1]), 1, date('Y')));

$frontend->assign( 'referral_id', $referral_id );

// get and pass referral info
$referrer_id = app_Referral::getReferrerId($referral_id);

$referral_info = app_Referral::getReferralInfo( $referral_id );

$profile_info = app_Profile::getFieldValues( $referral_id, array('username','email', 'password'));
$referral_info['total_paid_referrals'] = app_Referral::getTotalPurchaserReferrals($referral_id);
$referral_info = array_merge($referral_info, $profile_info);

$frontend->assign( 'referral_info', $referral_info );

// get and pass referral payment list
$referral_payments = app_Referral::getReferralAdminPaymentList( $referral_id );
foreach ( $referral_payments as $_key => $_referral_payment )
{
	$referral_payments[$_key]['time_stamp'] = $referral_payments[$_key]['time_stamp'];
	$referral_payments[$_key]['edited_time_stamp'] = ($referral_payments[$_key]['edited_time_stamp'])
	?
		$referral_payments[$_key]['edited_time_stamp']
	:
		0;
}
$frontend->assign( 'referral_payments', $referral_payments );

// get and pass referral payment info if it is selected
$payment_id = intval( $_GET['payment_id'] );
if ( $payment_id )
	$frontend->assign( 'payment_info', app_Referral::getReferralPaymentInfo( $referral_id, $payment_id ) );

$end_stamp = mktime(0,0,0,date('n'),1,date('Y'));
	
function frontendView4MonthSelect( $params )
{
	global $language;
	for ( $m = 1; $m<13; $m++) {
		$months[$m] = SK_Language::section("i18n.date")->text("month_short_$m");
	}
					
	$_url = sk_make_url(null, array('page'=>null));
	
	$_out = '<select onchange="location.href=this.value">';
	$_out.="<option value=\"$_url\">all time</option>";
	foreach( $months as $num=>$month )
	{
		$_selected = ( $num == $params['selected'] ) ? 'selected="selected"' : '';
		$_out .= '<option value="'.$_url.'month='.$num.'" '.$_selected.'>'.$month.'</option>';
	}
	
	$_out .= '</select>';
	
	return $_out;
}


$frontend->register_function('view4MonthSelect','frontendView4MonthSelect');

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'referral.js' );

$_page['title'] = "Referrals";
$template = 'referral.html';

$frontend->display( $template );

?>
