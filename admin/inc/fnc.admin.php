<?php
require_once(DIR_ADMIN_INC.'fnc.subadmin.php');
/**
 * save URL referer in admin area
 *
 * @return boolean
 */
function saveAdminURLReferer()
{
	if ( !strstr( $_SERVER['REQUEST_URI'], '/admin/' ) || strstr( '/admin/auth.php', $_SERVER['PHP_SELF'] ) || strstr( '/admin/logout.php', $_SERVER['PHP_SELF'] ) )
		return false;
		
	$_SESSION['admin']['url_referer'] = $_SERVER['REQUEST_URI'];
	return true;
}

/**
 * Returns admin URL Referer
 *
 * @return unknown
 */
function getAdminURLReferer()
{
	if($_admin_id = getAdminId())
 		return getSAdminDefaultPageUrl($_admin_id);
		
	$url = (strlen( trim($_SESSION['admin']['url_referer']) ) ? $_SESSION['admin']['url_referer'] : URL_ADMIN);
	return (preg_match('/logout.php/', $url))? URL_ADMIN : $url;
}

?>
