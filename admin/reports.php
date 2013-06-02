<?php

$file_key = 'reports';

$active_tab = 'reports_list';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );
require_once( DIR_ADMIN_INC.'fnc.report.php' );

require_once( 'inc/class.admin_frontend.php' );
require_once( 'inc/class.admin_configs.php' );
require_once( 'inc/fnc.profile_list.php' );

$frontend = new AdminFrontend();

$res_per_page = profileList_getResultsPerPageValue();

if( !isset($_GET['_page'] ))
	$_GET['_page'] = 1;
	
$limit = navigationDBLimit( $res_per_page, $_GET['_page'] );

adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);

$reports = getProfilesReports( $_GET['_page'], $res_per_page );

$results_num = count($reports['page']);
$total_results_num = count($reports['all']);

$frontend->assign_by_ref( 'total_results_num', $total_results_num );
$frontend->assign_by_ref( 'users_reports', $reports['page'] );
$frontend->assign( 'result_range', $limit['begin'].'-'.($limit['begin'] + $results_num - 1) );
$frontend->assign( 'rpp_select', ResPerPageSelect( array(10,20,30), $res_per_page ) );
$frontend->assign( 'navigation_pages', navigationPages( ceil( $total_results_num/$res_per_page ) ) );

require_once( 'inc.admin_menu.php' );

$_page['title'] = 'Reports';

$frontend->display( 'reports.html' );

?>
