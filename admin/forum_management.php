<?php

$file_key = 'forum';
$active_tab = 'management';

require_once( '../internals/Header.inc.php' );

// Admin auth
require_once( DIR_ADMIN_INC.'inc.auth.php' );

require_once( DIR_ADMIN_INC.'class.admin_frontend.php' );

$frontend = new AdminFrontend();

require_once( 'inc.admin_menu.php' );

function reOrderForums()
{
	$result = SK_MySQL::query( "SELECT `forum_group_id` FROM `".TBL_FORUM_GROUP."`" );
	while ($group_id = $result->fetch_cell()) {
		$query = SK_MySQL::placeholder( "SELECT `forum_id` FROM `".TBL_FORUM."` 
			WHERE `forum_group_id`=? ORDER BY `order`", $group_id );
		$res = SK_MySQL::query($query);
		$order = 1;
		$affected_rows = 0;
		while ($forum_id = $res->fetch_cell()) {
			$query = SK_MySQL::placeholder( "UPDATE `".TBL_FORUM."` SET `order`=? WHERE `forum_id`=?", $order++, $forum_id );
			SK_MySQL::query( $query );
			$affected_rows += SK_MySQL::affected_rows();
		}
		print_arr( "current forum_group: $group_id. updated forums: $affected_rows. Max order: $order" );
	}
}

//reOrderForums();

$_group_list = app_Forum::getAdminForumGroupList();

foreach ($_group_list as $group)
{
	$group_list[$group['forum_group_id']] = $group['order'];
	if ($group['forums'])
		foreach ($group['forums'] as $forum) {
			$forum_list[$forum['forum_id']] = $forum['order'];
		}
}

$frontend->assign_by_ref( 'group_list', $_group_list );

// include js modules
$frontend->IncludeJsFile( URL_ADMIN_JS.'frontend.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'opacity.js' );
$frontend->IncludeJsFile( URL_ADMIN_JS.'forum.js' );

$_page['title'] = "Forum Management";
$template = 'forum_management.html';

$frontend->registerOnloadJS("SK_Language.data['%interface.ok'] = " . json_encode('Ok') . ";");
$frontend->registerOnloadJS("SK_Language.data['%interface.cancel'] = " . json_encode('Cancel') . ";");

$frontend->registerOnloadJS("adminForum.group_list =  " . json_encode($group_list) . ";");
$frontend->registerOnloadJS("adminForum.forum_list =  " . json_encode($forum_list) . ";");

$frontend->registerOnloadJS("adminForum.construct();");

$frontend->display( $template );
?>