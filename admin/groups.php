<?php

$file_key = 'groups';

$active_tab = 'groups';

require_once '../internals/Header.inc.php';

// Admin authentication
require_once DIR_ADMIN_INC.'inc.auth.php';

require_once 'inc/class.admin_frontend.php';
$frontend = new AdminFrontend();

require_once 'inc.admin_menu.php';

if (isset($_POST['action']))
{
	if (count($_POST['groups_arr']))
	{
		switch ($_POST['action'])
		{
			case 'set_status':
				if (strlen($_POST['status']))
					foreach ($_POST['groups_arr'] as $group)
					{
						app_Groups::setGroupStatus($group, $_POST['status']);
					}
					$frontend->registerMessage("Status was changed");
				break;
				
			case 'delete':
					$count = 0;
					foreach ($_POST['groups_arr'] as $group)
					{
						if ( app_Groups::removeGroup($group))
							$count++;
					}
					if ($count) {
						$frontend->registerMessage( $count . " groups deleted");
					}
					else {
						$frontend->registerMessage( "No groups deleted", "notice");
					}
				break;	
		}
	}
	else 
		$frontend->registerMessage("No groups selected", 'notice');

	redirect( $_SERVER['REQUEST_URI'] );
}

$page = intval($_GET['page']) ? intval($_GET['page']) : 1;

if (isset($_GET['status']) && $_GET['status'] == 'approval')
{
	$groups = app_Groups::getGroupList( $page, 'approval' );
	$count = app_Groups::getGroupsCount( 'approval' );
}
else
{
	$groups = app_Groups::getGroupList( $page, 'all' );
	$count = app_Groups::getGroupsCount( 'all' );
}
	
$frontend->assign('paging',array(
	'total'		=> $count,
	'on_page'	=> SK_Config::Section('site')->Section('additional')->Section('mailbox')->mails_per_page,
	'pages'		=> SK_Config::Section('site')->Section('additional')->Section('profile_list')->nav_per_page,
));

$frontend->IncludeJsFile(URL_ADMIN_JS . 'groups.js');

$frontend->assign('groups', $groups);
$frontend->assign('group_url', SK_Navigation::href('group'));

$_page['title'] = "Groups";

$frontend->display('groups.html');
