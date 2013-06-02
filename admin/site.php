<?php

$file_key = 'site';
$active_tab = $_GET['unit'];

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );
require_once( DIR_ADMIN_INC.'class.admin_configs.php' );
require_once( DIR_ADMIN_INC.'fnc.classifieds.php' );

$frontend = new AdminFrontend();

if ( !empty($_POST['admin_password']) )
{
    $_POST['admin_password'] = app_Passwords::hashPassword($_POST['admin_password']);
}

if ( !empty($_POST['set_active_cls_on_creation']) && $_POST['set_active_cls_on_creation'] )
{
	approveAllClsItems();
}

if ( $active_tab == 'admin' && !empty($_POST['change_pass']) )
{
    $section = SK_Config::section('site.admin');
    if ( $section->admin_password != app_Passwords::hashPassword($_POST['old_password']) )
    {
        $frontend->registerMessage('Incorrect old password', 'error');

        redirect(sk_make_url());
    }

    if ( $_POST['new_password'] != $_POST['re_new_password'] )
    {
        $frontend->registerMessage('Passwords do not match', 'error');

        redirect(sk_make_url());
    }

    $section->set('admin_password', app_Passwords::hashPassword($_POST['new_password']));
    SK_Config::removeCache();
    $frontend->registerMessage('Password changed');
    redirect(sk_make_url());
}

adminConfig::SaveConfigs($_POST);


$result = adminConfig::getResult(null, false);

if (isset($result['validated'])) {

	if (in_array("quick_search_location_type", $result['validated'])) {
		component_QuickSearch::clearCompile();
	}

	if (in_array("captcha_on_join", $result['validated'])) {
		component_Join::clearCompile();
	}
}
if ($active_tab == 'automode')
{
    $configSection = SK_Config::section('site.automode');
    if (!$configSection->set_active_event_on_submit || !$configSection->set_active_blog_post_on_submit || !$configSection->set_active_cls_on_creation)
    {
        $query = "SELECT COUNT(`profile_id`) FROM `".TBL_SITE_MODERATORS."`";
        if (!SK_MySQL::query($query)->fetch_cell())
        {
            $frontend->assign('notice', 'There are no moderators on the site. You should add at least one moderator for approving events, blog posts, classifieds.');
            $configSection->set('set_active_event_on_submit', true);
            $configSection->set('set_active_blog_post_on_submit', true);
            $configSection->set('set_active_cls_on_creation', true);
            SK_Config::removeCache();
        }
    }
}


adminConfig::getResult($frontend);

if ( in_array(@$_GET['unit'], array('official', 'site_status', 'admin', 'additional', 'automode')) )
{
    $config_unit = $_GET['unit'];
    $_page['title'] = 'Global Configuration';
}
else
{
    $adit_unit = array(
        'groups' => array('file_key' => 'groups', 'active_tab' => 'group_settings', 'section' => 'additional.groups', 'title' => 'Group Settings'),
        'mailbox' => array('file_key' => 'mass_mailing', 'active_tab' => 'mailbox', 'section' => 'additional.mailbox', 'title' => 'Mail settings'),
        'splash_screen' => array('file_key' => 'splash_screen', 'active_tab' => 'splash_screen', 'section' => 'splash_screen', 'title' => 'Splash Screen'),
        'seo' => array('file_key' => 'sitemap', 'active_tab' => 'seo', 'section' => 'seo', 'title' => 'Global Configuration'),
        'security' => array('file_key' => 'security', 'active_tab' => 'security', 'section' => 'security', 'title' => 'Global Configuration'),
        'access' => array('file_key' => 'site', 'active_tab' => 'access', 'section' => 'access', 'title' => 'Global Configuration')
    );

    if ( array_key_exists(@$_GET['unit'], $adit_unit) )
    {
        $file_key = $adit_unit[$_GET['unit']]['file_key'];
        $active_tab = $adit_unit[$_GET['unit']]['active_tab'];
        $config_unit = $adit_unit[$_GET['unit']]['section'];
        $_page['title'] = $adit_unit[$_GET['unit']]['title'];
    }
    else
    {
        $config_unit = 'official';
        $_page['title'] = 'Global Configuration';
    }
}

// require file with specific functions
require_once( 'inc.admin_menu.php' );

if ($config_unit == 'admin' && (!isAdminAuthed(false) || $_SESSION['administration']['superadmin'] !== true)) {
	$referer = isset($_SERVER['HTTP_REFERRER']) ? $_SERVER['HTTP_REFERRER'] : URL_ADMIN;
	redirect($referer);
}

$isEmailChanged = SK_Config::section('site.admin')->admin_email != 'admin@yoursite.com';
$frontend->assign('isEmailChanged', $isEmailChanged);

/*if ( @$_POST['change_password'] )
{
	if ( $_POST['old_password'] == getConfig( 'admin_password' ) )
	{
		if ( $_POST['new_password'] == $_POST['confirm_new_password'] )
			if ( $unit_configs->SaveOneConfig( 'admin_password', $_POST['new_password'] ) )
			{
				$frontend->registerMessage( 'Admin password changed' );
				authAdmin( getConfig( 'admin_username' ), $_POST['new_password'] );
			}
			else
			{
				$frontend->registerMessage( 'Admin password not changed', 'notice' );
			}
		else
			$frontend->registerMessage( 'New password confirmation failed', 'error' );
	}
	else
		$frontend->registerMessage( 'Incorrect old password', 'error' );

	redirect( $_SERVER['REQUEST_URI'] );
}*/






// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );

if ( !in_array($config_unit, array('additional.mailbox', 'additional', 'access', 'official')) )
{
    $sections = adminConfig::getChildSections('site.'.$config_unit);
    array_unshift( $sections, 'site.'.$config_unit);
    $frontend->assign_by_ref('sections',$sections);
}
$frontend->assign("unit", $config_unit);

// display template
$frontend->display( 'site.html' );

?>
