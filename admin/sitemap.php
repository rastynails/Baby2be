<?php

if(isset($_GET['download']) && $_GET['download'] == 'y'){
	header("Content-Type: application/force-download; name=\"sitemap.xml\"");
	$fsize=filesize($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml');
	$buffer = fread( fopen($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml', "rb"), ($fsize) );
	header("Content-Length: $fsize");
       header("Content-Disposition: attachment; filename=\"sitemap.xml\""); 
	echo $buffer;
	exit();
}

$file_key = 'sitemap';
$active_tab = 'sitemap';



require_once( '../internals/Header.inc.php' );
iconv_set_encoding("internal_encoding", "UTF-8");
// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );


require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );

$frontend = new AdminFrontend( $language );

$frontend->IncludeJsFile(URL_ADMIN_JS.'jquery-plugins/datepicker/js/eye.js');
$frontend->IncludeJsFile(URL_ADMIN_JS.'jquery-plugins/datepicker/js/utils.js');
$frontend->IncludeJsFile(URL_ADMIN_JS.'jquery-plugins/datepicker/js/layout.js');
$frontend->IncludeJsFile(URL_ADMIN_JS.'jquery-plugins/datepicker/js/datepicker.js');

$frontend->includeCSSFile(URL_ADMIN_JS.'jquery-plugins/datepicker/css/datepicker.css');
//$frontend->includeCSSFile(URL_ADMIN_JS.'jquery-plugins/datepicker/css/layout.css');

require_once( 'inc.admin_menu.php' );

$_page['title'] = 'SEO SiteMap';

require_once '../internals/Debug.func.php'; 


$admin_config = new adminConfig();

switch($_POST['command']){
	case 'sitemap_generateOrUpdate':
		$map = app_SEOSitemap::generate();
		$fd = fopen(DIR_SITE_ROOT.'sitemap.xml', 'w');
		fwrite( $fd, $map );
		
		$frontend->registerMessage("Sitemap generated.");
		redirect($_SERVER['PHP_SELF']);
		break;	
		
	case 'sitemap_set_autoUpdate_settings':
		
		$doPingGoogle = ( $_POST['google_manuallySubmitedFirst'] == 'on' )? 1 : 0;
		$doPingYahoo = ( $_POST['yahoo_manuallySubmitedFirst'] == 'on' )? 1 : 0;
		$_arr = explode('-', $_POST['date']);
		
//--- generate random time
		$updateTimestamp = mktime(0, 0, 0, $_arr[1], $_arr[2], $_arr[0]);
		
		$min = 60;
		$hour = 60 * $min;
		$day = 24 *$hour;
		
		$updateTimestamp += rand(0, 48) *30*$min + 5*$min;
//--- ~generate random time

		$admin_config->SaveConfig('seo-sitemap', 'doPingGoogle', $doPingGoogle);
		$admin_config->SaveConfig('seo-sitemap', 'doPingYahoo', $doPingYahoo);
		$admin_config->SaveConfig('seo-sitemap', 'updateTimestamp', $updateTimestamp);
		break;
}

$SEO_sitemap_config = SK_Config::section("seo-sitemap")->getConfigsList();

$frontend->assign('doPingGoogle', $SEO_sitemap_config['doPingGoogle']->value);
$frontend->assign('doPingYahoo', $SEO_sitemap_config['doPingYahoo']->value);
$frontend->assign('yahoo_appId', $SEO_sitemap_config['yahoo_appId']->value);

$frontend->assign('updateDate', 
	date('Y', $SEO_sitemap_config['updateTimestamp']->value).'-'.date('m', $SEO_sitemap_config['updateTimestamp']->value).'-'.date('d', $SEO_sitemap_config['updateTimestamp']->value));	

if(file_exists(DIR_SITE_ROOT.'sitemap.xml'))
	$frontend->assign('showDownloadLink', true);
else 
	$frontend->assign('showDownloadLink', false);
	
$template = 'sitemap.html';


// display template
$frontend->display( $template );
?>