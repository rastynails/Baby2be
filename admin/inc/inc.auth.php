<?php

require_once( DIR_ADMIN_INC.'fnc.auth.php' );

require_once( DIR_ADMIN_INC.'fnc.admin.php' );
require_once( DIR_ADMIN_INC.'fnc.subadmin.php' );

header('Content-Type: text/html; charset=utf-8');

saveAdminURLReferer();

controlAdminAuth();

controlAdminPOSTActions();
$_file_key = @$file_key;
if(@$file_key == 'profile')
	$_file_key = "profiles";
if(isSAdmin() && !isSAdminSectionAccessControl(getAdminId(), $_file_key) && (!preg_match("/logout.php/", $_SERVER['PHP_SELF']) ))
	redirect(URL_ADMIN.'logout.php');
?>