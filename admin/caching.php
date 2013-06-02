<?php
$file_key = 'caching';

require_once '../internals/Header.inc.php';

// requiring admin authentication
require_once DIR_ADMIN_INC.'inc.auth.php';

// instantiating admin frontend
require_once 'inc/class.admin_frontend.php';
$frontend = new AdminFrontend();

$unit = ( !empty($_GET['unit']) && in_array($_GET['unit'], array('caching', 'cloudflair')) ) ? $_GET['unit'] : 'caching';
$frontend->assign( 'unit', $unit );

// time units
//$time_unts = array('seconds', 'minutes', 'hours');

// processing post data
if ( !empty($_POST) && $_POST['action'] ) {
	require_once DIR_ADMIN_INC.'cache_process.inc.php';
}

adminConfig::SaveConfigs( $_POST );

adminConfig::getResult($frontend, true);

// displaying page
//$frontend->assign_by_ref('time_units', $time_unts);

// getting cache configs
switch ( $unit )
{
    case 'cloudflair':        
        $active_tab = 'cloudflair';
        $_page['title'] = "CloudFlair";
        
        if ( SK_Config::section('cloudflare')->enable && empty($_SERVER['HTTP_CF_CONNECTING_IP']) )
        {
            $frontend->registerMessage("Warning: You should create an account on CloudFlare.", 'notice');
        }
        
        break;
    case 'caching':
    default:
        $active_tab = 'caching';
        $_page['title'] = "Site Caching";

        $frontend->assign('output_caching', SK_Config::section('layout')->caching);
        $frontend->assign('language_caching', SK_Config::section('languages')->caching);
        break;
}


require_once 'inc.admin_menu.php';

$frontend->display('caching.html');
