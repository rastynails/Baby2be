<?php

$file_key = 'chat';
$active_tab = 'configs';

require_once '../internals/Header.inc.php';

// requiring admin authentication
require_once DIR_ADMIN_INC.'inc.auth.php';

// instantiating admin frontend
require_once 'inc/class.admin_frontend.php';
$frontend = new AdminFrontend();

// time units
$time_units = array('seconds', 'minutes', 'hours');

// processing post data
if ( !empty($_POST) && $_POST['action'] ) {
	require_once DIR_ADMIN_INC.'chat_process.inc.php';
}

adminConfig::SaveConfigs($_POST);
adminConfig::getResult($frontend, false);

// displaying page
$frontend->includeJsFile(URL_ADMIN_JS.'ajax_chat_config.js');

// getting chat history configs
$chat_configs = SK_Config::section('chat');
$frontend->assign('chat_history_type', $chat_configs->history_type);
$frontend->assign('chat_history_recent_msgs_num', $chat_configs->history_recent_msgs_num);
$frontend->assign('chat_history_time', $chat_configs->history_time);
$frontend->assign('time_units', $time_units);

// getting chat rooms
$chat_rooms = app_Chat::getRooms(true);
$frontend->assign_by_ref('chat_rooms', $chat_rooms);

require_once 'inc.admin_menu.php';

$_page['title'] = "Chat Configuration";

$frontend->display('ajax_chat.html');
