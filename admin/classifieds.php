<?php

$file_key = 'classifieds';
$active_tab = 'classifieds_settings';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );
require_once( DIR_ADMIN_INC.'fnc.classifieds.php' );

$frontend = new AdminFrontend();

if ( $_POST ) 
{

	if ( $_POST['add_currency'] && trim($_POST['code']) && trim($_POST['sign']) ) {
		addCurrency(  trim($_POST['code']), trim($_POST['sign']) );
		redirect( $_SERVER['PHP_SELF'] );
	}
	
	if ( $_POST['delete'] && $_POST['config_id'] && $_POST['code'] ) {

        $delete_result = deleteCurrency( $_POST['config_id'], $_POST['code'] );

        switch ($delete_result)
        {
            case -1:
                $frontend->registerMessage('Currency is in use', 'error');
                break;
            default :
                redirect( $_SERVER['REQUEST_URI']  );
                break;
        }

	}
		
}

adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);

// require file with specific functions
require_once( 'inc.admin_menu.php' );

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

$frontend->assign('section', 'classifieds');

$_page['title'] = "Classifieds Settings";
$template = 'classifieds_settings.html';

$section = new SK_Config_Section('classifieds');
$curencies = $section->getConfigValues('currency');

$frontend->assign('curencies', $curencies);
// display template
$frontend->display( $template );

?>
