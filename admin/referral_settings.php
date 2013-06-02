<?php

$file_key = 'referrals';
$active_tab = 'settings';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'fnc.affiliate.php' );
require_once( DIR_AFFILIATE_INC.'fnc.affiliate.php' );

$frontend = new AdminFrontend();
require_once( 'inc.admin_menu.php' );

adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend);

$_page['title'] = 'Referrals';

$template = 'referral_settings.html';

$frontend->display($template);