<?php

$file_key = 'referrals';
$active_tab = 'referral_list';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_membership.php' );
require_once( 'inc/fnc.profile_list.php' );
require_once( 'inc/class.admin_profile.php' );

$frontend = new AdminFrontend();

$_page['title'] = 'Referrals';

require_once( 'inc.admin_menu.php' );
if ( $_GET['delete_referral'] )
{
	controlAdminGETActions();
	
	if ( app_Profile::unregisterProfile( $_GET['delete_referral'] ) )
		$frontend->registerMessage( 'Referral has been deleted' );
	else
		$frontend->registerMessage( 'Referral not deleted', 'error' );
	
	redirect( $_SERVER['PHP_SELF'] );
}
//------

if( intval($_GET['month']) && ($_GET['month']<=12) )
{
	if($_GET['month'] == 1)
		$bounds[0] = 12;
	else 
		$bounds[0] = $_GET['month'] -1;
	$bounds[1] = $_GET['month'];
	$default_month = $_GET['month'];
}	



if((int)$_GET['referrer_id'] )
	$referrer_id = $_GET['referrer_id'];
	
//------
//appReferral::trackReferralsPurchase(182,203);

if($bounds)
{
	$start_stamp = ( $bounds[0] == 12 )
		? 
			mktime(0, 0, 0, ($bounds[0]+1), 1, (date('Y')-1) ) 
		: 
			mktime(0, 0, 0, ($bounds[0]+1), 1, (date('Y')) );
				
	$end_stamp = mktime(0, 0, 0, ($bounds[1]+1), 1, date('Y'));
}
//--	



$page = (intval($_GET['page']))?$_GET['page']:1;		
$_rpp = 10;
		
$order_by = ($_GET['order_by'])?$_GET['order_by']:'referral_id';
$sort_order = ($_GET['sort_order'] == 'ASC')?'DESC':'ASC';

//$ext_order_by = ($_GET['ext_order_by'])?$_GET['ext_order_by']:'referral_id';
//$ext_sort_order = ($_GET['ext_sort_order'] == 'ASC')?'ASC':'DESC';



	//$arr = appReferral::getAccount($ext_order_by, $ext_sort_order , $order_by, $sort_order, $page, $_rpp, array($start_stamp,$end_stamp), $referrer_id );
	if($_GET['page'] == 1)
		$arr = app_Referral::getAccount($order_by, $sort_order, $order_by, $sort_order, 1, $_rpp, array($start_stamp,$end_stamp), $referrer_id );
	else 
		$arr = app_Referral::getAccount($order_by, $sort_order, $order_by, $sort_order, $page, $_rpp, array($start_stamp,$end_stamp), $referrer_id );
	
$referrals = $arr['referrals'];
$total = $arr['total'];
$_nav_url = sk_make_url(null, array('page'=>null));

$_sort_by_url = sk_make_url(null, array(
	'order_by' => null,
	'sort_order' => null,
	'page' => null
));

$_ext_sort_by_url = sk_make_url(null, array(
	'ext_order_by' => null,
	'ext_sort_order' => null,
	'order_by' => null,
	'sort_order' => null,
));

function frontendView4MonthSelect( $params )
{
	
	for ( $m = 1; $m<13; $m++) {
		$months[$m] = SK_Language::section("i18n.date")->text("month_short_$m");
	}
							
	$_url = sk_make_url(null, array(
		'page' => null,
		'month' => null,
		'order_by' => null,
		'sort_order' => null,
	));
	
	$_out = '<select onchange="location.href=this.value">';
	$_out.="<option value=\"$_url\">all time</option>";
	foreach( $months as $num=>$month )
	{
		$_selected = ( $num == $params['selected'] ) ? 'selected="selected"' : '';
		$_out .= '<option value="'.$_url.'&month='.$num.'" '.$_selected.'>'.$month.'</option>';
	}
	
	$_out .= '</select>';
	
	return $_out;
}

$frontend->assign( 'ext_order_by', $ext_order_by );
$frontend->assign( 'ext_sort_order', $ext_sort_order );

// page navigation
$frontend->assign( 'total', $total );
$frontend->assign( 'page', $page );
$frontend->assign( 'num_on_page', $_rpp );
$frontend->assign('nav_url', $_nav_url);
$frontend->assign('sort_by_url', $_sort_by_url);
$frontend->assign('ext_sort_by_url', $_ext_sort_by_url);
$count = ceil( $total/$_rpp );
for ( $i = 1; $i < $count+1; $i++ )
	$page_arr[] = $i;
$frontend->assign( 'page_arr', $page_arr );
$frontend->assign( 'count', $count );

$frontend->assign('sort_order',$sort_order);

//--
$frontend->assign( 'db_results', $limit['begin'].'-'.($limit['begin']+$results_num-1) );

$frontend->assign_by_ref( 'total_results_num', $total_results_num );
//--
$frontend->assign_by_ref( 'referrals', $referrals );
$frontend->assign( 'default_month', $default_month );
//--
$frontend->assign( 'rpp_select', ResPerPageSelect( array(10,30,50,100), $res_per_page ) );

$given_membership_types =AdminMembership::getGivenMembershipTypes();

$frontend->assign_by_ref( 'membership_types', $given_membership_types );

$frontend->register_function('view4MonthSelect','frontendView4MonthSelect');
$frontend->register_function('type_membership_fields','frontendType_membership_fields');

//$frontend->IncludeJsFile( URL_MAIN_JS.'opacity.js' );
//$frontend->IncludeJsFile( URL_MAIN_JS.'form.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'profile_list.js' );

$frontend->display( 'referral_list.html' );



?>
